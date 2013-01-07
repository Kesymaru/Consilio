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
			Categorias();
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

	}
}
/**
 * MUESTRA LAS CATEGORIAS DISPONIBLES PARA SELECCIONAR
 * EN UN PROYECTO
 */
function Categorias(){
	echo '<div id="categorias-componer">
				<div class="titulo">
					Categorias
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
		$datos = "No hay registros.";

		if(!empty($registro[0]['registros'])){
			$datos = unserialize($registro[0]['registros']);			
		}

		$lista = '<div id="proyectos" class="tipos">
				<input type="hidden" name="proyecto" id="proyecto" value="'.$id.'"/>
				<div class="titulo">
					'.$proyecto[0]['nombre'].'
			  		<button type="button" title="Buscar Proyectos" onClick="BuscarContent(\'buscar-Composicion\')">Buscar</button>
					<hr>
					<div class="busqueda">
						<input type="text" title="Escriba Para Buscar." id="buscar-Composicion" placeholder="Buscar"/>
					</div>
			  	</div>';

		$lista .= '<table class="table-list">
				   <tr>
				      <td>
				      	Categorias Incluidas
				      </td>
				   </tr>
				   <tr onClick="SelectCategoriaIncluida()">
				   	  <td>
				   	  	cATEGORIA
				   	  </td>
				   </tr>
				   </table>';

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



?>