<?php

/**
* AJAX PARA COMPONER PROYECTOS
*/

//CLASES REQUERIDAS
require_once("class/proyectos.php");
require_once("class/imageUpload.php");
require_once("class/registros.php");
require_once("class/usuarios.php");

if(isset($_POST['func'])){
	
	switch ($_POST['func']){

		//CATEGORIAS INCLUIDAS DEL PROYECTO
		case "Categorias":
			if(isset($_POST['proyecto'])){
				Categorias($_POST['proyecto']);
			}
			break;

		//CATEGORIAS HIJAS
		case 'CategoriasHijas':
			if(isset($_POST['padre'])){
				CategoriasHijas($_POST['padre']);
			}
			break;

		//DATOS DEL PROYECTO
		case 'ComponerProyecto':
			if(isset($_POST['id'])){
				ComponerProyecto($_POST['id']);
			}
			break;

		//INCLUYE CATEGORIAS SELECCIONADAS
		case 'IncluirCategorias':
			if(isset($_POST['proyecto']) && isset($_POST['categorias'])){
				IncluirCategorias($_POST['proyecto'], $_POST['categorias']);
			}
			break;

		//EXCLUIR CATEGORIAS
		case 'ExcluirCategorias':
			if(isset($_POST['proyecto']) && isset($_POST['categorias'])){
				ExcluirCategorias($_POST['proyecto'], $_POST['categorias']);
			}
			break;
	}
}

/**
 * MUESTRA LAS CATEGORIAS DISPONIBLES PARA SELECCIONAR
 * EN UN PROYECTO
 * @param $proyecto -> id del proyecto
 */
function Categorias($proyecto){

	echo '<div id="categorias-componer">
			<input type="hidden" id="proyecto" value="'.$proyecto.'" >
				<div class="titulo">
					Categorias
					<!-- <button id="incluir-categorias" title="Incluir Categorias Seleccionadas" type="button" onClick="GuardarCategorias()">Incluir</button> -->

					<img class="icon derecha" src="images/next.png" title="Incluir Categorias Seleccionadas" onClick="GuardarCategorias()" >
				  </div>';

	echo '<div class="root" id="Padre0">';

	$registros = new Registros();
	$padres = $registros->getHijos(0);

	if( !empty($padres) ){
		$id = 0;
		$nombre = "";
		echo '<ul>';

		foreach ($padres as $f => $categoria) {
			/*foreach ($padres[$f] as $campo => $valor) {
						
				if($campo == 'id'){
					$id = $valor;
					$tieneHijos = TieneHijos($valor);
				}
				if($campo == 'nombre'){
					$nombre = $valor;
				}else{
					continue;
				}
			}*/
			$clase = '';
			if( tieneHijos( $categoria['id'] ) ){
				$clase = 'padre';
			}
			$id = $categoria['id'];
			$nombre = $categoria['nombre'];
			echo '<li class="'.$clase.'" id="'.$id.'" onClick="HijosComponer('.$id.')" >
					<input type="checkbox" id="categoria'.$id.'"  value="'.$id.'" name="categoria[]" />
					<label for="'.$id.'">'.$nombre.'</label>
				  </li>';
		}
		echo '</ul>';
	}else{
		echo 'No hay datos.';
	}

	echo '</div><!-- end padre -->';
	echo '</div>';	
}

/**
* CARGA CATEGORIAS HIJAS DE UN PADRE
* @param $padre -> id del padre
*/
function CategoriasHijas($padre){
	$registros = new Registros();
	$hijos = $registros->getHijos($_POST['padre']);

	if(!empty($hijos)){ //tiene hijos
		echo '<div class="categoria" id="Padre'.$padre.'">';

		$id = 0;
		$nombre = "";

		echo '<ul>';
		foreach ($hijos as $f => $categoria) {
			
			$clase = '';
			if( tieneHijos($categoria['id']) ){
				$clase = 'padre';
			}

			$id = $categoria['id'];
			$nombre = $categoria['nombre'];

			//carga hijos de la categoria
			echo '<li class="'.$clase.'" id="'.$id.'" onClick="HijosComponer('.$id.')" >
					<input type="checkbox" id="categoria'.$id.'"  value="'.$id.'" name="categoria[]" />
					<label for="'.$id.'">'.$nombre.'</label>
				  </li>';
		}

		echo '</ul>';
		echo '</div>';
	}
}

/**
 * MUESTRA VISTA DE COMPOSICION DE UN PROYECTO
 * @param $id -> id del proyecto
 */
function ComponerProyecto($id){
	$registros = new Registros();
	$proyectos = new Proyectos();
	$cliente = new Cliente();

	$registro = $registros->getRegistros($id);
	$proyecto = $proyectos->getProyectoDatos($id);

	if(!empty($proyecto)){

		$datos = unserialize( $registro[0]['registro'] );

		$nombreCliente = $cliente->getClienteDato( "nombre", $proyecto[0]['cliente'] );

		$lista = '<div id="proyectos" class="tipos">
				<div class="titulo" title="'.$proyecto[0]['nombre'].' De '.$nombreCliente.'">
					<img class="icon izquierda" title="Excluir Selecciones" src="images/previous.png">

					<img class="boton-buscar icon" title="Buscar Proyectos" onClick="BusquedaFocus(\'busqueda-categorias\', \'buscar-categorias\', \'categoriasIncluidas\', false)" src="images/search2.png">

					'.$proyecto[0]['nombre'].' De '.$nombreCliente.'
					
			  	</div>

			  	<div class="subtitulo">
				  	Categorias Incluidas
				</div>

			  	<div class="busqueda" id="busqueda-categorias">
					<div class="buscador">
						<input type="search" title="Escriba Para Buscar Categorias Incluidas" id="buscar-categorias" placeholder="Buscar Categorias Incluidas"/>
					</div>
				</div>';

		$lista .= DatosRegistrados($datos);
				   
		$lista .= '<div class="datos-botones">
					<!-- <button type="button" onClick="$componer.Guardar();" >Migrar</button> -->
				  </div>';

	}else{
		$lista = '<div id="proyectos" class="tipos">
				<input type="hidden" name="proyecto" id="proyecto" value="'.$id.'"/>
				<div class="titulo">
					Proyecto No encontrado
			  		<button type="button" title="Buscar Proyectos" onClick="BuscarContent(\'buscar-Proyectos\')">Buscar</button>
			  		
					<div class="busqueda">
						<input type="text" title="Escriba Para Buscar Proyectos Por Nombre, Estado o Cliente" id="buscar-Proyectos" placeholder="Buscar"/>
					</div>
			  	</div>
			  	Error: Proyecto no encontrado.';
	}

	echo $lista;
}

/**
* COMPONE LAS CATEGORIAS INCLUIDAS REGISTRADAS
* @param array $datos -> array[][] con los id de las categorias
*//*
function DatosRegistrados($datos){
	$registros = new Registros();
	$lista = "";

	if(is_array($datos) && !empty($datos)){
		$lista .= '<ul id="categoriasIncluidas" class="listIzquierda">';

		foreach ($datos as $key => $categoria) {
			if(TieneHijos($categoria)){
				continue;
			}else{

				//elimina las categorias fantasmas
				if( $registros->CategoriaExiste( $categoria ) ){
				
					$nombre = CategoriaNombre($categoria);
					$camino = Camino($categoria);
					$lista .= '
							<li id="in'.$categoria.'" onClick="SelectCategoriaIncluida('.$categoria.')" title="'.$nombre.' Categoria Incluida">
								'.$camino.'
							</li>';
				}
			}
		}
		$lista .= '</ul>';

		return $lista;
	}else{
		$lista .= '<tr id="nodata">
						<td>
							Proyecto vacio
						</td>
				   </tr>';

		return $lista;
	}
}*/
//arreglo
function DatosRegistrados($datos){
	$registros = new Registros();
	$lista = "";
	
	//echo '<pre>'; print_r($datos); echo '</pre>';

	if( is_array($datos) && !empty($datos) && $datos != ''){
		$lista .= '<ul id="categoriasIncluidas" class="listIzquierda">';

		foreach ($datos as $f => $str) {
			//echo $path.'<br/>';
			
			$path = explode(',', $str);

			if( is_array($path) ){
				$nombre = CategoriaNombre( $path[sizeof($path)-1] );
				$id = $path[sizeof($path)-1];

				$lista .= '<li id="in'. $id .'" title="'. $nombre .' Categoria Incluida">';
				
				foreach ($path as $f => $categoria) {
					
					$nombre = CategoriaNombre($categoria);
					
					if( $f != sizeof($path)-1 ){
						$lista .= '<span id="path'.$categoria.'">'.$nombre.' / </span>';			
					}else{
						$lista .= '<b>'.$nombre.'</b>';
					}
				}
				$lista .= '</li>';
			}
		}

		$lista .= '</ul>';

		return $lista;
	}else{
		$lista .= '<ul id="categoriasIncluidas" class="listIzquierda">
						<li class="nodata">No hay categorias incluidas</li>
				   </ul>';

		return $lista;
	}
}

/**
* COMPONE EL CAMINO DE UNA CATEGORIA
* @param $categoria -> id de la categoria
* @return $camino
*/
function Camino($categoria){
	if( empty($categoria) && $categoria != '' ){
		return;
	}
	$camino = array();

	$padre = $categoria;

	$tiene = true;
	$nombre = "";
	do{
		if(!$padre = getPadre($padre)){
			$tiene = false;
		}else{
			$nombre = CategoriaNombre($padre);
			$camino[] = array('id' => $padre, 'nombre' => $nombre);
		}
	}while($tiene);

	//invierte el array
	$path = array_reverse($camino);

	$compuesto = '';

	foreach ($path as $f => $c) {
		$compuesto .= '<span id="path'.$c['id'].'"> '.$c['nombre'].'/ </span>';
	}
	$compuesto .= '<b>'.CategoriaNombre($categoria).'</b>';

	return $compuesto; //camino
}

/**
* DETERMINA SI UNA CATEGORIA TIENE HIJOS
* @param $padre -> categoria padre id
* @return $hijos -> array[][] datos de los hijos del padre
*/
function TieneHijos($padre){
	$registros = new Registros();
	$hijos = $registros->getHijos($padre);
	
	if(!empty($hijos)){
		//return $hijos;
		return true;
	}else{
		return false;
	}
}

/**
* OBTIEN EL ID DE UN PADRE
* @param $padre -> id del padre
*/
function getPadre($hijo){
	$registros = new Registros();
	$padre = $registros->getPadre($hijo);
	
	if(!empty($padre)){
		return $padre;
	}else{
		return false;
	}
}

/**
* OBTIENE LOS HIJOS
* @param int $padre -> id del padre
*/
function getHijos($padre){
	$registros = new Registros();
	$hijos = $registros->getHijos($padre);

	if( !empty($hijos)){
		return $hijos;
	}else{
		return false; //no tiene hijos
	}
}

/**
* OBTIENE EL NOMBRE DE UNA CATEGORIA
* @param $id -> id del 
* @RETURN $nombre -> string con el nombre
*/
function CategoriaNombre($id){
	$registros = new Registros();
	$nombre = $registros->getCategoriaDato("nombre", $id);
	return $nombre;
}

/**
* INCLUYE LAS CATEGORIAS
* @poram $proyecto -> id del proyecto
* @param $categorias -> array[] con los id de las categorias seleccionadas
*/
function IncluirCategorias($proyecto, $categorias){
	/*$registros = new Registros();

	$datos = $registros->getRegistros($proyecto);

	$registradas = unserialize($datos[0]['registro']);

	if( !is_array($registradas)){
		$registradas = array();
	}

	if(is_array($categorias) && is_array($registradas)){
		//$nuevas = array_diff($categorias, $registradas);
		
		//combina las nuevas con las ya incluidas
		$nuevas = array_merge($categorias,$registradas);

		//elimina duplicados
		$nuevas = array_unique($nuevas);

		//se registran
		if(!$registros->UpdateRegistro($proyecto, $nuevas)){
			echo "Error: ajaxComponer.php IncluirCategorias() no se guardaron las nuevas categorias registros->UpdateRegistro() ";
		}

		echo 'incluidas';

	}else{
		echo "<br/>Error: ajaxComponer.php IncluirCategorias() categorias o registros invalidos.";
	}*/

	$registros = new Registros();
	$lista = '';

	if( is_array($categorias) ){
		foreach ($categorias as $f => $categoria) {

			$datos = $registros->getCategoria($categoria);
			$lista .= '<li id="in'.$datos[0]['id'].'"  >
						'.Camino($datos[0]['id']).'
					  </li>';
		}
	}

	echo $lista;
}

/**
* EXCLUYE CATEGORIAS
* @param int $proyecto -> id del proyecto
* @param int $categorias -> array[] con los id a excluir
* @return string detalles de error si ocurre
*/
function ExcluirCategorias($proyecto, $categorias){
	$registros = new Registros();

	//actualiza el registro
	if(!$registros->UpdateRegistro($proyecto, $categorias)){
		echo "Error: ajaxComponer.php ExcluirCategorias() no se actualizo el registro en registros->UpdateRegistro() ";
	}
}

?>