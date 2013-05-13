<?php
/**
* PERMUITE COMPONER MAILS PARA ALGUNA FUNCIONALIDAD
*/
require_once("class/mail.php");
require_once("class/session.php");
require_once("class/proyectos.php");
require_once("class/usuarios.php");
require_once("class/exportar.php");

//SEGURIDAD
$session = new Session();
if( !$session->Logueado() ){
	header('Location: ../login.php');
}

if( isset($_POST['func']) ){

	switch ($_POST['func']) {
		case 'ProyectoMail':
			if( isset($_POST['proyecto']) ){
				ProyectoMail($_POST['proyecto']);
			}
			break;
		
		case 'ProyectoLink':
			if( isset($_POST['proyecto']) ){
				ProyectoLink($_POST['proyecto']);
			}

		case 'ProyectoMailPdf':
			if( isset($_POST['proyecto']) ){
				ProyectoMailPdf($_POST['proyecto']);
			}
			break;

		// ENVIOS
		case 'EnviarMail':
			if( isset($_POST['remitente']) && isset($_POST['destinatario']) && isset($_POST['asunto']) && isset($_POST['mail']) ){
				
				$cc = '';
				if(isset($_POST['cc'])){
					$cc = $_POST['cc'];
				}

				$bcc = "";
				if(isset($_POST['bcc'])){
					$bcc = $_POST['bcc'];
				}

				$mail = new Mail();
				$mail->enviar($_POST['remitente'], $_POST['destinatario'], $cc, $bcc, $_POST['asunto'], $_POST['mail']);

			}else{
				echo "Error: componerMail.php EnviarMail no se especifican remitente, destinatario, asunto o mensaje para enviar.<br/>";
			}
			break;

		case 'EnviarMailAdjunto':
			if( isset($_POST['proyecto']) && isset($_POST['remitente']) && isset($_POST['destinatario']) && isset($_POST['asunto']) && isset($_POST['mail']) ){
				
				$cc = '';
				if(isset($_POST['cc'])){
					$cc = $_POST['cc'];
				}

				$bcc = "";
				if(isset($_POST['bcc'])){
					$bcc = $_POST['bcc'];
				}
				$exportar = new Exportar();

				if( $link = $exportar->ExportarPdfClienteFile( $_POST['proyecto'] ) ){
					$nombre = $exportar->getProyectoNombre();
					$nombre = str_replace('.', '', $nombre); //elimina los puntos
					$nombre = str_replace(' ', '_', $nombre); //elimina los espacios en blanco
					$nombre = $nombre.'.PDF';

					$mail = new Mail();
					$mail->enviarAjunto($_POST['remitente'], $_POST['destinatario'], $cc, $bcc, $_POST['asunto'], $_POST['mail'], $nombre, $link);
				}else{
					echo "Error: componerMail.php EnviarMailAdjunto no se obtubo el pdf";
				}

			}else{
				echo "Error: componerMail.php EnviarMailAdjunto no se especifican remitente, destinatario, asunto o mensaje para enviar.<br/>";
			}
			break;
	}
	
}else{
	echo "<script> notificaError('Error: componerMail.php no se espeficican los parametros requeridos'); </script>";
}

/*********** COMPONER MAIL PARA PROYECTOS **********/

/**
* PERMITE COMPONER EL MAIL QUE SERA ENVIADO PARA NOTIFICAR SOBRE UN PROYECTO
* @param $id -> id del proyecto a componer
*/
function ProyectoMail($id){
	$componer = '';

	$registros = new Proyectos();
	$cliente = new Cliente();

	$clienteId = $registros->getProyectoDato("cliente",$id);
	$proyectoNombre = $registros->getProyectoDato("nombre",$id);

	$mail = new Mail();
	$correo = $cliente->getCorreo($clienteId);

	$correo['asunto'] = "Proyecto: $proyectoNombre";
	$correo['mensaje'] = "Su proyecto ya se encuentra disponible en la matriz, puede acceder desde este momento en el siguiente enlace:";
	$correo['link'] = "/index.php?proyecto=$id";
	
	$mailComponer = $mail->getCorreo($correo);

	$componer .= '<form id="FormularioProyectoMail" enctype="multipart/form-data" method="post" action="src/componerMail.php" >
					<input type="hidden" name="func" value="EnviarMail" >
					<input type="hidden" name="proyecto" value="'.$id.'" >
					<input type="hidden" name="remitente" value="'.$_SESSION['email'].'" >
					<div class="titulo">
						Componer Notificacion '.$proyectoNombre.'
					</div>
					
					<table class="tabla-mail">
						<tr>
							<td class="para">
								Para
							</td>
							<td class="destinatario">
								<input id="destinatario" name="destinatario" value="';

	if( is_array( $correo['destinatario'] )){
		foreach ($correo['destinatario'] as $nombre => $email) {
			$componer .= $nombre." <$email>, ";
		}
	}else{
		$componer .= $correo['destinatario'];
	}
		
	$componer .='" > <!-- end input -->
							</td>
						</tr>
						<tr>
							<td class="para">
								Cc
							</td>
							<td class="destinatario">
								<input type="text" name="cc" id="cc" >
							</td>
						</tr>
						<tr>
							<td class="para">
								Bcc
							</td>
							<td class="destinatario">
								<input type="text" name="bcc" id="bcc" >
							</td>
						</tr>
						<tr>
							<td class="para">
								Asunto
							</td>
							<td class="destinatario">
								<input type="text" name="asunto" id="asunto" value="Proyecto: '.$proyectoNombre.'" >
							</td>
						</tr>
					</table>

					<textarea id="mail" name="mail">
							'.$mailComponer.'
					</textarea>

					<div class="table-botonera">
						<button type="button" onClick="parent.$.fancybox.close();">Cancelar</button>
						<input type="reset" value="Limpiar" >
						<input type="submit" value="Enviar" onClick="EditorUpdateContent();" >
					</div>

				  </form>
			<script>

				FormularioProyectoMail();

			</script>';

	echo $componer;
}

/**
* PERMITE COMPONER EL MAIL QUE SERA ENVIADO CON EL PDF DEL PROYECTO
* @param $id -> id del proyecto a componer
*/
function ProyectoMailPdf($id){
	$componer = '';

	$registros = new Proyectos();
	$cliente = new Cliente();

	$clienteId = $registros->getProyectoDato("cliente",$id);
	$proyectoNombre = $registros->getProyectoDato("nombre",$id);

	$mail = new Mail();
	$correo = $cliente->getCorreo($clienteId);

	$correo['asunto'] = "Informe: $proyectoNombre";
	$correo['mensaje'] = "Se ha generado el informe para el proyecto $proyectoNombre, el cual se adjunta.";
	$correo['link'] = 'no';

	$mailComponer = $mail->getCorreo($correo);

	$componer .= '<form id="FormularioProyectoMail" enctype="multipart/form-data" method="post" action="src/componerMail.php" >
					<input type="hidden" name="func" value="EnviarMailAdjunto" >
					<input type="hidden" name="proyecto" value="'.$id.'" >
					<input type="hidden" name="remitente" value="'.$_SESSION['email'].'" >
					<div class="titulo">
						Informe '.$proyectoNombre.'
					</div>
					
					<table class="tabla-mail">
						<tr>
							<td class="para">
								Para
							</td>
							<td class="destinatario">
								<input id="destinatario" name="destinatario" value="';

	if( is_array( $correo['destinatario'] )){
		foreach ($correo['destinatario'] as $nombre => $email) {
			$componer .= $nombre." <$email>, ";
		}
	}else{
		$componer .= $correo['destinatario'];
	}
		
	$componer .='" > <!-- end input -->
							</td>
						</tr>
						<tr>
							<td class="para">
								Cc
							</td>
							<td class="destinatario">
								<input type="text" name="cc" id="cc" >
							</td>
						</tr>
						<tr>
							<td class="para">
								Bcc
							</td>
							<td class="destinatario">
								<input type="text" name="bcc" id="bcc" >
							</td>
						</tr>
						<tr>
							<td class="para">
								Asunto
							</td>
							<td class="destinatario">
								<input type="text" name="asunto" id="asunto" value="Informe: '.$proyectoNombre.'" >
							</td>
						</tr>
					</table>

					<textarea id="mail" name="mail">
							'.$mailComponer.'
					</textarea>
					<div class="table-botonera">
						<button type="button" onClick="parent.$.fancybox.close();">Cancelar</button>
						<input type="reset" value="Limpiar" >
						<input type="submit" value="Enviar" onClick="EditorUpdateContent();" >
					</div>

				  </form>
			<script>

				FormularioProyectoMail();

			</script>';

	echo $componer;
}

/**
* COMPONE EL LINK DE UN PROYECTO PARA QUE PUEDA SER COPIADO
* @param $id -> id proyecto
*/
function ProyectoLink($id){
	$registros = new Proyectos();
	$proyectoNombre = $registros->getProyectoDato("nombre",$id);

	$link = '<div class="titulo">'.$proyectoNombre.'</div>
			<div class="link-copiar">
			<div>';

	$link .= $_SESSION['matriz'].'/index.php?proyecto='.$id;

	$link .= '</div>
			</div>';

	echo $link;
}


?>