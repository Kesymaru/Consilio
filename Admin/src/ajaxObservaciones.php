<?php
/**
* AJAX PARA LAS OBSERVACIONES
*/
require_once("class/registros.php");

if( isset($_POST['func']) ){
	switch ( $_POST['func'] ) {
		
		//lista de los tipos de observaciones
		case 'TiposObservaciones':
			TiposObservaciones();
			break;
		
		//formulario para nuevo tipo
		case 'NuevoTipo':
			NuevoTipo();
			break;

		//REGISTRA UN NUEVO TIPO DE OBSERVACION
		case 'RegistrarTipoObservacion':
			if( isset($_POST['nombre'])){
				RegistrarTipoObservacion( $_POST['nombre'] );
			}
			break;

		case 'EditarTipo':
			if( isset($_POST['id'])){
				EditarTipo( $_POST['id'] );
			}
			break;

		case 'ActualizarTipoObservacion':
			if( isset($_POST['id']) && isset($_POST['nombre']) ){
				ActualizarTipoObservacion( $_POST['nombre'], $_POST['id'] );
			}else{
				echo "Error: ajaxObservaciones.php ActualizarTipoObservacion no se especifica el id o el nombre del tipo.";
			}
			break;

		case 'DeleteTipo':
			if(isset($_POST['id'])){
				DeleteTipo( $_POST['id'] );
			}
			break;

		/**************** OBSERVACIONES **************/

		//formulario para nueva observacion
		case 'NuevaObservacion':
			if( isset($_POST['proyecto']) && isset($_POST['categoria']) && isset($_POST['norma']) && isset($_POST['articulo']) ){
				NuevaObservacion( $_POST['proyecto'], $_POST['categoria'], $_POST['norma'], $_POST['articulo'] );
			}
			break;

		//registra una nueva observacion
		case 'RegistrarObservacion':
			if( isset($_POST['proyecto']) && isset($_POST['categoria']) && isset($_POST['norma']) && isset($_POST['articulo']) && isset($_POST['tipo']) && isset($_POST['observacion-nueva']) ){
				RegistrarObservacion( $_POST['proyecto'], $_POST['categoria'], $_POST['norma'], $_POST['articulo'], $_POST['tipo'], $_POST['observacion-nueva'] );
			}
			break;

		//EDICION DE UNA OBSERVACION
		case 'EditarObservacion':
			if( isset($_POST['id']) ){
				EditarObservacion( $_POST['id'] );
			}
			break;

		//actualiza una observacion
		case 'ActualizarObservacion':
			if( isset($_POST['observacion-nueva']) && isset($_POST['tipo']) && isset($_POST['id']) ){
				ActualizarObservacion($_POST['observacion-nueva'], $_POST['tipo'], $_POST['id'] );
			}
			break;

		//elimina observacion
		case 'DeleteObservacion':
			if( isset($_POST['id']) ){
				DeleteObservacion( $_POST['id'] );
			}
			break;

		/**************** HELPERS ******************/

		case 'getNombreTipo':
			if( isset($_POST['id']) ){
				echo getNombreTipo( $_POST['id'] );
			}
			break;
	}
}

/**
* OBTIENE LOS TIPOS DE OBSERVACIONES 
*/
function TiposObservaciones(){
	$registros = new Registros();

	$datos = $registros->getTiposObservaciones();

	$lista = '<div class="tipos">
				<div class="titulo">
				Tipos Observaciones
			   </div>';

	if(!empty($datos)){	
		$lista .= '<ul id="tipos-observacion">';

		foreach ($datos as $fila => $tipo) {

			$lista .= '<li id="'.$tipo['id'].'" >'.$tipo['nombre'].'</li>';

		}
		$lista .= '</ul>';
	}else{
		$lista .= 'No hay tipos';
	}

	$lista .= '</div>
		 <div class="menu-botones">
			<button  class="ocultos" id="EliminarTipoObservacion" title="Eliminar Tipo Observacion" onClick="EliminarTipoObservacion()">Eliminar</button>
			<button  class="ocultos" title="Editar Tipo Observacion" id="EditarTipoObservacion" onClick="EditarTipoObservacion()">Editar</button>
			<button title="Crear Nueva Tipo Observacion" onClick="NuevoTipoObservacion()">Nueva Tipo</button>
		 </div>';

	echo $lista;
}

/**
* FORMULARIO PARA NUEVO TIPO
*/
function NuevoTipo(){
	$formulario = '';

	$formulario .= '<form id="FormularioNuevoTipoObservacion" enctype="multipart/form-data" method="post" action="src/ajaxObservaciones.php" >
						<input type="hidden" name="func" value="RegistrarTipoObservacion" >
						<div class="titulo">
							Nuevo Tipo Observacion
						</div>
						<div class="datos">
							<table>
							<tr>
								<td>
									Nombre
								</td>
								<td>
									<input type="text" name="nombre" id="nombre" placeholder="Nombre" class="validate[required]"  >
								</td>
							</tr>
							</table>
							<br/><br/>
						</div>
						<div class="datos-botones">
							<button type="button" onClick="CancelarContent()" >Cancelar</button>
							<input type="reset" value="Limpiar">
							<input type="submit" value="Guardar" >
						</div>
					</form>';

	echo $formulario;
}

/**
* REGISTRA UN NUEVO TIPO DE OBSERVACION
*/
function RegistrarTipoObservacion($nombre){
	$registros = new Registros();

	if( !$registros->NewTipoObservacion($nombre) ){
		echo 'Error: ajaxObservaciones.php RegistrarTipoObservacion().<br/>';
	}
}

/**
* EDITAR UN TIPO DE OBSERVACION
* @param $id -> id del tipo ha editar
*/
function EditarTipo($id){
	$registros = new Registros();
	$datos = $registros->getTipoObservacionDatos($id);

	$formulario = '';

	if( !empty($datos) ){
		$formulario .= '<form id="FormularioEditarTipoObservacion" enctype="multipart/form-data" method="post" action="src/ajaxObservaciones.php" >
						<input type="hidden" name="func" value="ActualizarTipoObservacion" >
						<input type="hidden" name="id" value="'.$id.'" >
						<div class="titulo">
							Nuevo Tipo Observacion
						</div>
						<div class="datos">
							<table>
							<tr>
								<td>
									Nombre
								</td>
								<td>
									<input type="text" name="nombre" id="nombre" placeholder="Nombre" class="validate[required]"  value="'.$datos[0]['nombre'].'">
								</td>
							</tr>
							</table>
							<br/><br/>
						</div>
						<div class="datos-botones">
							<button type="button" onClick="CancelarContent()" >Cancelar</button>
							<input type="reset" value="Limpiar">
							<input type="submit" value="Guardar" >
						</div>
					</form>';
	}else{
		$formulario .= "Error: no hay datos sobre el tipo de observacion.<br/><br/>
						Intentelo de nuevo.<br/>
						<script>
						notificaError('ERROR: ajaxObservaciones.php EditarTipo().<br/>No hay datos del tipo solicitado.<br/>id: $id')
						</script>";
	}

	echo $formulario;
}

/**
* ACTUALIZA UN TIPO
* @param $nombre
* @param $id
*/
function ActualizarTipoObservacion($nombre, $id){
	$registros = new Registros();

	if( !$registros->UpdateTipoObservacion($nombre, $id) ){
		echo "Error: no se pudo actualizar el tipo de observacion.";
	}
}

/**
* ELIMINA UN TIPO
* @param $id -> id del tipo
*/
function DeleteTipo( $id ){
	$registros = new Registros();

	if( !$registros->DeleteTipoObservacion($id) ){
		echo "Error: ajaxObservaciones.php DeleteTipo().<br/>No se pudo eliminar el tipo id = $id";
	}
}

/************************ OBSERVACIONES ****************/

/**
* FORMULARIO PARA UNA NUEVA OBSERVACION
*/
function NuevaObservacion($proyecto, $categoria, $norma, $articulo){
	$formulario = '<form id="FormularioNuevaObservacion" enctype="multipart/form-data" method="post" action="src/ajaxObservaciones.php" >
					<div class="titulo">
						Nueva Observacion
					</div>
					<input type="hidden" name="func" value="RegistrarObservacion" >
					<input type="hidden" name="proyecto" value="'.$proyecto.'" >
					<input type="hidden" name="categoria" value="'.$categoria.'" >
					<input type="hidden" name="norma" value="'.$norma.'" >
					<input type="hidden" name="articulo" value="'.$articulo.'" > ';

	if( $tipos = TiposDisponibles() ){
		$formulario .= '<table>
						<tr>
							<td>
								Tipo
							</td>
							<td>
								'.$tipos.'
							</td>
						</tr>
						</table>
						<textarea id="observacion-nueva" name="observacion-nueva"></textarea>';

	}else{
		//no hay datos
		$formulario .= 'No hay tipos para observaciones diponibles.<br/>
						Debe crear almenos un tipo de observacion para poder crear una observacion.<br/>
						<p>Edicion -> Tipos Observacion</p>';
	}
	
	$formulario .= '<div class="observacion-botonera">
						<button type="button" onClick="ObservacionCancelar()" >Cancelar</button>
						<input type="submit" value="Guardar" onClick="EditorUpdateContent()" >
					</div>';

	$formulario .= '</form>';

	echo $formulario;
}

/**
* OBTIEN LOS TIPOS DE OBSERVACIONES DISPONIBLES
*/
function TiposDisponibles(){
	$registros = new Registros();

	$tipos = $registros->getTiposObservaciones();

	$select = '';

	if(!empty($tipos)){
		$select .= '<select name="tipo" id="tipo">';
		
		foreach ($tipos as $fila => $tipo) {
			$select .= '<option value="'.$tipo['id'].'">
						'.$tipo['nombre'].'
						</option>';
		}

		$select .= '</select>';

		return $select;
	}else{
		return false;
	}
}

/**
* REGISTRA UNA OBSERVACION NUEVA
* @param int $proyecto -> id del proyecto
* @param int #categoria -> id de la categoria
* @param int $norma -> id de la norma
* @param int $articulo -> id del articulo
* @param int $tipo -> id del tipo
* @param string $observacion -> html/text de la observacion
*/
function RegistrarObservacion( $proyecto, $categoria, $norma, $articulo, $tipo, $observacion){
	$registro = new Registros();

	if( !$registro->RegistrarObservacion($proyecto, $categoria, $norma, $articulo, $tipo, $observacion ) ){
		echo 'Error: ajaxObservaciones.php RegistrarObservacion().<br/>No se pudo crear la nueva observacion.';
	}
}

/**
 * FORMULARIO DE EDICION DE UNA OBASERVACION
 * @param int $id -> id de la obaservacion
 */
function EditarObservacion( $id ){
	$registros = new Registros();

	$datos = $registros->getObservacion( $id );

	if( !empty($datos) ){
		$formulario = '<form id="FormularioEditarObservacion" enctype="multipart/form-data" method="post" action="src/ajaxObservaciones.php" >
						<div class="titulo">
							Edición  Observacion
						</div>
						<input type="hidden" name="func" value="ActualizarObservacion" >
						<input type="hidden" id="observacionId" name="id" value="'.$id.'" >';

		if( $tipos = SelectedTipos( $datos[0]['tipo']) ){
			$formulario .= '<table>
							<tr>
								<td>
									Tipo
								</td>
								<td>
									'.$tipos.'
								</td>
							</tr>
							</table>
							<textarea id="observacion-nueva" name="observacion-nueva">'.base64_decode($datos[0]['observacion']).'</textarea>';

		}else{
			$formulario .= 'No hay tipos para observaciones diponibles.<br/>
							Debe crear almenos un tipo de observacion para poder crear una observacion.<br/>
							<p>Edicion -> Tipos Observacion</p>';
		}
		

	}else{
		$formulario .= "No hay datos de la observacion.";
	}
		
	$formulario .= '<div class="observacion-botonera">
						<button type="button" onClick="ObservacionCancelar()" >Cancelar</button>
						<input type="submit" value="Guardar" onClick="EditorUpdateContent()">
					</div>';

	$formulario .= '</form>';

	echo $formulario;
}

/**
 * 
 */
function SelectedTipos($id){
	$registros = new Registros();

	$tipos = $registros->getTiposObservaciones();

	$select = '';

	if(!empty($tipos)){
		$select .= '<select name="tipo" id="tipo">';
		
		foreach ($tipos as $fila => $tipo) {
			if($tipo['id'] == $id ){
				$select .= '<option value="'.$tipo['id'].'" selected>
						'.$tipo['nombre'].'
						</option>';
			}else{
				$select .= '<option value="'.$tipo['id'].'">
						'.$tipo['nombre'].'
						</option>';
			}
		}

		$select .= '</select>';

		return $select;
	}else{
		return false;
	}
}

/**
 * ACTUALIZA UNA OBSERVACION
 */
function ActualizarObservacion($observacion, $tipo, $id){
	$registros = new Registros();

	if( !$registros->UpdateObservacion($observacion, $tipo, $id) ){
		echo "Error: ajaxObservaciones.php ActualizarObservacion().<br/>NO se pudo actualizar la observacion id = $id";
	}
}

/**
* ELIMINA UNA OBSERVACION
* @param int $id => id de la observacion
*/
function DeleteObservacion( $id ){
	$registros = new Registros();

	if( !$registros->DeleteObservacion( $id )){
		echo 'Error: no se pudo eliminar la observacion '.$id.'<br/>';
	}
}

/************************* HELPRES ***************/

/**
* OBTIENE L NOMBRE DEL TIPO DE OBSERVACION
* @param int $uid -> id del tipo
*/
function getNombreTipo($id){
	$registros = new Registros();

	$nombre = $registros->getTipoObservacionDato("nombre", $id);

	if( empty($nombre) ){
		return 'Observacion'; //nombre por defecto
	}else{
		return $nombre;
	}
}

?>