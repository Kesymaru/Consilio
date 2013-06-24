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
     * OBTIENE LOS CLIENTES QUE TIENE PROYECTOS CON PERMISOS
     * @return bool|array -> false si falla/array con datos
     */
    public function ClientesConPermisos(){
        $base = new Database();

        //selecciona los clientes con proyectos con permisos
        $query = "SELECT
                      clientes.nombre,
                      clientes.email,
                      clientes.id,
                      COUNT( DISTINCT proyectos.id) AS proyectos
                  FROM
                      clientes,
                      proyectos,
                      permisos
                  WHERE
                      proyectos.permisos = 1 AND
                      proyectos.cliente = clientes.id AND
                      permisos.proyecto = proyectos.id AND
                      permisos.cliente = clientes.id
                  GROUP BY(clientes.id)";

        if( $permisos = $base->Select($query) ){
            return $permisos;
        }
        return false;
    }

    /**
     * OBTIENE LOS PROYECTOS DE UN CLIENTE CON PERMISOS
     * @param $cliente id del cliente
     * @return boolean|array
     */
    public function ProyectosConPermisos($cliente){
        $base = new Database();

        //selecciona todos los proyectos de un cliente con permisos
        $query = "SELECT DISTINCT
                    proyectos.*,
                    COUNT( DISTINCT permisos.id ) AS permisos
                  FROM
                    proyectos,
                    permisos
                  WHERE
                      proyectos.permisos = 1 AND
                      proyectos.cliente = 6 AND
                      permisos.cliente = 6 AND
                      permisos.proyecto = proyectos.id
                  GROUP BY(proyectos.id)";

        if( $datos = $base->Select($query) ){
            return $datos;
        }
        return false;
    }

    /**
     * OBTIENE TODOS LOS PERMISOS DE UN CLIENTE
     * @param int $proyecto id del proyecto
     * @return bool|array
     */
    public function PermisosProyecto( $proyecto ){
        $base = new Database();

        $proyecto = mysql_real_escape_string($proyecto);

        $query = "SELECT *
                  FROM
                  permisos
                  WHERE
                  cliente = '".$cliente."' AND
                  proyecto = '".$proyecto."' ";

        if( $datos = $base->Select($query) ){
            return $datos;
        }
        return false;
    }

    /************************************ AREAS DE APLIACION ********************/

    /**
     * OBTIENE LAS AREAS DE APLIACION ORDENADAS POR EL NOMBRE
     */
    public function getAreasAplicacion(){
        $base = new Database();

        $query = "SELECT * FROM areas_aplicacion ORDER BY nombre";

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
     * @param string $nombre
     * @return bool|int -> el id de la nueva area
     */
    public function RegistrarAreaAplicacion( $nombre ){
        $base = new Database();

        $nombre = mysql_real_escape_string( $nombre );

        $fecha = date("Y-m-d H:i:s");

        $query = "INSERT INTO areas_aplicacion (nombre, fecha_creacion, fecha_actualizacion) VALUES ('".$nombre."', '".$fecha."', '".$fecha."' ) ";

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
     * @param int $id -> id del area de aplicacion
     * @param string $nombre
     * @return bool -> true si se actualiza
     */
    public function UpdateArea($id, $nombre ){
        $base  = new Database();

        $id = mysql_real_escape_string($id);
        $nombre = mysql_real_escape_string( $nombre );
        $fecha_actualizacion = date("Y-m-d H:i:s");

        $query = "UPDATE areas_aplicacion SET nombre = '".$nombre."', fecha_actualizacion = '".$fecha_actualizacion."' WHERE id = '".$id."' ";

        if( $base->Update($query) ){
            return true;
        }else{
            return false;
        }

    }

}
