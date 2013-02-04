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

?>