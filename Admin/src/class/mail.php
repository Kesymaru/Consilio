<?php

/**
*	CLASE PARA ENVIAR MAILS
*/

class Mail {

	private $heades = '';
	private $plantilla = '';
	private $plantillaFooter = '';
	private $webmaster = 'aalfaro@77digital.com';
	private $webmasterError = 'aalfaro@77digital.com'; //notificacion de errores

	public function __construct(){
				
		date_default_timezone_set('America/Costa_Rica');

		//CREA PANTILLA HEADER
		$this->plantilla = '<!doctype html>
		<head>
			<meta charset="utf-8">
			<title>Matriz Escala</title>
			<style type="text/css">
			
			.mail{
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
			.tabla-td{
				padding: 10px;
			}
			.asunto{
				text-align: center;
				background-color: #6FA414;
				text-align: center !important;
				font-size: 22px;
				font-weight: bold;
				padding: 10px;
				color: #FFFFFF;
			}
			.titulo{
				text-align: center;
				color: #FFFFFF;
				font-weight: bold;
			}
			.contenido{
				text-align: left;
				background-color: #FFFFFF;
			}
			.link{
				background-color: #a1ca4a;
				text-align: center;
				vertical-align: middle;
			}
			.contenidoFooter{
				background-color: #FFFFFF;
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
			.footer-content{
				font-size: 12px;
				border: 0;
				margin: 0 auto;
				display: table;
				text-align: center;
			}
			.footer-content hr{
				min-width: 350px;
				border: 1px solid #747273;
				vertical-align: middle;
			}
			</style>
		</head>

		<body class="mail">
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
			<div class="footer-content">
				Este mail fue generado automaticamente.<br/>
				Para mayor informacion y ayuda:
				<hr class="hr">
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
	* CONFIGURACION DEL HEADER
	* @param $correo -> array con la configuracion y datos para el header
	* @retunr $header -> header conmpuesto
	*/
	private function header($correo){

		if( array_key_exists('de', $correo) ){
			$this->headers .= "From: " . $correo['de'] . "\r\n";

			if( array_key_exists('responder', $correo) ){
				$this->headers .= "Reply-To: " . $correo['responder'] . "\r\n";
			}else{
				$this->headers .= "Reply-To: " . $correo['de'] . "\r\n";
			}

		}else{
			$this->headers .= "From: " . $this->webmaster . "\r\n";
			$this->headers .= "Reply-To: " . $this->webmaster . "\r\n";
		}
		
		if(array_key_exists('confirmacion', $correo)){
			$this->headers .= "X-Confirm-Reading-To:" . $correo['confirmacion'] . "\r\n";
		}

		$this->headers .= "X-Mailer: Matricez" . "\r\n";
		$this->headers .= "Content-Type: text/html; charset=utf-8\r\n";
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
						<th colspan="2">
							'.$correo['asunto'].'
						</th>
					</tr>';
			}else{
				$mensajeFinal = '
				<table class="tabla">
					<tr>
						<th colspan="2" class="asunto" >
							Notificacion
						</th>
					</tr>';
			}

			//CONTENIDO
			$mensajeFinal .= '<tr class="contenido">
						        <td colspan="2" class="tabla-td">';

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
					<tr class="contenidoFooter">
						<td class="td-logo">
							
							<img class="logo" src="'.$_SESSION['matriz'].'/images/logoMail.png" title="Matriz" alt="Matriz">
						</td>';

			//imagen del cliente
			if( array_key_exists('imagen', $correo) ){

				$mensajeFinal .= '
						<td class="td-logoCliente">
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

				$mensajeFinal = $this->mailStyle($mensajeFinal);
				$this->header($correo);
				
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

	/**
	* REMPLAZE LAS CLASES POR EL ESTILO DIRECTO
	* @param $mensaje -> texto del mensaje a remplazar clases
	* @return $mensaje con estilo directo
	*/
	private function mailStyle($mensaje){

		$tema = array(
			'class="mail"' => 'style="background-color: #f4f4f4;"',

			'class="tabla"' => 'style="border: 1px solid #747273; border-collapse: collapse; box-shadow: 0 0 2px 1px #747273; padding: 0; width:90%; margin: 0 auto; font-size: 20px;"',

			'class="tabla-td"' => 'style="padding: 10px;"',

			'class="asunto"' => 'style="text-align: center; background-color: #6FA414; font-size: 22px; font-weight: bold; padding: 10px; color: #FFFFFF;"',

			'class="contenido"' => 'style="text-align: left; background-color: #FFFFFF;"',

			'class="link"' => 'style="background-color: #a1ca4a; text-align: center; vertical-align: middle;"',

			'class="contenidoFooter"' => 'style="background-color: #FFFFFF;"',

			'class="logo"' => 'style="display:block; float: left; height: 80px; padding-left: 10px;  padding-bottom: 10px;"',
			
			'class="td-logo"' => 'style="text-align: left; height: 80px;"',

			'class="logoCliente"' => 'style="display:block; float: right; height: 80px; width: 250px; padding-right: 10px; padding-bottom: 10px;"',

			'class="td-logoCliente"' => 'style="text-align: right; height: 80px;"',

			'class="footer"' => 'style="font-size: 12px; width: 100%; display: block; vertical-align: middle; text-align: center; background-color: #f4f4f4;"',

			'class="titulo"' => 'style="text-align: center; color: #FFFFFF; font-weight: bold;"',

			'class="footer-content"' => 'style="font-size: 12px; border: 0; text-align: center"',

			'class="hr"' => 'style="width:200px; border: 1px solid #747273;vertical-align: middle;"',

			);

		foreach ($tema as $class => $style) {
			$mensaje = str_replace( $class, $class.' '.$style, $mensaje);
		}

		return $mensaje;
	}


}


?>