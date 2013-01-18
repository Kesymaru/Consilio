
<?php

/**
* AJAX PARA COMENTARIOS
*/
require_once("class/comentarios.php");


if(isset($_POST['func'])){
	echo '111';
	switch ($_POST['func']) {

		case 'NuevoComentario':
			if( isset($_POST['proyecto']) && isset($_POST['articulo']) && isset($_POST['comentario']) ){
				echo 'enviando';
				
				NuevoComentario($_POST['proyecto'], $_POST['articulo'], $_POST['comentario'] );
			}
			break;
		
		default:
			# code...
			break;
	}
}

/**
* GUARDA UN NUEVO ARTICULO
*/
function NuevoComentario($proyecto, $articulo, $comentario ){
	$comentarios = new Comentarios();
	echo 'sssss';

	if( !$comentarios->newComentario($proyecto, $articulo, $comentario, $_POST['cliente_id'])){
		echo 'Error: no se pudo crear el nuevo comentario';
	}

}

?>