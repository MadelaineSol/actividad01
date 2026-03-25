<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/../database.php';

echo "<h2>Diagnostico de gastos</h2>";
echo "<pre>";

// 1. Sesion
echo "=== SESION ===\n";
echo "contexto_id: [" . (isset($_SESSION['contexto_id']) ? $_SESSION['contexto_id'] : 'NO EXISTE') . "]\n";
echo "user_data tipo: " . gettype($_SESSION['user_data'] ?? null) . "\n";
echo "\n";

// 2. Conexion
echo "=== CONEXION ===\n";
try {
    $con = Database::getCon();
    echo "Conexion: OK\n";
} catch (Exception $e) {
    echo "Conexion: FALLO - " . $e->getMessage() . "\n";
    echo "</pre>";
    exit;
}
echo "\n";

// 3. Tabla gastos existe?
echo "=== TABLA GASTOS ===\n";
$check = mysqli_query($con, "SHOW TABLES LIKE 'gastos'");
echo "Tabla 'gastos' existe: " . (mysqli_num_rows($check) > 0 ? 'SI' : 'NO') . "\n";
echo "\n";

// 4. Cuantos registros hay en total?
echo "=== REGISTROS TOTALES ===\n";
$q = mysqli_query($con, "SELECT COUNT(*) as total FROM gastos");
$r = mysqli_fetch_assoc($q);
echo "Total registros en gastos: " . $r['total'] . "\n";
echo "\n";

// 5. Mostrar los ultimos 5 registros
echo "=== ULTIMOS 5 GASTOS ===\n";
$q = mysqli_query($con, "SELECT id, id_empresa_administradora, periodo, categoria, concepto, monto, estado, fecha FROM gastos ORDER BY id DESC LIMIT 5");
if ($q) {
    while ($row = mysqli_fetch_assoc($q)) {
        echo "ID:" . $row['id'] 
             . " | empresa:" . $row['id_empresa_administradora'] 
             . " | periodo:" . $row['periodo'] 
             . " | cat:" . $row['categoria'] 
             . " | concepto:" . $row['concepto'] 
             . " | monto:" . $row['monto'] 
             . " | estado:" . $row['estado'] 
             . " | fecha:" . $row['fecha'] 
             . "\n";
    }
} else {
    echo "Error query: " . mysqli_error($con) . "\n";
}
echo "\n";

// 6. Filtro que haria la vista
$contexto = isset($_SESSION['contexto_id']) ? mysqli_real_escape_string($con, $_SESSION['contexto_id']) : '';
$periodo = date('Y-m');
echo "=== FILTRO DE LA VISTA ===\n";
echo "WHERE id_empresa_administradora = '$contexto' AND periodo = '$periodo'\n";

$q2 = mysqli_query($con, "SELECT COUNT(*) as total FROM gastos WHERE id_empresa_administradora = '$contexto' AND periodo = '$periodo'");
if ($q2) {
    $r2 = mysqli_fetch_assoc($q2);
    echo "Resultados con ese filtro: " . $r2['total'] . "\n";
} else {
    echo "Error: " . mysqli_error($con) . "\n";
}
echo "\n";

// 7. Probar sin filtro de periodo
$q3 = mysqli_query($con, "SELECT COUNT(*) as total FROM gastos WHERE id_empresa_administradora = '$contexto'");
if ($q3) {
    $r3 = mysqli_fetch_assoc($q3);
    echo "Resultados SOLO con empresa (sin periodo): " . $r3['total'] . "\n";
}
echo "\n";

// 8. Valores distintos de periodo en la tabla
echo "=== PERIODOS EN LA TABLA ===\n";
$q4 = mysqli_query($con, "SELECT DISTINCT periodo FROM gastos ORDER BY periodo DESC LIMIT 10");
if ($q4) {
    while ($row = mysqli_fetch_assoc($q4)) {
        echo "  -> [" . $row['periodo'] . "]\n";
    }
}
echo "\n";

// 9. Valores distintos de id_empresa_administradora
echo "=== EMPRESAS EN LA TABLA ===\n";
$q5 = mysqli_query($con, "SELECT DISTINCT id_empresa_administradora FROM gastos");
if ($q5) {
    while ($row = mysqli_fetch_assoc($q5)) {
        echo "  -> [" . $row['id_empresa_administradora'] . "]\n";
    }
}

// 10. URL
echo "\n=== URL CONFIG ===\n";
require_once __DIR__ . '/../config.php';
echo "URL definida: [" . URL . "]\n";
echo "URL con barra: [" . rtrim(URL, '/') . "/]\n";

echo "</pre>";
echo "<p><strong>Copia todo esto y pegamelo en el chat.</strong></p>";
?>