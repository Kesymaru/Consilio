<?php

require_once("classDatabase.php");

/**
* CLASE PARA NOTAS
*/
class Notas{
	
	/**
	* GETTER PARA UN DATO DE UNA NOTA
	* @param $dato -> dato a consultar
	* @param $id -> id de la nota
	* @return $dato si la consulta es correcta
	* @return false si la consulta es errorenea
	*/
	public function getNotaDato($dato, $id){
		$base = new Datase();
		$query = "SELECT ".$dato." FROM notas WHERE id = ".$id;
		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos[0][$dato];
		}else{
			return false;
		}
	}	
}

?>