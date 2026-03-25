<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nombre_admin = 'Administracion';
$ud = isset($_SESSION['user_data']) ? $_SESSION['user_data'] : null;
if (is_object($ud) && isset($ud->nombre)) {
    $nombre_admin = $ud->nombre;
} elseif (is_array($ud) && isset($ud['nombre'])) {
    $nombre_admin = $ud['nombre'];
}

$contexto_id = '';
if (isset($_SESSION['contexto_id']) && $_SESSION['contexto_id'] !== '') {
    $contexto_id = $_SESSION['contexto_id'];
} else {
    require_once __DIR__ . '/../database.php';
    $tmpCon = Database::getCon();
    $tmpQ = mysqli_query($tmpCon, "SELECT DISTINCT `id_empresa_administradora` FROM `gastos` LIMIT 1");
    if ($tmpQ && $tmpR = mysqli_fetch_assoc($tmpQ)) $contexto_id = $tmpR['id_empresa_administradora'];
}

$periodo_actual = date('Y-m');
?>

<style>
.gl *{box-sizing:border-box}
.gl .soft-card{background:rgba(255,255,255,.96);border:1px solid #d5edd2;box-shadow:0 16px 35px rgba(16,168,8,.10);border-radius:28px}
.gl .accent-line{width:92px;height:6px;border-radius:999px;background:linear-gradient(90deg,#f4c51c,#16c60c);margin-bottom:.9rem}
.gl .page-title{font-size:clamp(1.8rem,3vw,2.6rem);font-weight:900;color:#0b8707;line-height:1.05;margin-bottom:.2rem}
.gl .page-subtitle{color:#688162;font-size:.98rem;font-weight:700;line-height:1.5;margin:0}
.gl .form-label{font-size:.85rem;color:#1f3f18;font-weight:900;margin-bottom:.35rem}
.gl .form-control,.gl .form-select{min-height:46px;border-radius:14px;border:2px solid #d5edd2;font-weight:800;color:#1f3f18;width:100%;font-size:.9rem}
.gl .form-control:focus,.gl .form-select:focus{border-color:#16c60c;box-shadow:0 0 0 .25rem rgba(22,198,12,.12)}
.gl .btn-gescon{background:linear-gradient(135deg,#f4c51c,#d6a90c);border:none;color:#433900;border-radius:14px;min-height:46px;padding:.65rem 1.1rem;font-weight:900;box-shadow:0 10px 18px rgba(244,197,28,.20);font-size:.9rem}
.gl .btn-gescon:hover{color:#433900;opacity:.96}
.gl .btn-gescon-outline{background:#fff;color:#0b8707;border:2px solid #d5edd2;border-radius:14px;min-height:46px;padding:.65rem 1.1rem;font-weight:900;font-size:.9rem}
.gl .btn-gescon-outline:hover{border-color:#16c60c;color:#0b8707}
.gl .btn-generar{background:linear-gradient(135deg,#0b8707,#16c60c);border:none;color:#fff;border-radius:14px;min-height:46px;padding:.65rem 1.1rem;font-weight:900;box-shadow:0 10px 18px rgba(16,168,8,.20);font-size:.9rem}
.gl .btn-generar:hover{opacity:.92;color:#fff}
.gl .btn-danger-xs{background:none;color:#b42318;border:none;padding:.2rem .4rem;font-size:.85rem;cursor:pointer;opacity:.6}
.gl .btn-danger-xs:hover{opacity:1}
.gl .tag-soft{display:inline-flex;align-items:center;padding:.32rem .65rem;border-radius:999px;background:#eaffea;color:#0b8707;font-size:.72rem;font-weight:900;white-space:nowrap}
.gl .alert-box{display:none;margin-bottom:1rem;border-radius:14px;padding:.8rem 1rem;font-weight:800;font-size:.9rem}
.gl .ledger{border:1px solid #d5edd2;border-radius:22px;overflow:hidden;background:#fff}
.gl .ledger-grid{display:grid;grid-template-columns:80px 120px 1fr 110px 75px 100px 32px;align-items:center;padding:0 1.2rem;gap:0 .6rem}
.gl .ledger-header{background:#eaffea;border-bottom:2px solid #d5edd2}
.gl .ledger-header .ledger-grid{padding-top:.7rem;padding-bottom:.7rem}
.gl .ledger-header-col{font-size:.76rem;font-weight:900;color:#0b8707;text-transform:uppercase;letter-spacing:.04em;white-space:nowrap}
.gl .ledger-header-col.text-end{text-align:right}
.gl .ledger-body{max-height:55vh;overflow-y:auto}
.gl .ledger-row{border-bottom:1px solid #f0f7ee;transition:background .15s}
.gl .ledger-row .ledger-grid{padding-top:.65rem;padding-bottom:.65rem}
.gl .ledger-row:hover{background:#f8fff7}
.gl .ledger-row.anulado{opacity:.45}
.gl .ledger-row.anulado .lc-desc,.gl .ledger-row.anulado .lc-monto{text-decoration:line-through}
.gl .ledger-cell{font-size:.9rem;font-weight:700;color:#1f3f18;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.gl .ledger-cell.desc{white-space:normal;word-break:break-word}
.gl .ledger-cell.monto{font-weight:900;text-align:right;color:#0b8707;font-size:.95rem}
.gl .ledger-footer{background:#eaffea;padding:.8rem 1.2rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;border-top:2px solid #d5edd2;flex-wrap:wrap}
.gl .ledger-total-label{font-size:.9rem;font-weight:900;color:#0b8707}
.gl .ledger-total-value{font-size:1.3rem;font-weight:900;color:#0b8707}
.gl .ledger-new{background:#f8fff7;padding:1rem 1.2rem;border-top:2px dashed #d5edd2}
.gl .ledger-new .form-control,.gl .ledger-new .form-select{min-height:42px;font-size:.85rem;border-radius:12px}
.gl .ledger-empty{text-align:center;padding:2.5rem 1rem;color:#688162;font-weight:700}
.gl .ledger-empty i{font-size:2rem;color:#d5edd2;display:block;margin-bottom:.5rem}
.gl .summary-item{padding:.7rem 0;border-bottom:1px solid #f0f7ee}
.gl .summary-item:last-child{border-bottom:none}
.gl .summary-rubro{font-weight:900;color:#1f3f18;font-size:.9rem}
.gl .summary-count{color:#688162;font-weight:700;font-size:.78rem}
.gl .summary-amount{font-weight:900;color:#0b8707;font-size:.9rem}
.gl .summary-bar{height:6px;border-radius:99px;background:#eaffea;overflow:hidden;margin-top:.35rem}
.gl .summary-bar-fill{height:100%;border-radius:99px;background:linear-gradient(90deg,#16c60c,#0b8707)}
.gl .modal-content{border:none;border-radius:24px;overflow:hidden}
.gl .modal-header{background:#eaffea;border-bottom:1px solid #d5edd2}
.gl .modal-header .modal-title{color:#0b8707;font-weight:900}
.gl .result-card{background:#f8fff7;border:1px solid #d5edd2;border-radius:16px;padding:1rem;margin-top:.8rem}
.gl .result-row{display:flex;justify-content:space-between;padding:.3rem 0;font-weight:700;font-size:.9rem}
.gl .result-row .label{color:#688162}
.gl .result-row .value{color:#0b8707;font-weight:900}
.gl .det-grid{display:grid;grid-template-columns:1fr 90px 90px 90px;gap:0 .5rem;font-size:.82rem;font-weight:700}
.gl .det-header{color:#0b8707;font-weight:900;padding:.4rem 0;border-bottom:2px solid #d5edd2}
.gl .det-row{padding:.3rem 0;border-bottom:1px solid #f0f7ee}
.gl .det-row .debt{color:#b42318}
@media(max-width:991.98px){.gl .ledger-grid{grid-template-columns:70px 100px 1fr 70px 90px 28px}.gl .lc-prov{display:none}}
@media(max-width:575.98px){.gl .soft-card{border-radius:20px}.gl .ledger{border-radius:16px}.gl .ledger-grid{grid-template-columns:60px 80px 1fr 80px 28px;padding:0 .7rem;gap:0 .4rem}.gl .lc-prov,.gl .lc-estado{display:none}.gl .det-grid{grid-template-columns:1fr 70px 70px 70px;font-size:.75rem}}
</style>

<div class="gl">

<div class="soft-card p-3 p-md-4 mb-4">
  <div class="row g-3 align-items-center">
    <div class="col-12 col-lg-5">
      <div class="accent-line"></div>
      <h1 class="page-title">Libro de gastos</h1>
      <p class="page-subtitle">Hoja de contabilidad del consorcio. Carga gastos y genera expensas automaticamente.</p>
    </div>
    <div class="col-12 col-lg-7">
      <div class="row g-2 align-items-end justify-content-lg-end">
        <div class="col-6 col-sm-auto">
          <label class="form-label">Periodo</label>
          <input type="month" id="glFiltroPeriodo" class="form-control" value="<?php echo $periodo_actual; ?>" style="min-width:170px;">
        </div>
        <div class="col-6 col-sm-auto">
          <label class="form-label">Buscar</label>
          <input type="text" id="glBuscar" class="form-control" placeholder="Filtrar..." style="min-width:150px;">
        </div>
        <div class="col-auto d-flex gap-2 align-items-end">
          <button type="button" class="btn btn-gescon-outline" id="glBtnRefresh" title="Actualizar"><i class="bi bi-arrow-repeat"></i></button>
          <button type="button" class="btn btn-generar" data-bs-toggle="modal" data-bs-target="#glModalExp"><i class="bi bi-lightning-charge-fill"></i> Generar expensas</button>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="glMsg" class="alert-box"></div>

<div class="row g-4">
  <div class="col-12 col-xl-8">
    <div class="ledger">
      <div class="ledger-header"><div class="ledger-grid">
        <div class="ledger-header-col">Fecha</div><div class="ledger-header-col">Rubro</div><div class="ledger-header-col">Concepto</div><div class="ledger-header-col lc-prov">Proveedor</div><div class="ledger-header-col lc-estado">Estado</div><div class="ledger-header-col text-end">Importe</div><div class="ledger-header-col"></div>
      </div></div>
      <div class="ledger-body" id="glBody"><div class="ledger-empty"><i class="bi bi-journal-text"></i>Cargando...</div></div>
      <div class="ledger-new">
        <div style="font-size:.85rem;font-weight:900;color:#0b8707;margin-bottom:.6rem"><i class="bi bi-plus-circle"></i> Nuevo asiento</div>
        <form id="glForm" autocomplete="off">
          <input type="hidden" name="id_empresa_administradora" value="<?php echo htmlspecialchars($contexto_id, ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="periodo" id="glFormPeriodo" value="<?php echo $periodo_actual; ?>">
          <div class="row g-2 align-items-end">
            <div class="col-6 col-md-2"><label class="form-label">Fecha</label><input type="date" name="fecha_gasto" class="form-control" value="<?php echo date('Y-m-d'); ?>"></div>
            <div class="col-6 col-md-2"><label class="form-label">Rubro *</label><select name="rubro" class="form-select" required><option value="">...</option><option value="Mantenimiento">Mantenimiento</option><option value="Limpieza">Limpieza</option><option value="Seguridad">Seguridad</option><option value="Servicios publicos">Servicios publicos</option><option value="Honorarios">Honorarios</option><option value="Seguros">Seguros</option><option value="Reparaciones">Reparaciones</option><option value="Jardineria">Jardineria</option><option value="Ascensores">Ascensores</option><option value="Pileta / SUM">Pileta / SUM</option><option value="Impuestos y tasas">Impuestos</option><option value="Sueldos y cargas">Sueldos</option><option value="Varios">Varios</option></select></div>
            <div class="col-12 col-md-3"><label class="form-label">Concepto *</label><input type="text" name="descripcion" class="form-control" placeholder="Descripcion del gasto" required></div>
            <div class="col-6 col-md-2"><label class="form-label">Importe ($) *</label><input type="text" name="monto" class="form-control" placeholder="0.00" required inputmode="decimal"></div>
            <div class="col-6 col-md-1"><label class="form-label">Estado</label><select name="estado" class="form-select"><option value="pendiente">Pend.</option><option value="pagado">Pagado</option></select></div>
            <div class="col-12 col-md-2"><button type="button" class="btn btn-gescon w-100" id="glBtnSave"><i class="bi bi-plus-lg"></i> Cargar</button></div>
          </div>
          <div class="row g-2 mt-1">
            <div class="col-6 col-md-3"><input type="text" name="proveedor" class="form-control" placeholder="Proveedor (opcional)" style="min-height:38px;font-size:.82rem"></div>
            <div class="col-6 col-md-3"><input type="text" name="comprobante_nro" class="form-control" placeholder="Nro comprobante (opcional)" style="min-height:38px;font-size:.82rem"></div>
            <div class="col-12 col-md-6"><input type="text" name="observaciones" class="form-control" placeholder="Observaciones (opcional)" style="min-height:38px;font-size:.82rem"></div>
          </div>
        </form>
      </div>
      <div class="ledger-footer"><div><span class="ledger-total-label">Total del periodo:</span> <span class="ledger-total-value" id="glTotal">$ 0,00</span></div></div>
    </div>
  </div>
  <div class="col-12 col-xl-4">
    <div class="soft-card p-3 p-md-4 h-100">
      <div style="font-size:1.05rem;font-weight:900;color:#0b8707;margin-bottom:.3rem">Resumen por rubro</div>
      <div style="font-size:.84rem;color:#688162;font-weight:700;margin-bottom:1rem">Periodo: <strong id="glResumenPer"><?php echo date('m/Y'); ?></strong></div>
      <div id="glRubros"><div class="ledger-empty"><i class="bi bi-pie-chart"></i>Carga gastos para ver el resumen.</div></div>
      <div style="margin-top:1.2rem;padding-top:1rem;border-top:2px solid #eaffea">
        <div class="d-flex justify-content-between align-items-center"><span style="font-weight:900;color:#1f3f18;font-size:.95rem">Total general</span><span style="font-weight:900;color:#0b8707;font-size:1.15rem" id="glSideTotal">$ 0,00</span></div>
        <div class="d-flex justify-content-between align-items-center mt-2"><span style="font-weight:700;color:#688162;font-size:.85rem">Asientos</span><span style="font-weight:900;color:#1f3f18;font-size:.95rem" id="glSideCant">0</span></div>
      </div>
    </div>
  </div>
</div>
</div>

<!-- MODAL GENERAR EXPENSAS -->
<div class="modal fade gl" id="glModalExp" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-lightning-charge-fill"></i> Generar expensas del periodo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div id="glExpMsg"></div>
        <div id="glExpPaso1">
          <p style="font-weight:700;color:#1f3f18;font-size:.95rem;margin-bottom:.6rem">Esto va a:</p>
          <p style="font-weight:700;color:#688162;font-size:.88rem;line-height:1.6;margin:0">
            <i class="bi bi-1-circle-fill" style="color:#0b8707"></i> Tomar el <strong>total de gastos</strong> del periodo y repartirlo por <strong>coeficiente</strong> entre las unidades activas.<br>
            <i class="bi bi-2-circle-fill" style="color:#0b8707"></i> Detectar <strong>deuda impaga</strong> de periodos anteriores y arrastrarla como un cargo adicional.
          </p>
          <div class="result-card">
            <div class="result-row"><span class="label">Periodo</span><span class="value" id="glExpPeriodo"></span></div>
            <div class="result-row"><span class="label">Total gastos</span><span class="value" id="glExpTotal"></span></div>
            <div class="result-row"><span class="label">Dia vencimiento</span><span class="value"><input type="number" id="glExpVenc" value="10" min="1" max="28" style="width:50px;text-align:center;border:2px solid #d5edd2;border-radius:8px;font-weight:900;color:#0b8707"></span></div>
          </div>
          <p style="font-weight:700;color:#688162;font-size:.83rem;margin-top:.8rem"><i class="bi bi-shield-check"></i> Si ya se generaron para este periodo, no se duplican.</p>
        </div>
        <div id="glExpPaso2" style="display:none"><div id="glExpRes"></div></div>
      </div>
      <div class="modal-footer border-0 px-4 pb-4" id="glExpFooter">
        <button type="button" class="btn btn-gescon-outline" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-generar" id="glBtnGen"><i class="bi bi-lightning-charge-fill"></i> Confirmar y generar</button>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  var B='<?php echo rtrim(URL, "/"); ?>/';
  var CID='<?php echo htmlspecialchars($contexto_id, ENT_QUOTES, "UTF-8"); ?>';
  var filtro=document.getElementById('glFiltroPeriodo'),formPer=document.getElementById('glFormPeriodo'),form=document.getElementById('glForm'),body=document.getElementById('glBody'),buscar=document.getElementById('glBuscar'),btnSave=document.getElementById('glBtnSave'),btnRef=document.getElementById('glBtnRefresh'),msg=document.getElementById('glMsg'),lastTotal=0;

  filtro.addEventListener('change',function(){formPer.value=this.value;if(this.value){var p=this.value.split('-');document.getElementById('glResumenPer').textContent=p[1]+'/'+p[0];}load();});
  btnRef.addEventListener('click',function(){load();});
  load();

  // GUARDAR
  btnSave.addEventListener('click',function(){
    var r=form.querySelector('[name="rubro"]').value,d=form.querySelector('[name="descripcion"]').value.trim(),m=form.querySelector('[name="monto"]').value.trim();
    if(!r||!d||!m){showMsg('error','Completa rubro, concepto e importe.');return;}
    if(btnSave.dataset.busy==='1')return;btnSave.dataset.busy='1';btnSave.innerHTML='<i class="bi bi-hourglass-split"></i>';
    fetch(B+'?action=guardar_gasto',{method:'POST',body:new FormData(form)})
    .then(function(r){return r.json();}).then(function(data){
      if(data.status==='ok'){['rubro','descripcion','monto','proveedor','comprobante_nro','observaciones'].forEach(function(n){form.querySelector('[name="'+n+'"]').value='';});form.querySelector('[name="estado"]').value='pendiente';load();showMsg('ok',data.message);}
      else showMsg('error',data.message||'Error.');
      btnSave.dataset.busy='0';btnSave.innerHTML='<i class="bi bi-plus-lg"></i> Cargar';
    }).catch(function(){showMsg('error','Error de conexion.');btnSave.dataset.busy='0';btnSave.innerHTML='<i class="bi bi-plus-lg"></i> Cargar';});
  });

  buscar.addEventListener('input',function(){var q=this.value.toLowerCase().trim();body.querySelectorAll('.ledger-row').forEach(function(row){row.style.display=row.innerText.toLowerCase().includes(q)?'':'none';});});

  function load(){
    var fd=new FormData();fd.append('id_empresa_administradora',CID);fd.append('periodo',filtro.value);
    fetch(B+'?action=listar_gastos',{method:'POST',body:fd})
    .then(function(r){return r.json();}).then(function(data){
      if(data.status!=='ok'){body.innerHTML='<div class="ledger-empty"><i class="bi bi-exclamation-triangle"></i>'+esc(data.message||'Error')+'</div>';return;}
      lastTotal=data.total;drawLedger(data.gastos);drawRubros(data.rubros,data.total);
      document.getElementById('glTotal').textContent='$ '+fmtM(data.total);
      document.getElementById('glSideTotal').textContent='$ '+fmtM(data.total);
      document.getElementById('glSideCant').textContent=data.cantidad;
    }).catch(function(){body.innerHTML='<div class="ledger-empty"><i class="bi bi-wifi-off"></i>Error de conexion.</div>';});
  }

  function drawLedger(g){
    if(!g||g.length===0){body.innerHTML='<div class="ledger-empty"><i class="bi bi-journal-text"></i>No hay asientos en este periodo.<br>Usa el formulario de abajo para cargar el primer gasto.</div>';return;}
    var h='';g.forEach(function(r){
      var isA=r.estado==='anulado',f=r.fecha_gasto?fmtF(r.fecha_gasto):'-',est='';
      if(r.estado==='pagado')est='<span class="tag-soft" style="background:#e9ffe8">Pagado</span>';
      else if(isA)est='<span class="tag-soft" style="background:#ffe5e5;color:#b42318">Anulado</span>';
      else est='<span class="tag-soft" style="background:#fff6d8;color:#9a7700">Pend.</span>';
      h+='<div class="ledger-row'+(isA?' anulado':'')+'" data-id="'+r.id+'"><div class="ledger-grid">';
      h+='<div class="ledger-cell">'+f+'</div><div class="ledger-cell"><span class="tag-soft">'+esc(r.rubro)+'</span></div>';
      h+='<div class="ledger-cell desc lc-desc">'+esc(r.descripcion)+'</div>';
      h+='<div class="ledger-cell lc-prov" style="color:#688162;font-size:.85rem">'+esc(r.proveedor||'-')+'</div>';
      h+='<div class="ledger-cell lc-estado">'+est+'</div>';
      h+='<div class="ledger-cell monto lc-monto">$ '+fmtM(r.monto)+'</div>';
      h+='<div class="ledger-cell">';if(!isA)h+='<button class="btn-danger-xs" onclick="glAnular('+r.id+')" title="Anular"><i class="bi bi-x-lg"></i></button>';
      h+='</div></div></div>';
    });body.innerHTML=h;
  }

  function drawRubros(rubros,total){
    var c=document.getElementById('glRubros');
    if(!rubros||rubros.length===0){c.innerHTML='<div class="ledger-empty"><i class="bi bi-pie-chart"></i>Sin datos.</div>';return;}
    var h='';rubros.forEach(function(r){
      var pct=total>0?Math.round((parseFloat(r.total)/total)*100):0;
      h+='<div class="summary-item"><div class="d-flex justify-content-between align-items-center"><div><span class="summary-rubro">'+esc(r.rubro)+'</span> <span class="summary-count">'+r.cantidad+'</span></div><span class="summary-amount">$ '+fmtM(r.total)+'</span></div><div class="summary-bar"><div class="summary-bar-fill" style="width:'+pct+'%"></div></div></div>';
    });c.innerHTML=h;
  }

  window.glAnular=function(id){if(!confirm('Anular este asiento?'))return;var fd=new FormData();fd.append('id',id);fd.append('accion','anular');
    fetch(B+'?action=eliminar_gasto',{method:'POST',body:fd}).then(function(r){return r.json();}).then(function(data){showMsg(data.status,data.message);if(data.status==='ok')load();}).catch(function(){showMsg('error','Error.');});};

  // === GENERAR EXPENSAS ===
  var mEl=document.getElementById('glModalExp');
  mEl.addEventListener('show.bs.modal',function(){
    var p=filtro.value,pts=p.split('-');
    document.getElementById('glExpPeriodo').textContent=pts[1]+'/'+pts[0];
    document.getElementById('glExpTotal').textContent='$ '+fmtM(lastTotal);
    document.getElementById('glExpPaso1').style.display='';
    document.getElementById('glExpPaso2').style.display='none';
    document.getElementById('glExpFooter').style.display='';
    document.getElementById('glExpMsg').innerHTML='';
  });

  document.getElementById('glBtnGen').addEventListener('click',function(){
    var btn=this;if(btn.dataset.busy==='1')return;
    btn.dataset.busy='1';btn.innerHTML='<i class="bi bi-hourglass-split"></i> Generando...';
    var fd=new FormData();fd.append('id_empresa_administradora',CID);fd.append('periodo',filtro.value);fd.append('dia_vencimiento',document.getElementById('glExpVenc').value);

    fetch(B+'?action=generar_expensas',{method:'POST',body:fd})
    .then(function(r){return r.json();}).then(function(data){
      btn.dataset.busy='0';btn.innerHTML='<i class="bi bi-lightning-charge-fill"></i> Confirmar y generar';

      if(data.status==='ok'){
        document.getElementById('glExpPaso1').style.display='none';
        document.getElementById('glExpPaso2').style.display='';
        document.getElementById('glExpFooter').style.display='none';
        var res=data.resumen;

        var h='<div style="text-align:center;margin-bottom:1rem"><i class="bi bi-check-circle-fill" style="font-size:2.5rem;color:#0b8707"></i>';
        h+='<div style="font-weight:900;color:#0b8707;font-size:1.05rem;margin-top:.4rem">'+esc(data.message)+'</div></div>';

        h+='<div class="result-card">';
        h+='<div class="result-row"><span class="label">Total gastos</span><span class="value">$ '+fmtM(res.total_gastos)+'</span></div>';
        h+='<div class="result-row"><span class="label">Expensas generadas</span><span class="value">'+res.expensas_generadas+'</span></div>';
        h+='<div class="result-row"><span class="label">Deudas arrastradas</span><span class="value">'+(res.deudas_arrastradas>0?'<span style="color:#b42318">'+res.deudas_arrastradas+'</span>':'0')+'</span></div>';
        h+='<div class="result-row"><span class="label">Vencimiento</span><span class="value">'+esc(res.fecha_vencimiento)+'</span></div>';
        h+='</div>';

        // Detalle por unidad con columnas: Unidad | Expensa | Deuda ant | Total
        if(data.detalle&&data.detalle.length>0){
          h+='<div style="margin-top:1rem;max-height:250px;overflow-y:auto">';
          h+='<div class="det-grid det-header"><div>Unidad</div><div style="text-align:right">Expensa</div><div style="text-align:right">Deuda ant.</div><div style="text-align:right">Total</div></div>';
          data.detalle.forEach(function(d){
            h+='<div class="det-grid det-row">';
            h+='<div>'+esc(d.unidad)+'</div>';
            h+='<div style="text-align:right;color:#0b8707">$ '+fmtM(d.expensa)+'</div>';
            h+='<div style="text-align:right" class="'+(d.deuda_ant>0?'debt':'')+'">$ '+fmtM(d.deuda_ant)+'</div>';
            h+='<div style="text-align:right;font-weight:900;color:#1f3f18">$ '+fmtM(d.total)+'</div>';
            h+='</div>';
          });
          h+='</div>';
        }

        document.getElementById('glExpRes').innerHTML=h;
      }else{
        document.getElementById('glExpMsg').innerHTML='<div style="background:#ffe8e8;border:1px solid #f3c2c2;color:#b42318;border-radius:14px;padding:.8rem 1rem;font-weight:800;margin-bottom:.8rem"><i class="bi bi-exclamation-circle"></i> '+esc(data.message)+'</div>';
      }
    }).catch(function(){
      btn.dataset.busy='0';btn.innerHTML='<i class="bi bi-lightning-charge-fill"></i> Confirmar y generar';
      document.getElementById('glExpMsg').innerHTML='<div style="background:#ffe8e8;border:1px solid #f3c2c2;color:#b42318;border-radius:14px;padding:.8rem 1rem;font-weight:800">Error de conexion.</div>';
    });
  });

  function showMsg(t,m){msg.style.display='block';msg.style.background=t==='ok'?'#eaffea':'#ffe8e8';msg.style.border='1px solid '+(t==='ok'?'#d5edd2':'#f3c2c2');msg.style.color=t==='ok'?'#0b8707':'#b42318';msg.innerHTML='<i class="bi bi-'+(t==='ok'?'check-circle':'exclamation-circle')+'"></i> '+esc(m);setTimeout(function(){msg.style.display='none';},3500);}
  function fmtM(n){return parseFloat(n).toLocaleString('es-AR',{minimumFractionDigits:2,maximumFractionDigits:2});}
  function fmtF(d){var p=d.split('-');return p[2]+'/'+p[1];}
  function esc(s){if(!s)return '';var d=document.createElement('div');d.appendChild(document.createTextNode(s));return d.innerHTML;}
})();
</script>