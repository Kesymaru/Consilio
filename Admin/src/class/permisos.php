<?php
/**
 * User: Andrey
 * Date: 5/22/13
 * Time: 12:48 PM
 * CLASE PARA LOS PERMISOS DE LOS CLIENTES
 */

require_once('classDatabase.php');

class Permisos{

    public function __construct(){
        date_default_timezone_set('America/Costa_Rica');
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
            return true;
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

}
