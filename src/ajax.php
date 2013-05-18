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

    }
}

/********************* VIA GET ************/

if( isset($_GET['func']) ){

    switch( $_GET['func'] ){
        case 'calendario-permisos':
            $permisos = new Permisos();
            $permisos->getCalendario();
            break;
    }

}