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
	* @param int $proyecto -> id del proyecto
	* @param int $categoria -> id de la categoria
	* @param int $articulo -> id del articulo
	* @param string $comentario -> text/html del comentario
	* @param int $usuario -> id del usuario
	* @return boolean true -> si se crea
	* @return boolean false -> caso de error o fallo
	*/
	public function newComentario($proyecto, $categoria, $norma, $articulo, $comentario, $usuario){
		$base = new Database();
		$comentario = base64_encode($comentario);

		$proyecto = mysql_real_escape_string($proyecto);
		$categoria = mysql_real_escape_string($categoria);
		$norma = mysql_real_escape_string($norma);
		$articulo = mysql_real_escape_string($articulo);
		$usuario = mysql_real_escape_string($usuario);

		$query = "INSERT INTO comentarios ( comentario, proyecto, categoria, norma, articulo, usuario, tipo, fecha_creacion ) ";
		$query .= " VALUES ( '".$comentario."', '".$proyecto."', '".$categoria."', '".$norma."', '".$articulo."', '".$usuario."', 0, NOW() )";

		if($base->Insert($query)){
			return true;
		}else{
			return false;
		}
	}
}

?>