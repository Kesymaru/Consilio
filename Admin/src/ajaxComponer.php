<?php

/**
* AJAX PARA PROYECTOS
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
			if(isset($_POST['categoria']) && isset($_POST['proyecto'])){
				IncluirCategorias($_POST['proyecto'], $_POST['categoria']);
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

	echo '<form id="FormularioComponerCategorias" method="post" action="src/ajaxComponer.php" >
			<input type="hidden" name="func" value="IncluirCategorias" />
			<input type="hidden" name="proyecto" id="proyecto" value="'.$proyecto.'" />

			<div id="categorias-componer">
				<div class="titulo">
					Categorias
					<button id="incluir-categorias" title="Incluir Categorias Seleccionadas" type="button" onClick="GuardarCategorias()">Incluir</button>

				  </div>';

	echo '<div class="root" id="Padre0">';

	$registros = new Registros();
	$padres = $registros->getHijos(0);

	if( !empty($padres) ){
		$id = 0;
		$nombre = "";
		echo '<ul>';

		foreach ($padres as $f => $c) {
			foreach ($padres[$f] as $campo => $valor) {
						
				if($campo == 'id'){
					$id = $valor;
				}
				if($campo == 'nombre'){
					$nombre = $valor;
				}else{
					continue;
				}
			}
			echo '<li id="'.$id.'" onClick="HijosComponer('.$id.')" >
					<input type="checkbox" id="categoria'.$id.'"  value="'.$id.'" name="categoria[]" />
					<label for="'.$id.'">'.$nombre.'</label>
				  </li>';
		}
		echo '</ul>';
	}else{
		echo 'No hay datos.';
	}

	echo '</div><!-- end padre -->';
	echo '</div>
		</form>';	
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
		foreach ($hijos as $f => $c) {
			foreach ($hijos[$f] as $campo => $valor) {
				
				if($campo == 'id'){
					$id = $valor;
				}
				if($campo == 'nombre'){
					$nombre = $valor;
				}else{
					continue;
				}
			}
			//carga hijos de la categoria
			echo '<li id="'.$id.'" onClick="HijosComponer('.$id.')" >
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
					<button type="button" >Cancelar</button>
					<button type="button" >Limpiar</button>
					<input type="submit" value="Terminar" >
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
* @param $datos -> array[] con los id de las categorias
*/
function DatosRegistrados($datos){
	$lista = "";

	if(is_array($datos) && !empty($datos)){
		$lista .= '<ul id="categoriasIncluidas" class="listIzquierda">';

		foreach ($datos as $key => $categoria) {
			if(TieneHijos($categoria)){
				continue;
			}else{
				$nombre = CategoriaNombre($categoria);
				$camino = Camino($categoria);
				$lista .= '
						<li id="in'.$categoria.'" onClick="SelectCategoriaIncluida('.$categoria.')" title="'.$nombre.' Categoria Incluida">
							'.$camino.'
						</li>';
			}
		}
		$lista .= '</ul>';

		return $lista;
	}else{
		$lista .= '<tr id="nodata">
						<td>
							No hay datos
						</td>
				   </tr>';

		return $lista;
	}
}

/**
* COMPONE EL CAMINO DE UNA CATEGORIA
* @param $categoria -> id de la categoria
* @return $camino
*/
function Camino($categoria){
	$camino = array();

	$padre = $categoria;

	$tiene = true;
	$nombre = "";
	do{
		if(!$padre = getPadre($padre)){
			$tiene = false;
		}else{
			$nombre = CategoriaNombre($padre);
			//$camino[] = $nombre." ".$padre;
			$camino[] = $nombre;
		}
	}while($tiene);

	//invierte el array
	$path = array_reverse($camino);

	$compuesto = '<span class="path">';

	foreach ($path as $f => $c) {
		$compuesto .= $c.'/ ';
	}
	$compuesto .= '</span><b>'.CategoriaNombre($categoria).'</b>';

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
	$registros = new Registros();

	$datos = $registros->getRegistros($proyecto);

	$registradas = unserialize($datos[0]['registro']);

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
	}

}

/**
* EXCLUYE CATEGORIAS
* @param $proyecto -> id del proyecto
* @param $categorias -> array[] con los id a excluir
*/
function ExcluirCategorias($proyecto, $categorias){
	$registros = new Registros();

	$registro = $registros->getRegistros($proyecto);

	$registradas = unserialize($registro[0]['registro']);

	if(!empty($registradas) && is_array($categorias)){
		//botiene categorias que no se eliminan
		$seleccionadas = array_diff($registradas, $categorias);

		//actualiza el registro
		if(!$registros->UpdateRegistro($proyecto, $seleccionadas)){
			echo "Error: ajaxComponer.php ExcluirCategorias() no se actualizo el registro en registros->UpdateRegistro() ";
		}

	}else{
		echo 'Error: ajaxComponer.php ExcluirCategorias(), no hay registros para el proyecto '.$proyecto;
	}
}

?>