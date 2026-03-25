<?php
header('Content-Type: text/html; charset=utf-8');

echo "<h2>Test PHPMailer con SMTP</h2><pre>";

// 1. Verificar que los archivos existen
echo "=== ARCHIVOS ===\n";
$files = [
    'smtp_config.php'        => __DIR__ . '/../smtp_config.php',
    'phpmailer/PHPMailer.php' => __DIR__ . '/../phpmailer/PHPMailer.php',
    'phpmailer/SMTP.php'      => __DIR__ . '/../phpmailer/SMTP.php',
    'phpmailer/Exception.php' => __DIR__ . '/../phpmailer/Exception.php',
];

$todo_ok = true;
foreach ($files as $nombre => $ruta) {
    $existe = file_exists($ruta);
    echo "$nombre: " . ($existe ? 'OK' : 'NO ENCONTRADO') . "\n";
    if (!$existe) $todo_ok = false;
}

if (!$todo_ok) {
    echo "\nFALTAN ARCHIVOS. Ejecuta primero ?action=instalar_phpmailer\n";
    echo "</pre>";
    exit;
}

echo "\n";

// 2. Cargar config
require_once __DIR__ . '/../smtp_config.php';

echo "=== CONFIGURACION ===\n";
echo "SMTP_HOST: " . SMTP_HOST . "\n";
echo "SMTP_PORT: " . SMTP_PORT . "\n";
echo "SMTP_SECURE: " . SMTP_SECURE . "\n";
echo "SMTP_USER: " . SMTP_USER . "\n";
echo "SMTP_PASS: " . str_repeat('*', max(0, strlen(SMTP_PASS) - 4)) . substr(SMTP_PASS, -4) . " (" . strlen(SMTP_PASS) . " chars)\n";
echo "SMTP_FROM_EMAIL: " . SMTP_FROM_EMAIL . "\n";
echo "SMTP_FROM_NAME: " . SMTP_FROM_NAME . "\n\n";

// 3. Cargar PHPMailer
require_once __DIR__ . '/../phpmailer/Exception.php';
require_once __DIR__ . '/../phpmailer/PHPMailer.php';
require_once __DIR__ . '/../phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 4. Enviar test
$destino = 'scareaga629@gmail.com';

echo "=== ENVIANDO TEST A: $destino ===\n";
echo "(Mostrando debug SMTP completo...)\n\n";

$mail = new PHPMailer(true);

try {
    // Debug SMTP completo
    $mail->SMTPDebug = 3; // 0=off, 1=client, 2=client+server, 3=todo
    $mail->Debugoutput = function($str, $level) {
        echo htmlspecialchars($str);
    };

    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = SMTP_SECURE;
    $mail->Port       = SMTP_PORT;
    $mail->CharSet    = 'UTF-8';
    $mail->Timeout    = 15;

    // Si SSL falla, intentar sin verificar certificado
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        ],
    ];

    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    $mail->addAddress($destino, 'Test GesCon');

    $mail->isHTML(true);
    $mail->Subject = 'Test PHPMailer GesCon - ' . date('H:i:s');
    $mail->Body    = '<h1 style="color:green">Funciona!</h1><p>Email de prueba enviado a las ' . date('H:i:s d/m/Y') . ' desde PHPMailer con SMTP.</p>';
    $mail->AltBody = 'Test PHPMailer GesCon - ' . date('H:i:s d/m/Y');

    echo "\n--- Intentando enviar ---\n\n";

    $mail->send();

    echo "\n\n=== RESULTADO: ENVIADO CORRECTAMENTE ===\n";
    echo "El email deberia llegar a $destino en 1-2 minutos.\n";
    echo "Revisa bandeja de entrada y spam.\n";

} catch (Exception $e) {
    echo "\n\n=== RESULTADO: ERROR ===\n";
    echo "Error: " . $mail->ErrorInfo . "\n";
    echo "Excepcion: " . $e->getMessage() . "\n\n";

    echo "=== POSIBLES SOLUCIONES ===\n";

    if (strpos($mail->ErrorInfo, 'Could not connect') !== false || strpos($mail->ErrorInfo, 'Connection refused') !== false) {
        echo "- El servidor SMTP no responde en " . SMTP_HOST . ":" . SMTP_PORT . "\n";
        echo "- Proba cambiar el puerto:\n";
        echo "  Puerto 465 con ssl\n";
        echo "  Puerto 587 con tls\n";
        echo "  Puerto 25 sin encriptacion\n";
        echo "- Verifica que el host sea correcto (proba tambien 'localhost' o la IP del servidor)\n";
    }

    if (strpos($mail->ErrorInfo, 'Authentication') !== false || strpos($mail->ErrorInfo, 'credentials') !== false) {
        echo "- Usuario o contraseña incorrectos\n";
        echo "- Verifica que la cuenta " . SMTP_USER . " exista en cPanel\n";
        echo "- Verifica que la contraseña sea correcta\n";
    }

    if (strpos($mail->ErrorInfo, 'certificate') !== false || strpos($mail->ErrorInfo, 'SSL') !== false) {
        echo "- Problema con el certificado SSL del servidor\n";
        echo "- Ya se esta usando allow_self_signed, pero proba con puerto 587/tls o 25/sin ssl\n";
    }
}

echo "</pre>";
echo "<p><strong>Copia TODO esto (incluyendo el debug largo) y pegamelo.</strong></p>";
?>