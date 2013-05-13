

<?php

/**
* REDIRECCIONA A INDEX.PHP O A LOGIN SI NO ESTA LOGUEADO
*/

require_once("../src/class/session.php");

$session = new Session();

if($session->Logueado()){
	header('Location: ../index.php');
}else{
	header('Location: ../login.php');
}

?>