<?php

require_once("class/actualizaciones.php");
require_once("class/proyectos.php");

if( isset($_POST['func']) ){
	switch( $_POST['func'] ){

		//PROYECTOS CON ACTUALIZACIONES HABILIDATAS
		case 'Proyectos':
			echo Proyectos();
			break;

		//
		case 'Actualizaciones':
			if( isset($_POST['proyectos']) ){
				echo Actualizaciones($_POST['proyecto']);
			}
			break;

	}
}

/**
 * MUESTRA LOS PROYECTOS CON ACTUALIZACIONES
 * @return string $lista;
 */
function Proyectos(){
	$actualizaciones = new Actualizaciones();

	$lista = '<div class="titulo" >
	            Proyectos
	          </div>
	          <ul id="proyectos-actualizaciones">';

	if( $proyectos = $actualizaciones->getProyectos() ){

		foreach( $proyectos as $key => $proyecto ){
			$lista .= '<li id="'.$proyecto['id'].'">
						'.$proyecto['nombre'].'
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
	$actualizaciones = new Actualizaciones();
	$proyectos = new Proyectos();

	$proyecto = $proyectos->getProyectoDato("nombre", $proyecto);

	$lista = '<div class="titulo">
				'.$proyecto.' Actualizaciones
		      </div>
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

	$lista .= '</ul>';

	return $lista;
}

/**
 * EDITAR ACTUALIZACION
 * @param int $id
 * @return boolean
 */
function EditarActualizacion( $id ){

}

/**
 * ELIMINA UNA ACTUALIZACION
 * @param int $id
 * @return boolean
 */
function EliminarActualizacion( $id ){

}