<?php
/**
 * AJAX PARA EL ADMINISTRADOR DE COMENTARIOS
 */
require_once('class/proyectos.php');
require_once('class/comentarios.php');
require_once('class/registros.php');

if(isset($_POST['func'])){
	switch ($_POST['func']) {
		case 'Comentarios':
			Comentarios();
			break;
		
		case 'Comentario':
			if( isset( $_POST['proyecto'] ) ){
				Comentario( $_POST['proyecto'] );
			}
			break;

		case 'ComentariosArticulo':
			if( isset($_POST['proyecto']) && isset($_POST['articulo']) ){
				ComentariosArticulo( $_POST['proyecto'], $_POST['articulo'] );
			}
			break;
	}
}

/**
 * LISTA LOS COMENTARIOS POR PROYECTOS
 */
function Comentarios(){
	$comentarios = new Comentarios();
	$datos = $comentarios->getCOmentarios();

	$lista = '<div class="titulo">
				Comentarios
			  </div>';

	if(!empty($datos)){
		$proyectos = new Proyectos();

		$lista .= '<table class="table-list" id="comentarios" >
					<tr>
						<th>
							Proyecto
						</th>
						<th>
							Comentarios
						</th>
					</tr>';

		foreach ($datos as $fila => $comentario) {
			$proyecto = $proyectos->getProyectoDato('nombre', $comentario['proyecto']);
			
			$new = '';
			if( $comentario['leido'] == 0 ){
				$new = 'td-new';
			}

			$lista .= '<tr class="'.$new.'" id="'.$comentario['proyecto'].'" >
						<td>';

			if( $comentario['leido'] == 0 ){
				$lista .= '<span class="new">
							New
						   </span>
						   '.$proyecto;;
			}

			$lista .= '	</td>
						<td>
							'.$comentario['COUNT(*)'].'
						</td>
					   </tr>';
		}

	}else{
		$lista .= '<tr>
					<td>
						no hay comnetarios
					</td>
				   </tr>
				   </table>';
	}

	echo $lista;
}

/**
 * MUESTRA LA INFORMACION DE UN COMENTARIO Y LO MARCA COMO LEIDO
 * @param $proyecto -> id del proyecto de los comentarios
 */
function Comentario( $proyecto ){
	$comentarios = new Comentarios();
	$registros = new Registros();

	$datos = $comentarios->getComentario($proyecto);

	$lista = '<div class="titulo">
				Comentarios
			   </div>';

	if( !empty($datos) ){
		$lista .= '<ul class="list" id="comentarios-articulos">';

		foreach ($datos as $f => $comentario) {
			$articulo = $registros->getDatoArticulo("nombre", $comentario['articulo']);

			$lista .= '<li id="'.$comentario['articulo'].'" title="Comentario: '.$articulo.'" >'
						.$articulo.
						'</li>';
		}

		$lista .= '</ul>';

	}else{
		$lista .= 'no hay comentarios';
	}

	echo $lista;
}

/**
 * COMENTARIOS DE UN ARTICULO
 */
function ComentariosArticulo($proyecto, $articulo){
	$comentarios = new Comentarios();
	$registros = new Registros();
	$cliente = new Cliente();

	$datos = $comentarios->getComentariosArticulo($proyecto, $articulo);

	$articuloNombre = $registros->getDatoArticulo("nombre", $articulo);

	$formulario = '<div class="titulo" >
					 	Comentarios '.$articuloNombre.'
					</div>';

	if( !empty($datos) ){
		$formulario .= '<table class="tabla-comentario">';

		$clienteDatos = $cliente->getDatosCliente( $datos[0]['usuario'] );

		foreach ($datos as $f => $comentario) {
			
			$formulario .= '<tr>
								<td class="datos-usuario">
									<div class="imagen">
										<div class="img-wrapper">
										<img src="'.$clienteDatos[0]['imagen'].'" />
										</div>
									</div>
									<span>'.$clienteDatos[0]['nombre'].'</span>
								</td>
								<td class="comentario-usuario">
									'.base64_decode($comentario['comentario']).'
									<span>Fecha: '.$comentario['fecha_creacion'].'</span>
								</td>
							</tr>';
		}

		$formulario .= '</table>';
	}else{
		$formulario .= 'no hay comentarios';
	}

	$formulario .= '</form>';

	echo $formulario;
}

?>