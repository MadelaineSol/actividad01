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

$contexto_id = isset($_SESSION['contexto_id']) ? $_SESSION['contexto_id'] : '';
$periodo_actual = date('Y-m');
?>

<style>
.gastos-view *{box-sizing:border-box;}

.gastos-view .soft-card,
.gastos-view .mini-card{
  background:rgba(255,255,255,.96);
  border:1px solid #d5edd2;
  box-shadow:0 16px 35px rgba(16,168,8,.10);
}

.gastos-view .soft-card{border-radius:28px;}

.gastos-view .mini-card{
  border-radius:20px;
  padding:1rem;
  height:100%;
}

.gastos-view .accent-line{
  width:92px;height:6px;border-radius:999px;
  background:linear-gradient(90deg,#f4c51c 0%,#16c60c 100%);
  margin-bottom:.9rem;
}

.gastos-view .page-title{
  font-size:clamp(1.8rem,3vw,2.6rem);
  font-weight:900;color:#0b8707;
  line-height:1.05;margin-bottom:.2rem;
}

.gastos-view .page-subtitle{
  color:#688162;font-size:.98rem;
  font-weight:700;line-height:1.5;margin:0;
}

.gastos-view .section-title{
  font-size:1.12rem;color:#0b8707;
  font-weight:900;margin-bottom:.15rem;
}

.gastos-view .section-subtitle{
  font-size:.87rem;color:#688162;
  font-weight:700;margin:0;line-height:1.5;
}

.gastos-view .form-label{
  font-size:.9rem;color:#1f3f18;
  font-weight:900;margin-bottom:.45rem;
}

.gastos-view .form-control,
.gastos-view .form-select{
  min-height:52px;border-radius:16px;
  border:2px solid #d5edd2;
  font-weight:800;color:#1f3f18;width:100%;
}

.gastos-view .form-control:focus,
.gastos-view .form-select:focus{
  border-color:#16c60c;
  box-shadow:0 0 0 .25rem rgba(22,198,12,.12);
}

.gastos-view textarea.form-control{min-height:auto;}

.gastos-view .btn-gescon{
  background:linear-gradient(135deg,#f4c51c 0%,#d6a90c 100%);
  border:none;color:#433900;border-radius:16px;
  min-height:48px;padding:.75rem 1.15rem;
  font-weight:900;box-shadow:0 10px 18px rgba(244,197,28,.20);
}

.gastos-view .btn-gescon:hover{color:#433900;opacity:.96;}

.gastos-view .btn-gescon-outline{
  background:#fff;color:#0b8707;
  border:2px solid #d5edd2;border-radius:16px;
  min-height:48px;padding:.75rem 1.15rem;font-weight:900;
}

.gastos-view .btn-gescon-outline:hover{
  border-color:#16c60c;color:#0b8707;
}

.gastos-view .btn-gescon-sm{
  background:linear-gradient(135deg,#f4c51c 0%,#d6a90c 100%);
  border:none;color:#433900;border-radius:12px;
  padding:.4rem .75rem;font-weight:900;font-size:.82rem;
  box-shadow:0 6px 12px rgba(244,197,28,.15);
}

.gastos-view .btn-danger-sm{
  background:#fff;color:#b42318;
  border:2px solid #f3c2c2;border-radius:12px;
  padding:.4rem .75rem;font-weight:900;font-size:.82rem;
}

.gastos-view .btn-danger-sm:hover{
  background:#ffe8e8;color:#b42318;
}

.gastos-view .tag-soft{
  display:inline-flex;align-items:center;
  padding:.42rem .78rem;border-radius:999px;
  background:#eaffea;color:#0b8707;
  font-size:.74rem;font-weight:900;white-space:nowrap;
}

.gastos-view .kpi-card{
  background:#fff;border:1px solid #d5edd2;
  border-radius:26px;box-shadow:0 16px 35px rgba(16,168,8,.10);
  padding:1.15rem;height:100%;
  position:relative;overflow:hidden;
}

.gastos-view .kpi-card::after{
  content:"";position:absolute;
  width:84px;height:84px;border-radius:50%;
  background:rgba(22,198,12,.05);
  top:-18px;right:-18px;
}

.gastos-view .kpi-label{
  font-size:.84rem;color:#688162;
  font-weight:800;margin-bottom:.45rem;
  position:relative;z-index:2;
}

.gastos-view .kpi-value{
  font-size:clamp(1.5rem,2.2vw,2.1rem);
  color:#0b8707;font-weight:900;
  line-height:1.08;position:relative;z-index:2;
  white-space:nowrap;
}

.gastos-view .kpi-icon{
  width:54px;height:54px;border-radius:18px;
  background:linear-gradient(135deg,#f4c51c 0%,#ffd84f 100%);
  color:#453c00;display:flex;align-items:center;
  justify-content:center;font-size:1.18rem;
  box-shadow:0 10px 20px rgba(244,197,28,.20);
  flex-shrink:0;position:relative;z-index:2;
}

.gastos-view .kpi-foot{
  margin:.9rem 0 0;color:#688162;
  font-size:.86rem;font-weight:700;
  line-height:1.55;position:relative;z-index:2;
}

.gastos-view .table-responsive{border-radius:18px;overflow:auto;}

.gastos-view .table-modern{margin:0;min-width:800px;width:100%;}

.gastos-view .table-modern thead th{
  background:#eaffea;color:#0b8707;
  font-size:.82rem;font-weight:900;
  border-bottom:none;white-space:nowrap;
  padding:.7rem 1rem;
}

.gastos-view .table-modern td{
  color:#1f3f18;font-size:.91rem;font-weight:700;
  vertical-align:middle;white-space:nowrap;
  background:#fff;padding:.65rem 1rem;
}

.gastos-view .table-modern tbody tr:hover td{background:#f6fff5;}

.gastos-view .status-pill{
  display:inline-flex;align-items:center;
  padding:.38rem .72rem;border-radius:999px;
  font-size:.75rem;font-weight:900;
}

.gastos-view .status-ok{background:#e9ffe8;color:#0b8707;}
.gastos-view .status-pending{background:#fff6d8;color:#9a7700;}
.gastos-view .status-alert{background:#ffe5e5;color:#b42318;}

.gastos-view .rubro-bar{
  height:8px;border-radius:99px;
  background:#eaffea;overflow:hidden;
}

.gastos-view .rubro-bar-fill{
  height:100%;border-radius:99px;
  background:linear-gradient(90deg,#16c60c,#0b8707);
}

.gastos-view .alert-box{
  display:none;margin-bottom:1rem;
  border-radius:16px;padding:.9rem 1rem;font-weight:800;
}

.gastos-view .empty-state{
  text-align:center;padding:2.5rem 1rem;
  color:#688162;font-weight:700;
}

.gastos-view .empty-state i{
  font-size:2.5rem;color:#d5edd2;margin-bottom:.6rem;
}

@media(max-width:575.98px){
  .gastos-view .soft-card{border-radius:22px;}
  .gastos-view .mini-card{border-radius:18px;}
  .gastos-view .kpi-card{border-radius:18px;}
}
</style>

<div class="gastos-view">

  <!-- === TOPBAR === -->
  <div class="soft-card p-3 p-md-4 mb-4">
    <div class="row g-3 align-items-center">
      <div class="col-12 col-lg-7">
        <div class="accent-line"></div>
        <h1 class="page-title">Carga de gastos</h1>
        <p class="page-subtitle">Registrá los gastos del período vigente: servicios, mantenimiento, honorarios, seguros y más.</p>
      </div>
      <div class="col-12 col-lg-5">
        <div class="row g-2 align-items-end">
          <div class="col">
            <label class="form-label">Período activo</label>
            <input type="month" id="filtroPeriodo" class="form-control" value="<?= $periodo_actual ?>">
          </div>
          <div class="col-auto">
            <button type="button" class="btn btn-gescon" onclick="cargarGastos()">
              <i class="bi bi-arrow-repeat"></i> Actualizar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- === KPIs (dinámicos) === -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
      <div class="kpi-card">
        <div class="d-flex justify-content-between align-items-start gap-2">
          <div style="min-width:0;">
            <div class="kpi-label">Total período</div>
            <div class="kpi-value" id="kpiTotal">$ 0</div>
          </div>
          <div class="kpi-icon"><i class="bi bi-cash-stack"></i></div>
        </div>
        <p class="kpi-foot">Suma de gastos activos (no anulados).</p>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="kpi-card">
        <div class="d-flex justify-content-between align-items-start gap-2">
          <div style="min-width:0;">
            <div class="kpi-label">Gastos cargados</div>
            <div class="kpi-value" id="kpiCantidad">0</div>
          </div>
          <div class="kpi-icon"><i class="bi bi-receipt-cutoff"></i></div>
        </div>
        <p class="kpi-foot">Cantidad de registros en este período.</p>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="kpi-card">
        <div class="d-flex justify-content-between align-items-start gap-2">
          <div style="min-width:0;">
            <div class="kpi-label">Rubros distintos</div>
            <div class="kpi-value" id="kpiRubros">0</div>
          </div>
          <div class="kpi-icon"><i class="bi bi-tags-fill"></i></div>
        </div>
        <p class="kpi-foot">Categorías de gasto utilizadas.</p>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="kpi-card">
        <div class="d-flex justify-content-between align-items-start gap-2">
          <div style="min-width:0;">
            <div class="kpi-label">Período</div>
            <div class="kpi-value" id="kpiPeriodo"><?= date('m/Y') ?></div>
          </div>
          <div class="kpi-icon"><i class="bi bi-calendar3"></i></div>
        </div>
        <p class="kpi-foot">Mes de liquidación activo.</p>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">

    <!-- === FORMULARIO DE CARGA === -->
    <div class="col-12 col-xl-8">
      <div class="soft-card p-3 p-md-4 h-100">
        <div class="mb-3">
          <h2 class="section-title">Nuevo gasto</h2>
          <p class="section-subtitle">Completá los datos para registrar un gasto en el período seleccionado.</p>
        </div>

        <div id="respuestaAjaxGasto" class="alert-box"></div>

        <form id="formNuevoGasto">
          <input type="hidden" name="id_empresa_administradora" value="<?= htmlspecialchars($contexto_id, ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="periodo" id="inputPeriodo" value="<?= $periodo_actual ?>">

          <div class="row g-3">

            <div class="col-12 col-md-6">
              <label class="form-label">Rubro *</label>
              <select name="rubro" class="form-select" required>
                <option value="">Seleccionar...</option>
                <option value="Mantenimiento">Mantenimiento</option>
                <option value="Limpieza">Limpieza</option>
                <option value="Seguridad">Seguridad</option>
                <option value="Servicios públicos">Servicios públicos (luz, agua, gas)</option>
                <option value="Honorarios administración">Honorarios administración</option>
                <option value="Seguros">Seguros</option>
                <option value="Reparaciones">Reparaciones</option>
                <option value="Jardinería">Jardinería</option>
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
              <label class="form-label">Descripción *</label>
              <input type="text" name="descripcion" class="form-control" placeholder="Ej: Factura de luz mes de marzo" required>
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">Monto ($) *</label>
              <input type="text" name="monto" class="form-control" placeholder="0.00" required inputmode="decimal">
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">Fecha del gasto</label>
              <input type="date" name="fecha_gasto" class="form-control" value="<?= date('Y-m-d') ?>">
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

            <div class="col-12 d-flex flex-wrap gap-2 pt-2">
              <button type="submit" class="btn btn-gescon">
                <i class="bi bi-plus-circle"></i> Registrar gasto
              </button>
              <button type="reset" class="btn btn-gescon-outline">Limpiar</button>
            </div>

          </div>
        </form>
      </div>
    </div>

    <!-- === PANEL LATERAL: RESUMEN POR RUBRO === -->
    <div class="col-12 col-xl-4">
      <div class="row g-4">

        <div class="col-12">
          <div class="soft-card p-3 p-md-4">
            <div class="mb-3">
              <h2 class="section-title">Resumen por rubro</h2>
              <p class="section-subtitle">Distribución de gastos del período activo.</p>
            </div>
            <div id="contenedorRubros" class="d-grid gap-3">
              <div class="empty-state">
                <i class="bi bi-pie-chart d-block"></i>
                Cargá gastos para ver el resumen.
              </div>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="soft-card p-3 p-md-4">
            <div class="mb-3">
              <h2 class="section-title">Rubros habituales</h2>
              <p class="section-subtitle">Categorías más usadas en consorcios.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
              <span class="tag-soft">Mantenimiento</span>
              <span class="tag-soft">Limpieza</span>
              <span class="tag-soft">Seguridad</span>
              <span class="tag-soft">Servicios</span>
              <span class="tag-soft">Honorarios</span>
              <span class="tag-soft">Seguros</span>
              <span class="tag-soft">Reparaciones</span>
              <span class="tag-soft">Sueldos</span>
              <span class="tag-soft">Varios</span>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>

  <?php

require_once 'app/model/CobranzasData.php';

$CobranzasData = new CobranzasData;
$cobranzas = $CobranzasData->get_cobranza_by_id_empresa($_SESSION['contexto_id']);


?>

  <!-- === TABLA DE GASTOS CARGADOS === -->
  <div class="soft-card p-3 p-md-4 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
      <div>
        <h2 class="section-title">Deudas del período (Propietarios)</h2>
        <p class="section-subtitle">Listado completo con detalle, rubro, monto y estado.</p>
      </div>
      <div>
        <input type="text" id="buscarGasto" class="form-control" placeholder="Buscar gasto..." style="min-height:44px;min-width:220px;">
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-modern align-middle" id="tablaGastos">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Unidad funcional</th>
            <th>Persona</th>
            <th>Período</th>
            <th>Fecha de emisión</th>
            <th>Concepto</th>
            <th>Detalle</th>
            <th>Importe</th>
             <th>Estado</th>
              <th>Observaciones</th>
          </tr>
        </thead>
        <tbody id="tbodyGastos">

            <?php foreach ($cobranzas as $uf): ?>

          <tr>
             <td><?= htmlspecialchars(isset($uf['created_at']) ? $uf['created_at'] : '', ENT_QUOTES, 'UTF-8'); ?>   </td>


             <?php
require_once 'app/model/UnidadesFuncionalesData.php';

$UnidadesFuncionalesData = new UnidadesFuncionalesData;
$unidades = $UnidadesFuncionalesData->get_unidad_by_id($uf['id']);



?>
              <td><?= htmlspecialchars(isset($unidades['nombre']) ? $unidades['nombre'] : '', ENT_QUOTES, 'UTF-8'); ?>   </td>


               <?php
require_once 'app/model/PersonasData.php';

$PersonasData = new PersonasData;
$personas = $PersonasData->get_persona_by_id($uf['id']);



?>
               <td><?= htmlspecialchars(isset($personas['nombre']) ? $personas['nombre'] : '', ENT_QUOTES, 'UTF-8'); ?>     <?= htmlspecialchars(isset($personas['apellido']) ? $personas['apellido'] : '', ENT_QUOTES, 'UTF-8'); ?>   </td>
                <td><?= htmlspecialchars(isset($uf['periodo']) ? $uf['periodo'] : '', ENT_QUOTES, 'UTF-8'); ?>   </td>
                 <td><?= htmlspecialchars(isset($uf['fecha_emision']) ? $uf['fecha_emision'] : '', ENT_QUOTES, 'UTF-8'); ?>   </td>
                 <td><?= htmlspecialchars(isset($uf['concepto']) ? $uf['concepto'] : '', ENT_QUOTES, 'UTF-8'); ?>   </td>
                 <td><?= htmlspecialchars(isset($uf['detalle']) ? $uf['detalle'] : '', ENT_QUOTES, 'UTF-8'); ?>   </td>
                 <td><?= htmlspecialchars(isset($uf['importe']) ? $uf['importe'] : '', ENT_QUOTES, 'UTF-8'); ?>   </td>
                 <td><?= htmlspecialchars(isset($uf['estado']) ? $uf['estado'] : '', ENT_QUOTES, 'UTF-8'); ?>   </td>
                 <td><?= htmlspecialchars(isset($uf['observaciones']) ? $uf['observaciones'] : '', ENT_QUOTES, 'UTF-8'); ?>   </td>
          </tr>


             <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <br><br><br><br>

 <?php
require_once 'app/model/GastosData.php';

$GastosData = new GastosData;
$gastos = $GastosData->get_persona_by_id($_SESSION['contexto_id']);



?>

  <div class="soft-card p-3 p-md-4 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
      <div>
        <h2 class="section-title">Deudas del período (Administracion)</h2>
        <p class="section-subtitle">Listado completo con detalle, rubro, monto y estado.</p>
      </div>
      <div>
        <input type="text" id="buscarGasto" class="form-control" placeholder="Buscar gasto..." style="min-height:44px;min-width:220px;">
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-modern align-middle" id="tablaGastos">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Proveedor</th>
            <th>Período</th>
            <th>Categoria</th>
            <th>Conceptp</th>
            <th>Monto</th>
       
             <th>Estado</th>
     
          </tr>
        </thead>
        <tbody id="tbodyGastos">

            <?php foreach ($gastos as $ga): ?>

          <tr>
             <td><?= htmlspecialchars(isset($ga['created_at']) ? $ga['created_at'] : '', ENT_QUOTES, 'UTF-8'); ?>   </td>


             <?php
require_once 'app/model/ProveedorData.php';

$ProveedorData = new ProveedorData;
$proveedor = $ProveedorData->get_by_id($ga['id']);



?>
              <td><?= htmlspecialchars(isset($proveedor['razon_social']) ? $proveedor['razon_social'] : '', ENT_QUOTES, 'UTF-8'); ?>   </td>


               
               <td><?= htmlspecialchars(isset($ga['periodo']) ? $ga['periodo'] : '', ENT_QUOTES, 'UTF-8'); ?>      </td>
                <td><?= htmlspecialchars(isset($ga['categoria']) ? $ga['categoria'] : '', ENT_QUOTES, 'UTF-8'); ?>   </td>
                 <td><?= htmlspecialchars(isset($ga['concepto']) ? $ga['concepto'] : '', ENT_QUOTES, 'UTF-8'); ?>   </td>
                 <td><?= htmlspecialchars(isset($ga['monto']) ? $ga['monto'] : '', ENT_QUOTES, 'UTF-8'); ?>   </td>
                 <td><?= htmlspecialchars(isset($ga['estado']) ? $ga['estado'] : '', ENT_QUOTES, 'UTF-8'); ?>   </td>
                 
          </tr>


             <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>




</div><!-- /gastos-view -->


<script>
document.addEventListener('DOMContentLoaded', function () {

  const BASE = '<?= URL ?>';
  const contextoId = '<?= htmlspecialchars($contexto_id, ENT_QUOTES, "UTF-8") ?>';

  const filtroPeriodo  = document.getElementById('filtroPeriodo');
  const inputPeriodo   = document.getElementById('inputPeriodo');
  const form           = document.getElementById('formNuevoGasto');
  const respuesta      = document.getElementById('respuestaAjaxGasto');
  const tbody          = document.getElementById('tbodyGastos');
  const buscar         = document.getElementById('buscarGasto');

  // --- Sincronizar período del filtro con el hidden del form ---
  filtroPeriodo.addEventListener('change', function () {
    inputPeriodo.value = this.value;
    // Actualizar KPI período visual
    if (this.value) {
      var parts = this.value.split('-');
      document.getElementById('kpiPeriodo').textContent = parts[1] + '/' + parts[0];
    }
  });

  // --- Cargar gastos al inicio ---
  cargarGastos();

  // --- Enviar formulario via AJAX ---
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    if (form.dataset.enviando === '1') return;
    form.dataset.enviando = '1';

    var formData = new FormData(form);

    fetch(BASE + '?action=guardar_gasto', {
      method: 'POST',
      body: formData
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      mostrarRespuesta(data);
      if (data.status === 'ok') {
        form.reset();
        // Restaurar valores que el reset limpia
        inputPeriodo.value = filtroPeriodo.value;
        form.querySelector('[name="fecha_gasto"]').value = new Date().toISOString().slice(0, 10);
        cargarGastos();
      }
      form.dataset.enviando = '0';
    })
    .catch(function () {
      mostrarRespuesta({ status: 'error', message: 'Error de conexión.' });
      form.dataset.enviando = '0';
    });
  });

  // --- Buscar en tabla ---
  buscar.addEventListener('input', function () {
    var q = this.value.toLowerCase().trim();
    var rows = tbody.querySelectorAll('tr[data-id]');
    rows.forEach(function (row) {
      row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
  });

  // --- Función: Cargar gastos ---
  window.cargarGastos = function () {
    var fd = new FormData();
    fd.append('id_empresa_administradora', contextoId);
    fd.append('periodo', filtroPeriodo.value);

    fetch(BASE + '?action=listar_gastos', {
      method: 'POST',
      body: fd
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (data.status !== 'ok') return;
      renderTabla(data.gastos);
      renderKPIs(data);
      renderRubros(data.rubros, data.total);
    })
    .catch(function () {
      tbody.innerHTML = '<tr><td colspan="8"><div class="empty-state"><i class="bi bi-wifi-off d-block"></i>Error de conexión.</div></td></tr>';
    });
  };

  // --- Función: Renderizar tabla ---
  function renderTabla(gastos) {
    if (!gastos || gastos.length === 0) {
      tbody.innerHTML = '<tr><td colspan="8"><div class="empty-state"><i class="bi bi-inbox d-block"></i>No hay gastos cargados en este período.</div></td></tr>';
      return;
    }

    var html = '';
    gastos.forEach(function (g) {
      var estadoClass = 'status-pending';
      var estadoLabel = g.estado.charAt(0).toUpperCase() + g.estado.slice(1);
      if (g.estado === 'pagado') estadoClass = 'status-ok';
      if (g.estado === 'anulado') estadoClass = 'status-alert';

      var fecha = g.fecha_gasto ? formatFecha(g.fecha_gasto) : '-';
      var monto = formatMonto(g.monto);

      html += '<tr data-id="' + g.id + '">';
      html += '<td>' + fecha + '</td>';
      html += '<td>' + esc(g.rubro) + '</td>';
      html += '<td style="white-space:normal;max-width:220px;">' + esc(g.descripcion) + '</td>';
      html += '<td>' + esc(g.proveedor || '-') + '</td>';
      html += '<td>$ ' + monto + '</td>';
      html += '<td>' + esc(g.comprobante_nro || '-') + '</td>';
      html += '<td><span class="status-pill ' + estadoClass + '">' + estadoLabel + '</span></td>';
      html += '<td>';
      if (g.estado !== 'anulado') {
        html += '<button class="btn-danger-sm" onclick="anularGasto(' + g.id + ')"><i class="bi bi-x-circle"></i> Anular</button>';
      } else {
        html += '<span style="color:#b42318;font-size:.8rem;font-weight:800;">Anulado</span>';
      }
      html += '</td>';
      html += '</tr>';
    });

    tbody.innerHTML = html;
  }

  // --- Función: Renderizar KPIs ---
  function renderKPIs(data) {
    document.getElementById('kpiTotal').textContent = '$ ' + formatMonto(data.total);
    document.getElementById('kpiCantidad').textContent = data.cantidad;
    document.getElementById('kpiRubros').textContent = data.rubros ? data.rubros.length : 0;
  }

  // --- Función: Renderizar resumen por rubro ---
  function renderRubros(rubros, total) {
    var cont = document.getElementById('contenedorRubros');
    if (!rubros || rubros.length === 0) {
      cont.innerHTML = '<div class="empty-state"><i class="bi bi-pie-chart d-block"></i>Cargá gastos para ver el resumen.</div>';
      return;
    }

    var html = '';
    rubros.forEach(function (r) {
      var pct = total > 0 ? Math.round((parseFloat(r.total) / total) * 100) : 0;
      html += '<div class="mini-card">';
      html += '<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">';
      html += '<div><strong style="color:#1f3f18;font-size:.92rem;">' + esc(r.rubro) + '</strong>';
      html += ' <small style="color:#688162;font-weight:700;">(' + r.cantidad + ')</small></div>';
      html += '<span class="tag-soft">$ ' + formatMonto(r.total) + '</span>';
      html += '</div>';
      html += '<div class="rubro-bar"><div class="rubro-bar-fill" style="width:' + pct + '%;"></div></div>';
      html += '<small style="color:#688162;font-weight:700;">' + pct + '% del total</small>';
      html += '</div>';
    });

    cont.innerHTML = html;
  }

  // --- Función: Anular gasto ---
  window.anularGasto = function (id) {
    if (!confirm('¿Anular este gasto? No se elimina, queda marcado como anulado.')) return;

    var fd = new FormData();
    fd.append('id', id);
    fd.append('accion', 'anular');

    fetch(BASE + '?action=eliminar_gasto', {
      method: 'POST',
      body: fd
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      mostrarRespuesta(data);
      if (data.status === 'ok') cargarGastos();
    })
    .catch(function () {
      mostrarRespuesta({ status: 'error', message: 'Error de conexión.' });
    });
  };

  // --- Helpers ---
  function mostrarRespuesta(data) {
    respuesta.style.display = 'block';
    if (data.status === 'ok') {
      respuesta.style.background = '#eaffea';
      respuesta.style.border = '1px solid #d5edd2';
      respuesta.style.color = '#0b8707';
    } else {
      respuesta.style.background = '#ffe8e8';
      respuesta.style.border = '1px solid #f3c2c2';
      respuesta.style.color = '#b42318';
    }
    respuesta.innerHTML = '<i class="bi bi-' + (data.status === 'ok' ? 'check-circle' : 'exclamation-circle') + '"></i> ' + esc(data.message);

    setTimeout(function () {
      respuesta.style.display = 'none';
    }, 4000);
  }

  function formatMonto(n) {
    return parseFloat(n).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function formatFecha(dateStr) {
    var parts = dateStr.split('-');
    return parts[2] + '/' + parts[1] + '/' + parts[0];
  }

  function esc(str) {
    if (!str) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
  }

});
</script>