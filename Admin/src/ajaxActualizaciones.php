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

		//EDICION DE UNA ACTUALIZACION
		case 'editarActualizacion':
			if( isset($_POST['proyecto']) && isset($_POST['id'])){
				echo EditarActualizacion( $_POST['proyecto'], $_POST['id'] );
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
		      </div>
		      <div id="actualizaciones">
		        <ul>';

	if( $datos = $actualizaciones->getActualizaciones($proyecto) ){

		foreach($datos as $key => $actualizacion){

			$lista .= '<li id="'.$actualizacion['id'].'" >
							'.$actualizacion['nombre'].'
					   </li>';
		}

	}else{
		$lista .= '<li class="nodata"><span>+</span></li>';
	}

	$lista .= '</div>q
			   </ul>';

	return $lista;
}

/**
 * EDITAR ACTUALIZACION
 * @param int $proyecto
 * @param int $id
 * @return boolean
 */
function EditarActualizacion( $proyecto, $id ){
	$actualizaciones = new Actualizaciones();

	$nombre = $actualizaciones->getDato("nombre", $id);

	$form = '<div class="titulo">
	            Edicion '.$nombre.'
	         </div>
	         <div class="datos">';

	if( $datos = $actualizaciones->getActualizacion($id) ){
		$form .= '<form method="POST" action="src/ajaxActualizaciones.php">
					<table>
						<tr>
							<td>
								Nombre
							</td>
							<td>
								<input type="text" name="nombre" class="" placeholder="nombre" value="'.$datos[0]['nombre'].'">
							</td>
						</tr>
						<tr>
							<td title="Fecha en que inicia la vigencia">
								Fecha Vigencia
							</td>
							<td>
								<input type="text" name="fecha" value="'.$datos[0]['fecha_vigencia'].'">
							</td>
						</tr>
					</table>
					Notas<br/>
					<textarea>'.$datos[0]['notas'].'</textarea>
	            </form>';

	}else{
		$form .= '<p>No hay datos</p>';
	}

	$form .= '</div>
			  <div class="adjuntos">
			  </div>';

	$form .= '<div class="datos-botones">
	            <input type="submit" value="Guardar">
	            <input type="reset" value="Limpiar">
	            <button type="button" class="button-cancelar">Cancelar</button>
	          </div>';

	return $form;
}

/**
 * ELIMINA UNA ACTUALIZACION
 * @param int $id
 * @return boolean
 */
function EliminarActualizacion( $id ){

}