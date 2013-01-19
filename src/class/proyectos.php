<?php

/**
* CLASE PARA PROYECTOS DE CLIENTES
*/

require_once("classDatabase.php");
require_once("usuarios.php"); 
require_once("session.php");

class Proyectos{

	public function __construct(){
		//SEGURIDAD LOGUEADO
		$session = new Session();
		$session->Logueado();
	}

	/**
	* OBTIENE LOS PROYECTOS DE UN USUARIO
	* @return $datos -> array[][] con los datos de los proyectos
	*/
	public function getProyectos($usuario){
		$base = new Database();
		$query = "SELECT * FROM proyectos WHERE cliente = '".$usuario."' AND visible = '1' ORDER BY nombre";

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos;
		}else{
			return false;
		}
	}

	
	/**
	* OBTIEN LOS DATOS DE UN PROYECTO
	* @param $id -> id del proyecto
	* @return $datos -> array[][] datos del proyecto
	*/
	public function getProyectoDatos($id){
		$base = new Database();
		$query = "SELECT * FROM proyectos WHERE id = '".$id."'";

		$datos = $base->Select($query);

		if(!empty($datos)){
			 return $datos;
		}else{
			return false;
		}
	}

	/**
	* OBTIENE UN DATO DE UN PROYECTO
	* @para $id -> id del proyecto
	* @param $dato -> dato solicitado
	*/
	public function getProyectoDato($id, $dato){
		$base = new Database();
		$query = "SELECT * FROM proyectos WHERE id = '".$id."'";

		$datos = $base->Select($query);

		if(!empty($datos)){
			 return $datos[0][$dato];
		}else{
			return false;
		}
	}
}

?>