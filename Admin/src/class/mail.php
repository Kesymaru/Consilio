<?php

/**
*	CLASE PARA ENVIAR MAILS
*/

class Mail {

	private $heades = '';
	private $plantilla = '';
	private $plantillaFooter = '';
	private $webmaster = 'webmaster@matricez.com';
	private $webmasterError = 'aalfaro@77digital.com'; //notificacion de errores

	public function __construct(){
				
		date_default_timezone_set('America/Costa_Rica');

		//configuracion headers del email
		$this->headers .= "From: " . $this->webmaster . "\r\n";
		$this->headers .= "Reply-To: " . $this->webmaster . "\r\n";
		$this->headers .= "X-Mailer: Matricez" . "\r\n";
		$this->headers .= "Content-Type: text/html; charset=utf-8\r\n";

		//CREA PANTILLA HEADER
		$this->plantilla = '<!doctype html>
		<head>
			<meta charset="utf-8">
			<title>Matriz Escala</title>
			<style type="text/css">
			
			html, body{
				background-color: #f4f4f4;
			}
			.tabla{
				border: 1px solid #747273;
				margin: 0 auto;
				border-collapse: collapse;
				box-shadow: 0 0 2px 1px #747273;
				padding: 0;
				min-width: 500px;
				font-size: 20px;
			}
			.tabla td{
				padding: 10px;
			}
			.asunto th{
				text-align: center;
				background-color: #6FA414;
				text-align: center !important;
				font-size: 22px;
				font-weight: bold;
				padding: 10px;
				color: #fff;
			}
			.contenido{
				text-align: left;
			}
			.link{
				background-color: #a1ca4a;
				text-align: center;
				vertical-align: middle;
			}
			.logo{
				display: inline-block;
				float: left;
				max-height: 80px;
			}
			.logoCliente{
				display: inline-block;
				float: right;
				max-height: 80px;
				max-width: 250px;
			}
			.footer{
				font-size: 12px;
				width: 100%;
				display: block;
				vertical-align: middle;
			}
			.footer div{
				font-size: 12px;
				border: 0;
				margin: 0 auto;
				display: table;
				text-align: center;
			}
			.footer div hr{
				min-width: 350px;
				border: 1px solid #747273;
				vertical-align: middle;
			}
			</style>
		</head>

		<body>
		<br/>
		<br/>
		<br/>';

		//CREA PLANTILLA FOOTER
		$this->plantillaFooter = '
		<br/>
		<br/>
		<br/>
		</body>
		<div class="footer">
			<div>
				Este mail fue generado automaticamente.<br/>
				Para mayor informacion y ayuda:
				<hr>
				email: '.$this->webmaster.'
				<br/>
				website: <a href="'.$_SESSION['matriz'].'">matricez.com</a>
				<br/>
				tel: (506) 123456
			</div>
			<br/>
		</div>
		</body>
		</html>';

	}

	/**
	* ENVIA UN CORREO GENERICO
	* @param $carreo -> array con datos, para, nombre, link, imagen, mensaje, notas
	*/
	public function correo($correo){
				
		if(!empty($correo)){

			//TITULO CON ASUNTO
			if(array_key_exists('asunto', $correo)){
				$mensajeFinal = '
				<table class="tabla">
					<tr class="asunto">
						<th colspan="2" >
							'.$correo['asunto'].'
						</th>
					</tr>';
			}else{
				$mensajeFinal = '
				<table class="tabla">
					<tr class="asunto">
						<th colspan="2" >
							Notificacion
						</th>
					</tr>';
			}

			//CONTENIDO
			$mensajeFinal .= '<tr class="contenido">
						        <td colspan="2">';

			//titulo mensaje
			if(array_key_exists('nombre', $correo)){
				$nombre = $correo['nombre'];
				$mensajeFinal .= "Hola, $nombre:<br/><br/>";
			}else{
				$mensajeFinal .= "Estimado usuario:<br/><br/>";
			}

			//mensaje REQUERIDO
			if(array_key_exists('mensaje', $correo)){
				$mensajeFinal .= $correo['mensaje'];
			}else{
				echo "Error: mail.php correo() se necesita un mensaje para enviar el mail.";
				return false; //requerido
			}

			if(array_key_exists('link', $correo)){
				$link = $_SESSION['matriz'].$correo['link'];
			}else{
				if(array_key_exists('userId', $correo)){
					$usarId = $correo['userId'];
					$link = $_SESSION['matriz'].'/login.php?user='.$userId;
				}else{
					$link = $_SESSION['matriz'].'/login.php';
				}
			}
			//link
			$mensajeFinal .= '<br/>
					 	<br/><a href="'.$link.'" >
							'.$link.'
						</a>

					</td>
					</tr>'; //fin contenido

			//footer del mensaje 
			$mensajeFinal .= '
					<tr>
						<td>
							
							<img class="logo" src="'.$_SESSION['matriz'].'/images/logoMail.png" title="Matriz" alt="Matriz">
						</td>';

			//imagen del cliente
			if( array_key_exists('imagen', $correo) ){

				$mensajeFinal .= '
						<td>
							<img class="logoCliente" src="'.$_SESSION['home'].$correo['imagen'].'" alt="'.$nombre.'" title="'.$nombre.'">
						</td>';
			}

			$mensajeFinal.=	'
					</tr>	
				</table>
				';

			//mensaje armado
			$mensajeFinal = $this->plantilla . $mensajeFinal . $this->plantillaFooter;

			if( array_key_exists('email', $correo) ){

				if( mail($correo['email'], $correo['asunto'], $mensajeFinal, $this->headers) ){
					return true;				
				}else{
					echo "Error: no se pudo enviar el mail.<br/>A la direccion: ".$correo['email'];
					return false;
				}

			}else{
				echo 'Error: no se especifica un destinatario o este no es valido.<br/>';
				return false;
			}
			
		}else{
			echo "Error: mail.php datos requeridos no enviados, $correo esta vacio.";
			return false;
		}
	}

	/**
	 * ENVIA UN MAIL, NOTIFICA UN ERROR
	 * $error -> mensaje del error
	 */
	function errorMail($error){

		if( !mail($this->webmasterError,  "ERROR", $error, $this->headers) ){
			$_SESSION['error'] = "El envio del email de registro ha fallado!<br/>Por favor comuniquese con ".$this->webmasterError;
		}
	}

}
if( !isset($_SESSION['home'])){
	session_start();

$protocolo = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$dominio = $_SERVER['HTTP_HOST'];

$_SESSION['home'] = $protocolo.$dominio.'/matrizescala/Admin';
$_SESSION['matriz'] = $protocolo.$dominio.'/matrizescala';

}

if ( function_exists( 'mail' ) )
{
    echo 'mail() is available';
}
else
{
    echo 'mail() has been disabled';
}


$mail = new Mail();
$correo = array();
$correo['nombre'] = 'andrey test';
$correo['mensaje'] = 'test de mensaje';
$correo['asunto'] = 'test mail';
$correo['link'] = '/index.poryecto?11';
$correo['email'] = 'aalfaro@77digital.com';
$correo['userId'] = 1;


if( !$mail->correo($correo)){
	echo 'no enviado';
}

if(!mail('aalfaro@77digital.com', 'testting mail', 'prueba de mail')){
	echo 'error mail no enviado';s
}



?>