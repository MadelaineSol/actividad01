<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../smtp_config.php';

// Cargar PHPMailer
require_once __DIR__ . '/../phpmailer/Exception.php';
require_once __DIR__ . '/../phpmailer/PHPMailer.php';
require_once __DIR__ . '/../phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$con = Database::getCon();

$id_empresa = isset($_POST['id_empresa_administradora']) ? trim($_POST['id_empresa_administradora']) : '';
$periodo    = isset($_POST['periodo']) ? trim($_POST['periodo']) : '';

// Fallbacks
if ($id_empresa === '' && isset($_SESSION['contexto_id']) && $_SESSION['contexto_id'] !== '') {
    $id_empresa = trim($_SESSION['contexto_id']);
}
if ($id_empresa === '') {
    $qf = mysqli_query($con, "SELECT DISTINCT `id_empresa_administradora` FROM `gastos` LIMIT 1");
    if ($qf && $rf = mysqli_fetch_assoc($qf)) $id_empresa = $rf['id_empresa_administradora'];
}
if ($periodo === '') {
    $periodo = date('Y-m');
}

if ($id_empresa === '') {
    echo json_encode(['status' => 'error', 'message' => 'No se pudo determinar la empresa.']);
    exit;
}

$esc = function($v) use ($con) { return mysqli_real_escape_string($con, $v); };
$id_empresa_e = $esc($id_empresa);
$periodo_e    = $esc($periodo);

// Nombre del periodo
$partes = explode('-', $periodo);
$anio = intval($partes[0]);
$mes  = intval($partes[1]);
$meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
$nombreMes = isset($meses[$mes]) ? $meses[$mes] : $mes;
$periodoLabel = $nombreMes . ' ' . $anio;

$nombre_admin = 'GesCon';
$qAdmin = @mysqli_query($con, "SELECT `nombre` FROM `barrios` WHERE `id` = '$id_empresa_e' LIMIT 1");
if ($qAdmin && $rAdmin = mysqli_fetch_assoc($qAdmin)) {
    $nombre_admin = $rAdmin['nombre'] ?: 'GesCon';
}

// --- Traer cobranzas agrupadas por persona ---
$sql = "SELECT 
            c.`id`, c.`unidad_funcional_id`, c.`persona_id`, c.`tipo_persona`,
            c.`concepto`, c.`detalle`, c.`importe`, c.`importe_pagado`, c.`saldo`,
            c.`estado`, c.`fecha_vencimiento`,
            uf.`nombre` AS unidad_nombre, uf.`codigo` AS unidad_codigo
        FROM `cobranzas` c
        LEFT JOIN `unidades_funcionales` uf ON uf.`id` = c.`unidad_funcional_id`
        WHERE c.`id_empresa_administradora` = '$id_empresa_e'
          AND c.`periodo` = '$periodo_e'
        ORDER BY c.`unidad_funcional_id` ASC, c.`concepto` ASC";

$query = mysqli_query($con, $sql);

if (!$query || mysqli_num_rows($query) === 0) {
    echo json_encode(['status' => 'error', 'message' => 'No hay expensas generadas para ' . $periodoLabel . '.']);
    exit;
}

$agrupado = [];
while ($row = mysqli_fetch_assoc($query)) {
    $pid = $row['persona_id'];
    if ($pid <= 0) continue;

    if (!isset($agrupado[$pid])) {
        $pid_e = $esc($pid);
        $qPers = mysqli_query($con, "SELECT `nombre`, `apellido`, `email` FROM `personas` WHERE `id` = '$pid_e' LIMIT 1");
        $persona = $qPers ? mysqli_fetch_assoc($qPers) : null;

        $agrupado[$pid] = [
            'persona_id'     => $pid,
            'persona_nombre' => $persona ? trim($persona['nombre'] . ' ' . $persona['apellido']) : 'Sin nombre',
            'persona_email'  => $persona ? trim($persona['email']) : '',
            'tipo_persona'   => $row['tipo_persona'],
            'unidad_nombre'  => $row['unidad_nombre'] ?: $row['unidad_codigo'],
            'fecha_vencimiento' => $row['fecha_vencimiento'],
            'cargos'         => [],
            'total_emitido'  => 0,
            'total_pagado'   => 0,
            'total_saldo'    => 0
        ];
    }

    $agrupado[$pid]['cargos'][] = [
        'concepto' => $row['concepto'],
        'detalle'  => $row['detalle'],
        'importe'  => floatval($row['importe']),
        'pagado'   => floatval($row['importe_pagado']),
        'saldo'    => floatval($row['saldo']),
        'estado'   => $row['estado']
    ];

    $agrupado[$pid]['total_emitido'] += floatval($row['importe']);
    $agrupado[$pid]['total_pagado']  += floatval($row['importe_pagado']);
    $agrupado[$pid]['total_saldo']   += floatval($row['saldo']);
}

// --- Enviar emails con PHPMailer ---
$enviados   = 0;
$sin_email  = 0;
$errores    = 0;
$detalle    = [];

foreach ($agrupado as $p) {

    $email = $p['persona_email'];

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $sin_email++;
        $detalle[] = ['nombre' => $p['persona_nombre'], 'unidad' => $p['unidad_nombre'], 'email' => $email ?: '(sin email)', 'status' => 'sin_email'];
        continue;
    }

    $html = buildEmailHTML($p, $periodoLabel, $nombre_admin);
    $asunto = 'Expensas ' . $periodoLabel . ' - ' . $p['unidad_nombre'] . ' | ' . $nombre_admin;

    // --- PHPMailer ---
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($email, $p['persona_nombre']);
        $mail->addReplyTo(SMTP_FROM_EMAIL, SMTP_FROM_NAME);

        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $html;
        $mail->AltBody = 'Expensas ' . $periodoLabel . ' - Total: $' . number_format($p['total_saldo'], 2, ',', '.') . ' - Vto: ' . $p['fecha_vencimiento'];

        $mail->send();

        $enviados++;
        $detalle[] = ['nombre' => $p['persona_nombre'], 'unidad' => $p['unidad_nombre'], 'email' => $email, 'status' => 'enviado'];

    } catch (Exception $e) {
        $errores++;
        $detalle[] = ['nombre' => $p['persona_nombre'], 'unidad' => $p['unidad_nombre'], 'email' => $email, 'status' => 'error', 'error_msg' => $mail->ErrorInfo];
    }
}

// Respuesta
$mensaje = 'Se enviaron ' . $enviados . ' emails correctamente.';
if ($sin_email > 0) $mensaje .= ' ' . $sin_email . ' persona(s) sin email.';
if ($errores > 0)   $mensaje .= ' ' . $errores . ' envio(s) fallaron.';

echo json_encode([
    'status'  => $enviados > 0 ? 'ok' : 'error',
    'message' => $mensaje,
    'resumen' => ['enviados' => $enviados, 'sin_email' => $sin_email, 'errores' => $errores, 'total' => count($agrupado)],
    'detalle' => $detalle
]);

exit;


// =============================================
function buildEmailHTML($p, $periodoLabel, $nombreAdmin) {

    $fmt = function($n) { return number_format(floatval($n), 2, ',', '.'); };

    $venc = '';
    if ($p['fecha_vencimiento']) {
        $dv = explode('-', $p['fecha_vencimiento']);
        $venc = $dv[2] . '/' . $dv[1] . '/' . $dv[0];
    }

    $cargosHTML = '';
    foreach ($p['cargos'] as $c) {
        $isDeuda = ($c['concepto'] === 'Deuda anterior');
        $isMulta = ($c['concepto'] === 'Multa');
        $colorImporte = ($isDeuda || $isMulta) ? '#b42318' : '#0b8707';
        $icono = $isDeuda ? '⚠️' : ($isMulta ? '🚫' : '📄');

        $estadoColor = '#9a7700'; $estadoTxt = 'Pendiente';
        if ($c['estado'] === 'pagada') { $estadoColor = '#0b8707'; $estadoTxt = 'Pagada'; }
        elseif ($c['estado'] === 'parcial') { $estadoColor = '#d6a90c'; $estadoTxt = 'Parcial'; }

        $cargosHTML .= '
        <tr>
            <td style="padding:10px 14px;border-bottom:1px solid #f0f7ee;font-size:14px;color:#1f3f18">
                ' . $icono . ' <strong>' . htmlspecialchars($c['concepto']) . '</strong>
                <br><span style="font-size:12px;color:#688162">' . htmlspecialchars($c['detalle']) . '</span>
            </td>
            <td style="padding:10px 14px;border-bottom:1px solid #f0f7ee;text-align:right;font-size:14px;color:' . $colorImporte . ';font-weight:700">$ ' . $fmt($c['importe']) . '</td>
            <td style="padding:10px 14px;border-bottom:1px solid #f0f7ee;text-align:right;font-size:14px;color:#0b8707;font-weight:700">$ ' . $fmt($c['pagado']) . '</td>
            <td style="padding:10px 14px;border-bottom:1px solid #f0f7ee;text-align:right;font-size:14px;font-weight:700;color:' . ($c['saldo'] > 0 ? '#b42318' : '#0b8707') . '">$ ' . $fmt($c['saldo']) . '</td>
            <td style="padding:10px 14px;border-bottom:1px solid #f0f7ee;text-align:center">
                <span style="background:' . ($c['estado']==='pagada'?'#e9ffe8':($c['estado']==='parcial'?'#fff6d8':'#ffe5e5')) . ';color:' . $estadoColor . ';padding:4px 10px;border-radius:99px;font-size:11px;font-weight:800">' . $estadoTxt . '</span>
            </td>
        </tr>';
    }

    $saldoColor = $p['total_saldo'] > 0 ? '#b42318' : '#0b8707';

    return '
<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f6fff5;font-family:Arial,Helvetica,sans-serif">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f6fff5;padding:20px 0">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 10px 30px rgba(16,168,8,0.08)">

    <tr><td style="background:linear-gradient(135deg,#16c60c,#0b8707);padding:28px 30px;text-align:center">
        <div style="font-size:28px;font-weight:900;color:#f4c51c;margin-bottom:4px">' . htmlspecialchars($nombreAdmin) . '</div>
        <div style="font-size:14px;color:rgba(255,255,255,0.9);font-weight:700">Liquidacion de expensas</div>
    </td></tr>

    <tr><td style="padding:25px 30px 15px">
        <div style="background:#eaffea;border-radius:14px;padding:16px 20px;margin-bottom:18px">
            <div style="font-size:12px;color:#688162;font-weight:800;text-transform:uppercase;letter-spacing:1px">Periodo</div>
            <div style="font-size:22px;color:#0b8707;font-weight:900">' . htmlspecialchars($periodoLabel) . '</div>
        </div>
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:18px">
            <tr><td style="padding:6px 0;font-size:14px;color:#688162;font-weight:700">Unidad</td><td style="padding:6px 0;font-size:14px;color:#1f3f18;font-weight:900;text-align:right">' . htmlspecialchars($p['unidad_nombre']) . '</td></tr>
            <tr><td style="padding:6px 0;font-size:14px;color:#688162;font-weight:700">Titular</td><td style="padding:6px 0;font-size:14px;color:#1f3f18;font-weight:900;text-align:right">' . htmlspecialchars($p['persona_nombre']) . '</td></tr>
            <tr><td style="padding:6px 0;font-size:14px;color:#688162;font-weight:700">Tipo</td><td style="padding:6px 0;font-size:14px;color:#1f3f18;font-weight:900;text-align:right">' . htmlspecialchars(ucfirst($p['tipo_persona'])) . '</td></tr>
            ' . ($venc ? '<tr><td style="padding:6px 0;font-size:14px;color:#688162;font-weight:700">Vencimiento</td><td style="padding:6px 0;font-size:14px;color:#b42318;font-weight:900;text-align:right">' . $venc . '</td></tr>' : '') . '
        </table>
    </td></tr>

    <tr><td style="padding:0 30px 20px">
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #d5edd2;border-radius:14px;overflow:hidden">
            <tr>
                <th style="padding:10px 14px;background:#eaffea;text-align:left;font-size:11px;color:#0b8707;font-weight:900;text-transform:uppercase">Concepto</th>
                <th style="padding:10px 14px;background:#eaffea;text-align:right;font-size:11px;color:#0b8707;font-weight:900;text-transform:uppercase">Emitido</th>
                <th style="padding:10px 14px;background:#eaffea;text-align:right;font-size:11px;color:#0b8707;font-weight:900;text-transform:uppercase">Pagado</th>
                <th style="padding:10px 14px;background:#eaffea;text-align:right;font-size:11px;color:#0b8707;font-weight:900;text-transform:uppercase">Saldo</th>
                <th style="padding:10px 14px;background:#eaffea;text-align:center;font-size:11px;color:#0b8707;font-weight:900;text-transform:uppercase">Estado</th>
            </tr>
            ' . $cargosHTML . '
            <tr style="background:#f8fff7">
                <td style="padding:12px 14px;font-size:14px;font-weight:900;color:#0b8707">TOTAL</td>
                <td style="padding:12px 14px;text-align:right;font-size:14px;font-weight:900;color:#0b8707">$ ' . $fmt($p['total_emitido']) . '</td>
                <td style="padding:12px 14px;text-align:right;font-size:14px;font-weight:900;color:#0b8707">$ ' . $fmt($p['total_pagado']) . '</td>
                <td style="padding:12px 14px;text-align:right;font-size:15px;font-weight:900;color:' . $saldoColor . '">$ ' . $fmt($p['total_saldo']) . '</td>
                <td></td>
            </tr>
        </table>
    </td></tr>

    ' . ($p['total_saldo'] > 0 ? '
    <tr><td style="padding:0 30px 25px">
        <div style="background:#ffe5e5;border:2px solid #f3c2c2;border-radius:14px;padding:18px 20px;text-align:center">
            <div style="font-size:12px;color:#b42318;font-weight:800;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px">Saldo pendiente de pago</div>
            <div style="font-size:28px;color:#b42318;font-weight:900">$ ' . $fmt($p['total_saldo']) . '</div>
            ' . ($venc ? '<div style="font-size:12px;color:#b42318;font-weight:700;margin-top:4px">Vencimiento: ' . $venc . '</div>' : '') . '
        </div>
    </td></tr>' : '
    <tr><td style="padding:0 30px 25px">
        <div style="background:#e9ffe8;border:2px solid #d5edd2;border-radius:14px;padding:18px 20px;text-align:center">
            <div style="font-size:14px;color:#0b8707;font-weight:900">✅ Cuenta al dia - Sin saldo pendiente</div>
        </div>
    </td></tr>') . '

    <tr><td style="background:#f8fff7;padding:20px 30px;text-align:center;border-top:1px solid #d5edd2">
        <div style="font-size:12px;color:#688162;font-weight:700;line-height:1.6">
            Email automatico generado por <strong style="color:#0b8707">' . htmlspecialchars($nombreAdmin) . '</strong>.<br>
            Ante cualquier consulta, comunicate con la administracion.
        </div>
    </td></tr>

</table>
</td></tr></table>
</body></html>';
}
?>