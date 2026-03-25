<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../database.php';

$tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';

if ($tipo === '') {
    echo json_encode([]);
    exit;
}

$con = Database::getCon();

$data = [];

if ($tipo === 'edificio') {
    $sql = "SELECT id, nombre FROM barrios WHERE tipo = 'edificio' ORDER BY nombre ASC";
} elseif ($tipo === 'barrio') {
    $sql = "SELECT id, nombre FROM barrios WHERE tipo = 'barrio cerrado' ORDER BY nombre ASC";
} else {
    echo json_encode([]);
    exit;
}

$query = mysqli_query($con, $sql);

if ($query) {
    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = [
            'id' => $row['id'],
            'nombre' => $row['nombre']
        ];
    }
}

echo json_encode($data);
exit;
?>