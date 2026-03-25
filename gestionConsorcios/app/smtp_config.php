<?php
// =============================================
// Configuracion SMTP - Mail del hosting
// Ubicar en: app/smtp_config.php
//
// IMPORTANTE: Tenes que tener una cuenta de email
// creada en cPanel de luci.com.ar
// (cPanel → Cuentas de correo electronico → Crear)
// =============================================

define('SMTP_HOST', 'mail.luci.com.ar');       // Servidor de mail del hosting
define('SMTP_PORT', 587);     
define('SMTP_SECURE', 'tls');                  // 465 (SSL) o 587 (TLS) — probar con 465 primero
// define('SMTP_SECURE', 'ssl');                   // 'ssl' para puerto 465, 'tls' para puerto 587
define('SMTP_USER', 'gescon@luci.com.ar');      // La cuenta de email que creaste en cPanel
define('SMTP_PASS', 'vduc fbdv ozpg mzyv');      // La contraseña de esa cuenta
define('SMTP_FROM_EMAIL', 'gescon@luci.com.ar');// Tiene que ser la misma que SMTP_USER
define('SMTP_FROM_NAME', 'GesCon - Administracion');
?>