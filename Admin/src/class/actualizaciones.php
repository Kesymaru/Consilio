<?php
/**
 * CLASE PARA EL MANEJO DE LAS ACTUALIZACIONES
 */

require_once("classDatabase.php");

class Actualizaciones{

	private $base = "";

	public function __construct(){

		$this->base = new Database();
	}

	/**
	 * OBTIENE LOS PROYECTOS CON ACTUALIZACIONES HABITADA
	 * @return boolean|array
	 */
	public function getProyectos(){

		$query = "SELECT
				    *
				  FROM
				    proyectos
				  WHERE
				  actualizaciones = 1 ";

		if( $proyectos = $this->base->Select($query) ){
			return $proyectos;
		}

		return false;
	}

	/**
	 * OBTIENE LOS DATOS DE UNA ACTUALIZACION DE UN PROYECTO
	 * @param $proyecto
	 * @return booelan|array
	 */
	public function getActualizacionesProyecto( $proyecto ){

		$proyecto = mysql_real_escape_string($proyecto);

		$query = "SELECT
					*
				  FROM
				    actualizaciones
				  WHERE
				    proyecto = '$proyecto'";

		if( $datos = $this->base->Select($query) ){
			return $datos;
		}

		return false;
	}

	/**
	 * OBTIENE DATOS DE UNA ACTUALIZACION
	 * @param int $id
	 * @return boolean
	 */
	public function  getActualizacion( $id ){
		$id = mysql_real_escape_string($id);

		$query = "SELECT
					*
				  FROM
				    actualizaciones
				  WHERE
				    id = '$id' ";

		if( $datos = $this->base->Select($query) ){
			return $datos;
		}
		return false;
	}

	/**
	 * ACTUALIZA UNA ACTUALIZACION
	 * @param int $id
	 * @param string $nombre
	 * @param string $fecha date
	 * @param string $notas
	 * @param file $files
	 * @return boolean
	 */
	public function updateActualizacion( $id, $nombre, $fecha, $notas, $files = '' ){
		$id = mysql_real_escape_string($id);
		$nombre = mysql_real_escape_string($nombre);
		$fecha = mysql_real_escape_string($fecha);
		$notas = mysql_real_escape_string($notas);

		$query = "UPDATE
					actualizaciones
				  SET
				    nombre = '$nombre',
				    fecha = '$fecha',
				    notas = '$notas'
				  WHERE
				    id = '$id' ";

		if( $this->base->Update($query) ){

			if( $files != '' ){
				if( $this.uploadActualizacionFiles( $id, $files ) ){
					return true;
				}
			}else{
				return true;
			}
		}

		return false;
	}

	/**
	 * SUBE ARCHIVOS DE UNA ACTUALIZACION
	 * @param int $id actualizacion id
	 * @param file $files
	 * @return boolean
	 */
	private function uploadActualizacionFiles( $id, $files ){


		return false;
	}
	/**
	 * ELIMINA UNA ACTUALIZACION
	 * @param int $id
	 * @return booelean
	 */
	public function deleteActualizacion( $id ){
		$id = mysql_real_escape_string($id);

		$query = "DELETE
				  FROM
				    actualizaciones
				  WHERE id = '$id' ";

		if( $this->base->Delete($query) ){
			return true;
		}

		return false;
	}

}