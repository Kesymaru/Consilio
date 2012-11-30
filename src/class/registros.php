<?php

require_once("classDatabase.php");

/**
* MANEJA LOS REGISTROS DE CATEGORIAS, GENERALIDADES Y NORMAS
*/
class Registros{
	
	public function getNormaDato($dato, $id){
		$base = new Datase();
		$query = "SELECT ".$dato." FROM normas WHERE id = ".$id;
		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos[0][$dato];
		}else{
			return false;
		}
	}

	public function getCategoriaDato($dato, $id){
		$base = new Database();
		$query = "SELECT ".$dato." FROM categorias WHERE id = ".$id;
		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos[0][$dato];
		}else{
			return false;
		}
	}

	public function getGeneralidadDato(){
		$base = new Database();
		$query = "SELECT * FROM categorias WHERE normaId = ".$norma." AND parentId = ".$categoria.
		$temp = $base->Select($query);
		$id = $temp[0]['id'];

		$queryFinal = "SELECT ".$dato." FROM normas WHERE id = ".$id;
		$datos = $base->Select($queryFinal);

		if(!empty($datos)){
			return $datos[0][$dato];
		}else{
			return false;
		}
	}

}

?>