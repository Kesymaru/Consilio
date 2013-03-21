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

		//MUESTRA LOGUEOS DE LOS USUARIOS
		case 'Logs':
			Logs();
			break;

		//MUESTRA LAS ESTADISTICAS DEL CLIENTE
		case 'ClienteEstadisticas':
			if( isset( $_POST['id'] ) ){
				ClienteEstadisticas( $_POST['id'] );
			}
			break;

		case 'ClienteDates':
			if( isset($_POST['id']) ){
				getClienteDates( $_POST['id'] );
			}
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
			  		<img class="boton-buscar icon" title="Buscar Proyectos" onClick="Busqueda(\'busqueda-clientes\', \'buscar-clientes\', \'clientes\', false)" src="images/search2.png" >
			  	</div>

			  	<div class="busqueda" id="busqueda-clientes">
					<div class="buscador">
						<input type="search" title="Escriba Para Buscar" id="buscar-clientes" placeholder="Buscar"/>
					</div>
				</div>';

	if(!empty($clientes)){

		$lista .= '<div class="scroll">
					<ul>';
		
		foreach ($clientes as $fila => $cliente) {
			$lista .= '<li id="'.$cliente['id'].'" title="'.$cliente['nombre'].' '.$cliente['registro'].'" onClick="SelectCliente('.$cliente['id'].')">'
				.$cliente['nombre'].'
				</li>';
		}

		$lista .= '</ul><!-- end lista -->
					</div> <!-- end scroll -->';

	}else{
		$lista = "No hay clientes";
	}

	$lista .= '<div class="menu-botones">
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
					  			<td rowspan="7" class="td-user-image">
					  				<img id="imagen-usuario" src="'.$datos[0]['imagen'].'" title="Imagen Del Cliente" ><br/>					  				
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
					  				Pais
					  			</td>
					  			<td>
					  				'.PaisSelected($datos[0]['pais']).'
					  			</td>
					  		</tr>
					  		
					  		<tr>
					  			<td>
					  				Registro
					  			</td>
					  			<td>
					  				<input type="text" id="registro" name="registro" title="Registro Del Cliente" placeholder="Registro" value="'.$datos[0]['registro'].'" class="validate[required]" />
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
					  			<td rowspan="7" class="td-user-image">
					  				<img id="imagen-usuario" src="images/es.png" title="Imagen Del Nuevo Cliente" >
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
					  				Pais
					  			</td>
					  			<td>
					  				'. Paises() .'
					  			</td>
					  		</tr>
					  		<tr>
					  			<td>
					  				Registro
					  			</td>
					  			<td>
					  				<input type="text" id="registro" name="registro" title="Registro Del Nuevo Cliente" placeholder="Registro" class="validate[required]" />
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
* COMPONE UN SELECT CON LOS PAISES DISPONIBLES
* @return string $lista
*/
function Paises(){
	$cliente = new Cliente();

	$paisesDatos = $cliente->getPaises();

	$lista = '';

	if( !empty($paisesDatos) ){
		$lista .= '<select name="pais" id="pais">';

		foreach ($paisesDatos as $key => $pais) {
			
			if( $pais['id'] == 51 ){
				$lista .= '<option selected value="'.$pais['id'].'">
							'.$pais['Name'].'
						   </option>';
			}else{
				$lista .= '<option value="'.$pais['id'].'">
							'.$pais['Name'].'
						   </option>';
			}
		}

		$lista .= '</select>';
	}else{
		$lista .= 'Error: no se pudo obtener la informacion del pais';
	}

	return $lista;
}

/**
* COMPONE SELECT CON PAIS SELECCIONADO
* @param int $selected  id del pais seleccionado
* @return string $lista
*/
function PaisSelected($selected){
	$cliente = new Cliente();

	$paisesDatos = $cliente->getPaises();

	$lista = '';

	if( !empty($paisesDatos) ){
		$lista .= '<select name="pais" id="pais">';

		foreach ($paisesDatos as $key => $pais) {
			
			if( $pais['id'] == $selected ){
				$lista .= '<option selected value="'.$pais['id'].'">
							'.$pais['Name'].'
						   </option>';
			}else{
				$lista .= '<option value="'.$pais['id'].'">
							'.$pais['Name'].'
						   </option>';
			}
		}

		$lista .= '</select>';
	}else{
		$lista .= 'Error: no se pudo obtener la informacion del pais';
	}

	return $lista;
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
		if( !$cliente->UpdateCliente($id, $_POST['nombre'], $_POST['email'], $_POST['pais'], $_POST['registro'], $_POST['telefono'], $_POST['skype'], $_POST['usuario'], $contrasena )){
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
		if(!$cliente->NewCliente($_POST['nombre'], $_POST['email'], $_POST['pais'], $_POST['registro'], $_POST['telefono'], $_POST['skype'], $imagen, $_POST['contrasena'], $_POST['usuario'] )){
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


/**************************** LOGS ***********************************/

/**
* MUESTRA LISTA CON LOS LOGS DE LOS CLIENTES
*/
function Logs(){
	$usuarios = new Cliente();
	$clientes = $usuarios->getClientes();

	$lista = '<div class="clientes">
					<div class="titulo" title="Ingresos Clientes">
						Ingresos Clientes
					</div>
			  ';
				
	if(!empty($clientes)){

		$lista .= '<table id="cliente-logs" class="table-list">
					<tr>
						<th>
							Nombre
						</th>
						<th>
							Usuario
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

		//compone tabla de clientes
		foreach ($clientes as $dato => $cliente) {

			if( $logs = $usuarios->getClienteLogs( $cliente['id'] ) ){
				//echo '<prel>'; print_r($logs); echo '</pre>';

				$totalLogueos = sizeof( $logs );
				
				$x = sizeof($logs)-1;
				$ultimoLogueo =  $logs[$x]['fecha'];
				$ip = $logs[$x]['ip'];

			}else{
				$totalLogueos = "---";
				$ultimoLogueo = "---";
				$ip = "---";
			}

			$lista .= '<tr id="'.$cliente['id'].'">
						<td>
							'.$cliente['nombre'].'
						</td>
						<td>
							'.$cliente['usuario'].'
						</td>
						<td>
							'.$totalLogueos.'
						</td>
						<td>
							'.$ultimoLogueo.'
						</td>
						<td>
							'.$ip.'
						</td>
					  </tr>';
		}

		$lista .= '</table>';

		//controles
		$lista .= '<div class="datos-botones">
					<button type="button" title="Cancelar Edición" onClick="CancelarContent()">Cancelar</button>
					<input type="reset" title="Limpiar Edición" value="Limpiar" />
					<input type="submit" title="Guardar Edición" value="Guardar" />
				</div>
			</div>';

	}else{
		$lista .= 'No hay clientes';
	}

	echo $lista;
}

/**
* COMPONE LAS ESTADISTICAS DE UN CLIENTE
* @param int $id -> id del cliente
* @return string $estadisticas -> text/html con las estadisticas
*/
function ClienteEstadisticas( $id ){
	$cliente = new Cliente();
	$proyectos = new Proyectos();

	$logs = $cliente->getClienteLogs( $id );
	$datos = $cliente->getDatosCliente( $id );
	$datosProyectos = $proyectos->getProyectosCliente( $id );

	$estadisticas = '<div class="titulo">Registros '.$datos[0]['nombre'].'</div>';

	if( !empty($logs) ){
		//echo '<pre>'; print_r($logs); echo '</pre>';

		//dias en que entro y numero de entradas por dia
		//$logsDias = $cliente->getClienteLogsDias( $id );

		$logsDias = $cliente->getClienteLogsDia( $id, '2013-03-18');

		//echo '<pre>'; print_r($logsDias); echo '</pre>';

		$ahora = strtotime( date('Y-m-d G:i:s') );

		$estadisticas .= '<div class="datos-date">
							<div id="date" class="date-left">
							</div>
							<div class="date-right">
								<div class="sub-titulo">
									informacion
								</div>
								<!-- informacion del dia 
								<ul class="list" id="date-info">

								</ul> -->
								<table id="date-info">
									<thead>
										<tr>
											<th width="50%">
												Hora
											</th>
											<th width="50%">
												Ip
											</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						  </div>';
	}

	echo $estadisticas;
}

/**
* COMPONE EL JSON PARA EL CALENDARIO DE INGRESOS DE UN CLIENTE
* @param int $id -> id del cliente
*/
function getClienteDates( $id ){
	$cliente = new Cliente();

	$logs = $cliente->getClienteLogs( $id );

	$json = array();
	if( !empty($logs) ){
		foreach ($logs as $dato => $log) {
			
			$dia = substr($log['fecha'], 0, 10);
			$dia = explode('-', $dia);
			if( is_array($dia) ){
				$dia = $dia[0].'/'.$dia[1].'/'.$dia[2];
			}

			$hora = substr($log['fecha'], 11);

			$json[] = array( "Title"  => "Ingreso", "Date" => $dia, "Hour" => $hora, "Ip" => $log['ip'] );
		}

		//echo '<pre>'; print_r($json); echo '</pre>';
		echo stripslashes(json_encode($json));
	}
}

?>