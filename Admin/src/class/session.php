<?php

require_once("classDatabase.php");

class Session{
	
	/**
	* CONSTRUCTOR
	*/
	public function __construct(){
		error_reporting(E_ALL);
		session_set_cookie_params(1200);
		date_default_timezone_set('America/Costa_Rica');

		//sino se ha iniciado session
		if( !isset($_SESSION['admin']) ){
			session_start();

			$protocolo = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        	$dominio = $_SERVER['HTTP_HOST'];

			//$_SESSION['home'] = 'http://'.$_SERVER['HTTP_HOST'].'/Consilio';
			$_SESSION['home'] = $protocolo.$dominio.'/matrizescala/Admin';
			$_SESSION['matriz'] = $protocolo.$dominio.'/matrizescala';
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
			/*echo '<script type="text/javascript">
			window.location = "'.$login.'"
			</script>';*/
						
			//header('Location: '.$login);
			//exit;
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
					if($campo != 'password' && $campo != ''){
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
	* @param $usuario
	* @param $password -> sin encriptar
	*/
	public function LogIn($usuario, $password){
		$base = new Database();
		$usuario = mysql_real_escape_string($usuario);

		$password = $base->Encriptar($password);
		$query = "SELECT * FROM admin WHERE usuario = '".$usuario."' AND password = '".$password."'";

		//existe el usuario
		if( $base->Existe($query) ){
			
			if($this->AdminIniciarSession($usuario, $password)){
				$_SESSION['admin'] = true;
				$this->Logueado();

				return true;
			}

		}else{
			echo 'El usuario o la contraseña es incorrecta.<br/>'+$query;
		}

	}

	/**
	* INICIALIZA LA SESSION DE UN ADMIN
	* @param $usuario -> usuario del admin
	* @param #password -> password encriptado
	*/
	private function AdminIniciarSession($usuario, $password){
		$base = new Database();
		
		$usuario = mysql_real_escape_string($usuario);
		$datos = $base->Select("SELECT * FROM admin WHERE usuario = '".$usuario."' AND password = '".$password."'");

		if(!empty($datos)){
			/*foreach ($datos as $fila => $c) {
				foreach ($datos[$fila] as $campo => $valor) {
					if($campo != 'password' ){
						if(!empty($valor)){
							$_SESSION[$campo] = $valor;
						}
					}
				}
			}*/
			foreach ($datos[0] as $campo => $dato) {
				if($dato != '' && $campo != 'password' && $campo != 'log' && $campo != 'fecha_creacion' && $campo != 'fecha_actualizacion' && $campo != 'activo'){
					$_SESSION[$campo] = $dato;
				}
			}

			$_SESSION['tipo'] = 'admin';
			$_SESSION['bienvenida'] = false;
			$_SESSION['admin'] = true;
			
			//unset($_SESSION['invitado']);

			$this->RegistrarVisita($_SESSION['id']);

			/*echo '<pre>';
			print_r($_SESSION);
			echo '</pre>';*/

			return true;
		}else{
			$_SESSION['admin'] = false;
			return false;
		}

	}


	/**
	* LOGOUT 
	*/
	public function LogOut(){
		$this->SalidaAdmin($_SESSION['id']);

		session_unset($_SESSION['admin']);
		$_SESSION = array();
		session_destroy ();
	}

	/**
	* REGISTRA LA ENTRADA DEL ADMIN EN LOG
	* @param $id -> id del admin
	*/
	private function RegistrarVisita($id){
		$base = new Database();
		$id = mysql_real_escape_string($id);

		$query = "SELECT * FROM admin WHERE id = '".$id."'";

		$datos = $base->Select($query);
		if(empty($datos[0]['log'])){
			$registros = array();
			$registros[] = date("Y-m-d H:i:s");
		}else{
			$registros = array();
			$registros = unserialize($datos[0]['log']);
			if(!empty($registros)){
				$registros[] = date("Y-m-d H:i:s");
			}
		}
		$log = serialize($registros);

		$query = "UPDATE admin SET log = '".$log."', activo = 1 WHERE id = '".$id."'";
		$base->Update($query);
	}

	/**
	* REGISTRA SALIDA DEL ADMIN
	* @param $id -> id del admin
	*/
	private function SalidaAdmin($id){
		$base = new Database();
		$id = mysql_real_escape_string($id);

		$query = "UPDATE admin SET activo = 0 WHERE id = '".$id."'";
		$base->Update($query);
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
		error_reporting(E_ALL);
		//ini_set( 'session.save_path' , 'C:/wamp/www/matrizescala/Admin/sessions');

		ini_set( 'session.save_path' , '/var/data/development.77digital.com/matrizescala/Admin/sessions/');
			
		//session_unset();
		//session_destroy();

		$path = session_save_path();
		
		if (!is_writable(session_save_path()) ) {
    		echo 'Session path "'.session_save_path().'" is not writable for PHP!'; 
		}

		echo $path;
		session_start();

		//si el usuario no ha iniciado session
		if( isset($_SESSION['admin']) ){
			//$index = $_SERVER['HTTP_HOST'].'/matrizescala/index.php';
			header('Location: '.'index.php');
			exit;
		}else{
			//$_SESSION['invitado'] = false;
		}

	}

}


?>