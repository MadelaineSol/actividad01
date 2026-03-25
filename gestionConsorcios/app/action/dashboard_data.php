<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../database.php';

$con = Database::getCon();

$id_empresa = '';
if (isset($_SESSION['contexto_id']) && $_SESSION['contexto_id'] !== '') {
    $id_empresa = trim($_SESSION['contexto_id']);
}

if ($id_empresa === '') {
    echo json_encode(['status' => 'error', 'message' => 'No se pudo determinar la empresa.']);
    exit;
}

$esc = function($v) use ($con) { return mysqli_real_escape_string($con, $v); };
$id_e = $esc($id_empresa);

// 1. Unidades funcionales
$qUF = mysqli_query($con, "SELECT COUNT(*) as total,
    SUM(CASE WHEN estado_administrativo='activa' THEN 1 ELSE 0 END) as activas,
    SUM(CASE WHEN estado_ocupacion='ocupada' THEN 1 ELSE 0 END) as ocupadas,
    SUM(CASE WHEN estado_ocupacion='desocupada' THEN 1 ELSE 0 END) as desocupadas
    FROM `unidades_funcionales` WHERE `id_empresa_administradora`='$id_e'");
$uf = mysqli_fetch_assoc($qUF);

// 2. Personas
$qPer = mysqli_query($con, "SELECT COUNT(*) as total,
    SUM(CASE WHEN tipo_persona='propietario' THEN 1 ELSE 0 END) as propietarios,
    SUM(CASE WHEN tipo_persona='inquilino' THEN 1 ELSE 0 END) as inquilinos
    FROM `personas` WHERE `id_empresa_administradora`='$id_e'");
$personas = mysqli_fetch_assoc($qPer);

// 3. Gastos por periodo (last 6 months)
$qGastos = mysqli_query($con, "SELECT `periodo`, SUM(`monto`) as total, COUNT(*) as cantidad
    FROM `gastos`
    WHERE `id_empresa_administradora`='$id_e' AND `estado` != 'anulado'
    GROUP BY `periodo` ORDER BY `periodo` DESC LIMIT 6");
$gastos_por_periodo = [];
while ($r = mysqli_fetch_assoc($qGastos)) {
    $gastos_por_periodo[] = $r;
}

// 4. Gastos por rubro (current or latest period)
$periodo_actual = date('Y-m');
$qRubros = mysqli_query($con, "SELECT `categoria` as rubro, SUM(`monto`) as total, COUNT(*) as cantidad
    FROM `gastos`
    WHERE `id_empresa_administradora`='$id_e' AND `estado` != 'anulado'
    AND `periodo` = '$periodo_actual'
    GROUP BY `categoria` ORDER BY total DESC");
$gastos_por_rubro = [];
while ($r = mysqli_fetch_assoc($qRubros)) {
    $gastos_por_rubro[] = $r;
}
// If no data for current period, try latest
if (empty($gastos_por_rubro) && !empty($gastos_por_periodo)) {
    $ultimo = $esc($gastos_por_periodo[0]['periodo']);
    $qRubros2 = mysqli_query($con, "SELECT `categoria` as rubro, SUM(`monto`) as total, COUNT(*) as cantidad
        FROM `gastos`
        WHERE `id_empresa_administradora`='$id_e' AND `estado` != 'anulado'
        AND `periodo` = '$ultimo'
        GROUP BY `categoria` ORDER BY total DESC");
    while ($r = mysqli_fetch_assoc($qRubros2)) {
        $gastos_por_rubro[] = $r;
    }
}

// 5. Cobranzas resumen
$qCob = mysqli_query($con, "SELECT
    COALESCE(SUM(`importe`), 0) as total_emitido,
    COALESCE(SUM(`importe_pagado`), 0) as total_cobrado,
    COALESCE(SUM(`saldo`), 0) as total_pendiente,
    SUM(CASE WHEN `estado`='pagada' THEN 1 ELSE 0 END) as pagadas,
    SUM(CASE WHEN `estado`='pendiente' AND `saldo` > 0 THEN 1 ELSE 0 END) as pendientes,
    COUNT(*) as total_registros
    FROM `cobranzas` WHERE `id_empresa_administradora`='$id_e'");
$cobranzas = mysqli_fetch_assoc($qCob);

// 6. Cobranzas por periodo (last 6)
$qCobPer = mysqli_query($con, "SELECT `periodo`,
    COALESCE(SUM(`importe`), 0) as emitido,
    COALESCE(SUM(`importe_pagado`), 0) as cobrado,
    COALESCE(SUM(`saldo`), 0) as pendiente
    FROM `cobranzas` WHERE `id_empresa_administradora`='$id_e'
    GROUP BY `periodo` ORDER BY `periodo` DESC LIMIT 6");
$cobranzas_por_periodo = [];
while ($r = mysqli_fetch_assoc($qCobPer)) {
    $cobranzas_por_periodo[] = $r;
}

// 7. Top deudores
$qDeudores = mysqli_query($con, "SELECT c.`unidad_funcional_id`,
    uf.`nombre` as unidad,
    p.`nombre` as persona_nombre, p.`apellido` as persona_apellido,
    SUM(c.`saldo`) as deuda_total
    FROM `cobranzas` c
    LEFT JOIN `unidades_funcionales` uf ON c.`unidad_funcional_id` = uf.`id`
    LEFT JOIN `personas` p ON c.`persona_id` = p.`id`
    WHERE c.`id_empresa_administradora`='$id_e' AND c.`saldo` > 0 AND c.`estado` != 'anulada'
    GROUP BY c.`unidad_funcional_id`
    ORDER BY deuda_total DESC LIMIT 5");
$top_deudores = [];
while ($r = mysqli_fetch_assoc($qDeudores)) {
    $top_deudores[] = $r;
}

echo json_encode([
    'status' => 'ok',
    'unidades' => $uf,
    'personas' => $personas,
    'gastos_por_periodo' => array_reverse($gastos_por_periodo),
    'gastos_por_rubro' => $gastos_por_rubro,
    'cobranzas' => $cobranzas,
    'cobranzas_por_periodo' => array_reverse($cobranzas_por_periodo),
    'top_deudores' => $top_deudores
]);
exit;
