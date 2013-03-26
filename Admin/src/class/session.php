<?php

require_once("classDatabase.php");
//require_once("mysql_session_manager.php");

class Session{
	
	/**
	* CONSTRUCTOR
	*/
	public function __construct(){
		//ini_set( 'session.save_path' , '/var/data/development.77digital.com/matrizescala/Admin/sessions/');

		//error_reporting(E_ALL);
		error_reporting(0);
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
		//$ip = mysql_real_escape_string( $_SERVER['REMOTE_ADDR'] );
		$ip = mysql_real_escape_string( $_SERVER["HTTP_X_FORWARDED_FOR"] );
		$fecha = date('Y-m-d G:i:s');

		$query = "INSERT INTO admin_logs (admin, ip, fecha) VALUES ( '".$id."', '".$ip."', '".$fecha."' ) ";

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
	* DESBLOQUEA UNA IP
	* @param string $ip -> ip h desbloquear
	* @param boolean false -> si falla
	* @return boolean true -> si se realiza
	*/
	public function DesbloquearIp( $ip ){

		$ip = mysql_real_escape_string( $ip );
		
		echo $query = "UPDATE ip_bloqueadas SET ignorar = 1 WHERE ip = '".$ip."'";

		//si esta bloqueada permanentemente
		if( $this->EsPermanente( $ip )){
			
			$this->DesbloquearPermantente( $ip );

		}
		
		$base = new Database();

		if( $base->Update( $query )){
			return true;
		}else{
			return false;
		}

	}	

	/**
	* BLOQUEA UNA IP PERMANENTEMENTE
	* @param string $ip -> ip h desbloquear
	* @param string $sitio -> sitio al que aplica
	* @param boolean false -> si falla
	* @return boolean true -> si se realiza
	*/
	public function BloqueoPermanenteIp( $ip ){
		
		if( !$this->EsPermanente( $ip )){

			$base = new Database();

			$ip = mysql_real_escape_string( $ip );

			$fecha = date('Y-m-d G:i:s');
			$query = "INSERT INTO ip_bloqueos_permanentes (ip, fecha) VALUES ('".$ip."', '".$fecha."') ";

			if( $base->Insert( $query )){
				return true;
			}else{
				return false;
			}

		}else{
			return true; //ya estaba bloqueada
		}

	}

	/**
	* ELIMINA UN BLOQUEO PERMANENTE
	* @param string $ip -> ip ha desbloquear
	*/
	private function DesbloquearPermantente($ip){
		$base = new Database();

		$ip = mysql_real_escape_string( $ip );

		$query = "DELETE FROM ip_bloqueos_permanentes WHERE ip = '".$ip."'";

		if ( !$base->Delete( $query ) ){
			echo 'Error: al borrar bloqueo permancente de la ip '.$ip.'<br/>session.php class Bloquear->DesactivarBloqueoIp';
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

		$config = $base->Select( "SELECT * FROM config WHERE sitio = 1");

		$datos = $base->Select( $query );

		if( !empty($datos) ){
			
			$ultimo = $datos[0];

			if( empty($ultimo['fecha']) ){
				return false;
			}

			//fue desbloqueada por el admin
			if( $ultimo['ignorar'] == 1){
				return false;
			}

			//echo '<pre>'; print_r($datos); echo '</pre>';
			
			$now = date('Y-m-d G:i:s');
			$ahora = strtotime( $now );
			
			$expira = strtotime( $ultimo['fecha'].' +'.$config[0]['tiempo_bloqueo'].' minutes' );

			if( $ahora < $expira ){
				return true;
			}else{
				return false;
			}

			/*$minutos = $this->Minutos( $ip );

			if( $minutos >= 0 ){
				return true;
			}else{
				return false;
			}*/
		}
		
		return false; //no esta bloqueado
	}

	/**
	* OBTIENE LOS MINUTOS DE BLOQUEO DE UNA IP
	* @param string $ip -> ip
	* @return int $minutos -> timespan de la diferencia entre tiempo
	*/
	public function Minutos( $ip, $sitio ){
		$base = new Database();

		$ip = mysql_real_escape_string($ip);
		$sitio = mysql_real_escape_string($sitio);

		$query = "SELECT ip, MAX(fecha) AS fecha FROM ip_bloqueadas WHERE ip = '".$ip."' AND sitio = '".$sitio."'";

		$config = $base->Select( "SELECT * FROM config WHERE sitio = 1");
		$datos = $base->Select( $query );

		if( !empty($datos) ){

			$now = date('Y-m-d G:i:s');
			$ahora = strtotime( $now );
				
			$expira = strtotime( $datos[0]['fecha'].' +'.$config[0]['tiempo_bloqueo'].' minutes' );
			$expira2 = date('Y-m-d G:i:s', $expira);
			

			$minutos = $expira - $ahora;

			//echo $expira2.' - '.$now.' = '.($minutos/60).'<hr>';

			return $minutos;
		}else{
			return 0;
		}
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
	* OBTIENE TODAS LAS IPS BLOQUEADAS Y SUS DATOS
	* @param boolean false -> si no hay ips bloqueadas
	* @param array $datos -> datos de las ipss bloquedas
	*/
	public function getIps(){
		$base = new Database();

		//$query = "SELECT usuario, sitio, ip, id, estado, COUNT(ip), MAX(fecha) FROM ip_bloqueadas GROUP BY ip";
		$query = "SELECT sitio, ip, MAX(id) AS id, COUNT(ip) AS total_intentos, MAX(fecha) AS fecha FROM ip_bloqueadas GROUP BY ip, sitio";
		$datos = $base->Select( $query );

		if( !empty($datos) ){
			return $datos;
		}else{
			return false;
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

	/**
	* ELIMINA UN IP DEL REGISTRO
	* @param string $ip -> ip ha eliminar
	* @return boolean true -> si se elimina
	* @return boolean false -> si fallas
	*/
	public function EliminarIp( $ip ){
		
		// si la ip es permanente se elimina	
		if( $this->EsPermanente( $ip ) ){
			$this->DesbloquearPermantente( $ip );
		}
 
		$base = new Database();

		$ip = mysql_real_escape_string($ip);

		$query = "DELETE FROM ip_bloqueadas WHERE ip = '".$ip."'";

		if( $base->Delete( $query) ){
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