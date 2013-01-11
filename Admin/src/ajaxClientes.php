<?php

/**
* AJAX PARA CLIENTES
*/

require_once("class/usuarios.php");

if(isset($_POST['func'])){
	
	switch ($_POST['func']){

		//CARGA LA LISTA DE CLIENTES
		case 'Clientes':
			Clientes();
			break;

		//CARGA FORMULARIO DE EDICION DE CLIENTES
		case 'EditarCliente':
			if(isset($_POST['id'])){
				EditarCliente($_POST['id']);
			}
			break;

		//ACTUALIZA CLIENTE
		case 'ActualizarCliente':
			if(isset($_POST['cliente-id'])){
				ActualizarCliente($_POST['cliente-id']);			
			}
			break;

		//CARGA FORMULARIO DE NUEVO CLIENTE
		case 'NuevoCliente':
			NuevoCliente();
			break;

		//REGISTRA CLIENTE NUEVO
		case 'RegistrarCliente':
			RegistrarCliente();
			break;

		//EliminarCliente
		case 'EliminarCliente':
			if(isset($_POST['id'])){
				EliminarCliente($_POST['id']);
			}
			break;

		//OBTIENE LOS UUSARIOS Y LOS DEVUELVE EN UN ARRAY PARA VALIDAR
		case 'GetUsers':
			$users = GetUsers();
			echo json_encode($users);
			break;

	}
}

/***************************** CLIENTES *****************************/

/**
* LISTA LOS CLIENTES DISPONIBLES
*/
function Clientes(){
	$usuarios = new Cliente();
	$clientes = $usuarios->getClientes();

	$lista = '<div id="clientes" class="tipos">
				<div class="titulo">
					Clientes
			  		<button type="button" title="Buscar Clientes" onClick="BuscarMenu(\'buscar-Tipos\')">Buscar</button>
					<hr>
					<div class="busqueda">
						<input type="text" id="buscar-Tipos" placeholder="Buscar"/>
					</div>
			  	</div>';

	if(!empty($clientes)){

		$lista .= '<ul>';
		
		foreach ($clientes as $fila => $cliente) {
			$lista .= '<li id="'.$cliente['id'].'" onClick="SelectCliente('.$cliente['id'].')">'.$cliente['nombre'].'</li>';
		}

		$lista .= '</ul><!-- fin lista -->';

	}else{
		$lista = "No hay clientes";
	}

	$lista .= '<div class="datos-botones">
				<button type="button" id="EliminarCliente" title="Eliminar Cliente Seleccionado" onClick="EliminarCliente()">Eliminar</button>
				<button type="button" id="EditarCliente" title="Editar Cliente Seleccionado" onClick="EditarCliente()">Editar</button>
			   	<button type="button" id="NuevoCliente" title="Crear Nuevo Cliente" onClick="NuevoCliente()">Nuevo Cliente</button>
			   	<button type="button" id="ExportarClientes" title="Exportar Todos Los Clientes" onClick="ExportarClientes()">Exportar Clientes</button>
			   </div>
			   <!-- fin botonera -->
			   </div>';

	echo $lista;
}

/**
* CREA FORMULARIO DE EDICION DE UN CLIENTE
* @param $id -> id del client ha editar
* @return $formulario -> formulario compuesto
*/
function EditarCliente($id){
	$usuarios = new Cliente();
	$datos = $usuarios->getDatosCliente($id);

	$formulario = "";

	if(!empty($datos)){

		$formulario .= '<form id="FormularioEditarCliente" enctype="multipart/form-data" method="post" action="src/ajaxClientes.php" >
					<div class="clientes">
						<div class="titulo">
							Edición Cliente
					  		<hr>
					  	</div>
					  	<input type="hidden" name="func" value="ActualizarCliente" />
					  	<input type="hidden" id="cliente-id" name="cliente-id" value="'.$id.'" />

					  	<div class="datos" >
					  		<table>
					  		<tr>
					  			<td>
					  				Usuario
					  			</td>
					  			<td>
					  				<input type="text" id="usuario" title="Usuario Del Nuevo Cliente" name="usuario" placeholder="Usuario" class="validate[required]" value="'.$datos[0]['usuario'].'" />
					  			</td>
					  			<td rowspan="6" class="td-user-image">
					  				<img id="imagen-usuario" src="'.$datos[0]['imagen'].'" title="Imagen Del Cliente"><br/>					  				
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Contraseña
					  			</td>
					  			<td>
					  				<input type="text" id="contrasena" title="Cambiar Contraseña Del Cliente" name="contrasena" placeholder="Contraseña Nueva" class="validate[optional]"  />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Nombre
					  			</td>
					  			<td>
					  				<input type="text" id="nombre" name="nombre" title="Nombre Del Cliente" placeholder="Nombre" value="'.$datos[0]['nombre'].'" class="validate[required]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Email
					  			</td>
					  			<td>
					  				<input type="email" id="email" name="email" title="Email Del Cliente" placeholder="Email" value="'.$datos[0]['email'].'" class="validate[required, custom[email]]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Registro
					  			</td>
					  			<td>
					  				<input type="text" id="registro" name="registro" title="Registro Del Cliente" placeholder="Registro" value="'.$datos[0]['registro'].'" class="validate[required, custom[number]]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Telefono
					  			</td>
					  			<td>
					  				<input type="tel" id="telefono" name="telefono" title="Telefono Del Cliente" placeholder="Telefono" value="'.$datos[0]['telefono'].'" class="validate[required, custom[phone]]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Skype
					  			</td>
					  			<td>
					  				<input type="text" id="skype" name="skype" title="Skype Del Cliente" placeholder="Skype" value="'.$datos[0]['skype'].'" class="validate[optional]" />
					  			</td>
					  			<td style="text-align: center;">
					  				<input size="18" type="file" name="imagen" id="imagen" title="Seleccione Una Imagen Nueva Para El Cliente" onChange="PreviewImage(this, \'imagen-usuario\');" />
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
	}else{
		$formulario .= '<div class="datos">
						Error: el cliente seleccionado ya no existe.
						</div>';
	}

	echo $formulario;
}

/**
 * FORMULARIO PARA UN NUEVO CLIENTE
 */
function NuevoCliente(){
	$formulario = "";
	$formulario .= '<form id="FormularioNuevoCliente" enctype="multipart/form-data" method="post" action="src/ajaxClientes.php" >
					<div class="clientes">
						<div class="titulo">
							Edición Nuevo Cliente
					  		<hr>
					  	</div>
					  	<input type="hidden" name="func" value="RegistrarCliente" />

					  	<div class="datos" >
					  		<table>
					  		<tr>
					  			<td>
					  				Usuario
					  			</td>
					  			<td>
					  				<input type="text" id="usuario" title="Usuario Del Nuevo Cliente" name="usuario" placeholder="Usuario" class="validate[required, funcCall[ClienteUsuario] ]" />
					  			</td>
					  			<td rowspan="6" class="td-user-image">
					  				<img id="imagen-usuario" src="images/es.png" title="Imagen Del Nuevo Cliente">
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Contraseña
					  			</td>
					  			<td>
					  				<input type="text" id="contrasena" title="Contraseña Del Nuevo Cliente" name="contrasena" placeholder="Contraseña" class="validate[required]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Nombre
					  			</td>
					  			<td>
					  				<input type="text" id="nombre" name="nombre" title="Nombre Del Nuevo Cliente" placeholder="Nombre" class="validate[required]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Email
					  			</td>
					  			<td>
					  				<input type="email" id="email" name="email" title="Email Del Nuevo Cliente" placeholder="Email" class="validate[required, custom[email]]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Registro
					  			</td>
					  			<td>
					  				<input type="text" id="registro" name="registro" title="Registro Del Nuevo Cliente" placeholder="Registro" class="validate[required, custom[number]]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Telefono
					  			</td>
					  			<td>
					  				<input type="tel" id="telefono" name="telefono" title="Telefono Del Nuevo Cliente" placeholder="Telefono" class="validate[required, custom[phone]]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Skype
					  			</td>
					  			<td>
					  				<input type="text" id="skype" name="skype" title="Skype Del Nuevo Cliente" placeholder="Skype" class="validate[optional]" />
					  			</td>
					  			<td style="text-align: center;">
					  				<input size="18" type="file" name="imagen" id="imagen" title="Seleccione Una Imagen Nueva Para El Cliente" onChange="PreviewImage(this, \'imagen-usuario\');" />
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
* ACTUALIZA UN CLIENTE EDITADO
* @param $id -> id del cliente a actualizar
*/
function ActualizarCliente($id){
	$cliente = new Cliente();

	//datos minimos 
	if(isset($_POST['nombre']) && isset($_POST['email']) && isset($_POST['registro']) && isset($_POST['telefono']) ){
		
		//cambia de imagen
		if(isset($_FILES['imagen'])){
			//sube la imagen
			if(!$cliente->UploadClienteImagen($id, $_FILES['imagen']) ){
				echo 'Error: No se pudo subir la imagen.';
			}
		}

		//si cambia contrasena
		if(isset($_POST['contrasena'])){
			$contrasena = $_POST['contrasena'];
		}else{
			$contrasena = "";
		}

		//update sin cambio de imagen
		if( !$cliente->UpdateCliente($id, $_POST['nombre'], $_POST['email'], $_POST['registro'], $_POST['telefono'], $_POST['skype'], $_POST['usuario'], $contrasena )){
			echo "Error: No se pudo actuaizar los datos del cliente.";
		}

	}else{
		echo "Error: Parametros necesarios, en ajaxClientes.php.";
	}
}

/**
 * REGISTRAR CLIENTE
 */
function RegistrarCLiente(){
	$cliente = new Cliente();

	if(isset($_POST['nombre']) && isset($_POST['email']) && isset($_POST['registro']) && isset($_POST['telefono']) && isset($_POST['contrasena']) && isset($_POST['usuario']) ){
		
		$imagen = "";
		if(isset($_FILES['imagen'])){
			$imagen = $cliente->UploadImagen($_FILES['imagen']);
		}else{
			$imagen = "images/es.png";
		}

		//update sin cambio de imagen
		if(!$cliente->NewCliente($_POST['nombre'], $_POST['email'], $_POST['registro'], $_POST['telefono'], $_POST['skype'], $imagen, $_POST['contrasena'], $_POST['usuario'] )){
			echo "Error: ajaxClientes.php RegistrarCliente() No se pudo crear el nuevo cliente.";
		}

	}else{
		echo "Error: Parametros necesarios, en ajaxClientes.php RegistrarCliente().";
	}

}

/**
* ELIMINA UN CLIENTE
* @param $id -> id del cliente a borrar
*/
function EliminarCliente($id){
	$cliente = new Cliente();

	if(!$cliente->DeleteCliente($id)){
		echo 'Error: ajaxClientes.php no se pudo eliminar el cliente '.$id;
	}
}

/**
* OBTIENE USUARIOS NO DISPONIBLES
*/
function GetUsers(){
	$clientes = new Cliente();
	$usuarios = $clientes->getUsers();

	$users = array('');

	if(!empty($usuarios)){

		foreach ($usuarios as $f => $usuario) {
			$users[] = $usuario['usuario'];
		}
	}

	return $users;
}

?>