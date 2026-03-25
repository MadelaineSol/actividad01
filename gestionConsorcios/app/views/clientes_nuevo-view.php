<?php
// Alta de Clientes - Front-end demo
?>
<div class="glass p-4">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <div class="pill fw-semibold mb-2"><i class="bi bi-people-fill me-1"></i> Clientes</div>
      <h4 class="fw-bold mb-1">Cargar nuevo cliente</h4>
      <div class="muted">Alta rápida, condición de cobro y datos de contacto (demo).</div>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-soft" href="<?= URL ?>/?view=index"><i class="bi bi-arrow-left me-1"></i>Volver</a>
      <a class="btn btn-soft" href="<?= URL ?>/?view=facturador_ventas"><i class="bi bi-receipt-cutoff me-1"></i>Facturar</a>
    </div>
  </div>

  <div class="divider my-3"></div>

  <form class="glass p-3" style="border-radius:16px;" onsubmit="return addCliente(event)">
    <div class="row g-3">
      <div class="col-12 col-md-7">
        <label class="form-label">Nombre / Razón social</label>
        <input class="form-control soft-input" id="cl_nombre" placeholder="Ej: Juan Pérez / Empresa SRL" required>
      </div>
      <div class="col-12 col-md-5">
        <label class="form-label">CUIT/DNI</label>
        <input class="form-control soft-input mono" id="cl_doc" placeholder="20-12345678-9" required>
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Email</label>
        <input type="email" class="form-control soft-input" id="cl_email" placeholder="cliente@mail.com">
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">Teléfono</label>
        <input class="form-control soft-input mono" id="cl_tel" placeholder="+54 11 ...">
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">Condición de cobro</label>
        <select class="form-select soft-input" id="cl_cond">
          <option>Contado</option>
          <option>Cuenta corriente</option>
          <option>Transferencia</option>
          <option>Cheque</option>
        </select>
      </div>

      <div class="col-12">
        <label class="form-label">Dirección</label>
        <input class="form-control soft-input" id="cl_dir" placeholder="Calle, número, localidad">
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">Límite crédito (opcional)</label>
        <div class="input-group">
          <span class="input-group-text soft-input">$</span>
          <input type="number" step="0.01" min="0" class="form-control soft-input mono text-end" id="cl_lim" value="0">
        </div>
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Estado</label>
        <select class="form-select soft-input" id="cl_estado">
          <option value="Activo" selected>Activo</option>
          <option value="Inactivo">Inactivo</option>
        </select>
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Tipo</label>
        <select class="form-select soft-input" id="cl_tipo">
          <option>Particular</option>
          <option>Empresa</option>
          <option>Distribuidor</option>
        </select>
      </div>

      <div class="col-12">
        <label class="form-label">Notas</label>
        <textarea class="form-control soft-input" id="cl_notas" rows="3" placeholder="Preferencias, horarios, etc."></textarea>
      </div>

      <div class="col-12 d-flex gap-2">
        <button class="btn btn-accent" type="submit"><i class="bi bi-check2-circle me-1"></i>Guardar (demo)</button>
        <button class="btn btn-soft" type="button" onclick="fillClienteDemo()"><i class="bi bi-magic me-1"></i>Ejemplo</button>
        <button class="btn btn-soft" type="button" onclick="clearCliente()"><i class="bi bi-x-lg me-1"></i>Limpiar</button>
      </div>
    </div>
  </form>

  <div class="divider my-4"></div>

  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div class="fw-bold"><i class="bi bi-table me-1"></i> Clientes (demo)</div>
    <div class="muted small">Vista previa en memoria.</div>
  </div>

  <div class="table-responsive mt-3 glass p-2" style="border-radius:16px;">
    <table class="table table-soft align-middle mb-0">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Documento</th>
          <th>Email</th>
          <th>Tel</th>
          <th>Condición</th>
          <th class="text-end">Límite</th>
          <th>Estado</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody id="cliBody">
        <tr class="muted">
          <td colspan="8">Todavía no cargaste clientes.</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<script>
  const clientes = [];

  function escapeHtml(s){
    return (s||'').replace(/[&<>"']/g, (m)=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;' }[m]));
  }

  function money(n){
    const v = Number(n || 0);
    return '$ ' + v.toLocaleString('es-AR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
  }

  function renderClientes(){
    const tb = document.getElementById('cliBody');
    if(clientes.length === 0){
      tb.innerHTML = `<tr class="muted"><td colspan="8">Todavía no cargaste clientes.</td></tr>`;
      return;
    }

    tb.innerHTML = clientes.map((c,i)=>`
      <tr>
        <td>${escapeHtml(c.nombre)}</td>
        <td class="mono">${escapeHtml(c.doc)}</td>
        <td>${escapeHtml(c.email)}</td>
        <td class="mono">${escapeHtml(c.tel)}</td>
        <td>${escapeHtml(c.cond)}</td>
        <td class="text-end mono">${money(c.lim)}</td>
        <td><span class="badge badge-soft ${c.estado==='Activo'?'badge-ok':'badge-bad'}">${escapeHtml(c.estado)}</span></td>
        <td class="text-end">
          <button class="btn btn-soft btn-sm" type="button" onclick="dupCliente(${i})" title="Duplicar"><i class="bi bi-files"></i></button>
          <button class="btn btn-soft btn-sm" type="button" onclick="delCliente(${i})" title="Eliminar"><i class="bi bi-trash3"></i></button>
        </td>
      </tr>
    `).join('');
  }

  function addCliente(e){
    e.preventDefault();

    const c = {
      nombre: document.getElementById('cl_nombre').value.trim(),
      doc: document.getElementById('cl_doc').value.trim(),
      email: document.getElementById('cl_email').value.trim(),
      tel: document.getElementById('cl_tel').value.trim(),
      cond: document.getElementById('cl_cond').value,
      dir: document.getElementById('cl_dir').value.trim(),
      lim: Number(document.getElementById('cl_lim').value || 0),
      estado: document.getElementById('cl_estado').value,
      tipo: document.getElementById('cl_tipo').value,
      notas: document.getElementById('cl_notas').value.trim(),
    };

    clientes.unshift(c);
    renderClientes();
    try{ bootstrap.Toast.getOrCreateInstance(document.getElementById('toastSaved')).show(); }catch(err){}
    clearCliente();
    return false;
  }

  function delCliente(i){
    clientes.splice(i,1);
    renderClientes();
  }

  function dupCliente(i){
    const c = JSON.parse(JSON.stringify(clientes[i]));
    c.nombre = c.nombre + ' (copia)';
    clientes.unshift(c);
    renderClientes();
  }

  function fillClienteDemo(){
    document.getElementById('cl_nombre').value = 'Juan Pérez';
    document.getElementById('cl_doc').value = '20-12345678-9';
    document.getElementById('cl_email').value = 'juanperez@mail.com';
    document.getElementById('cl_tel').value = '+54 11 4444-4444';
    document.getElementById('cl_cond').value = 'Cuenta corriente';
    document.getElementById('cl_dir').value = 'Panamericana Km 46, Pilar';
    document.getElementById('cl_lim').value = 500000;
    document.getElementById('cl_estado').value = 'Activo';
    document.getElementById('cl_tipo').value = 'Particular';
    document.getElementById('cl_notas').value = 'Prefiere factura por email.';
  }

  function clearCliente(){
    ['cl_nombre','cl_doc','cl_email','cl_tel','cl_dir','cl_notas'].forEach(id=>document.getElementById(id).value='');
    document.getElementById('cl_lim').value = 0;
    document.getElementById('cl_estado').value = 'Activo';
    document.getElementById('cl_cond').value = 'Contado';
    document.getElementById('cl_tipo').value = 'Particular';
  }

  renderClientes();
</script>
