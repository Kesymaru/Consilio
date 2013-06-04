<?php
/**
 * User: Andrey
 * Date: 5/22/13
 * Time: 12:48 PM
 * CLASE PARA LOS PERMISOS DE LOS CLIENTES
 */

require_once('classDatabase.php');
require_once('usuarios.php');

class Permisos{

    public function __construct(){
        date_default_timezone_set('America/Costa_Rica');
    }

    /*********************************** PERMISOS *******************************/

    /**
     * OBTIENE LOS DATOS DE LOS CLIENTES CON PERMISOS
     * @return bool|array -> false si falla/array con datos
     */
    public function ClientesPermisos(){
        $base = new Database();
        $cliente = new Cliente();

        $query = "SELECT COUNT(permisos.id) AS permisos, clientes.nombre, clientes.imagen, clientes.pais, clientes.id FROM permisos, clientes WHERE permisos.cliente = clientes.id";

        if( $permisos = $base->Select($query) ){
            return $permisos;
        }else{
            return false;
        }

    }

    /**
     * OBTIENE TODOS LOS PERMISOS DE UN CLIENTE
     * @param $cliente
     * @return bool|array
     */
    public function getPermisos( $cliente ){
        $base = new Database();

        $cliente = mysql_real_escape_string($cliente);

        $query = "SELECT * FROM permisos WHERE cliente = '".$cliente."' ";

        if( $datos = $base->Select($query) ){
            return $datos;
        }else{
            return false;
        }

    }

    /************************************ AREAS DE APLIACION ********************/

    /**
     * OBTIENE LAS AREAS DE APLIACION
     */
    public function getAreasAplicacion(){
        $base = new Database();

        $query = "SELECT * FROM areas_aplicacion  ";

        if( $datos = $base->Select( $query ) ){
            if( !empty($datos) ){
                return $datos;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * OBTIENE INFORMACION DE UNA AREA DE APLICACION
     * @param int $id -> id del area
     */
    public function getArea( $id ){
        $base = new Database();

        $id = mysql_real_escape_string($id);

        $query = "SELECT * FROM areas_aplicacion WHERE id = '".$id."' ";

        if( $datos = $base->Select($query) ){
            return $datos;
        }else{
            return false;
        }
    }

    /**
     * REGISTRA UNA NUEVA AREA DE APLIACACION
     * @param $nombre
     * @param $descripcion
     * @return bool si se registro
     */
    public function RegistrarAreaAplicacion( $nombre, $descripcion ){
        $base = new Database();

        $nombre = mysql_real_escape_string( $nombre );
        $descripcion = mysql_real_escape_string( $descripcion );

        $fecha = date("Y-m-d H:i:s");

        $query = "INSERT INTO areas_aplicacion (nombre, descripcion, fecha_creacion, fecha_actualizacion) VALUES ('".$nombre."', '".$descripcion."', '".$fecha."', '".$fecha."' ) ";

        if( $base->Insert($query) ){

            if( $id = $base->getUltimoId() ){
                return $id;
            }
            return false;

        }else{
            return false;
        }

    }

    /**
     * ELIMINA UNA AREA DE APLICACION
     * @param int $id -> id del area ha eliminar
     * @return bool -> si se llevo a cabo true
     */
    public function EliminarArea( $id ){
        $base = new Database();

        $id = mysql_real_escape_string( $id );

        $query = "DELETE FROM areas_aplicacion WHERE id = '".$id."' ";

        if( $base->Delete($query)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * ACTUALIZA UNA AREA DE APLICACION
     * @param $id
     * @param $nombre
     * @param $descripcion
     * @return bool
     */
    public function UpdateArea($id, $nombre, $descripcion ){
        $base  = new Database();

        $id = mysql_real_escape_string($id);
        $nombre = mysql_real_escape_string( $nombre );
        $descripcion = mysql_real_escape_string( $descripcion );

        $query = "UPDATE areas_aplicacion SET nombre = '".$nombre."', descripcion = '".$descripcion."' WHERE id = '".$id."' ";

        if( $base->Update($query) ){
            return true;
        }else{
            return false;
        }

    }

}
