<?php

/**
* LOGUEO DESDE OTRA PAGINA
*/

require_once("src/class/session.php");

if( isset($_POST['username']) && $_POST['password'] ){
	
	$session = new Session();
	if( $session->RemoteLogIn($_POST['username'], $_POST['password']) ){
		header('Location: '.$_SESSION['home']);
	}else{
		header('Location: '.$_SERVER['HTTP_REFERER');
	}
}else{
	header('Location: '.$_SERVER['HTTP_REFERER');
}

?>