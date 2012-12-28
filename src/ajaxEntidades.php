<?php

/**
* AJAX PARA ENTIDADES
*/

require_once("class/registros.php");


if(isset($_POST['func'])){
	
	switch ($_POST['func']){

		//CARGA LAS ENTIDADES
		case 'Entidades':
			Entidades();
			break;

		//CARGA EL FORMULARIO PARA UN NUEVO GRUPO
		case 'NuevoGrupo':
			NuevoGrupo();
			break;

		//CARGA FORMULARIO PARA NUEVA ENTIDAD
		case 'NuevaEntidad':
			NuevaEntidad();
			break;

		//REGISTRA NUEVA ENTIDAD
		case 'RegistrarEntidad':
			if( isset($_POST['padre']) && isset($_POST['nombre'])){
				RegistrarEntidad($_POST['padre'], $_POST['nombre']);
			}
			break;

		//REGISTRA UN NUEVO GRUPOs
		case 'RegistrarGrupo':
			if(isset($_POST['nombre'])){
				RegistrarGrupo();
			}
			break;

		//EDICION DE UNA ENTIDAD
		case 'EditarEntidad':
			if(isset($_POST['id'])){
				EditarEntidad($_POST['id']);
			}	
			break;

		//ELIMINA UNA ENTIDAD
		case 'DeleteEntidad':
			if(isset($_POST['id'])){
				DeleteEntidad($_POST['id']);
			}
			break;

		//ACTUALIZA UN ENTIDAD
		case 'ActualizarEntidad':
			if(isset($_POST['id'])){
				ActualizarEntidad($_POST['id'], $_POST['nombre']);
			}
	}
}

/**
* CARGA LA LISTA DE ENTIDADES
*/
function Entidades(){
	$registros = new Registros();

	$entidades = $registros->getPadresEntidades();

	$lista = '<div id="entidades" class="entidades">
				<div class="titulo">
					Entidades
			  		<button type="button" onClick="BuscarMenu(\'buscar-Entidades\')">Buscar</button>
					<hr>
					<div class="busqueda">
						<input type="text" id="buscar-Entidades" placeholder="Buscar"/>
					</div>
			  	</div>';

	if(!empty($entidades)){
		$lista .= '<ul>';

		foreach ($entidades as $fila => $entidad) {
			$lista .= '<li class="grupo" id="'.$entidad['id'].'" onClick="SelectEntidad('.$entidad['id'].')">'.$entidad['nombre'].'</li>';

			$hijas = $registros->getEntidadesHijas($entidad['id']);

			if(!empty($hijas)){
				foreach ($hijas as $fi => $hija) {
					$lista .= '<li class="hija '.$entidad['id'].'" id="'.$hija['id'].'" onClick="SelectEntidad('.$hija['id'].')">'.$hija['nombre'].'</li>';
				}
			}
		}

		$lista .= '</ul>';

	}else{
		$lista .= 'No hay entidades.';
	}

	$lista .= '<div class="datos-botones">
				<button type="button" id="EliminarEntidad" onClick="EliminarEntidad()">Eliminar</button>
				<button type="button" id="EditarEntidad" onClick="EditarEntidad()">Editar</button>
				<button type="button" id="NuevoEntidad" onClick="NuevoGrupo()">Nuevo Grupo</button>
			   	<button type="button" id="NuevoEntidad" onClick="NuevaEntidad()">Nueva Entidad</button>
			   </div>
			   <!-- fin botonera -->
			   </div>';

	echo $lista;
}

/**
* FORMULARIO PARA NUEVO GRUPO
*/
function NuevoGrupo(){
	$formulario = '<form id="FormularioNuevoGrupo" enctype="multipart/form-data" method="post" action="src/ajaxEntidades.php" >
					<div id="tipos" class="tipos">
						<div class="titulo">
							Nuevo Grupo de Entidades
					  		<hr>
					  	</div>
					  	<input type="hidden" name="func" value="RegistrarGrupo" />
					  	<div class="datos">
					  		<table>
					  			<td>
					  				Nombre
					  			</td>
					  			<td>
					  				<input type="text" name="nombre" placeholder="Nombre" class="validate[required]" />
					  			</td>
					  		</tr>
					  		</table>
					  		<br/>
					  		<br/>
					  	</div>
					  	<div class="datos-botones">
					  		<button type="button" onClick="CancelarContent()">Cancelar</button>
							<input type="reset" value="Borrar" />
							<input type="submit" value="Guardar" />
						</div>
					</form>';

	echo $formulario;
}

/**
* FORMULARIO PARA NUEVA ENTIDAD
*/
function NuevaEntidad(){
	$formulario = '<form id="FormularioNuevaEntidad" enctype="multipart/form-data" method="post" action="src/ajaxEntidades.php" >
					<div id="tipos" class="tipos">
						<div class="titulo">
							Nueva Entidad
					  		<hr>
					  	</div>
					  	<input type="hidden" name="func" value="RegistrarEntidad" />
					  	<div class="datos">
					  		<table>
					  		<tr>
					  			<td>Grupo</td>
					  			<td>
					  				';
	$formulario .= SelectsPadres().'
					  			</td>
					  		</tr>
					  			<td>
					  				Nombre
					  			</td>
					  			<td>
					  				<input type="text" name="nombre" placeholder="Nombre" class="validate[required]" />
					  			</td>
					  		</tr>
					  		</table>
					  		<br/>
					  		<br/>
					  	</div>
					  	<div class="datos-botones">
					  		<button type="button" onClick="CancelarContent()">Cancelar</button>
							<input type="reset" value="Borrar" />
							<input type="submit" value="Guardar" />
						</div>
					</form>';

	echo $formulario;
}

/**
* FORMULARIO DE EDICION DE UNA ENTIDAD
*/
function EditarEntidad($id){
	$registros = new Registros();
	$entidad = $registros->getEntidadDatos($id);

	if(!empty($entidad)){

		$formulario = '<form id="FormularioEditarEntidad" enctype="multipart/form-data" method="post" action="src/ajaxEntidades.php" >
						<div id="tipos" class="tipos">
							<div class="titulo">
								Edicion Entidad
						  		<hr>
						  	</div>
						  	<input type="hidden" name="func" value="ActualizarEntidad" />
						  	<input type="hidden" name="id" value="'.$id.'" />
						  	<div class="datos">
						  		<table>';
		if($entidad[0]['grupo'] == 0){
			$formulario .= '
						  		<tr>
						  			<td>Grupo</td>
						  			<td>
						  				';
			$formulario .= SelectedPadre($entidad[0]['padre']).'
						  			</td>
						  		</tr>';
		}
		$formulario .=  '
						  			<td>
						  				Nombre
						  			</td>
						  			<td>
						  				<input type="text" id="nombre" name="nombre" placeholder="Nombre" 
						  				value="'.$entidad[0]['nombre'].'" class="validate[required]" />
						  			</td>
						  		</tr>
						  		</table>
						  		<br/>
						  		<br/>
						  	</div>
						  	<div class="datos-botones">
						  		<button type="button" onClick="CancelarContent()">Cancelar</button>
								<input type="reset" value="Borrar" />
								<input type="submit" value="Guardar" />
							</div>
						</form>';
	}else{
		$formulario .= "Ocurrio un error, la entidad no se encuentra.<br/>
			Intentelo de nuevo.";
	}
	echo $formulario;
}

/**
* COMPONE EL SELECT CON LOS PADRES O GRUPOS DISPONIBLES
* PARA UNA NUEVA ENTIDAD
*/
function SelectsPadres(){
	$select = "";
	$registros = new Registros();
	$padres = $registros->getPadresEntidades();

	if(!empty($padres)){
		$select .= '<select name="padre" id="grupos" class="validate[required]" >';
		
		$select .= '<option value="0">Ninguno</option>';

		foreach ($padres as $fila => $padre) {
			$select .= '<option value="'.$padre['id'].'" >'.$padre['nombre'].'</option>';
		}

		$select .= '</select>';
	}

	return $select;
}

/**
* COMPONE EL SELECT PARA GRUPOS DE ENTIDADES , CON UNA OPCION SELECCIONADA
* @param $id -> id opcion seleccionada
*/
function SelectedPadre($id){
	$select = "";
	$registros = new Registros();
	$padres = $registros->getPadresEntidades();

	if(!empty($padres)){
		$select .= '<select name="padre" id="padre" class="validate[required]" >';
		
		$select .= '<option value="0">Ninguno</option>';

		foreach ($padres as $fila => $padre) {
			
			if($padre['id'] == $id){
				$select .= '<option value="'.$padre['id'].'" selected>'.$padre['nombre'].'</option>';
			}else{
				$select .= '<option value="'.$padre['id'].'" >'.$padre['nombre'].'</option>';
			}
			
		}

		$select .= '</select>';
	}

	return $select;
}

/**
* REGISTRA UN GRUPO NUEVO
*/
function RegistrarGrupo(){
	$registros = new Registros();

	if( !$registros->NewEntidadGrupo($_POST['nombre']) ){
		echo "Errr: no se pudo guradar el nuevo grupo";
	}
}

/**
* REGISTRA UNA ENTIDAD NUEVA
* @param $padre -> id del padre
*/
function RegistrarEntidad($padre, $nombre){
	$registros = new Registros();

	if( !$registros->NewEntidad($padre, $nombre) ){
		echo "Errr: no se pudo guradar la nueva entidad.";
	}
}


/*
* ELIMINA UNA ENTIDAD
/ @param id -> id de la entidad ha eliminar
*/
function DeleteEntidad($id){
	$registros = new Registros();

	if( !$registros->DeleteEntidad($_POST['id']) ){
		echo "Error. No se pudo borrar la entidad, intentelo de nuevo.";
	}
}

/**
*
*/
function ActualizarEntidad($id, $nombre){
	//tiene padre es entidad hija
	if(isset($_POST['padre'])){
		$padre = $_POST['padre'];
	}else{
		//entidad padre o grupo
		$padre = 0;
	}

	$registros = new Registros();

	if(!$registros->UpdateEntidad($id, $nombre, $padre)){
		echo "Error. No se pudo actualizar la entidad.";
	}
}

?>