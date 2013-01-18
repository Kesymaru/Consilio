<?php

/**
 * MANEJADOR DE ERRORES
 */

require_once("mail.php");
require_once("session.php");

if(isset($_POST['error']) && isset($_POST['site']) ){
	$error = new Error();
	$error->newError( $_POST['error'], $_POST['site'] );
}

class Error{

	public function __construct(){
		date_default_timezone_set('America/Costa_Rica');
	}

	/**
	 * REGISTRA NUEVO ERROR
	 * @param $error -> mensaje
	 * @param $site -> sitio donde ocurrio
	 */
	public function newError($error, $site){
		
		$mail = new Mail();
		

		$myFile = "matrizErrors.txt";
		
		$error = str_replace("<br/>", "\n", $error);
		$error = str_replace("<br>", "\n", $error);
		$error = str_replace("<br/>", "\n", $error);
		$error = str_replace("<br />", "\n", $error);
		$error = str_replace("<hr>", "\n", $error);

		$mensaje = "\n\n".$error."\n\n";

		$mensaje .= "\n\tSitio: ".$site."\n";

		if($site == "Matriz" ){
			$mensaje .= $this->Usuario();
		}else{
			$mensaje .= $this->Admin();
		}

		$mensaje .= $this->Navegador();
		$mensaje .= "\t".date("F j Y - g:i a")."\n --------------------<>--------------------";
		
		if(filesize($myFile) > 0){
			$file = fopen($myFile, 'r') or die("Error: al abrir matrizErrors.txt");
			$contenido = fread($file, filesize($myFile));
		
			$mensaje = $contenido.$mensaje;
		}

		$file = fopen($myFile, 'w') or die("Error: al abrir matrizErrors.txt");
		fwrite($file, $mensaje);

		fclose($file);

		//envia email
		$mail->errorMail($mensaje);
	}

	/**
	* OBTIENE DATOS DEL NAVEGADOR
	*/
	private function Navegador(){
		$_SERVER['HTTP_USER_AGENT'];
		$browser = get_browser(null, true);
		$mensaje = "";

		if(is_array($browser)){
			$mensaje .= "\tNavegador: ".$browser['browser']."\n";
			$mensaje .= "\tPlataforma: ".$browser['platform']."\n";
			$mensaje .= "\tVersion: ".$browser['version']."\n";
			$mensaje .= "\tCSS: ".$browser['version']."\n";
			$mensaje .= "\tJAVASCRIPT: ".$browser['javascript']."\n";
		}else{
			$mensaje = "\t===navegador===\n";
		}
		return $mensaje;
	}

	/**
	* OBTIENE LOS DATOS DE UN USUARIO
	*/
	private function Usuario(){
		$mensaje = '';

		if( isset($_SESSION['cliente_nombre']) && isset($_SESSION['cliente_id']) ){
			$mensaje .= "\tUsuario: ".$_SESSION['cliente_nombre']."\n";
			$mensaje .= "\tID: ".$_SESSION['cliente_id']."\n";
			$mensaje .= "\tIP: ".$_SERVER["REMOTE_ADDR"]."\n";
		}else{
			$mensaje .= "\tUsuario: Invitado\n";
			$mensaje .= "\tIP: ".$_SERVER["REMOTE_ADDR"]."\n";
		}

		return $mensaje;
	}

	/**
	* OBTIENE LOS DATOS DE UN ADMIN
	*/
	private function Admin(){
		$mensaje = '';

		if( isset($_SESSION['nombre']) && isset($_SESSION['id']) ){
			$mensaje .= "\tUsuario: ".$_SESSION['nombre']."\n";
			$mensaje .= "\tID: ".$_SESSION['id']."\n";
			$mensaje .= "\tIP: ".$_SERVER["REMOTE_ADDR"]."\n";
		}else{
			$mensaje .= "\tUsuario: Invitado\n";
			$mensaje .= "\tIP: ".$_SERVER["REMOTE_ADDR"]."\n";
		}

		return $mensaje;
	}
}

?>