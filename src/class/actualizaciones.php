<?php
require_once('classDatabase.php');

class Actualizaciones{

	private $base = '';

	public function __construct(){
		$this->base = new Database();
	}

	/**
	 * OBTIENE ACTUALIZACIONES DE UN PROYECTO
	 * @param $proyecto
	 * @return array|boolean
	 */
	public function getActualizaciones($proyecto){
		$query = "
				SELECT *
				FROM actualizaciones
				WHERE proyecto = '$proyecto' ";

		if( $datos = $this->base->Select($query) ){
			return $datos;
		}
		return false;
	}

	/**
	 * OBTIENE LAS ACTUALIZACIONES Y LAS ORDENA EN CADA MES
	 * @param int $proyecto id proyecto
	 * @return boolean|array
	 */
	public function getActualizacionesOrdenadas($proyecto){

		$orden = array();

		if( $actualizaciones = $this->getActualizaciones($proyecto) ){

			foreach( $actualizaciones as $f => $actualizacion ){

				$year = $this->getYear($actualizacion['fecha_creacion']);
				$month = $this->getMonth($actualizacion['fecha_creacion']);

				//ordena por year y por month y cuenta
				if( !isset($orden[$year][$month]) ){
					$orden[$year][$month] = 1;
				}else{
					$orden[$year][$month]++;
				}

			}

			return $orden;
		}

		return false;
	}

	/**
	 * OBTIENE EL YEAR DE UNA FECHA
	 * @param date $date
	 * @return int
	 */
	private function getYear($date){
		return date('Y',strtotime($date));
	}

	/**
	 * OBTIENE EL MES DE UNA FECHA
	 * @param date $date
	 * @return int
	 */
	private function getMonth($date){
		return date('m',strtotime($date))-1;
	}

	/**
	 * OBTIENE LOS DATOS DE UNA ACTUALIZACION
	 * @param $id
	 * @return int
	 */
	public function getActualizacion( $id ){
		$query = "
			     SELECT *
			     FROM actualizaciones
			     WHERE id = '$id' ";

		if( $datos = $this->base->Select($query) ){
			return $datos;
		}
		return false;
	}

	/**
	 * OBTIENE LOS DATOS DE LAS ACTUALIZACIONES DE UN MES
	 * @param int $year
	 * @param int $month
	 * @return boolean|array
	 */
	public function getActualizacionesMonth($year, $month){
		$month += 1;
		$fechaMin = date( 'Y-m-d', strtotime($year.'-'.$month) );
		$fechaMax = date( 'Y-m-d', strtotime($year.'-'.$month.'+1 month -1 day') );

		$query = "
				 SELECT
				 	*
				 FROM
				 	actualizaciones
				 WHERE
					'$fechaMin' <= fecha_actualizacion AND
				    fecha_actualizacion <= '$fechaMax' ";

		if( $datos = $this->base->Select($query) ){
			return $datos;
		}

		return false;
	}

	/**
	 * OBTIENE LOS ARCHIVOS DE UNA ACTUALIZACION
	 * @param int $actualizacion
	 * @return boolean|array
	 */
	public function getActualizacionArchivos($actualizacion){

		$query = "
				 SELECT
				 	*
				 FROM
				 	actualizaciones_archivos
				 WHERE
				 	actualizacion = '$actualizacion' ";

		if( $datos = $this->base->Select($query) ){
			return $datos;
		}

		return false;
	}

}