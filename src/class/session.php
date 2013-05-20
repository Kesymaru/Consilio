<?php

require_once("classDatabase.php");
error_reporting(0);

class Session{
	
	/**
	* CONSTRUCTOR
	*/
	public function __construct(){

		//sino se ha iniciado session
		if( !isset($_SESSION['cliente']) ){
			session_start();
			//$_SESSION['home'] = 'http://'.$_SERVER['HTTP_HOST'].'/Consilio';
			//$_SESSION['home'] = '/matrizescala';
			//$_SESSION['datos'] = 'Admin/';

			$protocolo = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        	$dominio = $_SERVER['HTTP_HOST'];

			//$_SESSION['home'] = 'http://'.$_SERVER['HTTP_HOST'].'/Consilio';
			//$_SESSION['home'] = $protocolo.$dominio.'/matrizescala';
            $_SESSION['home'] = $protocolo.$dominio.'/escalasandbox';
			$_SESSION['datos'] = $protocolo.$dominio.'/matrizescala/Admin/';
			$_SESSION['origen'] = 'Admin/';
		}

		date_default_timezone_set('America/Costa_Rica');
		
	}
	
	/**
	* DETERMINA SI EL USUARIO ESTA LOGUEADO
	* return true si lo esta sino redirecciona al login.php
	*/
	public function Logueado(){

		if( !isset($_SESSION['cliente']) ){
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
	* REGISTRA VISITA
	*/
	public function LogIn($usuario, $password){
		$base = new Database();

		$usuario = mysql_real_escape_string($usuario);
		$password = mysql_real_escape_string($password);

		$password = $base->Encriptar($password);

		//existe el usuario
		if( $base->Existe("SELECT * FROM clientes WHERE usuario = '".$usuario."' AND contrasena = '".$password."'") ){
			
			if($this->UserIniciarSession($usuario, $password)){
				$_SESSION['cliente'] = true;
				$this->Logueado();

				return true;
			}

		}else{
			return false;
		}

	}

	/**
	* LOGUEO REMOTO
	*/
	public function RemoteLogIn($usuario, $password){
		$base = new Database();

		$usuario = mysql_real_escape_string($usuario);
		$password = mysql_real_escape_string($password);

		$password = $base->Encriptar($password);

		//existe el usuario
		if( $base->Existe("SELECT * FROM clientes WHERE usuario = '".$usuario."' AND contrasena = '".$password."'") ){
			
			if($this->UserIniciarSession($usuario, $password)){
				$_SESSION['cliente'] = true;
				return true;
			}

		}else{
			return false;
		}
	}

	/**
	* INICIALIZA LA SESSION DE UN USUARIO
	* @param $usuario -> usuario del admin
	* @param #password -> password encriptado
	*/
	private function UserIniciarSession($usuario, $password){
		$base = new Database();

		$query = "SELECT * FROM clientes WHERE usuario = '".$usuario."' AND contrasena = '".$password."'";

		$datos = $base->Select($query);

		if(!empty($datos)){
			//inicializa variables de session
			foreach ($datos as $fila => $c) {
				foreach ($datos[$fila] as $campo => $valor) {
					if($campo != 'password'){
						$_SESSION['cliente_'.$campo] = $valor;
					}
				}
			}

			$_SESSION['cliente'] = true;
			$_SESSION['cliente_bienvenida'] = false;
			
			$this->RegistrarVisita($_SESSION['cliente_id']);

			return true;
		}else{
			return false;
		}

	}

	/**
	* LOGOUT 
	*/
	public function LogOut(){
		session_unset($_SESSION['cliente']);
		$_SESSION = array();
		session_destroy ();
	}

	/**
	* REGISTRA UNA VISITA DEL CLIENTE
	* @param int $id -> id del cliente
	*/
	public function RegistrarVisita($id){
		$base = new Database();

		$id = mysql_real_escape_string($id);
		//$ip = mysql_real_escape_string( $_SERVER['REMOTE_ADDR'] );
		$ip = mysql_real_escape_string( $_SERVER["HTTP_X_FORWARDED_FOR"] );
		$fecha = date('Y-m-d G:i:s');

		$query = "INSERT INTO clientes_logs (cliente, ip, fecha) VALUES ( '".$id."', '".$ip."', '".$fecha."' )";
		
		$base->Insert($query);
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
		if( isset($_SESSION['cliente']) ){
			//$index = 'http://'.$_SERVER['HTTP_HOST'].'/Consilio/index.php';
			header('Location: '.'index.php');
			exit;
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
	* @param string $ip -> ip ha bloquear
	* @param string $usuario -> nombre del usuario con el que se intento ingresar
	* @param string $ip -> ip de la compu/divice
	* @param int $sitio -> 0= cliente, 1= admin
	*/
	public function BloquearIp( $ip, $usuario, $sitio ){
		
		//si esta bloqueada permanentemente
		if( $this->EsPermanente( $ip )){
			
			$this->DesbloquearPermantente( $ip );

		}

		$base = new Database();

		//$ip = $_SERVER['REMOTE_ADDR'];

		$usuario = mysql_real_escape_string($usuario);
		$ip = mysql_real_escape_string($ip);
		$sitio = mysql_real_escape_string($sitio);

		$fecha = date('Y-m-d G:i:s');

		$query = "INSERT INTO ip_bloqueadas (usuario, ip, sitio, fecha, ignorar ) VALUES ( '".$usuario."', '".$ip."', '".$sitio."', '".$fecha."', '0' )";

		if( $base->Insert( $query )){
			return true;
		}else{
			return false;
		}
	}

	/**
	* REVISA SI LA IP ESTA BLOQUEADA
	* @param string $ip -> ip a verificar
	* @return boolean true -> si esta bloqueado
	* @return boolean false -> si no esta bloqueado
	*/
	public function Estado( $ip ){

		//si la ip esta bloqueada permanentemente
		if( $this->EsPermanente($ip) ){
			return true;
		}

		$base = new Database();

		$ip = mysql_real_escape_string($ip);

		$query = "SELECT ip, id, ignorar, MAX(fecha) AS fecha FROM ip_bloqueadas WHERE ip = '".$ip."'";

		$config = $base->Select( "SELECT * FROM config");

		$datos = $base->Select( $query );

		if( !empty($datos) ){
			
			$ultimo = $datos[0];

			if( empty($ultimo['fecha']) ){
				return false;
			}

			echo '<pre>'; print_r($datos); echo '</pre>';
			
			//fue desbloqueada por el admin
			if( $ultimo['ignorar'] == 1){
				return false;
			}

			
			
			$now = date('Y-m-d G:i:s');
			$ahora = strtotime( $now );
			
			$expira = strtotime( $ultimo['fecha'].' +'.$config[0]['tiempo_bloqueo'].' minutes' );

			if( $ahora < $expira ){
				return true;
			}else{
				return false;
			}
		}
		
		return false; //no esta bloqueado
	}

	/**
	* COMPONE EL MENSAJE DE BLOQUEO
	* @return string $mensaje -> mensaje
	*/
	public function MensajeBloqueo(){
		$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		$base = new Database();

		$query = "SELECT * FROM config WHERE sitio = 1";

		$config = $base->Select($query);

		$mensaje = '';
		if( !empty($config) ){
			$mensaje = 'Has excedido el numero de intentos.<br/>
						Tu ip : '.$ip.' ha sido bloqueda en este sitio.<br/><br/>
						Proximo intento dentro de '.$config[0]['tiempo_bloqueo'].' minutos
						<br/><br/>
						<hr>
						Para mas informacion y/o ayuda:
						<br/>
							<a href="mailto:'.$config[0]['support'].'?Subject=Ayuda ip bloqueada" >
								'.$config[0]['support'].'
							</a>
						<hr>';

			return $mensaje;
		}else{
			echo 'Error: no se pudo obtener la configuracion del sistema.<br/>session.php bloquear';
		}
		
	}

	/**
	* SI LA IP ESTA BLOQUEADA PERMANENTEMENTE, ESTO APLICA PARA AMBOS SITIOS (admin y cliente);
	* @param string $ip -> ip del cliente
	* @return boolean true -> si es permanente
	* @return booelan false -> si no es permanente
	*/
	public function EsPermanente( $ip ){
		$base = new Database();

		$ip = mysql_real_escape_string($ip);

		$query = "SELECT * FROM ip_bloqueos_permanentes WHERE ip = '".$ip."' ";

		$datos = $base->Select( $query );

		if( !empty($datos) ){
			//ya existe
			return true;
		}else{
			return false;
		}
	}

}

/**
* CLASE PARA EL MANEJO DE LA CONFIGURACION DEL SITIO
*/
class Confi{
	public function __construct(){

	}

	/**
	* OBTIENE ALGUN PARAMETRO DE LA CONFIGURACION
	* @param string $sitio-> 0 = cliente, 1 = admin
	* @param string $campo -> dato solicitado
	* @return boolean false -> si falla
	* @return string/int $dato -> dato solicitado
	*/
	public function getConfig(){
		$base = new Database();

		$query = "SELECT * FROM config";

		$datos = $base->Select( $query );

		if( !empty($datos) ){
			return $datos;
		}else{
			return false;
		}
	}
}

?>