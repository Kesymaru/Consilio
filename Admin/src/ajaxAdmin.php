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

	$lista = '<div id="admins">
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
	$datos = $admin->getAdminDatos();

	$formulario = '';

	if(!empty($datos)){

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