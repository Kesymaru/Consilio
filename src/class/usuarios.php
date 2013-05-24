<?php
/**
* CLASE PARA MANEJAR LOS DATOS DE LOS USUARIOS
*/
require_once('session.php');
require_once('classDatabase.php');
require_once("imageUpload.php");

/**
* PARA MANEJAR LOS CLIENTES
*/
class Cliente{

	/**
	* ASEGURA QUE SOLO SI EL USUARIO ESTA LOGUEADO PUEDA USAR EL SCRIPT
	*/
	public function __construct(){
		
		//revisa que este logueado
		$session = new Session();
		$session->Logueado();

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



	/**
	* OBTIENE TODOS LOS DATOS DE UN CLIENTE
	* @param $id -> id del cliente
	*/
	function getDatosCliente($id){
		$base = new Database();
		$query = "SELECT * FROM clientes WHERE id = ".$id;

		$datos = $base->Select($query);
		
		if(!empty($datos)){
			return $datos;
		}else{
			return false;
		}
	}

    /**
     * OBTIENE LOS RESPONSABLES ASOCIADOS AL CLIENTE
     * @return bool|array
     */
    public function getResponsables(){
        $base = new Database();

        $cliente = $_SESSION['cliente_id'];

        $query = "SELECT * FROM clientes_responsables WHERE cliente = '".$cliente."' ";

        if( $responsables = $base->Select($query) ){
            if( !empty($responsables) ){
                return $responsables;
            }else{
                return false;
            }
        }else{
            return false;
        }

    }

}

?>