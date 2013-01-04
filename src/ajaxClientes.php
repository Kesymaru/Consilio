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
		$formulario .= '<form id="FormularioEditarCliente" enctype="multipart/form-data" method="post" action="src/ajaxTipos.php" >
					<div class="clientes">
						<div class="titulo">
							Edicion Cliente
					  		<hr>
					  	</div>
					  	<input type="hidden" name="func" value="ActualizarCliente" />
					  	<input type="hidden" id="cliente" name="cliente" value="'.$id.'" />

					  	<div class="datos" >
					  		<table>
					  		<tr>
					  			<td>
					  				Nombre
					  			</td>
					  			<td>
					  				<input type="text" id="nombre" name="nombre" title="Nombre Del Cliente" placeholder="Nombre" value="'.$datos[0]['nombre'].'" class="validate[required]" />
					  			</td>
					  			<td rowspan="5">
					  				<img id="imagen-usuario" src="images/'.$datos[0]['imagen'].'" title="Imagen Del Cliente"><br/>
					  				<input type="file" name="imagen" id="imagen" />
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
					  				<input type="text" id="skype" name="skype" title="Skype Del Cliente" placeholder="Skype" value="'.$datos[0]['skype'].'" class="validate[required]" />
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
* ACTUALIZA UN CLIENTE EDITADO
*/
function ActualizarCliente(){

}



?>