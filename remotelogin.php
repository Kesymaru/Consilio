<?php

/**
* LOGUEO DESDE OTRA PAGINA
*/

require_once("src/class/session.php");

if( isset($_POST['username']) && $_POST['password'] ){
	echo 'logueando';
	$session = new Session();
	if ( $session->RemoteLogIn($_POST['username'], $_POST['password']) ){
		echo $_POST['home'];
	}else{
		echo 'Error: credenciales invalidas.';
	}
}else{
	echo 'error: credenciales invalidas';
}

?>