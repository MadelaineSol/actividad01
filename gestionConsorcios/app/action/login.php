<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../control.php';
require_once __DIR__ . '/../database.php';// Configuraciones
*/

if(isset($_REQUEST['usuario']) AND isset($_REQUEST['password'])) {

    
  //  echo "<pre>"; print_r($control); echo "</pre>"; exit();
     load_model('LoginData');
    
    
     $loginData = new LoginData;
     
    
     
     $log = $loginData->loguear($_REQUEST);
  if($log){
    
    
      // echo "<pre>"; print_r($log); echo "</pre>";
   $_SESSION['user_data'] = $log;
           
   //var_dump("holaaa2");
   redir("?view=selector_contexto");
    
  } else{

      
   // var_dump("no encontro el usuario");
    redir("?view=login");
    
  } 

   
  
  
  }  else if(isset($_REQUEST['logout'])) {

      var_dump("entro al cuarto if");
    
//    echo "<pre>"; print_r("helou"); echo "</pre>"; 
session_unset();
session_destroy();

redir("?view=login");
    
  } 
      var_dump("entro al final");
?>