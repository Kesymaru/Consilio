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
     * COMPE LA VISTA DE CALENDARIO
     * @return string
     */
    public function Permisos(){
        $mes = date('m');
        $year = date('Y');

        $cliente = new Cliente();
        $datosCliente = $cliente->getDatosCliente( $_SESSION['cliente_id'] );
        $logo = $_SESSION['datos'].$datosCliente[0]['imagen'];

        $nombreMeses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre');

        $cliente = $_SESSION['cliente_id'];

        $calendario = '<div class="panel-side" id="panel-permisos" >
                            <div class="titulo" id="permisos-mes" >
                                Mayo
                            </div>
                            <ul class="permisos" id="lista-permisos" >
                                <!--
                                <li title="Requisito de Operacion">
                                    <span class="permisos-nombre">
                                        Permiso Sanitario de Funcionamiento
                                    </span>
                                    <span class="permisos-fecha">
                                        Fecha de Vencimiento: 14 de mayo de 2013
                                    </span>
                                    <div class="tags">
                                        <span>Requisito de Operacion</span>
                                    </div>
                                </li>
                                <li title="Requisito de Operacion">
                                    <span class="permisos-nombre">
                                        Patente Comercial
                                    </span>
                                    <span class="permisos-fecha">
                                        Fecha de Vencimiento: 14 de mayo de 2013
                                    </span>
                                    <span class="permisos-responsable">
                                        Responsable: Maria Julia Gonzalez
                                    </span>
                                    <span class="permisos-observacion">
                                        Observacion: Debe iniciar a tramirse 15 dias antes del vencimiento
                                    </span>
                                    <div class="tags">
                                        <span>Requisito de Operacion</span>
                                    </div>
                                </li>
                                <li title="Caldera">
                                    <span class="permisos-nombre">
                                        Reporte Operacion Caldera
                                    </span>
                                    <span class="permisos-fecha">
                                        Fecha de Vencimiento: 14 de mayo de 2013
                                    </span>
                                    <span class="permisos-fecha">
                                        Fecha de Emision: 14 de mayo de 2013
                                    </span>
                                    <span class="permisos-responsable">
                                        Responsable: Maria Julia Gonzalez
                                    </span>
                                    <span class="permisos-observacion">
                                        Observacion: Debe iniciar a tramirse 15 dias antes del vencimiento
                                    </span>
                                    <div class="tags">
                                        <span>Caldera</span>
                                    </div>
                                </li>
                                -->
                            </ul>
                            <div id="panel-edicion" class="panel-edicion">
                                <form id="nuevo-permiso">
                                    <div class="titulo">
                                        Nuevo Permiso
                                    </div>
                                    <br/>
                                    <input type="text" id="nombre" placeholder="Nombre" >
                                    <input type="date" id="fecha_expiracion" placeholder="Fecha expiracion" />
                                    <input type="date" id="fecha_emision" placeholder="Fecha emision" />
                                    <input type="text" id="responsables" placeholder="Responsables" />
                                    <textarea id="observacion" placeholder="Observacion" ></textarea>

                                    <hr>
                                    <button type="button" id="cancelar" class="button-cancelar" onclick="HidePanelEdicion()">Cancelar</button>
                                    <button type="button" id="aceptar" class="boton" onclick="NuevoPermisoAccion()">Crear</button>
                                </form>
                            </div>
                       </div>
                       <div class="calendar" id="calendar-permisos">
                            <img class="logo-cliente" src="'.$logo.'" title="'.$datosCliente[0]['nombre'].'" alt="'.$datosCliente[0]['nombre'].'" />
                            <div class="calendar-titulo">
                                <img id="previous-year-calendar" src="images/preview.png" class="icon izquierda" />
                                    <span id="year">'.$year.'</span>
                                <img id="next-year-calendar" src="images/next.png" class="icon derecha" />
                            </div>';

        //obtiene el calendario del a~o presente
        $contador = $this->getCalendario($year);

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

        return $calendario;
    }

    /**
     * OBTIENE EL NUMERO DE PERMISOS PARA CADA MES
     * @param int $cliente -> id del cliente
     * @param int $year -> numero del a~o
     * @return array $contador
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
     * OBTIENE LOS PERMISOS DE UN MES ESPECIFICO Y LOS COMPONE EN UNA LISTA
     * @param $year
     * @param $month
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

        $datos = $base->Select($query);

        $lista = '';

        if( !empty( $datos ) ){
            //echo '<pre>'; print_r($datos);echo '</pre>';

            foreach( $datos as $f => $permiso){


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

                //responsable
                /*if( $responsables = $this->getResponsables( $permiso['id'] ) ){
                    $lista .= '<span class="permisos-responsable">Responsables';

                    foreach( $responsables as $fila => $responsable ){
                        $lista .= $responsable.' ';
                    }
                    $lista .= '</span>';
                } */

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

        return $lista;
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

}
?>