<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$contexto_id = '';
if (isset($_SESSION['contexto_id']) && $_SESSION['contexto_id'] !== '') {
    $contexto_id = $_SESSION['contexto_id'];
}
?>
<style>
.cob .soft-card{background:rgba(255,255,255,.94);border:1px solid #d5edd2;box-shadow:0 16px 35px rgba(16,168,8,.10);border-radius:28px}
.cob .accent-line{width:92px;height:6px;border-radius:999px;background:linear-gradient(90deg,#f4c51c,#16c60c);margin-bottom:.9rem}
.cob .page-title{font-size:clamp(1.7rem,3vw,2.5rem);font-weight:900;color:#0b8707;line-height:1.05;margin-bottom:.2rem}
.cob .page-subtitle{color:#688162;font-size:.98rem;font-weight:700;line-height:1.5;margin:0}
.cob .kpi-card{background:#fff;border:1px solid #d5edd2;border-radius:26px;box-shadow:0 16px 35px rgba(16,168,8,.10);padding:1.2rem;height:100%;position:relative;overflow:hidden}
.cob .kpi-card::after{content:"";position:absolute;width:90px;height:90px;border-radius:50%;background:rgba(22,198,12,.05);top:-20px;right:-20px}
.cob .kpi-label{font-size:.84rem;color:#688162;font-weight:800;margin-bottom:.45rem;position:relative;z-index:2}
.cob .kpi-value{font-size:clamp(1.5rem,2.2vw,2.1rem);color:#0b8707;font-weight:900;line-height:1.08;position:relative;z-index:2}
.cob .kpi-icon{width:54px;height:54px;border-radius:18px;background:linear-gradient(135deg,#f4c51c,#ffd84f);color:#453c00;display:flex;align-items:center;justify-content:center;font-size:1.18rem;box-shadow:0 10px 20px rgba(244,197,28,.20);flex-shrink:0;position:relative;z-index:2}
.cob .kpi-foot{margin:.9rem 0 0;color:#688162;font-size:.86rem;font-weight:700;line-height:1.55;position:relative;z-index:2}
.cob .section-title{font-size:1.18rem;color:#0b8707;font-weight:900;margin-bottom:.15rem}
.cob .section-subtitle{font-size:.87rem;color:#688162;font-weight:700;margin:0}
.cob .table-modern{margin:0;min-width:700px}
.cob .table-modern thead th{background:#eaffea;color:#0b8707;font-size:.82rem;font-weight:900;border-bottom:none;white-space:nowrap}
.cob .table-modern td{color:#1f3f18;font-size:.91rem;font-weight:700;vertical-align:middle;white-space:nowrap;background:#fff}
.cob .status-pill{display:inline-flex;align-items:center;padding:.42rem .78rem;border-radius:999px;font-size:.75rem;font-weight:900}
.cob .status-ok{background:#e9ffe8;color:#0b8707}
.cob .status-pending{background:#fff6d8;color:#9a7700}
.cob .status-alert{background:#ffe5e5;color:#b42318}
.cob .btn-gescon{background:linear-gradient(135deg,#f4c51c,#d6a90c);border:none;color:#433900;border-radius:16px;min-height:46px;padding:.7rem 1.1rem;font-weight:900;box-shadow:0 10px 18px rgba(244,197,28,.20)}
.cob .btn-gescon:hover{color:#433900;opacity:.96}
.cob .form-control,.cob .form-select{min-height:46px;border-radius:14px;border:2px solid #d5edd2;font-weight:800;color:#1f3f18}
.cob .form-control:focus,.cob .form-select:focus{border-color:#16c60c;box-shadow:0 0 0 .25rem rgba(22,198,12,.12)}
.cob .search-input{min-height:50px;border-radius:16px;border:2px solid #d5edd2;font-weight:800;color:#1f3f18;background:#fff}
.cob .search-input:focus{border-color:#16c60c;box-shadow:0 0 0 .25rem rgba(22,198,12,.12)}
@media(max-width:575.98px){.cob .soft-card{border-radius:20px}.cob .kpi-card{border-radius:18px}}
</style>

<div class="cob">

<div class="soft-card p-3 p-md-4 mb-4">
  <div class="row g-3 align-items-center">
    <div class="col-12 col-lg-5">
      <div class="accent-line"></div>
      <h1 class="page-title">Cobranzas y deuda</h1>
      <p class="page-subtitle">Seguimiento de expensas emitidas, pagos y morosidad por unidad funcional.</p>
    </div>
    <div class="col-12 col-lg-7">
      <div class="row g-2 align-items-end justify-content-lg-end">
        <div class="col-6 col-sm-auto">
          <label class="form-label fw-bold" style="font-size:.85rem;">Periodo</label>
          <select id="cobFiltroPeriodo" class="form-select" style="min-width:160px;">
            <option value="">Todos los periodos</option>
          </select>
        </div>
        <div class="col-6 col-sm-auto">
          <label class="form-label fw-bold" style="font-size:.85rem;">Buscar</label>
          <input type="text" id="cobBuscar" class="form-control" placeholder="Filtrar..." style="min-width:150px;">
        </div>
        <div class="col-auto">
          <button type="button" class="btn btn-gescon" id="cobBtnRefresh"><i class="bi bi-arrow-repeat"></i> Actualizar</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- KPIs -->
<div class="row g-3 g-xl-4 mb-4">
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="kpi-card">
      <div class="d-flex justify-content-between align-items-start gap-3">
        <div><div class="kpi-label">Total emitido</div><div class="kpi-value" id="kpiEmitido">$ 0</div></div>
        <div class="kpi-icon"><i class="bi bi-receipt-cutoff"></i></div>
      </div>
      <p class="kpi-foot">Importe total de expensas generadas.</p>
    </div>
  </div>
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="kpi-card">
      <div class="d-flex justify-content-between align-items-start gap-3">
        <div><div class="kpi-label">Total cobrado</div><div class="kpi-value" id="kpiCobrado">$ 0</div></div>
        <div class="kpi-icon"><i class="bi bi-cash-stack"></i></div>
      </div>
      <p class="kpi-foot">Pagos acreditados hasta la fecha.</p>
    </div>
  </div>
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="kpi-card">
      <div class="d-flex justify-content-between align-items-start gap-3">
        <div><div class="kpi-label">Saldo pendiente</div><div class="kpi-value" id="kpiPendiente">$ 0</div></div>
        <div class="kpi-icon"><i class="bi bi-exclamation-diamond-fill"></i></div>
      </div>
      <p class="kpi-foot">Deuda acumulada de todas las unidades.</p>
    </div>
  </div>
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="kpi-card">
      <div class="d-flex justify-content-between align-items-start gap-3">
        <div><div class="kpi-label">Morosos</div><div class="kpi-value" id="kpiMorosos">0</div></div>
        <div class="kpi-icon"><i class="bi bi-person-exclamation"></i></div>
      </div>
      <p class="kpi-foot">Unidades con saldo pendiente mayor a 0.</p>
    </div>
  </div>
</div>

<!-- Tabla -->
<div class="soft-card p-3 p-md-4 mb-4">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
    <div>
      <h2 class="section-title">Estado de cobranzas</h2>
      <p class="section-subtitle">Detalle por unidad funcional con estado de pago y saldo.</p>
    </div>
    <div><span class="fw-bold" style="color:#688162;font-size:.9rem;" id="cobCantidad">0 registros</span></div>
  </div>

  <div class="table-responsive" style="border-radius:18px;">
    <table class="table table-modern align-middle" id="tablaCobros">
      <thead>
        <tr>
          <th>Periodo</th>
          <th>Unidad</th>
          <th>Persona</th>
          <th>Concepto</th>
          <th>Emitido</th>
          <th>Pagado</th>
          <th>Saldo</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody id="cobBody">
        <tr><td colspan="8" style="text-align:center;padding:2rem;color:#688162;font-weight:700;">
          <i class="bi bi-hourglass-split" style="font-size:1.5rem;display:block;margin-bottom:.3rem;"></i>Cargando...
        </td></tr>
      </tbody>
    </table>
  </div>
</div>

</div>

<script>
(function(){
  var B='<?= rtrim(URL, "/") ?>/';
  var CID='<?= htmlspecialchars($contexto_id, ENT_QUOTES, "UTF-8") ?>';
  var filtro=document.getElementById('cobFiltroPeriodo');
  var buscar=document.getElementById('cobBuscar');
  var body=document.getElementById('cobBody');

  document.getElementById('cobBtnRefresh').addEventListener('click', load);
  filtro.addEventListener('change', load);
  buscar.addEventListener('input', function(){
    var q=this.value.toLowerCase().trim();
    document.querySelectorAll('#cobBody tr.cob-row').forEach(function(row){
      row.style.display=row.innerText.toLowerCase().includes(q)?'':'none';
    });
  });

  load();

  function load(){
    var fd=new FormData();
    fd.append('id_empresa_administradora', CID);
    if(filtro.value) fd.append('periodo', filtro.value);

    fetch(B+'?action=listar_cobranzas',{method:'POST',body:fd})
    .then(function(r){return r.json();})
    .then(function(data){
      if(data.status!=='ok'){
        body.innerHTML='<tr><td colspan="8" style="text-align:center;padding:2rem;color:#b42318;font-weight:700;">'+esc(data.message||'Error')+'</td></tr>';
        return;
      }

      // KPIs
      var k=data.kpi;
      document.getElementById('kpiEmitido').textContent='$ '+fmtM(k.total_emitido);
      document.getElementById('kpiCobrado').textContent='$ '+fmtM(k.total_cobrado);
      document.getElementById('kpiPendiente').textContent='$ '+fmtM(k.total_pendiente);
      document.getElementById('kpiMorosos').textContent=k.morosos;
      document.getElementById('cobCantidad').textContent=k.total_registros+' registros';

      // Periodos dropdown (only first load)
      if(filtro.options.length<=1 && data.periodos && data.periodos.length>0){
        data.periodos.forEach(function(p){
          var opt=document.createElement('option');
          opt.value=p;
          var pts=p.split('-');
          opt.textContent=pts[1]+'/'+pts[0];
          filtro.appendChild(opt);
        });
      }

      // Table
      var cobs=data.cobranzas;
      if(!cobs||cobs.length===0){
        body.innerHTML='<tr><td colspan="8" style="text-align:center;padding:2rem;color:#688162;font-weight:700;"><i class="bi bi-inbox" style="font-size:1.5rem;display:block;margin-bottom:.3rem;"></i>No hay cobranzas registradas.<br>Genera expensas desde la seccion de Gastos.</td></tr>';
        return;
      }

      var h='';
      cobs.forEach(function(c){
        var unidad=c.unidad_nombre||c.unidad_codigo||('UF '+c.unidad_funcional_id);
        var persona=(c.persona_nombre||'')+' '+(c.persona_apellido||'');
        persona=persona.trim()||'Sin asignar';
        var per=c.periodo?fmtPer(c.periodo):'-';
        var saldo=parseFloat(c.saldo);

        var estadoClass='status-pending';
        var estadoText=c.estado||'pendiente';
        if(c.estado==='pagada'){estadoClass='status-ok';estadoText='Pagada';}
        else if(saldo>0){estadoClass='status-alert';estadoText='Pendiente';}
        else{estadoClass='status-ok';estadoText='Al dia';}

        h+='<tr class="cob-row">';
        h+='<td>'+esc(per)+'</td>';
        h+='<td>'+esc(unidad)+'</td>';
        h+='<td>'+esc(persona)+'</td>';
        h+='<td>'+esc(c.concepto||'-')+'</td>';
        h+='<td>$ '+fmtM(c.importe)+'</td>';
        h+='<td>$ '+fmtM(c.importe_pagado)+'</td>';
        h+='<td style="'+(saldo>0?'color:#b42318;font-weight:900;':'')+'">$ '+fmtM(saldo)+'</td>';
        h+='<td><span class="status-pill '+estadoClass+'">'+estadoText+'</span></td>';
        h+='</tr>';
      });
      body.innerHTML=h;
    })
    .catch(function(){
      body.innerHTML='<tr><td colspan="8" style="text-align:center;padding:2rem;color:#b42318;font-weight:700;">Error de conexion.</td></tr>';
    });
  }

  function fmtM(n){return parseFloat(n||0).toLocaleString('es-AR',{minimumFractionDigits:2,maximumFractionDigits:2});}
  function fmtPer(p){var pts=p.split('-');return pts[1]+'/'+pts[0];}
  function esc(s){if(!s)return '';var d=document.createElement('div');d.appendChild(document.createTextNode(s));return d.innerHTML;}
})();
</script>
