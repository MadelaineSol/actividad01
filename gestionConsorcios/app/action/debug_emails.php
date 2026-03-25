<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/../database.php';

$con = Database::getCon();

$id_empresa = '4'; // tu empresa
$periodo = '2026-03';

echo "<h2>Diagnostico de emails para expensas $periodo</h2>";
echo "<pre>";

// 1. Cobranzas del periodo
echo "=== COBRANZAS DEL PERIODO ===\n";
$q = mysqli_query($con, 
    "SELECT c.id, c.unidad_funcional_id, c.persona_id, c.tipo_persona, c.concepto, c.importe, c.saldo, c.estado
     FROM cobranzas c
     WHERE c.id_empresa_administradora = '$id_empresa' AND c.periodo = '$periodo'
     ORDER BY c.unidad_funcional_id, c.id"
);
if ($q) {
    $count = 0;
    while ($r = mysqli_fetch_assoc($q)) {
        echo "ID:" . $r['id'] 
             . " | UF:" . $r['unidad_funcional_id']
             . " | persona:" . $r['persona_id']
             . " | tipo:" . $r['tipo_persona']
             . " | concepto:" . $r['concepto']
             . " | importe:$" . $r['importe']
             . " | saldo:$" . $r['saldo']
             . " | estado:" . $r['estado']
             . "\n";
        $count++;
    }
    echo "Total registros: $count\n";
} else {
    echo "Error: " . mysqli_error($con) . "\n";
}

echo "\n";

// 2. Personas con sus emails
echo "=== PERSONAS VINCULADAS ===\n";
$q2 = mysqli_query($con,
    "SELECT DISTINCT c.persona_id, c.tipo_persona, c.unidad_funcional_id,
            p.nombre, p.apellido, p.email, p.telefono,
            uf.nombre AS unidad_nombre, uf.codigo AS unidad_codigo
     FROM cobranzas c
     LEFT JOIN personas p ON p.id = c.persona_id
     LEFT JOIN unidades_funcionales uf ON uf.id = c.unidad_funcional_id
     WHERE c.id_empresa_administradora = '$id_empresa' AND c.periodo = '$periodo'
     ORDER BY c.persona_id"
);

if ($q2) {
    $con_email = 0;
    $sin_email = 0;
    while ($r = mysqli_fetch_assoc($q2)) {
        $email = trim($r['email'] ?? '');
        $tiene = ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) ? 'SI' : 'NO';
        if ($tiene === 'SI') $con_email++; else $sin_email++;
        
        echo "persona_id:" . $r['persona_id']
             . " | " . ($r['nombre'] ?? '?') . " " . ($r['apellido'] ?? '')
             . " | email:[" . ($email ?: 'VACIO') . "]"
             . " | email_valido:" . $tiene
             . " | unidad:" . ($r['unidad_nombre'] ?: $r['unidad_codigo'])
             . " | tipo:" . $r['tipo_persona']
             . "\n";
    }
    echo "\nCon email valido: $con_email\n";
    echo "Sin email: $sin_email\n";
} else {
    echo "Error: " . mysqli_error($con) . "\n";
}

echo "\n";

// 3. Verificar que la tabla personas exista y tenga campo email
echo "=== ESTRUCTURA PERSONAS ===\n";
$q3 = mysqli_query($con, "SHOW COLUMNS FROM personas LIKE 'email'");
if ($q3 && mysqli_num_rows($q3) > 0) {
    $col = mysqli_fetch_assoc($q3);
    echo "Campo 'email' existe: SI | Tipo: " . $col['Type'] . "\n";
} else {
    echo "Campo 'email' existe: NO - ESTE ES EL PROBLEMA\n";
}

echo "\n";

// 4. Todos los emails de la tabla personas
echo "=== TODOS LOS EMAILS EN PERSONAS ===\n";
$q4 = mysqli_query($con, "SELECT id, nombre, apellido, email FROM personas WHERE id_empresa_administradora = '$id_empresa' ORDER BY id");
if ($q4) {
    while ($r = mysqli_fetch_assoc($q4)) {
        echo "ID:" . $r['id'] 
             . " | " . $r['nombre'] . " " . $r['apellido']
             . " | email:[" . (trim($r['email']) ?: 'VACIO') . "]"
             . "\n";
    }
} else {
    echo "Error: " . mysqli_error($con) . "\n";
}

// 5. Test de mail()
echo "\n=== TEST FUNCION mail() ===\n";
$testOk = @mail('test@test.com', 'Test', 'Test', "From: test@luci.com.ar\r\n");
echo "mail() funciona: " . ($testOk ? 'SI' : 'NO (puede estar bloqueado en el hosting)') . "\n";

echo "</pre>";
echo "<p><strong>Copiame todo esto en el chat.</strong></p>";
?>