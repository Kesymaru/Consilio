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
            if( isset($_POST['proyecto']) ){
                Caledario( $_POST['proyecto'] );
            }
            break;

        //CALENDARIO DE UN YEAR
        case 'CalendarYear':
            if( isset($_POST['year']) && isset($_POST['proyecto']) ){

                $permisos = new Permisos();
                $calendario = $permisos->getCalendario( $_POST['proyecto'], $_POST['year'] );

                echo json_encode($calendario);
            }
            break;

        //PERMISOS DE UN PROYECTO
        case 'Permisos':
            if( isset($_POST['proyecto']) ){
                Permisos($_POST['proyecto']);
            }
            break;

        //PERMISOS DE UN MES
        case 'PermisosMonth':
            if( isset($_POST['year']) && isset($_POST['month']) && isset($_POST['proyecto']) ){
                PermisosMonth( $_POST['proyecto'], $_POST['year'], $_POST['month'] );
            }
            break;

        //MUSTRA EL FORMULARIO DE UN NUEVO PERMISO
        case 'NuevoPermiso':
            if( isset($_POST['proyecto']) ){
                echo FomularioNuevoPermiso($_POST['proyecto']);
            }
            break;

        case 'RegistrarPermiso':
            echo '<pre>'; print_r( $_POST); echo '</pre>';
            echo '<pre>'; print_r( $_FILES ); echo '</pre>';
            //echo '<pre>'; print_r( pathinfo($_FILES['archivo0']['name']) ); echo "</pre>";

            if( isset($_POST['proyecto']) && isset($_POST['nombre'])
                && isset($_POST['fecha_emision'])
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
            //si es de un permiso
            if( isset($_POST['id']) ){
                getResponsablesPermiso( $_POST['id'] );
                return;
            }
            getResponsables();
            break;

        //OBTIENE TODOS LOS MAILS DISPONIBLES PARA EL CLIENTE
        case 'getMails':
            getMails();
            break;

        //EDICION DE UN PERMISO
        case 'EditarPermiso':
            if( isset($_POST['id']) ){
                echo EditarPermiso( $_POST['id'] );
            }
            break;

        //ELIMINA UN ARCHIVO ADJUNTADO
        case 'EliminarArchivo':
            if( isset($_POST['id']) ){
               $permisos = new Permisos();

                if( !$permisos->DeleteArchivo($_POST['id']) ){
                    echo "<br/>Error: no se pudo eliminar el archivo.";
                }
            }
            break;

        case 'EliminarPermiso':
            if( isset($_POST['permiso']) ){
                $permisos = new Permisos();
                if( !$permisos->DeletePermisos($_POST['id']) ){
                    echo "Error: no se pudo eliminar el permisos.";
                }
            }
            break;
    }
}

/**
 * CREA UN NUEVO PERMISO
 */
function NuevoPermiso(){
    $permisos = new Permisos();

    $proyecto = $_POST['proyecto'];
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
    if( isset($_POST['responsables']) ){
        $responsables = $_POST['responsables'];
    }

    if( $id = $permisos->NuevoPermiso( $proyecto, $nombre, $fecha_emision, $fecha_expiracion, $recordatorio, $emails, $areas, $observacion, $responsables ) ){

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
 * @param int $proyecto -> id del proyecto
 * @type $mes -> numero del mes actual
 * @type $year -> a~o actual
 */
function Caledario($proyecto){
    $mes = date('m');
    $year = date('Y');

    $cliente = new Cliente();
    $permisos = new Permisos();

    $datosCliente = $cliente->getDatosCliente( $_SESSION['cliente_id'] );
    $logo = $_SESSION['datos'].$datosCliente[0]['imagen'];

    $nombreMeses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre');

    $cliente = $_SESSION['cliente_id'];

    $calendario = '';

    /*$calendario .= '<div class="panel-side" id="panel-permisos" >
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
                            <div id="panel-edicion" class="panel-edicion">
                       </div>
                       <!-- fin panel edicion nuevo permiso -->';*/

    $calendario.=  '   </div>
                       <div class="calendar" id="calendar-permisos">
                            <div class="calendar-titulo">
                                <img id="previous-year-calendar" src="images/preview.png" class="icon izquierda" title="Anterior" />
                                    <span id="year">'.$year.'</span>
                                <img id="next-year-calendar" src="images/next.png" class="icon derecha" title="Siguiente" />
                            </div>';

    //obtiene el calendario del a~o presente
    $contador = $permisos->getCalendario($proyecto, $year);

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
 * IMPRIME LA LISTA DE TODOS LOS PERMISOS DE UN PROYECTO
 * ORDENADA POR FECHA DE MENOR A MAYOR
 * @param int $proyecto -> id del proyecto
 */
function Permisos($proyecto){
    $permisos = new Permisos();

    $lista = '<div id="permisos" >
                <div class="titulo" >
                    Permisos
                    <span class="icon-plus icon-15 icon-derecha" onclick="$Permisos.NuevoPermiso()" ></span>
                </div>
                <div class="permisos-wrapper">
                    <ul class="permisos" id="lista-permisos" >';

    if( $datos = $permisos->getPermisos($proyecto) ){
        $cliente = new Cliente();

        foreach( $datos as $f => $permiso ){
            $lista .= '<li id="permiso-'.$permiso['id'].'" >
                               <span class="permisos-nombre">
                                    '.$permiso['nombre'].'

                                    <button class="derecha button-simbolo" type="button" onclick="$Permisos.Editar('.$permiso['id'].')" title="Editar Permiso">Editar</button>

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

            //areas de aplicacion
            $lista .= AreasApliacion( $permiso['id'] );

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
        $lista .='<li class="add">
                    <span title="Crear Nuevo Permiso" onclick="$Permisos.NuevoPermiso()">
                        +
                    </span>
                  </li>';
    }

    $lista .= '        </ul>
                  </div>
                  <!-- fin lista de permisos -->

              </div>
              <!-- fin panel edicion nuevo permiso -->

              <!-- permisos de un mes -->
              <div id="permisos-mes">
                <div class="titulo">
                    Enero
                    <span class="icon-plus icon-15 icon-derecha" onclick="$Permisos.NuevoPermiso()" ></span>
                </div>
              </div>

              <!-- panel de edicion de nuevo permiso -->
              <div id="panel-edicion" class="panel-edicion">';

    echo $lista;
}

/**
 * IMPRIME LA LISTA DE LOS PERMISOS DE UN MES
 * @param int $proyecto -> id del proyecto
 * @param int $year
 * @param int $month
 */
function PermisosMonth( $proyecto, $year, $month ){
    $permisos = new Permisos();
//    $cliente = new Cliente();

    $lista = '';

    if( $datos = $permisos->getPermisosMonth( $proyecto, $year, $month ) ){
        //echo '<pre>'; print_r($datos); echo '</pre>';

        //compone la lista del permisos
//        $lista .= PermisoLista( $datos );

        foreach( $datos as $f => $permiso ){
            $lista .= '<li id="permiso-'.$permiso['id'].'" >
                               <span class="permisos-nombre">
                                    '.$permiso['nombre'].'

                                    <button class="derecha button-simbolo" type="button" onclick="$Permisos.Eliminar('.$permiso['id'].')">Eliminar</button>
                                    <button class="derecha button-simbolo" type="button" onclick="$Permisos.Editar('.$permiso['id'].')" title="Editar Permiso">Editar</button>

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

            //areas de aplicacion
            $lista .= AreasApliacion( $permiso['id'] );

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
 * COMPONE LOS DATOS DE UN PERMISO EN UNA LISTA
 * @param srray $datos -> datos del permiso
 * @return string $lista
 */
function PermisoLista( $datos ){
    $cliente = new Cliente();
    $lista = '';

    foreach( $datos as $f => $permiso ){
        $lista .= '<li id="permiso-'.$permiso['id'].'" >
                               <span class="permisos-nombre">
                                    '.$permiso['nombre'].'

                                    <button class="derecha button-simbolo" type="button" onclick="$Permisos.Editar('.$permiso['id'].')" title="Editar Permiso">Editar</button>

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

        //areas de aplicacion
        $lista .= AreasApliacion( $permiso['id'] );

        //si tiene observacion
        if( $permiso['observacion'] != '' && !empty( $permiso['observacion']) ){
            $lista .= '<span class="permisos-observacion">
                               '.$permiso['observacion'].'
                           </span>';
        }

        $lista .= ArchivosPermiso($permiso['id']);

        $lista .= '</li>';
    }
    return $lista;
}

/**
 * COMPONE LAS AREAS DE APLICACION DE UN PERMISO
 * @param $id
 */
function AreasApliacion($id){
    $permisos = new Permisos();

    $lista = '';

    if( $seleccionadas = $permisos->getAreasApliccionPermiso($id) ){
        $lista .= '<span class="permisos-areas">
                    Areas Aplicacion: ';

        foreach( $seleccionadas as $f => $area ){

            if( $datos = $permisos->getAreaAplicacion( $area['area'] )){
                $lista .= $datos[0]['nombre'];

                if( $f < sizeof($seleccionadas)-1 ){
                    $lista .= ', ';
                }
            }
        }

        $lista .= '</span>';
    }

    return $lista;

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
            if( $info['extension'] == "png" || $info['extension'] == "jpg" || $info['extension'] == "gif"){
                $imagen = $_SESSION['datos'].$archivo['link'];
            }

            $lista .= '<a href="http://localhost/matrizescala/src/download.php?link='.$_SESSION['datos'].$archivo['link'].'" title="Descargar" >
                            <img src="'.$imagen.'" />
                            <p>'.$archivo['nombre'].'</p>
                       </a>';
        }

        $lista .= '</span>';
    }

    return $lista;
}

/**
 * COMPONE LA LISTA DE ARCHIVOS DE UN PERMISOS EN EDICION
 * @param $id -> id del permiso
 */
function EditarArchivosPermiso($id){
    $permisos = new Permisos();
    $lista = '';

    if( $archivos = $permisos->getPermisosArchivos($id) ){
        foreach( $archivos as $f => $archivo ){
            $info = pathinfo($archivo['link']);
            //echo '<pre>'; print_r($info); echo '</pre>';

            $title = 'Documento';
            $imagen = 'images/folder.png';

            if( $info['extension'] == 'png' || $info['extension'] == 'jpg' ){
                $title = 'Imagen';
                $imagen = $_SESSION['datos'].$archivo['link'];
            }

            $lista .='<li class="file" title="'.$title.'" id="archivo'.$archivo['id'].'">
                        <img class="close" src="images/close.png" title="Quitar Documento" onclick="$Permisos.RemoveArchivo('.$archivo['id'].')">
                        <img class="image" src="'.$imagen.'">
                        <div>
                            <span>'.$archivo['nombre'].'</span>
                        </div>
                    </li>';
        }
    }

    return $lista;
}

/**
 * COMPONE EL FORMULARIO PARA UN NUEVO PERMISO
 */
function FomularioNuevoPermiso($proyecto){

    $formulario = '<form id="FormularioNuevoPermiso" class="chosen-centrado" enctype="multipart/form-data" method="post" action="src/ajaxPermisos.php" >
                        <div class="titulo">
                            Nuevo Permiso
                        </div>
                        <input type="hidden" name="func" value="RegistrarPermiso" />
                        <input type="hidden" name="proyecto" id="proyecto" value="'.$proyecto.'" />
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
                                    <input type="hidden" id="responsables" name="responsables" placeholder="Responsables"  />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Área de Aplicación
                                </td>
                                <td colspan="2">
                                    '.SelectAreasAplicacion().'
                                </td>
                            </tr>
                        </table>
                        ';

    $formulario .=      '<textarea id="observacion" name="observacion" placeholder="Observacion" ></textarea>
                         <div class="archivos" id="select-archivos" >
                            <div class="add" id="add-file">Archivos</div>

                            <!-- inputs de los archivos van ocultos -->
                            <div id="archivos-inputs">
                                <input type="file" id="input0" name="archivo0" />
                            </div>

                            <ul>
                                <!-- preview archivos elejidos -->
                            </ul>
                         </div>

                         <div class="datos-botones">
                             <button type="button" id="cancelar" class="button-cancelar" onclick="$Permisos.HidePanelEdicion()">Cancelar</button>
                             <button type="button" onclick="$Permisos.ResetFormularioNuevoPermiso()">Limpiar</button>
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

    $select = '<input type="hidden" id="responsables" name="responsables" placeholder="Responsables"  /> ';

    return $select;
}

/**
 * COMPONE EL SELECT PARA LAS AREAS DE APLICACION
 */
function SelectAreasAplicacion(){
    $permisos = new Permisos();

    $select = '<select id="areas" name="areas" placeholder="Area de aplicacion" >';

    if( $areas = $permisos->getAreasAplicacion() ){

        foreach( $areas as $f => $area ){

            $select .= '<option value="'.$area['id'].'" >
                            '.$area['nombre'].'
                       </option>';
        }

    }

    $select .= '</select>';

    return $select;
}

/**
 * COMPONE SELECT DE AREAS DE APLICACION SELECCIONADAS DE UN PERMISOS
 * @param int $id -> id del permiso
 * @return string
 */
function SelectedAreasAplicacion($id){
    $permisos = new Permisos();

    $select = '<select id="areas" name="areas" placeholder="Area de aplicacion" >';

    //si tiene areas seleccionadas
    if( $selected = $permisos->getAreasApliccionPermiso($id) ){
        if( $areas = $permisos->getAreasAplicacion() ){
            //echo '<pre>'; print_r($selected); echo '</pre>';
            foreach( $areas as $f => $area ){

                $select .= '<option value="'.$area['id'].'" ';

                foreach( $selected as $key => $incluida ){
                    if( in_array($area['id'], $incluida) ){
                        $select .= ' selected ';
                        unset($selected[$key]);
                        break;
                    }
                }

                $select .= '>
                               '.$area['nombre'].'
                            </option>';
            }

        }
    }else{
        echo 'no tiene areas seleccionadas';
    }

    $select .= '</select>';

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
 * OBTIENE RESPONSABLES DE UN PERMISO
 */
function getResponsablesPermiso( $id ){
    $permisos = new Permisos();
    $cliente = new Cliente();

    $lista = array();

    //obtiene los responsables seleccionados
    if( $responsables = $permisos->getResponsables($id) ){

        $selected = array();
        foreach( $responsables as $f => $responsable ){
            $selected[] = array(
                "text"=>$responsable['nombre'].' '.$responsable['apellidos'],
                "id"=>$responsable['id'],
                "title"=>$responsable['nombre'].' titulo'
            );
        }
        $lista['selected'] = $selected;
    }

    //obtiene los responsables disponibles
    if( $responsables = $cliente->getResponsables() ){
        $tags = array();
        foreach( $responsables as $f => $responsable ){
            $tags[] = array(
                "text"=>$responsable['nombre'].' '.$responsable['apellidos'],
                "id"=>$responsable['id'],
                "title"=>$responsable['nombre'].' titulo'
            );
        }
        $lista['tags'] = $tags;
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

/**
 * FORMULARIO PARA LA EDICION DE UN PERMISO
 * @param $id -> id del permiso
 * @return string
 */
function EditarPermiso( $id ){
    $permiso = new Permisos();

    $formulario = '';

    if( !$datos = $permiso->getPermiso( $id ) ){
        return '';
    }

    //fecha recordatorio
    $recordatorio = '';
    if( $datosRecordatorio = $permiso->getRecordatorio($id) ){
        $recordatorio = $datosRecordatorio[0]['fecha_inicio'];
    }

    //responsables seleccionados
    $lista_responsables = '';

    if( $responsables = $permiso->getResponsables( $id ) ){
        foreach( $responsables as $f => $responsable ){

            $lista_responsables .= $responsable['responsable'];

            if( $f < sizeof($responsables)-1 ){
                $lista_responsables .= ',';
            }

        }
    }

    //emails para recordatorio
    $lista_emails = '';
    $emails = $permiso->getRecordatorioEmails( $id );

    if( is_array($emails) ){
        foreach( $emails as $f => $email ){
            $lista_emails .= $email['email'];

            if( $f < sizeof($emails)-1 ){
                $lista_emails .= ',';
            }
        }
    }

    //formatea las fechas
    $datos[0]['fecha_emision'] = date( 'd/m/Y', strtotime($datos[0]['fecha_emision']) );
    $datos[0]['fecha_expiracion'] = date( 'd/m/Y', strtotime($datos[0]['fecha_expiracion']) );
    $recordatorio = date( 'd/m/Y', strtotime($recordatorio) );

    $formulario = '<form id="FormularioEditarPermiso" class="chosen-centrado" enctype="multipart/form-data" method="post" action="src/ajaxPermisos.php" >
                        <div class="titulo">
                            Nuevo Permiso
                        </div>
                        <input type="hidden" name="func" value="ActualizarPermiso" />
                        <input type="hidden" name="id" value="'.$id.'" />

                        <br/>
                        <table>
                            <tr title="Nombre del Permiso" >
                                <td>
                                    Nombre
                                </td>
                                <td colspan="2" >
                                   <input type="text" id="nombre" name="nombre" placeholder="Nombre" class="validate[required]" value="'.$datos[0]['nombre'].'" >
                                </td>
                            </tr>
                            <tr title="Fecha de Emision del permiso" >
                                <td>
                                    Fecha Emision
                                </td>
                                <td colspan="2" >
                                    <input type="text" id="fecha_emision" name="fecha_emision" placeholder="Fecha emision" class="validate[required] datepicker"  value="'.$datos[0]['fecha_emision'].'" />
                                </td>
                            </tr>
                            <tr title="Fecha de Expiracion del permiso" >
                                <td>
                                    Fecha Expiracion
                                </td>
                                <td colspan="2" >
                                    <input type="text" id="fecha_expiracion" name="fecha_expiracion" placeholder="Fecha expiracion" class="validate[required] datepicker"  value="'.$datos[0]['fecha_expiracion'].'" />
                                </td>
                            </tr>
                            <tr title="Recordatorio para la Expiracion del permiso" >
                                <td>
                                    Recordatorio
                                </td>
                                <td colspan="2">
                                    <input type="text" id="recordatorio" name="recordatorio" placeholder="Fecha recordatorio" class="validate[required,datepicker]" value="'.$recordatorio.'" />
                                </td>
                            </tr>
                            <tr title="Emails para el recordatorio">
                                <td>
                                    Emails:
                                </td>
                                <td colspan="2">
                                    <input type="text" id="emails" name="emails" placeholder="Email para el recordatorio" value="'.$lista_emails.'" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Responsables
                                </td>
                                <td colspan="2">
                                    <input type="hidden" id="responsables" name="responsables" placeholder="Responsables" value="'.$lista_responsables.'" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Área de Aplicación
                                </td>
                                <td colspan="2">
                                    '.SelectedAreasAplicacion( $id ).'
                                </td>
                            </tr>
                        </table>
                        ';

    $formulario .=      '<textarea id="observacion" name="observacion" placeholder="Observacion" >'.$datos[0]['observacion'].'</textarea>
                         <div class="archivos" id="select-archivos" >
                            <div class="add" id="add-file">Archivos</div>

                            <!-- inputs de los archivos van ocultos -->
                            <div id="archivos-inputs">
                                <input type="file" id="input0" name="archivo0" />
                            </div>

                            <ul>
                                '.EditarArchivosPermiso($id).'
                            </ul>
                         </div>

                         <div class="datos-botones">
                             <button type="button" id="cancelar" class="button-cancelar" onclick="$Permisos.HidePanelEdicion()">Cancelar</button>
                             <button type="button" onclick="$Permisos.ResetFormulario()">Limpiar</button>
                             <input class="button" type="submit" value="Guardar" />
                         </div>
                    </form>';

    return $formulario;


}