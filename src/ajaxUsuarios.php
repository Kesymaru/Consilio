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

		/*if(isset($_POST['usuario']) && isset($_POST['password'])){
			$session = new Session();
			$session->LogIn($_POST['usuario'], $_POST['password']);				
		}*/
		if( isset($_POST['usuario']) && isset($_POST['password']) ){
			$session = new Session();
			
			if( $session->LogIn($_POST['usuario'], $_POST['password']) ){
				echo '<script>
						top.location.href = "index.php";
					   </script>';
			}else{
				$bloquear = new Bloquear();

				if(isset($_SESSION['intentos'])){
					$_SESSION['intentos']++;
				}else{
					$_SESSION['intentos'] = 1;
				}
				if( 3 <= $_SESSION['intentos'] ){

					$_SESSION['bloquedo'] = true;

					$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];

					//BloquearIp( $ip, $usuario, $sitio ) $_SERVER['REMOTE_ADDR']
					$bloquear->BloquearIp( $ip, $_POST['usuario'], 0); //bloquea la ip
					
					$mensaje = $bloquear->MensajeBloqueo();

					echo '<script>
							notificaIntento("Has excedido el numero de intentos.");
							Bloqueado( '.json_encode($mensaje).' );
						   </script>';
				}else{
					echo '<script>
							notificaAtencion("El usuario o la contraseña es incorrecta.<br/>Intento: '.$_SESSION['intentos'].'");
						   </script>';
				}
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

	//VERIFICA SI LA IP TIENE BLOQUEO ACTIVO O PERMANENTE
	case 'EstadoBloqueado':

		$bloquear = new Bloquear();

		$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];

		//si esta bloqueado en admin
		if( $bloquear->Estado( $ip ) || $_SESSION['bloquedo'] == true ){
			$_SESSION['bloquedo'] = true;
			echo $bloquear->MensajeBloqueo();
		}else{
			$_SESSION['bloquedo'] = false;
		}
		break;

}

?>