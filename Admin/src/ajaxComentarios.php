<?php
/**
 * AJAX PARA EL ADMINISTRADOR DE COMENTARIOS
 */
require_once('class/proyectos.php');
require_once('class/comentarios.php');
require_once('class/registros.php');
require_once('class/usuarios.php');

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

		//ELIMINA UN COMENTARIO
		case 'EliminarComentario':
			if( isset($_POST['id'])){
				EliminarComentario( $_POST['id'] );
			}
			break;
	}
}

/**
 * LISTA LOS COMENTARIOS POR PROYECTOS
 */
function Comentarios(){
	$comentarios = new Comentarios();
	$cliente = new Cliente();

	$datos = $comentarios->getComentarios();

	$lista = '<div class="titulo">
				Comentarios

				<img class="boton-buscar icon" title="Buscar Proyectos" onClick="Busqueda(\'busqueda-comentarios\', \'buscar-comentarios\', \'comentarios\', true)" src="images/search2.png" >
			  </div>
			  <div class="busqueda" id="busqueda-comentarios">
					<div class="buscador">
						<input type="search" title="Escriba Para Buscar Proyectos" id="buscar-comentarios" placeholder="Buscar Proyectos"/>
					</div>
				</div>';

	$lista .= '<table class="table-list" id="comentarios" >
					<tr>
						<th>
							Proyecto
						</th>
						<th>
							Cliente
						</th>
						<th>
							Comentarios
						</th>
					</tr>';

	if(!empty($datos)){
		$proyectos = new Proyectos();

		foreach ($datos as $fila => $comentario) {
			$proyecto = $proyectos->getProyectoDato('nombre', $comentario['proyecto']);
			$clienteNombre = $cliente->getClienteDato("nombre", $comentario['usuario']);

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
						   '.$proyecto;
			}else{
				$lista .= $proyecto;
			}

			$lista .= '	</td>
						<td>
							'.$clienteNombre.'
						</td>
						<td>
							<span class="contador-center">'.$comentario['COUNT(*)'].'</span>
						</td>
					   </tr>';
		}

		$lista .= '</table>';

		$lista .= '<div class="datos-botones">
					<button type="button" title="Cancelar Edición" onClick="CancelarContent()">Cancelar</button>
					<button class="" id="ComentariosEliminar" type="button" title="Eliminar Comentarios del proyecto" onClick="EliminarComentariosProyecto()">Eliminar</button>
					<button class="" id="BotonBloquearIp" type="button" title="Ver Comentarios" onClick="Comentario()">Comentarios</button>
				  </div>';

	}else{
		$lista .= '<tr>
					<td colspan="3" class="nodata">
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
	$proyectos = new Proyectos();

	$proyectoNombre = $proyectos->getProyectoDato("nombre" , $proyecto );
	$datos = $comentarios->getComentario($proyecto);

	$lista = '<div class="titulo" title="Comentarios De '.$proyectoNombre.'">
				<img class="boton-buscar icon" type="button" title="Buscar Comentarios En Articulos" onClick="Busqueda(\'busqueda-comentarios-menu\', \'buscar-comentarios-menu\', \'comentarios-articulos\', false)" src="images/search2.png" >
				'.$proyectoNombre.'
			   </div>
			   <div class="busqueda" id="busqueda-comentarios-menu">
					<div class="buscador">
						<input type="search" title="Escriba Para Buscar Normas" id="buscar-comentarios-menu" placeholder="Buscar Normas"/>
					</div>
				</div>';

	$lista .= '<ul class="list" id="comentarios-articulos">';

	if( !empty($datos) ){

		foreach ($datos as $f => $comentario) {
			$articulo = $registros->getDatoArticulo("nombre", $comentario['articulo']);

			$lista .= '<li id="'.$comentario['articulo'].'" title="Comentario: '.$articulo.'" >';
						
			if( $c = $comentarios->ContaraArticuloComentarios($proyecto, $comentario['articulo']) ){
				$lista .= '<span class="contador" title="'.$c.' Comentarios">'.$c.'</span>';
			}
			
			$lista .= $articulo.'</li>';
		}

	}else{
		$lista .= '<li class="nodata">
				   		No hay comentarios
				   </div>';
	}

	$lista .= '</ul>';

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
					</div>
					';

	if( !empty($datos) ){
		$formulario .= '<table class="tabla-comentario">';

		$clienteDatos = $cliente->getDatosCliente( $datos[0]['usuario'] );

		foreach ($datos as $f => $comentario) {
			
			$formulario .= '<tr id="'.$comentario['id'].'">
								<td class="datos-usuario">
									<div class="imagen" title="'.$clienteDatos[0]['nombre'].'">
										<div class="img-wrapper">
											<img src="'.$clienteDatos[0]['imagen'].'" onerror="this.src=\'images/es.png\'"  />
										</div>
									</div>
									<span>'.$clienteDatos[0]['nombre'].'</span>
								</td>
								<td class="comentario-usuario">
									<img class="delete-comentario" src="images/close.png" onClick="EliminarComentario(\''.$comentario['id'].'\')">

									'.base64_decode($comentario['comentario']).'
									
									<span>
										Fecha: '.$comentario['fecha_creacion'].'
									</span>
								</td>
							</tr>';

			//marca comentario como leido
			$comentarios->ComentarioLeido( $comentario['id'] );
		}

		$formulario .= '</table>';
	}else{
		$formulario .= '<div class="nodata">No hay comentarios</div>';
	}

	$formulario .= '</form>';

	echo $formulario;
}

/**
* MARCA UN COMENTARIO COMO LEIDO
* @param int $id => id del comentario a marcar
*/
function ComentarioLeido($id){

}

/**
* ELIMINA EL COMENTARIO
* @param string $id -> id del comentario a eliminar
*/
function EliminarComentario( $id ){
	$comentarios = new Comentarios();

	if( !$comentarios->EliminarComentario( $id ) ){
		echo 'Error: no se pudo eliminar el comentario id '.$id.'<br/>ajaxComentarios EliminarComentario';
	}

}

?>