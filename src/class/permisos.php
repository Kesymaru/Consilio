<?php

error_reporting(E_ALL);

require_once('session.php');
require_once('classDatabase.php');
require_once('usuarios.php');

class Permisos {

    private $errors = array();
    private $extensiones = array('gif', 'jpg', 'jpeg', 'png', 'zip', 'rar', 'pdf', 'txt', 'xls', 'xlsx', 'ods', 'docx', 'doc', 'odt', 'rtf', 'pptx', 'ppt', 'pptm');
    private $months = array( "es" => array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"),
                             "in" => array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December") );

    public function __construct(){
        $session = new Session();

        //garantiza que este logueado
        if( !$session->Logueado() ){
            return false;
            exit();
        }

        date_default_timezone_set('America/Costa_Rica');
    }

    /**
     * OBTIENE EL NUMERO DE PERMISOS PARA CADA MES
     * @param int $proyecto -> id del proyecto
     * @param int $year numero del a~o
     * @return array $contador con el total de permisos para cada mes
     */
    public function getCalendario( $proyecto, $year){
        $base = new Database();
        $session = new Session();

        if( !$session->Logueado() ){
            return false;
        }
        $proyecto = mysql_real_escape_string($proyecto);

        $contador = array(0=>'0',1=>'0',2=>'0',3=>'0',4=>'0',5=>'0',6=>'0',7=>'0',8=>'0',9=>'0',10=>'0',11=>'0');
        $expirados =  array(0=>'0',1=>'0',2=>'0',3=>'0',4=>'0',5=>'0',6=>'0',7=>'0',8=>'0',9=>'0',10=>'0',11=>'0');

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

            $query = "SELECT COUNT(id) AS total FROM permisos WHERE cliente = '".$cliente."' AND proyecto = '".$proyecto."' AND fecha_expiracion >= '".$fecha_minima."' AND fecha_expiracion <= '".$fecha_maxima."' ";

            if( $datos = $base->Select($query) ){
                if( !empty($datos) ){
                    $contador[$i-1] = $datos[0]['total'];
                }

                $query = "SELECT * FROM permisos WHERE cliente = '".$cliente."' AND proyecto = '".$proyecto."' AND fecha_expiracion >= '".$fecha_minima."' AND fecha_expiracion <= '".$fecha_maxima."' ";

                if( $datosMeses = $base->Select($query) ){
                    foreach( $datosMeses as $f => $mes ){
                        $fecha = $this->FormatearFecha( $mes['fecha_expiracion'] );
                        if( $this->Expiro($fecha)){
                            $expirados[$i-1] = 1;
                        }
                    }
                }
            }
        }

        $total = array( "contador" => $contador, "expirados" => $expirados );

        return $total;
    }

    /**
     * OBTIENE TODOS LOS PERMISOS DE UN PROYECTO
     * @param $proyecto  -> id del proyecto
     * @return array|bool
     */
    public function getPermisos($proyecto){
        $base = new Database();

        $proyecto = mysql_real_escape_string($proyecto);

        $query = "SELECT * FROM permisos WHERE proyecto = '".$proyecto."' ORDER BY fecha_expiracion ";

        if( $datos = $base->Select($query) ){
            return $datos;
        }
        return false;
    }

    /**
     * OBTIENE LOS PERMISOS DE UN MES ESPECIFICO
     * @param int $proyecto
     * @param int $year
     * @param int $month numero del mes
     * @return bool/array
     */
    public function getPermisosMonth($proyecto, $year, $month ){
        $base = new Database();

        $proyecto = mysql_real_escape_string($proyecto);
        $year = mysql_real_escape_string( $year );
        $month = mysql_real_escape_string( $month );
        $month++;
        $cliente = mysql_real_escape_string( $_SESSION['cliente_id'] );

        $fecha_minima = $year.'-'.$month.'-01';
        $fecha_maxima = $year.'-'.($month+1).'-01';

        $query = "SELECT * FROM permisos WHERE cliente = '".$cliente."' AND proyecto = '".$proyecto."' AND fecha_expiracion >= '".$fecha_minima."' AND fecha_expiracion < '".$fecha_maxima."' ";

        if( $datos = $base->Select($query) ){
            return $datos;
        }
        return false;
    }

    /**
     * OBTIEN LOS DATOS DE UN PERMISO
     * @param $id -> id del permiso
     * @return array|bool
     */
    public function getPermiso( $id ){
        $base = new Database();

        $id = mysql_real_escape_string($id);

        $query = "SELECT * FROM permisos WHERE id = '".$id."' ";

        if( $datos = $base->Select($query) ){
            $query = "SELECT * FROM permisos_responsables WHERE permiso = '".$id."' ";

            $datos['responsables'] =$base->Select($query);

            $query = "SELECT * FROM permisos_recordatorios WHERE permiso = '".$id."' ";
            $datos['recordatorios'] = $base->Select($query);

            $query = "SELECT * FROM permisos_recordatorios_emails WHERE permiso = '".$id."' ";
            $datos['emails'] = $base->Select($query);


            return $datos;
        }
        return false;
    }

    /**
     * OBTIENE LOS RESPONSABLES DE UN PERMISO
     * @param int $id -> id del permiso
     * @return array
     */
    public function getResponsables( $id ){
        $base = new Database();

        $id = mysql_real_escape_string($id);
        $cliente = mysql_real_escape_string($_SESSION['cliente_id']);

        $query = "SELECT clientes_responsables.nombre, clientes_responsables.apellidos, clientes_responsables.email, clientes_responsables.id AS id, permisos_responsables.permiso, permisos_responsables.id AS permisos_responsables_id
FROM clientes_responsables, permisos_responsables
WHERE clientes_responsables.cliente = '".$cliente."'
AND permisos_responsables.permiso = '".$id."'
AND permisos_responsables.responsable = clientes_responsables.id";

        if( $responsables = $base->Select($query) ){
            return $responsables;
        }
        return false;
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
     * @param string $proyecto -> id del proyecto
     * @param string $nombre
     * @param date $fecha_emision
     * @param date $fecha_expiracion
     * @param int $recordatorio
     * @param string $emails
     * @param array|string $areas
     * @param string $observacion
     * @param array|string $responsables
     */
    public function NuevoPermiso( $proyecto, $nombre, $fecha_emision, $fecha_expiracion, $recordatorio, $emails, $areas, $observacion, $responsables ){
        $base = new Database();

        $proyecto = mysql_real_escape_string($proyecto);
        $nombre = mysql_real_escape_string( $nombre );
        $fecha_emision = mysql_real_escape_string( $fecha_emision );
        $fecha_expiracion = mysql_real_escape_string( $fecha_expiracion );
        $recordatorio = mysql_real_escape_string( $recordatorio );
        $emails = mysql_real_escape_string( $emails );
        $observacion = mysql_real_escape_string( $observacion );
        $cliente = $_SESSION['cliente_id'];

        //invierte las fechas con formato dd/mm/yyyy -> yyyy-mm-dd
        $fecha_emision = $this->FormatearFecha( $fecha_emision );

        $fecha_expiracion = $this->FormatearFecha( $fecha_expiracion );

        $recordatorio = $this->FormatearFecha( $recordatorio );

        $fecha_creacion = date('Y-m-d G:i:s');

        $query = "INSERT INTO permisos (proyecto, nombre, fecha_emision, fecha_expiracion, observacion, fecha_creacion, cliente ) VALUES ( '".$proyecto."', '".$nombre."', '".$fecha_emision."', '".$fecha_expiracion."', '".$observacion."', '".$fecha_creacion."', '".$cliente."' ) ";

        if( $base->Insert($query) ){
            if( $id = $base->getUltimoId() ){

                //registra las areas
                $this->AreasApliccionPermiso($id, $areas);

                echo 'crea permisos<br/>';
                //registra los responsables
                $this->PermisoResponsables($id, $responsables);

                //crea el recordatorio
                if( $this->NuevoRecordatorio($id, $recordatorio, $emails) ){
                    return $id;
                }
            }else{
                echo "Error: no se pudo obtener el id del nuevo permiso.";
                return false;
            }
        }
        return false;
    }

    /**
     * @param $fecha -> fecha en formato dd/mm/yyyy
     * @return date -> fecha formateada yyyy-mm-dd
     */
    public function FormatearFecha( $fecha ){
        $fecha = str_replace('/','-',$fecha);
        return date( 'Y-m-d', strtotime($fecha) );
    }

    /**
     * DESFORMATEA UN FECHA
     * @param $fecha -> fecha en formato yyyy-mm-dd
     * @return date -> fecha en formato dd/mm/yyyy
     */
    public function DesFormatearFecha( $fecha ){
        /*$fecha = date( 'd-m-Y', strtotime($fecha) );
        return str_replace('-','/',$fecha);*/
        return date('d/m/Y', strtotime($fecha));
    }

    /**
     * CREA UN NUEVO RECORDATORIO PARA EL PERMISO
     * @param int $permiso -> id del permiso al que pertenece
     * @param string $fecha_inicio -> fecha de inicio del recordatorio dd/mm/yy
     * @param string $emails -> emails
     * @return boole
     */
    private function NuevoRecordatorio( $permiso, $fecha_inicio, $emails ){
        $base = new Database();

        //invierte la fecha
        $fecha_inicio = date( 'Y-m-d', strtotime($fecha_inicio) );

        $fecha_creacion = date('Y-m-d G:i:s');

        $query = "INSERT INTO permisos_recordatorios ( fecha_inicio, permiso, fecha_creacion ) VALUES ('".$fecha_inicio."', '".$permiso."', '".$fecha_creacion."' ) ";

        if( $base->Insert($query) ){

            $this->EmailsRecordatorio( $permiso, $emails );

            return true;
        }

        return false;
    }

    /**
     * REGISTRA LOS CORREOS PARA UN RECORDATORIO
     * @param int $permiso -> id del permiso del recordatorio
     * @param string $datos -> texto separado por comas con los valores
     */
    private function EmailsRecordatorio( $permiso, $datos ){
        $base = new Database();

        $emails = explode(",", $datos);

        //tiene mas de un email
        if( is_array( $emails) ){

            foreach($emails as $f => $email ){
                $email = mysql_real_escape_string($email);

                $query = "INSERT INTO permisos_recordatorios_emails (email, permiso) VALUE ('".$email."','".$permiso."')";

                $base->Insert( $query );
            }
        }else{
            $email  = mysql_real_escape_string($datos);

            $query = "INTER INTO permisos_recordatorios_emails (email, permiso) VALUE ('".$email."','".$permiso."')";

            $base->Insert( $query );
        }
    }

    /**
     * OBTIEN LOS EMAILS PARA EL RECORDATORIO
     * @param $id -> id del permiso
     * @return bool|array
     */
    public function getRecordatorioEmails( $id ){
        $base = new Database();

        $query = "SELECT * FROM permisos_recordatorios_emails WHERE permiso = '".$id."' ";

        if( $datos = $base->Select($query) ){
            return $datos;
        }
        return false;
    }

    /**
     * OBTIENE EL RECORDATORIO DE UN PERMISO
     * @param int $id = id del permiso
     */
    public function getRecordatorio($id){
        $base = new Database();

        $id = mysql_real_escape_string($id);

        $query = "SELECT * FROM permisos_recordatorios WHERE permiso = '".$id."' ";

        if($datos = $base->Select($query)){
            return $datos;
        }
        return false;
    }

    /**
     * REGISTRA LOS RESPONSABLES DE UN PERMISO
     * @param int $permiso -> id del permiso
     * @param array|string $responsables
     * @return bool
     */
    public function PermisoResponsables($permiso, $responsables ){
        if( empty($responsables) ){
            echo "Error: responsables esta vacio.";
            return false;
        }
        echo 'PermisoResponsable <br/>';

        //obtiene los responsables
        $responsables = explode(",", $responsables);

        if( is_array($responsables) ){

            foreach( $responsables as $f => $responsable ){
                $responsable = mysql_real_escape_string( $responsable );

                $this->CrearResponsable($permiso, $responsable);

            }

        }else{
            echo 'no es array';
            $responsable = mysql_real_escape_string( $responsables );

            $this->CrearResponsable($permiso, $responsable);
        }

    }

    /**
     * DETERMINA SI EL RESPONSABLE EXISTE Y SINO LO CREA
     * @param int $permiso -> id del permiso
     * @param string $responsable -> email
     * @return string $query -> el query
     */
    private function CrearResponsable($permiso, $responsable){
        $cliente = new Cliente();

        $fecha_creacion = date("Y-m-d H:i:s");

        if( is_numeric($responsable) ){
            echo 'responsable es numero '.$responsable.'<br/>';
            //si ya existe
            if( $cliente->ExisteResponsable($responsable) ){
                echo 'Ya existe<br/>';
                $responsable = mysql_real_escape_string($responsable);

                $query = "INSERT INTO permisos_responsables (permiso, responsable, fecha_creacion) VALUES ('".$permiso."', '".$responsable."', '".$fecha_creacion."' ) ";
                $base = new Database();
                $base->Insert( $query );

            }

            return true;
        }
        echo 'no existia, crea uno nuevo.<br/>';
        //crea un nuevo responsable
        if( $nuevo = $cliente->NuevoResponsable($responsable,"") ){
            $responsable = mysql_real_escape_string($responsable);

            $query = "INSERT INTO permisos_responsables (permiso, responsable, fecha_creacion) VALUES ('".$permiso."', '".$nuevo."', '".$fecha_creacion."' ) ";
        }

        $base = new Database();
        $base->Insert( $query );
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

                    if( $this->RegistrarArchivoPermiso($archivo['name'], $link, $permiso)){
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
     * @param string $nombre -> nombre del archivo
     * @param string $link -> link del archivo
     * @param int $permiso -> id del permiso
     * @return bool
     */
    private function RegistrarArchivoPermiso( $nombre, $link, $permiso ){
        $base = new Database();

        $nombre = mysql_real_escape_string($nombre);
        $fecha_creacion = date('Y-m-d G:i:s');

        //elimina el Admin/ inicial del link
        $link = substr($link,6);

        $query = "INSERT INTO permisos_archivos (nombre, link, permiso, fecha_creacion) VALUES ('".$nombre."', '".$link."', '".$permiso."', '".$fecha_creacion."') ";

        if( $base->Insert($query) ){
            return true;
        }
        return false;
    }

    /**
     * OBTIENE LOS PERMISOS DE UN ARCHIVO
     * @param $id
     * @return array|bool
     */
    public function getPermisosArchivos($id){
        $base = new Database();

        $id = mysql_real_escape_string($id);

        $query = "SELECT * FROM permisos_archivos WHERE permiso = '".$id."' ";

        if( $archivos = $base->Select($query) ){
            return $archivos;
        }
        return false;
    }

    /**
     * OBTIENE LAS AREAS DE APLICACION DE UN PERMISO
     * @param int $id -> id del permiso
     */
    public function getAreasApliccionPermiso( $id ){
        $base = new Database();

        $query = "SELECT * FROM permisos_areas_aplicacion WHERE permiso = '".$id."' ";

        if( $datos = $base->Select($query) ){
            return $datos;
        }
        return false;
    }

    /**
     * ELIMINA UN ARCHIVO DE UN PERMISO
     * @param $id => id del archivo
     * @return bool
     */
    public function DeleteArchivo($id){
        $base = new Database();

        $id = mysql_real_escape_string($id);

        $query = "SELECT * FROM permisos_archivos WHERE id = '".$id."' ";

        if( $datos = $base->Select($query)){
            $link = '../Admin/'.$datos[0]['link'];

            $query = "DELETE FROM permisos_archivos WHERE id = '".$id."' ";

            //borra archivo
            if( $base->DeleteFile($link) ){
                //borra de la base de datos
                if( $base->Delete($query) ){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * ELIMINA UN PERMISO
     * @param int $proyecto -> id del proyecto
     * @param int $id -> id del permisos a eliminar
     */
    public function DeletePermisos( $proyecto, $id ){
        $base = new Database();

        $id = mysql_real_escape_string($id);
        $proyecto = mysql_real_escape_string($proyecto);

        $query = "DELETE FROM permisos WHERE id = '".$id."' AND proyecto = '".$proyecto."' ";

        if( $base->Delete($query) ){

            $query = "SELECT * FROM permisos_archivos WHERE permiso = '".$id."' ";

            //elimina los archivos
            if( $archivos = $base->Select($query) ){
                //elimina cada uno de los archivos del permiso
                foreach($archivos as $f => $archivo ){
                    $this->DeleteArchivo( $archivo['id'] );
                }

            }

            //elimina los datos del permiso
            if( !$this->DeleteDatosPermisos($id) ){
                return false;
            }

            return true;
        }

        return false;
    }

    private function DeleteDatosPermisos( $id ){
        $base = new Database();
        $id = mysql_real_escape_string($id);

        $query = "DELETE FROM permisos_areas_aplicacion WHERE permiso = '".$id."' ";

        //elimina permisos_areas_aplicacion
        if( $base->Delete($query) ){
            $query = "DELETE FROM permisos_recordatorios WHERE permiso = '".$id."' ";

            //elimina permisos_recordatorios
            if( $base->Delete($query) ){
                $query = "DELETE FROM permisos_recordatorios_emails WHERE permiso = '".$id."' ";

                //elimina permisos_recordatorios_emails
                if( $base->Delete($query) ){
                    $query = "DELETE FROM permisos_responsables WHERE permiso = '".$id."' ";

                    //elimina permisos_responsables
                    if( $base->Delete($query) ){
                        //elimino todos los datos
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * OBTIENE EL NOMBRE DEL MES
     * @param int $mes
     * @param string $idioma
     */
    public function getMonth($mes, $idioma){
        return $this->months[$idioma][$mes];
    }

    /**
     * DETERMINA SI LA FECHA EXPIRO
     * @param $fecha -> dd/mm/yyy
     * @retrun bool
     */
    public function Expiro( $fecha ){
        $fecha = str_replace('/','-',$fecha);

        $ahora = strtotime(date("d-m-Y H:i:00",time()));
        $fecha = strtotime($fecha);

        if($ahora > $fecha){
            return true;
        }
        return false;
    }

    /**
     * ACTUALIZA UN PERMISO
     * @param int $proyecto -> id del proyecto
     * @param int $id -> id del permiso -> nombre
     * @param string $nombre -> nombre
     * @param string $fecha_emision -> fecha de emision
     * @param string $fecha_expiracion -> fecha de expiracion
     * @param string $recordatorio -> fecha para el recordatorio
     * @param string $emails -> emails, valor separado por comas
     * @param string $areas -> area de aplicacion
     * @param string $observacion -> observacion
     * @param string $responsables -> responsables valor separado por comas
     * @retrun bool
     */
    public function ActualizarPermiso( $proyecto, $id, $nombre, $fecha_emision, $fecha_expiracion, $recordatorio, $emails, $areas, $observacion, $responsables ){
        $base = new Database();

        //limpieza de los parametros
        $proyecto = mysql_real_escape_string($proyecto);
        $id = mysql_real_escape_string($id);
        $nombre = mysql_real_escape_string($nombre);
        $fecha_emision = mysql_real_escape_string($fecha_emision);
        $fecha_expiracion = mysql_real_escape_string($fecha_expiracion);
        $recordatorio = mysql_real_escape_string($recordatorio);
        $observacion = mysql_real_escape_string($observacion);

        //formatea las fechas
        $fecha_emision = $this->FormatearFecha($fecha_emision);
        $fecha_expiracion = $this->FormatearFecha($fecha_expiracion);
        $recordatorio = $this->FormatearFecha($recordatorio);

        $fecha_actualizacion = date('Y-m-d G:i:s');

        $query = "UPDATE permisos SET
                  nombre = '".$nombre."',
                  fecha_emision = '".$fecha_emision."',
                  fecha_expiracion = '".$fecha_expiracion."',
                  observacion = '".$observacion."',
                  fecha_actualizacion = '".$fecha_actualizacion."'
                  WHERE proyecto = '".$proyecto."' AND id = '".$id."' ";

        if( $base->Update($query) ){

            //actualiza el area de aplicacion
            $query = "UPDATE permisos_areas_aplicacion SET
                      area = '".$areas."'
                      WHERE permiso = '".$id."' ";

            if( !$base->Update($query) ){
                echo "Error: no se pudo actualizar el area de aplicacion del permiso";
                return false;
            }

            //actualza el recordatorio del permiso
            $query = "UPDATE permisos_recordatorios SET
                      fecha_inicio = '".$recordatorio."',
                      fecha_actualizacion = '".$fecha_actualizacion."'
                      WHERE permiso = '".$id."' ";

            if( !$base->Update($query) ){
                echo "Error: no se pudo actualizar el recordatorio del permiso.";
                return false;
            }

            //elimina los responsables del permiso
            $query = "DELETE FROM permisos_responsables WHERE permiso = '".$id."' ";
            $base->Delete($query);

            //elimina los emails de recordatorios del permiso
            $query = "DELETE FROM permisos_recordatorios_emails WHERE permiso = '".$id."' ";
            $base->Delete($query);

            //crea los responsables basado en los nuevos
            $this->PermisoResponsables($id, $responsables);

            //guarda los nuevos emails del permiso
            $this->EmailsRecordatorio($id, $emails);
            return true;
        }
        return false;
    }


    /************************************ AREAS DE APLIACION ********************/

    /**
     * OBTIENE LAS AREAS DE APLIACION
     * @return bool|array
     */
    public function getAreasAplicacion(){
        $base = new Database();

        $query = "SELECT * FROM areas_aplicacion ORDER BY nombre ";

        if( $datos = $base->Select( $query ) ){
            return $datos;
        }
        return false;
    }

    /**
     * OBTIEN LOS DATOS DE UN AREA DE APLICACION
     * @param $id -> id del area
     */
    public function getAreaAplicacion($id){
        $base = new Database();

        $query = "SELECT * FROM areas_aplicacion WHERE id = '".$id."' ORDER BY nombre";

        if( $datos = $base->Select($query)){
            return $datos;
        }
        return false;
    }

    /**
     * REGISTRA LAS AREAS DE APLICACION DE UN PERMISO
     * @param $id
     * @param $areas
     * @return bool
     */
    private function AreasApliccionPermiso( $id , $areas ){
        $base = new Database();

        $id = mysql_real_escape_string($id);
        $fecha_creacion = date('Y-m-d G:i:s');

        if( is_array($areas) ){

            foreach( $areas as $f => $area ){
                $query = "INSERT INTO permisos_areas_aplicacion (permiso, area, fecha_creacion) VALUES ('".$id."', '".$area."', '".$fecha_creacion."') ";

                $base->Insert($query);
            }
            return true;

        }else{
            $areas = mysql_real_escape_string($areas);

            $query = "INSERT INTO permisos_areas_aplicacion (permiso, area, fecha_creacion) VALUES ('".$id."', '".$areas."', '".$fecha_creacion."') ";

            if( $base->Insert($query) ){
                return true;
            }
        }
        return false;
    }


}