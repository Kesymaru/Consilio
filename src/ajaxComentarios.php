
<?php

/**
* AJAX PARA COMENTARIOS
*/
require_once("class/comentarios.php");


if(isset($_POST['func'])){

	switch ($_POST['func']) {

		case 'NuevoComentario':
			if( isset($_POST['proyecto']) && isset($_POST['categoria']) && isset($_POST['articulo']) && isset($_POST['comentario']) ){
				
				NuevoComentario($_POST['proyecto'], $_POST['categoria'], $_POST['articulo'], $_POST['comentario'] );

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
function NuevoComentario($proyecto, $categoria, $articulo, $comentario ){
	$comentarios = new Comentarios();

	if( !$comentarios->newComentario($proyecto, $categoria, $articulo, $comentario, $_SESSION['cliente_id'])){
		echo 'Error: no se pudo crear el nuevo comentario';
	}

}

?>