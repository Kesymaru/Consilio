<?php
/**
* PERMUITE COMPONER MAILS PARA ALGUNA FUNCIONALIDAD
*/
require_once("class/mail.php");
require_once("class/session.php");
require_once("class/proyectos.php");
require_once("class/usuarios.php");

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

	$componer .= '<form id="FormularioProyectoMail" >
					<table class="tabla-mail">
						<tr>
							<td colspan="2" class=titulo"">
								Notificacion '.$proyectoNombre.'
							</td>
						</tr>
						<tr>
							<td>
								Para
							</td>
							<td>
								'.$correo['nombre'].' "' .$correo['email'].'"
							</td>
						</tr>
						<tr>
							<td colspan="2" >
								<textarea name="mail" id="mail">'.$mailComponer.'</textarea>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<button type="button" onClick="parent.$.fancybox.close();">Cancelar</button>
								<input type="reset" value="Limpiar" >
								<input type="submit" value="Enviar" >
							</td>
						</tr>
					</table>
				  </form>
			<script>
				var alto = $("html").height() * 0.7
				notifica(alto);
				
				$("#FormularioProyectoMail").css(\'height\', alto+"px");
				alto = alto * 0.5;
				notifica(alto);
				EditorAlto(\'mail\', alto);
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