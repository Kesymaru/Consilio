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

		//ACTUALIZA DATOS AL SELECCIONAR NORMAS PARA LA CATEGORIA
		case 'ActualizarCategoria':
			if( isset($_POST['categoria']) && isset($_POST['normas']) ){
				ActualizarCategoria( $_POST['categoria'], $_POST['normas']);
			}
			break;

		//ACTUALIZA DATOS AL EDITAR LA CATEGORIA
		case 'ActualizarDatosCategoria':
			if( isset($_POST['nombre']) && isset($_POST['categoria']) ){
				ActualizarDatosCategoria($_POST['categoria'], $_POST['nombre']);
			}
			break;

		// CARGA EL BOX PARA EDITAR NUEVA SUBCATEGORIA
		case 'NuevaCategoria':
			if( isset($_POST['padre'])){
				echo NuevaCategoria( $_POST['padre'] );				
			}
			break;
		
		//GUARDA UNA NUEVA SUBCATEGORIA
		case 'RegistrarCategoria':
			if( isset($_POST['padre']) && isset($_POST['nombre']) ){
				RegistrarCategoria($_POST['nombre'], $_POST['padre']);
			}
			break;

		//ELIMINA UN CATEGORIA Y TODOS SUS HIJOS
		case 'DeleteCategoria':
			if(isset($_POST['categoria']) ){
				DeleteCategoria($_POST['categoria']);
			}
			break;

		//EDITAR CATEGORIA
		case 'EditarCategoria':
			if(isset($_POST['categoria'])){
				EditarCategoria($_POST['categoria']);
			}
			break;

		case 'ListaCategorias':
			if(isset($_POST['padre'])){
				ListaCategorias($_POST['padre']);
			}
			break;

		case 'OrdenarCategorias':
			if(isset($_POST['categorias'])){
				OrdenarCategorias( $_POST['categorias'] );
			}
			break;
	}
}

/**
* MUESTRA LAS CATEGORIAS ROOT
*/
function Padres(){
	echo '<div id="categorias">
				<div class="titulo">
					Categorias
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
		echo '</div><!-- end root ->';
	}else{
		echo 'No hay categorias.
				</div><!-- end root -->';
		
		//muestra boton para crear supercategoria
		echo '<div class="datos-botones">
			   		<button type="button" id="NuevaCategoria" title="Crear Nueva Categoria" onClick="NuevaCategoria(0)">Nueva Categoria</button>
			   </div>';
	}
	echo '</div><!-- end categorias -->';
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
* MUESTRA LAS NORMAS DE LA CATEGORIA CON LA TABLA DE SELECCION
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
					  	</div>
					  	<input type="hidden" name="func" value="ActualizarCategoria" />
					  	<input type="hidden" id="categoria" name="categoria" value="'.$categoria.'" />
					  	<div class="datos">
					  		<table>
					  		<tr>
					  			<td>Nombre</td>
					  			<td>
					  				<input type="text" name="nombre" id="nombre" title="Nombre De La Categoria" value="'.$nombre.'" placeholder="Nombre" class="validate[required]" />
					  			</td>
					  		</tr>
					  		</table>
					  		<table id="normas-categoria">
					  			<tr>
					  				<th colspan="2">
					  					Normas Incluidas
					  					<button type="button" title="Buscar Normas Incluidas" onClick="BuscarNormaCategoria(\'incluidas\')">Buscar</button>
					  				</th>
					  				<th colspan="2">
					  					Normas Disponibles
					  					<button type="button" title="Buscar Normas Disponibles" onClick="BuscarNormaCategoria(\'disponibles\')">Buscar</button>
					  				</th>
					  			</tr>
					  			<tr>
					  				<td id="buscar-seleccionadas">
					  					<input type="text" title="Escriba Para Buscar" placeholder="Buscar" />
					  				</td>

					  				<td class="control" onClick="QuitarNormasSeleccionadas()" rowspan="2" title="Excluir Selecciones">
					  					>
					  				</td>
					  				<td class="control" onClick="AgregarNormasSeleccionadas()" rowspan="2" title="Incluir Selecciones">
					  					<
					  				</td>

					  				<td id="buscar-disponibles">
					  					<input type="text" title="Escriba Para Buscar" placeholder="Buscar" />
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
					  		<button type="button" title="Cancelar Edición" onClick="CancelarContent()">Cancelar</button>
							<button type="button" title="Limpiar Edición" onClick="NormasCategoria('.$categoria.')" >Limpiar</button>
							<input type="submit" title="Guardar Edición" value="Guardar" />
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
* FROMULARIO PARA EDITAR UNA CATEGORIA
* @param $id -> id de la categoria ha editar
* @return $formulario -> formulario compuesto
*/
function EditarCategoria($id){
	$registros = new Registros();

	$datos = $registros->getCategoria($id);
	$formulario = '';

	if(!empty($datos)){

		if($datos[0]['padre'] != 0){
			$titulo = 'Categoria';
		}else{
			$titulo = 'Supercategoria';
		}

		$formulario = '<form id="FormularioEditarCategoria" enctype="multipart/form-data" method="post" action="src/ajaxEdicion.php" >
						<div id="tipos" class="tipos">
							<div class="titulo">
								Edicion '.$titulo.'
						  	</div>
						  	<input type="hidden" name="func" value="ActualizarDatosCategoria" />
						  	<input type="hidden" id="categoria" name="categoria" value="'.$id.'" />
						  	<div class="datos">
						  		<table>
						  		<tr>
						  			<td>Nombre</td>
						  			<td>
						  				<input type="text" name="nombre" id="nombre" placeholder="Nombre" class="validate[required]" value="'.$datos[0]['nombre'].'" />
						  			</td>';
		
		if($datos[0]['padre'] == 0){
			$formulario .= '<td rowspan="2" class="td-category-image">
								<img id="imagen-categoria" src="'.$datos[0]['imagen'].'" />
							</td>';
		}

		$formulario .= '</tr>';

		if($datos[0]['padre'] == 0){
			$formulario .= '<tr>
								<td colspan="2">
								<input title="Cambie la imagen de la Supercategoria" type="file" name="imagen" onChange="PreviewImage(this, \'imagen-categoria\')" />
								</td>
							</tr>';
		}
		
		$formulario .= '</table>
						<br/><br/>';

	}else{
		$formulario .= '<div id="tipos" class="tipos">
							<div class="titulo">
								Error Cateogria
						  	</div>
						  	<div class="datos error" >
						  		Categoria no encontrada.<br/>
						  		Intentelo de nuevo.
						  	</div>
						';
	}
					  		
	$formulario .=	'</div>
					  	<div class="datos-botones">
					  		<button type="button" onClick="CancelarContent()">Cancelar</button>
					  		<input type="reset" value="Limpiar" />
							<input type="submit" value="Guardar" />
						</div>
					</form>';

	echo $formulario;
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
		echo 'Error. No se pudo actualizar la categoria.<br/>ajaxEdicion.php ActualizarCategoria()';
	}

}

/**
* ACTUALIZA LOS DATOS DE UNA CATEGORIA
* @param $id -> id categoria
* @param $nombre
*/
function ActualizarDatosCategoria($id, $nombre){
	$registro = new Registros();

	if(isset($_FILES['imagen'])){
		if( !$imagen = $registro->UploadImage($_FILES['imagen'], "images/categorias/") ){
			$imagen = "images/es.png"; //fallback
		}
	}else{
		$imagen = ""; //no tiene imagen
	}

	if( !$registro->UpdateDatosCategoria($nombre, $imagen, $id) ){
		echo 'Error: No se pudo actualizar la categoria.<br/>ajaxEdicion.php ActualizarDatosCategoria()';
	}
}

/**
* FROMULARIO PARA NUEVA CATEGORIA
* @param $padre -> id del padre al que pertenece
* @return $formulario -> formulario compuesto
*/
function NuevaCategoria($padre){
	$formulario = '';
	
	if($padre != 0){
		$titulo = 'Categoria';
	}else{
		$titulo = 'Supercategoria';
	}

	$formulario = '<form id="FormularioNuevaCategoria" enctype="multipart/form-data" method="post" action="src/ajaxEdicion.php" >
					<div id="tipos" class="tipos">
						<div class="titulo">
						Nueva '.$titulo.'
					</div>
					  	<input type="hidden" name="func" value="RegistrarCategoria" />
					  	<input type="hidden" id="padre" name="padre" value="'.$padre.'" />
					  	<div class="datos">
					  		<table>
					  		<tr>
					  			<td>Nombre</td>
					  			<td>
					  				<input type="text" name="nombre" id="nombre" placeholder="Nombre" class="validate[required]" title="Nombre de la '.$titulo.'" />
					  			</td>';
	if($padre == 0){
		$formulario .=   '<td rowspan="2" class="td-category-image">
							<img id="imagen-categoria" title="Imagen Para Supercategoria" src="images/es.png" />
						  </td>';
	}
	
	$formulario .= '		</tr>';

	if($padre == 0){
		$formulario .= '<tr>
						<td colspan="2">
							<input title="Seleccione una imagen para la Supercategoria" type="file" name="imagen" onChange="PreviewImage(this, \'imagen-categoria\')" />
						</td>
						</tr>';
	}
					  		
	$formulario .=	'	</table>
					<br/><br/>
					 </div>
					  	<div class="datos-botones">
					  		<button type="button" onClick="CancelarContent()">Cancelar</button>
					  		<input type="reset" value="Limpiar" />
							<input type="submit" value="Guardar" />
						</div>
					</form>';

	return $formulario;
}

/**
 * REGISTRA UNA NUEVA CATEGORIA
 * @param $nombre
 * @param $padre
 */
function RegistrarCategoria($nombre, $padre){
	$registro = new Registros();

	if($padre == 0){ //es root tiene imagen
		
		if(isset($_FILES['imagen'])){
			if( !$imagen = $registro->UploadImage($_FILES['imagen'], "images/categorias/") ){
				$imagen = "images/es.png"; //fallback
				echo "Error: Imagen no valida, no se ha podido subir.";
			}
		}else{
			$imagen = "images/es.png"; //default
		}

		if( !$registro->NewCategoria($nombre, $imagen, $padre) ){
			echo "Error: ajaxEdicion.php RegistrarCategoria() no se ha registrado la nueva categoria.";
		}
	}else{
		$imagen = ""; //no tiene imagen
		if( !$registro->NewCategoria($nombre, $imagen, $padre) ){
			echo "Error: ajaxEdicion.php RegistrarCategoria() no se ha registrado la nueva categoria.";
		}
	}
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

/**
* CREA LISTA ORDENADA DE LAS CATEGORIAS DE UN PADRE
* @param $padre -> id del padre
*/
function ListaCategorias($padre){
	$registros = new Registros();

	$lista = '<div class="titulo">
				Ordenar Categorias
			  		<img class="boton-buscar icon" title="Buscar Categorias" onClick="BusquedaFocus(\'busqueda-categorias\', \'buscar-categorias\', \'listaCategorias\', false)" src="images/search2.png" >
			  	</div>

			  	<div class="busqueda" id="busqueda-categorias">
					<div class="buscador">
						<input type="search" title="Escriba Para Buscar Categorias" id="buscar-categorias" placeholder="Buscar Categorias"/>
					</div>
				</div>
				<div class="datosLista">';

	$datos = $registros->getCategorias($padre);

	if( !empty($datos) ){
		$lista .= '<ul class="list" id="listaCategorias">';
		
		foreach ($datos as $fila => $categoria) {
			$lista .= '<li id="list'.$categoria['id'].'" >'.$categoria['nombre'].'</li>';
		}

		$lista .= '</ul>
					</div>';
	}else{
		$lista .= "No hay subcategorias.
					</div>";
	}

	$lista .= '<div class="datos-botones">
				<button type="button" title="Limpiar Orden" onClick="OrdenarCategorias('.$padre.')">Limpiar</button>
				<button type="button" onClick="GuardarOrdenCategorias()" title="Guardar Orden De La Lista" >Guardar</button>
			   </div>
			   <!-- fin botonera -->
			   </div>';

	echo $lista;
}

/**
* ORDENA LAS CATEGORIAS
* @param $categorias -> array[][] con el id de la categoria y su posicion en la lista
*/
function OrdenarCategorias($categorias){
	$registros = new Registros();
	$errors = '';

	if(is_array($categorias)){
		$posicion = 0;
		foreach ($categorias as $fila => $id) {
			$posicion++;

			//actualiza la psicion de la categori
			if( !$registros->UpdateCategoriaPosicion($id, $posicion) ){
				$errors .= 'Error: ajaxEdicion.php OrdenarCategorias().<br/>No se pudo actualizar la categoria con el id: '.$id.'<hr>';
			}
		}

		if($errors != ''){
			echo $errors;
		}
	}else{	
		echo "error NO ES ARRAY.<br/>";
		echo $categorias;
	}
}

?>