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

	/**
	* METODO PARA OBTENER LOS DATOS DE LAS NORMAS DE UNA CATEGORIA DE UN PROYECTO EN REGISTROS
	* @param $categoria -> id de la categoria
	* @param $proyecto -> id del proyecto
	* @return array[][] -> con datos de las normas registradas
	* @return false -> si la consulta falla
	*/
	public function getNormas($categoria, $proyecto){
		$resultado = array();
		$base = new Database();
		$query = "SELECT DISTINCT norma FROM registros WHERE categoria = ".$categoria." AND proyecto =".$proyecto;

		$normas = $base->Select($query);

		if(!empty($normas)){
			foreach ($normas as $fila => $id) {
				$query = "SELECT * FROM normas WHERE id = ".$normas[$fila]['norma'];
				$norma = $base->Select($query);
				if(!empty($norma)){
					$resultado = $norma;
				}else{
					return false;
				}
			}

			return $resultado;
		}else{
			return false;
		}
	}

}


?>