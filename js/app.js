// ============================================================
// TALLER MECANICO - Sistema de Gestión
// Almacenamiento con localStorage
// ============================================================

const DB = {
    get(key) {
        return JSON.parse(localStorage.getItem(key) || '[]');
    },
    set(key, data) {
        localStorage.setItem(key, JSON.stringify(data));
    },
    nextId(key) {
        const items = this.get(key);
        return items.length > 0 ? Math.max(...items.map(i => i.id)) + 1 : 1;
    }
};

// ============================================================
// VEHICULOS
// ============================================================
const Vehiculos = {
    STORAGE_KEY: 'taller_vehiculos',

    getAll() { return DB.get(this.STORAGE_KEY); },

    getById(id) { return this.getAll().find(v => v.id === id); },

    save(vehiculo) {
        const vehiculos = this.getAll();
        if (vehiculo.id) {
            const idx = vehiculos.findIndex(v => v.id === vehiculo.id);
            if (idx !== -1) vehiculos[idx] = { ...vehiculos[idx], ...vehiculo };
        } else {
            vehiculo.id = DB.nextId(this.STORAGE_KEY);
            vehiculo.fechaRegistro = new Date().toISOString();
            vehiculos.push(vehiculo);
        }
        DB.set(this.STORAGE_KEY, vehiculos);
        return vehiculo;
    },

    delete(id) {
        const vehiculos = this.getAll().filter(v => v.id !== id);
        DB.set(this.STORAGE_KEY, vehiculos);
    },

    search(term) {
        term = term.toLowerCase();
        return this.getAll().filter(v =>
            v.patente.toLowerCase().includes(term) ||
            v.marca.toLowerCase().includes(term) ||
            v.modelo.toLowerCase().includes(term) ||
            v.propietario.toLowerCase().includes(term)
        );
    }
};

// ============================================================
// ORDENES DE TRABAJO
// ============================================================
const Ordenes = {
    STORAGE_KEY: 'taller_ordenes',
    ESTADOS: ['Pendiente', 'En Progreso', 'Completado', 'Entregado', 'Cancelado'],

    getAll() { return DB.get(this.STORAGE_KEY); },

    getById(id) { return this.getAll().find(o => o.id === id); },

    save(orden) {
        const ordenes = this.getAll();
        if (orden.id) {
            const idx = ordenes.findIndex(o => o.id === orden.id);
            if (idx !== -1) {
                ordenes[idx] = { ...ordenes[idx], ...orden };
                if (!ordenes[idx].historial) ordenes[idx].historial = [];
                ordenes[idx].historial.push({
                    fecha: new Date().toISOString(),
                    accion: 'Orden actualizada'
                });
            }
        } else {
            orden.id = DB.nextId(this.STORAGE_KEY);
            orden.numero = 'OT-' + String(orden.id).padStart(5, '0');
            orden.estado = 'Pendiente';
            orden.fechaCreacion = new Date().toISOString();
            orden.historial = [{ fecha: new Date().toISOString(), accion: 'Orden creada' }];
            ordenes.push(orden);
        }
        DB.set(this.STORAGE_KEY, ordenes);
        return orden;
    },

    cambiarEstado(id, nuevoEstado) {
        const ordenes = this.getAll();
        const idx = ordenes.findIndex(o => o.id === id);
        if (idx !== -1) {
            const estadoAnterior = ordenes[idx].estado;
            ordenes[idx].estado = nuevoEstado;
            if (!ordenes[idx].historial) ordenes[idx].historial = [];
            ordenes[idx].historial.push({
                fecha: new Date().toISOString(),
                accion: `Estado cambiado de "${estadoAnterior}" a "${nuevoEstado}"`
            });
            if (nuevoEstado === 'Completado') {
                ordenes[idx].fechaCompletado = new Date().toISOString();
            }
            DB.set(this.STORAGE_KEY, ordenes);
        }
    },

    delete(id) {
        DB.set(this.STORAGE_KEY, this.getAll().filter(o => o.id !== id));
    },

    search(term) {
        term = term.toLowerCase();
        return this.getAll().filter(o => {
            const vehiculo = Vehiculos.getById(o.vehiculoId);
            return o.numero.toLowerCase().includes(term) ||
                o.descripcion.toLowerCase().includes(term) ||
                o.estado.toLowerCase().includes(term) ||
                (vehiculo && vehiculo.patente.toLowerCase().includes(term));
        });
    },

    getByEstado(estado) {
        return this.getAll().filter(o => o.estado === estado);
    }
};

// ============================================================
// PRESUPUESTOS
// ============================================================
const Presupuestos = {
    STORAGE_KEY: 'taller_presupuestos',
    ESTADOS: ['Pendiente', 'Aprobado', 'Rechazado'],

    getAll() { return DB.get(this.STORAGE_KEY); },

    getById(id) { return this.getAll().find(p => p.id === id); },

    save(presupuesto) {
        const presupuestos = this.getAll();
        if (presupuesto.id) {
            const idx = presupuestos.findIndex(p => p.id === presupuesto.id);
            if (idx !== -1) presupuestos[idx] = { ...presupuestos[idx], ...presupuesto };
        } else {
            presupuesto.id = DB.nextId(this.STORAGE_KEY);
            presupuesto.numero = 'PRES-' + String(presupuesto.id).padStart(5, '0');
            presupuesto.estado = 'Pendiente';
            presupuesto.fecha = new Date().toISOString();
            presupuestos.push(presupuesto);
        }
        DB.set(this.STORAGE_KEY, presupuestos);
        return presupuesto;
    },

    cambiarEstado(id, nuevoEstado) {
        const presupuestos = this.getAll();
        const idx = presupuestos.findIndex(p => p.id === id);
        if (idx !== -1) {
            presupuestos[idx].estado = nuevoEstado;
            DB.set(this.STORAGE_KEY, presupuestos);
        }
    },

    delete(id) {
        DB.set(this.STORAGE_KEY, this.getAll().filter(p => p.id !== id));
    },

    calcularTotal(items) {
        return items.reduce((sum, item) => sum + (item.cantidad * item.precioUnitario), 0);
    }
};

// ============================================================
// ORDENES DE PAGO
// ============================================================
const Pagos = {
    STORAGE_KEY: 'taller_pagos',
    ESTADOS: ['Pendiente', 'Pagado', 'Parcial'],
    METODOS: ['Efectivo', 'Tarjeta de Débito', 'Tarjeta de Crédito', 'Transferencia', 'Cheque'],

    getAll() { return DB.get(this.STORAGE_KEY); },

    getById(id) { return this.getAll().find(p => p.id === id); },

    save(pago) {
        const pagos = this.getAll();
        if (pago.id) {
            const idx = pagos.findIndex(p => p.id === pago.id);
            if (idx !== -1) pagos[idx] = { ...pagos[idx], ...pago };
        } else {
            pago.id = DB.nextId(this.STORAGE_KEY);
            pago.numero = 'PAG-' + String(pago.id).padStart(5, '0');
            pago.estado = 'Pendiente';
            pago.fecha = new Date().toISOString();
            pago.pagosRealizados = [];
            pagos.push(pago);
        }
        DB.set(this.STORAGE_KEY, pagos);
        return pago;
    },

    registrarPago(id, monto, metodo) {
        const pagos = this.getAll();
        const idx = pagos.findIndex(p => p.id === id);
        if (idx !== -1) {
            if (!pagos[idx].pagosRealizados) pagos[idx].pagosRealizados = [];
            pagos[idx].pagosRealizados.push({
                monto: parseFloat(monto),
                metodo,
                fecha: new Date().toISOString()
            });
            const totalPagado = pagos[idx].pagosRealizados.reduce((s, p) => s + p.monto, 0);
            if (totalPagado >= pagos[idx].montoTotal) {
                pagos[idx].estado = 'Pagado';
            } else {
                pagos[idx].estado = 'Parcial';
            }
            DB.set(this.STORAGE_KEY, pagos);
        }
    },

    delete(id) {
        DB.set(this.STORAGE_KEY, this.getAll().filter(p => p.id !== id));
    },

    getTotalPagado(pago) {
        return (pago.pagosRealizados || []).reduce((s, p) => s + p.monto, 0);
    }
};

// ============================================================
// UTILIDADES
// ============================================================
function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    return d.toLocaleDateString('es-AR', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function formatDateTime(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    return d.toLocaleDateString('es-AR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(amount);
}

function getBadgeClass(estado) {
    const map = {
        'Pendiente': 'badge-pendiente',
        'En Progreso': 'badge-en-progreso',
        'Completado': 'badge-completado',
        'Entregado': 'badge-entregado',
        'Cancelado': 'badge-cancelado',
        'Aprobado': 'badge-aprobado',
        'Rechazado': 'badge-rechazado',
        'Pagado': 'badge-pagado',
        'Parcial': 'badge-parcial'
    };
    return map[estado] || 'badge-pendiente';
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'warning'} position-fixed`;
    toast.style.cssText = 'top:20px;right:20px;z-index:9999;min-width:300px;animation:fadeIn 0.3s';
    toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'exclamation-triangle'} me-2"></i>${message}`;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity 0.3s'; setTimeout(() => toast.remove(), 300); }, 3000);
}

// Mobile sidebar toggle
function initSidebar() {
    const toggle = document.querySelector('.mobile-toggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    if (toggle) {
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        });
    }
    if (overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        });
    }
}

document.addEventListener('DOMContentLoaded', initSidebar);

// ============================================================
// DASHBOARD
// ============================================================
function loadDashboard() {
    const vehiculos = Vehiculos.getAll();
    const ordenes = Ordenes.getAll();
    const presupuestos = Presupuestos.getAll();
    const pagos = Pagos.getAll();

    document.getElementById('totalVehiculos').textContent = vehiculos.length;
    document.getElementById('totalOrdenes').textContent = ordenes.length;
    document.getElementById('ordenesActivas').textContent = ordenes.filter(o => o.estado === 'En Progreso').length;

    const totalIngresos = pagos.reduce((sum, p) => sum + Pagos.getTotalPagado(p), 0);
    document.getElementById('totalIngresos').textContent = formatCurrency(totalIngresos);

    // Recent orders table
    const recentOrders = ordenes.slice(-5).reverse();
    const tbody = document.getElementById('recentOrdersBody');
    if (tbody) {
        tbody.innerHTML = recentOrders.map(o => {
            const vehiculo = Vehiculos.getById(o.vehiculoId);
            return `<tr>
                <td><strong>${o.numero}</strong></td>
                <td>${vehiculo ? vehiculo.patente : '-'}</td>
                <td>${o.descripcion.substring(0, 40)}${o.descripcion.length > 40 ? '...' : ''}</td>
                <td><span class="badge-status ${getBadgeClass(o.estado)}">${o.estado}</span></td>
                <td>${formatDate(o.fechaCreacion)}</td>
            </tr>`;
        }).join('');
    }

    // Pending budgets
    const pendingBudgets = presupuestos.filter(p => p.estado === 'Pendiente').slice(-5).reverse();
    const budgetBody = document.getElementById('pendingBudgetsBody');
    if (budgetBody) {
        budgetBody.innerHTML = pendingBudgets.map(p => {
            const orden = Ordenes.getById(p.ordenId);
            return `<tr>
                <td><strong>${p.numero}</strong></td>
                <td>${orden ? orden.numero : '-'}</td>
                <td>${formatCurrency(p.total)}</td>
                <td><span class="badge-status ${getBadgeClass(p.estado)}">${p.estado}</span></td>
                <td>${formatDate(p.fecha)}</td>
            </tr>`;
        }).join('');
    }
}
