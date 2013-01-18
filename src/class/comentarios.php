<?php

/**
* CLASE PARA EL MANEJO DE COMENTARIOS
*/
require_once("classDatabase.php");
require_once("session.php");

class Comentarios{

	public function __construct(){
		$session = new Session();
		$session->Logueado();
	}

	public function getComentarios($proyecto, $articulo){
		$base = new Database();
		$query = "SELECT * FROM comentarios WHERE proyecto = '".$proyecto."' AND articulo = '".$articulo."'";

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos;
		}else{
			return false;
		}
	}

	/**
	* CREA UN COMENTARIO NUEVO
	* @param $proyecto
	* @param $articulo
	* @param $comentario
	* @param $usuario
	* @return tru si se crea
	*/
	public function newComentario($proyecto, $articulo, $comentario, $usuario){
		$base = new Database();
		$comentario = base64_encode($comentario);

		$proyecto = mysql_real_escape_string($proyecto);
		$articulo = mysql_real_escape_string($articulo);

		$query = "INSERT INTO comentarios ( comentario, proyecto, articulo, usuario, tipo ) ";
		$query .= " VALUES ( '".$comentario."', '".$proyecto."', '".$articulo."', '".$usuario."', 0 )";
		
		if($base->Insert($query)){
			return true;
		}else{
			return false;
		}
	}
}

?>