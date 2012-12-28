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
		case 'NuevaCategoria':
			if( isset($_POST['padre'])){
				echo NuevaCategoria( $_POST['padre'] );				
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
					  					<button type="button" onClick="BuscarNormaCategoria(\'incluidas\')">Buscar</button>
					  				</th>
					  				<th colspan="2">
					  					Normas Disponibles
					  					<button type="button" onClick="BuscarNormaCategoria(\'disponibles\')">Buscar</button>
					  				</th>
					  			</tr>
					  			<tr>
					  				<td id="buscar-seleccionadas">
					  					<input type="text" placeholder="Buscar" />
					  				</td>

					  				<td class="control" onClick="QuitarNormasSeleccionadas()" rowspan="2">
					  					>
					  				</td>
					  				<td class="control" onClick="AgregarNormasSeleccionadas()" rowspan="2">
					  					<
					  				</td>

					  				<td id="buscar-disponibles">
					  					<input type="text" placeholder="Buscar" />
					  				</td>
					  			</tr>
					  			<tr>
					  				<td id="td-seleccionadas">';
	$formulario .= NormasSeleccionadas($categoria).'
					  					<br/>
					  				</td>
					  				<td id="td-disponibles">';
	$formulario .= NormasDisponibles($categoria).'
										<br/>
					  				</td>
					  			</tr>
					  		</table>
					  	</div>
					  	<div class="datos-botones">
					  		<button type="button" onClick="CancelarContent()">Cancelar</button>
							<button type="button" onClick="NormasCategoria('.$categoria.')" >Limpiar</button>
							<input type="submit" value="Guardar" />
						</div>
					</form>';

	return $formulario;
}

/**
* MUESTRA LAS NORMAS DISPONIBLES, EXCLUYENDO LAS YA SELECCIONADAS
* @param $categoria -> id de la categoria
* @return $lista -> lista compuesta con las normas
*/
function NormasDisponibles($categoria){
	$lista = "";
	$registros = new Registros();

	$seleccionadas = $registros->getSelectedNormas($categoria);
	$normas = $registros->getNormasHabilitadas();

	if(!empty($normas)){	
		$lista .= '<ul id="disponibles">';
		foreach ($normas as $fila => $norma) {
			
			$esta = false;

			//si tiene seleccionadas
			if(!empty($seleccionadas)){

				foreach ($seleccionadas as $valor) {
					if($norma['id'] == $valor){
						$esta = true;
						break;
					}
				}

				//si la norma no esta seleccionada
				if(!$esta){
					$lista .= '<li id="norma'.$norma['id'].'" onClick="SelectNorma('.$norma['id'].')">'.$norma['nombre'].' - '.$norma['numero'].'</li>';
				}
			//si no tiene seleccionadas muestra todas las normas
			}else{ 
				$lista .= '<li id="norma'.$norma['id'].'" onClick="SelectNorma('.$norma['id'].')">'.$norma['nombre'].' - '.$norma['numero'].'</li>';
			}
		}
		$lista .= '</ul>';
	}else{
		$lista .= 'No hay normas.';
	}
	
	return $lista;
}

/**
* MUESTRA LAS NORMAS SELECCIONADAS
* @param $categoria -> id de la categoria
* @return $lista -> lista de normas compuesta
*/ 
function NormasSeleccionadas($categoria){
	$lista = '';
	$registros = new Registros();

	$seleccionadas = $registros->getSelectedNormas($categoria);
	$normas = $registros->getNormasHabilitadas();

	if(!empty($normas) && !empty($seleccionadas)){
		$lista .= '<ul id="seleccionadas">';
		foreach ($normas as $fila => $norma) {

			foreach ($seleccionadas as $valor ) {

				if($valor == $norma['id']){
					//norma en la lista
					$lista .= '<li id="norma'.$norma['id'].'" onClick="SelectNorma('.$norma['id'].')">'.$norma['nombre'].' - '.$norma['numero'].'</li>';
					
					//inputs hiddens con los valores seleccionados
					$lista .= '<input id="normaSelected'.$norma['id'].'" type="hidden" name="normas[]" value="'.$norma['id'].'" />';
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

	//ordena los ids de las normas desendentemente
	sort($normas);

	//actualiza nombre de la categoria
	if( !$registros->UpdateCategoria($_POST['nombre'], $normas, $categoria) ){
		echo 'Error. No se pudo actualizar la categoria.';
	}

}

/**
* FROMULARIO PARA NUEVA CATEGORIA
* @param $padre -> id del padre al que pertenece
* @return $formulario -> formulario compuesto
*/
function NuevaCategoria($padre){
	$formulario = '';

	$formulario = '<form id="FormularioNormasCategoria" enctype="multipart/form-data" method="post" action="src/ajaxEdicion.php" >
					<div id="tipos" class="tipos">
						<div class="titulo">
							Nueva Categoria
					  		<hr>
					  	</div>
					  	<input type="hidden" name="func" value="RegistrarCategoria" />
					  	<input type="hidden" id="padre" name="padre" value="'.$padre.'" />
					  	<div class="datos">
					  		<table>
					  		<tr>
					  			<td>Nombre</td>
					  			<td>
					  				<input type="text" name="nombre" id="nombre" placeholder="Nombre" class="validate[required]" />
					  			</td>
					  		</tr>
					  		</table>
					  		<table id="normas-categoria">
					  			<tr>
					  				<th colspan="2">
					  					Normas Incluidas
					  					<button type="button" onClick="BuscarNormaCategoria(\'incluidas\')">Buscar</button>
					  				</th>
					  				<th colspan="2">
					  					Normas Disponibles
					  					<button type="button" onClick="BuscarNormaCategoria(\'disponibles\')">Buscar</button>
					  				</th>
					  			</tr>
					  			<tr>
					  				<td id="buscar-seleccionadas">
					  					<input type="text" placeholder="Buscar" />
					  				</td>

					  				<td class="control" onClick="QuitarNormasSeleccionadas()" rowspan="2">
					  					>
					  				</td>
					  				<td class="control" onClick="AgregarNormasSeleccionadas()" rowspan="2">
					  					<
					  				</td>

					  				<td id="buscar-disponibles">
					  					<input type="text" placeholder="Buscar" />
					  				</td>
					  			</tr>
					  			<tr>
					  				<td id="td-seleccionadas">
					  					<ul id="seleccionadas"></ul>
					  					<br/>
					  				</td>
					  				<td id="td-disponibles">';
	$formulario .= Normas().'
										<br/>
					  				</td>
					  			</tr>
					  		</table>
					  	</div>
					  	<div class="datos-botones">
					  		<button type="button" onClick="CancelarContent()">Cancelar</button>
							<button type="button" onClick="NuevaCategoria('.$padre.')" >Limpiar</button>
							<input type="submit" value="Guardar" />
						</div>
					</form>';

	return $formulario;
}

/**
* LISTA TODAS LAS NORMAS
* @return $lista -> lista compuesta con las normas
*/
function Normas(){
	$lista = "";
	$registros = new Registros();

	$normas = $registros->getNormasHabilitadas();

	if(!empty($normas)){	
		$lista .= '<ul id="disponibles">';

		foreach ($normas as $fila => $norma) {
			
			$lista .= '<li id="norma'.$norma['id'].'" onClick="SelectNorma('.$norma['id'].')">'.$norma['nombre'].' - '.$norma['numero'].'</li>';
		}
		$lista .= '</ul>';
	}else{
		$lista .= 'No hay normas.';
	}
	
	return $lista;
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