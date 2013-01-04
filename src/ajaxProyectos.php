<?php

/**
* AJAX PARA PROYECTOS
*/

require_once("class/proyectos.php");
require_once("class/imageUpload.php");
require_once("class/usuarios.php");

if(isset($_POST['func'])){
	
	switch ($_POST['func']){

		//LISTA PROYECTOS
		case 'Proyectos':
			Proyectos();
			break;

		//NUEVO PROYECTO
		case 'NuevoProyecto':
			NuevoProyecto();
			break;

		//EDITAR PROYECTO
		case 'EditarProyecto':
			if(isset($_POST['id'])){
				EditarProyecto($_POST['id']);
			}
			break;

		//REGISTRA UN NUEVO PROYECTO
		case 'RegistrarProyecto':
			RegistrarProyecto();
			break;

		//ACTUALIZAR UN PROYECTO
		case 'ActualizarProyecto':
			if(isset($_POST['proyecto'])){
				ActualizarProyecto($_POST['proyecto']);			
			}
			break;
	}
}

/**
* MUESTRA LISTA DE PROYECTOS
*/
function Proyectos(){
	$proyectos = new Proyectos();
	$datos = $proyectos->getProyectos();

	$lista = '';

	$lista = '<div id="proyectos" class="tipos">
				<div class="titulo">
					Proyectos
			  		<button type="button" title="Buscar Proyectos" onClick="BuscarMenu(\'buscar-Proyectos\')">Buscar</button>
					<hr>
					<div class="busqueda">
						<input type="text" id="buscar-Proyectos" placeholder="Buscar"/>
					</div>
			  	</div>';

	if(!empty($datos)){

		$lista .= '<ul>';
		
		foreach ($datos as $fila => $proyecto) {
			$lista .= '<li id="'.$proyecto['id'].'" onClick="SelectProyecto('.$proyecto['id'].')">'.$proyecto['nombre'].'</li>';
		}

		$lista .= '</ul><!-- fin lista -->';

	}else{
		$lista .= "No hay proyectos";
	}

	$lista .= '<div class="datos-botones">
				<button type="button" id="EliminarProyecto" title="Eliminar Proyecto Seleccionado" onClick="EliminarProyecto()">Eliminar</button>
				<button type="button" id="EditarProyecto" title="Editar Proyecto Seleccionado" onClick="EditarProyecto()">Editar</button>
			   	<button type="button" id="NuevoProyecto" title="Crear Nuevo Proyecto" onClick="NuevoProyecto()">Nuevo Proyecto</button>
			   </div>
			   <!-- fin botonera -->
			   </div>';

	echo $lista;
}

/**
* FORMULARIO DE NUEVO PROYECTO
*/
function NuevoProyecto(){

	$formulario = "";
	
	$formulario .= '<form id="FormularioNuevoProyecto" enctype="multipart/form-data" method="post" action="src/ajaxProyectos.php" >
					<div class="proyectos">
						<div class="titulo">
							Edición Nuevo Proyecto
					  		<hr>
					  	</div>
					  	<input type="hidden" name="func" value="RegistrarProyecto" />

					  	<div class="datos" >
					  		<table>
					  		<tr>
					  			<td>
					  				Nombre
					  			</td>
					  			<td>
					  				<input type="text" name="nombre" title="Nombre Para Nuevo Proyecto" placeholder="Nombre" class="validate[required]" />
					  			</td>
					  			<td rowspan="2" class="td-user-image">
					  				<img src="images/es.png" />
					  				<br/>
					  				<input type="file" name="imagen" id="imagen" class="validate[optional]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Cliente
					  			</td>
					  			<td>
					  				';
	$formulario .= SelectClientes().'
								</td>
					  		</tr>
					  		</table>
					  		Descripcion
					  		<textarea name="descripcion" id="descripcion"></textarea>
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
* FORMULARIO PARA EDITAR UN PROYECTO
* @param $id -> id del proyecto
*/ 
function EditarProyecto($id){
	$proyecto = new Proyectos();

	$datos = $proyecto->getProyectoDatos($id);

	$formulario = "";

	if(!empty($datos)){
		
		$formulario .= '<form id="FormularioNuevoProyecto" enctype="multipart/form-data" method="post" action="src/ajaxProyectos.php" >
					<div class="proyectos">
						<div class="titulo">
							Edición Proyecto
					  		<hr>
					  	</div>
					  	<input type="hidden" name="func" value="RegistrarProyecto" />
					  	<input type="hidden" name="proyecto" value="'.$id.'" id="proyecto" />
					  	<div class="datos" >
					  		<table>
					  		<tr>
					  			<td>
					  				Nombre
					  			</td>
					  			<td>
					  				<input type="text" name="nombre" title="Nombre Para Nuevo Proyecto" placeholder="Nombre" class="validate[required]" value="'.$datos[0]['nombre'].'" />
					  			</td>
					  			<td rowspan="2" class="td-user-image">
					  				<img src="'.$datos[0]['imagen'].'" />
					  				<br/>
					  				<input type="file" name="imagen" id="imagen" class="validate[optional]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Cliente
					  			</td>
					  			<td>
					  				';
		$formulario .= SelectedClientes($datos[0]['cliente']).'
								</td>
					  		</tr>
					  		</table>
					  		Descripcion
					  		<textarea name="descripcion" id="descripcion">';

		$formulario .= base64_decode($datos[0]['descripcion']).'</textarea>
					  	</div>
					  	<div class="datos-botones">
					  		<button type="button" title="Cancelar Edición" onClick="CancelarContent()">Cancelar</button>
							<input type="reset" title="Limpiar Edición" value="Limpiar" />
							<input type="submit" title="Guardar Edición" value="Guardar" />
						</div>
					</form>';

	}else{
		$formulario .= "Error: no hay informacion sobre el proyecto.";
	}

	echo $formulario;
}

/**
* COMPONE EL SELECT CON LOS CLIENTES
* @return $select -> lista de seleccion
*/
function SelectClientes(){
	$clientes = new Cliente();
	$datos = $clientes->getClientes();

	$select = "";

	if(!empty($datos)){
		$select .= '<select name="cliente" title="Cliente Para Nuevo Proyecto" class="validate[required]">';

		foreach ($datos as $fila => $cliente) {
			$select .= '<option value="'.$cliente['id'].'">'.$cliente['nombre'].'</option>';
		}

		$select .= '</select>';
	}else{
		$select .= "No hay clientes.";
	}
	return $select;
}

/**
* COMPONE SELECT CON EL CLIENTE SELECCIONADO
* @param $id -> id de la opcion seleccionada
*/
function SelectedClientes($id){
	$clientes = new Cliente();
	$datos = $clientes->getClientes();

	$select = "";

	if(!empty($datos)){
		$select .= '<select name="cliente" title="Cliente Para Nuevo Proyecto" class="validate[required]">';

		foreach ($datos as $fila => $cliente) {
			if($id == $cliente['id']){
				$select .= '<option value="'.$cliente['id'].'" selected>'.$cliente['nombre'].'</option>';
			}else{
				$select .= '<option value="'.$cliente['id'].'">'.$cliente['nombre'].'</option>';
			}
			
		}

		$select .= '</select>';
	}else{
		$select .= "No hay clientes.";
	}
	return $select;
}

/**
* REGISTRA UN NUEVO PROYECTO
*/
function RegistrarProyecto(){
	$proyectos = new Proyectos();

	if(isset($_POST['nombre']) && isset($_POST['cliente']) ){

		$imagen = "";
		$descripcion = "";

		//imagen
		if(isset($_FILES['imagen'])){
			$imagen = $_FILES['imagen'];
		}

		if(isset($_POST['descripcion'])){
			$descripcion = $_POST['descripcion'];
		}

		if( !$proyectos->NewProyecto($_POST['nombre'], $_POST['cliente'], $descripcion, $imagen) ){
			echo "ERROR: no se pudo registrar el nuevo proyecto, ajaxProyectos.php RegistrarProyecto() linea 165";
		}

	}else{
		echo "ERROR: faltan parametros requeridos para registrar el nuevo proyecto.";
	}
}

/**
* ACTUALIZA UN PROYECTO
* @param $id -> id del proyecto
*/
function ActualizarProyecto($id){
	$proyectos = new Proyectos();

	if(isset($_POST['nombre']) && isset($_POST['cliente']) ){

		$imagen = "";
		$descripcion = "";

		//imagen
		if(isset($_FILES['imagen'])){
			$imagen = $_FILES['imagen'];
		}

		if(isset($_POST['descripcion'])){
			$descripcion = $_POST['descripcion'];
		}

		if( !$proyectos->UpdateProyecto($_POST['nombre'], $_POST['cliente'], $descripcion, $imagen) ){
			echo "ERROR: no se pudo actualizar el proyecto, ajaxProyectos.php ActualizarProyecto(".$id.") linea 312";
		}

	}else{
		echo "ERROR: faltan parametros requeridos para actualizar proyecto.";
	}
}

?>