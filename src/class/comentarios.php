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
	* OBTIENE EL COPMENTARIO
	* @param int $id -> id del comentario
	* @return array $datos -> datos del comentario
	* @return boolean false si falla
	*/
	public function getComentario($id){
		$base = new Database();

		$id = mysql_real_escape_string($id);

		$query = "SELECT * FROM comentarios WHERE id = '".$id."'";

		$datos = $base->Select($query);
		if( !empty($datos) ){
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
	* @return int id -> id del nuevo comentario creado
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
			$id = $base->getUltimoId();
			
			if( isset($id) ){
				return $id;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	* OBTIENE EL TOTAL DE COMENTARIOS NUEVOS/SIN LEER
	* @param int $proyecto -> id del proyecto
	* @param int $categoria -> id de la categoria
	* @param int $articulo -> id del articulo
	* @return int -> el total de comentarios nuevos/sin leer
	*/
	public function getTotalComentarios( $proyecto, $categoria, $articulo ){
		$base = new Database();

		$proyecto = mysql_real_escape_string($proyecto);
		$categoria = mysql_real_escape_string($categoria);
		$articulo = mysql_real_escape_string($articulo);

		$query = "SELECT COUNT(id) AS total FROM comentarios WHERE proyecto = '".$proyecto."' AND categoria = '".$categoria."' AND articulo = '".$articulo."' ";

		if( $datos = $base->Select($query) ){
			return $datos[0]['total'];
		}else{
			return false;
		}

	}

	/**
	* DETERMINA SI UN ARTICULO TIENE COMENTARIOS SIN LEER
	* @param int $proyecto -> id del proyecto
	* @param int $categoria -> id de la categoria
	* @param int $articulo -> id del articulo
	* @return booelan true -> tiene comentarios sin leer
	*/
	public function comentariosSinLeer( $proyecto, $categoria, $articulo ){
		$base = new Database();

		$proyecto = mysql_real_escape_string($proyecto);
		$categoria = mysql_real_escape_string($categoria);
		$articulo = mysql_real_escape_string($articulo);

		$query = "SELECT COUNT(id) AS total FROM comentarios WHERE proyecto = '".$proyecto."' AND categoria = '".$categoria."'  AND  articulo = '".$articulo."' AND  leido = 0 ";

		if( $datos = $base->Select($query) ){
			if( !empty($datos ) ){
				return true;
			}
			return true;
		}else{
			return false;
		}
	}
}

?>