
<?php
	session_name("Comercio360");
@session_start();
if($_GET['logout']=="1"){
    session_destroy();
    	session_name("Comercio360");
@session_start();
}
echo "<pre>".print_r($_SESSION)."</pre>";
?>