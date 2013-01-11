<?php

/**
* AJAX PARA LOS USUARIOS TIPOS CLIENTE
*/
require_once("class/session.php");
require_once("class/usuarios.php");

switch ($_POST['func']){

	/*
	* USUARIOS
	*/

	//LOQUEA
	case 'LogIn':
		if(isset($_POST['usuario']) && isset($_POST['password'])){
			$session = new Session();
			$session->LogIn($_POST['usuario'], $_POST['password']);
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

	//registr usuario
	case 'registro':
		if( isset($_POST['usuario']) && isset($_POST['email']) && isset($_POST['password'])){
			registro($_POST['usuario'], $_POST['email'], $_POST['password']);
		}
		break;
}

?>