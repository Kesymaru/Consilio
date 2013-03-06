<?php
/**
* AJAX PARA PROYECTOS EN CLIENTES
*/

require_once("class/proyectos.php");
require_once("class/registros.php");
require_once('class/usuarios.php');
require_once("class/comentarios.php");

if(isset($_POST['func'])){
	switch ($_POST['func']) {

		//CARGA LAS CATEGORIAS ROOT DE UN PROYECTO
		case 'CategoriasRoot':
			if(isset($_POST['proyecto'])){
				CategoriasRoot($_POST['proyecto']);
			}

		//OBTIENE LOS HIJOS DE UN PADRE SELECCIONADO
		case 'Hijos':
			if(isset($_POST['padre']) && isset($_POST['proyecto'])){
				Hijos( $_POST['padre'], $_POST['proyecto'] );
			}
			break;

		case 'TieneHijos':
			if( isset($_POST['categoria'])){
				TieneHijos( $_POST['categoria'] );
			}
			break;

		//OBTIENE LOS IDS DE LOS HIJOS DE UN PADRE
		case 'GetHijos':
			if( isset($_POST['padre']) ){
				$registros = new Registros();
				//el id de todos los hijos
				$hijos = $registros->getTodosHijos($_POST['padre']);
				echo json_encode($hijos);
			}
			break;

		//OBTIENE LOS IDS DE LOS HERMANOS DE UN PADRE
		case 'GetHermanos':
			if( isset($_POST['padre']) ){
				$registros = new Registros();
				//el id de todos los hijos
				$hijos = $registros->getTodosHermanos($_POST['padre']);
				echo json_encode($hijos);
			}
			break;

		//DATOS DE UNA CATEGORIA
		case 'Normas':
			if(isset($_POST['proyecto']) && isset($_POST['id']) ){
				Normas($_POST['proyecto'], $_POST['id']);
			}
			break;

		//CARGA NORMA
		case 'Articulos':
			if( isset($_POST['proyecto']) && $_POST['categoria'] &&  isset($_POST['id']) ){
				Articulos($_POST['proyecto'], $_POST['categoria'], $_POST['id']);
			}
			break;

		//DATOS DE UN ARTICULO
		case 'DatosArticulo':

			if( isset($_POST['proyecto']) && isset($_POST['categoria']) && isset($_POST['norma']) && isset($_POST['id'])){
				DatosArticulo($_POST['proyecto'], $_POST['categoria'], $_POST['norma'], $_POST['id']);
			}
			break;

		/** NOMBRE PARA EL CAMINO ***/
		
		//NOMBRE DE LA NORMA
		case 'NormaNombre':
			if(isset($_POST['id'])){
				NormaNombre($_POST['id']);
			}
			break;

		//NOMBRE DEL ARTICULO
		case 'ArticuloNombre':
			if(isset($_POST['id'])){
				ArticuloNombre($_POST['id']);
			}
			break;

		//NOMBRE DE LA CATEGORIA
		case 'CategoriaNombre':
			if(isset($_POST['id'])){
				CategoriaNombre($_POST['id']);
			}
			break;
	}

}else{
	echo "Error: Falta de parametros.";
}

/**
* CATEGORIAS ROOT DE UN PROYECTO
*/
function CategoriasRoot($proyecto){
	$registros = new Registros();

	$datos = $registros->getRegistros($proyecto);

	$lista = '';

	if(!empty($datos)){
		$categorias = unserialize($datos[0]['registro']);

		$lista .= '<ul id="supercategorias" class="categorias">';

		if(!empty($categorias)){
			foreach ($categorias as $key => $categoria) {
				
				if( !$datosCategoria = $registros->getCategoriaDatos($categoria) ){
					continue;
				}

				if($datosCategoria[0]['padre'] == 0){
					
					$lista .= '<li class="" id="'.$categoria.'" >';
										
					$imagen = $_SESSION['datos'].$datosCategoria[0]['imagen'];

					$lista .= '<img title="'.$datosCategoria[0]['nombre'].'" src="'.$imagen.'" onerror="this.src=\'images/es.png\'" /><p>'.$datosCategoria[0]['nombre'].'</p>';

					$lista .= '</li>';
				}
			}
		}else{
			$lista .= '<li></li>';
		}

		$lista .= '</ul>';
	}else{

	}

	echo $lista;
}

/**
* CARGA CATEGORIAS HIJAS DE UN PADRE
* @param $padre -> id del padre
*/
function Hijos($padre, $proyecto){
	$registros = new Registros();
	$hijos = $registros->getHijos($_POST['padre']);

	$lista = "";

	if(!empty($hijos)){ //tiene hijos
		$datos = $registros->getRegistros($proyecto);
		$disponibles = unserialize($datos[0]['registro']);

		//$lista .= '<div class="categoria" id="Padre'.$padre.'">';

		//$lista .= '<ul id="Padre'.$padre.'">';
		
		foreach ($hijos as $f => $hijo) {

			foreach ($disponibles as $s => $incluida) {
				if($incluida == $hijo['id']){
					$lista .= '<li>
								<span id="'.$hijo['id'].'">
									'.$hijo['nombre'].'
								</span>

								<ul id="sub'.$hijo['id'].'" class="subcategoria">
								</ul>
							   </li>';
				}else{
					continue;
				}
			}
			//carga hijos de la categoria
			//$lista .= '<li id="'.$hijo['id'].'" onClick="Hijos('.$hijo['id'].', '.$proyecto.')">'.$hijo['nombre'].'</li>';
		}

		//$lista .= '</ul>';
		//$lista .= '</div>';

		//tiene hijos por lo tanto no es hoja
		//echo '<script>NormasCategoria('.$padre.');</script>';

	}else{
		//no tiene hijos es una hoja
		if( !$registros->EsRoot($padre) ){
			//$lista .= '<script>$listaCategorias.Normas('.$padre.');</script>';
			//$lista .= 'no tiene hijos';
		}
	}

	echo $lista;
}

/**
* VALIDA QUE UNA CATEGORIA PADRE TENGA HIJOS
* @param int $categoria -> id del padre
*/
function TieneHijos($categoria){
	$registros = new Registros();
	$hijos = $registros->getHijos($categoria);

	if( !empty($hijos) ){
		echo json_encode('true');
		//echo json_encode( $hijos );
	}else{
		echo json_encode('false');
	}
}

/**
 * LISTA DE NORMAS INCLUIDAS Y VISIBLE DE UNA CATEGORIA
 * @param $proyecto -> id del proyecto
 * @param $id -> id de la categoria
 */
function Normas($proyecto, $id){
	$registros = new Registros();
	$normas = $registros->getValidNormas($proyecto, $id);

	//$lista = '<uL class="lista">';
	$lista = '';

	if(!empty($normas)){
		//$normas = unserialize($datos[0]['normas']);

		$conteo = 0;

		foreach ($normas as $key => $norma) {
			$datosNormas = $registros->getDatosNorma($norma);

			//si la norma es visible
			if($datosNormas[0]['status'] == 1){

				$lista .= '<li id="'.$datosNormas[0]['id'].'"  title="'.$datosNormas[0]['nombre'].' #'.$datosNormas[0]['numero'].'">
				              '.$datosNormas[0]['nombre'].'
						   </li>';
				$conteo++;
			}
		}
		
		if($conteo == 0){
			$lista .= '<li>No hay normas</li>';
		}

	}else{
		$lista .= '<li>No hay normas</li>';
	}

	//$lista .= '  </ul>';

	echo $lista;
}

/**
 * MUESTRA LISTA DE ARTICULOS de una norma
 * @param int $proyecto -> id del proyecto
 * @param int $categoria -> id de la categoria
 * @param int $id -> id de la norma
 * @return text/html $lista -> lista de articulos de la norma
 */
function Articulos($proyecto, $categoria, $id){
	$registros = new Registros();
	$datos = $registros->getValidArticulos($proyecto, $categoria, $id);
	$norma = $registros->getDatoNorma("nombre", $id);
	

	$lista = '<ul id="Articulos'.$id.'">';

	if(!empty($datos)){
		$conteo = 0;

		foreach ($datos as $fila => $norma) {
			$lista .= '<li id="'.$norma['id'].'" title="'.$norma['nombre'].'">
						'.$norma['nombre'].'
					   </li>';
			$conteo++;
		}
		
		if($conteo == 0){
			$lista .= '<li>No hay articulos</li>';
		}
	}else{
		$lista .= '<li>No hay articulos</li>';
	}

	$lista .= '<ul>';

	echo $lista;
}

/**
* CARGA DATOS DE UN ARTICULO
* @param int $proyecto -> id del proyecto
* @param int $categoria -> id de la categoria
* @param int $norma -> id de la norma
* @param int $id -> id del articulo
*/
function DatosArticulo($proyecto, $categoria, $norma, $id){
	date_default_timezone_set('America/Costa_Rica');

	$registros = new Registros();

	//$datos = $registros->getArticulo($id);
	
	//OBTIENE LA DATA DEL ARTICULO VALIDANDO SI EL PROYECTO ESTA ACTIVO
	$datos = $registros->getValidArticuloDatos($proyecto, $id);

	$observacion = $registros->getObservacion($proyecto, $categoria, $norma, $id);
	$lista = '';

	if( !empty($datos) ){
		$lista = '<div id="datos-articulo">
				<div class="titulo">
					<img id="solapa" class="icon izquierda rotacion" onClick="$listaCategorias.OcultarDatos()" src="images/next.png" />
					'.$datos[0]['nombre'].'
			  	</div>
			  	<div class="datos">';

		//agrega la observacion
		if( !empty($observacion) ){
			$observacionTitulo = $registros->getTipoObservacion($observacion[0]['tipo']);
			$lista .= '<div class="box" >
							<div class="dato-titulo">
								'.$observacionTitulo.'
							</div>
							<div class="dato">
								'.base64_decode($observacion[0]['observacion']).'
							</div>
						</div>';
		}

		foreach ($datos as $fila => $articulo) {
			
			foreach ($articulo as $dato => $valor) {
				
				if($dato == 'id' || $dato == "fecha_creacion" || $dato == "fecha_actualizacion" || $dato == 'id_version' || $dato == 'fecha_snapshot' || $dato == "borrado" || $dato == "norma" || $dato == "nombre"){
					continue;
				}

				if($dato == 'resumen' || $dato == "permisos" || $dato == "articulo" || $dato == "sanciones"){
					
					if(empty($valor)){
						continue;
					}

					$lista .= '<div class="box" >
									<div class="dato-titulo">
										'.$dato.'
									</div>
									<div class="dato">
										'.base64_decode($valor).'
									</div>
						   		</div>';
					continue;
				}

				if($dato == "entidad"){
					$entidades = unserialize($valor);

					$nombres = Entidades($entidades);
					
					if(!empty($nombres)){
						$lista .= '<div class="box">
										<div class="dato-titulo">
											'.$dato.'
										</div>
									<div class="dato">';

						foreach ($nombres as $key => $nombre) {
							$lista .= $nombre."<br/>";
						}

						$lista .= '</div>
						   		</div>';
					}

					continue;
				}

				$lista .= '<div class="box">
								<div class="dato-titulo">
									'.$dato.'
								</div>
								<div class="dato">
									'.$valor.'
								</div>
					   		</div>';
			}
		} //end foreach

		$archivos = $registros->getArchivosArticulo($articulo['id']);

		if(!empty($archivos)){
			$lista .= '<div class="box" >
								<div class="dato-titulo">
									Adjuntos
								</div>
								<div class="dato">
								<ul>';

			foreach ($archivos as $f => $archivo) {
				$lista .= '<li>
							<a title="Descargar Adjunto '.$archivo['nombre'].'" href="src/download.php?link='.$_SESSION['datos'].$archivo['link'].'">
									'.$archivo['nombre'].'
									<img src="images/folder.png" />
							</a>
							</li>';
			}
			$lista .= '</div>
						</div>';
		}

		$lista .= '</div><!-- end datos cargados -->
					</div><!-- end datos -->';

		//compone el panel de los comentarios
		$lista .= PanelComentarios($proyecto, $categoria, $id);

		$lista .= '
					<div id="datos-footer">
						Última Actualización '.date("m d Y - g:i a").'
						<img class="icon derecha" onClick="Comentar()" src="images/coment.png" />
					</div>

					</div><!-- end datos-articulo -->';

	}else{
		$lista .= '<div class="">
					<script>notificaError("Error ajaxProyectos.php DatosArticulo articulo '.$id.' <br/>No se encontraron datos.");
					</div>
					</div>';
	}

	echo $lista;
}


/**
* OBTIENE LAS ENTIDADES
* @param $entidad -> array[] con las entidades
* @return $nombres -> array[] con los nombres de las entidades registradas
*/
function Entidades($entidades){
	$registro = new Registros();

	$nombres = array();

	foreach ($entidades as $fila => $id) {
		$nombre = $registro->getEntidadDato("nombre", $id);
		$nombres[] = $nombre;
	}

	return $nombres;
}


/*************** HELPERS *************/

/**
* OBTIENE EL NOMBRE DE UNA NORMA
*/
function NormaNombre($id){
	$registros = new Registros();
	$nombre =  $registros->getDatoNorma("nombre", $id);

	echo $nombre;
}

/**
* OBTIENE EL NOMBRE DE UN ARTICULO
*/
function ArticuloNombre($id){
	$registros = new Registros();
	$nombre =  $registros->getDatoArticulo("nombre", $id);

	echo $nombre;
}

/**
* OBTIENE EL NOMBRE DE UNA CATEGORIA
*/
function CategoriaNombre($id){
	$registros = new Registros();
	$nombre =  $registros->getCategoriaDato("nombre", $id);

	echo $nombre;
}

/**
* COMPONE EL PANEL PARA LOS COMENTARIOS
* @param int $proyecto -> id del proyecto
* @param int $categoria -> id categoria
* @param int $articulo -> id del articulo
* @return string $panel -> html/text
*/
function PanelComentarios($proyecto, $categoria, $articulo){
	$comentarios = new Comentarios();
	$datos = $comentarios->getComentarios($proyecto, $categoria, $articulo);
	
	$cliente = new Cliente();
	$logo = $_SESSION['datos'].$cliente->getClienteDato("imagen", $_SESSION['cliente_id']);

	$panel = '';
	$oculto = '';
	$panel .= '<div id="panel-comentario">
				';

	if(!empty($datos)){
		$oculto = 'ocultos';

		$panel .= '<div id="comentarios"> <table class="comentarios" >';
		
		//compone los comentarios
		foreach ($datos as $fila => $comentario) {
			
			$usuario = $cliente->getClienteDato("nombre", $comentario['usuario']);
			$usuarioImg = $_SESSION['datos'].$cliente->getClienteDato("imagen", $comentario['usuario']); 
			
			$panel .= '<tr>
						<td class="comentario-imagen">
							<div class="div-imagen">
								<div title="'.$usuario.'" class="img-wrapper2" >
									<img src="'.$usuarioImg.'" />
								</div>
							</div>
							<span>'.$usuario.'</span>
					</td>';
			

			$panel .= '<td class="comentario" >
						'.base64_decode($comentario['comentario']).'
					   </td>
					   </tr>';
		}

		$panel .= '</table>
				  <div class="botonera-comentarios">
				  	<button id="NewComentario" type="button" onClick="NewComentario()">Comentar</button>
				  </div>
				  </div><!-- comentarios -->';
		

	}else{
		//no hay comentarios
		$panel .= '<div id="comentarios" > 
					<table class="comentarios" >
					</table>
					<div class="botonera-comentarios">
				  		<button id="NewComentario" type="button" onClick="NewComentario()">Comentar</button>
				  	</div>
				   </div><!-- comentarios -->';
	}

	$panel .= '<div id="new-comentario" >
						
					<div id="panel-editor">
							<textarea id="comentario" placeholder="Comentar ..." name="comentario"></textarea>
					</div>

					<div class="div-imagen panel-imagen">
						<div title="'.$_SESSION['cliente_nombre'].'" class="img-wrapper" >
							<img src="'.$logo.'" />
						</div>
					</div>
					<div>';

	if($oculto == ''){
		$panel .= '<button class="button-cancelar" type="button" onClick="Comentar()">Cancelar</button>';
	}else{
		$panel .= '<button class="button-cancelar" type="button" onClick="NewComentario()">Cancelar</button>';
	}
					
	$panel .='	<button type="button" onClick="AgregarComentario('.$articulo.')">Guardar</button>

				</div>
				</div><!-- end new comentario -->';

	$panel .= '</div><!-- end panel -->';

	return $panel;
}

?>
