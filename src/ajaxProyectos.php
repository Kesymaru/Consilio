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
		case 'ProyectosAvance':
			ProyectosAvance();
			break;

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

		//ELIMINAR UN PROYECTO
		case 'EliminarProyecto':
			if(isset($_POST['id'])){
				EliminarProyecto($_POST['id']);
			}
			break;

		case 'DuplicarProyecto':
			if(isset($_POST['id'])){
				DuplicarProyecto($_POST['id']);
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
				<button type="button" id="EliminarProyecto" class="ocultos" title="Eliminar Proyecto Seleccionado" onClick="EliminarProyecto()">Eliminar</button>
				<button type="button" id="EditarProyecto" class="ocultos" title="Editar Proyecto Seleccionado" onClick="EditarProyecto()">Editar</button>
				<button type="button" id="DuplicarProyecto" class="ocultos" title="Duplicar Proyecto Seleccionado" onClick="DuplicarProyecto()">Duplicar</button>
			   	<button type="button" id="NuevoProyecto" title="Crear Nuevo Proyecto" onClick="NuevoProyecto()">Nuevo Proyecto</button>
			   </div>
			   <!-- fin botonera -->
			   </div>';

	echo $lista;
}

/**
* MUESTRA LISTA DE PROYECTOS AVANZADA
*/
function ProyectosAvance(){
	$proyectos = new Proyectos();
	$datos = $proyectos->getProyectos();

	$lista = '';

	$lista = '<div id="proyectos" class="tipos">
				<div class="titulo">
					Proyectos
			  		<button type="button" title="Buscar Proyectos" onClick="BuscarContent(\'buscar-Proyectos\')">Buscar</button>
					<hr>
					<div class="busqueda">
						<input type="text" title="Escriba Para Buscar Proyectos Por Nombre, Estado o Cliente" id="buscar-Proyectos" placeholder="Buscar"/>
					</div>
			  	</div>';

	if(!empty($datos)){

		$lista .= '<table class="table-list">
					<tr>
						<td>Nombre</td>
						<td>Cliente</td>
						<td>Estado</td>
					</tr>';
		
		foreach ($datos as $fila => $proyecto) {
			$lista .= '<tr id="'.$proyecto['id'].'" class="custom-tooltip" title="'.$proyecto['imagen'].'" onClick="SelectProyecto('.$proyecto['id'].')">
			              <td>'.$proyecto['nombre'].'</td>
					      <td>'.Cliente($proyecto['cliente']).'</td>
					      <td>'.Estado($proyecto['status']).'</td>
					   </tr>';
		}

		$lista .= '</table><!-- fin lista -->';

	}else{
		$lista .= '<table class="table-list">
					<tr>
						<td>
							No hay proyectos
						</td>
					</tr>
				   </table>';
	}

	$lista .= '<div class="datos-botones">
				<button type="button" id="EliminarProyecto" class="ocultos" title="Eliminar Proyecto Seleccionado" onClick="EliminarProyecto()">Eliminar</button>
				<button type="button" id="EditarProyecto" class="ocultos" title="Editar Proyecto Seleccionado" onClick="EditarProyecto()">Editar</button>
				<button type="button" id="ComponerProyecto" class="ocultos" title="Componer El Proyecto Seleccionado" onClick="ComponerProyectoSeleccionado()">Componer</button>
				<button type="button" id="DuplicarProyecto" class="ocultos" title="Duplicar Proyecto Seleccionado" onClick="DuplicarProyecto()">Duplicar</button>
			   	<button type="button" id="NuevoProyecto" title="Crear Nuevo Proyecto" onClick="NuevoProyecto()">Nuevo Proyecto</button>
			   </div>
			   <!-- fin botonera -->
			   </div>';

	echo $lista;
}

/**
 * OBTIENE EL NOMBRE DEL CLIENTE
 * @param $id -> id del cliente
 */
function Cliente($id){
	$clientes = new Cliente();
	return $clientes->getClienteDato("nombre", $id);
}

/**
 * DEVUELVE EL ESTADO
 */
function Estado($estado){
	if($estado == 1){
		return "Activo";
	}else{
		return "Inactivo";
	}
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
					  			<td rowspan="3" class="td-user-image">
					  				<img id="image-preview" title="Imagen Para Nuevo Proyecto" src="images/es.png" />
					  				<br/>
					  				<input type="file" name="imagen" id="imagen" class="validate[optional]" onchange="PreviewImage(this,\'image-preview\')"/>
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
					  		<tr>
					  			<td>
					  				Estado
					  			</td>
					  			<td>
					  				<select name="estado">
					  					<option value="1" selected>Activo</option>
					  					<option value="0">Inactivo</option>
					  				</select>
					  			</td>
					  		</tr>
					  		</table>
					  		Descripcion
					  		<textarea name="descripcion" id="descripcion"></textarea>
					  	</div>
					  	<div class="datos-botones">
					  		<button type="button" title="Cancelar Edición" onClick="CancelarProyecto()">Cancelar</button>
							<input type="reset" title="Limpiar Edición" value="Limpiar" />
							<input type="submit" title="Guardar Edición" value="Guardar" onClick="EditorUpdateContent()" />
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
		
		$formulario .= '<form id="FormularioEditarProyecto" enctype="multipart/form-data" method="post" action="src/ajaxProyectos.php" >
					<div class="proyectos">
						<div class="titulo">
							Edición Proyecto
					  		<hr>
					  	</div>
					  	<input type="hidden" name="func" value="ActualizarProyecto" />
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
					  			<td rowspan="3" class="td-user-image">
					  				<img id="image-preview" title="Imagen Para El Proyecto" src="'.$datos[0]['imagen'].'" />
					  				<br/>
					  				<input type="file" name="imagen" id="imagen" class="validate[optional]" onchange="PreviewImage(this,\'image-preview\')" />
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
					  		<tr>
					  			<td>
					  				Estado
					  			</td>
					  			<td>
					  				<select name="estado">';
		
		if($datos[0]['status'] == 1){
			$formulario .= '<option value="1" selected>Activo</option>
					  		<option value="0">Inactivo</option>';
		}else{
			$formulario .= '<option value="1">Activo</option>
					  		<option value="0" selected>Inactivo</option>';
		}

		$formulario .= '
					  				</select>
					  			</td>
					  		</tr>
					  		</table>
					  		Descripcion
					  		<textarea name="descripcion" id="descripcion">';

		$formulario .= base64_decode($datos[0]['descripcion']).'</textarea>
					  	</div>
					  	<div class="datos-botones">
					  		<button type="button" title="Cancelar Edición" onClick="CancelarProyecto()">Cancelar</button>
							<input type="reset" title="Limpiar Edición" value="Limpiar" />
							<input type="submit" title="Guardar Edición" value="Guardar" onClick="EditorUpdateContent()" />
						</div>
					</form>';

	}else{
		$formulario .= "Error: no hay informacion sobre el proyecto.<br/>";
		$formulario .= "Los datos del proyecto id = ".$id." no pudieron ser encontrados.";
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

		$imagen = "images/es.png";
		$descripcion = "";

		//imagen
		if(isset($_FILES['imagen'])){
			$imagen = $proyectos->UploadImagen($_FILES['imagen']);
		}

		if(isset($_POST['descripcion'])){
			$descripcion = $_POST['descripcion'];
		}

		if( !$proyectos->NewProyecto($_POST['nombre'], $_POST['cliente'], $descripcion, $imagen, $_POST['estado']) ){
			echo "ERROR: no se pudo registrar el nuevo proyecto, ajaxProyectos.php RegistrarProyecto() linea 413";
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

		//sube imagen si tiene
		if(isset($_FILES['imagen'])){
			$imagen = $proyectos->UploadImagen($_FILES['imagen']);
		}

		if(isset($_POST['descripcion'])){
			$descripcion = $_POST['descripcion'];
		}

		if( !$proyectos->UpdateProyecto($id, $_POST['nombre'], $_POST['cliente'], $descripcion, $imagen, $_POST['estado']) ){
			echo "<br/>ERROR: no se pudo actualizar el proyecto, ajaxProyectos.php ActualizarProyecto(".$id.") linea 349";
		}

	}else{
		echo "<br/>ERROR: faltan parametros requeridos para actualizar proyecto.";
	}
}

/**
 * ELIMINA UN PROYECTO
 * @param $id -> id del proyecto
 */
function EliminarProyecto($id){
	$proyecto = new Proyectos();

	if(!$proyecto->DeleteProyecto($id)){
		echo "<br/>Error: no se podo eliminar el proyecto.";
	}
}

/**
 * DUPLICA UN PROYECTO
 * @param $id -> id del proyecto a duplicar
 */
function DuplicarProyecto($id){
	$proyectos = new Proyectos();

	if($nuevo = $proyectos->DuplicarProyecto($id)){
		echo $nuevo;
	}else{
		echo "<br/>Error: No se pudo duplicar el proyecto, ajaxProyectos.php DuplicarProyecto()";
	}
}

?>