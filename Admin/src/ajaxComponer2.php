<?php

/**
 * AJAX PARA COMPONER PROYECTOS
 */

//CLASES REQUERIDAS
require_once("class/proyectos.php");
require_once("class/imageUpload.php");
require_once("class/registros.php");
require_once("class/usuarios.php");

if(isset($_GET)){
	foreach($_GET as $key => $value ){
		$_POST[$key] = $value;
	}
}

//$_POST['proyecto'] = 24;
//$_POST['func'] = "GuardarCategorias";
//$_POST['categorias'] = array('1,2,3','10,20,30','1000,2000,3000');

if(isset($_POST['func'])){

	switch ($_POST['func']){

		//CATEGORIAS INCLUIDAS DEL PROYECTO
		case "Categorias":
			if(isset($_POST['proyecto']) && isset($_POST['padre']) ){
				Categorias($_POST['proyecto'], $_POST['padre']);
			}
			break;

		//INCLUIR NUEVAS CATEGORIAS
		case 'GuardarCategorias':
			if(isset($_POST['proyecto']) && isset($_POST['categorias'])){
				GuardarCategorias($_POST['proyecto'], $_POST['categorias']);
			}
			break;

		case 'ExcluirCategorias':
			if(isset($_POST['proyecto']) && isset($_POST['categorias'])){
				ExcluirCategorias($_POST['proyecto'], $_POST['categorias']);
			}
			break;

		//INCLUIDAS
		case 'Incluidas':
			if(isset($_POST['proyecto'])){
				Incluidas($_POST['proyecto']);
			}
			break;

		//CHECK IS ESTA INCLUIDA
		case 'Incluida':
			if( isset($_POST['proyecto']) && isset($_POST['categoria'])){
				if( Incluida($_POST['proyecto'], $_POST['categoria']) ){
					echo 'Incluida';
				}else{
					echo 'NO INCLUIDA';
				}
			}
	}
}

/**
 * CARGA HIJOS DE UNA CATEGORIA
 * @param int $proyecto id
 * @param int $padre id
 */
function Categorias($proyecto, $padre){

	$registros = new Registros();
	$padres = $registros->getHijos($padre);
	$datos = $registros->getRegistros($proyecto);

	if( !empty($padres) ){
		echo '<ul>';

		foreach ($padres as $f => $categoria) {
			$nietos = $registros->getHijos($categoria['id']);

			$clase = 'noNext';
			//si tiene hijos
			if( !empty($nietos) ){
				$clase = 'hasNext';
			}

			$id = $categoria['id'];
			$nombre = $categoria['nombre'];
			$checked = '';

			//si esta incluida
			if( Incluida($proyect, $id, $datos )){
				$checked = "checked";
			}

			echo '<li class="'.$clase.'" id="'.$id.'" >
					<input type="checkbox" value="'.$id.'" '.$checked.'/>
					'.$nombre.'
				  </li>';
		}
		echo '</ul>';
	}
}

/**
 * INCLUYE LAS NUEVAS CATEGORIAS
 * @param int $proyecto id
 * @param array $categorias
 */
function GuardarCategorias($proyecto, $categorias){
	$registros = new Registros();

	$paraIncluir = array();
	$incluidas = array();
	$registroFinal = array();

	echo 'NUEVAS<pre>'; print_r($categorias); echo '</pre>';

	if( is_array($categorias) ){

		if( $datos = $registros->getRegistros($proyecto) ){
			$incluidas = unserialize($datos[0]['registro']);
			if(is_array($incluidas)){
				$registroFinal = $incluidas;
			}else{
				$incluidas = array();
			}
			echo 'INCLUIDAS<pre>'; print_r($incluidas); echo '</pre>';
		}

		//compone array de registro final agregando solo los registros nuevos
		$paraIncluir = array_diff($categorias, $incluidas);
		echo 'PARA INCLUIR<pre>'; print_r($paraIncluir); echo '</pre>';

		//tiene nuevas
		if( !empty($paraIncluir) ){
			foreach($paraIncluir as $f => $val ){
				if( !empty($val) ){
					$registroFinal[] = $val;
				}
			}
		}

//		echo 'REGISTRO FINAL<pre>';print_r($registroFinal);echo '</pre>';

		//actualiza el registro
		if(!$registros->UpdateRegistro($proyecto, $registroFinal)){
			echo "ERROR: no se actualizo el registro. ajaxComponer.php GuardarCategorias";
		}
	}

	//actualiza el registro
	/*if(!$registros->UpdateRegistro($proyecto, $categorias)){
		echo "Error: ajaxComponer.php ExcluirCategorias() no se actualizo el registro en registros->UpdateRegistro() ";
	}*/
}

/**
 * EXLUYE CATEGORIAS DEL REGISTRO
 */
function ExcluirCategorias($proyecto, $categorias){
	$registros = new Registros();

	$incluidas = array();
	$registroFinal = array();

//	echo 'HA EXCLUIR<pre>'; print_r($categorias); echo '</pre>';

	if( is_array($categorias) ){

		if( $datos = $registros->getRegistros($proyecto) ){
			$incluidas = unserialize($datos[0]['registro']);
			if(is_array($incluidas)){
				$registroFinal = $incluidas;
			}else{
				$incluidas = array();
			}
//			echo 'INCLUIDAS<pre>'; print_r($incluidas); echo '</pre>';
		}

		//optiene los registros que no se excluyen
		$registroFinal = array_diff($incluidas, $categorias);
		//reordena los index
		$registroFinal = array_values($registroFinal);

//		echo 'REGISTRO FINAL<pre>';print_r($registroFinal);echo '</pre>';

		//actualiza el registro
		if(!$registros->UpdateRegistro($proyecto, $registroFinal)){
			echo "ERROR: no se actualizo el registro. ajaxComponer.php ExcluirCategorias";
		}
	}
}

/**
 * OBTIENE LAS CATEGORIAS INCLUIDAS DE UN PROYECTO
 * @param int $proyecto
 */
function Incluidas($proyecto){
	$registros = new Registros();
	$lista = "";

	if( $datos = $registros->getRegistros($proyecto)){
		$incluidas = unserialize($datos[0]['registro']);
//		echo '<pre>'; print_r($incluidas); echo '</pre>';

		$lista .= '<ul id="categoriasIncluidas" class="listIzquierda">';

		foreach ($incluidas as $f => $str) {
			//echo $path.'<br/>';
			$strPath = '';
			$path = explode(',', $str);

			if( is_array($path) ){
				$nombre = CategoriaNombre( $path[sizeof($path)-1] );
				$id = $path[sizeof($path)-1];

				$lista .= '<li id="in'. $id .'" title="'. $nombre .' Categoria Incluida">';

				foreach ($path as $f => $categoria) {

					$nombre = CategoriaNombre($categoria);

					if( $f != sizeof($path)-1 ){
						$strPath .= $categoria.",";
						$lista .= '<span id="path'.$categoria.'">'.$nombre.' / </span>';
					}else{
						$strPath .= $categoria;
						$lista .= '<span id="'.$strPath.'"><b>'.$nombre.'</b></span>';
					}
				}
				$lista .= '</li>';
			}
		}

		$lista .= '</ul>';

	}else{
		$lista .= '<ul id="categoriasIncluidas" class="listIzquierda">
						<li class="nodata">No hay categorias incluidas</li>
				   </ul>';
	}
	echo $lista;
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
 * DETERMINA SI UNA CATEGORIA ESTA INCLUIDA
 * @param int $proyecto id
 * @param int $categoria id
 */
function Incluida($proyecto, $categoria, $datos=""){

	if( $datos === "" ){
		$registros = new Registros();
		$datos = $registros->getRegistros($proyecto);
	}

	if( !empty($datos) ){

		foreach($datos as $index => $valor ){
			$incluidas = unserialize($valor['registro']);

			foreach( $incluidas as $fila => $incluida ){
				$array = explode(",",$incluida);

				if( is_array($array) ){
					if( in_array($categoria, $array) ){
						return true;
					}
				}
			}

		}
	}

	return false;
}
