<?php
$usuario = 'Sol';
if (isset($_SESSION['user_data'])) {
    if (is_object($_SESSION['user_data']) && isset($_SESSION['user_data']->nombre)) {
        $usuario = $_SESSION['user_data']->nombre;
    } elseif (is_array($_SESSION['user_data']) && isset($_SESSION['user_data']['nombre'])) {
        $usuario = $_SESSION['user_data']['nombre'];
    }
}
?>

<style>
.dashboard-boceto{
  width:100%;
}

.dashboard-boceto *{
  box-sizing:border-box;
}

.dashboard-boceto .topbar-card,
.dashboard-boceto .soft-card,
.dashboard-boceto .kpi-card,
.dashboard-boceto .mini-card,
.dashboard-boceto .kanban-col{
  background:rgba(255,255,255,.96);
  border:1px solid #d5edd2;
  box-shadow:0 16px 35px rgba(16,168,8,.10);
}

.dashboard-boceto .topbar-card,
.dashboard-boceto .soft-card{
  border-radius:28px;
}

.dashboard-boceto .topbar-card{
  padding:1rem;
}

@media (min-width:768px){
  .dashboard-boceto .topbar-card{
    padding:1.25rem 1.35rem;
  }
}

.dashboard-boceto .accent-line{
  width:92px;
  height:6px;
  border-radius:999px;
  background:linear-gradient(90deg,#f4c51c 0%,#16c60c 100%);
  margin-bottom:.9rem;
}

.dashboard-boceto .page-title{
  font-size:clamp(1.9rem,3vw,2.8rem);
  font-weight:900;
  color:#0b8707;
  line-height:1.05;
  margin-bottom:.25rem;
}

.dashboard-boceto .page-subtitle{
  color:#688162;
  font-size:.98rem;
  font-weight:700;
  line-height:1.5;
  margin:0;
}

.dashboard-boceto .form-control,
.dashboard-boceto .form-select{
  min-height:50px;
  border-radius:16px;
  border:2px solid #d5edd2;
  font-weight:800;
  color:#1f3f18;
}

.dashboard-boceto .form-control:focus,
.dashboard-boceto .form-select:focus,
.dashboard-boceto .form-check-input:focus{
  border-color:#16c60c;
  box-shadow:0 0 0 .25rem rgba(22,198,12,.12);
}

.dashboard-boceto textarea.form-control{
  min-height:auto;
}

.dashboard-boceto .toolbar-wrap{
  display:flex;
  flex-wrap:wrap;
  gap:.75rem;
}

.dashboard-boceto .toolbar-chip{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:.45rem;
  border:2px solid #d5edd2;
  background:#fff;
  color:#0b8707;
  border-radius:999px;
  padding:.65rem .95rem;
  font-size:.86rem;
  font-weight:900;
  text-decoration:none;
  white-space:nowrap;
}

.dashboard-boceto .toolbar-chip:hover{
  border-color:#16c60c;
  color:#0b8707;
}

.dashboard-boceto .kpi-card{
  border-radius:26px;
  padding:1.15rem;
  height:100%;
  position:relative;
  overflow:hidden;
}

.dashboard-boceto .kpi-card::after{
  content:"";
  position:absolute;
  width:84px;
  height:84px;
  border-radius:50%;
  background:rgba(22,198,12,.05);
  top:-18px;
  right:-18px;
}

.dashboard-boceto .kpi-label{
  font-size:.84rem;
  color:#688162;
  font-weight:800;
  margin-bottom:.45rem;
  position:relative;
  z-index:2;
}

.dashboard-boceto .kpi-value{
  font-size:clamp(1.5rem,2.2vw,2.1rem);
  color:#0b8707;
  font-weight:900;
  line-height:1.08;
  position:relative;
  z-index:2;
}

.dashboard-boceto .kpi-icon{
  width:54px;
  height:54px;
  border-radius:18px;
  background:linear-gradient(135deg,#f4c51c 0%,#ffd84f 100%);
  color:#453c00;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:1.18rem;
  box-shadow:0 10px 20px rgba(244,197,28,.20);
  flex-shrink:0;
  position:relative;
  z-index:2;
}

.dashboard-boceto .kpi-foot{
  margin:.9rem 0 0;
  color:#688162;
  font-size:.86rem;
  font-weight:700;
  line-height:1.55;
  position:relative;
  z-index:2;
}

.dashboard-boceto .highlight{
  color:#d6a90c;
  font-weight:900;
}

.dashboard-boceto .section-title{
  font-size:1.18rem;
  color:#0b8707;
  font-weight:900;
  margin-bottom:.15rem;
}

.dashboard-boceto .section-subtitle{
  font-size:.87rem;
  color:#688162;
  font-weight:700;
  margin:0;
  line-height:1.5;
}

.dashboard-boceto .btn-gescon{
  background:linear-gradient(135deg,#f4c51c 0%,#d6a90c 100%);
  border:none;
  color:#433900;
  border-radius:16px;
  min-height:46px;
  padding:.7rem 1.1rem;
  font-weight:900;
  box-shadow:0 10px 18px rgba(244,197,28,.20);
}

.dashboard-boceto .btn-gescon:hover{
  color:#433900;
  opacity:.96;
}

.dashboard-boceto .btn-gescon-outline{
  background:#fff;
  color:#0b8707;
  border:2px solid #d5edd2;
  border-radius:16px;
  min-height:46px;
  padding:.7rem 1.1rem;
  font-weight:900;
}

.dashboard-boceto .btn-gescon-outline:hover{
  border-color:#16c60c;
  color:#0b8707;
}

.dashboard-boceto .mini-card{
  border-radius:20px;
  padding:1rem;
  height:100%;
}

.dashboard-boceto .mini-card h6{
  color:#1f3f18;
  font-size:.98rem;
  font-weight:900;
  margin-bottom:.12rem;
}

.dashboard-boceto .mini-card small{
  color:#688162;
  font-size:.78rem;
  font-weight:700;
}

.dashboard-boceto .mini-card p{
  color:#688162;
  font-size:.85rem;
  font-weight:700;
  line-height:1.55;
  margin:.6rem 0 0;
}

.dashboard-boceto .tag-soft{
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

.dashboard-boceto .table-responsive{
  border-radius:18px;
  overflow:auto;
}

.dashboard-boceto .table-modern{
  margin:0;
  min-width:920px;
}

.dashboard-boceto .table-modern thead th{
  background:#eaffea;
  color:#0b8707;
  font-size:.82rem;
  font-weight:900;
  border-bottom:none;
  white-space:nowrap;
}

.dashboard-boceto .table-modern td{
  color:#1f3f18;
  font-size:.91rem;
  font-weight:700;
  vertical-align:middle;
  white-space:nowrap;
}

.dashboard-boceto .status-pill{
  display:inline-flex;
  align-items:center;
  padding:.42rem .78rem;
  border-radius:999px;
  font-size:.75rem;
  font-weight:900;
}

.dashboard-boceto .status-ok{
  background:#e9ffe8;
  color:#0b8707;
}

.dashboard-boceto .status-pending{
  background:#fff6d8;
  color:#9a7700;
}

.dashboard-boceto .status-alert{
  background:#ffe8e8;
  color:#b42318;
}

.dashboard-boceto .timeline-item{
  position:relative;
  padding-left:1.4rem;
}

.dashboard-boceto .timeline-item::before{
  content:"";
  position:absolute;
  left:0;
  top:.35rem;
  width:10px;
  height:10px;
  border-radius:50%;
  background:#f4c51c;
  box-shadow:0 0 0 4px rgba(244,197,28,.18);
}

.dashboard-boceto .kanban-col{
  border-radius:22px;
  padding:1rem;
  height:100%;
  box-shadow:0 10px 24px rgba(16,168,8,.05);
}

.dashboard-boceto .kanban-title{
  font-size:.95rem;
  font-weight:900;
  color:#0b8707;
  margin-bottom:.8rem;
}

.dashboard-boceto .kanban-card{
  background:#f6fff5;
  border:1px solid #d5edd2;
  border-radius:16px;
  padding:.85rem;
  margin-bottom:.75rem;
}

.dashboard-boceto .nav-pills .nav-link{
  border-radius:999px;
  font-weight:900;
  color:#0b8707;
  border:2px solid transparent;
}

.dashboard-boceto .nav-pills .nav-link.active{
  background:#eaffea;
  color:#0b8707;
  border-color:#d5edd2;
}

.dashboard-boceto .modal-content{
  border:none;
  border-radius:24px;
  overflow:hidden;
}

.dashboard-boceto .modal-header{
  background:#eaffea;
  border-bottom:1px solid #d5edd2;
}

@media (max-width:575.98px){
  .dashboard-boceto .topbar-card,
  .dashboard-boceto .soft-card{
    border-radius:22px;
  }

  .dashboard-boceto .kpi-card,
  .dashboard-boceto .mini-card,
  .dashboard-boceto .kanban-col{
    border-radius:18px;
  }

  .dashboard-boceto .toolbar-chip{
    width:100%;
  }

  .dashboard-boceto .page-title{
    font-size:2rem;
  }
}
</style>

<div class="dashboard-boceto">

  <div class="topbar-card mb-4">
    <div class="row g-3 align-items-center">
      <div class="col-12 col-xl-6">
        <div class="accent-line"></div>
        <h1 class="page-title">Dashboard de boceto</h1>
        <p class="page-subtitle">Espacio de pruebas para diseñar vistas, formularios, tablas, módulos y flujos de GesCon.</p>
      </div>

      <div class="col-12 col-xl-6">
        <div class="row g-3 align-items-center">
          <div class="col-12">
            <input type="text" class="form-control" placeholder="Buscar componente, módulo o pantalla...">
          </div>
          <div class="col-12">
            <div class="toolbar-wrap">
              <a href="#" class="toolbar-chip"><i class="bi bi-file-earmark-plus"></i> Nueva vista</a>
              <a href="#" class="toolbar-chip"><i class="bi bi-stars"></i> Ideas</a>
              <a href="?action=login&logout=1" class="toolbar-chip"><i class="bi bi-box-arrow-right"></i> Salir</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 g-xl-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
      <div class="kpi-card">
        <div class="d-flex justify-content-between align-items-start gap-3">
          <div>
            <div class="kpi-label">Módulos bocetados</div>
            <div class="kpi-value">18</div>
          </div>
          <div class="kpi-icon"><i class="bi bi-grid-1x2-fill"></i></div>
        </div>
        <p class="kpi-foot">Resumen, cobranzas, banco, expensas, usuarios, reportes y más.</p>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
      <div class="kpi-card">
        <div class="d-flex justify-content-between align-items-start gap-3">
          <div>
            <div class="kpi-label">Formularios activos</div>
            <div class="kpi-value">9</div>
          </div>
          <div class="kpi-icon"><i class="bi bi-ui-checks-grid"></i></div>
        </div>
        <p class="kpi-foot">Carga de propietarios, consorcios, pagos, tickets y contratos.</p>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
      <div class="kpi-card">
        <div class="d-flex justify-content-between align-items-start gap-3">
          <div>
            <div class="kpi-label">Tablas configuradas</div>
            <div class="kpi-value">12</div>
          </div>
          <div class="kpi-icon"><i class="bi bi-table"></i></div>
        </div>
        <p class="kpi-foot">Listados listos para adaptar a datos reales del sistema.</p>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
      <div class="kpi-card">
        <div class="d-flex justify-content-between align-items-start gap-3">
          <div>
            <div class="kpi-label">Estado del boceto</div>
            <div class="kpi-value">76%</div>
          </div>
          <div class="kpi-icon"><i class="bi bi-bar-chart-line-fill"></i></div>
        </div>
        <p class="kpi-foot"><span class="highlight">+8%</span> de avance esta semana.</p>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-12 col-xxl-8">
      <div class="soft-card p-3 p-md-4 h-100">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
          <div>
            <h2 class="section-title">Barra de herramientas de boceto</h2>
            <p class="section-subtitle">Inputs y filtros para simular búsquedas, estados y escenarios de uso.</p>
          </div>
          <button class="btn btn-gescon" data-bs-toggle="modal" data-bs-target="#modalNuevoModulo">Crear módulo</button>
        </div>

        <div class="row g-3">
          <div class="col-12 col-md-6 col-lg-4">
            <label class="form-label fw-bold">Módulo</label>
            <select class="form-select">
              <option>Dashboard general</option>
              <option>Cobranzas</option>
              <option>Banco / API</option>
              <option>Expensas</option>
              <option>Incidencias</option>
            </select>
          </div>

          <div class="col-12 col-md-6 col-lg-4">
            <label class="form-label fw-bold">Estado</label>
            <select class="form-select">
              <option>Boceto</option>
              <option>En revisión</option>
              <option>Listo para maquetar</option>
              <option>Listo para backend</option>
            </select>
          </div>

          <div class="col-12 col-md-6 col-lg-4">
            <label class="form-label fw-bold">Prioridad</label>
            <select class="form-select">
              <option>Alta</option>
              <option>Media</option>
              <option>Baja</option>
            </select>
          </div>

          <div class="col-12 col-md-8">
            <label class="form-label fw-bold">Nombre de vista</label>
            <input type="text" class="form-control" placeholder="Ej: resumen_expensas_mensual">
          </div>

          <div class="col-12 col-md-4">
            <label class="form-label fw-bold">Fecha objetivo</label>
            <input type="date" class="form-control">
          </div>

          <div class="col-12">
            <label class="form-label fw-bold">Descripción funcional</label>
            <textarea class="form-control" rows="4" placeholder="Describí qué debería mostrar la vista, qué acciones tiene y qué datos consume..."></textarea>
          </div>

          <div class="col-12 col-lg-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="chk1" checked>
              <label class="form-check-label fw-bold" for="chk1">Incluye tabla</label>
            </div>
          </div>

          <div class="col-12 col-lg-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="chk2" checked>
              <label class="form-check-label fw-bold" for="chk2">Incluye formulario</label>
            </div>
          </div>

          <div class="col-12 col-lg-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="chk3">
              <label class="form-check-label fw-bold" for="chk3">Incluye integración bancaria</label>
            </div>
          </div>

          <div class="col-12 d-flex flex-wrap gap-2 pt-2">
            <button class="btn btn-gescon">Guardar boceto</button>
            <button class="btn btn-gescon-outline">Duplicar</button>
            <button class="btn btn-gescon-outline">Limpiar</button>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-xxl-4">
      <div class="row g-4">
        <div class="col-12">
          <div class="soft-card p-3 p-md-4">
            <div class="mb-3">
              <h2 class="section-title">Acciones rápidas</h2>
              <p class="section-subtitle">Atajos para prototipar pantallas del sistema.</p>
            </div>

            <div class="d-grid gap-3">
              <div class="mini-card">
                <h6>Alta de propietario</h6>
                <small>Formulario rápido</small>
                <p>Nombre, DNI, email, teléfono, unidad y observaciones.</p>
              </div>

              <div class="mini-card">
                <h6>Conciliación bancaria</h6>
                <small>Vista operacional</small>
                <p>Tabla de movimientos, matching y pagos no identificados.</p>
              </div>

              <div class="mini-card">
                <h6>Resumen de expensas</h6>
                <small>Vista pública interna</small>
                <p>Totales, vencimientos, importes por unidad y estado de pago.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="soft-card p-3 p-md-4">
            <div class="mb-3">
              <h2 class="section-title">Checklist</h2>
              <p class="section-subtitle">Control rápido del boceto actual.</p>
            </div>

            <div class="d-grid gap-2">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" checked id="t1">
                <label class="form-check-label fw-bold" for="t1">Responsive móvil</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" checked id="t2">
                <label class="form-check-label fw-bold" for="t2">Responsive tablet</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" checked id="t3">
                <label class="form-check-label fw-bold" for="t3">Responsive desktop</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="t4">
                <label class="form-check-label fw-bold" for="t4">Conectado a backend</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="t5">
                <label class="form-check-label fw-bold" for="t5">Conectado a banco/API</label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="soft-card p-3 p-md-4 mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
      <div>
        <h2 class="section-title">Tabla de módulos y vistas</h2>
        <p class="section-subtitle">Tabla grande para probar listados, filtros, estados y acciones.</p>
      </div>

      <ul class="nav nav-pills gap-2">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-listado" type="button">Listado</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-kanban" type="button">Kanban</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-timeline" type="button">Timeline</button></li>
      </ul>
    </div>

    <div class="tab-content">
      <div class="tab-pane fade show active" id="tab-listado">
        <div class="table-responsive">
          <table class="table table-modern align-middle">
            <thead>
              <tr>
                <th>Vista</th>
                <th>Módulo</th>
                <th>Responsable</th>
                <th>Prioridad</th>
                <th>Estado</th>
                <th>Última edición</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>dashboard_general</td>
                <td>Inicio</td>
                <td><?= htmlspecialchars($usuario); ?></td>
                <td>Alta</td>
                <td><span class="status-pill status-ok">Activo</span></td>
                <td>13/03/2026</td>
                <td><button class="btn btn-sm btn-gescon-outline">Editar</button></td>
              </tr>
              <tr>
                <td>expensas_resumen</td>
                <td>Expensas</td>
                <td>Diseño UI</td>
                <td>Alta</td>
                <td><span class="status-pill status-pending">Boceto</span></td>
                <td>12/03/2026</td>
                <td><button class="btn btn-sm btn-gescon-outline">Editar</button></td>
              </tr>
              <tr>
                <td>banco_conciliacion</td>
                <td>Banco / API</td>
                <td>Backend</td>
                <td>Alta</td>
                <td><span class="status-pill status-alert">Pendiente</span></td>
                <td>11/03/2026</td>
                <td><button class="btn btn-sm btn-gescon-outline">Ver</button></td>
              </tr>
              <tr>
                <td>propietarios_abm</td>
                <td>Personas</td>
                <td>Operaciones</td>
                <td>Media</td>
                <td><span class="status-pill status-ok">Listo</span></td>
                <td>10/03/2026</td>
                <td><button class="btn btn-sm btn-gescon-outline">Preview</button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="tab-pane fade" id="tab-kanban">
        <div class="row g-3">
          <div class="col-12 col-md-6 col-xl-3">
            <div class="kanban-col">
              <div class="kanban-title">Ideas</div>
              <div class="kanban-card"><strong>Portal del propietario</strong><br><small>Resumen, pagos y documentación</small></div>
              <div class="kanban-card"><strong>Alertas push</strong><br><small>Vencimientos y pagos</small></div>
            </div>
          </div>

          <div class="col-12 col-md-6 col-xl-3">
            <div class="kanban-col">
              <div class="kanban-title">Boceto</div>
              <div class="kanban-card"><strong>Cobranzas y deuda</strong><br><small>Tabla + filtros + alertas</small></div>
              <div class="kanban-card"><strong>Banco / API</strong><br><small>Movimiento + matching</small></div>
            </div>
          </div>

          <div class="col-12 col-md-6 col-xl-3">
            <div class="kanban-col">
              <div class="kanban-title">En revisión</div>
              <div class="kanban-card"><strong>Dashboard general</strong><br><small>KPI + actividad + vencimientos</small></div>
            </div>
          </div>

          <div class="col-12 col-md-6 col-xl-3">
            <div class="kanban-col">
              <div class="kanban-title">Listo</div>
              <div class="kanban-card"><strong>Login</strong><br><small>Sin layout + validación</small></div>
              <div class="kanban-card"><strong>Resumen expensas</strong><br><small>Diseño validado</small></div>
            </div>
          </div>
        </div>
      </div>

      <div class="tab-pane fade" id="tab-timeline">
        <div class="row g-3">
          <div class="col-12 col-lg-6">
            <div class="mini-card">
              <div class="timeline-item mb-3">
                <h6 class="mb-1">13/03/2026 · Dashboard de boceto</h6>
                <small class="text-muted fw-bold">Se consolidó un tablero base para diseñar futuras vistas.</small>
              </div>
              <div class="timeline-item mb-3">
                <h6 class="mb-1">12/03/2026 · Vistas de expensas</h6>
                <small class="text-muted fw-bold">Resumen, gastos y cobranzas adaptados a estética GesCon.</small>
              </div>
              <div class="timeline-item">
                <h6 class="mb-1">11/03/2026 · Login</h6>
                <small class="text-muted fw-bold">Se definió un acceso sin layout y con control de sesión.</small>
              </div>
            </div>
          </div>

          <div class="col-12 col-lg-6">
            <div class="mini-card">
              <h6 class="mb-2">Notas de sprint</h6>
              <p class="mb-2">Priorizar vistas que den valor comercial al sistema: dashboard, cobranzas, banco y expensas.</p>
              <p class="mb-0">Siguiente objetivo: conectar tablas a datos reales y preparar un módulo ABM reutilizable.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<div class="modal fade" id="modalNuevoModulo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" style="color:#0b8707;">Nuevo módulo de boceto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div class="row g-3">
          <div class="col-12 col-md-6">
            <label class="form-label fw-bold">Nombre</label>
            <input type="text" class="form-control" placeholder="Ej: modulo_banco_api">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label fw-bold">Categoría</label>
            <select class="form-select">
              <option>Operativo</option>
              <option>Administrativo</option>
              <option>Financiero</option>
              <option>Usuarios</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label fw-bold">Objetivo</label>
            <textarea class="form-control" rows="4" placeholder="Qué problema resuelve este módulo y qué datos mostraría..."></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 px-4 pb-4">
        <button type="button" class="btn btn-gescon-outline" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-gescon">Crear módulo</button>
      </div>
    </div>
  </div>
</div>