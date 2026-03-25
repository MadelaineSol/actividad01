<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../database.php';

$con = Database::getCon();

$tipo_contexto = isset($_POST['tipo_contexto']) ? trim($_POST['tipo_contexto']) : '';
$contexto_id = isset($_POST['contexto_id']) ? trim($_POST['contexto_id']) : '';
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$torre = isset($_POST['torre']) ? trim($_POST['torre']) : '';
$bloque = isset($_POST['bloque']) ? trim($_POST['bloque']) : '';
$piso = isset($_POST['piso']) ? trim($_POST['piso']) : '';
$departamento = isset($_POST['departamento']) ? trim($_POST['departamento']) : '';
$lote = isset($_POST['lote']) ? trim($_POST['lote']) : '';
$manzana = isset($_POST['manzana']) ? trim($_POST['manzana']) : '';
$tipo_unidad = isset($_POST['tipo_unidad']) ? trim($_POST['tipo_unidad']) : '';
$superficie = isset($_POST['superficie']) ? trim($_POST['superficie']) : '';
$coeficiente_expensas = isset($_POST['coeficiente_expensas']) ? trim($_POST['coeficiente_expensas']) : '';
$estado_ocupacion = isset($_POST['estado_ocupacion']) ? trim($_POST['estado_ocupacion']) : '';
$estado_administrativo = isset($_POST['estado_administrativo']) ? trim($_POST['estado_administrativo']) : '';
$propietario_id = isset($_POST['propietario_id']) ? trim($_POST['propietario_id']) : '';
$inquilino_id = isset($_POST['inquilino_id']) ? trim($_POST['inquilino_id']) : '';
// var_dump($inquilino_id);
// exit();
$medidor_luz = isset($_POST['medidor_luz']) ? trim($_POST['medidor_luz']) : '';
$medidor_agua = isset($_POST['medidor_agua']) ? trim($_POST['medidor_agua']) : '';
$medidor_gas = isset($_POST['medidor_gas']) ? trim($_POST['medidor_gas']) : '';
$observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';
$id_empresa_administradora = isset($_POST['id_empresa_administradora']) ? trim($_POST['id_empresa_administradora']) : '';

if ($tipo_contexto === '' || $contexto_id === '' || $nombre === '' || $tipo_unidad === '' || $estado_ocupacion === '' || $estado_administrativo === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Completá los campos obligatorios.'
    ]);
    exit;
}

$codigo = '';
if ($torre !== '' || $piso !== '' || $departamento !== '') {
    $codigo = trim($torre . '-' . $piso . '-' . $departamento, '-');
} elseif ($manzana !== '' || $lote !== '') {
    $codigo = trim($manzana . '-' . $lote, '-');
} else {
    $codigo = 'UF-' . time();
}

$sql = "INSERT INTO unidades_funcionales (
    tipo_contexto,
    contexto_id,
    codigo,
    nombre,
    torre,
    bloque,
    piso,
    departamento,
    lote,
    manzana,
    tipo_unidad,
    superficie,
    coeficiente_expensas,
    estado_ocupacion,
    estado_administrativo,
    propietario_id,
    inquilino_id,
    medidor_luz,
    medidor_agua,
    medidor_gas,
    observaciones,
    id_empresa_administradora
) VALUES (
    '".mysqli_real_escape_string($con, $tipo_contexto)."',
    '".mysqli_real_escape_string($con, $contexto_id)."',
    '".mysqli_real_escape_string($con, $codigo)."',
    '".mysqli_real_escape_string($con, $nombre)."',
    '".mysqli_real_escape_string($con, $torre)."',
    '".mysqli_real_escape_string($con, $bloque)."',
    '".mysqli_real_escape_string($con, $piso)."',
    '".mysqli_real_escape_string($con, $departamento)."',
    '".mysqli_real_escape_string($con, $lote)."',
    '".mysqli_real_escape_string($con, $manzana)."',
    '".mysqli_real_escape_string($con, $tipo_unidad)."',
    '".mysqli_real_escape_string($con, $superficie)."',
    '".mysqli_real_escape_string($con, $coeficiente_expensas)."',
    '".mysqli_real_escape_string($con, $estado_ocupacion)."',
    '".mysqli_real_escape_string($con, $estado_administrativo)."',
    '".mysqli_real_escape_string($con, $propietario_id)."',
    '".mysqli_real_escape_string($con, $inquilino_id)."',
    '".mysqli_real_escape_string($con, $medidor_luz)."',
    '".mysqli_real_escape_string($con, $medidor_agua)."',
    '".mysqli_real_escape_string($con, $medidor_gas)."',
    '".mysqli_real_escape_string($con, $observaciones)."',
    '".mysqli_real_escape_string($con, $id_empresa_administradora)."'
)";

$query = mysqli_query($con, $sql);

if ($query) {
    echo json_encode([
        'status' => 'ok',
        'message' => 'Unidad funcional guardada correctamente.'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No se pudo guardar la unidad funcional.'
    ]);
}
exit;
?>