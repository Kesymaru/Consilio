<?php
/**
 * User: Andrey
 * Date: 5/22/13
 * Time: 11:16 AM
 * AJAX PARA LA ADMINISTRACION DE LOS PERMISOS DEL CLIENTE
 */

require_once("class/permisos.php");

if( isset($_POST['func']) ){

    switch ( $_POST['func'] ){

        //PERMISOS DEL CLIENTE
        case 'Permisos':
            break;

        case 'ActualizarPermiso':
            if( isset($_POST['']) ){

            }
            break;

        /******************************* AREAS DE APLICACION *************/

        case 'AreasAplicacion':
            AreasApliacion();
            break;

        case 'NuevaArea':
            NuevaArea();
            break;

        case 'RegistrarArea':
            if( isset($_POST['nombre']) ){
                $permisos = new Permisos();

                $descripcion = '';
                if( isset($_POST['descripcion']) ){
                    $descripcion = $_POST['descripcion'];
                }

                if( $id = $permisos->RegistrarAreaAplicacion( $_POST['nombre'], $descripcion ) ){
                    echo $id;
                }else{
                    echo "Error: no se pudo registrar la nueva area de aplicacion.";
                }

            }else{
                echo "Error: valor para nombre requerido.<br/>";
            }
            break;

        case 'EditarArea':
            if( isset($_POST['id']) ){
                EditarArea( $_POST['id'] );
            }
            break;

        case 'ActualizarArea':
            if( isset($_POST['id']) && isset($_POST['nombre']) ){

                $descripcion = '';
                if( isset($_POST['descripcion']) ){
                    $descripcion = $_POST['descripcion'];
                }

                $permisos = new Permisos();

                if( !$permisos->UpdateArea($_POST['id'], $_POST['nombre'], $descripcion) ){
                    echo "Error: no se pudo actualizar la area de aplicacion.<br/>id: ".$_POST['id'];
                }
            }
            break;

        case 'EliminarArea':
            if( isset($_POST['id']) ){
                $permisos = new Permisos();
                if( !$permisos->EliminarArea($_POST['id']) ){
                    echo 'Error: no se pudo eliminar el area, intente lo de nuevo mas tarde.<br/>';
                }
            }
            break;
    }
}

/********************** AREAS DE APLICACION ****************/

/**
 * LISTA PARA EL MENU CON LAS AREAS DE APLIACION
 */
function AreasApliacion(){
    $permisos = new Permisos();

    $lista = '<div id="areas" class="tipos">
                <div class="titulo">
                    Areas Aplicacion
                    <img src="images/search2.png" onclick="Busqueda(\'busqueda-areas\', \'buscar-areas\', \'areas\', false)" title="Buscar Areas de Aplicacion" class="boton-buscar icon">
                  </div>

                  <div id="busqueda-areas" class="busqueda">
					<div class="buscador">
						<input type="search" placeholder="Buscar" id="buscar-areas" title="Escriba Para Buscar">
					</div>
				</div>

                <!-- contenido lista -->
                <div class="scroll" >
                  <ul>';

    if( $areas = $permisos->getAreasAplicacion() ){

        foreach( $areas as $f => $area ){

            //tiene descripcion
            if( $area['descripcion'] != '' && $area['descripcion'] != null ){
                $title = (strlen($area['descripcion']) > 50) ? substr($area['descripcion'], 0, 50) . '...' : $area['descripcion'];

                $lista .= '<li title="'.$title.'" id="'.$area['id'].'" >';
            }else{
                $lista .= '<li  id="'.$area['id'].'" >';
            }

            $lista .= $area['nombre'].'
                          </li>';
        }

    }else{
        $lista .= '<li class="nodata">No hay areas</li>';
    }

    $lista .= '    </ul>
               </div>
               <!-- fin scroll -->
               <!-- botonera -->
               <div class="menu-botones">
                    <button type="button" class="ocultos" onclick="$AreasAplicacion.Eliminar()">Eliminar</button>
                    <button type="button" class="ocultos" onclick="$AreasAplicacion.Editar()">Editar</button>
                    <button type="button" onclick=" $AreasAplicacion.Nueva() ">Nueva</button>
               </div>
               <!-- fin botonera -->
           </div>';

    echo $lista;
}

/**
 * FORMULARIO PARA NUEVA AREA
 */
function NuevaArea(){

    $formulario = '<form id="FormularioNuevaArea" enctype="multipart/form-data" method="post" action="src/ajaxPermisos.php" >
                        <div class="titulo">
                            Nueva Area de Aplicacion
                        </div>

                        <input type="hidden" name="func" value="RegistrarArea" />

                         <div class="datos" >
                            <table>
                                <tr>
                                    <td>
                                        Nombre
                                    </td>
                                    <td>
                                        <input type="text" id="nombre" name="nombre" placeholder="Nombre" class="validate[required]" />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="full">
                                        Descripcion
                                        <br/>
                                        <textarea name="descripcion" id="descripcion" placeholder="Descripcion" class="validate[optional]" ></textarea>
                                    </td>
                                </tr>
                            </table>
                            <br/><br/>
                         </div>
                         <div class="datos-botones">
                            <button type="button" title="Cancelar Edición" onClick="CancelarContent()">Cancelar</button>
                            <input type="reset" title="Limpiar Edición" value="Limpiar" />
							<input type="submit" title="Guardar Edición" value="Guardar" />
                         </div>

                     </form>';

    echo $formulario;
}

/**
 * EDITAR UNA AREA
 * @param int $id -> id del area a editar
 */
function EditarArea( $id ){
    $permisos = new Permisos();

    $formulario = '';

    if ( $datos = $permisos->getArea( $id ) ){
        $formulario .= '<form id="FormularioEditarArea" enctype="multipart/form-data" method="post" action="src/ajaxPermisos.php" >
                        <div class="titulo">
                            Edicion Area
                        </div>

                        <input type="hidden" name="func" value="ActualizarArea" />
                        <input type="hidden" id="area" name="id" value="'.$id.'" />

                         <div class="datos" >
                            <table>
                                <tr>
                                    <td>
                                        Nombre
                                    </td>
                                    <td>
                                        <input type="text" id="nombre" name="nombre" placeholder="Nombre" class="validate[required]"  value="'.$datos[0]['nombre'].'" />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="full">
                                        Descripcion
                                        <br/>
                                        <textarea name="descripcion" id="descripcion" placeholder="Descripcion" class="validate[optional]" >'.$datos[0]['descripcion'].'</textarea>
                                    </td>
                                </tr>
                            </table>
                            <br/><br/>
                         </div>
                         <div class="datos-botones">
                            <button type="button" title="Cancelar Edición" onClick="CancelarContent()">Cancelar</button>
                            <input type="reset" title="Limpiar Edición" value="Limpiar" />
							<input type="submit" title="Guardar Edición" value="Guardar" />
                         </div>

                     </form>';
    }else{

    }

    echo $formulario;
}