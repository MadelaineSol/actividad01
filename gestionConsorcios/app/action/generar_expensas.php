<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../database.php';

$con = Database::getCon();

// --- Recibir parametros ---
$id_empresa = isset($_POST['id_empresa_administradora']) ? trim($_POST['id_empresa_administradora']) : '';
$periodo    = isset($_POST['periodo']) ? trim($_POST['periodo']) : '';
$vencimiento_dia = isset($_POST['dia_vencimiento']) ? intval($_POST['dia_vencimiento']) : 10;

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

// --- 1. Verificar que no se hayan generado ya ---
$qCheck = mysqli_query($con, 
    "SELECT COUNT(*) as total FROM `cobranzas` 
     WHERE `id_empresa_administradora` = '$id_empresa_e' 
       AND `periodo` = '$periodo_e' 
       AND `concepto` IN ('Expensas', 'Deuda anterior')"
);
$rCheck = mysqli_fetch_assoc($qCheck);
if ($rCheck && intval($rCheck['total']) > 0) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Ya se generaron ' . $rCheck['total'] . ' registros para el periodo ' . $periodo . '. Si queres regenerar, primero elimina las cobranzas existentes de este periodo.'
    ]);
    exit;
}

// --- 2. Obtener total de gastos del periodo ---
$qGastos = mysqli_query($con,
    "SELECT COALESCE(SUM(`monto`), 0) as total, COUNT(*) as cantidad
     FROM `gastos`
     WHERE `id_empresa_administradora` = '$id_empresa_e'
       AND `periodo` = '$periodo_e'
       AND `estado` != 'anulado'"
);
$rGastos = mysqli_fetch_assoc($qGastos);
$totalGastos = floatval($rGastos['total']);
$cantGastos  = intval($rGastos['cantidad']);

if ($totalGastos <= 0) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'No hay gastos cargados (o todos estan anulados) para el periodo ' . $periodo . '. Carga gastos primero.'
    ]);
    exit;
}

// --- 3. Obtener unidades funcionales activas con coeficiente > 0 ---
$qUF = mysqli_query($con,
    "SELECT `id`, `tipo_contexto`, `contexto_id`, `nombre`, `codigo`,
            `coeficiente_expensas`, `propietario_id`, `inquilino_id`, `estado_ocupacion`
     FROM `unidades_funcionales`
     WHERE `id_empresa_administradora` = '$id_empresa_e'
       AND `estado_administrativo` = 'activa'
       AND `coeficiente_expensas` > 0
     ORDER BY `id` ASC"
);

if (!$qUF || mysqli_num_rows($qUF) === 0) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'No hay unidades funcionales activas con coeficiente de expensas mayor a 0.'
    ]);
    exit;
}

$unidades = [];
$sumaCoeficientes = 0;

while ($uf = mysqli_fetch_assoc($qUF)) {
    $coef = floatval($uf['coeficiente_expensas']);
    $sumaCoeficientes += $coef;
    $unidades[] = $uf;
}

if ($sumaCoeficientes <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'La suma de coeficientes es 0. Revisa los coeficientes.']);
    exit;
}

// --- 4. Calcular fechas ---
$partes = explode('-', $periodo);
$anio = intval($partes[0]);
$mes  = intval($partes[1]);

$fecha_emision = date('Y-m-d');
$fecha_vencimiento = sprintf('%04d-%02d-%02d', $anio, $mes, min($vencimiento_dia, 28));

$meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
$nombreMes = isset($meses[$mes]) ? $meses[$mes] : $mes;
$detalle = 'Expensas ' . $nombreMes . ' ' . $anio;

// --- 5. Generar expensas + arrastre de deuda ---
$generadas = 0;
$arrastres = 0;
$errores   = 0;
$detalleGeneracion = [];

foreach ($unidades as $uf) {
    $coef = floatval($uf['coeficiente_expensas']);
    $importe = round(($totalGastos * $coef) / $sumaCoeficientes, 2);

    if ($importe <= 0) continue;

    // Determinar persona responsable
    $persona_id = 0;
    $tipo_persona = 'propietario';

    if (!empty($uf['propietario_id']) && intval($uf['propietario_id']) > 0) {
        $persona_id = intval($uf['propietario_id']);
        $tipo_persona = 'propietario';
    } elseif (!empty($uf['inquilino_id']) && intval($uf['inquilino_id']) > 0) {
        $persona_id = intval($uf['inquilino_id']);
        $tipo_persona = 'inquilino';
    }

    $uf_id_e = $esc($uf['id']);
    $ctx_tipo_e = $esc($uf['tipo_contexto']);
    $ctx_id_e = $esc($uf['contexto_id']);
    $nombre_unidad = $uf['nombre'] ?: $uf['codigo'];

    // =====================================================
    // A) INSERTAR EXPENSA DEL MES (por coeficiente)
    // =====================================================
    $sql = "INSERT INTO `cobranzas` (
        `id_empresa_administradora`, `contexto_tipo`, `contexto_id`,
        `unidad_funcional_id`, `persona_id`, `tipo_persona`,
        `periodo`, `fecha_emision`, `fecha_vencimiento`,
        `concepto`, `detalle`, `importe`, `importe_pagado`, `saldo`,
        `estado`, `observaciones`
    ) VALUES (
        '$id_empresa_e', '$ctx_tipo_e', '$ctx_id_e',
        '$uf_id_e', '$persona_id', '".$esc($tipo_persona)."',
        '$periodo_e', '".$esc($fecha_emision)."', '".$esc($fecha_vencimiento)."',
        'Expensas', '".$esc($detalle)."',
        '$importe', '0.00', '$importe',
        'pendiente',
        'Generada automaticamente. Coef: $coef / Total gastos: \$$totalGastos'
    )";

    $ok = mysqli_query($con, $sql);
    if ($ok) {
        $generadas++;
    } else {
        $errores++;
    }

    // =====================================================
    // B) ARRASTRE DE DEUDA ANTERIOR
    //    Sumar saldo > 0 de periodos anteriores al actual
    // =====================================================
    $qDeuda = mysqli_query($con,
        "SELECT COALESCE(SUM(`saldo`), 0) as deuda_total
         FROM `cobranzas`
         WHERE `id_empresa_administradora` = '$id_empresa_e'
           AND `unidad_funcional_id` = '$uf_id_e'
           AND `periodo` < '$periodo_e'
           AND `saldo` > 0
           AND `estado` NOT IN ('pagada', 'anulada')"
    );

    $deudaAnterior = 0;
    if ($qDeuda && $rDeuda = mysqli_fetch_assoc($qDeuda)) {
        $deudaAnterior = floatval($rDeuda['deuda_total']);
    }

    $infoUnidad = [
        'unidad'    => $nombre_unidad,
        'coef'      => $coef,
        'expensa'   => $importe,
        'deuda_ant' => $deudaAnterior,
        'total'     => $importe + $deudaAnterior
    ];

    if ($deudaAnterior > 0) {
        $sqlDeuda = "INSERT INTO `cobranzas` (
            `id_empresa_administradora`, `contexto_tipo`, `contexto_id`,
            `unidad_funcional_id`, `persona_id`, `tipo_persona`,
            `periodo`, `fecha_emision`, `fecha_vencimiento`,
            `concepto`, `detalle`, `importe`, `importe_pagado`, `saldo`,
            `estado`, `observaciones`
        ) VALUES (
            '$id_empresa_e', '$ctx_tipo_e', '$ctx_id_e',
            '$uf_id_e', '$persona_id', '".$esc($tipo_persona)."',
            '$periodo_e', '".$esc($fecha_emision)."', '".$esc($fecha_vencimiento)."',
            'Deuda anterior',
            'Saldo pendiente de periodos anteriores',
            '$deudaAnterior', '0.00', '$deudaAnterior',
            'pendiente',
            'Arrastre automatico de deuda impaga de periodos anteriores a $periodo'
        )";

        $okD = mysqli_query($con, $sqlDeuda);
        if ($okD) {
            $arrastres++;
        } else {
            $errores++;
        }
    }

    $detalleGeneracion[] = $infoUnidad;
}

// --- 6. Respuesta ---
if ($generadas > 0) {
    $mensajeBase = 'Se generaron ' . $generadas . ' expensas para ' . $detalle . '.';
    if ($arrastres > 0) {
        $mensajeBase .= ' Ademas se arrastraron ' . $arrastres . ' deudas de periodos anteriores.';
    }

    echo json_encode([
        'status'    => 'ok',
        'message'   => $mensajeBase,
        'resumen'   => [
            'periodo'           => $periodo,
            'total_gastos'      => $totalGastos,
            'cantidad_gastos'   => $cantGastos,
            'expensas_generadas'=> $generadas,
            'deudas_arrastradas'=> $arrastres,
            'errores'           => $errores,
            'suma_coeficientes' => $sumaCoeficientes,
            'fecha_vencimiento' => $fecha_vencimiento
        ],
        'detalle'   => $detalleGeneracion
    ]);
} else {
    echo json_encode([
        'status'  => 'error',
        'message' => 'No se pudo generar ninguna expensa. Errores: ' . $errores
    ]);
}

exit;
?>