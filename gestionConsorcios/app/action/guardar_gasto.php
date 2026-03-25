<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../database.php';

$con = Database::getCon();

// --- Recibir campos ---
$id_empresa   = isset($_POST['id_empresa_administradora']) ? trim($_POST['id_empresa_administradora']) : '';
$periodo      = isset($_POST['periodo'])        ? trim($_POST['periodo'])        : '';
$categoria    = isset($_POST['rubro'])           ? trim($_POST['rubro'])           : '';
$concepto     = isset($_POST['descripcion'])     ? trim($_POST['descripcion'])     : '';
$monto        = isset($_POST['monto'])           ? trim($_POST['monto'])           : '';
$fecha        = isset($_POST['fecha_gasto'])     ? trim($_POST['fecha_gasto'])     : '';
$proveedor    = isset($_POST['proveedor'])       ? trim($_POST['proveedor'])       : '';
$comp_numero  = isset($_POST['comprobante_nro']) ? trim($_POST['comprobante_nro']) : '';
$estado       = isset($_POST['estado'])          ? trim($_POST['estado'])          : 'pendiente';
$observaciones= isset($_POST['observaciones'])   ? trim($_POST['observaciones'])   : '';

// Fallback 1: sesion
if ($id_empresa === '' && isset($_SESSION['contexto_id']) && $_SESSION['contexto_id'] !== '') {
    $id_empresa = trim($_SESSION['contexto_id']);
}

// Fallback 2: buscar empresa existente
if ($id_empresa === '') {
    $qf = mysqli_query($con, "SELECT DISTINCT `id_empresa_administradora` FROM `gastos` LIMIT 1");
    if ($qf && $rf = mysqli_fetch_assoc($qf)) {
        $id_empresa = $rf['id_empresa_administradora'];
    }
}

if ($periodo === '') {
    $periodo = date('Y-m');
}

// Validar
$faltantes = [];
if ($id_empresa === '') $faltantes[] = 'empresa';
if ($categoria === '')  $faltantes[] = 'rubro';
if ($concepto === '')   $faltantes[] = 'descripcion';
if ($monto === '')      $faltantes[] = 'monto';

if (count($faltantes) > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Falta completar: ' . implode(', ', $faltantes) . '.']);
    exit;
}

$monto = str_replace(',', '.', $monto);
if (!is_numeric($monto) || floatval($monto) <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'El monto debe ser un numero mayor a cero.']);
    exit;
}

$hash = md5(uniqid($id_empresa . $periodo . $monto, true));

$created_by = 'NULL';
if (isset($_SESSION['user_data'])) {
    $ud = $_SESSION['user_data'];
    if (is_object($ud) && isset($ud->id)) $created_by = intval($ud->id);
    elseif (is_array($ud) && isset($ud['id'])) $created_by = intval($ud['id']);
}

$esc = function($v) use ($con) { return mysqli_real_escape_string($con, $v); };

$sql = "INSERT INTO `gastos` (
    `id_empresa_administradora`, `proveedor_id`, `periodo`, `fecha`, `categoria`,
    `concepto`, `monto`, `estado`, `comprobante_tipo`, `comprobante_numero`,
    `archivo_comprobante`, `observaciones`, `hash`, `created_by`
) VALUES (
    '".$esc($id_empresa)."', NULL, '".$esc($periodo)."', '".$esc($fecha)."', '".$esc($categoria)."',
    '".$esc($concepto)."', '".$esc($monto)."', '".$esc($estado)."', NULL,
    ".($comp_numero !== '' ? "'".$esc($comp_numero)."'" : "NULL").",
    NULL,
    ".($observaciones !== '' ? "'".$esc($observaciones)."'" : "NULL").",
    '".$esc($hash)."', ".$created_by."
)";

$query = mysqli_query($con, $sql);

if ($query) {
    echo json_encode(['status' => 'ok', 'message' => 'Gasto registrado correctamente.', 'id' => mysqli_insert_id($con)]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . mysqli_error($con)]);
}

exit;
?>