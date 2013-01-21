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
			  		<img class="boton-buscar icon" title="Buscar Proyectos" onClick="Busqueda(\'busqueda-proyectos\', \'buscar-proyectos\', \'proyectos\', false)" src="images/search2.png" >
			  	</div>

			  	<div class="busqueda" id="busqueda-proyectos">
					<div class="buscador">
						<input type="search" title="Escriba Para Buscar Proyectos" id="buscar-proyectos" placeholder="Buscar Proyectos"/>
					</div>
				</div>';

	if(!empty($datos)){

		$lista .= '<ul>';
		
		foreach ($datos as $fila => $proyecto) {
			$lista .= '<li title="Proyecto De '.Cliente($proyecto['cliente']).'" id="'.$proyecto['id'].'" onClick="SelectProyecto('.$proyecto['id'].')">'.$proyecto['nombre'].'</li>';
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
			  		<img class="boton-buscar icon" title="Buscar Proyectos" onClick="Busqueda(\'busqueda-proyectos\', \'buscar-proyectos\', \'proyectos\', true)" src="images/search2.png">
			  	</div>

			  	<div class="busqueda" id="busqueda-proyectos">
					<div class="buscador">
						<input type="search" title="Escriba Para Buscar Proyectos" id="buscar-proyectos" placeholder="Buscar Proyectos"/>
					</div>
				</div>';

	if(!empty($datos)){
		$lista .= '<table id="proyectos" class="table-list">
					<tr>
						<th>Nombre</th>
						<th>Cliente</th>
						<th>Estado</th>
						<th>Visibilidad</th>
					</tr>';

		
		foreach ($datos as $fila => $proyecto) {
			$lista .= '<tr id="'.$proyecto['id'].'" class="custom-tooltip" title="'.$proyecto['imagen'].'" onClick="SelectProyecto('.$proyecto['id'].')">
			              <td>'.$proyecto['nombre'].'</td>
					      <td>'.Cliente($proyecto['cliente']).'</td>
					      <td>'.Estado($proyecto['status']).'</td>
					      <td>'.Visibilidad($proyecto['visible']).'</td>
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
* DEVUELVE LA VISIBILIDAD DE UN PROYECTO
*/
function Visibilidad($visibilidad){
	if($visibilidad == 1){
		return "Visible";
	}else{
		return "Oculto";
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
					  			<td rowspan="4" class="td-project-image">
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
					  		<!-- desactivacion
					  		<tr>
					  			<td>
					  				Estado
					  			</td>
					  			<td>
									<div id="radio-estado" title="Estado Del Proyecto">
										<input id="radio-estado1" type="radio" checked="checked" name="estado" value="1">
						  				<label for="radio-estado1">Activo</label>

						  				<input id="radio-estado2" type="radio" name="estado" value="0">
						  				<label for="radio-estado2">Inactivo</label>
					  				</div>
					  			</td>
					  		</tr>
					  		-->
					  		<tr>
					  			<td>
					  				Visible
					  			</td>
					  			<td>
									<div id="radio-visible" title="Visibilidad Del Proyecto Para El cliente">
										<input id="radio-visible1" type="radio" checked="checked" name="visible" value="1">
						  				<label for="radio-visible1">Visible</label>

						  				<input id="radio-visible2" type="radio" name="visible" value="0">
						  				<label for="radio-visible2">Oculto</label>
					  				</div>
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
					  			<td rowspan="4" class="td-project-image">
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
					  			
					  				<div id="radio-estado" title="Estado Del Proyecto">';
		
		if($datos[0]['status'] == 1){
			$formulario .= '<input id="radio-estado1" type="radio" checked="checked" name="estado" value="1">
						  				<label for="radio-estado1">Activo</label>

						  			<input id="radio-estado2" type="radio" name="estado" value="0">
						  				<label for="radio-estado2">Inactivo</label>';
		}else{
			$formulario .= '<input id="radio-estado1" type="radio" name="estado" value="1">
						  				<label for="radio-estado1">Activo</label>

						  			<input id="radio-estado2" type="radio" name="estado" checked="checked" value="0">
						  				<label for="radio-estado2">Inactivo</label>';
		}

		$formulario .= '
					  				</div>
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Visible
					  			</td>
					  			<td>
					  				<div id="radio-visible" title="Visibilidad Del Proyecto Para El cliente">';
		
		if($datos[0]['visible'] == 1){

			$formulario .= '<input id="radio-visible1" type="radio" checked="checked" name="visible" value="1">
						  				<label for="radio-visible1">Visible</label>

						  			<input id="radio-visible2" type="radio" name="visible" value="0">
						  				<label for="radio-visible2">Oculto</label>';
		}else{
			$formulario .= '<input id="radio-visible1" type="radio" name="visible" value="1">
						  				<label for="radio-visible1">Visible</label>

						  			<input id="radio-visible2" type="radio" name="visible" checked="checked" value="0">
						  				<label for="radio-visible2">Oculto</label>';
		}					  			
		
		$formulario .= '    
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
		$select .= '<select id="cliente" name="cliente" title="Cliente Para Nuevo Proyecto" class="validate[required]">';

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
		$select .= '<select id="cliente" name="cliente" title="Cliente Para Nuevo Proyecto" class="validate[required]">';

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

		$estado = 1;
		/*if($_POST['estado'] == 1 || $_POST['estado'] == 0){
			$estado = $_POST['estado'];
		}*/

		$visible = 1;
		if($_POST['visible'] == 1 || $_POST['visible'] == 0){
			$visible = $_POST['visible'];
		}

		if( !$proyectos->NewProyecto( $_POST['nombre'], $_POST['cliente'], $descripcion, $imagen, $estado, $visible )){
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

		$estado = 1;
		if($_POST['estado'] == 1 || $_POST['estado'] == 0){
			$estado = $_POST['estado'];
		}

		$visible = 1;
		if($_POST['visible'] == 1 || $_POST['visible'] == 0){
			$visible = $_POST['visible'];
		}

		if( !$proyectos->UpdateProyecto($id, $_POST['nombre'], $_POST['cliente'], $descripcion, $imagen, $estado, $visible )){
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