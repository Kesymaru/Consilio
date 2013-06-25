<?php
/**
 * Created by IntelliJ IDEA.
 * User: Andrey
 * Date: 6/24/13
 * Time: 10:48 AM
 * To change this template use File | Settings | File Templates.
 */

require_once("classDatabase.php");
require_once("template.php");
require_once("mail.php");

class Notificaciones {

    /**
     * OBTIENE LAS NOTIFICACIONES
     */
    public function __construct(){

    }

    /**
     *NOTIFICACIONES PARA LOS PERMISOS
     */
    public function Permisos(){

        $menssage .= '';

        $para = '';

        $datos = array(
            "title" => 'Titulo',
            "subtitle" => "Hola test",
            "menssage" => $menssage,

            "from" => "support@matriz.com",
            "info_from" => "Notificaciones Matriz Escala",
            "info_mobile" => "123456",
            "info_phone" => "987654",
            "info_email" => "support@matriz.com",
            "info_home" => "google.com",

            "to" => $para,
            "bcc" => '',

            "direccion_edificio" => 'edificio 3, piso 12',
            "direccion" => "La sabana, San Jose, Costa Rica",

            "cliente_nombre" => "nombre cliente",
            "cliente_imagen" => "../../images/es.png",
        );

        if( $permisos = $this->getNotificacionesPermisos() ){

            foreach($permisos as $fila => $permiso ){
                //datos para la notificacion
                $datos = array(
                    "title" => 'Permisos Expirados: '.$permiso['proyecto_nombre'],
                    "cliente_nombre" => $permiso['cliente_nombre'],
                    "cliente_imagen" => $permiso['cliente_imagen'],
                );

                $to = '';
                foreach($permiso['emails'] as $f => $email ){
                    $to .= $email.',';
                }
                $datos['to'] = $to;

                $menssage = 'El proyecto '.$permiso['proyecto_nombre'].' tiene los siguientes permisos expirados:';

                $datos['menssage'] = $menssage;

                $this->Notificar($datos);
            }

            return true;
        }
        return false;
    }

    /**
     *OBTIENE TODOS LOS PERMISOS CON NOTIFICACIONES Y SUS DATOS
     */
    public function getNotificacionesPermisos(){
        $base = new Database();

        $fecha = '2013-07-10';

        //selecciona los permisos con recordatorios
        $query = "SELECT
          permisos.*,
          MIN( permisos_recordatorios.fecha_inicio ) AS desde,
          COUNT( DISTINCT permisos_recordatorios.id ) As recordatorios,
          proyectos.nombre as proyecto_nombre,
          proyectos.imagen as proyecto_imagen,
          clientes.nombre as cliente_nombre,
          clientes.email as cliente_email,
          clientes.imagen as cliente_imagen
        FROM
          permisos,
          permisos_recordatorios,
          proyectos,
          clientes
        WHERE
          permisos_recordatorios.permiso = permisos.id AND
          permisos_recordatorios.fecha_inicio <= '2013-07-10' AND
          proyectos.id = permisos.proyecto AND
          clientes.id = permisos.cliente
        GROUP BY permisos.proyecto";

        if( $permisos = $base->Select($query) ){

            foreach($permisos as $f => $permiso ){

                //obtiene los emails para las notifciaciones
                $query = "SELECT
                            permisos_recordatorios_emails.*
                          FROM
                              permisos,
                              permisos_recordatorios_emails
                          WHERE
                              permisos.proyecto = '".$permiso['proyecto']."' AND
                              permisos.cliente = '".$permiso['cliente']."' AND
                              permisos_recordatorios_emails.permiso = permisos.id";

                if( $datos = $base->Select($query) ){
                    $permisos[$f]['emails'] = $datos;
                }

            }

            return $permisos;
        }
        return false;
    }

    /**
     * ENVIA LA NOTIFICACION
     * @param array $datos
     * @param string $template
     */
    public function Notificar($datos, $template = 'default'){
        $template = new Template();

        $final = $template->Email($datos, $template);
        echo 'notifica';

        echo  $final;
    }
}


$notifcaciones = new Notificaciones();

$notifcaciones->Permisos();

//echo '<pre>'; print_r($notifcaciones->getNotificacionesPermisos()); echo '</pre>';


/**

$datos = array(
    "title" => 'Permisos Expirados: '.$permiso['proyecto_nombre'],
    "subtitle" => "El proyecto ".$permiso['proyecto_nombre']." tiene los siguientes permisos expirados:",
    "menssage" => $menssage,

    "from" => "support@matriz.com",
    "info_from" => "Notificaciones Matriz Escala",
    "info_mobile" => "123456",
    "info_phone" => "987654",
    "info_email" => "support@matriz.com",
    "info_home" => "google.com",

    "to" => $para,
    "bcc" => '',

    "direccion_edificio" => 'edificio 3, piso 12',
    "direccion" => "La sabana, San Jose, Costa Rica",

    "cliente_nombre" => "nombre cliente",
    "cliente_imagen" => "../../images/es.png",
);

 */

