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

	/**
	* ASEGURA QUE SOLO SI EL USUARIO ESTA LOGUEADO PUEDA USAR EL SCRIPT
	*/
	public function __construct(){
		
		//revisa que este logueado
		$session = new Session();
		$session->Logueado();

        date_default_timezone_set('America/Costa_Rica');
	}

	/**
	* OBTENER UN DATO DE UN CLIENTE
	* @param $dato -> dato requerido
	* @param $id -> id del cliente
	* @return $dato
	*/
	public function getClienteDato($dato, $id){
		$base = new Database();

        $dato = mysql_real_escape_string($dato);
        $id = mysql_real_escape_string($id);

        $query = "SELECT ".$dato." FROM clientes WHERE id = '".$id."'";

		if( $datos = $base->Select($query) ){
            return $datos[0][$dato];
        }

        return false;
	}

	/**
	* OBTIENE TODOS LOS DATOS DE UN CLIENTE
	* @param $id -> id del cliente
	*/
	function getDatosCliente($id){
		$base = new Database();

        $id = mysql_real_escape_string($id);

		$query = "SELECT * FROM clientes WHERE id = '".$id."' ";

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

        }
        return false;
    }

    /**
     * DETERMINA SI UN RESPONSABLE DE UN CLIENTE EXISTE
     * @param $id -> id del responsable
     */
    public function ExisteResponsable($id){
        $base = new Database();

        $id = mysql_real_escape_string( $id );
        $cliente = mysql_real_escape_string( $_SESSION['cliente_id'] );

        $query = "SELECT * FROM clientes_responsables WHERE cliente = '".$cliente."' AND id = '".$id."' ";

        if( $base->Existe($query) ){
            return true;
        }
        return false;
    }

    /**
     * @param $nombre
     * @param $email
     * @return bool|int
     */
    public function NuevoResponsable($nombre, $email){
        $base = new Database();

        $nombre = mysql_real_escape_string( $nombre );
        $fecha_creacion = date("Y-m-d H:i:s");

        $query = "INSERT INTO clientes_responsables (nombre, email, fecha_creacion) values ('".$nombre."', '".$email."', '".$fecha_creacion."') ";

        if( $base->Insert($query)){
            return $base->getUltimoId();
        }
        return false;
    }

}
