<?php
/**
* PERMUITE COMPONER MAILS PARA ALGUNA FUNCIONALIDAD
*/
require_once("class/mail.php");
require_once("class/session.php");

//SEGURIDAD
if( !$session->Logueado() ){
	header('Location: ../login.php');
}

if( isset($_POST['func']) ){

	switch ($_POST['func']) {
		case 'ProyectoMail':
			if( isset($_POST['proyecto']) ){
				echo 'mail componer';
			}
			break;
		
		default:
			# code...
			break;
	}
	
}else{
	echo "<script> notificaError(Error: "
}

?>