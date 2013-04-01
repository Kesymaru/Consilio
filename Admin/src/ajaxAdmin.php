<?php

/**
* AJAX PARA LA MANIPULACION DE LOAS ADMINISTRADORES
*/
require_once("class/session.php");
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

		/***************** INTENTOS **************/
		case 'Intentos':
			Intentos();
			break;

		//DESBLOQUEA UNA IP
		case 'Desbloquear':
			if( isset( $_POST['ip'] ) ){
				Desbloquear( $_POST['ip'] );
			}
			break;

		//BLOQUEA PERMANENTEMENTE UNA IP
		case 'BloquearPermanentemente':
			if( isset($_POST['ip']) ){
				BloqueoPermanente( $_POST['ip'] );
			}
			break;

		//elimina una ip del registro
		case 'EliminarIp':
			if( isset($_POST['ip']) ){
				EliminarIp( $_POST['ip'] );
			}
			break;

		/************** CONFIGURACION ***********/

		case 'Config':
			Config();
			break;

		case 'ActualizarConfig':
			ActualizarConfig();
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
					  				Titulo
					  			</td>
					  			<td>
					  				<input type="text" name="titulo" title="Titulo Del Admin" placeholder="Titulo" value="'.$datos[0]['titulo'].'" class="validate[optional]" />
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
					  				Mobile
					  			</td>
					  			<td>
					  				<input type="tel" id="mobile" name="mobile" title="Mobile Del Admin" placeholder="Mobile" value="'.$datos[0]['mobile'].'" class="validate[funcCall[phoneOptional]]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Fax
					  			</td>
					  			<td>
					  				<input type="tel" id="fax" name="fax" title="Fax Del Admin" placeholder="Fax" value="'.$datos[0]['fax'].'" class="validate[funcCall[phoneOptional]]" />
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

		if( !$admin->UpdateAdmin($id, $_POST['usuario'], $_POST['nombre'], $_POST['apellidos'], $titulo, $_POST['email'], $_POST['telefono'], $mobile, $fax, $skype, $imagen, $password )){
			echo '<br/>Error: No se pudo actualizar el Admin.<br/>ajaxAdmin.php ActualzarAdmin()';
		}
		//actualiza datos de session
		$admin->updateSession();
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
					  				<input type="tel" id="mobile" name="mobile" title="Mobile Del Nuevo Admin" placeholder="Mobile" class="validate[funcCall[phoneOptional]]" />
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Fax
					  			</td>
					  			<td>
					  				<input type="tel" id="fax" name="fax" title="Fax Del Nuevo Admin" placeholder="Fax" class="validate[funcCall[phoneOptional]]" />
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
	$administradores =  new Admin();
	$datos = $administradores->getAdmins();

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
							Total Ingresos
						</th>
						<th>
							Ultimo Ingreso
						</th>
						<th>
							Ip
						</th>
					</tr>';
		
		foreach ($datos as $fila => $admin) {
			$lista .= '<tr>
						<td>
							'.$admin['nombre'].
						'</td>';

			$totalLogueos = "---";
			$ultimoIngreso = "---";
			$activo = "---";
			$ip = "---";

			if( $logs = $administradores->getAdminLogs( $admin['id'] ) ){
				$totalLogueos = sizeof( $logs );
				$x = sizeof($logs)-1;
				$ultimoIngreso = $logs[ $x ]['fecha'];
				
				if( $logs[$x]['ip'] != '' ){
					$ip = $logs[$x]['ip'];
				}
			}

			$lista .= '<td>
						'.$totalLogueos.'
					   </td>
					   <td>
					   	'.$ultimoIngreso.'
					   </td>
					   <td>
					   	'.$ip.'
					   </td>';

			$lista .= '</tr>';
		}

		$lista .= '</table>';

	}

	echo $lista;
}

/**
* MUESTRA LA LISTA DE IPS BLOQUEAS
*/
function Intentos(){
	$bloquear = new Bloquear();

	$lista = '<div class="titulo">
				Intentos Bloqueados
			  </div>
			  	<table class="table-list" id="intentos-bloqueado">
			  		<thead>
			  			<th title="Fecha del intento">
			  				Fecha
			  			</th>
			  			<th title="Intentos bloqueados">
			  				Intentos
			  			</th>
			  			<th title="Identificador unico del cumputador/dispositivo">
			  				Ip
			  			</th>
			  			<th title="Sitio donde se realizo el intento">
			  				Sitio
			  			</th>
			  			<th title="Estado del bloqueo">
			  				Estado
			  			</th>
			  		</thead>';

	if( $datos = $bloquear->getIps() ){

		//echo '<pre>'; print_r($datos); echo '</pre>';
		
		foreach ($datos as $f => $intento) {
			
			//si esta activo o es permatente el bloqueo
			if( $bloquear->Estado( $intento['ip'], $intento['sitio'] ) ){
				$estado = true;
			}else{
				$estado = false;
			}

			if( $intento['sitio'] == 0){
				$sitio = 'Cliente';
			}else{
				$sitio = 'Admin';
			}

			$lista .= '<tr id="'.$intento['id'].'" > 
						<td>
							'.$intento['fecha'].'
						</td>
						<td>
							'.$intento['total_intentos'].'
						</td>
						<td class="ip">
							'.$intento['ip'].'
						</td>
						<td>
							'.$sitio.'
						</td>
						<td class="estado">';
			
			$status = '---';
			
			if( $estado ){
				$minutos = $bloquear->Minutos( $intento['ip'], $intento['sitio'] );
				$minutos = floor($minutos/60);
				$status = "<span>Bloqueado: $minutos minutos</span>";
			}else{
				$status = 'Bloqueo expiro.';
			}

			if( $bloquear->EsPermanente( $intento['ip'] )){
				$status = 'Bloqueado Permanentemente.';
			}

			$lista .= '
						<div class="status-circle">
							<span>'.$status.'</span>
					   	</div>
					   </td>
					  </tr>';
		}

	}else{	
		$lista .= '<tr>
						<td class="nodata" colspan="5">
							No hay intentos bloqueadas
						</td>
				   </tr>';
	}

	$lista .= '	</table>';

	$lista .= '<div class="datos-botones">
					<button type="button" title="Cancelar Edición" onClick="CancelarContent()">Cancelar</button>
					<button class="ocultos" id="BotonBorrarIp" type="button" title="Eliminar IP" onClick="EliminarIp()">Eliminar</button>
					<button class="ocultos" id="BotonBloquearIp" type="button" title="Bloquear Permanentemente" onClick="BloquearIp()">Bloquear</button>
					<button class="ocultos" id="BotonDesloquearIp" type="button" title="Elimina el bloqueo" onClick="DesbloquearIp()">Desbloquear</button>
				</div>';

	echo $lista;
}


/**
* DESBLOQUEAR
* @param string $ip -> ip ha bloquears
*/
function Desbloquear( $ip ){
	$bloquear = new Bloquear();

	//echo 'la ip es: '. $ip;

	if( !$bloquear->DesbloquearIp( $ip ) ){
		echo 'Error: No se pudo desbloquear la ip '.$ip;
	}
}

/**
* BLOQUEO PERMANTENTE DE UN IP
* @param string $ip -> ip d bloquear
*/
function BloqueoPermanente( $ip ){
	$bloquear = new Bloquear();

	if( !$bloquear->BloqueoPermanenteIp($ip) ){
		echo 'Error: no se pudo bloquear permantentemente la ip '.$ip.'<br/>ajaxAdmin.php BloquearPermanentemente';
	}
}

/**
* ELIMINA UNA IP DEL REGISTROS, TAMBIEN DEL DE PERMANENTES SI ESTA HAY
* @param string $ip -> ip ha eliminar
*/
function EliminarIp( $ip ){
	$bloquear = new Bloquear();

	if( !$bloquear->EliminarIp( $ip ) ){
		echo "Error: no se pudo eliminar la ip ".$ip.'<br/> ajaxAdmin.php EliminarIp';
	}
}

/******************************** CONFIGURACION **************/
	
/**
* PANEL DE CONFIGURACION DEL SISTEMA
*/
function Config(){
	$configuracion = new Config();
	$config = $configuracion->getConfig();

	$panel = '<div class="titulo">
				Configuración
			  </div>
			  <form id="FormularioConfig" enctype="multipart/form-data" method="post" action="src/ajaxAdmin.php">
			  	<input type="hidden" name="func" value="ActualizarConfig">

				  <div class="datos-full">
				  	<div class="columna-full">
				  		<div class="subtitulo">
				  			General
				  		</div>
				  		<table>
				  			<tr>
				  				<td>
				  					Email
				  				</td>
				  				<td>
				  					<input type="text" id="support" name="support" title="Email para soporte" placeholder="email de soporte" value="'.$config[0]['support'].'" class="validate[required,custom[email]]" >
				  				</td>
				  			</tr>
				  			<tr>
				  				<td>
				  					Telefono
				  				</td>
				  				<td>
				  					<input type="text" id="telefono" title="Telefono" name="telefono" placeholder="Telefono soporte" value="'.$config[0]['telefono'].'" class="validate[required,custom[phone]]" >
				  				</td>
				  			</tr>
				  			<tr>
				  				<td>
				  					Fax
				  				</td>
				  				<td>
				  					<input type="text" id="fax" title="Fax" name="fax" placeholder="Fax soporte" value="'.$config[0]['fax'].'" class="validate[funcCall[phoneOptional]]" >
				  				</td>
				  			</tr>
				  			<tr>
				  				<td>
				  					Skype
				  				</td>
				  				<td> 
				  					<input type="text" id="skype" title="Skype" placeholder="Skype soporte" name="skype" value="'.$config[0]['skype'].'" >
				  				</td>
				  			</tr>
				  			<tr>
				  				<td>
				  					Link al salir
				  				</td>
				  				<td>
				  					<input type="text" id="link" title="Link para redirrecionar al salir" placeholder="Link al salir" name="link" value="'.$config[0]['link_salida'].'" class="validate[required]" >
				  				</td>
				  			</tr>
				  		</table>
				  		
				  		<div class="subtitulo">
				  			Seguridad
				  		</div>
				  		<table>
				  			<tr>
				  				<td title="Tiempo en minutos">
				  					Tiempo bloqueo
				  				</td>
				  				<td colspan="2">
				  					<input type="number" id="tiempo" title="Tiempo en el que se bloquea una ip" placeholder="Timepo bloqueo" name="tiempo" value="'.$config[0]['tiempo_bloqueo'].'" class="validate[required,custom[integer]]" >
				  				</td>
				  			</tr>
				  			<!--
				  			<tr>
				  				<td title="Permitir el reset de password del usuario" style="vertical-align: middle !important;">
				  					Reseteo de password
				  				</td>
				  				<td title="Para clientes" >
				  					<div class="titulo">Clientes</div>
				  					<table>
				  					<tr>
				  						<td>
				  							<input id="resetClienteSi" type="radio" name="resetCliente" value="1">
				  							<label for="resetClienteSi">Si</label>
				  						</td>
				  						<td>
				  							<input id="resetClienteNo" type="radio" name="resetCliente" value="0">
				  							<label for="resetClienteNo">No</label>
				  						</td>
				  					</tr>
				  					</table>
				  				</td>
				  				<td title="Para admins">
				  					<div class="titulo">Admin</div>
				  					<table>
				  					<tr>
				  						<td>
				  							<input id="resetAdminSi" type="radio" name="resetAdmin" value="1">
				  							<label for="resetAdminSi">Si</label>
				  						</td>
				  						<td>
				  							<input id="resetAdminNo" type="radio" name="resetAdmin" value="0">
				  							<label for="resetAdminNo">No</label>
				  						</td>
				  					</tr>
				  					</table>
				  				</td>
				  			</tr>-->
				  		</table>
				  	</div>
				  	<!--<div class="columna1">
				  		<div class="subtitulo">
				  			Vista Clientes
				  		</div>

				  	</div>
				  	<div class="columna2">
				  		<div class="subtitulo">
				  			Administracion
				  		</div>

				  	</div>-->
				  </div>
				  
				  <div class="datos-botones">
				  	<button type="button" title="Cancelar Edición" onClick="CancelarContent()">Cancelar</button>
				  	<button type="button" title="Usar valores por defecto" onClick="DefaultConfig()">Default</button>
					<input type="reset" title="Limpiar Edición" value="Limpiar" />
					<button type="button" title="Guardar Edición" onClick="ConfirmaConfig()">Guardar</button>
				  </div>
			  </form>';

	echo $panel;
}

/**
* ACTUALIZA LA CONFIGURACION DEL SITIO
*/
function ActualizarConfig(){
	$error = false;

	//requeridos
	$support = 'support@matriz.com';
	if( isset($_POST['support']) ){
		$support = $_POST['support'];
	}else{
		echo "Error: ajaxAdmin.php ActualizarConfig se requiere de un email de soporte.<br/>";
		$error = true;
	}

	$telefono = '1234-5678';
	if( isset($_POST['telefono']) ){
		$telefono = $_POST['telefono'];
	}else{
		echo "Error: ajaxAdmin.php ActualizarConfig se requiere de un telefono.<br/>";
		$error = true;
	}
	
	$tiempo = 120; //default
	if( isset($_POST['tiempo']) ){
		$tiempo = $_POST['tiempo'];
	}else{
		echo "Error: ajaxAdmin.php ActualizarConfig se requiere de un tiempo para bloqueo.<br/>";
		$error = true;
	}

	//si faltan datos
	if( $error ){
		return false;
	}

	$fax = '';
	if( isset($_POST['fax'])){
		$fax = $_POST['fax'];
	}

	$skype = '';
	if( isset($_POST['skype']) ){
		$skype = $_POST['skype'];
	}

	$link = 'login.php';
	if( isset($_POST['link'])){
		$link = $_POST['link'];
	}

	$configuracion = new Config();
	
	if( !$configuracion->updateConfig( $support, $telefono, $fax, $skype, $link, $tiempo ) ){
		echo 'Error: ajaxAdmin.php ActualizarConfig.<br/>Al actualizar la configuracion.';
	}

}

?>