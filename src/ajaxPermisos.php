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
require_once('class/session.php');

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
            break;

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
                && isset($_POST['emails'])
                && isset($_POST['areas']) ){
                NuevoPermiso();
            }else{
                echo 'faltan parametros';
            }
            break;

        case 'getResponsables':
            getResponsables();
            break;

        case 'getMails':
            getMails();
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
    $emails = $_POST['emails'];
    $areas = $_POST['areas'];

    $observacion = '';
    if( isset($_POST['observacion']) ){
        $observacion = $_POST['observacion'];
    }

    $responsables = '';
    if( isset($_POST['responsable']) ){
        $responsables = $_POST['responsables'];
    }

    if( $id = $permisos->NuevoPermiso( $nombre, $fecha_emision, $fecha_expiracion, $recordatorio, $emails, $areas, $observacion, $responsables ) ){

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
                                <span>Enero</span>
                                <button type="button" class="derecha button-simbolo" onclick="$Permisos.NuevoPermiso()" title="Crear Nuevo Permiso">+</button>
                            </div>
                            <div class="permisos-wrapper">
                                <ul class="permisos" id="lista-permisos" >
                                    <!-- lista permisos -->
                                    <li class="add" >
                                        <span title="Crear Nuevo Permiso" onclick="$Permisos.NuevoPermiso()">
                                            +
                                        </span>
                                    </li>
                                </ul>
                            </div>
                            <!-- fin lista de permisos -->

                            <!-- panel de edicion de nuevo permiso -->
                            <div id="panel-edicion" class="panel-edicion"> ';

    $calendario .= FomularioNuevoPermiso();

    $calendario.=          '</div>
                            <!-- fin panel edicion nuevo permiso -->

                       </div>
                       <div class="calendar" id="calendar-permisos">
                            <!-- <img class="logo-cliente" src="'.$logo.'" title="'.$datosCliente[0]['nombre'].'" alt="'.$datosCliente[0]['nombre'].'" /> -->
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
    $cliente = new Cliente();

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

            //responsables                   
            if( $responsables = $cliente->getResponsables() ){
                
                $lista .= '<span class="permisos-responsable">
                            Responsable: ';

                foreach ($responsables as $f => $responsable) {
                    $lista .= $responsable['nombre'].' '.$responsable['apellidos'].", ";
                }

                $lista .= '</span>';

            }

            //si tiene observacion
            if( $permiso['observacion'] != '' && !empty( $permiso['observacion']) ){
                $lista .= '<span class="permisos-observacion">
                               '.$permiso['observacion'].'
                           </span>';
            }

            $lista .= ArchivosPermiso($permiso['id']);

            $lista .= '</li>';
        }

    }else{
        $lista .= '<li class="add" onclick="$Permisos.NuevoPermiso()">+</li>';
    }
    //$lista = '<li class="add" onclick="$Permisos.NuevoPermiso()">+</li>';
    echo $lista;
}

/**
 * OBTIENE LOS ARCHIVOS DE UN PERMISO
 * @param int $id -> id del permiso
 */
function ArchivosPermiso($id){
    $permisos = new Permisos();

    $lista = '';

    if( $archivos = $permisos->getPermisosArchivos($id) ){
        $lista .= '<span class="permisos-archivos">';

        foreach($archivos as $f => $archivo){
            $info = pathinfo($archivo['link']);
            //echo '<pre>'; print_r($info); echo '</pre>';

            $imagen = "images/folder.png";
            if( $info['extension'] == "png" || $info['extension'] == "jpeg" || $info['extension'] == "gif"){
                $imagen = $_SESSION['datos'].$archivo['link'];
            }

            $lista .= '<a href="http://localhost/matrizescala/src/download.php?link='.$_SESSION['datos'].$archivo['link'].'" title="Descargar" >
                            <img src="'.$imagen.'" />
                            <p>'.$archivo['nombre'].'<p>
                       </a>';
        }

        $lista .= '</span>';
    }

    return $lista;
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
                                    <input type="text" id="fecha_emision" name="fecha_emision" placeholder="Fecha emision" class="validate[required] datepicker" />
                                </td>
                            </tr>
                            <tr title="Fecha de Expiracion del permiso" >
                                <td>
                                    Fecha Expiracion
                                </td>
                                <td colspan="2" >
                                    <input type="text" id="fecha_expiracion" name="fecha_expiracion" placeholder="Fecha expiracion" class="validate[required] datepicker" />
                                </td>
                            </tr>
                            <tr title="Recordatorio para la Expiracion del permiso" >
                                <td>
                                    Recordatorio
                                </td>
                                <td colspan="2">
                                    <input type="text" id="recordatorio" name="recordatorio" placeholder="Fecha recordatorio" class="validate[required,datepicker]" />
                                </td>
                            </tr>
                            <tr title="Emails para el recordatorio">
                                <td>
                                    Emails:
                                </td>
                                <td colspan="2">
                                    <input type="text" id="emails" name="emails" placeholder="Email para el recordatorio" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Responsables
                                </td>
                                <td colspan="2">
                                    <input type="hidden" id="responsables" placeholder="Responsables"  />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Areas de Aplicacion
                                </td>
                                <td colspan="2">
                                    '.SelectAreasAplicacion().'
                                </td>
                            </tr>
                        </table>
                        ';

//    $formulario .= SelecResponsables();
//    $formulario .= SelectAreasAplicacion();

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
            $select .= '<option value="'.$responsable['id'].'" title="'.$responsable['email'].'" >
                            '.$responsable['nombre'].' '.$responsable['apellidos'].'
                        </option>';
        }
    }

    $select .= '</select>';

    $select = '<input type="hidden" id="responsables" placeholder="Responsables"  /> ';

    return $select;
}

/**
 * COMPONE EL SELECT PARA LAS AREAS DE APLICACION
 */
function SelectAreasAplicacion(){
    $permisos = new Permisos();

    $select = '<select id="areas" name="areas" data-placeholder="Area de aplicacion" multiple >';

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

//    $select = '<input type="hidden" id="areas" placeholder="Areas" /> ';
    return $select;
}

/**
 * OBTIENE LOS RESPONSABLES DE UN CLIENTE
 */
function getResponsables(){
    $cliente = new Cliente();

    $lista = array();

    if( $responsables = $cliente->getResponsables() ){
        foreach( $responsables as $f => $responsable ){
            $lista[] = array(
                "text"=>$responsable['nombre'].' '.$responsable['apellidos'],
                "id"=>$responsable['id'],
                "title"=>$responsable['nombre'].' titulo'
                    );
        }
    }

    echo json_encode( $lista );
}

/**
 * OBTIENE LOS EMAILS DISPONIBLES DE UN CLINETE
 * EMAIL PROPIO Y EMAILS DE RESPONSABLES
 */
function getMails(){
    $cliente = new Cliente();

    $datos = $cliente->getDatosCliente( $_SESSION['cliente_id'] );

    $emails = array();

    if( $responsables = $cliente->getResponsables() ){
        $emails[] = array(
                "text"=>$datos[0]['email'],
                "id"=>$datos[0]['email']
        );

        foreach( $responsables as $f => $responsable ){
            $lista[] = array(
                "text"=>$responsable['email'],
                "id"=>$responsable['email']
            );
        }
    }

    echo json_encode( $emails );
}

