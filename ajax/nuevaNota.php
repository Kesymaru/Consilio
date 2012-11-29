<?php 
	require_once("../db.php"); 
/*
	para crear un nueva nota del proyecto
*/

//logueo
if( !isset($_SESSION['logueado']) ){
	$home = $_SESSION['home']."/login.php";
	header('Location: '.$home);
	exit;
}

?>
<script type="text/javascript">

	$('#formularioNuevaNota button, input:reset, .controls button').button();

	$("#formularioNuevaNota").validationEngine({
		inlineValidation: true
	});

	$('input[placeholder]').placeholder();
	$('textarea[placeholder]').placeholder();

	/*$('#formularioNuevaNota').submit(function() {
		//return false;
	});
	*/
	function resetForm(){
		$('#formularioNuevaNota')[0].reset();
	}

</script>

<div class="titulo">
	Nueva Nota
</div>

<form id="formularioNuevaNota">

	<div class="center">
		Nota
		<br/><br/>
		<textarea class="validate[required,maxSize[600]]" id="nota" name="nota" placeholder="Nota para el proyecto"></textarea>
	</div>

</form> 
<div class="controls">
	<input type="reset" value="Limpiar" onClick="resetForm()">
	<button onclick="nuevaNota();">Enviar</button>
</div>