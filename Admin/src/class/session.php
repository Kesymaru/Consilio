<?php

require_once("classDatabase.php");
//require_once("mysql_session_manager.php");

class Session{
	
	/**
	* CONSTRUCTOR
	*/
	public function __construct(){
		//ini_set( 'session.save_path' , '/var/data/development.77digital.com/matrizescala/Admin/sessions/');

		error_reporting(E_ALL);
		//session_set_cookie_params(1200);
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
			return false;
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

		$query = "INSERT INTO admin_logs (admin, fecha) VALUES ('".$id."', NOW() ) ";

		$base->Insert($query);
	}

	/**
	* REGISTRA SALIDA DEL ADMIN
	* @param $id -> id del admin
	*/
	private function SalidaAdmin($id){
		/*$base = new Database();

		$id = mysql_real_escape_string($id);

		$query = "INSERT INTO admin_logs (admin, fecha, tipo) VALUES ('".$id."', NOW(), 'out' )";

		$base->Insert( $query );*/
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

		//ini_set( 'session.save_path' , '/var/data/development.77digital.com/matrizescala/Admin/sessions/');


		//echo = session_save_path();
		
		if (!is_writable(session_save_path()) ) {
    		echo '<script>
    				notificaError("Session path '.session_save_path().' no es escribible.");
    			  </script>';
		}

		session_start();

		//si el usuario no ha iniciado session
		if( isset($_SESSION['admin']) ){
			//$index = $_SERVER['HTTP_HOST'].'/matrizescala/index.php';
			header('Location: '.'index.php');
			exit;
		}else{
			//$_SESSION['invitado'] = false;
		}

		if( isset($_SESSION['bloqueado'])){
			$ip= $_SERVER['REMOTE_ADDR']; 

			$_SESSION['bloquedo'] = true;

			echo '<script>
					Bloqueado("'.$ip.'");
				  </script>';
		}

	}

}

/**
* CLASE PARA BLOQUEAR 
*/
class Bloquear{

	public function __construct(){
		date_default_timezone_set('America/Costa_Rica');
	}

	/**
	* BLOQUEA UNA IP, ADMIN BLOQUEA 4 HORAS, CLIENTE BLOQUEA 1 HORA
	* @param string $usuario -> nombre del usuario con el que se intento ingresar
	* @param string $ip -> ip de la compu/divice
	* @param int $admin -> 0= cliente, 1= admin
	*/
	public function BloquearIp( $usuario, $ip, $admin ){
		$base = new Database();

		$usuario = mysql_real_escape_string($usuario);
		$ip = mysql_real_escape_string($ip);
		$admin = mysql_real_escape_string($admin);

		$query = "INSERT INTO ip_bloqueadas (usuario, ip, admin, fecha) VALUES ( '".$usuario."', '".$ip."', '".$admin."', NOW() )";

		if( !$base->Insert( $query )){
			
		}
	} 

	/**
	* REVISA SI LA IP ESTA BLOQUEADA
	*/
	public function Estado(){
		$ip= $_SERVER['REMOTE_ADDR']; 
		
		$base = new Database();

		$ip = mysql_real_escape_string($ip);

		$query = "SELECT ip, id, MAX(fecha) AS fecha FROM ip_bloqueadas WHERE ip = '".$ip."'";

		$config = $base->Select( "SELECT * FROM config WHERE sitio = 1");

		$datos = $base->Select( $query );

		if( !empty($datos) ){
			//echo '<pre>'; print_r($datos); echo '</pre>';
			$ultimo = $datos[0];

			$now = date('Y-m-d G:i:s');
			$ahora = strtotime( $now );
			
			$expira = strtotime( $ultimo['fecha'].' +'.$config[0]['tiempo_bloqueo'].' minutes' );

			if( $ahora < $expira ){
				return true;
			}else{
				return false;
			}
		}else{
			return false; //no esta bloqueado
		}
	}
}


?>