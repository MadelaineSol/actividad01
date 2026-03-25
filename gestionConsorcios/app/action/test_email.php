<?php
header('Content-Type: text/html; charset=utf-8');

$destino = 'scareaga629@gmail.com';

echo "<h2>Test de envio de email</h2><pre>";

// 1. Info del servidor
echo "=== SERVIDOR ===\n";
echo "PHP version: " . phpversion() . "\n";
echo "Server: " . ($_SERVER['SERVER_NAME'] ?? 'desconocido') . "\n";
echo "sendmail_path: " . ini_get('sendmail_path') . "\n";
echo "SMTP: " . ini_get('SMTP') . "\n";
echo "smtp_port: " . ini_get('smtp_port') . "\n";
echo "sendmail_from: " . ini_get('sendmail_from') . "\n\n";

// 2. Test basico con mail()
echo "=== TEST 1: mail() basico ===\n";
echo "Enviando a: $destino\n";

$asunto1 = 'Test GesCon - ' . date('H:i:s');
$cuerpo1 = 'Este es un test basico de mail() enviado a las ' . date('H:i:s d/m/Y');
$headers1 = "From: gescon@luci.com.ar\r\nContent-Type: text/plain; charset=UTF-8\r\n";

$result1 = mail($destino, $asunto1, $cuerpo1, $headers1);
echo "Resultado: " . ($result1 ? 'OK (mail() devolvio TRUE)' : 'FALLO (mail() devolvio FALSE)') . "\n";
echo "Error PHP (si hay): " . (error_get_last() ? error_get_last()['message'] : 'ninguno') . "\n\n";

// 3. Test con HTML
echo "=== TEST 2: mail() con HTML ===\n";

$asunto2 = 'Test HTML GesCon - ' . date('H:i:s');
$cuerpo2 = '<html><body><h1 style="color:green">Test GesCon</h1><p>Email HTML enviado a las ' . date('H:i:s d/m/Y') . '</p></body></html>';
$headers2  = "MIME-Version: 1.0\r\n";
$headers2 .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers2 .= "From: GesCon <gescon@luci.com.ar>\r\n";
$headers2 .= "Reply-To: gescon@luci.com.ar\r\n";

$result2 = mail($destino, $asunto2, $cuerpo2, $headers2);
echo "Resultado: " . ($result2 ? 'OK' : 'FALLO') . "\n\n";

// 4. Test con -f parameter
echo "=== TEST 3: mail() con parametro -f ===\n";

$asunto3 = 'Test -f GesCon - ' . date('H:i:s');
$cuerpo3 = 'Test con parametro -f a las ' . date('H:i:s');
$headers3 = "From: gescon@luci.com.ar\r\nContent-Type: text/plain; charset=UTF-8\r\n";

$result3 = mail($destino, $asunto3, $cuerpo3, $headers3, '-f gescon@luci.com.ar');
echo "Resultado: " . ($result3 ? 'OK' : 'FALLO') . "\n\n";

// 5. Test con el email del dominio del hosting
echo "=== TEST 4: usando From del dominio del hosting ===\n";

$asunto4 = 'Test hosting GesCon - ' . date('H:i:s');
$cuerpo4 = 'Test con From del dominio del hosting a las ' . date('H:i:s');
$headers4 = "From: solcareaga@luci.com.ar\r\nContent-Type: text/plain; charset=UTF-8\r\n";

$result4 = mail($destino, $asunto4, $cuerpo4, $headers4);
echo "Resultado: " . ($result4 ? 'OK' : 'FALLO') . "\n\n";

// Resumen
echo "=== RESUMEN ===\n";
echo "Test 1 (basico):    " . ($result1 ? 'OK' : 'FALLO') . "\n";
echo "Test 2 (HTML):      " . ($result2 ? 'OK' : 'FALLO') . "\n";
echo "Test 3 (con -f):    " . ($result3 ? 'OK' : 'FALLO') . "\n";
echo "Test 4 (hosting):   " . ($result4 ? 'OK' : 'FALLO') . "\n\n";

echo "IMPORTANTE:\n";
echo "- Si todos dicen OK pero no llega nada, revisa SPAM en Gmail\n";
echo "- Si no esta en spam, el hosting esta bloqueando o Gmail rechaza el dominio\n";
echo "- Espera 5 minutos antes de concluir que no llego\n";
echo "- Revisa tambien en Promociones y Actualizaciones de Gmail\n";

echo "</pre>";
echo "<p><strong>Copia esto y pegamelo. Despues espera 5 min y revisa todas las carpetas de Gmail.</strong></p>";
?>