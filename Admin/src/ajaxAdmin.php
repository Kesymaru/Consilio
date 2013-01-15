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
			if( isset($_POST['admin']) ){
				ActualizarAdmin($_POST['admin']);
			}
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
			  		<button class="boton-buscar" type="button" title="Buscar Proyectos" onClick="Busqueda(\'busqueda-clientes\', \'buscar-clientes\', \'admins\', false)">Buscar</button>
					<hr>
					<div class="busqueda" id="busqueda-clientes">
						<div class="buscador">
							<input type="search" title="Escriba Para Buscar" id="buscar-clientes" placeholder="Buscar"/>
						</div>
					</div>
			  	</div>';

	if(!empty($datos)){
		$lista .= '<ul>';

		foreach ($datos as $fila => $admin) {
			if( $_SESSION['id'] == $admin['id']){
				$lista .= '<li class="me" id="'.$admin['id'].'" onClick="SelectAdmin('.$admin['id'].')">'.$admin['nombre'].'</li>';
			}else{
				$lista .= '<li id="'.$admin['id'].'" onClick="SelectAdmin('.$admin['id'].')">'.$admin['nombre'].'</li>';

			}
		}

		$lista .= '</ul>';
	}else{
		$lista .= 'No hay datos';
	}

	$lista .= '<div class="datos-botones">
				<button type="button" id="EliminarAdmin" title="Eliminar Admin Seleccionado" onClick="EliminarAdmin()">Eliminar</button>
				<button type="button" id="EditarAdmin" title="Editar Admin Seleccionado" onClick="EditarAdmin()">Editar</button>
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
					  			<td rowspan="5" class="td-user-image">
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
		$formulario .= 'No hay datos';
	}

	echo $formulario;
}

/**
* ACTUALIZA UN ADMIN EDITADO
* @param $id -> id del admin
*/
function ActualizarAdmin($id){

}

/**
* FORMULARIO PARA UN NUEVO ADMIN
*/
function CrearAdmins(){

}

/**
* REGISTRA UN NUEVO ADMIN
*/
function RegistrarAdmin(){

}

?>