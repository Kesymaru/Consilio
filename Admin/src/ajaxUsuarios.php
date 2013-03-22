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

					//BloquearIp( $ip, $usuario, $sitio, $intento )
					$bloquear->BloquearIp( $_SERVER['REMOTE_ADDR'], $_POST['usuario'], 1); //bloquea la ip

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

	case 'EstadoBloqueado':
		$bloquear = new Bloquear();

		//si esta bloqueado en admin
		if( $bloquear->Estado( $_SERVER['REMOTE_ADDR'], 1 ) ){
			$_SESSION['bloquedo'] = true;
			echo $bloquear->MensajeBloqueo();
		}
		break;
}

?>