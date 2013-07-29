<?php

require_once("classDatabase.php");

/**
 * Class Config
 * CLASE PARA LA CONFIGURACION
 */
class Config{

	private $idioma = 'es';
	private $months = '';
	private $days = '';

	private $base = '';

	/**
	 * OPCIONES DE CONFIGURACION
	 * @param string $idioma
	 */
	public function __construct($idioma = 'es'){
		$this->idioma = $idioma;

		$this->base = new Database();

		//set data
		$this->setMonthsNames();
		$this->setDaysNames();
	}

	/**
	 * OBTIENE ALGUN PARAMETRO DE LA CONFIGURACION
	 * @param string $sitio-> 0 = cliente, 1 = admin
	 * @param string $campo -> dato solicitado
	 * @return boolean false -> si falla
	 * @return string/int $dato -> dato solicitado
	 */
	public function getConfig(){
		$base = new Database();

		$query = "SELECT * FROM config";

		$datos = $base->Select( $query );

		if( !empty($datos) ){
			return $datos;
		}else{
			return false;
		}
	}

	/**
	 * OBTIENE EL NOMBRE DE UN MES
	 * @return boolean|string
	 */
	private function setMonthsNames(){
		$query = "
				 SELECT
				 	valor
				 FROM
				 	idiomas
				 WHERE
				 	idioma = '".$this->idioma."' AND
				 	campo = 'months'";

		if( $datos = $this->base->Select($query) ){

			if( $months = explode(",", $datos[0]['valor'] ) ){

				$this->months = $months;
			}
			return $datos;
		}
	}

	/**
	 * OBTIENE EL NOMBRE DE UN DIA
	 * @param int $day
	 * @return boolean|array
	 */
	private function setDaysNames(){
		$query = "
				 SELECT
				 	valor
				 FROM
				 	idiomas
				 WHERE
				 	idioma = '".$this->idioma."' AND
				 	campo = 'days' ";

		if( $datos = $this->base->Select($query) ){

			if( $days = explode(",", $datos[0]['valor'] )){
				$this->days = $days;
			}
		}
		return false;
	}

	/************************** GETTERS *******************/

	/**
	 * OBTIENE EL NOMBRE DE UN MES
	 * @param int $month
	 * @return string|boolean
	 */
	public function getMonth($month){

		if( is_array($this->months )){
			return $this->months[$month];
		}

		return false;
	}

	public function getDay($day){

		if( is_array($this->days )){
			return $this->days[$day];
		}

		return false;
	}
}