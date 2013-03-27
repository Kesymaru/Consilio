<?php

/**
* AJAX PARA LOS UAURIOS - SEAN ADMIN O CLIENTE
*/
require_once("class/session.php");
require_once("class/usuarios.php");

//error_reporting(0);

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

					$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];

					//BloquearIp( $ip, $usuario, $sitio, $intento ) $_SERVER['REMOTE_ADDR']
					$bloquear->BloquearIp( $ip, $_POST['usuario'], 1); //bloquea la ip

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
	case 'resetPasswordUsuario':
		if( isset($_POST['usuario']) ){
			$reset = new Reset();
			 
			if( $reset->resetPasswordUsuario( $_POST['usuario'] ) ){
				MensajeReset();
			}
			
		}
		break;

	//RESET PASSWORD CON EL EMAIL
	case 'resetPasswordEmail':
		if( isset($_POST['email']) ){			
			$reset = new Reset();
			
			if( $reset->resetPasswordEmail( $_POST['email'] ) ){
				 MensajeReset();
			}
			
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

	//VERIFICA SI LA IP TIENE BLOQUEO ACTIVO O PERMANENTE
	case 'EstadoBloqueado':

		$bloquear = new Bloquear();

		/*$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];


		//si esta bloqueado en admin
		if( $bloquear->Estado( $ip ) || $_SESSION['bloquedo'] == true){
			$_SESSION['bloquedo'] = true;
			echo $bloquear->MensajeBloqueo();
		}else{
			$_SESSION['bloquedo'] = false;
		}*/
		break;
}

/**
* MENSAJE AL RESETEAR UNA CUENTA
*/
function MensajeReset(){
	echo 'Un email ha sido enviado con la nueva contraseña.<br/>';
}

?>