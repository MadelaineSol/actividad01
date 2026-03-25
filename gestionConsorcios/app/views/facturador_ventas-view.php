<?php
// Facturador de Ventas (Front-end) - UI glass style
?>
<style>
  /* Para exportar PDF (imprimir) más limpio */
  @media print{
    .no-print{ display:none !important; }
    .row > .col-12.col-lg-3 { display:none !important; }
    .row > .col-12.col-lg-9 { flex: 0 0 100% !important; max-width: 100% !important; }
    body{ background:#fff !important; color:#000 !important; }
    .glass{ box-shadow:none !important; border-color: rgba(0,0,0,.15) !important; background:#fff !important; }
    .muted{ color:#444 !important; }
    .btn, .pill{ display:none !important; }
  }
</style>

<div class="glass p-4">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <div class="pill fw-semibold mb-2"><i class="bi bi-receipt-cutoff me-1"></i> Facturador de Ventas</div>
      <h4 class="fw-bold mb-1">Nueva venta</h4>
      <div class="muted">Cargá ítems, calculá totales y exportá a PDF desde el navegador.</div>
    </div>
    <div class="d-flex gap-2 no-print">
      <a class="btn btn-soft" href="<?= URL ?>/?view=index"><i class="bi bi-arrow-left me-1"></i>Volver</a>
      <button class="btn btn-soft" type="button" onclick="fillVentaDemo()"><i class="bi bi-magic me-1"></i>Ejemplo</button>
      <button class="btn btn-accent" type="button" onclick="window.print()"><i class="bi bi-filetype-pdf me-1"></i>PDF</button>
    </div>
  </div>

  <div class="divider my-3"></div>

  <!-- Cabecera documento -->
  <div class="row g-3">
    <div class="col-12 col-lg-7">
      <div class="glass p-3" style="border-radius:16px;">
        <div class="d-flex justify-content-between flex-wrap gap-2">
          <div>
            <div class="fw-bold">TU EMPRESA</div>
            <div class="muted small">CUIT 20-00000000-0 • Responsable Inscripto</div>
            <div class="muted small">Dirección / Tel / Email</div>
          </div>
          <div class="text-end">
            <div class="pill fw-semibold">VENTA</div>
            <div class="muted small">N° <span class="mono" id="v_num">V-000001</span></div>
            <div class="muted small">Fecha <span class="mono" id="v_fecha"><?= date('Y-m-d'); ?></span></div>
          </div>
        </div>
      </div>

      <div class="glass p-3 mt-3" style="border-radius:16px;">
        <div class="fw-bold mb-2"><i class="bi bi-person-badge me-1"></i> Cliente</div>
        <div class="row g-2">
          <div class="col-12 col-md-6">
            <label class="form-label muted small mb-1">Nombre / Razón Social</label>
            <input class="form-control soft-input" id="c_nombre" placeholder="Ej: Juan Pérez / Empresa SRL">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label muted small mb-1">CUIT/DNI</label>
            <input class="form-control soft-input mono" id="c_doc" placeholder="Ej: 20-12345678-9">
          </div>
          <div class="col-12">
            <label class="form-label muted small mb-1">Dirección</label>
            <input class="form-control soft-input" id="c_dir" placeholder="Calle 123, Localidad">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label muted small mb-1">Condición</label>
            <select class="form-select soft-input" id="v_condicion">
              <option value="Contado">Contado</option>
              <option value="Cuenta corriente">Cuenta corriente</option>
              <option value="Transferencia">Transferencia</option>
              <option value="Cheque">Cheque</option>
            </select>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label muted small mb-1">Vendedor</label>
            <input class="form-control soft-input" id="v_vendedor" placeholder="Ej: Sol">
          </div>
        </div>
      </div>
    </div>

    <!-- Resumen -->
    <div class="col-12 col-lg-5">
      <div class="glass p-3" style="border-radius:16px;">
        <div class="fw-bold mb-2"><i class="bi bi-calculator me-1"></i> Totales</div>

        <div class="d-flex justify-content-between">
          <div class="muted">Subtotal</div>
          <div class="mono fw-semibold" id="t_sub">$ 0,00</div>
        </div>

        <div class="d-flex justify-content-between mt-2">
          <div class="muted">IVA</div>
          <div class="mono fw-semibold" id="t_iva">$ 0,00</div>
        </div>

        <div class="d-flex justify-content-between mt-2 align-items-center">
          <div class="muted">Descuento</div>
          <div class="d-flex align-items-center gap-2">
            <input class="form-control soft-input mono text-end" id="t_desc" value="0" style="max-width:110px;">
            <span class="muted small">%</span>
          </div>
        </div>

        <div class="divider my-3"></div>

        <div class="d-flex justify-content-between">
          <div class="fw-bold">TOTAL</div>
          <div class="mono fw-bold fs-5" id="t_total">$ 0,00</div>
        </div>

        <div class="muted small mt-2">
          <i class="bi bi-info-circle me-1"></i> Guardar en PDF: botón PDF → “Guardar como PDF”.
        </div>

        <div class="d-flex gap-2 mt-3 no-print">
          <button class="btn btn-accent w-100" type="button" onclick="saveVentaDemo()">
            <i class="bi bi-check2-circle me-1"></i> Guardar (demo)
          </button>
          <button class="btn btn-soft" type="button" onclick="clearVenta()"><i class="bi bi-x-lg"></i></button>
        </div>
      </div>

      <div class="glass p-3 mt-3" style="border-radius:16px;">
        <div class="fw-bold mb-2"><i class="bi bi-chat-left-text me-1"></i> Observaciones</div>
        <textarea class="form-control soft-input" id="v_obs" rows="6" placeholder="Notas, forma de entrega, etc."></textarea>
      </div>
    </div>
  </div>

  <div class="divider my-4"></div>

  <!-- Detalle -->
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div class="fw-bold"><i class="bi bi-list-check me-1"></i> Ítems</div>
    <div class="no-print d-flex gap-2">
      <button class="btn btn-soft btn-sm" type="button" onclick="addRowVenta()"><i class="bi bi-plus-lg me-1"></i>Agregar</button>
      <button class="btn btn-soft btn-sm" type="button" onclick="addRowVenta(true)"><i class="bi bi-upc-scan me-1"></i>Rápido</button>
    </div>
  </div>

  <div class="table-responsive mt-3 glass p-2" style="border-radius:16px;">
    <table class="table table-soft align-middle mb-0" id="ventaTable">
      <thead>
        <tr>
          <th style="min-width:260px;">Producto / Servicio</th>
          <th style="width:120px;" class="text-end">Cant.</th>
          <th style="width:170px;" class="text-end">Precio</th>
          <th style="width:120px;" class="text-end">IVA</th>
          <th style="width:190px;" class="text-end">Subtotal</th>
          <th style="width:70px;" class="text-end no-print"></th>
        </tr>
      </thead>
      <tbody id="ventaBody"></tbody>
    </table>
  </div>

  <div class="muted small mt-3">
    <i class="bi bi-lightning-charge-fill me-1"></i>
    Tip: agregá tus productos reales desde “Inventario” y después conectamos este facturador al backend.
  </div>
</div>

<script>
  function money(n){
    const v = Number(n || 0);
    return '$ ' + v.toLocaleString('es-AR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
  }

  function addRowVenta(rapida=false){
    const tbody = document.getElementById('ventaBody');
    const tr = document.createElement('tr');

    tr.innerHTML = `
      <td><input class="form-control soft-input" placeholder="Ej: Producto / Servicio" value="${rapida ? 'Producto demo' : ''}" oninput="recalcVenta()"></td>
      <td class="text-end"><input type="number" min="0" step="1" class="form-control soft-input mono text-end" value="1" oninput="recalcVenta()"></td>
      <td class="text-end"><input type="number" min="0" step="0.01" class="form-control soft-input mono text-end" value="${rapida ? 10000 : 0}" oninput="recalcVenta()"></td>
      <td class="text-end">
        <select class="form-select soft-input mono" oninput="recalcVenta()">
          <option value="0">0%</option>
          <option value="10.5">10.5%</option>
          <option value="21" selected>21%</option>
          <option value="27">27%</option>
        </select>
      </td>
      <td class="text-end mono fw-semibold v_sub">$ 0,00</td>
      <td class="text-end no-print">
        <button class="btn btn-soft btn-sm" type="button" onclick="removeRowVenta(this)"><i class="bi bi-trash3"></i></button>
      </td>
    `;

    tbody.appendChild(tr);
    recalcVenta();
  }

  function removeRowVenta(btn){
    btn.closest('tr').remove();
    recalcVenta();
  }

  function recalcVenta(){
    const rows = [...document.querySelectorAll('#ventaBody tr')];
    let sub = 0;
    let iva = 0;

    rows.forEach(r=>{
      const inputs = r.querySelectorAll('input, select');
      const qty = Number(inputs[1].value || 0);
      const price = Number(inputs[2].value || 0);
      const ivaPct = Number(inputs[3].value || 0);

      const net = qty * price;
      const tax = net * (ivaPct/100);

      sub += net;
      iva += tax;

      r.querySelector('.v_sub').textContent = money(net + tax);
    });

    const d = Number((document.getElementById('t_desc').value || '0').replace(',', '.')) || 0;
    const totalSinDesc = sub + iva;
    const descMonto = totalSinDesc * (d/100);
    const total = totalSinDesc - descMonto;

    document.getElementById('t_sub').textContent = money(sub);
    document.getElementById('t_iva').textContent = money(iva);
    document.getElementById('t_total').textContent = money(total);
  }

  document.getElementById('t_desc').addEventListener('input', recalcVenta);

  function fillVentaDemo(){
    document.getElementById('v_num').textContent = 'V-000124';
    document.getElementById('c_nombre').value = 'ConstruMarket SRL';
    document.getElementById('c_doc').value = '30-12345678-9';
    document.getElementById('c_dir').value = 'Av. Ejemplo 123, Pilar';
    document.getElementById('v_vendedor').value = 'Sol';
    document.getElementById('v_obs').value = 'Entrega 48/72 hs. Pago por transferencia.';
    document.getElementById('t_desc').value = '0';

    document.getElementById('ventaBody').innerHTML = '';
    addRowVenta(true);
    addRowVenta(true);
    // ajustar segunda fila
    const rows = document.querySelectorAll('#ventaBody tr');
    if(rows[1]){
      rows[1].querySelectorAll('input')[0].value = 'Servicio técnico';
      rows[1].querySelectorAll('input')[2].value = 25000;
      rows[1].querySelector('select').value = '21';
    }
    recalcVenta();
  }

  function clearVenta(){
    document.getElementById('c_nombre').value = '';
    document.getElementById('c_doc').value = '';
    document.getElementById('c_dir').value = '';
    document.getElementById('v_vendedor').value = '';
    document.getElementById('v_obs').value = '';
    document.getElementById('t_desc').value = '0';
    document.getElementById('ventaBody').innerHTML = '';
    addRowVenta();
    recalcVenta();
  }

  function saveVentaDemo(){
    try{ bootstrap.Toast.getOrCreateInstance(document.getElementById('toastSaved')).show(); }catch(e){}
  }

  // init
  addRowVenta();
  recalcVenta();
</script>
