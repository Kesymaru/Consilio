<?php 
/*
	ARCHIVO PARA EDICION DE DATOS DEL ADMIN
*/

require_once("../src/class/usuarios.php"); 

$admin =  new Admin();

?>

<script type="text/javascript">
	$('#cambiarPassword').hide();

	$('#formularioAdmin button, input:reset, .controls button').button();

	$('#cambiar').click( function(){
		if( $('#cambiarPassword').is(':visible')){
			$('#cambiarPassword').slideUp();
		}else{
			$('#cambiarPassword').slideDown();
		}
	});

	$("#formularioAdmin").validationEngine();
	$('input[placeholder]').placeholder();

	/*$('#formularioUsuario').submit(function() {
		//return false;
	});
	*/
	function resetForm(){
		$('#formularioAdmin')[0].reset();
	}

	/**
	* CAMBIO DE DATOS DEL ADMIN
	*/
	function FormEditarAdmin(){

		if ($('#formularioAdmin').validationEngine('validate')){ 
			nombre   = $('#nombre').val();
			email    = $('#email').val();
			telefono = $('#telefono').val();
			skype    = $('#skype').val();
			
			cambiar = 0;
			if( $('#cambiarPassword').is(':visible')){
				cambiar = 1;
				password    = $('#nuevoPassword1').val();
				ActualizarPassword(password);
			}

			Actualizar('ActualizarAdminDato', "nombre", nombre);
			Actualizar('ActualizarAdminDato',"email", email);
			Actualizar('ActualizarAdminDato',"telefono", telefono);
			Actualizar('ActualizarAdminDato',"skype",skype);

			if(cambiar == 1){
				notifica('Se ha cambiado el password exitosamente.');
			}
			notifica('Datos actualizados exitosamente.');
			closeDialogo();
	    }else{
	    	notificaError('Error datos invalidos.')
	    }
	}

	//para Actualizar datos
	function Actualizar(func, dato, nuevo){
		var queryParams = { "func" : func, "dato" : dato, "nuevo" : nuevo};
		  	$.ajax({
		        data:  queryParams,
		        url:   'src/ajaxUsuarios.php',
		        type:  'post',
		        success:  function (response) {
		        },
		        fail: function(){

		        }
			});
	}

	function ActualizarPassword(password){
		var queryParams = {"func" : "ActualizarAdminPassword", "password" : password };
		$.ajax({
		        data:  queryParams,
		        url:   'src/ajaxUsuarios.php',
		        type:  'post',
		        success:  function (response) {
		        },
		        fail: function(){

		        }
			});
	}

</script>

<div class="titulo">
	Datos <?php  echo $admin->getAdminDato("nombre"); ?>
</div>

<form id="formularioAdmin">

	<div class="dialogoLeft">
		
		<img id="userImage" src="<?php echo $admin->getAdminDato("imagen"); ?>">
		<br/>
		<div id="imagenLoad">
			<input type="file" name="filename" />
			<input type="hidden" name="tipoImagen" value="admin" id="tipoImagen">
			<button onclick="ajaxUpload(formularioAdmin,'src/class/ajaxupload.php?filename=filename&amp;maxSize=9999999999&amp;maxW=200&amp;fullPath=http://localhost/Consilio/images/users/&amp;relPath=../../images/users/&amp;colorR=255&amp;colorG=255&amp;colorB=255&amp;maxH=300','upload_area','Subiendo la imagen...&lt;br /&gt;&lt;img src=\'images/loader_light_blue.gif\' width=\'128\' height=\'15\' border=\'0\' /&gt;','&lt;img src=\'images/error.gif\' width=\'16\' height=\'16\' border=\'0\' /&gt; Error in Upload, check settings and path info in source code.'); return false;">Cambiar Imagen</button>
		</div>
		<div id="upload_area">

		</div>

	</div>

	<div class="dialogoRight">
		
		<table>
		<tr>
			<td>
				Nombre:
			</td>
			<td>
				<input type="text" class="validate[required]" id="nombre" title="[*no]" name="nombre" value="<?php echo $admin->getAdminDato("nombre"); ?>">
			</td>
		</tr>
		<tr>
			<td>
				Email:
			</td>
			<td>
				<input type="text" class="validate[required,custom[email]]" id="email" name="email" value="<?php echo $admin->getAdminDato("email"); ?>">
			</td>
		</tr>
		<tr>
			<td>
				Tel:
			</td>
			<td>
				<input type="number" class="validate[custom[number]]" id="telefono" name="telefono" value="<?php echo $admin->getAdminDato("telefono");?>">
			</td>
		</tr>
		<tr>
			<td>
				Skype:
			</td>
			<td>
				<input type="text" name="skype" id="skype" value="<?php echo $admin->getAdminDato("skype");?>">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="muestra">
				<button id="cambiar">Cambiar Password</button>
				<div id="cambiarPassword">
					<input class="validate[required,minSize[4]]" type="password" id="nuevoPassword1" placeholder="Nuevo Password">
					<br/>
					<input class="validate[required,equals[nuevoPassword1],minSize[4]]"  id="nuevoPassword2" type="password" placeholder="Confirmar Password">
				</div>
			</td>
		</tr>
		</table>
	    
	</div>

</form> 
<div class="controls">
	<input type="reset" value="Limpiar" onClick="resetForm()">
	<button onclick="FormEditarAdmin();">Enviar</button>
</div>