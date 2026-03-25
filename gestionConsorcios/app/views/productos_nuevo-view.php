<?php
// Alta de Productos (Inventario) - Front-end demo
?>
<div class="glass p-4">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <div class="pill fw-semibold mb-2"><i class="bi bi-box-seam me-1"></i> Inventario</div>
      <h4 class="fw-bold mb-1">Cargar nuevo producto</h4>
      <div class="muted">Alta rápida de productos y vista previa en tabla (demo front-end).</div>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-soft" href="<?= URL ?>/?view=index"><i class="bi bi-arrow-left me-1"></i>Volver</a>
      <a class="btn btn-soft" href="<?= URL ?>/?view=facturador_ventas"><i class="bi bi-receipt-cutoff me-1"></i>Ir a Ventas</a>
    </div>
  </div>

  <div class="divider my-3"></div>

  <form class="glass p-3" style="border-radius:16px;" onsubmit="return addProducto(event)">
    <div class="row g-3">
      <div class="col-12 col-md-4">
        <label class="form-label">Código / SKU</label>
        <input class="form-control soft-input mono" id="p_sku" placeholder="Ej: SKU-0001" required>
      </div>
      <div class="col-12 col-md-8">
        <label class="form-label">Nombre</label>
        <input class="form-control soft-input" id="p_nombre" placeholder="Ej: Batería Litio 48V 100Ah" required>
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">Categoría</label>
        <select class="form-select soft-input" id="p_cat">
          <option>Baterías</option>
          <option>Cargadores</option>
          <option>Servicios</option>
          <option>Repuestos</option>
          <option>Otros</option>
        </select>
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Proveedor</label>
        <input class="form-control soft-input" id="p_prov" placeholder="Ej: Fiberco / Metrotel">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Unidad</label>
        <select class="form-select soft-input" id="p_unidad">
          <option>Unidad</option>
          <option>Hora</option>
          <option>Kg</option>
          <option>Metro</option>
          <option>Pack</option>
        </select>
      </div>

      <div class="col-12 col-md-3">
        <label class="form-label">Costo</label>
        <div class="input-group">
          <span class="input-group-text soft-input">$</span>
          <input type="number" step="0.01" min="0" class="form-control soft-input mono text-end" id="p_costo" value="0">
        </div>
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">Precio</label>
        <div class="input-group">
          <span class="input-group-text soft-input">$</span>
          <input type="number" step="0.01" min="0" class="form-control soft-input mono text-end" id="p_precio" value="0" required>
        </div>
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">IVA</label>
        <select class="form-select soft-input mono" id="p_iva">
          <option value="0">0%</option>
          <option value="10.5">10.5%</option>
          <option value="21" selected>21%</option>
          <option value="27">27%</option>
        </select>
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">Stock inicial</label>
        <input type="number" step="1" min="0" class="form-control soft-input mono text-end" id="p_stock" value="0">
      </div>

      <div class="col-12 col-md-3">
        <label class="form-label">Stock mínimo</label>
        <input type="number" step="1" min="0" class="form-control soft-input mono text-end" id="p_min" value="0">
      </div>
      <div class="col-12 col-md-9">
        <label class="form-label">Descripción (opcional)</label>
        <input class="form-control soft-input" id="p_desc" placeholder="Notas internas...">
      </div>

      <div class="col-12 d-flex gap-2">
        <button class="btn btn-accent" type="submit"><i class="bi bi-check2-circle me-1"></i>Guardar (demo)</button>
        <button class="btn btn-soft" type="button" onclick="fillProductoDemo()"><i class="bi bi-magic me-1"></i>Ejemplo</button>
        <button class="btn btn-soft" type="button" onclick="clearProducto()"><i class="bi bi-x-lg me-1"></i>Limpiar</button>
      </div>
    </div>
  </form>

  <div class="divider my-4"></div>

  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div class="fw-bold"><i class="bi bi-table me-1"></i> Productos cargados (demo)</div>
    <div class="muted small">Esto es una vista previa en memoria del navegador.</div>
  </div>

  <div class="table-responsive mt-3 glass p-2" style="border-radius:16px;">
    <table class="table table-soft align-middle mb-0">
      <thead>
        <tr>
          <th>SKU</th>
          <th>Nombre</th>
          <th>Categoría</th>
          <th>Proveedor</th>
          <th class="text-end">Precio</th>
          <th class="text-end">Stock</th>
          <th class="text-end">Mín.</th>
          <th>IVA</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody id="prodBody">
        <tr class="muted">
          <td colspan="9">Todavía no cargaste productos. Usá “Ejemplo” o “Guardar”.</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<script>
  const productos = [];

  function money(n){
    const v = Number(n || 0);
    return '$ ' + v.toLocaleString('es-AR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
  }

  function renderProductos(){
    const tb = document.getElementById('prodBody');
    if(productos.length === 0){
      tb.innerHTML = `<tr class="muted"><td colspan="9">Todavía no cargaste productos. Usá “Ejemplo” o “Guardar”.</td></tr>`;
      return;
    }
    tb.innerHTML = productos.map((p, i)=>`
      <tr>
        <td class="mono">${escapeHtml(p.sku)}</td>
        <td>${escapeHtml(p.nombre)}</td>
        <td>${escapeHtml(p.cat)}</td>
        <td>${escapeHtml(p.prov)}</td>
        <td class="text-end mono">${money(p.precio)}</td>
        <td class="text-end mono">${p.stock}</td>
        <td class="text-end mono">${p.min}</td>
        <td class="mono">${p.iva}%</td>
        <td class="text-end">
          <button class="btn btn-soft btn-sm" type="button" onclick="dupProducto(${i})" title="Duplicar"><i class="bi bi-files"></i></button>
          <button class="btn btn-soft btn-sm" type="button" onclick="delProducto(${i})" title="Eliminar"><i class="bi bi-trash3"></i></button>
        </td>
      </tr>
    `).join('');
  }

  function addProducto(e){
    e.preventDefault();

    const p = {
      sku: document.getElementById('p_sku').value.trim(),
      nombre: document.getElementById('p_nombre').value.trim(),
      cat: document.getElementById('p_cat').value,
      prov: document.getElementById('p_prov').value.trim(),
      unidad: document.getElementById('p_unidad').value,
      costo: Number(document.getElementById('p_costo').value || 0),
      precio: Number(document.getElementById('p_precio').value || 0),
      iva: Number(document.getElementById('p_iva').value || 0),
      stock: Number(document.getElementById('p_stock').value || 0),
      min: Number(document.getElementById('p_min').value || 0),
      desc: document.getElementById('p_desc').value.trim()
    };

    productos.unshift(p);
    renderProductos();
    try{ bootstrap.Toast.getOrCreateInstance(document.getElementById('toastSaved')).show(); }catch(err){}
    clearProducto(false);
    return false;
  }

  function delProducto(i){
    productos.splice(i,1);
    renderProductos();
  }

  function dupProducto(i){
    const p = JSON.parse(JSON.stringify(productos[i]));
    p.sku = p.sku + '-COPY';
    productos.unshift(p);
    renderProductos();
  }

  function fillProductoDemo(){
    document.getElementById('p_sku').value = 'SKU-1001';
    document.getElementById('p_nombre').value = 'Cargador 48V 30A';
    document.getElementById('p_cat').value = 'Cargadores';
    document.getElementById('p_prov').value = 'INFRAESTRUCTURA FIBERCO';
    document.getElementById('p_unidad').value = 'Unidad';
    document.getElementById('p_costo').value = 120000;
    document.getElementById('p_precio').value = 185000;
    document.getElementById('p_iva').value = '21';
    document.getElementById('p_stock').value = 5;
    document.getElementById('p_min').value = 2;
    document.getElementById('p_desc').value = 'Incluye cable y manual.';
  }

  function clearProducto(clearSku=true){
    if(clearSku) document.getElementById('p_sku').value = '';
    document.getElementById('p_nombre').value = '';
    document.getElementById('p_prov').value = '';
    document.getElementById('p_costo').value = 0;
    document.getElementById('p_precio').value = 0;
    document.getElementById('p_stock').value = 0;
    document.getElementById('p_min').value = 0;
    document.getElementById('p_desc').value = '';
  }

  function escapeHtml(s){
    return (s||'').replace(/[&<>"']/g, (m)=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;' }[m]));
  }

  renderProductos();
</script>
