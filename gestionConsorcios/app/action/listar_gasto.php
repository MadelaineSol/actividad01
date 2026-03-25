<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../database.php';

try {
    $con = Database::getCon();
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'No se pudo conectar a la base de datos.']);
    exit;
}

$id_empresa = isset($_POST['id_empresa_administradora']) ? trim($_POST['id_empresa_administradora']) : '';
$periodo    = isset($_POST['periodo']) ? trim($_POST['periodo']) : '';

// Fallback a sesion si viene vacio
if ($id_empresa === '' && isset($_SESSION['contexto_id'])) {
    $id_empresa = trim($_SESSION['contexto_id']);
}

if ($id_empresa === '') {
    echo json_encode(['status' => 'error', 'message' => 'Falta contexto de empresa.']);
    exit;
}

$id_empresa = mysqli_real_escape_string($con, $id_empresa);
$periodo    = mysqli_real_escape_string($con, $periodo);

// --- Gastos del periodo (tabla: gastos) ---
$sql = "SELECT
            `id`,
            `periodo`,
            `categoria`          AS rubro,
            `concepto`           AS descripcion,
            `monto`,
            `fecha`              AS fecha_gasto,
            `proveedor_id`,
            `comprobante_numero` AS comprobante_nro,
            `estado`,
            `observaciones`,
            `created_at`
        FROM `gastos`
        WHERE `id_empresa_administradora` = '$id_empresa'";

if ($periodo !== '') {
    $sql .= " AND `periodo` = '$periodo'";
}

$sql .= " ORDER BY `fecha` DESC, `id` DESC";

$query = mysqli_query($con, $sql);

if (!$query) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error en consulta: ' . mysqli_error($con)
    ]);
    exit;
}

$gastos = [];
$total  = 0;

while ($row = mysqli_fetch_assoc($query)) {
    // Intentar traer nombre del proveedor si existe la tabla
    $row['proveedor'] = '';
    if (!empty($row['proveedor_id'])) {
        $pid = mysqli_real_escape_string($con, $row['proveedor_id']);
        $qp = @mysqli_query($con, "SELECT `razon_social` FROM `proveedores` WHERE `id` = '$pid' LIMIT 1");
        if ($qp && $rp = mysqli_fetch_assoc($qp)) {
            $row['proveedor'] = $rp['razon_social'];
        }
    }

    $gastos[] = $row;
    if ($row['estado'] !== 'anulado') {
        $total += floatval($row['monto']);
    }
}

// --- Totales por rubro (categoria) ---
$sql2 = "SELECT `categoria` AS rubro, COUNT(*) AS cantidad, SUM(`monto`) AS total
         FROM `gastos`
         WHERE `id_empresa_administradora` = '$id_empresa'
           AND `estado` != 'anulado'";

if ($periodo !== '') {
    $sql2 .= " AND `periodo` = '$periodo'";
}

$sql2 .= " GROUP BY `categoria` ORDER BY total DESC";

$query2 = mysqli_query($con, $sql2);

$rubros = [];
if ($query2) {
    while ($r = mysqli_fetch_assoc($query2)) {
        $rubros[] = $r;
    }
}

echo json_encode([
    'status'   => 'ok',
    'gastos'   => $gastos,
    'rubros'   => $rubros,
    'total'    => $total,
    'cantidad' => count($gastos)
]);

exit;
?>