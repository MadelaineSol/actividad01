<?php
header('Content-Type: text/html; charset=utf-8');

$dir = __DIR__ . '/../phpmailer/';

echo "<h2>Instalador de PHPMailer</h2><pre>";

if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
    echo "Carpeta creada: app/phpmailer/\n";
} else {
    echo "Carpeta ya existe: app/phpmailer/\n";
}

$base = 'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/';
$archivos = ['PHPMailer.php', 'SMTP.php', 'Exception.php'];

$ok = 0;
$err = 0;

foreach ($archivos as $archivo) {
    $url = $base . $archivo;
    $destino = $dir . $archivo;

    if (file_exists($destino)) {
        echo "[OK] $archivo ya existe (" . filesize($destino) . " bytes)\n";
        $ok++;
        continue;
    }

    echo "Descargando $archivo... ";
    $contenido = @file_get_contents($url);

    if ($contenido !== false && strlen($contenido) > 100) {
        file_put_contents($destino, $contenido);
        echo "OK (" . strlen($contenido) . " bytes)\n";
        $ok++;
    } else {
        echo "FALLO - intentando con cURL...\n";

        // Fallback con cURL
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $contenido = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($contenido !== false && strlen($contenido) > 100 && $httpCode == 200) {
                file_put_contents($destino, $contenido);
                echo "  OK con cURL (" . strlen($contenido) . " bytes)\n";
                $ok++;
            } else {
                echo "  FALLO tambien con cURL (HTTP $httpCode)\n";
                $err++;
            }
        } else {
            echo "  cURL no disponible\n";
            $err++;
        }
    }
}

echo "\n=== RESULTADO ===\n";
echo "Descargados: $ok / " . count($archivos) . "\n";
echo "Errores: $err\n\n";

// Verificar
echo "=== VERIFICACION ===\n";
foreach ($archivos as $archivo) {
    $ruta = $dir . $archivo;
    $existe = file_exists($ruta);
    echo "$archivo: " . ($existe ? 'OK (' . filesize($ruta) . ' bytes)' : 'NO ENCONTRADO') . "\n";
}

if ($ok === count($archivos)) {
    echo "\nPHPMailer instalado correctamente en app/phpmailer/\n";
    echo "Ya podes ejecutar ?action=test_phpmailer\n";
} else {
    echo "\nNO se pudieron descargar todos los archivos.\n";
    echo "El hosting puede tener bloqueado el acceso a GitHub.\n\n";
    echo "SOLUCION MANUAL:\n";
    echo "1. Descarga estos 3 archivos desde tu PC:\n";
    echo "   https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/PHPMailer.php\n";
    echo "   https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/SMTP.php\n";
    echo "   https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/Exception.php\n";
    echo "2. Subi los 3 a la carpeta: app/phpmailer/\n";
}

echo "</pre>";
?>