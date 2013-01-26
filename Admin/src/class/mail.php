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
		session_start();
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
			.titulo{
				background-color: #6FA414;
				text-align: center !important;
				font-size: 22px;
				font-weight: bold;
				color: #fff;
			}
			.link{
				background-color: #a1ca4a;
				text-align: center;
				vertical-align: middle;
			}
			.logo{
				float: right;
				height: 80px;
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
				website: <a href="'.$_SESSION['home'].'">matricez.com</a>
				<br/>
				tel: (506)123456
			</div>
			<br/>
		</div>
		</body>
		</html>';

	}

	/**
	* ENVIA EL MAIL
	* @param $para -> string mail de destino
	* @param $asunto -> string subject del mail
	* @param $mensake -> mensaje compuesto con la plantilla
	* @return true si se envia
	* @return false sino
	*/
	private function enviar($para, $asunto, $mensaje){
		if(mail($para, $asunto, $mensaje, $this->headers)){
			return true;
		}else{
			return false;
		}
	}

	/**
	* MAIL PARA NOTIFICA UN NUEVO REGISTRO CON SU PASSWORD
	* @param $para -> direccion a la cual enviar el mail
	* @param $usuario -> usuario nuevo registro
	* @param $password -> passwod del nuevo registro
	*/
	public function mailRegistro($para, $usuario, $password){

		//crea mensaje
		$mensaje .= '
		<table class="tabla">
			<tr class="titulo">
				<td colspan="2">
					Registro Exitoso
				</td>
			</tr>
			<tr class="fila">
				<td>
					Usuario:
				</td>
				<td>
					'.$usuario.'
				</td>
			</tr>
			<tr class="fila">
				<td>
					Contrasena:
				</td>
				<td>
					'.$password.'
				</td>
			</tr>
			<tr>
				<td colspan="2" class="link">
					<a href="'.$_SESSION['home'].'/login.php??usuario='.$usuario.'&reset=1" >
						<img scr="'.$_SESSION['home'].'/images/mailIngresarBoton.png" title="Ingresar" alt="Ingresar">
					</a>
					<img class="logo" src="http://admin.77digital.com/Consilio/images/logoMail.png" title="Matriz" alt="Matriz">
				</td>
			</tr>	
		</table>';

		$mensaje = $this->plantilla . $mensaje . $this->plantillaFooter;

		//envia mail
		if(!$this->enviar($para, "Registro Matriz Escala", $mensaje)){
			echo 'Error: no se podo enviar el mail de confirmacion del registro.<br/>Por favor comuniquese con:<br/>'.$this->$webmaster;
		}
	}

	/**
	* MAIL PARA CUANDO SE RESETEA UN PASSWORD
	* @param $para -> mail distinatario
	* @param $nombre -> nombre del usuario
	* @param $usuario -> usuario
	* @param $password -> nuevo password y sin encriptar
	*/
	public function mailResetPassword($para, $nombre, $usuario, $password){

		$this->plantilla .= '
		<table class="tabla">
			<tr class="titulo">
				<td colspan="2">
					Nueva Contraseña
				</td>
			</tr>
			<tr>
				<td colspan="2">
					Hola, '.$nombre.':<br/>
					Hace poco has pedido cambiar tu contraseña de Matricez.
				</td>
			</tr>
			<tr class="fila">
				<td>
					Usuario:
				</td>
				<td>
					'.$usuario.'
				</td>
			</tr>
			<tr class="fila">
				<td>
					Contrasena:
				</td>
				<td>
					'.$password.'
				</td>
			</tr>
			<tr>
				<td colspan="2" class="link">
					<a href="'.$_SESSION['home'].'/login.php?usuario='.$usuario.'&reset=1" >
						<img scr="'.$_SESSION['home'].'/images/mailIngresarBoton.png" title="Ingresar" alt="Ingresar">
					</a>
					<img class="logo" src="'.$_SESSION['home'].'/images/logoMail.png" title="Matriz" alt="Matriz">
				</td>
			</tr>	
		</table>
		';

		$this->plantilla .= $this->plantillaFooter;
		
		//envia mail
		$this->enviar($para, "Nueva Contraseña Matricez");
	}

	/**
	* ENVIA UN CORREO GENERICO
	* @param $para -> direccion diestinatario
	* @param $asunto -> asunto del mail
	* @param $nombre -> nombre de usuario
	* @param $
	*/
	public function correo($para, $asunto, $nombre, $link = "", $mensaje){
		
		if($de == ""){
			$de = $this->webmaster;
		}

		if($link == ""){
			$link = '/login.php';
		}

		$mensajeFinal = '
		<table class="tabla">
			<tr class="titulo">
				<td colspan="2">
					Nueva Contraseña
				</td>
			</tr>
			<tr>
				<td colspan="2">';
		if( $nombre != ""){
			$mensajeFinal .= "Hola, $nombre:<br/>";
		}
					
		$mensajeFinal .= $mensaje.'
				</td>
			</tr>
			<tr>
				<td colspan="2" class="link">
					<a href="'.$_SESSION['matriz'].$link.'" >
						<img scr="'.$_SESSION['matriz'].'/images/mailIngresarBoton.png" title="Ingresar" alt="Ingresar">
					</a>
					<img class="logo" src="'.$_SESSION['matriz'].'/images/logoMail.png" title="Matriz" alt="Matriz">
				</td>
			</tr>	
		</table>
		';

		$mensajeFinal = $this->plantilla . $mensajeFinal . $this->plantillaFooter;
		
		if(!enviar($para, $asunto, $mensajeFinal)){
			echo "Error: no se pudo enviar el mail.";
			return false;
		}
	}

	/**
	 * ENVIA UN MAIL, NOTIFICA UN ERROR
	 * $error -> mensaje del error
	 */
	function errorMail($error){

		if(!mail($this->webmasterError,  "ERROR", $error, $this->headers)){
			$_SESSION['error'] = "El envio del email de registro ha fallado!<br/>Por favor comuniquese con ".$this->webmasterError;
		}
	}

}

?>