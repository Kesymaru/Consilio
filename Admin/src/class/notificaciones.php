<?php
/**
 * Created by IntelliJ IDEA.
 * User: Andrey
 * Date: 6/24/13
 * Time: 10:48 AM
 * To change this template use File | Settings | File Templates.
 */

require_once("classDatabase.php");
require_once("mail.php");

class notificaciones {

    /**
     * OBTIENE LAS NOTIFICACIONES
     */
    public function __construct(){

    }

    /**
     *NOTIFICACIONES PARA LOS PERMISOS
     */
    public function Permisos(){

    }

    /**
     *OBTIENE TODOS LOS PERMISOS CON NOTIFICACIONES Y SUS DATOS
     */
    private function getPermisos(){
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
          clientes.email as cliente_email
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
                              permisos.proyecto = 24 AND
                              permisos.cliente = 6 AND
                              permisos_recordatorios_emails.permiso = permisos.id";

                if( $datos = $base->Select($query) ){
                    $permisos[$f]['emails'] = $datos;
                }

            }

            return $datos;
        }
        return false;
    }
}