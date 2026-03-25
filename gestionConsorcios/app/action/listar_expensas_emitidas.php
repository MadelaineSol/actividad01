<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../database.php';

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
    echo json_encode(['status' => 'ok', 'unidades' => [], 'totales' => ['emitido' => 0, 'cobrado' => 0, 'pendiente' => 0, 'cantidad_unidades' => 0]]);
    exit;
}

$id_empresa_e = mysqli_real_escape_string($con, $id_empresa);
$periodo_e    = mysqli_real_escape_string($con, $periodo);

// --- Traer todas las cobranzas del periodo agrupadas por unidad ---
$sql = "SELECT 
            c.`id`,
            c.`unidad_funcional_id`,
            c.`persona_id`,
            c.`tipo_persona`,
            c.`concepto`,
            c.`detalle`,
            c.`importe`,
            c.`importe_pagado`,
            c.`saldo`,
            c.`estado`,
            c.`fecha_emision`,
            c.`fecha_vencimiento`,
            c.`observaciones`,
            uf.`nombre`      AS unidad_nombre,
            uf.`codigo`      AS unidad_codigo,
            uf.`tipo_unidad` AS unidad_tipo,
            uf.`coeficiente_expensas` AS coeficiente
        FROM `cobranzas` c
        LEFT JOIN `unidades_funcionales` uf ON uf.`id` = c.`unidad_funcional_id`
        WHERE c.`id_empresa_administradora` = '$id_empresa_e'
          AND c.`periodo` = '$periodo_e'
        ORDER BY c.`unidad_funcional_id` ASC, c.`concepto` ASC, c.`id` ASC";

$query = mysqli_query($con, $sql);

if (!$query) {
    echo json_encode(['status' => 'error', 'message' => 'Error SQL: ' . mysqli_error($con)]);
    exit;
}

// Agrupar por unidad
$unidades_map = [];
$total_emitido  = 0;
$total_cobrado  = 0;
$total_pendiente = 0;

while ($row = mysqli_fetch_assoc($query)) {
    $uf_id = $row['unidad_funcional_id'];

    // Buscar nombre de la persona
    $persona_nombre = '';
    if ($row['persona_id'] > 0) {
        $pid = mysqli_real_escape_string($con, $row['persona_id']);
        $qp = @mysqli_query($con, "SELECT `nombre`, `apellido` FROM `personas` WHERE `id` = '$pid' LIMIT 1");
        if ($qp && $rp = mysqli_fetch_assoc($qp)) {
            $persona_nombre = trim($rp['nombre'] . ' ' . $rp['apellido']);
        }
    }

    if (!isset($unidades_map[$uf_id])) {
        $unidades_map[$uf_id] = [
            'unidad_id'     => $uf_id,
            'unidad_nombre' => $row['unidad_nombre'] ?: $row['unidad_codigo'],
            'unidad_codigo' => $row['unidad_codigo'],
            'unidad_tipo'   => $row['unidad_tipo'],
            'coeficiente'   => floatval($row['coeficiente']),
            'persona_nombre'=> $persona_nombre,
            'tipo_persona'  => $row['tipo_persona'],
            'fecha_vencimiento' => $row['fecha_vencimiento'],
            'cargos'        => [],
            'total_emitido' => 0,
            'total_pagado'  => 0,
            'total_saldo'   => 0,
            'estado_general'=> 'pendiente'
        ];
    }

    // Si no teniamos nombre, intentar con este registro
    if ($unidades_map[$uf_id]['persona_nombre'] === '' && $persona_nombre !== '') {
        $unidades_map[$uf_id]['persona_nombre'] = $persona_nombre;
        $unidades_map[$uf_id]['tipo_persona'] = $row['tipo_persona'];
    }

    $unidades_map[$uf_id]['cargos'][] = [
        'id'        => $row['id'],
        'concepto'  => $row['concepto'],
        'detalle'   => $row['detalle'],
        'importe'   => floatval($row['importe']),
        'pagado'    => floatval($row['importe_pagado']),
        'saldo'     => floatval($row['saldo']),
        'estado'    => $row['estado']
    ];

    $unidades_map[$uf_id]['total_emitido'] += floatval($row['importe']);
    $unidades_map[$uf_id]['total_pagado']  += floatval($row['importe_pagado']);
    $unidades_map[$uf_id]['total_saldo']   += floatval($row['saldo']);

    $total_emitido  += floatval($row['importe']);
    $total_cobrado  += floatval($row['importe_pagado']);
    $total_pendiente += floatval($row['saldo']);
}

// Determinar estado general de cada unidad
foreach ($unidades_map as &$u) {
    if ($u['total_saldo'] <= 0) {
        $u['estado_general'] = 'pagada';
    } elseif ($u['total_pagado'] > 0) {
        $u['estado_general'] = 'parcial';
    } else {
        $u['estado_general'] = 'pendiente';
    }
}
unset($u);

echo json_encode([
    'status'   => 'ok',
    'unidades' => array_values($unidades_map),
    'totales'  => [
        'emitido'           => $total_emitido,
        'cobrado'           => $total_cobrado,
        'pendiente'         => $total_pendiente,
        'cantidad_unidades' => count($unidades_map)
    ]
]);

exit;
?>