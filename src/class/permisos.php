<?php
/*
 * CLASE PARA METODOS DE LOS PERMISOS DEL CLIENTE
 */

require_once('session.php');
require_once('classDatabase.php');
require_once('usuarios.php');

class Permisos {

    private $extensiones = array('gif', 'jpg', 'jpeg', 'png', 'zip', 'rar', 'pdf', 'txt', 'xls', 'xlsx', 'ods', 'docx', 'doc', 'odt', 'rtf', 'pptx', 'ppt', 'pptm');

    public function __construct(){
        $session = new Session();
        $session->Logueado();

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
            
            if( $i <= 9 ){
                $fecha_minima = $year.'-0'.$i.'-01';
                $fecha_maxima = $year.'-0'.$i.'-31';
            }else{
                $fecha_minima = $year.'-'.$i.'-01';
                $fecha_maxima = $year.'-'.$i.'-31';    
            }

            $query = "SELECT COUNT(id) AS total FROM permisos WHERE cliente = '".$cliente."' AND fecha_expiracion >= '".$fecha_minima."' AND fecha_expiracion <= '".$fecha_maxima."' ";

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
     * CREA UN NUEVO PERMISO
     * @param string $nombre
     * @param date $fecha_emision
     * @param date $fecha_expiracion
     * @param int $recordatorio
     * @param int $tipo_recordatorio
     * @param string $email
     * @param array|string $areas
     * @param string $observacion
     * @param array|string $responsables
     */
    public function NuevoPermiso( $nombre, $fecha_emision, $fecha_expiracion, $recordatorio, $tipo_recordatorio, $email, $areas, $observacion, $responsables  ){
        $base = new Database();

        $nombre = mysql_real_escape_string( $nombre );
        $fecha_emision = mysql_real_escape_string( $fecha_emision );
        $fecha_expiracion = mysql_real_escape_string( $fecha_expiracion );
        $recordatorio = mysql_real_escape_string( $recordatorio );
        $tipo_recordatorio = mysql_real_escape_string( $tipo_recordatorio );
        $email = mysql_real_escape_string( $email );
        $observacion = mysql_real_escape_string( $observacion );
        $cliente = $_SESSION['cliente_id'];

        $fecha_creacion = date('Y-m-d G:i:s');

        echo $query = "INSERT INTO permisos (nombre, fecha_emision, fecha_expiracion, observacion, fecha_creacion, cliente ) VALUES ( '".$nombre."', '".$fecha_emision."', '".$fecha_expiracion."', '".$observacion."', '".$fecha_creacion."', '".$cliente."' ) ";

        if( $base->Insert($query) ){
            if( $id = $base->getUltimoId() ){

                //crea el recordatorio
                if( $this->NuevoRecordatorio($id, $recordatorio, $tipo_recordatorio) ){
                    return $id;
                }else{
                    return false;
                }

            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * CREA UN NUEVO RECORDATORIO PARA EL PERMISO
     * @param int $permiso -> id del permiso al que pertenece
     * @param int $tiempo -> unidad de tiempo para el recordatorio
     */
    private function NuevoRecordatorio( $permiso, $tiempo, $tipo ){
        $base = new Database();

        if( 0 <= $tipo && $tipo <= 2 ){
            $fecha_creacion = date('Y-m-d G:i:s');

            $query = "INSERT INTO permisos_recordatorios ( tiempo, tipo, permiso, fecha_creacion ) VALUES ('".$tiempo."', '".$tipo."', '".$permiso."', '".$fecha_creacion."' ) ";

            if( $base->Insert($query) ){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * SUBE UN ARCHIVO DE UN PERMISO
     * @param $archivo
     * @param $permiso
     * @return array $error
     */
    public function UploadFiles($archivos, $permiso){
        $error = array();

        foreach( $archivos as $f => $archivo ){

            if( !empty($archivo['tmp_name']) ){

                $upload = new Upload();
                $upload->SetFileName($archivo['name']);
                $upload->SetTempName($archivo['tmp_name']);
                $upload->SetValidExtensions( $this->extensiones );

                $upload->SetUploadDirectory("../Admin/permisos_archivos/");

                $upload->SetMaximumFileSize(90000000);

                if($upload->UploadFile()){
                    $link = $upload->GetUploadDirectory().$upload->GetFileName();

                    $link = str_replace("../", "", $link);

                    if( $this->RegistrarArchivoPermiso($link, $permiso)){
                        continue;
                    }else{
                        $base = new Database();
                        $link = '../'.$link;
                        $base->DeleteImagen($link);
                        echo "Error: no se pudo registrar el archivo: ".$archivo['name']."<br/>";
                    }
                }else{
                    echo "Error: no se pudo subir el archivo: ".$archivo['name']."<br/>";
                }
            }else{
                echo "Error: archivo vacio.";
            }
        }

        return true;

    }

    /**
     * REGISTRA EN LA BASE DE DATOS EL ARCHIVO SUBIDO
     * @param $link -> link del archivo
     * @param $permiso -> id del permiso
     * @return bool
     */
    private function RegistrarArchivoPermiso( $link, $permiso ){
        $base = new Database();

        $fecha_creacion = date('Y-m-d G:i:s');

        $query = "INSERT INTO permisos_archivos (link, permiso, fecha_creacion) VALUES ('".$link."', '".$permiso."', '".$fecha_creacion."') ";

        if( $base->Insert($query) ){
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