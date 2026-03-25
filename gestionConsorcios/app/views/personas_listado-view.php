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

$id_empresa_admin = isset($_SESSION['contexto_id']) ? (int)$_SESSION['contexto_id'] : 0;

$personas = [];
if ($id_empresa_admin > 0) {
    $con = Database::getCon();
    $sql = "SELECT * FROM personas WHERE id_empresa_administradora = $id_empresa_admin ORDER BY id DESC";
    $query = mysqli_query($con, $sql);

    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $personas[] = $row;
        }
    }
}
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

<style>
.personas-listado-view *{
  box-sizing:border-box;
}

.personas-listado-view{
  width:100%;
  max-width:100%;
  overflow-x:hidden;
}

.personas-listado-view .soft-card,
.personas-listado-view .mini-card{
  background:rgba(255,255,255,.96);
  border:1px solid #d5edd2;
  box-shadow:0 16px 35px rgba(16,168,8,.10);
}

.personas-listado-view .soft-card{
  border-radius:28px;
}

.personas-listado-view .mini-card{
  border-radius:20px;
  padding:1rem;
  height:100%;
}

.personas-listado-view .accent-line{
  width:92px;
  height:6px;
  border-radius:999px;
  background:linear-gradient(90deg,#f4c51c 0%,#16c60c 100%);
  margin-bottom:.9rem;
}

.personas-listado-view .page-title{
  font-size:clamp(1.8rem,3vw,2.6rem);
  font-weight:900;
  color:#0b8707;
  line-height:1.05;
  margin-bottom:.2rem;
}

.personas-listado-view .page-subtitle{
  color:#688162;
  font-size:.98rem;
  font-weight:700;
  line-height:1.5;
  margin:0;
}

.personas-listado-view .section-title{
  font-size:1.12rem;
  color:#0b8707;
  font-weight:900;
  margin-bottom:.15rem;
}

.personas-listado-view .section-subtitle{
  font-size:.87rem;
  color:#688162;
  font-weight:700;
  margin:0;
  line-height:1.5;
}

.personas-listado-view .tag-soft{
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

.personas-listado-view .status-pill{
  display:inline-flex;
  align-items:center;
  padding:.42rem .78rem;
  border-radius:999px;
  font-size:.75rem;
  font-weight:900;
}

.personas-listado-view .status-ok{
  background:#e9ffe8;
  color:#0b8707;
}

.personas-listado-view .status-pending{
  background:#fff6d8;
  color:#9a7700;
}

.personas-listado-view .btn-gescon{
  background:linear-gradient(135deg,#f4c51c 0%,#d6a90c 100%) !important;
  border:none !important;
  color:#433900 !important;
  border-radius:14px !important;
  min-height:42px;
  padding:.6rem 1rem !important;
  font-weight:900 !important;
  box-shadow:0 10px 18px rgba(244,197,28,.20);
}

.personas-listado-view .btn-gescon-outline{
  background:#fff !important;
  color:#0b8707 !important;
  border:2px solid #d5edd2 !important;
  border-radius:14px !important;
  min-height:42px;
  padding:.6rem 1rem !important;
  font-weight:900 !important;
}

.personas-listado-view .table-wrap{
  width:100%;
  max-width:100%;
  overflow-x:auto;
  overflow-y:hidden;
  border-radius:20px;
  -webkit-overflow-scrolling:touch;
}

.personas-listado-view table.dataTable{
  width:100% !important;
  min-width:1200px;
  border-collapse:separate !important;
  border-spacing:0;
  margin:0 !important;
}

.personas-listado-view table.dataTable thead th{
  background:#eaffea !important;
  color:#0b8707 !important;
  font-size:.82rem;
  font-weight:900;
  border-bottom:none !important;
  white-space:nowrap;
}

.personas-listado-view table.dataTable tbody td{
  color:#1f3f18;
  font-size:.91rem;
  font-weight:700;
  vertical-align:middle;
  white-space:nowrap;
}

.personas-listado-view .dataTables_wrapper{
  width:100%;
  overflow:hidden;
}

.personas-listado-view .dt-buttons{
  display:flex;
  flex-wrap:wrap;
  gap:.6rem;
  margin-bottom:1rem;
}

.personas-listado-view .dataTables_wrapper .dataTables_length,
.personas-listado-view .dataTables_wrapper .dataTables_filter{
  margin-bottom:1rem;
}

.personas-listado-view .dataTables_wrapper .dataTables_filter{
  text-align:right;
}

.personas-listado-view .dataTables_wrapper .dataTables_filter label,
.personas-listado-view .dataTables_wrapper .dataTables_length label{
  font-weight:800;
  color:#688162;
}

.personas-listado-view .dataTables_wrapper .dataTables_filter input,
.personas-listado-view .dataTables_wrapper .dataTables_length select{
  border:2px solid #d5edd2 !important;
  border-radius:12px !important;
  min-height:40px;
  padding:.35rem .7rem;
  font-weight:800;
  color:#1f3f18;
  background:#fff;
}

.personas-listado-view .dataTables_wrapper .dataTables_filter input:focus,
.personas-listado-view .dataTables_wrapper .dataTables_length select:focus{
  outline:none;
  border-color:#16c60c !important;
  box-shadow:0 0 0 .25rem rgba(22,198,12,.12) !important;
}

.personas-listado-view .dataTables_info,
.personas-listado-view .dataTables_paginate{
  margin-top:1rem !important;
  font-weight:800;
  color:#688162 !important;
}

.personas-listado-view .pagination .page-link{
  color:#0b8707;
  font-weight:900;
  border-radius:10px !important;
  margin:0 .15rem;
  border:1px solid #d5edd2;
}

.personas-listado-view .pagination .active .page-link{
  background:#16c60c !important;
  border-color:#16c60c !important;
  color:#fff !important;
}

@media (max-width:991.98px){
  .personas-listado-view .dataTables_wrapper .dataTables_filter{
    text-align:left;
  }
}

@media (max-width:575.98px){
  .personas-listado-view .soft-card{
    border-radius:22px;
  }

  .personas-listado-view .mini-card{
    border-radius:18px;
  }

  .personas-listado-view .dt-buttons{
    flex-direction:column;
    align-items:stretch;
  }

  .personas-listado-view .dt-buttons .btn{
    width:100%;
  }
}
</style>

<div class="personas-listado-view">
  <div class="row g-4">

    <div class="col-12">
      <div class="soft-card p-3 p-md-4">
        <div class="row g-4 align-items-center">
          <div class="col-12 col-xxl-8">
            <div class="accent-line"></div>
            <h1 class="page-title">Listado de personas</h1>
            <p class="page-subtitle">Visualizá propietarios, inquilinos y personas con ambos roles cargadas por la empresa logueada.</p>
          </div>

          <div class="col-12 col-xxl-4">
            <div class="mini-card">
              <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                  <div class="section-title">Administrador actual</div>
                  <div class="section-subtitle"><?= htmlspecialchars($nombre_admin, ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <span class="tag-soft">Empresa ID: <?= htmlspecialchars((string)$id_empresa_admin, ENT_QUOTES, 'UTF-8'); ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="soft-card p-3 p-md-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
          <div>
            <h2 class="section-title">Personas cargadas</h2>
            <p class="section-subtitle">Podés buscar, paginar, exportar a Excel y exportar a PDF.</p>
          </div>

          <div>
            <a href="?view=personas_nueva" class="btn btn-gescon">
              + Nueva persona
            </a>
          </div>
        </div>

        <div class="table-wrap">
          <table id="tablaPersonas" class="table align-middle w-100">
            <thead>
              <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>DNI</th>
                <th>CUIT</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Dirección</th>
                <th>Fecha nac.</th>
                <th>Estado</th>
                <th>Observaciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($personas)): ?>
                <?php foreach ($personas as $p): ?>
                  <tr>
                    <td><?= htmlspecialchars(isset($p['id']) ? $p['id'] : '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars(isset($p['tipo_persona']) ? $p['tipo_persona'] : '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars(isset($p['nombre']) ? $p['nombre'] : '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars(isset($p['apellido']) ? $p['apellido'] : '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars(isset($p['dni']) ? $p['dni'] : '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars(isset($p['cuit']) ? $p['cuit'] : '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars(isset($p['telefono']) ? $p['telefono'] : '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars(isset($p['email']) ? $p['email'] : '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars(isset($p['direccion']) ? $p['direccion'] : '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars(isset($p['fecha_nacimiento']) ? $p['fecha_nacimiento'] : '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                      <?php
                        $estado = isset($p['estado']) ? $p['estado'] : '';
                        $claseEstado = ($estado === 'activo') ? 'status-ok' : 'status-pending';
                      ?>
                      <span class="status-pill <?= $claseEstado; ?>">
                        <?= htmlspecialchars($estado, ENT_QUOTES, 'UTF-8'); ?>
                      </span>
                    </td>
                    <td><?= htmlspecialchars(isset($p['observaciones']) ? $p['observaciones'] : '', ENT_QUOTES, 'UTF-8'); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <?php if (empty($personas)): ?>
          <div style="background:#fff6d8;border:1px solid #f1df9e;color:#9a7700;border-radius:18px;padding:1rem;font-weight:800;margin-top:1rem;">
            No hay personas cargadas para la empresa logueada.
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
  $('#tablaPersonas').DataTable({
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
    },
    pageLength: 10,
    autoWidth: false,
    scrollX: true,
    responsive: false,
    dom: "<'row mb-3 align-items-center'<'col-12 col-lg-7'B><'col-12 col-lg-5'f>>" +
         "<'row'<'col-12'tr>>" +
         "<'row mt-3'<'col-12 col-md-5'i><'col-12 col-md-7'p>>",
    buttons: [
      {
        extend: 'excelHtml5',
        text: 'Exportar a Excel',
        className: 'btn btn-gescon',
        title: 'Listado_personas'
      },
      {
        extend: 'pdfHtml5',
        text: 'Exportar a PDF',
        className: 'btn btn-gescon-outline',
        title: 'Listado_personas',
        orientation: 'landscape',
        pageSize: 'A4'
      }
    ]
  });
});
</script>