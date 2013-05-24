<?php
/*
 * CLASE PARA METODOS DE LOS PERMISOS DEL CLIENTE
 */

require_once('session.php');
require_once('classDatabase.php');
require_once('usuarios.php');

class Permisos {

    public function __construct(){
        date_default_timezone_set('America/Costa_Rica');
    }

    /**
     * OBTIENE EL NUMERO DE PERMISOS PARA CADA MES
     * @param int $year numero del a~o
     * @return array $contador con el total de permisos para cada mes
     */
    public function getCalendario($year){
        $base = new Database();
        $session = new Session();

        if( !$session->Logueado() ){
            return false;
        }

        $contador = array(0=>'0',1=>'0',2=>'0',3=>'0',4=>'0',5=>'0',6=>'0',7=>'0',8=>'0',9=>'0',10=>'0',11=>'0');

        $year = mysql_real_escape_string( $year );
        $cliente = mysql_real_escape_string( $_SESSION['cliente_id'] );

        for( $i = 01; $i <= 12; $i++ ){
            $fecha_minima = $year.'-'.$i.'-01';
            $fecha_maxima = $year.'-'.$i.'-31';

            $query = "SELECT COUNT(id) AS total FROM permisos WHERE cliente = '".$cliente."' AND fecha_expiracion >= '".$fecha_minima."' AND fecha_expiracion < '".$fecha_maxima."' ";

            if( $datos = $base->Select($query) ){
                if( !empty($datos) ){
                    $contador[$i-1] = $datos[0]['total'];
                }
            }
        }

        return $contador;
    }

    /**
     * OBTIENE LOS PERMISOS DE UN MES ESPECIFICO
     * @param int $year
     * @param int $month numero del mes
     * @return bool/array
     */
    public function getPermisos($year, $month ){
        $base = new Database();
        $session = new Session();

        if( !$session->Logueado() ){
            return false;
        }

        $year = mysql_real_escape_string( $year );
        $month = mysql_real_escape_string( $month );
        $month++;
        $cliente = mysql_real_escape_string( $_SESSION['cliente_id'] );

        //2013-05-01
        $fecha_minima = $year.'-'.$month.'-01';
        $fecha_maxima = $year.'-'.($month+1).'-01';

        $query = "SELECT * FROM permisos WHERE cliente = '".$cliente."' AND fecha_expiracion >= '".$fecha_minima."' AND fecha_expiracion < '".$fecha_maxima."' ";

        if( $datos = $base->Select($query) ){
            if( !empty( $datos ) ){
                return $datos;
            }
        }
        return false;
    }

    /**
     * OBTIENE LOS RESPONSABLES DE UN PERMISO
     * @param int $permiso -> id del permiso
     * @return array $responsables
     */
    public function getResponsables( $permiso ){
        $base = new Database();

        $responsable = array('Maria Julia Gonzalez','Andrey Alfaro Alvarado');

        return $responsables;
    }

    /**
     * OBTIENE EL NOMBRE DE LAS CATEGORIAS DE UN PERMISO
     * @param int $permiso -> id del permiso
     * @return array $categorias
     */
    public function getCategorias( $permiso ){
        $base = new Database();

        $categorias = array( 0 => 'Caldera', 120=>'Categoria' );

        return $categorias;
    }

    /**
     * CREA UN PERMISO NUEVO
     * @param $nombre
     * @param $fecha_expiracion
     * @param $fecha_emision
     * @param $observacion
     * @param $responsables
     * @param $categorias
     * @return bool
     */
    public function NuevoPermiso( $nombre, $fecha_expiracion, $fecha_emision, $observacion, $responsables, $categorias  ){
        $base = new Database();

        $nombre = mysql_real_escape_string( $nombre );
        $fecha_expiracion = mysql_real_escape_string( $fecha_expiracion );
        $fecha_emision = mysql_real_escape_string( $fecha_emision );
        $observacion = mysql_real_escape_string($observacion);
        $responsables = mysql_real_escape_string( $responsables );
        $categorias = mysql_real_escape_string( $categorias );

        $fecha_creacion = date('Y-m-d G:i:s');

        $query = "INSERT INTO permisos (nombre, fecha_expiracion, fecha_emision, observacion, fecha_creacion ) VALUES ('".$nombre."', '".$fecha_expiracion."', '".$fecha_emision."', '".$observacion."', '".$fecha_creacion."', ) ";

        if( $base->Insert($query)){
            return true;
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

}
?>