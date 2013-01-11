<?php
/**
* AJAX PARA PROYECTOS EN CLIENTES
*/

require_once("class/proyectos.php");
require_once("class/registros.php");

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
			if(isset($_POST['id'])){
				Normas($_POST['id']);
			}
			break;

		//CARGA NORMA
		case 'Articulos':
			if(isset($_POST['id'])){
				Articulos($_POST['id']);
			}
			break;

		//DATOS DE UN ARTICULO
		case 'DatosArticulo':
			if(isset($_POST['id'])){
				DatosArticulo($_POST['id']);
			}
			break;
	}

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
				
				$datosCategoria = $registros->getCategoriaDatos($categoria);

				if($datosCategoria[0]['padre'] == 0){
					
					$lista .= '<li class="" id="'.$categoria.'" onClick="PadreHijos('.$categoria.','.$proyecto.')">';

					$lista .= '<img title="'.$datosCategoria[0]['nombre'].'" src="'.$_SESSION['datos'].$datosCategoria[0]['imagen'].'" /><p>'.$datosCategoria[0]['nombre'].'</p>';

					$lista .= '</li>';
				}
			}
		}else{
			$lista .= '<li>No hay datos</li>';
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

		$lista .= '<div class="categoria" id="Padre'.$padre.'">';

		$lista .= '<ul>';
		
		foreach ($hijos as $f => $hijo) {

			foreach ($disponibles as $s => $incluida) {
				if($incluida == $hijo['id']){
					$lista .= '<li id="'.$hijo['id'].'" onClick="Hijos('.$hijo['id'].', '.$proyecto.')">'.$hijo['nombre'].'</li>';
				}else{
					continue;
				}
			}
			//carga hijos de la categoria
			//$lista .= '<li id="'.$hijo['id'].'" onClick="Hijos('.$hijo['id'].', '.$proyecto.')">'.$hijo['nombre'].'</li>';
		}

		$lista .= '</ul>';
		$lista .= '</div>';

		//tiene hijos por lo tanto no es hoja
		//echo '<script>NormasCategoria('.$padre.');</script>';

	}else{
		//no tiene hijos es una hoja
		if( !$registros->EsRoot($padre) ){
			$lista .= '<script>Normas('.$padre.','.$proyecto.');</script>';
		}
	}

	echo $lista;
}

/**
 * LISTA DE NORMAS DE UNA CATEGORIA
 * @param $id -> id de la categoria
 */
function Normas($id){
	$registros = new Registros();
	$datos = $registros->getCategoriaDatos($id);

	if(!empty($datos)){
		$normas = unserialize($datos[0]['normas']);
		
		$lista = '<div class="titulo" id="normas" >
				  	<button class="atras" type="button" onClick="ShowCategorias()">Atras</button>
					'.$datos[0]['nombre'].'
					</div>
					<div class="datos">
				<uL class="lista">';

		if(!empty($normas)){
			foreach ($normas as $key => $norma) {
				$datosNormas = $registros->getDatosNorma($norma);

				if($datosNormas[0]['status'] == 1){

					$lista .= '<li id="'.$datosNormas[0]['id'].'" onClick="SelectNorma('.$datosNormas[0]['id'].')">'.$datosNormas[0]['nombre'].'</li>';
				}
			}
		}else{
			$lista .= '<li>No hay datos</li>';
		}
		

		$lista .= '</ul>
					</div>';
	}
	echo $lista;
}

/**
 * MUESTRA LISTA DE ARTICULOS de una norma
 * @param $id -> id de la norma
 */
function Articulos($id){
	$registros = new Registros();
	$datos = $registros->getArticulos($id);

	$lista = '';

	if(!empty($datos)){
		$lista .= '<div class="titulo">
				<button class="atras" type="button" onClick="ShowNormas()">Atras</button>
					'.$datos[0]['nombre'].'
				</div>
				<div class="datos">
				<ul class="lista">';

		foreach ($datos as $fila => $norma) {
				$lista .= '<li id="'.$norma['id'].'" onClick="SelectArticulo('.$norma['id'].')">'.$norma['nombre'].'</li>';
		}
		$lista .= '</ul>
					</div>';

		echo $lista;
	}
}

/**
* CARGA DATOS DE UN ARTICULO
* @param $id -> id del articulo
*/
function DatosArticulo($id){
	
	date_default_timezone_set('America/Costa_Rica');

	$registros = new Registros();
	$datos = $registros->getArticulo($id);

	$lista = '<div id="datos-articulo">
				<div class="titulo">
					Datos
			  	</div>';

	if(!empty($datos)){
		foreach ($datos as $fila => $articulo) {
			
			foreach ($articulo as $dato => $valor) {
				
				if($dato == 'id' || $dato == "fecha" || $dato == "borrado" || $dato == "norma" || $dato == "nombre"){
					continue;
				}

				if($dato == 'resumen' || $dato == "permisos" || $dato == "articulo" || $dato == "sanciones"){
					
					if(empty($valor)){
						continue;
					}

					$lista .= '<div class="box">
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
			$lista .= '<div class="box">
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
		
		$lista .= '<div id="datos-footer">
						Última Actualización '.date("m d Y - g:i a").'
					</div>
					</div><!-- end datos cargados -->
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

?>