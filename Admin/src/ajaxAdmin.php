<?php

/**
* AJAX PARA LA MANIPULACION DE LOAS ADMINISTRADORES
*/

require_once("class/usuarios.php");

if(isset($_POST['func'])){
	switch ($_POST['func']) {
		
		//carga la lista de administradores del sitio
		case 'Admin':
			Admins();
			break;
		
		//CARGA FORMULARIO DE EDICION DE UN ADMIN
		case 'EditarAdmin':
			if(isset($_POST['id'])){
				EditarAdmin($_POST['id']);
			}
			break;
		
		//ACTUALIZA UN ADMIN
		case 'ActualizarAdmin':
			if( isset($_POST['admin-id']) ){
				ActualizarAdmin($_POST['admin-id']);
			}
			break;

		//FORMULARIO PARA NUEVO ADMIN
		case 'NuevoAdmin':
			NuevoAdmin();
			break;

		//REGISTRA UN NUEVO ADMIN
		case 'RegistrarAdmin':
			RegistrarAdmin();
			break;

		//LISTA DE USUARIOS YA TOMADOS
		case 'UsuariosDisponibles':
			$usuarios = UsuariosDisponibles();
			echo json_encode($usuarios);
			break;

		//USUARIOS DISPONIBLES PARA ADMIN EXISTENTE
		case 'UsuariosDisponiblesAdmin':
			if(isset($_POST['id'])){
				$usuarios = UsuariosDisponiblesAdmin($_POST['id']);
				echo json_encode($usuarios);
			}
			break;

		//ELIMINAR UN ADMIN
		case 'EliminarAdmin':
			if(isset($_POST['id'])){
				EliminarAdmin($_POST['id']);
			}
		break;

		/***** ADMIN LOGS ********/
		//LOGS ADMINS
		case 'AdminLogs':
			AdminLogs();
			break;
	}
}

/**
* CREA UNA LISTA CON LOS ADMINS DE LA MATRIZ
*/
function Admins(){
	$admins = new Admin();
	$datos = $admins->getAdmins();

	$lista = '<div id="admins" class="tipos">
				<div class="titulo">
					Administradores
			  		<img class="boton-buscar icon" title="Buscar Proyectos" onClick="Busqueda(\'busqueda-admins\', \'buscar-admins\', \'admins\', false)" src="images/search2.png" >
			  	</div>

			  	<div class="busqueda" id="busqueda-admins">
					<div class="buscador">
						<input type="search" title="Escriba Para Buscar Administradores" id="buscar-admins" placeholder="Buscar"/>
					</div>
				</div>';

	if(!empty($datos)){
		$lista .= '<div class="scroll">
					<ul>';

		foreach ($datos as $fila => $admin) {
			if( $_SESSION['id'] == $admin['id']){
				$lista .= '<li class="me" id="'.$admin['id'].'" onClick="SelectAdmin('.$admin['id'].')" title="'.$admin['nombre'].' '.$admin['apellidos'].'" >'.$admin['nombre'].'</li>';
			}else{
				$lista .= '<li id="'.$admin['id'].'" onClick="SelectAdmin('.$admin['id'].')" title="'.$admin['nombre'].' '.$admin['apellidos'].'" >'.$admin['nombre'].'</li>';

			}
		}

		$lista .= '</ul>
					</div> <!-- end scroll -->';
	}else{
		$lista .= 'No hay datos';
	}

	$lista .= '<div class="menu-botones">
				<button class="ocultos" type="button" id="EliminarAdmin" title="Eliminar Admin Seleccionado" onClick="EliminarAdmin()">Eliminar</button>
				<button class="ocultos" type="button" id="EditarAdmin" title="Editar Admin Seleccionado" onClick="EditarAdmin()">Editar</button>
			   	<button type="button" id="NuevoAdmin" title="Crear Nuevo Admin" onClick="NuevoAdmin()">Nuevo Admin</button>
			   </div>
			   <!-- fin botonera -->
			   </div>';

	echo $lista;
}

/**
* CREA UN FORMULARIO PARA EDITAR UN ADMIN
* @param #id -> id del admin a editar
*/
function EditarAdmin($id){
	$admin = new Admin();
	$datos = $admin->getAdminDatos($id);

	$formulario = '';

	if(!empty($datos)){

		$formulario .= '<form id="FormularioEditarAdmin" enctype="multipart/form-data" method="post" action="src/ajaxAdmin.php" >
					<div class="clientes">
						<div class="titulo">
							Edición Admin
					  	</div>
					  	<input type="hidden" name="func" value="ActualizarAdmin" />
					  	<input type="hidden" id="admin-id" name="admin-id" value="'.$id.'" />

					  	<div class="datos" >
					  		<table>
					  		<tr>
					  			<td>
					  				Usuario
					  			</td>
					  			<td>
					  				<input type="text" id="usuario" title="Usuario Del Nuevo Admin" name="usuario" placeholder="Usuario" class="validate[required, funcCall[UsuariosDiponiblesAdminEdicion] ]" value="'.$datos[0]['usuario'].'" />
					  			</td>
					  			<td rowspan="6" class="td-user-image">';
		//si la imagen exite	
		if(file_exists("../".$datos[0]['imagen'])){
			//imagen del admin
			$formulario .= '<img id="imagen-admin" src="'.$datos[0]['imagen'].'" title="Imagen Del Admin"><br/>';
		}else{
			//no tien imagen fallback imagen por defecto
			$formulario .= '<img id="imagen-admin" src="images/es.png" title="Imagen Del Admin"><br/>';
		}
					  								  				
		$formulario .=  '	</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Contraseña
					  			</td>
					  			<td>
					  				<input type="text" id="contrasena" title="Cambiar Contraseña Del Admin" name="contrasena" placeholder="Contraseña Nueva" class="validate[optional]"  />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Nombre
					  			</td>
					  			<td>
					  				<input type="text" id="nombre" name="nombre" title="Nombre Del Admin" placeholder="Nombre" value="'.$datos[0]['nombre'].'" class="validate[required]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Apellidos
					  			</td>
					  			<td>
					  				<input type="text" id="apellidos" name="apellidos" title="Apellidos Del Admin" placeholder="Apellidos" value="'.$datos[0]['apellidos'].'" class="validate[required]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Email
					  			</td>
					  			<td>
					  				<input type="email" id="email" name="email" title="Email Del Admin" placeholder="Email" value="'.$datos[0]['email'].'" class="validate[required, custom[email]]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Telefono
					  			</td>
					  			<td>
					  				<input type="tel" id="telefono" name="telefono" title="Telefono Del Admin" placeholder="Telefono" value="'.$datos[0]['telefono'].'" class="validate[required, custom[phone]]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Skype
					  			</td>
					  			<td>
					  				<input type="text" id="skype" name="skype" title="Skype Del Admin" placeholder="Skype" value="'.$datos[0]['skype'].'" class="validate[optional]" />
					  			</td>
					  			<td style="text-align: center;">
					  				<input size="18" type="file" name="imagen" id="imagen" title="Seleccione Una Imagen Nueva Para El Admin" onChange="PreviewImage(this, \'imagen-admin\');" />
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
					</form><!-- end form editar admin -->';
	}else{
		$formulario .= 'No hay datos';
	}

	echo $formulario;
}

/**
* ACTUALIZA UN ADMIN EDITADO
* @param $id -> id del admin
*/
function ActualizarAdmin($id){
	$admin = new Admin();

	if(isset($_POST['usuario']) && isset($_POST['nombre']) && isset($_POST['apellidos']) && isset($_POST['email']) && isset($_POST['telefono']) ){

		$skype = '';
		if(isset($_POST['skype'])){
			$skype = $_POST['skype'];
		}

		$password = "";
		if( isset($_POST['contrasena'])){
			$password = $_POST['contrasena'];
		}

		$imagen = '';
		if(isset($_FILES['imagen'])){
			if($_FILES['imagen'] != ""){
				$imagen = $admin->UploadImagen($_FILES['imagen']);
			}
		}

		if( !$admin->UpdateAdmin($id, $_POST['usuario'], $_POST['nombre'], $_POST['apellidos'], $_POST['email'], $_POST['telefono'], $skype, $imagen, $password )){
			echo '<br/>Error: No se pudo actualizar el Admin.<br/>ajaxAdmin.php ActualzarAdmin()';
		}		
	}else{
		echo '<br/>Error: datos requeridos no encontrados.<br/>ajaxAdmin.php ActualizarAdmin().';
	}
}

/**
* FORMULARIO PARA UN NUEVO ADMIN
*/
function NuevoAdmin(){
	$formulario = "";
	$formulario .= '<form id="FormularioNuevoAdmin" enctype="multipart/form-data" method="post" action="src/ajaxAdmin.php" >
					<div class="clientes">
						<div class="titulo">
							Edición Nuevo Admin
					  	</div>
					  	<input type="hidden" name="func" value="RegistrarAdmin" />

					  	<div class="datos" >
					  		<table>
					  		<tr>
					  			<td>
					  				Usuario
					  			</td>
					  			<td>
					  				<input type="text" id="usuario" title="Usuario Del Nuevo Admin" name="usuario" placeholder="Usuario" class="validate[required, funcCall[UsuariosDiponiblesAdmin] ]" />
					  			</td>
					  			<td rowspan="6" class="td-user-image">
					  				<img id="imagen-admin" src="images/es.png" title="Imagen Del Nuevo Admin">
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Contraseña
					  			</td>
					  			<td>
					  				<input type="text" id="contrasena" title="Contraseña Del Nuevo Admin" name="contrasena" placeholder="Contraseña" class="validate[required]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Nombre
					  			</td>
					  			<td>
					  				<input type="text" id="nombre" name="nombre" title="Nombre Del Nuevo Admin" placeholder="Nombre" class="validate[required]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Apellidos
					  			</td>
					  			<td>
					  				<input type="text" id="apellidos" name="apellidos" title="Apellidos Del Nuevo Admin" placeholder="Apellidos" class="validate[required]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Titulo
					  			</td>
					  			<td>
					  				<input type="text" name="titulo" title="Titulo Del Nuevo Admin" placeholder="Titulo" class="validate[optional]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Email
					  			</td>
					  			<td>
					  				<input type="email" id="email" name="email" title="Email Del Nuevo Admin" placeholder="Email" class="validate[required, custom[email]]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Telefono
					  			</td>
					  			<td>
					  				<input type="tel" id="telefono" name="telefono" title="Telefono Del Nuevo Admin" placeholder="Telefono" class="validate[required, custom[phone]]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Mobile
					  			</td>
					  			<td>
					  				<input type="tel" id="mobile" name="mobile" title="Mobile Del Nuevo Admin" placeholder="Mobile" class="validate[optinal, custom[phone]]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Fax
					  			</td>
					  			<td>
					  				<input type="tel" id="fax" name="fax" title="Fax Del Nuevo Admin" placeholder="Fax" class="validate[optinal, custom[phone]]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Skype
					  			</td>
					  			<td>
					  				<input type="text" id="skype" name="skype" title="Skype Del Nuevo Admin" placeholder="Skype" class="validate[optional]" />
					  			</td>
					  			<td style="text-align: center;">
					  				<input size="18" type="file" name="imagen" id="imagen" title="Seleccione Una Imagen Nueva Para El Admin" onChange="PreviewImage(this, \'imagen-admin\');" />
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
* REGISTRA UN NUEVO ADMIN
*/
function RegistrarAdmin(){
	$admin = new Admin();

	if(isset($_POST['usuario']) && isset($_POST['nombre']) && isset($_POST['apellidos']) && isset($_POST['email']) && isset($_POST['telefono']) && isset($_POST['contrasena']) ){

		$titulo = '';
		if(isset($_POST['titulo'])){
			$titulo = $_POST['titulo'];
		}

		$mobile = '';
		if(isset($_POST['mobile'])){
			$mobile = $_POST['mobile'];
		}

		$fax = '';
		if(isset($_POST['fax'])){
			$fax = $_POST['fax'];
		}

		$skype = '';
		if(isset($_POST['skype'])){
			$skype = $_POST['skype'];
		}

		$imagen = "images/es.png";
		if(isset($_FILES['imagen'])){
			$imagen = $admin->UploadImagen($_FILES['imagen']);
		}

		if( !$admin->NewAdmin($_POST['usuario'], $_POST['nombre'], $_POST['apellidos'], $titulo, $_POST['email'], $_POST['telefono'], $mobile, $fax, $skype, $imagen, $_POST['contrasena'] )){
			echo '<br/>Error: No se pudo crear un nuevo Admin.<br/>ajaxAdmin.php RegistrarAdmin()';
		}		
	}else{
		echo '<br/>Error: datos requeridos no encontrados.<br/>ajaxAdmin.php RegistrarAdmin().';
	}
}


/************************ HELPERS ******************/

/**
* OBTIENE LA LISTA DE TODOS LOS USUARIOS TOMAD0S
*/
function UsuariosDisponibles(){
	$admin = new Admin();
	$usuarios = $admin->getUsers();

	$users = array('');

	if(!empty($usuarios)){

		foreach ($usuarios as $f => $usuario) {
			$users[] = $usuario['usuario'];
		}
	}

	return $users;
}


/**
* OBTIENE LOS USUARIOS DISPONIBLES
* @param $id -> id del admin
*/
function UsuariosDisponiblesAdmin($id){
	$admin = new Admin();
	$usuarios = $admin->getUsersAdmin($id);

	$users = array('');

	if(!empty($usuarios)){

		foreach ($usuarios as $f => $usuario) {
			$users[] = $usuario['usuario'];
		}
	}

	return $users;
}

/**
* ELIMINA UN ADMIN
* @param $id -> id del admin
*/
function EliminarAdmin($id){
	$admin = new Admin();

	if(!$admin->DeleteAdmin($id)){
		echo '<br/>Error: no se pudo eliminar el admin con el id = '.$id;
	}
}


/*********************** ADMIN LOGS *************/

function AdminLogs(){
	$admin =  new Admin();
	$datos = $admin->getAdmins();

	$lista = '<div class="titulo">
				Admin Logs
			 </div>';

	if(!empty($datos)){
		$lista .= '<table class="table-list">
					<tr>
						<th>
							Admin
						</th>
						<th>
							Ultimo Acceso
						</th>
						<th>
							Activo
						</th>
					</tr>';
		
		foreach ($datos as $fila => $admin) {
			$lista .= '<tr><td>'.$admin['nombre'].'</td>';
			$logs  = unserialize($admin['log']);

			if(!empty($logs)){
				$lista .= '<td>'.$logs[ sizeof($logs)-1 ].'</td>';
			}else{
				$lista .= '<td></td>';
			}

			if($admin['activo'] == 1){
				$lista .= '<td>Activo</td>';
			}else{
				$lista .= '<td>No Activo</td>';
			}
			$lista .= '</tr>';
		}

		$lista .= '</table>';

	}else{
		return '';
	}

	echo $lista;
}

?>