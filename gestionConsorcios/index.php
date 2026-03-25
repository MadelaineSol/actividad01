<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
error_reporting(E_ALL ^ E_WARNING); // Error/Exception engine, always use E_ALL

ini_set('ignore_repeated_errors', TRUE); // always use TRUE

ini_set('display_errors', 1); // Error/Exception display, use FALSE only in production environment or real server. Use TRUE in development environment

//ini_set('log_errors', TRUE); // Error/Exception file logging engine.
//ini_set('error_log', 'error.log'); // Logging file path
require_once 'app/config.php'; // Configuraciones
require_once 'app/control.php';
require_once 'app/database.php';

$control = new Control;

if($_REQUEST['action']!= ""){
    
    
    
    $control->load_action($_REQUEST['action'],$datos);
}
else if($_REQUEST['view']!= ""){
    $control->load_view($_REQUEST['view'],$datos);
    
    
}
else{
    $control->load_view('index',$datos);
}


?>