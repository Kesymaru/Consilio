
<?php

/**
* AJAX PARA COMENTARIOS
*/
require_once("class/comentarios.php");


if(isset($_POST['func'])){

	switch ($_POST['func']) {

		case 'NuevoComentario':
			if( isset($_POST['proyecto']) && isset($_POST['categoria']) && isset($_POST['norma']) && isset($_POST['articulo']) && isset($_POST['comentario']) ){
				
				NuevoComentario($_POST['proyecto'], $_POST['categoria'], $_POST['norma'], $_POST['articulo'], $_POST['comentario'] );

			}
			break;
		
		default:
			# code...
			break;
	}
}

/**
* GUARDA UN NUEVO ARTICULO
* @param int $proyecto -> id del proyecto
* @param int $categoria -> id de la categoria
* @param int $norma -> id de la norma
* @param int $articulo -> id del articulo
* @param string $comentario -> text/html del comentario
*/
function NuevoComentario($proyecto, $categoria, $norma, $articulo, $comentario ){
	$comentarios = new Comentarios();

	if( !$comentarios->newComentario($proyecto, $categoria, $norma, $articulo, $comentario, $_SESSION['cliente_id'])){
		echo 'Error: no se pudo crear el nuevo comentario';
	}

}

?>