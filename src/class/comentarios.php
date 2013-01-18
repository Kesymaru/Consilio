<?php

/**
* CLASE PARA EL MANEJO DE COMENTARIOS
*/
require_once("classDatabase.php");
require_once("session.php");

class Comentarios{

	public function __construct(){
		//seguridad esta logueado
		$session = new Session();
		$session->Logueado();
	}

	/**
	* OBTIENE LOS COMENTARIOS DE UNA ARTICULO 
	* @param $proyecto
	* @param $categoria
	* @param $articulo
	* @return $datos[][]
	*/
	public function getComentarios($proyecto, $categoria, $articulo){
		$base = new Database();
		$query = "SELECT * FROM comentarios WHERE proyecto = '".$proyecto."' AND categoria = '".$categoria."' AND articulo = '".$articulo."'";

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
	* @param $categoria
	* @param $articulo
	* @param $comentario
	* @param $usuario
	* @return tru si se crea
	*/
	public function newComentario($proyecto, $categoria, $articulo, $comentario, $usuario){
		$base = new Database();
		$comentario = base64_encode($comentario);

		$proyecto = mysql_real_escape_string($proyecto);
		$articulo = mysql_real_escape_string($articulo);

		$query = "INSERT INTO comentarios ( comentario, proyecto, categoria, articulo, usuario, tipo ) ";
		$query .= " VALUES ( '".$comentario."', '".$proyecto."', '".$categoria."', '".$articulo."', '".$usuario."', 0 )";

		if($base->Insert($query)){
			return true;
		}else{
			return false;
		}
	}
}

?>