<?php
/**
* CLASE PARA MANEJAR LOS DATOS DE LOS USUARIOS
*/
require_once('session.php');
require_once('classDatabase.php');

/**
* PARA MANEJAR LOS CLIENTES
*/
class Cliente{
	private $session = '';

	/**
	* ASEGURA QUE SOLO SI EL USUARIO ESTA LOGUEADO PUEDA USAR EL SCRIPT
	*/
	public function __construct(){
		$this->session = new Session();
	}

	/**
	* OBTENER UN DATO DE UN CLIENTE
	* @param $dato -> dato requerido
	* @param $id -> id del cliente
	* @return $dato
	*/
	public function getClienteDato($dato, $id){
		$base = new Database();
		$datos = $base->Select("SELECT ".$dato." FROM clientes WHERE id = '".$id."'");
		return $datos[0][$dato];
	}

}

/**
* PARA LOS ADMINS
*/
class Admin{
	private $session = '';

	/**
	* ASEGURA QUE SOLO SI EL USUARIO ESTA LOGUEADO PUEDA USAR EL SCRIPT
	*/
	public function __construct(){
		$this->session = new Session();
	}

/*** METODOS PARA DATOS DE ADMIN ***/

	/**
	* METODO PARA OBTENER DATOS DE ADMIN
	* @param $dato -> dato a obtener
	* @return dato consultado
	*/
	public function getAdminDato($dato){
		$base = new Database();
		$datos = $base->Select("SELECT ".$dato." FROM admin WHERE id = '".$_SESSION['id']."'");
		return $datos[0][$dato];
	}

	/**
	* METODO PARA ACTUALIZAR UN DATO DEL USUARIO
	* @param $dato -> dato ha actualizar
	* @param $nuevo -> el nuevo dato
	*/
	public function setAdminDato($dato, $nuevo){
		$base = new Database();
		$query = "UPDATE admin SET ".$dato." = '".$nuevo."' WHERE id = ".$_SESSION['id'];
		$base->Update($query);

		//actualiza datos en session
		$this->session->Update("admin");
	}

	/**
	* METODO PARA CAMBIAR EL PASSWORD DEL ADMIN
	* @param $password -> password sin encriptar
	*/
	public function setAdminPassword($password){
		$base = new Database();
		$password = $base->Encriptar($password);

		$query = "UPDATE admin SET password = '".$password."' WHERE id = ".$_SESSION['id'];
		$base->Update($query);
	}

	/**
	* METODO PARA ACTUALIZAR LA IMAGEN DEL ADMIN
	* @param $link -> link de la imagen
	*/
	public function setAdminImagen($link){
		$base = new Database();

		//link viejo
		$imagenOld = $this->getAdminDato('imagen');

		$query = "UPDATE admin SET imagen = '".$link."' WHERE id = ".$_SESSION['id'];
		
		if($base->Update($query)){
			$base->DeleteImagen($imagenOld);
			return true;
		}else{
			return false;
		}
	}

}

?>