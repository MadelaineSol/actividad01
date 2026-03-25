<?php
require_once __DIR__ . '/../data/expensas_data.php';

// UF seleccionada (puede venir por GET)
$ufSel = isset($_GET['uf']) ? (int)$_GET['uf'] : 0;
$ufData = null;
if($ufSel > 0){
  foreach($expensas_uf as $e){
    if($e['uf']==$ufSel){ $ufData=$e; break; }
  }
}
?>
<style>
:root{--g:#0b8707;--gl:#16c60c;--gs:#eaffea;--y:#f4c51c;--yd:#d6a90c;--b:#d5edd2;--t:#1f3f18;--m:#688162}
.pdf-preview{max-width:900px;margin:0 auto;background:#fff;border:1px solid var(--b);border-radius:22px;padding:2rem 2.2rem;box-shadow:0 16px 35px rgba(16,168,8,.08);font-family:'Nunito',sans-serif}
.pdf-header{display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;margin-bottom:1.2rem;flex-wrap:wrap}
.pdf-logo{font-size:1.8rem;font-weight:900;color:var(--g)}
.pdf-title-bar{background:var(--gs);border:1px solid var(--b);border-radius:12px;padding:.8rem 1rem;margin-bottom:1rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem}
.pdf-title-bar h3{margin:0;font-size:1.1rem;font-weight:900;color:var(--g)}
.pdf-section{margin-bottom:1.2rem}
.pdf-section h4{font-size:.92rem;font-weight:900;color:var(--g);margin-bottom:.6rem;padding-bottom:.4rem;border-bottom:2px solid var(--b)}
.pdf-table{width:100%;border-collapse:collapse;font-size:.82rem}
.pdf-table th{background:var(--gs);color:var(--g);font-weight:900;padding:6px 10px;text-align:left;border-bottom:2px solid var(--b);font-size:.76rem;text-transform:uppercase;letter-spacing:.03em}
.pdf-table td{padding:6px 10px;border-bottom:1px solid #f0f7ee;font-weight:700;color:var(--t)}
.pdf-total{background:linear-gradient(135deg,var(--g),var(--gl));color:#fff;border-radius:12px;padding:.9rem 1.2rem;display:flex;justify-content:space-between;align-items:center;font-weight:900;font-size:1rem;margin-bottom:.6rem}
.pdf-footer{margin-top:1.5rem;padding-top:1rem;border-top:2px solid var(--b);font-size:.72rem;color:var(--m);text-align:center;line-height:1.7}
.tcard{background:rgba(255,255,255,.94);border:1px solid var(--b);box-shadow:0 16px 35px rgba(16,168,8,.10);border-radius:28px;padding:1rem 1.25rem}
.scard{background:rgba(255,255,255,.94);border:1px solid var(--b);box-shadow:0 16px 35px rgba(16,168,8,.10);border-radius:28px}
.accent{width:92px;height:6px;border-radius:999px;background:linear-gradient(90deg,var(--y),var(--gl));margin-bottom:.9rem}
.ptitle{font-size:clamp(1.7rem,3vw,2.5rem);font-weight:900;color:var(--g);line-height:1.05;margin-bottom:.2rem}
.psub{color:var(--m);font-size:.95rem;font-weight:700;line-height:1.5;margin:0}
.stitle{font-size:1.1rem;font-weight:900;color:var(--g);margin-bottom:.15rem}
.btn-g{background:linear-gradient(135deg,var(--y),var(--yd));border:none;color:#433900;border-radius:16px;min-height:46px;padding:.7rem 1.1rem;font-weight:900;box-shadow:0 10px 18px rgba(244,197,28,.20);cursor:pointer;display:inline-flex;align-items:center;gap:.4rem}
.btn-go{background:#fff;color:var(--g);border:2px solid var(--b);border-radius:16px;min-height:46px;padding:.7rem 1.1rem;font-weight:900;cursor:pointer;display:inline-flex;align-items:center;gap:.4rem}
@media print{.no-print{display:none!important}.pdf-preview{border:none;box-shadow:none;border-radius:0;padding:0;max-width:100%}}
@media(max-width:575.98px){.pdf-preview{padding:1rem;border-radius:16px}.pdf-header{flex-direction:column}.tcard,.scard{border-radius:20px}}
</style>

<!-- Controles (no imprimen) -->
<div class="no-print mb-4">
  <div class="tcard">
    <div class="row g-3 align-items-center">
      <div class="col-12 col-xl-7">
        <div class="accent"></div>
        <h1 class="ptitle">Generador de Expensas PDF</h1>
        <p class="psub">Selecciona una UF, configura las opciones y genera la expensa completa para imprimir o enviar</p>
      </div>
      <div class="col-12 col-xl-5 d-flex flex-wrap gap-2 justify-content-xl-end">
        <button class="btn-g" onclick="window.print()"><i class="bi bi-printer-fill"></i> Imprimir / Guardar PDF</button>
        <button class="btn-go" onclick="enviarMail()"><i class="bi bi-envelope-fill"></i> Enviar por email</button>
      </div>
    </div>
  </div>

  <div class="scard p-3 p-md-4 mt-4">
    <h2 class="stitle mb-3">Configurar expensa</h2>
    <div class="row g-3">
      <div class="col-12 col-md-4">
        <label class="form-label fw-bold">Unidad Funcional</label>
        <select class="form-select" id="selUF" onchange="cambiarUF()">
          <option value="0">-- Seleccionar UF --</option>
          <?php foreach($expensas_uf as $e): ?>
          <option value="<?= $e['uf'] ?>" <?= $ufSel==$e['uf']?'selected':'' ?>>
            UF <?= $e['uf'] ?> -- <?= htmlspecialchars($e['prop'],ENT_QUOTES,'UTF-8') ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label fw-bold">Periodo</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($periodo_nombre, ENT_QUOTES, 'UTF-8') ?>" readonly>
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label fw-bold">Opciones de inclusion</label>
        <div class="d-flex flex-wrap gap-3 mt-1">
          <label style="font-size:.85rem;font-weight:900;cursor:pointer;"><input type="checkbox" id="chkDetalle" checked> Detalle gastos</label>
          <label style="font-size:.85rem;font-weight:900;cursor:pointer;"><input type="checkbox" id="chkPago" checked> Formas de pago</label>
        </div>
      </div>
    </div>
  </div>
</div>

<?php if(empty($expensas_uf)): ?>
<div style="text-align:center;padding:3rem 1rem;">
  <i class="bi bi-exclamation-triangle" style="font-size:3rem;color:#d6a90c;"></i>
  <h3 style="color:#0b8707;font-weight:900;margin-top:1rem;">No hay expensas generadas</h3>
  <p style="color:#688162;font-weight:700;">Para generar expensas, primero carga gastos en la seccion <a href="?view=expensas_gastos" style="color:#0b8707;font-weight:900;">Gastos del periodo</a> y luego usa el boton "Generar expensas".</p>
</div>
<?php else: ?>

<!-- PDF PREVIEW -->
<div class="pdf-preview" id="pdfArea">

  <!-- Header -->
  <div class="pdf-header">
    <div>
      <div class="pdf-logo">GesCon</div>
      <div style="font-size:.82rem;font-weight:900;color:#444;margin-top:2px;">ADMINISTRACION</div>
      <div style="font-size:.75rem;color:#688162;line-height:1.6;">
        Sistema de Gestion de Consorcios
      </div>
    </div>
    <div style="text-align:right;">
      <div style="font-weight:900;font-size:.95rem;color:#0b8707;">EXPENSAS DEL PERIODO</div>
      <div style="font-size:.78rem;color:#444;line-height:1.7;">
        Periodo: <?= htmlspecialchars($periodo_nombre, ENT_QUOTES, 'UTF-8') ?>
      </div>
      <div style="margin-top:8px;display:flex;gap:6px;justify-content:flex-end;flex-wrap:wrap;">
        <span style="background:#eaffea;color:#0b8707;padding:3px 9px;border-radius:6px;font-weight:900;font-size:.78rem;">1er Venc: <?= $venc1 ?> (+<?= $rec1 ?>%)</span>
        <span style="background:#fff6d8;color:#9a7700;padding:3px 9px;border-radius:6px;font-weight:900;font-size:.78rem;">2do Venc: <?= $venc2 ?> (+<?= $rec2 ?>%)</span>
      </div>
    </div>
  </div>

  <!-- Titulo -->
  <div class="pdf-title-bar">
    <h3>MIS EXPENSAS -- <?= strtoupper($periodo_nombre) ?></h3>
    <span style="font-size:.85rem;">
      <?= $ufData ? 'UF '.$ufData['uf'].' -- '.htmlspecialchars($ufData['prop'],ENT_QUOTES,'UTF-8') : 'Selecciona una UF' ?>
    </span>
  </div>

  <?php if($ufData): ?>
  <!-- Datos UF -->
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
    <div style="background:#f6fff5;border:1px solid #d5edd2;border-radius:10px;padding:12px;">
      <div style="font-weight:900;color:#0b8707;margin-bottom:6px;font-size:.8rem;text-transform:uppercase;letter-spacing:.04em;">Datos de la Unidad</div>
      <div style="font-size:.8rem;color:#444;line-height:1.8;">
        <div><strong>UF N:</strong> <?= $ufData['uf'] ?></div>
        <div><strong>Propietario:</strong> <?= htmlspecialchars($ufData['prop'],ENT_QUOTES,'UTF-8') ?></div>
        <div><strong>Coeficiente:</strong> <?= number_format($ufData['coef'], 4) ?></div>
        <div><strong>Periodo:</strong> <?= htmlspecialchars($periodo_nombre, ENT_QUOTES, 'UTF-8') ?></div>
      </div>
    </div>
    <div style="background:#f6fff5;border:1px solid #d5edd2;border-radius:10px;padding:12px;">
      <div style="font-weight:900;color:#0b8707;margin-bottom:6px;font-size:.8rem;text-transform:uppercase;letter-spacing:.04em;">Estado de Cuenta</div>
      <div style="font-size:.8rem;color:#444;line-height:1.8;">
        <div><strong>Expensa del periodo:</strong> <?= pesos($ufData['total']) ?></div>
        <div><strong>Deuda anterior:</strong> <span style="<?= $ufData['deuda']>0?'color:#b42318;font-weight:900;':'' ?>"><?= pesos($ufData['deuda']) ?></span></div>
        <?php if($ufData['deuda']>0): ?>
        <div style="color:#b42318;font-weight:900;margin-top:4px;">Tiene deuda pendiente</div>
        <?php else: ?>
        <div style="color:#0b8707;font-weight:900;margin-top:4px;">Sin deuda anterior</div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Detalle expensas -->
  <div class="pdf-section" id="sectionDetalle">
    <h4>Detalle de expensas del periodo</h4>
    <table class="pdf-table">
      <thead><tr><th>Concepto</th><th style="text-align:right;">Importe</th></tr></thead>
      <tbody>
        <tr><td>Expensas Ordinarias (prorrateo por coeficiente)</td><td style="text-align:right;"><?= pesos($ufData['expOrd']) ?></td></tr>
        <?php if($ufData['deuda']>0): ?>
        <tr style="background:#ffe5e5;"><td><strong>Deuda anterior</strong></td><td style="text-align:right;font-weight:900;color:#b42318;"><?= pesos($ufData['deuda']) ?></td></tr>
        <?php endif; ?>
        <tr style="background:#eaffea;"><td><strong>TOTAL A PAGAR</strong></td><td style="text-align:right;font-weight:900;"><?= pesos($ufData['aPagar']) ?></td></tr>
      </tbody>
    </table>
  </div>

  <!-- Gastos comunales -->
  <?php if(!empty($rubros)): ?>
  <div class="pdf-section" id="sectionGastos">
    <h4>Principales gastos comunales del periodo</h4>
    <table class="pdf-table">
      <thead><tr><th>Rubro</th><th style="text-align:right;">Incid.</th><th style="text-align:right;">Importe</th></tr></thead>
      <tbody>
        <?php foreach(array_slice($rubros,0,10) as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r['rubro'],ENT_QUOTES,'UTF-8') ?></td>
          <td style="text-align:right;"><?= number_format($r['inc'],2) ?>%</td>
          <td style="text-align:right;font-weight:900;"><?= pesos($r['importe']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <!-- Total -->
  <?php
  $total1v = $ufData['aPagar'];
  $total2v = round($total1v * (1 + $rec2/100), 2);
  ?>
  <div class="pdf-total">
    <span>TOTAL A PAGAR -- 1er Vencimiento (<?= $venc1 ?>)</span>
    <span><?= pesos($total1v) ?></span>
  </div>
  <div style="display:flex;gap:8px;margin-bottom:16px;font-size:.8rem;flex-wrap:wrap;">
    <div style="background:#fff6d8;border:1px solid #d6a90c;border-radius:8px;padding:5px 10px;font-weight:900;">
      2do venc (<?= $venc2 ?>): <?= pesos($total2v) ?> (+<?= $rec2 ?>%)
    </div>
    <?php if($ufData['deuda']>0): ?>
    <div style="background:#ffe5e5;border:1px solid #f3c2c2;border-radius:8px;padding:5px 10px;font-weight:900;color:#b42318;">
      Incluye deuda anterior de <?= pesos($ufData['deuda']) ?>
    </div>
    <?php endif; ?>
  </div>

  <?php else: ?>
  <div style="text-align:center;padding:2rem 1rem;color:#688162;font-weight:700;">
    <i class="bi bi-arrow-up-circle" style="font-size:2rem;color:#d5edd2;display:block;margin-bottom:.5rem;"></i>
    Selecciona una unidad funcional para ver la expensa detallada.
  </div>
  <?php endif; ?>

  <div class="pdf-footer">
    <strong>GesCon -- Sistema de Gestion de Consorcios</strong><br>
    Documento generado el <?= date('d/m/Y H:i') ?>
  </div>

</div><!-- /pdf-preview -->
<?php endif; ?>

<!-- Toast -->
<div class="position-fixed bottom-0 end-0 p-3 no-print" style="z-index:9999;">
  <div id="gcToast" class="toast align-items-center text-white border-0" role="alert" style="border-radius:16px;">
    <div class="d-flex"><div class="toast-body fw-bold" id="gcToastMsg">OK</div>
    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>
  </div>
</div>

<script>
function cambiarUF(){
  var uf=document.getElementById('selUF').value;
  if(uf>0) window.location.href='?view=expensas_pdf&uf='+uf;
}
function enviarMail(){
  var uf=document.getElementById('selUF').value;
  if(!uf||uf==0){
    alert('Selecciona una UF primero');
    return;
  }
  alert('Para enviar expensas por email, usa la seccion "Expensas emitidas" donde podes enviar a todos los propietarios.');
}
</script>
