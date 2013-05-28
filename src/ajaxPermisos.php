<?php
/**
 * User: Andrey
 * AJAX EL CALENDARIO DE PERMISOS
 */
error_reporting( E_ALL );

require_once('master.php');
require_once('class/upload.php');
require_once('class/permisos.php');
require_once('class/usuarios.php');


if( isset($_POST['func']) ){

    switch( $_POST['func'] ){
        case 'TabProyectos':
            $master = new Master();
            echo $master->Proyectos();
            break;

        case 'TabPermisos':
            Caledario();
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

                ListaPermisos( $_POST['year'], $_POST['month'] );
            }
            break;

        case 'RegistrarPermiso':
            /*echo '<pre>'; print_r( $_POST); echo '</pre>';
            echo '<pre>'; print_r( $_FILES ); echo '</pre>';
            echo '<pre>'; print_r( pathinfo($_FILES['archivo0']['name']) ); echo "</pre>"; */

            if( isset($_POST['nombre']) && isset($_POST['fecha_emision'])
                && isset($_POST['fecha_expiracion'])
                && isset($_POST['recordatorio'])
                && isset($_POST['tipo_recordatorio'])
                && isset($_POST['areas']) ){
                NuevoPermiso();
            }else{
                echo 'faltan parametros';
            }
            break;
    }
}

/**
 * CREA UN NUEVO PERMISO
 */
function NuevoPermiso(){
    $permisos = new Permisos();

    $nombre = $_POST['nombre'];
    $fecha_emision = $_POST['fecha_emision'];
    $fecha_expiracion = $_POST['fecha_expiracion'];
    $recordatorio = $_POST['recordatorio'];
    $tipo_recordatorio = $_POST['tipo_recordatorio'];
    $areas = $_POST['areas'];

    if( !isset($_POST['email']) ){
        $email = $_POST['usar-mi-correo'];
    }

    $observacion = '';
    if( isset($_POST['observacion']) ){
        $observacion = $_POST['observacion'];
    }

    $responsables = '';
    if( isset($_POST['responsable']) ){
        $responsables = $_POST['responsables'];
    }

    if( $id = $permisos->NuevoPermiso( $nombre, $fecha_emision, $fecha_expiracion, $recordatorio, $tipo_recordatorio, $email, $areas, $observacion, $responsables ) ){

        //sube los archivos
        if( $error = $permisos->UploadFiles($_FILES, $id ) ){

            return true;

        }else{
            echo "<br/>Error: no se pudo subir los archivos del nuevo permiso.<br/>";
            return false;
        }

    }else{
        echo "<br/>Error: no se pudo crear el nuevo permiso.<br/>";
        return false;
    }

}

/********************** PERMISOS ********************/

/**
 * COMPONE EL CALENDARIO DE LOS PERMISOS
 * @type $mes -> numero del mes actual
 * @type $year -> a~o actual
 */
function Caledario(){
    $mes = date('m');
    $year = date('Y');

    $cliente = new Cliente();
    $permisos = new Permisos();

    $datosCliente = $cliente->getDatosCliente( $_SESSION['cliente_id'] );
    $logo = $_SESSION['datos'].$datosCliente[0]['imagen'];

    $nombreMeses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre');

    $cliente = $_SESSION['cliente_id'];

    $calendario = '<div class="panel-side" id="panel-permisos" >
                            <div class="titulo" id="permisos-mes" >
                                Mayo
                            </div>
                            <ul class="permisos" id="lista-permisos" >
                                <!-- lista permisos -->
                            </ul>

                            <!-- panel de edicion de nuevo permiso -->
                            <div id="panel-edicion" class="panel-edicion"> ';

    $calendario .= FomularioNuevoPermiso();

    $calendario.=          '</div>
                            <!-- fin panel edicion nuevo permiso -->

                       </div>
                       <div class="calendar" id="calendar-permisos">
                            <img class="logo-cliente" src="'.$logo.'" title="'.$datosCliente[0]['nombre'].'" alt="'.$datosCliente[0]['nombre'].'" />
                            <div class="calendar-titulo">
                                <img id="previous-year-calendar" src="images/preview.png" class="icon izquierda" title="Anterior" />
                                    <span id="year">'.$year.'</span>
                                <img id="next-year-calendar" src="images/next.png" class="icon derecha" title="Siguiente" />
                            </div>';

    //obtiene el calendario del a~o presente
    $contador = $permisos->getCalendario($year);

    foreach($contador as $f => $permiso ){

        $activo = '';
        if($permiso > 0){
            $activo = 'mes-actived';
        }

        $calendario .= '<div id="'.$f.'" class="mes '.$activo.'">
                                <div class="titulo">
                                    '.$nombreMeses[$f].'
                                </div>';

        $calendario .= '    <div class="contador-permisos">
                                    '.$permiso.'
                                </div>
                                <div class="contador-add">
                                    +
                                </div>
                                <img src="images/banderin.png" class="banderin" />
                            </div>';

    }

    $calendario .= '</div>';

    echo $calendario;
}

/**
 * COMPONE LA LISTA DE PERMISOS DE UN A~O
 * @param $year
 * @param $month
 */
function ListaPermisos( $year, $month ){
    $permisos = new Permisos();

    $lista = '';

    if( $datos = $permisos->getPermisos( $year, $month ) ){
        //echo '<pre>'; print_r($datos); echo '</pre>';

        foreach( $datos as $f => $permiso ){
            $lista .= '<li>
                               <span class="permisos-nombre">
                                    '.$permiso['nombre'].'
                               </span>
                               <span class="permisos-fecha">
                                    Fecha de Vencimiento: '.$permiso['fecha_expiracion'].'
                               </span>
                               <span>
                                    Fecha de Emicion: '.$permiso['fecha_emision'].'
                               </span>';

            $lista .= '<span class="permisos-responsable">
                            Responsable: '.$permiso['responsables'].'
                           </span>';

            $lista .= '<span class="permisos-observacion">
                               '.$permiso['observacion'].'
                           </span>';

            $lista .= '</li>';
        }

    }else{
        $lista .= '<li class="add">+</li>';
    }

    echo $lista;
}

/**
 * COMPONE EL FORMULARIO PARA UN NUEVO PERMISO
 */
function FomularioNuevoPermiso(){

    $formulario = '<form id="FormularioNuevoPermiso" class="chosen-centrado" enctype="multipart/form-data" method="post" action="src/ajaxPermisos.php" >
                        <div class="titulo">
                            Nuevo Permiso
                        </div>
                        <input type="hidden" name="func" value="RegistrarPermiso" />
                        <br/>
                        <table>
                            <tr title="Nombre del Permiso" >
                                <td>
                                    Nombre
                                </td>
                                <td colspan="2" >
                                   <input type="text" id="nombre" name="nombre" placeholder="Nombre" class="validate[required]" >
                                </td>
                            </tr>
                            <tr title="Fecha de Emision del permiso" >
                                <td>
                                    Fecha Emision
                                </td>
                                <td colspan="2" >
                                    <input type="text" id="fecha_emision" name="fecha_emision" placeholder="Fecha emision" class="validate[required],custom[date]" />
                                </td>
                            </tr>
                            <tr title="Fecha de Expiracion del permiso" >
                                <td>
                                    Fecha Expiracion
                                </td>
                                <td colspan="2" >
                                    <input type="text" id="fecha_expiracion" name="fecha_expiracion" placeholder="Fecha expiracion" class="validate[required],custom[date]" />
                                </td>
                            </tr>
                            <tr title="Recordatorio para la Expiracion del permiso" >
                                <td>
                                    Recordatorio
                                </td>
                                <td>
                                    <input type="number" id="recordatorio" name="recordatorio" placeholder="Tiempo" class="validate[required,custom[number]]" />
                                </td>
                                <td class="td-right">
                                    <select class="validate[required]" name="tipo_recordatorio" >
                                        <option value="0">Días</option>
                                        <option value="1">Semanas</option>
                                        <option value="2">Meses</option>
                                    </select>
                                </td>
                            </tr>
                            <tr title="Email para el recordatorio">
                                <td>
                                    Email:
                                </td>
                                <td>
                                    <input type="text" id="email_recordatorio" name="email" placeholder="Email para el recordatorio" class="validate[required],custom[email]" />
                                </td>
                                <td class="td-right">
                                    <label for="usar-mi-correo">
                                        <input type="checkbox" id="usar-mi-correo" value="'.$_SESSION['cliente_email'].'" name="usar-mi-correo">
                                        Usar mi email
                                    </label>
                                </td>
                            </tr>
                        </table>';

    $formulario .= SelecResponsables();
    $formulario .= SelectAreasAplicacion();

    $formulario .=      '<textarea id="observacion" name="observacion" placeholder="Observacion" ></textarea>
                         <div class="archivos" id="select-archivos" >
                            <div class="add" id="add-file">Archivos</div>
                            <div id="archivos-inputs">
                                <input type="file" id="archivo0" name="archivo0" />
                            </div>

                            <ul>
                                <!-- preview archivos elejidos -->
                            </ul>
                         </div>

                         <div class="datos-botones">
                             <button type="button" id="cancelar" class="button-cancelar" onclick="$Permisos.HideFormularioNuevoPermiso()">Cancelar</button>
                             <input class="button" type="reset" value="Limpiar" />
                             <input class="button" type="submit" value="Crear" />
                         </div>
                    </form>';

    return $formulario;
}

/**
 * COMPONE UN SELECT CON LA LISTA DE PERSONAS RESPONSABLES
 */
function SelecResponsables(){
    $cliente = new Cliente();

    $select = '<select id="responsables" name="responsables" data-placeholder="Responsables" multiple class="validate[optional]" >';

    if( $responsables = $cliente->getResponsables() ){
        foreach( $responsables as $f => $responsable ){
            $select .= '<option value="'.$responsable['id'].'" title="'.$responsable['email'].'" >'.$responsable['nombre'].' '.$responsable['apellidos'].'</option>';
        }
    }

    $select .= '</select>';

    return $select;
}

/**
 * COMPONE EL SELECT PARA LAS AREAS DE APLICACION
 */
function SelectAreasAplicacion(){
    $permisos = new Permisos();

    $select = '<select id="areas" name="areas" data-placeholder="Area de aplicacion" multiple class="validate[required]" >';

    if( $areas = $permisos->getAreasAplicacion() ){

        foreach( $areas as $f => $area ){

            //corta la descripcion si es larga
            if( $area['descripcion'] != "" ){
                $title = (strlen($area['descripcion']) > 50) ? substr($area['descripcion'], 0, 50) . '...' : $area['descripcion'];
            }

            $select .= '<option value="'.$area['id'].'" title="'.$title.'" > '.$area['nombre'].'</option>';
        }

    }

    $select .= '</select>';

    return $select;
}

