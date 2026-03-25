<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$tipo_contexto = isset($_POST['tipo_contexto']) ? trim($_POST['tipo_contexto']) : '';
$contexto_id = isset($_POST['contexto_id']) ? trim($_POST['contexto_id']) : '';
$observacion_contexto = isset($_POST['observacion_contexto']) ? trim($_POST['observacion_contexto']) : '';

if ($tipo_contexto !== '' && $contexto_id !== '') {
    $_SESSION['tipo_contexto'] = $tipo_contexto;
    $_SESSION['contexto_id'] = $contexto_id;
    $_SESSION['observacion_contexto'] = $observacion_contexto;

    redir("?view=index");
    exit;
}

redir("?view=selector_contexto");
exit;
?>