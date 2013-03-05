
<?php

/**
* AJAX PARA COMENTARIOS
*/
require_once("class/comentarios.php");
require_once("class/usuarios.php");

if(isset($_POST['func'])){

	switch ($_POST['func']) {

		case 'NuevoComentario':
			if( isset($_POST['proyecto']) && isset($_POST['categoria']) && isset($_POST['norma']) && isset($_POST['articulo']) && isset($_POST['comentario']) ){
				
				NuevoComentario($_POST['proyecto'], $_POST['categoria'], $_POST['norma'], $_POST['articulo'], $_POST['comentario'] );

			}
			break;
		
		default:
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

	if(  $id = $comentarios->newComentario($proyecto, $categoria, $norma, $articulo, $comentario, $_SESSION['cliente_id'])){
		getNuevoComentario($id);
	}else{
		echo 'Error: no se pudo crear el nuevo comentario';
	}
}

/**
* OBTIENE LE NUEVO COMENTARIO Y LO COMPONE EN UNA FILA NUEVA PARA LA LISTA
* @param int id -> id del nuevo comentario
* @return string nuevo -> nuevo comentario compuesto html/text
*/
function getNuevoComentario($id){
	$comentarios = new Comentarios();
	$datos = $comentarios->getComentario($id);

	$nuevo = '';

	if( !empty($datos) ){
		$cliente = new Cliente();

		$usuario = $cliente->getClienteDato("nombre", $datos[0]['usuario']);
		$usuarioImg = $_SESSION['datos'].$cliente->getClienteDato("imagen", $datos[0]['usuario']);

		$nuevo = '<tr>
					<td>
						<div class="div-imagen">
							<div title="'.$usuario.'" class="img-wrapper2" >
								<img src="'.$usuarioImg.'" />
							</div>
						</div>
						<span>'.$usuario.'</span>
					</td>
					<td class="comentario" >
						'.base64_decode($datos[0]['comentario']).'
					</td>
			    </tr>';
	}

	echo $nuevo;
}

?>