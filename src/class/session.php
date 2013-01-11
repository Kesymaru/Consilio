<?php

require_once("classDatabase.php");

class Session{
	
	/**
	* CONSTRUCTOR
	*/
	public function __construct(){

		//sino se ha iniciado session
		if( !isset($_SESSION['admin']) ){
			session_start();
			//$_SESSION['home'] = 'http://'.$_SERVER['HTTP_HOST'].'/Consilio';
			$_SESSION['home'] = '/matrizescala';
		}

	}
	
	/**
	* DETERMINA SI EL USUARIO ESTA LOGUEADO
	* return true si lo esta sino redirecciona al login.php
	*/
	public function Logueado(){

		if( !isset($_SESSION['admin']) ){
			$login = $_SESSION['home']."/login.php";

			//redirecciona
			echo '<script type="text/javascript">
			window.location = "'.$login.'"
			</script>';
						
			//header('Location: '.$login);
			exit;
		}else{
			return true;
		}

	}

	/**
	* ACTUALIZA LOS DATOS DE LA SESSION
	* @param $usuarioTipo -> tipo de usuario admin o cliente
	*/
	public function Update($usuarioTipo){
		$base = new Database();
		$datos = $base->Select("SELECT * FROM ".$usuarioTipo." WHERE id = '".$_SESSION['id']."'");

		if(!empty($datos)){
			foreach ($datos as $fila => $c) {
				foreach ($datos[$fila] as $campo => $valor) {
					if($campo != 'password'){
						//carga los datos en sessiones
						$_SESSION[$campo] = $valor;
					}
				}
			}
			return true;
		}else{
			return false;
		}
	}

	/**
	* SE ENCARGA DE LOGUEAR USUARIO
	*/
	public function LogIn($usuario, $password){
		$base = new Database();

		$password = $base->Encriptar($password);

		//existe el usuario
		if( $base->Existe("SELECT * FROM admin WHERE usuario = '".$usuario."' AND password = '".$password."'") ){
			
			if($this->AdminIniciarSession($usuario, $password)){
				$_SESSION['admin'] = true;
				$this->Logueado();
			}

		}else{
			echo 'El usuario o la contraseña es incorrecta';
		}

	}

	/**
	* INICIALIZA LA SESSION DE UN ADMIN
	* @param $usuario -> usuario del admin
	* @param #password -> password encriptado
	*/
	private function AdminIniciarSession($usuario, $password){

		$base = new Database();
		
		$where = " usuario = '".$usuario."' AND contrasena = '".$password."'";
		
		$datos = $base->Select("SELECT * FROM admin WHERE usuario = '".$usuario."' AND password = '".$password."'");

		if(!empty($datos)){
			foreach ($datos as $fila => $c) {
				foreach ($datos[$fila] as $campo => $valor) {
					if($campo != 'password'){
						$_SESSION[$campo] = $valor;
					}
				}
			}
			$_SESSION['tipo'] = 'admin';
			$_SESSION['bienvenida'] = false;
			return true;
		}else{
			return false;
		}

	}

	/**
	* LOGOUT 
	*/
	public function LogOut(){
		session_unset($_SESSION['admin']);
		$_SESSION = array();
		session_destroy ();
	}

}

/**
* CLASE PARA LA SESSION EN EL login.php
*/
class SessionInvitado{
	
	/**
	* CONSTRUCTOR HACE TODO EL TRABAJO
	*/
	public function __construct(){
		session_start();

		//si el usuario no ha iniciado session
		if( isset($_SESSION['admin']) ){
			//$index = $_SERVER['HTTP_HOST'].'/matrizescala/index.php';
			header('Location: '.'index.php');
			exit;
		}

	}

}


?>