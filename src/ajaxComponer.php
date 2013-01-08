<?php

/**
* AJAX PARA PROYECTOS
*/

//CLASES REQUERIDAS
require_once("class/proyectos.php");
require_once("class/imageUpload.php");
require_once("class/registros.php");

if(isset($_POST['func'])){
	
	switch ($_POST['func']){

		//categorias seleccionables para el proyecto
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
			echo 'excluyendo';
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
					<button id="incluir-categorias" type="button" onClick="GuardarCategorias()">Incluir</button>
					<hr>
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

	$registro = $registros->getRegistros($id);
	$proyecto = $proyectos->getProyectoDatos($id);

	if(!empty($proyecto)){

		if( !empty($registro[0]['registro']) ){
			$datos = unserialize( $registro[0]['registro'] );
			echo '<pre>';
			print_r($datos);
			echo '</pre>';

			//echo $datos = unserialize($datos);
		}

		$datos = unserialize( $registro[0]['registro'] );	

		$lista = '<div id="proyectos" class="tipos">
				<div class="titulo">
					'.$proyecto[0]['nombre'].'
			  		<button type="button" title="Buscar Proyectos" onClick="BuscarContent(\'buscar-Composicion\')">Buscar</button>
					<hr>
					<div class="busqueda">
						<input type="text" title="Escriba Para Buscar." id="buscar-Composicion" placeholder="Buscar"/>
					</div>
			  	</div>';

		$lista .= '<table class="table-list" id="categorias-incluidas">
				   <tr>
				      <td>
				      	Categorias Incluidas
				      </td>
				   </tr>';

		$lista .= DatosRegistrados($datos);
				   
		$lista .= '</table>';

	}else{
		$lista = '<div id="proyectos" class="tipos">
				<input type="hidden" name="proyecto" id="proyecto" value="'.$id.'"/>
				<div class="titulo">
					Proyecto No encontrado
			  		<button type="button" title="Buscar Proyectos" onClick="BuscarContent(\'buscar-Proyectos\')">Buscar</button>
					<hr>
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
		foreach ($datos as $key => $categoria) {
			if(TieneHijos($categoria)){
				continue;
			}else{
				$lista .= '<tr>
						<td id="in'.$categoria.'" onClick="SelectCategoriaIncluida('.$categoria.')">
							'.CategoriaNombre($categoria).'
						</td>
					</tr>';
			}
		}

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
		echo "<br/>Error: ajaxComponer.js IncluirCategorias() categorias o registros invalidos.";
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

	if(!empty($registradas)){
		//botiene categorias que no se eliminan
		$seleccionadas = array_diff($categorias, $registradas);

		//actualiza el registro
		if(!$registros->UpdateRegistro($proyecto, $seleccionadas)){
			echo "Error: ajaxComponer.php ExcluirCategorias() no se actualizo el registro en registros->UpdateRegistro() ";
		}

		echo 'quedaron<br/><pre>';
		print_r($seleccionadas);
		echo '</pre>';

	}else{
		echo 'Error: ajaxComponer.php ExcluirCategorias(), no hay registros para el proyecto '.$proyecto;
	}
}

?>