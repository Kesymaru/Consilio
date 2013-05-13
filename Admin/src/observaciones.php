<?php
/**
* PARA CREAR UNA OBSERVACION DE UNA CATEGORIA EN UN CLIENTE
*/

require_once("class/registros.php");

if(isset($_GET['proyecto']) && isset($_GET['categoria'])){
	Cabecera();
	Observacion( $_GET['proyecto'], $_GET['categoria'] );
	Cierre();
}

if( isset($_POST['func'])){
	switch ($_POST['func']) {
		
		case 'RegistrarObservacion':
			if(isset($_POST['proyecto']) && isset($_POST['observacion']) && isset($_POST['categoria']) ){
				RegistrarObservacion($_POST['proyecto'], $_POST['categoria'], $_POST['observacion']);
			}
			break;
		
		case 'Reset':
			if(isset($_POST['proyecto']) && isset($_POST['categoria']) ){
				getObservacion( $_POST['proyecto'], $_POST['categoria'] );		
			}
			break;
	}
	
}

/**
* CABECERAS HTML
*/
function Cabecera(){
	?>

<html>
<head>
	<meta charset="utf-8">

	<link rel="stylesheet" href="../css/style.css" type="text/css">
	<link rel="stylesheet" href="../css/jquery-ui-1.9.0.custom.css" type="text/css">

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<script type="text/javascript" src="../js/jquery-ui-1.9.0.custom.js"></script>

	<script type="text/javascript" src="../js/observaciones.js"></script>
	
	<script type="text/javascript" src="../js/jquery.form.js"></script>
	<script src="../editor/ckeditor.js"></script>

</head>
<body style="overflow-y: hidden;">

	<?php
}

function Cierre(){
	?>

</body>
</html>

	<?php
}

/**
* MUESTRA LAS OBSERVACIONES DE LA CATEGORIA
* @param #proyecto -> id del proyecto
* @param $categoria -> id de la categoria
*/
function Observacion($proyecto, $categoria){
	$registros = new Registros();

	$categoriaDatos = $registros->getCategoria( $categoria );

	$datos = $registros->getObservacion($proyecto, $categoria);

	echo '<div class="preview">
			<form id="FormularioObservaciones" enctype="multipart/form-data" method="post" 	action="observaciones.php" >
				<input type="hidden" id="proyecto" name="proyecto" value="'.$proyecto.'" />
				<input type="hidden" id="categoria" name="categoria" value="'.$categoria.'" />
				<input type="hidden" name="func" value="RegistrarObservacion">
				
				<div class="titulo">
					Observaciones De '.$categoriaDatos[0]['nombre'].'
				</div>
				<div id="datos-preview" class="datos-preview">
			';

	//si tiene observaciones
	if(!empty($datos)){
		//tiene datos
		$observacion = base64_decode($datos[0]['observacion']);

		echo '<textarea id="observacion" name="observacion">'.$observacion.'</textarea>';

		echo '</div> <!-- end datos-preview -->';
	}else{
		//no tiene observaciones
		
		echo '<textarea id="observacion" name="observacion"></textarea>';

		echo '</div> <!-- end datos-preview -->';
	}

	echo '
			<div id="preview-botones"  class="preview-botones">
				<button type="button" onClick="parent.$.fancybox.close();" title="Cancelar Edición" >Cancelar</button>
			 	<input type="reset" onClick="Limpiar()" title="Limpiar Edición" value="Limpiar" />
			 	<input type="submit" onClick="EditorUpdateContent()" title="Guardar Edición" value="Guardar" />
			 </div>
		</form>
		</div><!-- end preview -->';
}

/**
* REGISTRA O ACTUALIZA UNA OBSERVACION
* @param $categoria -> id categoria
* @param $proyecto -> id del proyecto
* @param $observacion -> text de la observacion
*/
function RegistrarObservacion($proyecto, $categoria, $observacion){
	$registros = new Registros();
	if ( !$registros->RegistrarObservacion($proyecto, $categoria, $observacion) ){
		echo 'Error: no se pudo registrar la observacion.';
	}
}

/**
* OBTIENE LA OBSERVACION 
* @param #proyecto -> id del proyecto
* @param $categoria -> id de la categoria
* @return $observacion -> texto en html
*/
function getObservacion($proyecto, $categoria){
	$registros = new Registros();
	$datos = $registros->getObservacionDato("observacion", $proyecto, $categoria);

	if(!empty($datos)){
		$datos = base64_decode($datos);
		echo $datos;
	}
}


?>