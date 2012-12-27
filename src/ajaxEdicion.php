<?php

require_once("class/imageUpload.php");
require_once("class/registros.php");


if(isset($_POST['func'])){
	
	switch ($_POST['func']){

		//CARGA LAS CATEGORIAS PADRES
		case 'Padres':
			Padres();
			break;

		//OBTIENE LOS HIJOS DE UN PADRE SELECCIONADO
		case 'Hijos':
			if(isset($_POST['padre'])){
				Hijos( $_POST['padre'] );
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

		//OBTIENE EL PADRE DE UN HIJO
		case 'GetPadre':
			if( isset($_POST['hijo']) ){
				$registros = new Registros();
				$padre = $registros->getPadre($_POST['hijo']);
				echo $padre;
			}
			break;

		//CARGA LOS DATOS DE UNA CATEGORIA EN UN FORMULARIO PARA LA EDICION
		case 'NormasCategoria':
			if( isset($_POST['categoria']) ){
				echo NormasCategoria( $_POST['categoria'] );
			}
			break;

		//ACTUALIZA DATOS, REGISTRA DATOS Y/O SUBE ARCHIVO
		case 'ActualizarCategoria':
			if( isset($_POST['categoria']) && isset($_POST['normas']) ){
				ActualizarCategoria( $_POST['categoria'], $_POST['normas']);
			}
			break;

		// CARGA EL BOX PARA EDITAR NUEVA SUBCATEGORIA
		case 'BoxNuevaCategoria':
			if( isset($_POST['padre'])){
				echo BoxNuevaCategoria( $_POST['padre'] );				
			}
			break;
		
		//GUARDA UNA NUEVA SUBCATEGORIA
		case 'NuevaSubCategoria':
			if( isset($_POST['padre']) && isset($_POST['nombre']) ){
				$registro = new Registros();

				if( $_POST['nombre'] != ''){
					//crea la nueva subcategoria
					$registro->NuevaSubCategoria($_POST['padre'], $_POST['nombre']);
				}else{
					echo "Debe tener un valor";
				}
			}
			break;

		//ELIMINA UN CATEGORIA Y TODOS SUS HIJOS
		case 'DeleteCategoria':
			if(isset($_POST['categoria']) ){
				DeleteCategoria($_POST['categoria']);
			}
			break;

	}
}

/**
* CATEGORIAS PADRES
*/
function Padres(){
	echo '<div id="categorias">
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
			echo '<li id="'.$id.'" onClick="Hijos('.$id.')">'.$nombre.'</li>';
		}
		echo '</ul>';
	}else{
		echo 'No hay datos.';
	}

	echo '</div>';
	echo '</div>';
}

/**
* CARGA CATEGORIAS HIJAS DE UN PADRE
* @param $padre -> id del padre
*/
function Hijos($padre){
	$registros = new Registros();
	$hijos = $registros->getHijos($_POST['padre']);

	if(!empty($hijos)){ //tiene hijos
		echo '<div class="categoria" id="Padre'.$_POST['padre'].'">';

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
			echo '<li id="'.$id.'" onClick="Hijos('.$id.')">'.$nombre.'</li>';
		}

		echo '</ul>';
		echo '</div>';

		//tiene hijos por lo tanto no es hoja
		//echo '<script>NormasCategoria('.$_POST['padre'].');</script>';

	}else{
		//no tiene hijos es una hoja
		if( !$registros->EsRoot($padre) ){
			echo '<script>NormasCategoria('.$_POST['padre'].');</script>';
		}
	}
}

/**
* MUESTRA LAS NORMAS DE LA CATEGORIA CON LA OPCION DE SELECCIONAR LAS
* @param $categoria -> id de la categoria 
* @return $formulario -> con la lista compuesta
*/
function NormasCategoria($categoria){
	$formulario = '';
	$registros = new Registros();
	
	$nombre = $registros->getCategoriaDato("nombre", $categoria);

	$formulario = '<form id="FormularioNormasCategoria" enctype="multipart/form-data" method="post" action="src/ajaxEdicion.php" >
					<div id="tipos" class="tipos">
						<div class="titulo">
							Normas de '.$nombre.'
					  		<hr>
					  	</div>
					  	<input type="hidden" name="func" value="ActualizarCategoria" />
					  	<input type="hidden" name="categoria" value="'.$categoria.'" />
					  	<div class="datos">
					  		<table>
					  		<tr>
					  			<td>Nombre</td>
					  			<td>
					  				<input type="text" name="nombre" id="nombre" value="'.$nombre.'" placeholder="Nombre" class="validate[required]" />
					  			</td>
					  		</tr>
					  		</table>
					  		<table id="normas-categoria">
					  			<tr>
					  				<th colspan="2">
					  					Normas Incluidas
					  				</th>
					  				<th colspan="2">
					  					Normas Disponibles
					  				</th>
					  			</tr>
					  			<tr>
					  				<td id="td-seleccionadas">
					  					<br/>';
	$formulario .= SelectedNormas($categoria).'
					  					<br/>
					  				</td>
					  				<td class="control" onClick="QuitarNormasSeleccionadas()">
					  					>
					  				</td>
					  				<td class="control" onClick="AgregarNormasSeleccionadas()">
					  					<
					  				</td>
					  				<td id="td-diponibles">
					  					<br/>';
	$formulario .= Normas($categoria).'
										<br/>
					  				</td>
					  			</tr>
					  		</table>
					  	</div>
					  	<div class="datos-botones">
					  		<button type="button" onClick="CancelarContent()">Cancelar</button>
							<input type="reset" value="Borrar" />
							<input type="submit" value="Guardar" />
						</div>
					</form>';

	return $formulario;
}

/**
* OBTIEN LAS NORMAS DE LA CATEGORIA
* @param $categoria -> id de la categoria
* @return $lista -> lista compuesta con las normas
*/
function Normas($categoria){
	$lista = "";
	$registros = new Registros();

	$normas = $registros->getNormas();

	if(!empty($normas)){	
		$lista .= '<ul id="disponibles">';
		foreach ($normas as $fila => $norma) {
			$lista .= '<li id="norma'.$norma['id'].'" onClick="SelectNorma('.$norma['id'].')">'.$norma['nombre'].'</li>';
		}
		$lista .= '</ul>';
	}else{
		$lista .= 'No hay normas.<br>
				   Por favor ingrese normas, puede agregar normas en:<br/>
				   Edicion->Normas<br/>';
	}
	
	return $lista;
}

function SelectedNormas($categoria){
	$lista = '';
	$registros = new Registros();

	$seleccionadas = $registros->getSelectedNormas($categoria);
	$normas = $registros->getNormas();

	if(!empty($normas) && !empty($seleccionadas)){
		$lista .= '<ul id="seleccionadas">';
		foreach ($normas as $fila => $norma) {

			foreach ($seleccionadas as $valor ) {
				echo $valor;

				if($valor == $norma['id']){
					$lista .= '<li>'.$norma['nombre'].'</li>';
				}
			}
		}
		$lista .= '</ul>';
	}else{
		$lista .= '<ul id="seleccionadas"></ul>';
	}

	return $lista;
}

/**
* OBTIENE LOS DATOS ENVIADOS Y LOS GUARDA
* @param $categoria -> id de la categoria ha registrar
* @param $normas -> array[] con las normas seleccionadas
* @return true -> si actualiza bien
*/
function ActualizarCategoria($categoria, $normas){
	$registros = new Registros();

	//actualiza nombre de la categoria
	if( !$registros->UpdateCategoria($_POST['nombre'], $normas, $categoria) ){
		echo 'Error. No se pudo actualizar la categoria.';
	}

}

/**
* BOX PARA NUEVA SUBCATEGORIA
* @param $padre -> id del padre al que pertenece
*/
function BoxNuevaCategoria($padre){
	$box = '';
	$box .= '<form id="FormularioSubCategoria" enctype="multipart/form-data" method="post" action="src/ajaxEdicion.php" >
		<div id="nivel1">
		<div id="nombreNorma">
			Nueva Categoria
		</div>
		<div class="datos dark-input">
			<input type="hidden" id="padre" name="padre" value="'.$padre.'"/>
			<input type="hidden" name="func" id="func" value="NuevaSubCategoria" />
			<br/><br/>
			<input type="text" data-prompt-position="bottomLeft" class="validate[required]" name="nombre" placeholder="Nombre" />
			<br/><br/><br/>
			
		</div>
		</div>
		<button type="button" onClick="CancelarNuevaCateogria('.$padre.')">Cancelar</button>
		<input type="reset" value="Borrar" />
		<input type="submit" value="Guardar" />
			</form>';

	return $box;
}

/**
* ELIMINA UNA CATEGORIA Y TODOS SU HIJOS
* @param $categoria -> id de la categoria
*/
function DeleteCategoria($categoria){
	$registros = new Registros();
	$hijos = $registros->getHijos($categoria);

	if(!empty($hijos)){
		//BORRA TODAS LAS SUBCATEGORIAS DE LA CATEGORIA
		foreach ($hijos as $filas => $hijo) {
			DeleteCategoriaRecursivo($hijo['id']);
			$registros->DeleteCategoria($hijo['id']);
		}
	}

	//BORRA LA CATEGORIA
	$registros->DeleteCategoria($categoria);
}

/**
* BORRA LOS HIJOS DE UNA CATEGORIA RECURSIVAMENTE
* @param $padre -> id del padre
*/
function DeleteCategoriaRecursivo($padre){

	if( TieneHijos($padre) ){
		$registros = new Registros();
		$hijos = $registros->getHijos($padre);

		foreach ($hijos as $fila => $hijo) {
			if(TieneHijos($hijo['id'])){
				DeleteCategoriaRecursivo( $hijo['id'] );
				$registros->DeleteCategoria($hijo['id']);
			}else{
				$registros->DeleteCategoria( $hijo['id'] );
			}
		}
	}

}

/**
* DETERMINA SI UN PADRE TIENE HIJOS
*/
function TieneHijos($padre){
	$registros = new Registros();
	$hijos = $registros->getHijos($padre);
	
	if(!empty($hijos)){
		return true;
	}else{
		return false;
	}
}


?>