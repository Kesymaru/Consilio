<?php
/**
 * CLASE PARA LA ADMINISTRACIONDE LOS COMENTARIOS
 */
require_once('session.php');
require_once('classDatabase.php');

class Comentarios{
	
	public function __contruct(){
		$session = new Session();
		$session->logueado();
	}

	/**
	 * OBTIENE TODOS LOS COMENTARIOS
	 * @return $datos -> arrya[][]
	 */
	public function getComentarios(){
		$base = new Database();
		//$query = "SELECT * FROM comentarios ORDER BY fecha_creacion DESC";
		$query = "SELECT id, leido, usuario, fecha_creacion, proyecto, COUNT(*) FROM comentarios ";
		$query .= " GROUP BY proyecto ORDER BY fecha_creacion DESC";

		$datos = $base->Select($query);
		return $datos;
	}

	/**
	 * OBTIENE LA INFO DE UN COMENTARIO
	 * @param  $proyecto -> id del proyecto del comentario
	 * @return $datos -> array[][]
	 */
	public function getComentario( $proyecto ){
		$base = new Database();
		
		$proyecto = mysql_real_escape_string($proyecto);

		$query = "SELECT * FROM comentarios WHERE proyecto = '".$proyecto."' GROUP BY articulo ORDER BY articulo DESC";

		$datos = $base->Select($query);
		return $datos;
	}

	/**
	 * OBTIENE TODOS LOS COMENTARIO DE UN ARTICULO
	 */
	public function getComentariosArticulo($proyecto, $articulo){
		$base = new Database();

		$proyecto = mysql_real_escape_string($proyecto);
		$articulos = mysql_real_escape_string($articulo);

		$query = "SELECT * FROM comentarios WHERE proyecto = '".$proyecto."' AND articulo = '".$articulo."' ORDER BY fecha_creacion";

		$datos = $base->Select($query);

		return $datos;
	}

	/**
	*MARCA UN COMENTARIO COMO LEIDO
	* @param $id -> id comentario
	*/
	function ComentarioLeido($id){
		$base = new Database();
		
		$id = mysql_real_escape_string($id);

		$query = "UPDATE comentarios SET leido = 1 WHERE id = '".$id."'";

		if( $base->Update($query) ){
			return true;
		}else{
			return false;
		}
	}
}
?>