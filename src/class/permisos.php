<?php
/*
 * CLASE PARA METODOS DE LOS PERMISOS DEL CLIENTE
 */

require_once('session.php');
require_once('classDatabase.php');
require_once('usuarios.php');

class Permisos {

    /**
     * COMPE LA VISTA DE CALENDARIO
     * @return string
     */
    public function Permisos(){

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
                       </div>
                       <div class="calendar" id="calendar-permisos">
                            <img class="logo-cliente" src="'.$logo.'" title="'.$datosCliente[0]['nombre'].'" alt="'.$datosCliente[0]['nombre'].'" />';

        for( $i = 0; $i <= 11; $i++){

            $activo = '';
            if( $i == 4 ){
                $activo = 'mes-actived';
            }

            $calendario .= '<div id="'.$i.'" class="mes '.$activo.'">
                                <div class="titulo">
                                    '.$nombreMeses[$i].'
                                    <table class="week-headers">
                                    <tr>
                                        <td>L<span>un</span></td><td>M<span>ar</span></td><td>M<span>ir</span></td>
                                        <td>J<span>ue</span></td><td>V<span>ie</span></td><td>S<span>ab</span></td><td>D<span>om</span></td>
                                    </tr>
                                    </table>
                                </div>
                                <div class="contador-permisos">
                                    3
                                </div>
                                <img src="images/banderin.png" class="banderin" />
                            </div>';

        }

        $calendario .= '</div>';

        return $calendario;
    }

    /**
     * OBTIENE EL CALENDARIO DE LOS PERMISOS DEL CLIENTE
     */
    public function getCalensario(){

    }

}