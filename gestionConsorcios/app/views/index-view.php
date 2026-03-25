<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'app/model/BarriosData.php';
require_once 'app/model/UnidadesFuncionalesData.php';
require_once 'app/model/PersonasData.php';

$BarriosData = new BarriosData;
$barrio = $BarriosData->get_by_id($_SESSION['contexto_id']);

$tipo_contexto_actual = isset($_SESSION['tipo_contexto']) ? $_SESSION['tipo_contexto'] : 'No definido';
$contexto_id_actual = isset($_SESSION['contexto_id']) ? $_SESSION['contexto_id'] : 'No definido';

$UnidadesData = new UnidadesFuncionalesData;
$unidades = $UnidadesData->get_by_id($_SESSION['contexto_id']);
?>

<style>
.dash .soft-card{background:rgba(255,255,255,.94);border:1px solid #d5edd2;box-shadow:0 16px 35px rgba(16,168,8,.10);border-radius:28px}
.dash .accent-line{width:92px;height:6px;border-radius:999px;background:linear-gradient(90deg,#f4c51c,#16c60c);margin-bottom:.9rem}
.dash .page-title{font-size:clamp(1.7rem,3vw,2.5rem);font-weight:900;color:#0b8707;line-height:1.05;margin-bottom:.2rem}
.dash .page-subtitle{color:#688162;font-size:.98rem;font-weight:700;line-height:1.5;margin:0}
.dash .kpi-card{background:#fff;border:1px solid #d5edd2;border-radius:26px;box-shadow:0 16px 35px rgba(16,168,8,.10);padding:1.2rem;height:100%;position:relative;overflow:hidden}
.dash .kpi-card::after{content:"";position:absolute;width:90px;height:90px;border-radius:50%;background:rgba(22,198,12,.05);top:-20px;right:-20px}
.dash .kpi-label{font-size:.84rem;color:#688162;font-weight:800;margin-bottom:.45rem;position:relative;z-index:2}
.dash .kpi-value{font-size:clamp(1.5rem,2.2vw,2.1rem);color:#0b8707;font-weight:900;line-height:1.08;position:relative;z-index:2}
.dash .kpi-icon{width:54px;height:54px;border-radius:18px;background:linear-gradient(135deg,#f4c51c,#ffd84f);color:#453c00;display:flex;align-items:center;justify-content:center;font-size:1.18rem;box-shadow:0 10px 20px rgba(244,197,28,.20);flex-shrink:0;position:relative;z-index:2}
.dash .section-title{font-size:1.18rem;color:#0b8707;font-weight:900;margin-bottom:.15rem}
.dash .section-subtitle{font-size:.87rem;color:#688162;font-weight:700;margin:0}
.dash .table-modern{margin:0;min-width:600px}
.dash .table-modern thead th{background:#eaffea;color:#0b8707;font-size:.82rem;font-weight:900;border-bottom:none;white-space:nowrap}
.dash .table-modern td{color:#1f3f18;font-size:.91rem;font-weight:700;vertical-align:middle;white-space:nowrap}
.dash .status-pill{display:inline-flex;align-items:center;padding:.42rem .78rem;border-radius:999px;font-size:.75rem;font-weight:900}
.dash .status-ok{background:#e9ffe8;color:#0b8707}
.dash .status-pending{background:#fff6d8;color:#9a7700}
.dash .status-alert{background:#ffe8e8;color:#b42318}
.dash .btn-gescon{background:linear-gradient(135deg,#f4c51c,#d6a90c);border:none;color:#433900;border-radius:16px;min-height:46px;padding:.7rem 1.1rem;font-weight:900;box-shadow:0 10px 18px rgba(244,197,28,.20);text-decoration:none;display:inline-flex;align-items:center;gap:.4rem}
.dash .chart-container{position:relative;width:100%;max-height:280px}
.dash .deudor-item{padding:.7rem 0;border-bottom:1px solid #f0f7ee;display:flex;justify-content:space-between;align-items:center}
.dash .deudor-item:last-child{border-bottom:none}
@media(max-width:575.98px){.dash .soft-card{border-radius:20px}.dash .kpi-card{border-radius:18px}}
</style>

<div class="dash">

<!-- Header -->
<div class="soft-card p-3 p-md-4 mb-4">
  <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
    <div>
      <div class="accent-line"></div>
      <h1 class="page-title">Dashboard</h1>
      <p class="page-subtitle">Panel de control del consorcio. Datos en tiempo real.</p>
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:.65rem;">
      <span class="status-pill status-ok">
        Tipo: <?= htmlspecialchars($tipo_contexto_actual, ENT_QUOTES, 'UTF-8') ?>
      </span>
      <span class="status-pill status-pending">
        <?= htmlspecialchars($barrio['nombre'] ?? 'Sin nombre', ENT_QUOTES, 'UTF-8') ?>
      </span>
    </div>
  </div>
</div>

<!-- KPIs -->
<div class="row g-3 g-xl-4 mb-4">
  <div class="col-6 col-xl-3">
    <div class="kpi-card">
      <div class="d-flex justify-content-between align-items-start gap-3">
        <div><div class="kpi-label">Unidades</div><div class="kpi-value" id="dashUF">--</div></div>
        <div class="kpi-icon"><i class="bi bi-building"></i></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="kpi-card">
      <div class="d-flex justify-content-between align-items-start gap-3">
        <div><div class="kpi-label">Personas</div><div class="kpi-value" id="dashPersonas">--</div></div>
        <div class="kpi-icon"><i class="bi bi-people-fill"></i></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="kpi-card">
      <div class="d-flex justify-content-between align-items-start gap-3">
        <div><div class="kpi-label">Cobrado</div><div class="kpi-value" id="dashCobrado" style="color:#0b8707;">--</div></div>
        <div class="kpi-icon"><i class="bi bi-cash-stack"></i></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="kpi-card">
      <div class="d-flex justify-content-between align-items-start gap-3">
        <div><div class="kpi-label">Pendiente</div><div class="kpi-value" id="dashPendiente" style="color:#b42318;">--</div></div>
        <div class="kpi-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
      </div>
    </div>
  </div>
</div>

<!-- Charts row -->
<div class="row g-4 mb-4">
  <div class="col-12 col-xl-8">
    <div class="soft-card p-3 p-md-4 h-100">
      <h2 class="section-title">Gastos por periodo</h2>
      <p class="section-subtitle mb-3">Evolucion de gastos en los ultimos periodos.</p>
      <div class="chart-container">
        <canvas id="chartGastos"></canvas>
      </div>
    </div>
  </div>
  <div class="col-12 col-xl-4">
    <div class="soft-card p-3 p-md-4 h-100">
      <h2 class="section-title">Gastos por rubro</h2>
      <p class="section-subtitle mb-3">Distribucion del ultimo periodo.</p>
      <div class="chart-container">
        <canvas id="chartRubros"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Cobranzas chart + top deudores -->
<div class="row g-4 mb-4">
  <div class="col-12 col-xl-7">
    <div class="soft-card p-3 p-md-4 h-100">
      <h2 class="section-title">Cobranzas por periodo</h2>
      <p class="section-subtitle mb-3">Emitido vs cobrado por mes.</p>
      <div class="chart-container">
        <canvas id="chartCobranzas"></canvas>
      </div>
    </div>
  </div>
  <div class="col-12 col-xl-5">
    <div class="soft-card p-3 p-md-4 h-100">
      <h2 class="section-title">Top deudores</h2>
      <p class="section-subtitle mb-3">Unidades con mayor saldo pendiente.</p>
      <div id="dashDeudores">
        <div style="text-align:center;padding:1.5rem;color:#688162;font-weight:700;"><i class="bi bi-hourglass-split"></i> Cargando...</div>
      </div>
    </div>
  </div>
</div>

<!-- Unidades funcionales table -->
<div class="soft-card p-3 p-md-4 mb-4">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
    <div>
      <h2 class="section-title">Unidades funcionales</h2>
      <p class="section-subtitle">Listado de unidades del consorcio.</p>
    </div>
    <a href="?view=expensas_gastos" class="btn btn-gescon"><i class="bi bi-journal-text"></i> Ir a gastos</a>
  </div>

  <div class="table-responsive" style="border-radius:18px;">
    <table class="table table-modern align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Tipo</th>
          <th>Coeficiente</th>
          <th>Ocupacion</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php if(empty($unidades)): ?>
        <tr><td colspan="6" style="text-align:center;padding:2rem;color:#688162;font-weight:700;">No hay unidades funcionales cargadas.</td></tr>
        <?php else: ?>
        <?php foreach ($unidades as $uf): ?>
        <tr>
          <td><?= htmlspecialchars($uf['id'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($uf['nombre'] ?? $uf['codigo'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($uf['tipo_unidad'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($uf['coeficiente_expensas'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          <td>
            <?php
            $eo = $uf['estado_ocupacion'] ?? '';
            $co = $eo === 'ocupada' ? 'status-ok' : ($eo === 'desocupada' ? 'status-alert' : 'status-pending');
            ?>
            <span class="status-pill <?= $co ?>"><?= htmlspecialchars($eo, ENT_QUOTES, 'UTF-8') ?></span>
          </td>
          <td>
            <?php
            $ea = $uf['estado_administrativo'] ?? '';
            $ca = $ea === 'activa' ? 'status-ok' : 'status-alert';
            ?>
            <span class="status-pill <?= $ca ?>"><?= htmlspecialchars($ea, ENT_QUOTES, 'UTF-8') ?></span>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
(function(){
  var B='<?= rtrim(URL, "/") ?>/';

  fetch(B+'?action=dashboard_data')
  .then(function(r){return r.json();})
  .then(function(d){
    if(d.status!=='ok') return;

    // KPIs
    var uf=d.unidades||{};
    var per=d.personas||{};
    var cob=d.cobranzas||{};
    document.getElementById('dashUF').textContent=(uf.activas||0)+' activas';
    document.getElementById('dashPersonas').textContent=(per.total||0);
    document.getElementById('dashCobrado').textContent='$ '+fmtM(cob.total_cobrado||0);
    document.getElementById('dashPendiente').textContent='$ '+fmtM(cob.total_pendiente||0);

    // Chart: Gastos por periodo
    var gp=d.gastos_por_periodo||[];
    if(gp.length>0){
      new Chart(document.getElementById('chartGastos'),{
        type:'bar',
        data:{
          labels:gp.map(function(g){var p=g.periodo.split('-');return p[1]+'/'+p[0];}),
          datasets:[{label:'Gastos',data:gp.map(function(g){return parseFloat(g.total);}),backgroundColor:'rgba(22,198,12,0.7)',borderRadius:8,borderSkipped:false}]
        },
        options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{callback:function(v){return '$ '+fmtM(v);}}}}}
      });
    }

    // Chart: Rubros (doughnut)
    var gr=d.gastos_por_rubro||[];
    if(gr.length>0){
      var colors=['#16c60c','#f4c51c','#10a808','#d6a90c','#0b8707','#ffd84f','#688162','#1f3f18','#d5edd2','#eaffea'];
      new Chart(document.getElementById('chartRubros'),{
        type:'doughnut',
        data:{
          labels:gr.map(function(r){return r.rubro||'Sin rubro';}),
          datasets:[{data:gr.map(function(r){return parseFloat(r.total);}),backgroundColor:colors.slice(0,gr.length),borderWidth:2,borderColor:'#fff'}]
        },
        options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{font:{size:11,weight:'bold'},padding:8}}}}
      });
    }

    // Chart: Cobranzas por periodo
    var cp=d.cobranzas_por_periodo||[];
    if(cp.length>0){
      new Chart(document.getElementById('chartCobranzas'),{
        type:'bar',
        data:{
          labels:cp.map(function(c){var p=c.periodo.split('-');return p[1]+'/'+p[0];}),
          datasets:[
            {label:'Emitido',data:cp.map(function(c){return parseFloat(c.emitido);}),backgroundColor:'rgba(22,198,12,0.6)',borderRadius:6,borderSkipped:false},
            {label:'Cobrado',data:cp.map(function(c){return parseFloat(c.cobrado);}),backgroundColor:'rgba(244,197,28,0.7)',borderRadius:6,borderSkipped:false},
            {label:'Pendiente',data:cp.map(function(c){return parseFloat(c.pendiente);}),backgroundColor:'rgba(180,35,24,0.5)',borderRadius:6,borderSkipped:false}
          ]
        },
        options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{labels:{font:{weight:'bold'}}}},scales:{y:{beginAtZero:true,ticks:{callback:function(v){return '$ '+fmtM(v);}}}}}
      });
    }

    // Top deudores
    var td=d.top_deudores||[];
    var el=document.getElementById('dashDeudores');
    if(td.length===0){
      el.innerHTML='<div style="text-align:center;padding:1.5rem;color:#688162;font-weight:700;"><i class="bi bi-check-circle" style="font-size:1.5rem;color:#0b8707;display:block;margin-bottom:.3rem;"></i>No hay deudores registrados.</div>';
    }else{
      var h='';
      td.forEach(function(d,i){
        var nombre=(d.persona_nombre||'')+' '+(d.persona_apellido||'');
        nombre=nombre.trim()||'Sin asignar';
        var unidad=d.unidad||('UF '+d.unidad_funcional_id);
        h+='<div class="deudor-item">';
        h+='<div><span style="font-weight:900;color:#1f3f18;font-size:.9rem;">'+(i+1)+'. '+esc(unidad)+'</span><br><span style="font-size:.82rem;color:#688162;font-weight:700;">'+esc(nombre)+'</span></div>';
        h+='<span style="font-weight:900;color:#b42318;font-size:1rem;">$ '+fmtM(d.deuda_total)+'</span>';
        h+='</div>';
      });
      el.innerHTML=h;
    }
  })
  .catch(function(e){
    console.error('Error loading dashboard:', e);
  });

  function fmtM(n){return parseFloat(n||0).toLocaleString('es-AR',{minimumFractionDigits:2,maximumFractionDigits:2});}
  function esc(s){if(!s)return '';var d=document.createElement('div');d.appendChild(document.createTextNode(s));return d.innerHTML;}
})();
</script>
