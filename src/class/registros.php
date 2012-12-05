<?php

require_once("classDatabase.php");
require_once("usuarios.php");
require_once("session.php");

/**
* MANEJA LOS REGISTROS DE LAS NORMAS Y CATEGORIAS
*/
class Registros{
	private $registros = array(); //array[][][][];
	private $categorias = array(); 

	/**
	* OBTIENE TODOS LOS REGISTROS DE UN PROYECTO Y LOS COMPONE EN UN SOLO ARRAY
	* @param $proyecto -> id del proyecto 	
	*/
	public function getRegistros($proyecto){
		//SEGURIDAD LOGUEADO
		$session = new Session();
		$session->Logueado();

		$base = new Database();
		$consulta = $base->Select("SELECT * FROM registros WHERE proyecto = ".$proyecto);

		if(!empty($consulta)){
			
			//COMPONE TODOS LOS REGISTROS DE UN PROYECTO
			foreach ($consulta as $fila => $valors) {
				$this->registros[$fila]['categoria'] = $this->getNorma($consulta[$fila]['categoria']);
				
				$this->registros[$fila]['normas'] = $this->getDatosNorma($consulta[$fila]['categoria']);

				$this->registros[$fila]['observacion'] = $this->getObservacion($consulta[$fila]['observacion']);
				
				$this->registros[$fila]['archivos'] = $this->getArchivos($proyecto, $consulta[$fila]['categoria']);
			}

			return $this->registros;
		}else{
			//NO HAY DATOS PARA EL PROYECTO
			return false;
		}
	}

	/**
	* PRIMEROS 2 NIVELES DE LAS CATEGORIAS
	*/
	public function getCategorias(){
		$base = new Database();
		$query = "SELECT id FROM categorias WHERE padre = 0";

		$datos = $base->Select($query);

		if(!empty($datos)){
			foreach ($datos as $fila => $padre) {
				$this->categorias = $datos[$fila]['id'];
				$query = "SELECT id FROM categorias WHERE padre = ".$datos[$fila]['id'];
				$this->categorias = $base->Select($query);
			}
			return $this->categorias;
		}else{
			return null;
		}
	}

	/**
	* METODO RECURSIVO PARA OBTENER LOS HIJOS
	* @param $padre -> id del padre
	*/
	private function getHijos($padre){
		$base = new Database();
		$query = "SELECT * FROM categorias WHERE padre = ".$padre;
		$hijos = $base->Select($query);
		
		if(is_array($hijos)){
			foreach ($hijos as $fila => $valor) {
				$this->categorias = $hijos[$fila]['id'];
				$this->categorias = $this->getHijos($hijos[$fila]['id']);
			}
		}else{
			return;
		}
	}

	/**
	* HELPER
	*/
	public function MostrarArray(){
		echo '<pre>';
		print_r($this->registros);
		echo '</pre>';
	}

	/**
	* OBTIENE EL NOMBRE DE LA NORMA DE LA CATEGORIA
	* @param $categoria -> id de la categoria
	* @return $norma -> nombre de la norma
	*/
	public function getNorma($categoria){
		$base = new Database();
		$query = "SELECT * FROM normas WHERE categoria = ".$categoria;

		$norma = $base->Select($query);

		if(!empty($norma)){
			return $norma[0]['nombre'];
		}else{
			return null;
		}
	}

	/**
	* OBTIENE LOS DATOS DE LA OBSERVACION
	* @param $id -> id de la observacion
	* @return $datos[][] -> datos de la observacion
	* @return false si falla
	*/
	public function getObservacion($id){
		$base = new Database();
		$query = "SELECT * FROM observaciones WHERE id = ".$id;
		
		$datos = $base->Select($query);
		
		if(!empty($datos)){
			return $datos;
		}else{
			return null;
		}
	}


	/**
	* COMPONE LAS NORMAS DE UNA CATAGORIA
	* @param $categoria -> id de la categoria
	* @return $datos[][] -> datos de las normas
	*/
	public function getDatosNorma($categoria){
		$base = new Database();
		$query = "SELECT * FROM datos WHERE categoria = ".$categoria;

		$datos = $base->Select($query);
		
		if(!empty($datos)){
			return $datos;
		}else{
			return false;
		}
	}

	/**
	* OBTIENE LOS ARCHIVOS ADJUNTO DE UN REGISTRO
	* @param $proyecto -> id proyecto
	* @param $categoria -> id de la categoria
	*/
	public function getArchivos($proyecto, $categoria){
		$base = new Database();
		$query = "SELECT * FROM archivos WHERE proyecto = ".$proyecto." AND categoria = ".$categoria;

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos;
		}else{
			return false;
		}
	}

	/**
	* OBTIENE EL NOMBRE DE UN CAMPO
	* @param $campo -> id del campo
	* @return $campo -> nombre del campo
	* @return false si fallas
	*/
	public function getCampo($campo){
		$base = new Database();
		$query = "SELECT * FROM campos WHERE id = ".$campo;

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos[0]['nombre'];
		}else{
			return false;
		}
	}

	/**
	* OBTIENE DATOS DE UN HIJO
	* @param $hijos[][]
	*/
	public function Hijos($padre){
		$base = new Database();
		$query = "SELECT * FROM categorias WHERE padre = ".$padre;

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos;
		}else{
			return false;
		}
	}

	/**
	* OBTIENE LOS ID DE LOS HIJOS DE UN PADRE
	* @param $hijos[]
	*/
	public function HijosId($padre){
		$hijos = array();
		$base = new Database();
		$query = "SELECT * FROM categorias WHERE padre = ".$padre;

		$datos = $base->Select($query);

		if(!empty($datos)){
			foreach ($datos as $fila => $c) {
				$hijos = $datos[$fila]['id'];
			}
			return $hijos;
		}else{
			return false;
		}
	}
}
/*
$registros = new Registros();
$registros->getRegistros(51);
$registros->MostrarArray();
*/
?>