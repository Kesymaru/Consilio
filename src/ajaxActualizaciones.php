<?php
require_once("class/proyectos.php");
require_once("class/actualizaciones.php");
require_once("class/config.php");

if( isset($_POST['func']) ){

	switch($_POST['func']){

		//actualizaciones del proyecto lista para calendario
		case 'ActualizacionesProyecto':
			if( isset($_POST['proyecto']) ){
				echo Actualizaciones( $_POST['proyecto'] );
			}
			break;

		//lista de un mes
		case 'ActualizacionesMonth':
			if( isset($_POST['month']) && isset($_POST['year']) ){
				echo ActualizacionesMonth($_POST['year'], $_POST['month']);
			}
			break;
	}

}


/**
* CREA LAS ACTUALIZACIONES
* @param int $proyecto
* return string
*/
function Actualizaciones( $proyecto ){
	$actualizaciones = new Actualizaciones();
	$config = new Config();

	$lista = "<div id='actualizacion-calendario'>
				<div class='titulo'>
					Fecha
				</div>
					<div class='actualizacion-wrapper'>
					<ul>";

	//si tiene actualizaciones
	if( $datos = $actualizaciones->getActualizacionesOrdenadas($proyecto) ){

		foreach( $datos as $year => $months ){

			$lista .= '<li class="year">
					        '.$year.'
					   </li>';

			foreach( $months as $f => $contador){
				$monthName = $config->getMonth($f);
				$lista .= '<li class="'.$year.'" id="'.$f.'" >
								'.$monthName.'
								<span class="contador">'.$contador.'</span>
							</li>';
			}

		}

	}else{
		$lista .= '<li  class="no-data">No hay actualizaciones</li>';
	}

	$lista .= '</div><!-- end wrapper -->
			   </div>';

	return $lista;
}

/**
 * OBTIENE LAS ACTUALIZACIONES DE UN MES
 * @param int $year
 * @param int $mes
 * @return string
 */
function ActualizacionesMonth($year, $month){
	$actualizaciones = new Actualizaciones();
	$config = new Config();

	$lista = '<div id="actualizacion-content">
				<div class="titulo">
					'.$config->getMonth($month).'
				</div>
				<div class="actualizacion-wrapper actualizaciones-datos">
				<ul>';

	if( $datos = $actualizaciones->getActualizacionesMonth($year, $month) ){
		//echo '<pre>'; print_r($datos); echo '</pre>';

		foreach($datos as $f => $actualizacion ){
			$lista .= '<li class="titulo">
							'.$actualizacion['nombre'].'
					   </li>';

			$lista .= '<li id="'.$actualizacion['id'].'">
							<table>
								<tr>
									<td>
										Nombre:
									</td>
									<td>
										'.$actualizacion['nombre'].'
									</td>
								</tr>
								<tr>
									<td>
										Fecha:
									</td>
									<td>
										'.$actualizacion['fecha_actualizacion'].'
									</td>
								</tr>';

			if( $actualizacion['notas'] != "" ){
				$lista .= '<tr>
								<td>
									Nota:
								</td>
								<td>
									'.$actualizacion['notas'].'
								</td>
						   </tr>';
			}

			//archivos
			if( $archivos = $actualizaciones->getActualizacionArchivos($actualizacion['id']) ){
				$lista .= '<tr>
							 <td colspan="2" class="permiso-archivos" >
							 <ul>';

				foreach( $archivos as $k => $archivo ){
					$nombre = $archivo['nombre'];
					$lista .= '<li>
			                       <a href="" title="Descargar">
			                            <img src="images/folder.png">
			                            <div>
			                                <span>'.$nombre.'</span>
			                            </div>
			                       </a>
		                       </li>';
				}

				$lista .= ' </ul>
 							</td>
							</tr>';
			}

			$lista .= '</table>
					   </li>';
		}

	}else{
		$lista .= '<li class="nodata">No hay actualizaciones</li>';
	}

	$lista .= '	</ul>
				</div>
			  </div>';

	return $lista;
}