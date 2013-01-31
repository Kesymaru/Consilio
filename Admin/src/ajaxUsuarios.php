<?php

/**
* AJAX PARA LOS UAURIOS - SEAN ADMIN O CLIENTE
*/
require_once("class/session.php");
require_once("class/usuarios.php");

switch ($_POST['func']){

	/*
		USUARIOS
	*/

	//LOQUEA
	case 'LogIn':
		if(isset($_POST['usuario']) && isset($_POST['password'])){
			$session = new Session();
			if( !$session->LogIn($_POST['usuario'], $_POST['password']) ){
				echo 'El usuario o la contraseña es incorrecta.<br/>';
			}
		}
		break;

	//SALIR LOGOUT
	case 'LogOut':
		$session = new Session();
		$session->LogOut();
		break;

	//RESET PASSWORD CON EL USUARIO
	case 'ResetPasswordUsuario':
		if(isset($_POST['usuario'])){
			resetPasswordUsuario($_POST['usuario']);
		}
		break;

	//RESET PASSWORD CON EL EMAIL
	case 'resetPasswordEmail':
		if(isset($_POST['email'])){
			resetPasswordEmail($_POST['email']);
		}
		break;

	//ACTUALIZA UN DATO DEL ADMIN
	case 'ActualizarAdminDato':
		if(isset($_POST['nuevo']) && isset($_POST['dato'])){
			$admin = new Admin();
			$admin->setAdminDato($_POST['dato'], $_POST['nuevo']);
		}
		break;

	case 'ActualizarAdminPassword':
		if(isset($_POST['password'])){
			$admin = new Admin();
			$admin->setAdminPassword($_POST['password']);
		}
		break;
}

?>