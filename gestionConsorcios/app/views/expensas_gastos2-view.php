<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// FIX: proteger contra user_data que sea string
$nombre_admin = 'Administracion';
$ud = isset($_SESSION['user_data']) ? $_SESSION['user_data'] : null;
if (is_object($ud) && isset($ud->nombre)) {
    $nombre_admin = $ud->nombre;
} elseif (is_array($ud) && isset($ud['nombre'])) {
    $nombre_admin = $ud['nombre'];
}

$contexto_id = isset($_SESSION['contexto_id']) ? $_SESSION['contexto_id'] : '';
$periodo_actual = date('Y-m');
?>

<style>
.gv *{box-sizing:border-box}
.gv .soft-card,.gv .mini-card{background:rgba(255,255,255,.96);border:1px solid #d5edd2;box-shadow:0 16px 35px rgba(16,168,8,.10)}
.gv .soft-card{border-radius:28px}
.gv .mini-card{border-radius:20px;padding:1rem;height:100%}
.gv .accent-line{width:92px;height:6px;border-radius:999px;background:linear-gradient(90deg,#f4c51c,#16c60c);margin-bottom:.9rem}
.gv .page-title{font-size:clamp(1.8rem,3vw,2.6rem);font-weight:900;color:#0b8707;line-height:1.05;margin-bottom:.2rem}
.gv .page-subtitle{color:#688162;font-size:.98rem;font-weight:700;line-height:1.5;margin:0}
.gv .section-title{font-size:1.12rem;color:#0b8707;font-weight:900;margin-bottom:.15rem}
.gv .section-subtitle{font-size:.87rem;color:#688162;font-weight:700;margin:0;line-height:1.5}
.gv .form-label{font-size:.9rem;color:#1f3f18;font-weight:900;margin-bottom:.45rem}
.gv .form-control,.gv .form-select{min-height:52px;border-radius:16px;border:2px solid #d5edd2;font-weight:800;color:#1f3f18;width:100%}
.gv .form-control:focus,.gv .form-select:focus{border-color:#16c60c;box-shadow:0 0 0 .25rem rgba(22,198,12,.12)}
.gv .btn-gescon{background:linear-gradient(135deg,#f4c51c,#d6a90c);border:none;color:#433900;border-radius:16px;min-height:48px;padding:.75rem 1.15rem;font-weight:900;box-shadow:0 10px 18px rgba(244,197,28,.20)}
.gv .btn-gescon:hover{color:#433900;opacity:.96}
.gv .btn-gescon-outline{background:#fff;color:#0b8707;border:2px solid #d5edd2;border-radius:16px;min-height:48px;padding:.75rem 1.15rem;font-weight:900}
.gv .btn-gescon-outline:hover{border-color:#16c60c;color:#0b8707}
.gv .btn-danger-sm{background:#fff;color:#b42318;border:2px solid #f3c2c2;border-radius:12px;padding:.4rem .75rem;font-weight:900;font-size:.82rem;cursor:pointer}
.gv .btn-danger-sm:hover{background:#ffe8e8;color:#b42318}
.gv .tag-soft{display:inline-flex;align-items:center;padding:.42rem .78rem;border-radius:999px;background:#eaffea;color:#0b8707;font-size:.74rem;font-weight:900;white-space:nowrap}
.gv .kpi-card{background:#fff;border:1px solid #d5edd2;border-radius:26px;box-shadow:0 16px 35px rgba(16,168,8,.10);padding:1.15rem;height:100%;position:relative;overflow:hidden}
.gv .kpi-card::after{content:"";position:absolute;width:84px;height:84px;border-radius:50%;background:rgba(22,198,12,.05);top:-18px;right:-18px}
.gv .kpi-label{font-size:.84rem;color:#688162;font-weight:800;margin-bottom:.45rem;position:relative;z-index:2}
.gv .kpi-value{font-size:clamp(1.5rem,2.2vw,2.1rem);color:#0b8707;font-weight:900;line-height:1.08;position:relative;z-index:2;white-space:nowrap}
.gv .kpi-icon{width:54px;height:54px;border-radius:18px;background:linear-gradient(135deg,#f4c51c,#ffd84f);color:#453c00;display:flex;align-items:center;justify-content:center;font-size:1.18rem;box-shadow:0 10px 20px rgba(244,197,28,.20);flex-shrink:0;position:relative;z-index:2}
.gv .kpi-foot{margin:.9rem 0 0;color:#688162;font-size:.86rem;font-weight:700;line-height:1.55;position:relative;z-index:2}
.gv .table-responsive{border-radius:18px;overflow:auto}
.gv .table-modern{margin:0;min-width:800px;width:100%}
.gv .table-modern thead th{background:#eaffea;color:#0b8707;font-size:.82rem;font-weight:900;border-bottom:none;white-space:nowrap;padding:.7rem 1rem}
.gv .table-modern td{color:#1f3f18;font-size:.91rem;font-weight:700;vertical-align:middle;white-space:nowrap;background:#fff;padding:.65rem 1rem}
.gv .table-modern tbody tr:hover td{background:#f6fff5}
.gv .table-modern tfoot td{background:#eaffea;color:#0b8707;font-weight:900;font-size:.95rem;padding:.75rem 1rem}
.gv .status-pill{display:inline-flex;align-items:center;padding:.38rem .72rem;border-radius:999px;font-size:.75rem;font-weight:900}
.gv .status-ok{background:#e9ffe8;color:#0b8707}
.gv .status-pending{background:#fff6d8;color:#9a7700}
.gv .status-alert{background:#ffe5e5;color:#b42318}
.gv .rubro-bar{height:8px;border-radius:99px;background:#eaffea;overflow:hidden}
.gv .rubro-bar-fill{height:100%;border-radius:99px;background:linear-gradient(90deg,#16c60c,#0b8707)}
.gv .alert-box{display:none;margin-bottom:1rem;border-radius:16px;padding:.9rem 1rem;font-weight:800}
.gv .empty-state{text-align:center;padding:2.5rem 1rem;color:#688162;font-weight:700}
.gv .empty-state i{font-size:2.5rem;color:#d5edd2;margin-bottom:.6rem}
.gv .modal-content{border:none;border-radius:24px;overflow:hidden}
.gv .modal-header{background:#eaffea;border-bottom:1px solid #d5edd2}
.gv .modal-header .modal-title{color:#0b8707;font-weight:900}
.gv .modal-body .form-control,.gv .modal-body .form-select{min-height:48px}
@media(max-width:575.98px){.gv .soft-card{border-radius:22px}.gv .mini-card,.gv .kpi-card{border-radius:18px}}
</style>

<div class="gv">

<!-- TOPBAR -->
<div class="soft-card p-3 p-md-4 mb-4">
  <div class="row g-3 align-items-center">
    <div class="col-12 col-lg-5">
      <div class="accent-line"></div>
      <h1 class="page-title">Gastos del periodo</h1>
      <p class="page-subtitle">Registra y controla los gastos del consorcio: servicios, mantenimiento, honorarios, seguros y mas.</p>
    </div>
    <div class="col-12 col-lg-7">
      <div class="row g-2 align-items-end justify-content-lg-end">
        <div class="col-6 col-sm-auto">
          <label class="form-label">Periodo</label>
          <input type="month" id="gvFiltroPeriodo" class="form-control" value="<?php echo $periodo_actual; ?>" style="min-width:170px;">
        </div>
        <div class="col-6 col-sm-auto">
          <label class="form-label">Buscar</label>
          <input type="text" id="gvBuscar" class="form-control" placeholder="Filtrar..." style="min-width:150px;">
        </div>
        <div class="col-12 col-sm-auto d-flex gap-2 align-items-end">
          <button type="button" class="btn btn-gescon-outline" id="gvBtnRefresh" title="Actualizar">
            <i class="bi bi-arrow-repeat"></i>
          </button>
          <button type="button" class="btn btn-gescon" data-bs-toggle="modal" data-bs-target="#gvModal">
            <i class="bi bi-plus-circle"></i> Nuevo gasto
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- KPIs -->
<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3">
    <div class="kpi-card">
      <div class="d-flex justify-content-between align-items-start gap-2">
        <div style="min-width:0"><div class="kpi-label">Total periodo</div><div class="kpi-value" id="gvKpiTotal">$ 0</div></div>
        <div class="kpi-icon"><i class="bi bi-cash-stack"></i></div>
      </div>
      <p class="kpi-foot">Suma de gastos activos (no anulados).</p>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="kpi-card">
      <div class="d-flex justify-content-between align-items-start gap-2">
        <div style="min-width:0"><div class="kpi-label">Gastos cargados</div><div class="kpi-value" id="gvKpiCant">0</div></div>
        <div class="kpi-icon"><i class="bi bi-receipt-cutoff"></i></div>
      </div>
      <p class="kpi-foot">Cantidad de registros en este periodo.</p>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="kpi-card">
      <div class="d-flex justify-content-between align-items-start gap-2">
        <div style="min-width:0"><div class="kpi-label">Rubros distintos</div><div class="kpi-value" id="gvKpiRubros">0</div></div>
        <div class="kpi-icon"><i class="bi bi-tags-fill"></i></div>
      </div>
      <p class="kpi-foot">Categorias de gasto utilizadas.</p>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="kpi-card">
      <div class="d-flex justify-content-between align-items-start gap-2">
        <div style="min-width:0"><div class="kpi-label">Periodo</div><div class="kpi-value" id="gvKpiPer"><?php echo date('m/Y'); ?></div></div>
        <div class="kpi-icon"><i class="bi bi-calendar3"></i></div>
      </div>
      <p class="kpi-foot">Mes de liquidacion activo.</p>
    </div>
  </div>
</div>

<!-- TABLA + RUBROS -->
<div class="row g-4 mb-4">
  <div class="col-12 col-xl-8">
    <div class="soft-card p-3 p-md-4 h-100">
      <div id="gvMsg" class="alert-box"></div>
      <div class="mb-3">
        <h2 class="section-title">Detalle de gastos</h2>
        <p class="section-subtitle">Listado completo del periodo seleccionado.</p>
      </div>
      <div class="table-responsive">
        <?php
        require_once 'app/model/GastosData.php';
$BarriosData = new BarriosData;
$barrio = $BarriosData->get_by_id($_SESSION['contexto_id']);


?>

        <table class="table table-modern align-middle">
          <thead>
            <tr>
              <th>Fecha</th><th>Rubro</th><th>Descripcion</th><th>Proveedor</th><th>Comprobante</th><th>Estado</th><th class="text-end">Monto</th><th></th>
            </tr>
          </thead>
          <tbody id="gvTbody">
            <tr><td colspan="8"><div class="empty-state"><i class="bi bi-inbox d-block"></i>Cargando...</div></td></tr>
          </tbody>
          <tfoot id="gvTfoot" style="display:none">
            <tr><td colspan="6" class="text-end">Total del periodo</td><td class="text-end" id="gvTotalPie">$ 0</td><td></td></tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
  <div class="col-12 col-xl-4">
    <div class="row g-4">
      <div class="col-12">
        <div class="soft-card p-3 p-md-4">
          <div class="mb-3"><h2 class="section-title">Resumen por rubro</h2><p class="section-subtitle">Distribucion de gastos del periodo.</p></div>
          <div id="gvRubros" class="d-grid gap-3">
            <div class="empty-state"><i class="bi bi-pie-chart d-block"></i>Carga gastos para ver el resumen.</div>
          </div>
        </div>
      </div>
      <div class="col-12">
        <div class="soft-card p-3 p-md-4">
          <div class="mb-3"><h2 class="section-title">Rubros habituales</h2><p class="section-subtitle">Categorias mas usadas.</p></div>
          <div class="d-flex flex-wrap gap-2">
            <span class="tag-soft">Mantenimiento</span><span class="tag-soft">Limpieza</span><span class="tag-soft">Seguridad</span><span class="tag-soft">Servicios</span><span class="tag-soft">Honorarios</span><span class="tag-soft">Seguros</span><span class="tag-soft">Reparaciones</span><span class="tag-soft">Sueldos</span><span class="tag-soft">Varios</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</div><!-- /gv -->

<!-- MODAL -->
<div class="modal fade gv" id="gvModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Nuevo gasto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div id="gvFormMsg" class="alert-box"></div>
        <form id="gvForm" autocomplete="off">
          <input type="hidden" name="id_empresa_administradora" value="<?php echo htmlspecialchars($contexto_id, ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="periodo" id="gvFormPeriodo" value="<?php echo $periodo_actual; ?>">
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label">Rubro *</label>
              <select name="rubro" class="form-select" required>
                <option value="">Seleccionar...</option>
                <option value="Mantenimiento">Mantenimiento</option>
                <option value="Limpieza">Limpieza</option>
                <option value="Seguridad">Seguridad</option>
                <option value="Servicios publicos">Servicios publicos (luz, agua, gas)</option>
                <option value="Honorarios administracion">Honorarios administracion</option>
                <option value="Seguros">Seguros</option>
                <option value="Reparaciones">Reparaciones</option>
                <option value="Jardineria">Jardineria</option>
                <option value="Ascensores">Ascensores</option>
                <option value="Pileta / SUM">Pileta / SUM</option>
                <option value="Impuestos y tasas">Impuestos y tasas</option>
                <option value="Sueldos y cargas">Sueldos y cargas sociales</option>
                <option value="Varios">Varios</option>
              </select>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Estado</label>
              <select name="estado" class="form-select">
                <option value="pendiente">Pendiente</option>
                <option value="pagado">Pagado</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Descripcion *</label>
              <input type="text" name="descripcion" class="form-control" placeholder="Ej: Factura de luz mes de marzo" required>
            </div>
            <div class="col-12 col-md-4">
              <label class="form-label">Monto ($) *</label>
              <input type="text" name="monto" class="form-control" placeholder="0.00" required inputmode="decimal">
            </div>
            <div class="col-12 col-md-4">
              <label class="form-label">Fecha del gasto</label>
              <input type="date" name="fecha_gasto" class="form-control" value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="col-12 col-md-4">
              <label class="form-label">Nro. comprobante</label>
              <input type="text" name="comprobante_nro" class="form-control" placeholder="Opcional">
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Proveedor</label>
              <input type="text" name="proveedor" class="form-control" placeholder="Nombre del proveedor">
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Observaciones</label>
              <input type="text" name="observaciones" class="form-control" placeholder="Nota opcional">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer border-0 px-4 pb-4">
        <button type="button" class="btn btn-gescon-outline" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-gescon" id="gvBtnSave"><i class="bi bi-check-circle"></i> Registrar gasto</button>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  /* FIX: asegurar barra final en la URL base para evitar redirect 301 que pierde POST */
  var B = '<?php echo rtrim(URL, "/"); ?>/';
  var CID = '<?php echo htmlspecialchars($contexto_id, ENT_QUOTES, "UTF-8"); ?>';
  var filtro = document.getElementById('gvFiltroPeriodo');
  var formPer = document.getElementById('gvFormPeriodo');
  var form = document.getElementById('gvForm');
  var tbody = document.getElementById('gvTbody');
  var tfoot = document.getElementById('gvTfoot');
  var buscar = document.getElementById('gvBuscar');
  var btnSave = document.getElementById('gvBtnSave');
  var btnRef = document.getElementById('gvBtnRefresh');
  var msg = document.getElementById('gvMsg');
  var fmsg = document.getElementById('gvFormMsg');
  var modalEl = document.getElementById('gvModal');
  var modal = null;

  if(modalEl && typeof bootstrap !== 'undefined'){
    modal = new bootstrap.Modal(modalEl);
  }

  filtro.addEventListener('change', function(){
    formPer.value = this.value;
    if(this.value){var p=this.value.split('-');document.getElementById('gvKpiPer').textContent=p[1]+'/'+p[0];}
    load();
  });

  btnRef.addEventListener('click', function(){ load(); });

  load();

  // GUARDAR
  btnSave.addEventListener('click', function(){
    var r = form.querySelector('[name="rubro"]').value;
    var d = form.querySelector('[name="descripcion"]').value.trim();
    var m = form.querySelector('[name="monto"]').value.trim();
    if(!r||!d||!m){showFMsg('error','Completa rubro, descripcion y monto.');return;}
    if(btnSave.dataset.busy==='1')return;
    btnSave.dataset.busy='1';
    btnSave.innerHTML='<i class="bi bi-hourglass-split"></i> Guardando...';
    var fd=new FormData(form);
    fetch(B+'?action=guardar_gasto',{method:'POST',body:fd})
    .then(function(r){return r.json();})
    .then(function(data){
      if(data.status==='ok'){
        form.reset();
        formPer.value=filtro.value;
        form.querySelector('[name="fecha_gasto"]').value=new Date().toISOString().slice(0,10);
        if(modal)modal.hide();
        load();
        showMsg('ok',data.message);
      }else{
        showFMsg('error',data.message||'Error al guardar.');
      }
      btnSave.dataset.busy='0';
      btnSave.innerHTML='<i class="bi bi-check-circle"></i> Registrar gasto';
    })
    .catch(function(){
      showFMsg('error','Error de conexion.');
      btnSave.dataset.busy='0';
      btnSave.innerHTML='<i class="bi bi-check-circle"></i> Registrar gasto';
    });
  });

  // BUSCAR
  buscar.addEventListener('input',function(){
    var q=this.value.toLowerCase().trim();
    var rows=tbody.querySelectorAll('tr[data-id]');
    rows.forEach(function(row){row.style.display=row.innerText.toLowerCase().includes(q)?'':'none';});
  });

  // CARGAR GASTOS
  function load(){
    var fd=new FormData();
    fd.append('id_empresa_administradora',CID);
    fd.append('periodo',filtro.value);
    fetch(B+'?action=listar_gastos',{method:'POST',body:fd})
    .then(function(r){return r.json();})
    .then(function(data){
      if(data.status!=='ok')return;
      drawTable(data.gastos,data.total);
      drawKPI(data);
      drawRubros(data.rubros,data.total);
    })
    .catch(function(){
      tbody.innerHTML='<tr><td colspan="8"><div class="empty-state"><i class="bi bi-wifi-off d-block"></i>Error de conexion.</div></td></tr>';
      tfoot.style.display='none';
    });
  }

  // TABLA
  function drawTable(g,total){
    if(!g||g.length===0){
      tbody.innerHTML='<tr><td colspan="8"><div class="empty-state"><i class="bi bi-inbox d-block"></i>No hay gastos en este periodo.<br>Usa <strong>Nuevo gasto</strong> para empezar.</div></td></tr>';
      tfoot.style.display='none';
      return;
    }
    var h='';
    g.forEach(function(r){
      var cls='status-pending',lbl=r.estado.charAt(0).toUpperCase()+r.estado.slice(1);
      if(r.estado==='pagado')cls='status-ok';
      if(r.estado==='anulado')cls='status-alert';
      var f=r.fecha_gasto?fmtF(r.fecha_gasto):'-';
      h+='<tr data-id="'+r.id+'"'+(r.estado==='anulado'?' style="opacity:.5"':'')+'>';
      h+='<td>'+f+'</td>';
      h+='<td><span class="tag-soft">'+esc(r.rubro)+'</span></td>';
      h+='<td style="white-space:normal;max-width:240px">'+esc(r.descripcion)+'</td>';
      h+='<td>'+esc(r.proveedor||'-')+'</td>';
      h+='<td>'+esc(r.comprobante_nro||'-')+'</td>';
      h+='<td><span class="status-pill '+cls+'">'+lbl+'</span></td>';
      h+='<td class="text-end" style="font-weight:900">$ '+fmtM(r.monto)+'</td>';
      h+='<td>';
      if(r.estado!=='anulado')h+='<button class="btn-danger-sm" onclick="gvAnular('+r.id+')" title="Anular"><i class="bi bi-x-circle"></i></button>';
      h+='</td></tr>';
    });
    tbody.innerHTML=h;
    document.getElementById('gvTotalPie').textContent='$ '+fmtM(total);
    tfoot.style.display='';
  }

  // KPIs
  function drawKPI(d){
    document.getElementById('gvKpiTotal').textContent='$ '+fmtM(d.total);
    document.getElementById('gvKpiCant').textContent=d.cantidad;
    document.getElementById('gvKpiRubros').textContent=d.rubros?d.rubros.length:0;
  }

  // RUBROS
  function drawRubros(rubros,total){
    var c=document.getElementById('gvRubros');
    if(!rubros||rubros.length===0){c.innerHTML='<div class="empty-state"><i class="bi bi-pie-chart d-block"></i>Carga gastos para ver el resumen.</div>';return;}
    var h='';
    rubros.forEach(function(r){
      var pct=total>0?Math.round((parseFloat(r.total)/total)*100):0;
      h+='<div class="mini-card">';
      h+='<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">';
      h+='<div><strong style="color:#1f3f18;font-size:.92rem">'+esc(r.rubro)+'</strong>';
      h+=' <small style="color:#688162;font-weight:700">('+r.cantidad+')</small></div>';
      h+='<span class="tag-soft">$ '+fmtM(r.total)+'</span></div>';
      h+='<div class="rubro-bar"><div class="rubro-bar-fill" style="width:'+pct+'%"></div></div>';
      h+='<small style="color:#688162;font-weight:700">'+pct+'% del total</small></div>';
    });
    c.innerHTML=h;
  }

  // ANULAR
  window.gvAnular=function(id){
    if(!confirm('Anular este gasto? Queda marcado como anulado.'))return;
    var fd=new FormData();fd.append('id',id);fd.append('accion','anular');
    fetch(B+'?action=eliminar_gasto',{method:'POST',body:fd})
    .then(function(r){return r.json();})
    .then(function(data){showMsg(data.status,data.message);if(data.status==='ok')load();})
    .catch(function(){showMsg('error','Error de conexion.');});
  };

  // HELPERS
  function showMsg(t,m){
    msg.style.display='block';
    msg.style.background=t==='ok'?'#eaffea':'#ffe8e8';
    msg.style.border='1px solid '+(t==='ok'?'#d5edd2':'#f3c2c2');
    msg.style.color=t==='ok'?'#0b8707':'#b42318';
    msg.innerHTML='<i class="bi bi-'+(t==='ok'?'check-circle':'exclamation-circle')+'"></i> '+esc(m);
    setTimeout(function(){msg.style.display='none';},4000);
  }
  function showFMsg(t,m){
    fmsg.style.display='block';
    fmsg.style.background=t==='ok'?'#eaffea':'#ffe8e8';
    fmsg.style.border='1px solid '+(t==='ok'?'#d5edd2':'#f3c2c2');
    fmsg.style.color=t==='ok'?'#0b8707':'#b42318';
    fmsg.innerHTML='<i class="bi bi-exclamation-circle"></i> '+esc(m);
    setTimeout(function(){fmsg.style.display='none';},4000);
  }
  function fmtM(n){return parseFloat(n).toLocaleString('es-AR',{minimumFractionDigits:2,maximumFractionDigits:2});}
  function fmtF(d){var p=d.split('-');return p[2]+'/'+p[1]+'/'+p[0];}
  function esc(s){if(!s)return '';var d=document.createElement('div');d.appendChild(document.createTextNode(s));return d.innerHTML;}
})();
</script>