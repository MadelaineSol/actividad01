<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../database.php';

$con = Database::getCon();

$id     = isset($_POST['id']) ? trim($_POST['id']) : '';
$accion = isset($_POST['accion']) ? trim($_POST['accion']) : 'anular';

if ($id === '' || !is_numeric($id)) {
    echo json_encode(['status' => 'error', 'message' => 'ID invalido.']);
    exit;
}

$id = mysqli_real_escape_string($con, $id);

if ($accion === 'eliminar') {
    $sql = "DELETE FROM `gastos` WHERE `id` = '$id'";
} else {
    $sql = "UPDATE `gastos` SET `estado` = 'anulado' WHERE `id` = '$id'";
}

$query = mysqli_query($con, $sql);

if ($query) {
    echo json_encode([
        'status'  => 'ok',
        'message' => $accion === 'eliminar' ? 'Gasto eliminado.' : 'Gasto anulado.'
    ]);
} else {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Error: ' . mysqli_error($con)
    ]);
}

exit;
?>