<?php
// Alta de Proveedores - Front-end demo
?>
<div class="glass p-4">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <div class="pill fw-semibold mb-2"><i class="bi bi-truck me-1"></i> Proveedores</div>
      <h4 class="fw-bold mb-1">Cargar nuevo proveedor</h4>
      <div class="muted">Datos básicos, contacto y condición. Vista previa en tabla (demo).</div>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-soft" href="<?= URL ?>/?view=index"><i class="bi bi-arrow-left me-1"></i>Volver</a>
      <a class="btn btn-soft" href="<?= URL ?>/?view=productos_nuevo"><i class="bi bi-box-seam me-1"></i>Inventario</a>
    </div>
  </div>

  <div class="divider my-3"></div>

  <form class="glass p-3" style="border-radius:16px;" onsubmit="return addProveedor(event)">
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <label class="form-label">Razón social</label>
        <input class="form-control soft-input" id="pr_nombre" placeholder="Ej: Almacén Central SA" required>
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">CUIT</label>
        <input class="form-control soft-input mono" id="pr_cuit" placeholder="30-12345678-9" required>
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">Condición IVA</label>
        <select class="form-select soft-input" id="pr_iva">
          <option>Responsable Inscripto</option>
          <option>Monotributo</option>
          <option>Exento</option>
          <option>Consumidor Final</option>
        </select>
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Email</label>
        <input type="email" class="form-control soft-input" id="pr_email" placeholder="compras@proveedor.com">
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">Teléfono</label>
        <input class="form-control soft-input mono" id="pr_tel" placeholder="+54 11 ...">
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">Cuenta / Condición</label>
        <select class="form-select soft-input" id="pr_cond">
          <option>Contado</option>
          <option>Cuenta corriente</option>
          <option>Transferencia</option>
          <option>Cheque</option>
        </select>
      </div>

      <div class="col-12">
        <label class="form-label">Dirección</label>
        <input class="form-control soft-input" id="pr_dir" placeholder="Calle, número, localidad">
      </div>

      <div class="col-12 col-md-8">
        <label class="form-label">Contacto (nombre)</label>
        <input class="form-control soft-input" id="pr_contacto" placeholder="Ej: Mariana / Juan">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Estado</label>
        <select class="form-select soft-input" id="pr_estado">
          <option value="Activo" selected>Activo</option>
          <option value="Inactivo">Inactivo</option>
        </select>
      </div>

      <div class="col-12">
        <label class="form-label">Notas</label>
        <textarea class="form-control soft-input" id="pr_notas" rows="3" placeholder="Horarios, condiciones, etc."></textarea>
      </div>

      <div class="col-12 d-flex gap-2">
        <button class="btn btn-accent" type="submit"><i class="bi bi-check2-circle me-1"></i>Guardar (demo)</button>
        <button class="btn btn-soft" type="button" onclick="fillProveedorDemo()"><i class="bi bi-magic me-1"></i>Ejemplo</button>
        <button class="btn btn-soft" type="button" onclick="clearProveedor()"><i class="bi bi-x-lg me-1"></i>Limpiar</button>
      </div>
    </div>
  </form>

  <div class="divider my-4"></div>

  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div class="fw-bold"><i class="bi bi-table me-1"></i> Proveedores (demo)</div>
    <div class="muted small">Solo vista previa (sin DB).</div>
  </div>

  <div class="table-responsive mt-3 glass p-2" style="border-radius:16px;">
    <table class="table table-soft align-middle mb-0">
      <thead>
        <tr>
          <th>Razón social</th>
          <th>CUIT</th>
          <th>Email</th>
          <th>Tel</th>
          <th>Condición</th>
          <th>Estado</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody id="provBody">
        <tr class="muted">
          <td colspan="7">Todavía no cargaste proveedores.</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<script>
  const proveedores = [];

  function escapeHtml(s){
    return (s||'').replace(/[&<>"']/g, (m)=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;' }[m]));
  }

  function renderProveedores(){
    const tb = document.getElementById('provBody');
    if(proveedores.length === 0){
      tb.innerHTML = `<tr class="muted"><td colspan="7">Todavía no cargaste proveedores.</td></tr>`;
      return;
    }
    tb.innerHTML = proveedores.map((p,i)=>`
      <tr>
        <td>${escapeHtml(p.nombre)}</td>
        <td class="mono">${escapeHtml(p.cuit)}</td>
        <td>${escapeHtml(p.email)}</td>
        <td class="mono">${escapeHtml(p.tel)}</td>
        <td>${escapeHtml(p.cond)}</td>
        <td><span class="badge badge-soft ${p.estado==='Activo'?'badge-ok':'badge-bad'}">${escapeHtml(p.estado)}</span></td>
        <td class="text-end">
          <button class="btn btn-soft btn-sm" type="button" onclick="dupProveedor(${i})" title="Duplicar"><i class="bi bi-files"></i></button>
          <button class="btn btn-soft btn-sm" type="button" onclick="delProveedor(${i})" title="Eliminar"><i class="bi bi-trash3"></i></button>
        </td>
      </tr>
    `).join('');
  }

  function addProveedor(e){
    e.preventDefault();

    const p = {
      nombre: document.getElementById('pr_nombre').value.trim(),
      cuit: document.getElementById('pr_cuit').value.trim(),
      iva: document.getElementById('pr_iva').value,
      email: document.getElementById('pr_email').value.trim(),
      tel: document.getElementById('pr_tel').value.trim(),
      cond: document.getElementById('pr_cond').value,
      dir: document.getElementById('pr_dir').value.trim(),
      contacto: document.getElementById('pr_contacto').value.trim(),
      estado: document.getElementById('pr_estado').value,
      notas: document.getElementById('pr_notas').value.trim(),
    };

    proveedores.unshift(p);
    renderProveedores();
    try{ bootstrap.Toast.getOrCreateInstance(document.getElementById('toastSaved')).show(); }catch(err){}
    clearProveedor();
    return false;
  }

  function delProveedor(i){
    proveedores.splice(i,1);
    renderProveedores();
  }

  function dupProveedor(i){
    const p = JSON.parse(JSON.stringify(proveedores[i]));
    p.nombre = p.nombre + ' (copia)';
    proveedores.unshift(p);
    renderProveedores();
  }

  function fillProveedorDemo(){
    document.getElementById('pr_nombre').value = 'INFRAESTRUCTURA FIBERCO';
    document.getElementById('pr_cuit').value = '30-98765432-1';
    document.getElementById('pr_iva').value = 'Responsable Inscripto';
    document.getElementById('pr_email').value = 'compras@fiberco.com';
    document.getElementById('pr_tel').value = '+54 11 5555-5555';
    document.getElementById('pr_cond').value = 'Cuenta corriente';
    document.getElementById('pr_dir').value = 'Panamericana Km 46, Pilar';
    document.getElementById('pr_contacto').value = 'Mariana';
    document.getElementById('pr_estado').value = 'Activo';
    document.getElementById('pr_notas').value = 'Entrega 48 hs. Factura A.';
  }

  function clearProveedor(){
    ['pr_nombre','pr_cuit','pr_email','pr_tel','pr_dir','pr_contacto','pr_notas'].forEach(id=>document.getElementById(id).value='');
    document.getElementById('pr_estado').value = 'Activo';
    document.getElementById('pr_cond').value = 'Contado';
  }

  renderProveedores();
</script>
