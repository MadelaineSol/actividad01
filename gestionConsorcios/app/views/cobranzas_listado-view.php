<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nombre_admin = 'Administración';
if (isset($_SESSION['user_data'])) {
    if (is_object($_SESSION['user_data']) && isset($_SESSION['user_data']->nombre)) {
        $nombre_admin = $_SESSION['user_data']->nombre;
    } elseif (is_array($_SESSION['user_data']) && isset($_SESSION['user_data']['nombre'])) {
        $nombre_admin = $_SESSION['user_data']['nombre'];
    }
}



require_once 'app/model/CobranzasData.php';
$CobranzasData = new CobranzasData;

$cobranzas=$CobranzasData->get_cobranza_by_id_empresa($_SESSION['contexto_id']);

$contexto_id = isset($_SESSION['contexto_id']) ? (int)$_SESSION['contexto_id'] : 0;
$tipo_contexto = isset($_SESSION['tipo_contexto']) ? $_SESSION['tipo_contexto'] : 'No definido';







?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

<style>
.cobranzas-view *{
  box-sizing:border-box;
}

.cobranzas-view{
  width:100%;
  max-width:100%;
  overflow-x:hidden;
}

.cobranzas-view .soft-card,
.cobranzas-view .mini-card,
.cobranzas-view .kpi-card{
  background:rgba(255,255,255,.96);
  border:1px solid #d5edd2;
  box-shadow:0 16px 35px rgba(16,168,8,.10);
}

.cobranzas-view .soft-card{
  border-radius:28px;
}

.cobranzas-view .mini-card{
  border-radius:20px;
  padding:1rem;
  height:100%;
}

.cobranzas-view .kpi-card{
  border-radius:24px;
  padding:1.15rem;
  height:100%;
  position:relative;
  overflow:hidden;
}

.cobranzas-view .kpi-card::after{
  content:"";
  position:absolute;
  width:90px;
  height:90px;
  border-radius:50%;
  background:rgba(22,198,12,.05);
  top:-18px;
  right:-18px;
}

.cobranzas-view .accent-line{
  width:92px;
  height:6px;
  border-radius:999px;
  background:linear-gradient(90deg,#f4c51c 0%,#16c60c 100%);
  margin-bottom:.9rem;
}

.cobranzas-view .page-title{
  font-size:clamp(1.8rem,3vw,2.6rem);
  font-weight:900;
  color:#0b8707;
  line-height:1.05;
  margin-bottom:.2rem;
}

.cobranzas-view .page-subtitle{
  color:#688162;
  font-size:.98rem;
  font-weight:700;
  line-height:1.5;
  margin:0;
}

.cobranzas-view .section-title{
  font-size:1.12rem;
  color:#0b8707;
  font-weight:900;
  margin-bottom:.15rem;
}

.cobranzas-view .section-subtitle{
  font-size:.87rem;
  color:#688162;
  font-weight:700;
  margin:0;
  line-height:1.5;
}

.cobranzas-view .tag-soft{
  display:inline-flex;
  align-items:center;
  padding:.42rem .78rem;
  border-radius:999px;
  background:#eaffea;
  color:#0b8707;
  font-size:.74rem;
  font-weight:900;
  white-space:nowrap;
}

.cobranzas-view .status-pill{
  display:inline-flex;
  align-items:center;
  padding:.42rem .78rem;
  border-radius:999px;
  font-size:.75rem;
  font-weight:900;
}

.cobranzas-view .status-ok{
  background:#e9ffe8;
  color:#0b8707;
}

.cobranzas-view .status-pending{
  background:#fff6d8;
  color:#9a7700;
}

.cobranzas-view .status-alert{
  background:#ffe8e8;
  color:#b42318;
}

.cobranzas-view .btn-gescon{
  background:linear-gradient(135deg,#f4c51c 0%,#d6a90c 100%) !important;
  border:none !important;
  color:#433900 !important;
  border-radius:14px !important;
  min-height:42px;
  padding:.6rem 1rem !important;
  font-weight:900 !important;
  box-shadow:0 10px 18px rgba(244,197,28,.20);
}

.cobranzas-view .btn-gescon-outline{
  background:#fff !important;
  color:#0b8707 !important;
  border:2px solid #d5edd2 !important;
  border-radius:14px !important;
  min-height:42px;
  padding:.6rem 1rem !important;
  font-weight:900 !important;
}

.cobranzas-view .kpi-label{
  font-size:.83rem;
  color:#688162;
  font-weight:800;
  margin-bottom:.35rem;
  position:relative;
  z-index:2;
}

.cobranzas-view .kpi-value{
  font-size:clamp(1.45rem,2.2vw,2rem);
  color:#0b8707;
  font-weight:900;
  line-height:1.08;
  position:relative;
  z-index:2;
}

.cobranzas-view .kpi-foot{
  margin:.8rem 0 0;
  color:#688162;
  font-size:.84rem;
  font-weight:700;
  line-height:1.5;
  position:relative;
  z-index:2;
}

.cobranzas-view .table-wrap{
  width:100%;
  max-width:100%;
  overflow-x:auto;
  overflow-y:hidden;
  border-radius:20px;
  -webkit-overflow-scrolling:touch;
}

.cobranzas-view table.dataTable{
  width:100% !important;
  min-width:1000px;
  border-collapse:separate !important;
  border-spacing:0;
  margin:0 !important;
}

.cobranzas-view table.dataTable thead th{
  background:#eaffea !important;
  color:#0b8707 !important;
  font-size:.80rem;
  font-weight:900;
  border-bottom:none !important;
  white-space:normal;
  line-height:1.25;
}

.cobranzas-view table.dataTable tbody td{
  color:#1f3f18;
  font-size:.88rem;
  font-weight:700;
  vertical-align:middle;
  white-space:normal;
  line-height:1.35;
}

.cobranzas-view .col-detalle{
  min-width:220px;
}

.cobranzas-view .col-concepto{
  min-width:130px;
}

.cobranzas-view .col-importe,
.cobranzas-view .col-pagado,
.cobranzas-view .col-saldo{
  min-width:110px;
  white-space:nowrap !important;
}

.cobranzas-view .col-fecha{
  min-width:110px;
  white-space:nowrap !important;
}

.cobranzas-view .col-estado{
  min-width:110px;
  white-space:nowrap !important;
}

.cobranzas-view .dataTables_wrapper{
  width:100%;
  overflow:hidden;
}

.cobranzas-view .dt-buttons{
  display:flex;
  flex-wrap:wrap;
  gap:.6rem;
  margin-bottom:1rem;
}

.cobranzas-view .dataTables_wrapper .dataTables_filter{
  text-align:right;
}

.cobranzas-view .dataTables_wrapper .dataTables_filter label,
.cobranzas-view .dataTables_wrapper .dataTables_length label{
  font-weight:800;
  color:#688162;
}

.cobranzas-view .dataTables_wrapper .dataTables_filter input,
.cobranzas-view .dataTables_wrapper .dataTables_length select{
  border:2px solid #d5edd2 !important;
  border-radius:12px !important;
  min-height:40px;
  padding:.35rem .7rem;
  font-weight:800;
  color:#1f3f18;
  background:#fff;
}

.cobranzas-view .dataTables_wrapper .dataTables_filter input:focus,
.cobranzas-view .dataTables_wrapper .dataTables_length select:focus{
  outline:none;
  border-color:#16c60c !important;
  box-shadow:0 0 0 .25rem rgba(22,198,12,.12) !important;
}

.cobranzas-view .pagination .page-link{
  color:#0b8707;
  font-weight:900;
  border-radius:10px !important;
  margin:0 .15rem;
  border:1px solid #d5edd2;
}

.cobranzas-view .pagination .active .page-link{
  background:#16c60c !important;
  border-color:#16c60c !important;
  color:#fff !important;
}

.cobranzas-view .filter-box{
  background:#f8fff7;
  border:1px solid #d5edd2;
  border-radius:18px;
  padding:1rem;
}

.cobranzas-view .form-control,
.cobranzas-view .form-select{
  min-height:48px;
  border-radius:14px;
  border:2px solid #d5edd2;
  font-weight:800;
  color:#1f3f18;
}

.cobranzas-view .form-control:focus,
.cobranzas-view .form-select:focus{
  border-color:#16c60c;
  box-shadow:0 0 0 .25rem rgba(22,198,12,.12);
}

@media (max-width:1199.98px){
  .cobranzas-view table.dataTable{
    min-width:920px;
  }
}

@media (max-width:991.98px){
  .cobranzas-view .dataTables_wrapper .dataTables_filter{
    text-align:left;
  }
}

@media (max-width:767.98px){
  .cobranzas-view table.dataTable{
    min-width:860px;
  }
}

@media (max-width:575.98px){
  .cobranzas-view .soft-card{
    border-radius:22px;
  }

  .cobranzas-view .mini-card,
  .cobranzas-view .kpi-card{
    border-radius:18px;
  }

  .cobranzas-view .dt-buttons{
    flex-direction:column;
    align-items:stretch;
  }

  .cobranzas-view .dt-buttons .btn{
    width:100%;
  }
}
</style>

<div class="cobranzas-view">
  <div class="row g-4">

    <div class="col-12">
      <div class="soft-card p-3 p-md-4">
        <div class="row g-4 align-items-center">
          <div class="col-12 col-xxl-8">
            <div class="accent-line"></div>
            <h1 class="page-title">Cobranzas y deudas</h1>
            <p class="page-subtitle">Controlá importes emitidos, pagos registrados, saldos pendientes y estados de cobranza del contexto activo.</p>
          </div>

          <div class="col-12 col-xxl-4">
            <div class="mini-card">
              <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                  <div class="section-title">Contexto activo</div>
                  <div class="section-subtitle"><?= htmlspecialchars(isset($barrio['nombre']) ? $barrio['nombre'] : 'Sin contexto', ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <span class="tag-soft"><?= htmlspecialchars($tipo_contexto, ENT_QUOTES, 'UTF-8'); ?></span>
              </div>
              <div style="margin-top:.7rem;color:#688162;font-size:.84rem;font-weight:700;">
                Administrador: <?= htmlspecialchars($nombre_admin, ENT_QUOTES, 'UTF-8'); ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-xxl-3">
      <div class="kpi-card">
        <div class="kpi-label">Total emitido</div>
        <div class="kpi-value">$ <?= number_format($total_emitido, 2, ',', '.'); ?></div>
        <p class="kpi-foot">Suma total de cobranzas generadas en el contexto.</p>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-xxl-3">
      <div class="kpi-card">
        <div class="kpi-label">Total cobrado</div>
        <div class="kpi-value">$ <?= number_format($total_pagado, 2, ',', '.'); ?></div>
        <p class="kpi-foot">Importe ya registrado como abonado.</p>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-xxl-3">
      <div class="kpi-card">
        <div class="kpi-label">Saldo pendiente</div>
        <div class="kpi-value">$ <?= number_format($total_saldo, 2, ',', '.'); ?></div>
        <p class="kpi-foot">Monto que todavía falta cobrar.</p>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-xxl-3">
      <div class="kpi-card">
        <div class="kpi-label">Cobranzas abiertas</div>
        <div class="kpi-value"><?= (int)$total_pendientes + (int)$total_parciales; ?></div>
        <p class="kpi-foot"><?= (int)$total_pendientes; ?> pendientes y <?= (int)$total_parciales; ?> parciales.</p>
      </div>
    </div>

    <div class="col-12 col-xl-4">
      <div class="soft-card p-3 p-md-4 h-100">
        <div class="mb-3">
          <h2 class="section-title">Filtro visual de trabajo</h2>
          <p class="section-subtitle">Boceto para búsquedas rápidas de cobranzas.</p>
        </div>

        <div class="filter-box">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-bold">Periodo</label>
              <input type="month" class="form-control">
            </div>

            <div class="col-12">
              <label class="form-label fw-bold">Estado</label>
              <select class="form-select">
                <option>Todos</option>
                <option>Pendiente</option>
                <option>Parcial</option>
                <option>Pagada</option>
                <option>Vencida</option>
                <option>Anulada</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label fw-bold">Concepto</label>
              <select class="form-select">
                <option>Todos</option>
                <option>Expensas</option>
                <option>Multa</option>
                <option>Recargo</option>
                <option>Servicio</option>
              </select>
            </div>

            <div class="col-12 d-flex flex-wrap gap-2">
              <button class="btn btn-gescon">Aplicar</button>
              <button class="btn btn-gescon-outline">Limpiar</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-xl-8">
      <div class="soft-card p-3 p-md-4 h-100">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
          <div>
            <h2 class="section-title">Resumen de estados</h2>
            <p class="section-subtitle">Distribución rápida de cobranzas del contexto.</p>
          </div>

          <a href="?view=cobranzas_nueva" class="btn btn-gescon">+ Nueva cobranza</a>
        </div>

        <div class="row g-3">
          <div class="col-12 col-md-4">
            <div class="mini-card">
              <h6 style="font-size:1rem;font-weight:900;color:#1f3f18;margin-bottom:.2rem;">Pendientes</h6>
              <small style="color:#688162;font-weight:700;">Sin pagos registrados</small>
              <p style="margin-top:.55rem;font-size:1.8rem;font-weight:900;color:#9a7700;"><?= (int)$total_pendientes; ?></p>
            </div>
          </div>

          <div class="col-12 col-md-4">
            <div class="mini-card">
              <h6 style="font-size:1rem;font-weight:900;color:#1f3f18;margin-bottom:.2rem;">Parciales</h6>
              <small style="color:#688162;font-weight:700;">Cobro incompleto</small>
              <p style="margin-top:.55rem;font-size:1.8rem;font-weight:900;color:#9a7700;"><?= (int)$total_parciales; ?></p>
            </div>
          </div>

          <div class="col-12 col-md-4">
            <div class="mini-card">
              <h6 style="font-size:1rem;font-weight:900;color:#1f3f18;margin-bottom:.2rem;">Pagadas</h6>
              <small style="color:#688162;font-weight:700;">Canceladas completamente</small>
              <p style="margin-top:.55rem;font-size:1.8rem;font-weight:900;color:#0b8707;"><?= (int)$total_pagadas; ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="soft-card p-3 p-md-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
          <div>
            <h2 class="section-title">Listado de cobranzas</h2>
            <p class="section-subtitle">Podés buscar, exportar a Excel y exportar a PDF.</p>
          </div>
        </div>

        <div class="table-wrap">
          <table id="tablaCobranzas" class="table align-middle w-100">
            <thead>
              <tr>
                <th>ID</th>
                <th>Periodo</th>
                <th>Unidad</th>
                <th>Persona</th>
                <th>Tipo</th>
                <th class="col-concepto">Concepto</th>
                <th class="col-fecha">Emisión</th>
                <th class="col-fecha">Vencimiento</th>
                <th class="col-importe">Importe</th>
                <th class="col-pagado">Pagado</th>
                <th class="col-saldo">Saldo</th>
                <th class="col-estado">Estado</th>
                <th class="col-detalle">Detalle</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($cobranzas as $c): ?>
                <tr>
                  <td><?= htmlspecialchars($c['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?= htmlspecialchars($c['periodo'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?= htmlspecialchars($c['unidad_funcional_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?= htmlspecialchars($c['persona_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?= htmlspecialchars($c['tipo_persona'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?= htmlspecialchars($c['concepto'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?= htmlspecialchars($c['fecha_emision'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?= htmlspecialchars($c['fecha_vencimiento'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td>$ <?= number_format((float)$c['importe'], 2, ',', '.'); ?></td>
                  <td>$ <?= number_format((float)$c['importe_pagado'], 2, ',', '.'); ?></td>
                  <td>$ <?= number_format((float)$c['saldo'], 2, ',', '.'); ?></td>
                  <td>
                    <?php
                      $estado = isset($c['estado']) ? $c['estado'] : '';
                      $claseEstado = 'status-pending';
                      if ($estado === 'pagada') $claseEstado = 'status-ok';
                      if ($estado === 'vencida' || $estado === 'anulada') $claseEstado = 'status-alert';
                    ?>
                    <span class="status-pill <?= $claseEstado; ?>">
                      <?= htmlspecialchars($estado, ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                  </td>
                  <td><?= htmlspecialchars($c['detalle'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <?php if (empty($cobranzas)): ?>
          <div style="background:#fff6d8;border:1px solid #f1df9e;color:#9a7700;border-radius:18px;padding:1rem;font-weight:800;margin-top:1rem;">
            No hay cobranzas cargadas para este contexto.
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
$(document).ready(function () {
  $('#tablaCobranzas').DataTable({
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
    },
    pageLength: 10,
    autoWidth: false,
    scrollX: true,
    responsive: false,
    dom: "<'row mb-3 align-items-center g-2'<'col-12 col-xl-7'B><'col-12 col-xl-5'f>>" +
         "<'row'<'col-12'tr>>" +
         "<'row mt-3 g-2'<'col-12 col-md-5'i><'col-12 col-md-7'p>>",
    columnDefs: [
      { targets: [0], width: "60px" },
      { targets: [1], width: "90px" },
      { targets: [2], width: "90px" },
      { targets: [3], width: "90px" },
      { targets: [4], width: "90px" },
      { targets: [5], width: "130px" },
      { targets: [6,7], width: "110px" },
      { targets: [8,9,10], width: "110px" },
      { targets: [11], width: "110px" },
      { targets: [12], width: "220px" }
    ],
    buttons: [
      {
        extend: 'excelHtml5',
        text: 'Exportar a Excel',
        className: 'btn btn-gescon',
        title: 'Listado_cobranzas'
      },
      {
        extend: 'pdfHtml5',
        text: 'Exportar a PDF',
        className: 'btn btn-gescon-outline',
        title: 'Listado_cobranzas',
        orientation: 'landscape',
        pageSize: 'A4'
      }
    ]
  });
});
</script>