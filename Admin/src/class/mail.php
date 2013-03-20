<?php

/**
*	CLASE PARA ENVIAR MAILS
*/

class Mail {

	private $plantilla = '';
	private $disclaim = '';
	private $webmaster = 'aalfaro@77digital.com';
	private $webmasterError = 'aalfaro@77digital.com'; //notificacion de errores

	public function __construct(){
				
		date_default_timezone_set('America/Costa_Rica');

		//CREA PANTILLA HEADER
		$this->plantilla = '<!doctype html>
		<head>
			<meta charset="utf-8">
			<title>Matriz Escala</title>
		<body class="mail">
		<div class="mail">
		<br/>
		<br/>
		<br/>';

		$this->disclaim = '<div class="disclaim" >
					<span class="bold" >
					Aviso de Confidencialidad.
					</span>
					<br/>
					Este correo electrónico y/o el material adjunto es para el usu exclusivo de la persona o entidad a la que expresamente se le ha enviado y puede contener información confidencial o material privilegiado. Si usted no es el destinatario legítimo del mismo por favor reportélo inmediatamente al remitente del correo y borrelo. Cualquier revisión queda expresamente prohibido. Este correo electrónico no pretende ni debe ser considerado como constitutivo de ninguna relación legal contractual o de otra índole similar.
				</div>
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
		$header = '';

		//remitente
		if( array_key_exists('remitente', $correo) ){
			if( is_array($correo['remitente']) ){
				
				$header .= "From: " . $correo['remitente']['nombre'] . "<" .$correo['remitente']['email'].">" . "\r\n";
				
			}else{
				$header .= "From: " . $correo['remitente'] . "\r\n";
			}
						
		}else{
			$header .= "From: " . $this->webmaster . "\r\n";
		}

		//destinatarios
		if( array_key_exists('destinatario', $correo)){
			
			if( is_array($correo['destinatario'])){
				
				$header .= "To: ";
				foreach ($correo['destinatario'] as $nombre => $email) {
					$header .= "" . $nombre . "<$email> , ";
				}
				$header .= "\r\n";

			}else{
				$header .= "To: " . $correo['destinatario'] . "\r\n";
			}

		}else{
			return false;
		}

		//responder a
		if( array_key_exists('responder', $correo) ){
			
			if(is_array($correo['responder'])){
				$header .= "Reply-To: " . $correo['responder']['nombre'] . "<" .$correo['responder']['email'].">" . "\r\n";
			}else{
				$header .= "Reply-To: " . $correo['responder'] . "\r\n";
			}

		}else{
			$header .= "Reply-To: " . $this->webmaster . "\r\n";
		}
		
		//confirmacion
		if(array_key_exists('confirmacion', $correo)){
			$header .= "X-Confirm-Reading-To:" . $correo['confirmacion'] . "\r\n";
		}

		//COPIA
		if( array_key_exists('cc', $correo)){
			
			if(is_array($correo['cc'])){

				foreach ($correo['cc'] as $nombre => $email) {
					$header .= "Cc: " . $nombre ."<". $email .">". "\r\n";
				}

			}else{
				$header .= "Cc: " . $correo['cc'] . "\r\n";
			}
		}

		//BCC
		if( array_key_exists('bcc', $correo)){
			if(is_array($correo['bcc'])){
				
				foreach ($correo['bcc'] as $nombre => $email) {
					$header .= "Bcc: " . $nombre ."<". $email .">". "\r\n";
				}

			}else{
				$header .= "Bcc: " . $correo['cc'] . "\r\n";
			}
		}

		$header .= "X-Mailer: Escal Matriz" . "\r\n";

		//tiene adjuntos
		if( array_key_exists('adjunto', $correo) ){
			$strSid = $correo['adjunto'];

			$header .= "MIME-Version: 1.0\n";
			$header .= "Content-Type: multipart/mixed; boundary=\"".$strSid."\"\n\n";
			$header .= "This is a multi-part message in MIME format.\n";

			$header .= "--".$strSid."\n";
			$header .= "Content-type: text/html; charset=utf-8\n";
			$header .= "Content-Transfer-Encoding: 7bit\n\n";
		}else{
			$header .= "Content-Type: text/html; charset=utf-8\r\n";
		}

		return $header;
	}

	/**
	* CONFIGURA EL FOOTER
	* @param $correo -> array con la configuracion
	*/
	private function footer($correo){
			
		$footer = '
				<table class="footer">
					<tr>
						<td rowspan="7" class="direccion" >';

		if(array_key_exists("nombreRemitente", $correo)){
			$footer .= $correo['nombreRemitente'];
		}

		if(array_key_exists("tituloRemitente", $correo)){
				$footer .= ". ".$correo['tituloRemitente'].", ";
			}

		$footer .= '
						Consultores Escala<br/>
						Oficentro Ejecutivo la sabana Torre 7, Piso 2<br/>
						Sabana Sur, San José, Costa Rica
						</td>
					</tr>';
					

		if(array_key_exists("mobile", $correo)){
			$footer .= '<tr>
						<td>
							Mobile
						</td>
						<td>
							'.$correo['mobile'].'
						</td>
					</tr>';
		}

		if(array_key_exists("telefono", $correo)){
			$footer .= '<tr>
							<td>
								Oficina
							</td>
							<td>
							'.$correo['telefono'].'
							</td>
						</tr>';
		}
							

		if(array_key_exists("fax", $correo)){
			$footer .= '<tr>
							<td>
								Fax
							</td>
							<td coslpan="2">
								'.$correo['fax'].'
							</td>
						</tr>';
		}

		if(array_key_exists("skype", $correo)){
			$footer .= '<tr>
							<td>
								Skype
							</td>
							<td coslpan="2">
								'.$correo['skype'].'
							</td>
						</tr>';
		}

		if(array_key_exists("remitente", $correo)){
			if( is_array($correo['remitente']) ){

				$footer .= '<tr>
								<td>
									Email
								</td>
								<td>
									'.$correo['remitente']['email'].'
								</td>
							</tr>';
			}else{

				$footer .= '<tr>
								<td>
									Email
								</td>
								<td>
									'.$correo['remitente'].'
								</td>
							</tr>';
			}
		}
							
		$footer .=   '<tr>
						<td>
							Website:
						</td>
						<td>
							<a href="'.$_SESSION['matriz'].'">matricez.com</a>
						</td>
					</tr>
					</table>';

		$footer .= $this->disclaim;

		return $footer;
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
			$mensajeFinal = $this->plantilla . $mensajeFinal . $this->footer($correo);

			if( array_key_exists('destinatario', $correo) ){

				$to = '';
				foreach ($correo['destinatario'] as $nombre => $email) {
					$to .= $nombre." <".$email.">, ";
				}

				$mensajeFinal = $this->mailStyle($mensajeFinal);

				if( mail($to, $correo['asunto'], $mensajeFinal, $this->header($correo)) ){
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
	* OBTIENE EL CORREO COMPUESTO
	* @param $carreo -> array con datos, para, nombre, link, imagen, mensaje, notas
	*/
	public function getCorreo($correo){
				
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
					$userId = $correo['userId'];
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
							<img class="logoCliente" src="'.$_SESSION['home'].'/'.$correo['imagen'].'" alt="'.$nombre.'" title="'.$nombre.'">
						</td>';
			}

			$mensajeFinal.=	'
					</tr>	
				</table>
				';

			//mensaje armado
			$mensajeFinal = $this->plantilla . $mensajeFinal . $this->footer($correo);

			if( array_key_exists('destinatario', $correo) ){

				$to = '';
				foreach ($correo['destinatario'] as $nombre => $email) {
					$to .= $nombre." <".$email.">, ";
				}

				$mensajeFinal = $this->mailStyle($mensajeFinal);

				return $mensajeFinal;

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

			'class="titulo"' => 'style="text-align: center; color: #FFFFFF; font-weight: bold;"',

			'class="footer"' => 'style="font-size: 16px; border: 0; text-align: left; border: 0px; margin: 20px auto; background-color: #f4f4f4;"',

			'class="direccion"' => 'style="padding-right: 20px;"',

			'class="disclaim"' => 'style="width: 90%; display: block; border-top: 1px solid #dedede; text-align: left !important; font-size: 14px; margin-bottom: 10px; padding-left: 10px; padding-right: 10px; background-color: #f4f4f4; padding-top: 5px; margin-left: auto; margin-right: auto;"',

			'class="bold"' => 'style="font-weight: bold !important;"',

			);

		foreach ($tema as $class => $style) {
			$mensaje = str_replace( $class, $class.' '.$style, $mensaje);
		}

		return $mensaje;
	}

	/**
	* ENVIA UN MAIL PERSONALIZADO
	* @param string $destinatario
	* @param array $cc
	* @param array $bcc
	* @param string $mail -> mensaje
	*/
	function enviar($remitente, $destinatario, $cc, $bcc, $asunto, $mail){
		$correo = array();
		$correo['remitente'] = $remitente;
		$correo['destinatario'] = $destinatario;

		if($cc != ''){
			$correo['cc'] = $cc;
		}
		if( $bcc != ''){
			$correo['bcc'] = $bcc;
		}
		if( $asunto == ''){
			$asunto == "Notificacion Escala Consultores";
		}

		$header = $this->header($correo);

		if( !mail($destinatario, $asunto, $mail, $header) ){
			echo "Erro: no se pudo enviar el mail.<br/>Detalles:<br/>Para: ".$destinatario;
			echo "<br/>De: ".$remitente."<br/>Asunto: ".$asunto;
			return false;
		}else{
			return true;
		}
	}

	/**
	* ENVIA CORREO CON ADJUNTO
	* @param string $destinatario
	* @param array $cc
	* @param array $bcc
	* @param string $mail -> mensaje
	* @param string $nombre -> nombre del archivo
	* @param string $link -> link del archivo
	*/
	public function enviarAjunto($remitente, $destinatario, $cc, $bcc, $asunto, $mail, $nombre, $link){
		$strSid = md5(uniqid(time()));

		$correo = array();
		$correo['adjunto'] = $strSid;
		$correo['remitente'] = $remitente;
		$correo['destinatario'] = $destinatario;

		if($cc != ''){
			$correo['cc'] = $cc;
		}
		if( $bcc != ''){
			$correo['bcc'] = $bcc;
		}
		if( $asunto == ''){
			$asunto == "Notificacion Escala Consultores";
		}

		$header = $this->header($correo);

		$header .= $mail."\n\n"; //agrega el mensaje

		$archivo = chunk_split(base64_encode(file_get_contents( $link ) ) ); 
		$header .= "--".$strSid."\n";
		$header .= "Content-Type: application/octet-stream; name=\"".$nombre."\"\n"; 
		$header .= "Content-Transfer-Encoding: base64\n";
		$header .= "Content-Disposition: attachment; filename=\"".$nombre."\"\n\n";
		$header .= $archivo."\n\n"; //agrega el archivo adjunto

		if( !mail($destinatario, $asunto, null, $header) ){

			echo "Erro: no se pudo enviar el mail con el archivo adjunto.<br/>Detalles:<br/>Para: ".$destinatario;
			echo "<br/>De: ".$remitente."<br/>Asunto: ".$asunto;
			
			return false;
		}else{
			return true;
		}
	}

	/**
	 * ENVIA UN MAIL, NOTIFICA UN ERROR
	 * $error -> mensaje del error
	 */
	function errorMail($error){

		if( !mail($this->webmasterError,"ERROR", $error) ){
			$_SESSION['error'] = "El envio del email de registro ha fallado!<br/>Por favor comuniquese con ".$this->webmasterError;
		}
	}
}


?>