<?php

require_once("class/actualizaciones.php");
require_once("class/proyectos.php");
require_once("class/config.php");

if( isset($_POST['func']) ){
	switch( $_POST['func'] ){

		//PROYECTOS CON ACTUALIZACIONES HABILIDATAS
		case 'Proyectos':
			echo Proyectos();
			break;

		//ACTUALIZACIONES DE UN PROYECTO
		case 'Actualizaciones':
			if( isset($_POST['proyecto']) ){
				echo Actualizaciones($_POST['proyecto']);
			}
			break;

		//EDICION DE NUEVA ACTUALIZACION
		case 'NuevaActualizacion':
			if( isset($_POST['proyecto']) ){
				echo NuevaActualizacion( $_POST['proyecto'] );
			}
			break;

		//NUEVA ACTUALIZACION
		case 'CrearActualizacion':
			if( isset($_POST['proyecto']) && isset($_POST['nombre']) && isset($_POST['notas']) && isset($_POST['fecha']) ){
				CrearActualizacion();
			}
			break;

		//EDICION DE UNA ACTUALIZACION
		case 'EditarActualizacion':
			if( isset($_POST['proyecto']) && isset($_POST['id'])){
				echo EditarActualizacion( $_POST['proyecto'], $_POST['id'] );
			}
			break;

		//ACTUALIZAR ACTUALIZACION EDITADA
		case 'ActualizarActualizacion':
			if( isset($_POST['id']) && isset($_POST['nombre']) && isset($_POST['fecha']) ){
				ActualizarActualizacion($_POST['id']);
			}
			break;

		//ELIMINAR ACTUALIZACION
		case "EliminarActualizacion":
			if( isset($_POST['id']) ){
				EliminarActualizacion($_POST['id']);
			}
			break;

	}
}

/**
 * MUESTRA LOS PROYECTOS CON ACTUALIZACIONES
 * @return string $lista;
 */
function Proyectos(){
	$proyectos = new Proyectos();
	$actualizaciones = new Actualizaciones();

	$lista = '<div class="titulo" >
	            Proyectos
	          <img class="boton-buscar icon" title="Buscar Actualizaciones" onclick="Busqueda(\'busqueda-proyectos-actualizaciones\', \'busqueda-proyectos-actualizaciones-input\', \'proyectos-actualizaciones\', false)" src="images/search2.png">
		      </div>
		      <div class="busqueda" id="busqueda-proyectos-actualizaciones" style="display: none;">
					<div class="buscador">
							<input type="search" title="Escriba Para Buscar Entidades" id="busqueda-proyectos-actualizaciones-input" placeholder="Buscar Actualizaciones">
					</div>
			  </div>
	          <ul id="proyectos-actualizaciones">';

	if( $datos = $proyectos->getProyectosActualizaciones() ){

		foreach( $datos as $key => $proyecto ){
			$contador = $actualizaciones->getContadorProyecto($proyecto['id']);

			$lista .= '<li id="'.$proyecto['id'].'">
						'.$proyecto['nombre'].'
						<span class="contador">'.$contador.'</span>
					   </li>';
		}
	}else{
		$lista .= '<li class="notada">
						No hay proyectos con actualizaciones habilitadas
				   </li>';
	}

	$lista .= '</ul>';

	return $lista;
}

/**
 * MUESTRA LA LISTA CON TODAS LAS ACTUALIZACIONES DE UN PROYECTO
 * @param int $proyecto
 * @return string lista html de la lista
 */
function Actualizaciones( $proyecto ){
	$proyectos = new Proyectos();
	$actualizaciones = new Actualizaciones();

	$proyectoName = $proyectos->getProyectoDato("nombre", $proyecto);

	$lista = '<div class="titulo">
				'.$proyectoName.'
				<img class="boton-buscar icon" title="Buscar Actualizaciones" onclick="Busqueda(\'busqueda-actualizaciones\', \'busqueda-actualizaciones-input\', \'actualizaciones\', false)" src="images/search2.png">
		      </div>
		      <div class="busqueda" id="busqueda-actualizaciones" style="display: none;">
					<div class="buscador">
							<input type="search" title="Escriba Para Buscar Entidades" id="busqueda-actualizaciones-input" placeholder="Buscar Actualizaciones">
					</div>
			  </div>
		      <div id="actualizaciones">
		        <ul class="scroll" >';

	if( $datos = $actualizaciones->getActualizaciones($proyecto) ){

		foreach($datos as $key => $actualizacion){

			$lista .= '<li id="'.$actualizacion['id'].'" >
							'.$actualizacion['nombre'].'
					   </li>';
		}

	}

	$lista .= '</div>
			   </ul>
			   <div class="menu-botones">
				<button type="button" onclick="$Actualizaciones.crearActualizacion()">Nueva</button>
				<button type="button" class="ocultos" onclick="$Actualizacion.editarActualizacion()">Eliminar</button>
				<button type="button" class="ocultos">Eliminar</button>
			   </div>';

	return $lista;
}

/**
 * EDICION DE UNA NUEVA ACTUALIZACION
 * @param int $proyecto
 * @return boolean
 */
function NuevaActualizacion( $proyecto ){

	$form = '<div class="titulo">
	            Nueva Actualizacion
	         </div>
	         <form method="POST" action="src/ajaxActualizaciones.php" id="FormularioNuevaActualizacion">
	         <div class="datos">
	            <input type="hidden" name="func" value="CrearActualizacion">
	            <input type="hidden" name="proyecto" value="'.$proyecto.'">
					<table>
						<tr>
							<td>
								Nombre
							</td>
							<td>
								<input type="text" name="nombre" class="validate[required]" placeholder="nombre" >
							</td>
						</tr>
						<tr>
							<td title="Fecha en que inicia la vigencia">
								Fecha Vigencia
							</td>
							<td>
								<input type="text" name="fecha" id="fecha"  class="validate[required]" placeholder="fecha">
							</td>
						</tr>
					</table>

					Notas<br/>
					<textarea name="notas" placeholder="Notas" class="validate[optional]"></textarea>
					<br/><br/>
			  </div>
			  <div class="adjuntos">
			  </div>

			  <div class="datos-botones">
			    <button type="button" class="button-cancelar" onClick="CancelarContent()">Cancelar</button>
	            <button type="submit">Guardar</button>
	            <button type="reset">Limpiar</button>
	          </div>

	          </form>';

	return $form;
}

/**
 * CREA UNA NUEVA ACTUALIZACION
 */
function CrearActualizacion(){
	$actualizacion = new Actualizaciones();

	$proyecto = $_POST['proyecto'];
	$nombre = $_POST['nombre'];
	$notas = $_POST['notas'];
	$fecha = $_POST['fecha'];

	if( !$actualizacion->newActualizacion($proyecto, $nombre, $notas, $fecha) ){
		echo "Error: no se pudo crear la actualizacion.";
	}

}

/**
 * EDITAR ACTUALIZACION
 * @param int $proyecto
 * @param int $id
 * @return boolean
 */
function EditarActualizacion( $proyecto, $id ){
	$actualizaciones = new Actualizaciones();
	$config  = new Config();

	$nombre = $actualizaciones->getDato("nombre", $id);

	$form = '<div class="titulo">
	            Edicion '.$nombre.'
	         </div>
	         <form method="POST" action="src/ajaxActualizaciones.php">
	         <div class="datos">';

	if( $datos = $actualizaciones->getActualizacion($id) ){

		$fecha = $config->formatDateOut($datos[0]['fecha_vigencia']);

		$form .= '  <input type="hidden" name="func" value="ActualizarActualizacion">
					<input type="hidden" name="id" value="'.$datos[0]['id'].'">
					<table>
						<tr>
							<td>
								Nombre
							</td>
							<td>
								<input type="text" name="nombre" class="validate[required]" placeholder="nombre" value="'.$datos[0]['nombre'].'">
							</td>
						</tr>
						<tr>
							<td title="Fecha en que inicia la vigencia">
								Fecha Vigencia
							</td>
							<td>
								<input type="text" name="fecha" id="fecha" class="validate[required]" placeholder="fecha" value="'.$fecha.'">
							</td>
						</tr>
					</table>

					Notas<br/>
					<textarea name="notas" class="validate[optional]" placeholder="notas">'.$datos[0]['notas'].'</textarea>
					<br/><br/>
	            ';

	}else{
		$form .= '<p>No hay datos</p>';
	}

	$form .= '</div>
			  <div class="adjuntos">
			  </div>';

	$form .= '<div class="datos-botones">
				<button type="button" class="button-cancelar" onClick="CancelarContent()">Cancelar</button>
	            <button type="submit">Guardar</button>
	            <button type="reset">Limpiar</button>
	          </div>

	          </form>';

	return $form;
}

/**
 * ACTUALIZA UNA ACTUALIZACION EDITADA
 * @param int $id
 * @return boolean
 */
function ActualizarActualizacion($id){
	$actualizaciones = new Actualizaciones();

	$id = $_POST['id'];
	$nombre = $_POST['nombre'];
	$notas = "";
	if( isset($_POST['notas']) ){
		$notas = $_POST['notas'];
	}
	$fecha_vigencia = $_POST['fecha'];

	if( $actualizaciones->updateActualizacion($id, $nombre, $notas, $fecha_vigencia) ){
		return true;
	}

	return false;
}

/**
 * ELIMINA UNA ACTUALIZACION
 * @param $id
 */
function EliminarActualizacion($id){
	$actualizaciones = new Actualizaciones();

	if( !$actualizaciones->deleteActualizacion($id) ){
		echo "Error: no se pudo eliminar la actualizacion.";
	}

}