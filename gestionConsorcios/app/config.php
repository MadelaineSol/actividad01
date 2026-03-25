<?php
define('APP_NAME',"GesCon"); // Nombre de la app
define('APP', dirname(dirname(__FILE__))); // dirname
define('URL', 'https://luci.com.ar/gestionConsorcios'); // URL DEL root 

session_name(APP_NAME);
@session_start();

function incluirDir($dir){    //Incluye todos los ficheros de un directorio
   // echo $dir;
    if (file_exists($dir)) {
     foreach ( glob(  $dir.'*.php') as $filename)
{
  //  echo $filename;
  include_once $filename;

}
} else {
    echo "El fichero $nombre_fichero no existe"; exit();
}
   
}
