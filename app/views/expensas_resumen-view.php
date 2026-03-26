<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$contexto_id = '';
if (isset($_SESSION['contexto_id']) && $_SESSION['contexto_id'] !== '') {
    $contexto_id = $_SESSION['contexto_id'];
}

require_once __DIR__ . '/../database.php';
$con = Database::getCon();
$id_e = mysqli_real_escape_string($con, $contexto_id);

// Get gastos data
$gastos = [];
$total_gastos = 0;
$rubros_data = [];

$periodo_filtro = isset($_GET['periodo']) ? $_GET['periodo'] : '';
$where_periodo = '';
if ($periodo_filtro !== '') {
    $per_e = mysqli_real_escape_string($con, $periodo_filtro);
    $where_periodo = " AND `periodo` = '$per_e'";
}

// Gastos by rubro
$qRubros = mysqli_query($con, "SELECT `categoria` as rubro, SUM(`monto`) as total, COUNT(*) as cantidad
    FROM `gastos`
    WHERE `id_empresa_administradora`='$id_e' AND `estado` != 'anulado' $where_periodo
    GROUP BY `categoria` ORDER BY total DESC");
if ($qRubros) {
    while ($r = mysqli_fetch_assoc($qRubros)) {
        $rubros_data[] = $r;
        $total_gastos += floatval($r['total']);
    }
}

// Cobranzas summary
$qCob = mysqli_query($con, "SELECT
    COALESCE(SUM(`importe`), 0) as emitido,
    COALESCE(SUM(`importe_pagado`), 0) as cobrado,
    COALESCE(SUM(`saldo`), 0) as pendiente,
    COUNT(*) as registros
    FROM `cobranzas` WHERE `id_empresa_administradora`='$id_e' $where_periodo");
$cob = $qCob ? mysqli_fetch_assoc($qCob) : ['emitido'=>0,'cobrado'=>0,'pendiente'=>0,'registros'=>0];

// Available periods
$periodos = [];
$qPer = mysqli_query($con, "SELECT DISTINCT `periodo` FROM `gastos`
    WHERE `id_empresa_administradora`='$id_e'
    ORDER BY `periodo` DESC LIMIT 12");
if ($qPer) {
    while ($rp = mysqli_fetch_assoc($qPer)) {
        $periodos[] = $rp['periodo'];
    }
}
?>
<style>
.res .soft-card{background:rgba(255,255,255,.94);border:1px solid #d5edd2;box-shadow:0 16px 35px rgba(16,168,8,.10);border-radius:28px}
.res .accent-line{width:92px;height:6px;border-radius:999px;background:linear-gradient(90deg,#f4c51c,#16c60c);margin-bottom:.9rem}
.res .page-title{font-size:clamp(1.7rem,3vw,2.5rem);font-weight:900;color:#0b8707;line-height:1.05;margin-bottom:.2rem}
.res .page-subtitle{color:#688162;font-size:.98rem;font-weight:700;line-height:1.5;margin:0}
.res .kpi-card{background:#fff;border:1px solid #d5edd2;border-radius:26px;box-shadow:0 16px 35px rgba(16,168,8,.10);padding:1.2rem;height:100%;position:relative;overflow:hidden}
.res .kpi-card::after{content:"";position:absolute;width:90px;height:90px;border-radius:50%;background:rgba(22,198,12,.05);top:-20px;right:-20px}
.res .kpi-label{font-size:.84rem;color:#688162;font-weight:800;margin-bottom:.45rem;position:relative;z-index:2}
.res .kpi-value{font-size:clamp(1.3rem,2vw,1.9rem);color:#0b8707;font-weight:900;line-height:1.08;position:relative;z-index:2}
.res .kpi-icon{width:54px;height:54px;border-radius:18px;background:linear-gradient(135deg,#f4c51c,#ffd84f);color:#453c00;display:flex;align-items:center;justify-content:center;font-size:1.18rem;box-shadow:0 10px 20px rgba(244,197,28,.20);flex-shrink:0;position:relative;z-index:2}
.res .section-title{font-size:1.18rem;color:#0b8707;font-weight:900;margin-bottom:.15rem}
.res .section-subtitle{font-size:.87rem;color:#688162;font-weight:700;margin:0}
.res .table-modern{margin:0;min-width:500px}
.res .table-modern thead th{background:#eaffea;color:#0b8707;font-size:.82rem;font-weight:900;border-bottom:none;white-space:nowrap}
.res .table-modern td{color:#1f3f18;font-size:.91rem;font-weight:700;vertical-align:middle;background:#fff}
.res .status-pill{display:inline-flex;align-items:center;padding:.42rem .78rem;border-radius:999px;font-size:.75rem;font-weight:900}
.res .status-ok{background:#e9ffe8;color:#0b8707}
.res .status-pending{background:#fff6d8;color:#9a7700}
.res .summary-bar{height:8px;border-radius:99px;background:#eaffea;overflow:hidden;margin-top:.35rem}
.res .summary-bar-fill{height:100%;border-radius:99px;background:linear-gradient(90deg,#16c60c,#0b8707)}
.res .btn-gescon{background:linear-gradient(135deg,#f4c51c,#d6a90c);border:none;color:#433900;border-radius:16px;min-height:46px;padding:.7rem 1.1rem;font-weight:900;box-shadow:0 10px 18px rgba(244,197,28,.20);text-decoration:none;display:inline-flex;align-items:center;gap:.4rem}
.res .form-select{min-height:46px;border-radius:14px;border:2px solid #d5edd2;font-weight:800;color:#1f3f18}
@media(max-width:575.98px){.res .soft-card{border-radius:20px}.res .kpi-card{border-radius:18px}}
</style>

<div class="res">

<div class="soft-card p-3 p-md-4 mb-4">
  <div class="row g-3 align-items-center">
    <div class="col-12 col-lg-6">
      <div class="accent-line"></div>
      <h1 class="page-title">Resumen de expensas</h1>
      <p class="page-subtitle">Vista consolidada de gastos por rubro y estado de cobranzas del consorcio.</p>
    </div>
    <div class="col-12 col-lg-6 d-flex flex-wrap gap-2 justify-content-lg-end align-items-end">
      <div>
        <label class="form-label fw-bold" style="font-size:.85rem;">Periodo</label>
        <select class="form-select" onchange="if(this.value)location.href='?view=expensas_resumen&periodo='+this.value;else location.href='?view=expensas_resumen';" style="min-width:160px;">
          <option value="">Todos</option>
          <?php foreach($periodos as $p):
            $pts=explode('-',$p);
            $sel=($periodo_filtro===$p)?'selected':'';
          ?>
          <option value="<?=$p?>" <?=$sel?>><?=$pts[1]?>/<?=$pts[0]?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <a href="?view=expensas_gastos" class="btn btn-gescon"><i class="bi bi-journal-text"></i> Ver gastos</a>
    </div>
  </div>
</div>

<!-- KPIs -->
<div class="row g-3 g-xl-4 mb-4">
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="kpi-card">
      <div class="d-flex justify-content-between align-items-start gap-3">
        <div><div class="kpi-label">Total gastos</div><div class="kpi-value">$ <?= number_format($total_gastos, 2, ',', '.') ?></div></div>
        <div class="kpi-icon"><i class="bi bi-cash-stack"></i></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="kpi-card">
      <div class="d-flex justify-content-between align-items-start gap-3">
        <div><div class="kpi-label">Emitido</div><div class="kpi-value">$ <?= number_format(floatval($cob['emitido']), 2, ',', '.') ?></div></div>
        <div class="kpi-icon"><i class="bi bi-receipt-cutoff"></i></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="kpi-card">
      <div class="d-flex justify-content-between align-items-start gap-3">
        <div><div class="kpi-label">Cobrado</div><div class="kpi-value" style="color:#0b8707;">$ <?= number_format(floatval($cob['cobrado']), 2, ',', '.') ?></div></div>
        <div class="kpi-icon"><i class="bi bi-check-circle-fill"></i></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="kpi-card">
      <div class="d-flex justify-content-between align-items-start gap-3">
        <div><div class="kpi-label">Pendiente</div><div class="kpi-value" style="color:#b42318;">$ <?= number_format(floatval($cob['pendiente']), 2, ',', '.') ?></div></div>
        <div class="kpi-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
      </div>
    </div>
  </div>
</div>

<!-- Gastos por rubro -->
<div class="row g-4">
  <div class="col-12 col-xl-7">
    <div class="soft-card p-3 p-md-4 h-100">
      <h2 class="section-title">Detalle de gastos por rubro</h2>
      <p class="section-subtitle mb-3">Distribucion de gastos del periodo seleccionado.</p>

      <?php if(empty($rubros_data)): ?>
      <div style="text-align:center;padding:2rem;color:#688162;font-weight:700;">
        <i class="bi bi-inbox" style="font-size:2rem;color:#d5edd2;display:block;margin-bottom:.5rem;"></i>
        No hay gastos cargados para el periodo seleccionado.
      </div>
      <?php else: ?>
      <div class="table-responsive" style="border-radius:18px;">
        <table class="table table-modern align-middle">
          <thead>
            <tr>
              <th>Rubro</th>
              <th>Cantidad</th>
              <th>Monto</th>
              <th>Participacion</th>
              <th>%</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($rubros_data as $r):
              $pct = $total_gastos > 0 ? round((floatval($r['total']) / $total_gastos) * 100, 1) : 0;
            ?>
            <tr>
              <td><span class="status-pill status-ok"><?= htmlspecialchars($r['rubro'], ENT_QUOTES, 'UTF-8') ?></span></td>
              <td><?= intval($r['cantidad']) ?></td>
              <td style="font-weight:900;">$ <?= number_format(floatval($r['total']), 2, ',', '.') ?></td>
              <td style="min-width:120px;">
                <div class="summary-bar"><div class="summary-bar-fill" style="width:<?= $pct ?>%"></div></div>
              </td>
              <td style="font-weight:900;color:#0b8707;"><?= $pct ?>%</td>
            </tr>
            <?php endforeach; ?>
            <tr style="background:#eaffea;">
              <td style="font-weight:900;">TOTAL</td>
              <td style="font-weight:900;"><?= array_sum(array_column($rubros_data, 'cantidad')) ?></td>
              <td style="font-weight:900;">$ <?= number_format($total_gastos, 2, ',', '.') ?></td>
              <td></td>
              <td style="font-weight:900;">100%</td>
            </tr>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="col-12 col-xl-5">
    <div class="soft-card p-3 p-md-4 h-100">
      <h2 class="section-title">Estado de cobranzas</h2>
      <p class="section-subtitle mb-3">Resumen de emision vs cobro.</p>

      <?php
      $emitido = floatval($cob['emitido']);
      $cobrado = floatval($cob['cobrado']);
      $pct_cobro = $emitido > 0 ? round(($cobrado / $emitido) * 100, 1) : 0;
      ?>

      <div style="background:#f6fff5;border:1px solid #d5edd2;border-radius:16px;padding:1rem;margin-bottom:1rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem;">
          <span style="font-weight:900;color:#1f3f18;font-size:.9rem;">Cobro</span>
          <span style="font-weight:900;color:#0b8707;font-size:1.1rem;"><?= $pct_cobro ?>%</span>
        </div>
        <div class="summary-bar" style="height:12px;">
          <div class="summary-bar-fill" style="width:<?= min($pct_cobro, 100) ?>%;<?= $pct_cobro < 50 ? 'background:linear-gradient(90deg,#f4c51c,#d6a90c);' : '' ?>"></div>
        </div>
      </div>

      <div style="display:grid;gap:.8rem;">
        <div style="background:#fff;border:1px solid #d5edd2;border-radius:16px;padding:1rem;">
          <div style="font-size:.84rem;color:#688162;font-weight:800;">Total emitido</div>
          <div style="font-size:1.3rem;font-weight:900;color:#0b8707;">$ <?= number_format($emitido, 2, ',', '.') ?></div>
        </div>
        <div style="background:#fff;border:1px solid #d5edd2;border-radius:16px;padding:1rem;">
          <div style="font-size:.84rem;color:#688162;font-weight:800;">Total cobrado</div>
          <div style="font-size:1.3rem;font-weight:900;color:#0b8707;">$ <?= number_format($cobrado, 2, ',', '.') ?></div>
        </div>
        <div style="background:#fff;border:1px solid #d5edd2;border-radius:16px;padding:1rem;">
          <div style="font-size:.84rem;color:#688162;font-weight:800;">Pendiente</div>
          <div style="font-size:1.3rem;font-weight:900;color:#b42318;">$ <?= number_format(floatval($cob['pendiente']), 2, ',', '.') ?></div>
        </div>
        <div style="background:#fff;border:1px solid #d5edd2;border-radius:16px;padding:1rem;">
          <div style="font-size:.84rem;color:#688162;font-weight:800;">Registros</div>
          <div style="font-size:1.3rem;font-weight:900;color:#1f3f18;"><?= intval($cob['registros']) ?></div>
        </div>
      </div>

      <div style="margin-top:1.2rem;">
        <a href="?view=expensas_cobranzas" class="btn btn-gescon w-100"><i class="bi bi-table"></i> Ver detalle de cobranzas</a>
      </div>
    </div>
  </div>
</div>

</div>
