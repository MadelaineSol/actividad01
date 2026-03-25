<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../database.php';

$con = Database::getCon();

$tipo_persona = isset($_POST['tipo_persona']) ? trim($_POST['tipo_persona']) : '';
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$apellido = isset($_POST['apellido']) ? trim($_POST['apellido']) : '';
$dni = isset($_POST['dni']) ? trim($_POST['dni']) : '';
$cuit = isset($_POST['cuit']) ? trim($_POST['cuit']) : '';
$telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : '';
$fecha_nacimiento = isset($_POST['fecha_nacimiento']) ? trim($_POST['fecha_nacimiento']) : '';
$estado = isset($_POST['estado']) ? trim($_POST['estado']) : '';
$observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';
$id_empresa_administradora = isset($_POST['id_empresa_administradora']) ? trim($_POST['id_empresa_administradora']) : '';

if ($tipo_persona === '' || $nombre === '' || $apellido === '' || $estado === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Completá los campos obligatorios.'
    ]);
    exit;
}

if ($id_empresa_administradora === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'No se encontró la empresa administradora.'
    ]);
    exit;
}

$sql = "INSERT INTO personas (
    tipo_persona,
    nombre,
    apellido,
    dni,
    cuit,
    telefono,
    email,
    direccion,
    fecha_nacimiento,
    estado,
    observaciones,
    id_empresa_administradora
) VALUES (
    '".mysqli_real_escape_string($con, $tipo_persona)."',
    '".mysqli_real_escape_string($con, $nombre)."',
    '".mysqli_real_escape_string($con, $apellido)."',
    '".mysqli_real_escape_string($con, $dni)."',
    '".mysqli_real_escape_string($con, $cuit)."',
    '".mysqli_real_escape_string($con, $telefono)."',
    '".mysqli_real_escape_string($con, $email)."',
    '".mysqli_real_escape_string($con, $direccion)."',
    '".mysqli_real_escape_string($con, $fecha_nacimiento)."',
    '".mysqli_real_escape_string($con, $estado)."',
    '".mysqli_real_escape_string($con, $observaciones)."',
    '".mysqli_real_escape_string($con, $id_empresa_administradora)."'
)";

$query = mysqli_query($con, $sql);

if ($query) {
    echo json_encode([
        'status' => 'ok',
        'message' => 'Persona guardada correctamente.'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No se pudo guardar la persona: ' . mysqli_error($con)
    ]);
}

exit;
?>