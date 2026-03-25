<?php



class Control
{
   

    public function load_view($view, $datos = [])
    {
        if (file_exists('app/views/' . $view . '-view.php') OR file_exists('app/views/ajax/' . $view . '.php'))
        {
            $_SESSION['vista_anterior'] = $_SESSION['vista_actual'];
            $_SESSION['vista_actual'] = $view;

            if ($view == 'login' ) {
                require_once 'app/views/login-view.php';
            }else if ($view == 'selector_contexto' ) {
                require_once 'app/views/selector_contexto-view.php';
            } else {
                require_once 'app/layout/layout.php';
            }

        } else
        {
          //   echo MODULO;
            require_once 'app/views/404-view.php';
            //die($view ." 404 NOT FOUND");
        }
    }
        public function load_action($action, $datos = [])
    {
        if (file_exists('app/action/' . $action . '.php'))
        {
            $_SESSION['action_anterior'] = $_SESSION['action_actual'];
            $_SESSION['action_actual'] = $action;
            require_once 'app/action/' . $action . '.php';
        } else
        {
            die($action ." 404 Action NOT FOUND");
        }
    }
    }

    function redir($url)
    {
        echo "<script>window.location='" .URL. $url . "';</script>";
    }
    
          function newtab($url,$nombre="newtab",$ancho = "1200")
    {
        
?>
<script type="text/javascript">

	 let params = 'scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,width=<?= $ancho ?>px,height=500px';

open(<?= '"'.URL.''.$url.'"'?>, '<?= $nombre ?>', params);
	
</script>
<?php
	
    }

function load_model($model,$origin="")
    { 
//          if($origin=="index"){
//             $ruta = '../';
            
//          }else if ($origin != ""){
//             $ruta='../'.$origin.'/';
//          } else $ruta ='';
// if (file_exists($ruta.'app/model/' . $model . '.php'))
//         {

//             require_once $ruta.'app/model/' . $model . '.php';
//         } else
//         {
//             die($model ." 404 model NOT FOUND");
//         }
        

//         return new $model;

   // Esto apunta a /app/model/ siempre, sin importar desde dónde lo llames
    $file = __DIR__ . '/model/' . $model . '.php';

    if (file_exists($file)) {
        require_once $file;
    } else {
        die($model . " 404 model NOT FOUND (" . $file . ")");
    }

    return new $model;
    }