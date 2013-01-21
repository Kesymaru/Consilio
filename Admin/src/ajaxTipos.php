<?php

/**
* AJAX PARA TIPOS
*/

require_once("class/registros.php");


if(isset($_POST['func'])){
	
	switch ($_POST['func']){

		//CARGA LOS TIPOS
		case 'Tipos':
			Tipos();
			break;

		//CARGA FORMULARIO PARA NUEVO TIPO\
		case 'NuevoTipo':
			NuevoTipo();
			break;

		//REGISTRA UN NUEVO TIPO
		case 'RegistrarTipo':
			if(isset($_POST['nombre'])){
				RegistrarTipo($_POST['nombre']);
			}
			break;

		//CARGA LA EDICION DE UN TIPOs
		case 'EditarTipo':
			if(isset($_POST['id'])){
				EditarTipo($_POST['id']);
			}
			break;

		//ELIMINA UN TIPO
		case 'EliminarTipo':
			if(isset($_POST['id'])){
				EliminarTipo($_POST['id']);
			}
			break;

		//ACTUALIZA UN TIPO
		case 'ActualizarTipo':
			if(isset($_POST['nombre']) && isset($_POST['tipo'])){
				ActualizarTipo($_POST['nombre'], $_POST['tipo']);
			}
			break;
	}
}

/**
* CARGA LA LISTA CON TODOS LOS TIPOS
*/
function Tipos(){
	$registros = new Registros();
	$tipos = $registros->getTipos();

	$lista = '<div id="tipos" class="tipos">
				<div class="titulo">
					Tipos Norma
			  		<img class="boton-buscar icon" title="Buscar Proyectos" onClick="Busqueda(\'busqueda-tipos\', \'buscar-tipos\', \'tipos\', false)" src="images/search2.png">
			  	</div>
			  	<div class="busqueda" id="busqueda-tipos">
					<div class="buscador">
						<input type="search" title="Escriba Para Buscar Tipos" id="buscar-tipos" placeholder="Buscar Tipos"/>
					</div>
				</div>';

	if(!empty($tipos)){

		$lista .= '<ul>';
		
		foreach ($tipos as $fila => $tipo) {
			$lista .= '<li id="'.$tipo['id'].'" onClick="SelectTipo('.$tipo['id'].')">'.$tipo['nombre'].'</li>';
		}

		$lista .= '</ul><!-- fin lista -->';
	}else{
		$lista .= 'No hay tipos';
	}

	$lista .= '<div class="datos-botones">
				<button type="button" id="EliminarTipo" title="Eliminar Tipo Seleccionado" onClick="EliminarTipo()">Eliminar</button>
				<button type="button" id="EditarTipo" title="Editar Tipo Seleccionado" onClick="EditarTipo()">Editar</button>
			   	<button type="button" id="NuevoTipo" title="Crear Nuevo Tipo" onClick="NuevoTipo()">Nuevo Tipo</button>
			   </div>
			   <!-- fin botonera -->
			   </div>';
	
	//muestra la lista
	echo $lista;
}

/**
* FORMULARIO PARA NUEVO TIPO
*/
function NuevoTipo(){

	$formulario = '<form id="FormularioNuevoTipo" enctype="multipart/form-data" method="post" action="src/ajaxTipos.php" >
					<div id="tipos" class="tipos">
						<div class="titulo">
							Nuevo Tipo
					  	</div>
					  	<input type="hidden" name="func" value="RegistrarTipo" />

					  	<div class="datos" ?
					  		<table>
					  		<tr>
					  			<td>
					  				Nombre
					  			</td>
					  			<td>
					  				<input type="text" title="Nombre para Nuevo Tipo" name="nombre" placeholder="Nombre" class="validate[required]" />
					  			</td>
					  		</tr>
					  		</table>
					  		<br/>
					  		<br/>
					  	</div>
					  	<div class="datos-botones">
					  		<button type="button" title="Cancelar Edición" onClick="CancelarContent()">Cancelar</button>
							<input type="reset" title="Limpiar Edición" value="Limpiar" />
							<input type="submit" title="Guardar Edición" value="Guardar" />
						</div>
					</form>';

	echo $formulario;
}

/**
* REGISTR UN NUEVO TIPO
* @param $nombre -> nombre del nuevo tipo
*/
function RegistrarTipo($nombre){
	$registros = new Registros();

	if( !$registros->NuevoTipo($nombre) ){
		echo "Error, no se pudo crear el nuevo tipo de norma.";
	}
}

/**
* FORMULARIO DE EDICION DE UN TIPO
* @param $id -> id del tipo ha editar
*/
function EditarTipo($id){
	$registros = new Registros();

	$datos = $registros->getTipo($id);
	
	$formulario = '';

	if(!empty($datos)){	
		$formulario .= '<form id="FormularioEditarTipo" enctype="multipart/form-data" method="post" action="src/ajaxTipos.php" >
					<div id="tipos" class="tipos">
						<div class="titulo">
							Edicion Tipo de Norma
					  	</div>
					  	<input type="hidden" name="func" value="ActualizarTipo" />
					  	<input type="hidden" id="tipo" name="tipo" value="'.$id.'" />

					  	<div class="datos" ?
					  		<table>
					  		<tr>
					  			<td>
					  				Nombre
					  			</td>
					  			<td>
					  				<input type="text" id="nombre" name="nombre" placeholder="Nombre" value="'.$datos[0]['nombre'].'" class="validate[required]" />
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
		//no existe el tipo
		$formulario .= '<div class="datos">
						Error. No se encontraron datos para el tipo.
					   </div>';
	}

	echo $formulario;
}

/**
* ELIMINA UN TIPO
* @param id -> id del tipo ha eliminar
*/
function EliminarTipo($tipo){
	$registros = new Registros();

	if( !$registros->DeleteTipo($tipo) ){
		echo "Error: no se pudo borrar el tipo.";
	}
}

/**
* ACTUALIZA UN TIPO
* @param $nombre -> nombre nuevo 
* @param $id -> id del tipo
*/
function ActualizarTipo($nombre, $id){
	$registros = new Registros();

	if( !$registros->UpdateTipo($nombre, $id)){
		echo "Error: no se podo actualizar la norma.";
	}
}


?>

