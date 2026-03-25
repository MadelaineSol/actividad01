<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../database.php';

$con = Database::getCon();

$id_empresa = isset($_POST['id_empresa_administradora']) ? trim($_POST['id_empresa_administradora']) : '';
$periodo = isset($_POST['periodo']) ? trim($_POST['periodo']) : '';

if ($id_empresa === '' && isset($_SESSION['contexto_id']) && $_SESSION['contexto_id'] !== '') {
    $id_empresa = trim($_SESSION['contexto_id']);
}

if ($id_empresa === '') {
    echo json_encode(['status' => 'error', 'message' => 'No se pudo determinar la empresa.']);
    exit;
}

$esc = function($v) use ($con) { return mysqli_real_escape_string($con, $v); };
$id_empresa_e = $esc($id_empresa);

// Build WHERE clause
$where = "c.`id_empresa_administradora` = '$id_empresa_e'";
if ($periodo !== '') {
    $periodo_e = $esc($periodo);
    $where .= " AND c.`periodo` = '$periodo_e'";
}

// Get cobranzas with joined data
$sql = "SELECT c.*,
        uf.nombre as unidad_nombre, uf.codigo as unidad_codigo,
        p.nombre as persona_nombre, p.apellido as persona_apellido, p.email as persona_email
        FROM `cobranzas` c
        LEFT JOIN `unidades_funcionales` uf ON c.`unidad_funcional_id` = uf.`id`
        LEFT JOIN `personas` p ON c.`persona_id` = p.`id`
        WHERE $where
        ORDER BY c.`periodo` DESC, c.`id` DESC";

$result = mysqli_query($con, $sql);

$cobranzas = [];
$total_emitido = 0;
$total_cobrado = 0;
$total_pendiente = 0;
$morosos = 0;
$al_dia = 0;

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $importe = floatval($row['importe']);
        $pagado = floatval($row['importe_pagado']);
        $saldo = floatval($row['saldo']);

        $total_emitido += $importe;
        $total_cobrado += $pagado;
        $total_pendiente += $saldo;

        if ($row['estado'] === 'pagada') {
            $al_dia++;
        } elseif ($saldo > 0) {
            $morosos++;
        }

        $cobranzas[] = $row;
    }
}

// Get distinct periods
$sqlPeriodos = "SELECT DISTINCT `periodo` FROM `cobranzas`
                WHERE `id_empresa_administradora` = '$id_empresa_e'
                ORDER BY `periodo` DESC";
$resPeriodos = mysqli_query($con, $sqlPeriodos);
$periodos = [];
if ($resPeriodos) {
    while ($rp = mysqli_fetch_assoc($resPeriodos)) {
        $periodos[] = $rp['periodo'];
    }
}

echo json_encode([
    'status' => 'ok',
    'cobranzas' => $cobranzas,
    'kpi' => [
        'total_emitido' => $total_emitido,
        'total_cobrado' => $total_cobrado,
        'total_pendiente' => $total_pendiente,
        'morosos' => $morosos,
        'al_dia' => $al_dia,
        'total_registros' => count($cobranzas)
    ],
    'periodos' => $periodos
]);
exit;
