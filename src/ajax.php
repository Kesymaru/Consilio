<?php
/**
 * User: Andrey
 * AJAX GENERAL
 */
error_reporting( 0 );

require_once('master.php');
require_once('class/permisos.php');

if( isset($_POST['func']) ){

    switch( $_POST['func'] ){
        case 'TabProyectos':
            $master = new Master();
            echo $master->Proyectos();
            break;

        case 'TabPermisos':
            $permisos = new Permisos();
            echo $permisos->Permisos();
            break;

        //CALENDARIO DE UN YEAR
        case 'CalendarYear':
            if( isset($_POST['year']) ){

                $permisos = new Permisos();
                $calendario = $permisos->getCalendario( $_POST['year'] );

                echo json_encode($calendario);
            }
            breakk;

        case 'Permisos':
            if( isset($_POST['year']) && isset($_POST['month']) ){

                $permisos = new Permisos();
                $lista = $permisos->getPermisos($_POST['year'], $_POST['month']);

                echo $lista;
            }
            break;

        case 'NuevoPermiso':
            if( isset($_POST['nombre']) && isset($_POST['fecha_expiracion']) && isset($_POST['fecha_emision']) && isset($_POST['observacion']) && isset($_POST['responsables']) && isset($_POST['categorias'])  ){
                $permisos = new Permisos();

                if ( !$permisos->NuevoPermiso( $_POST['nombre'], $_POST['fecha_expiracion'], $_POST['fecha_emision'], $_POST['observacion'], $_POST['responsables'], $_POST['categorias'] ) ){
                    echo '<br/>Error: no se pudo crear el nuevo permiso.<br/>Intentelo de nuevo';
                }
            }
            break;
    }
}
