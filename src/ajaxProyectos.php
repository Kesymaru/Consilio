<?php
/**
* AJAX PARA PROYECTOS EN CLIENTES
*/

require_once("class/proyectos.php");
require_once("class/registros.php");
require_once('class/usuarios.php');
require_once("class/comentarios.php");
require_once("class/busqueda.php");

error_reporting(1);

if(isset($_POST['func'])){
	
	switch ($_POST['func']) {

        case 'SuperTab':
            if( isset($_POST['proyecto']) ){
                echo SuperTab( $_POST['proyecto'] );
            }
            break;

        //CARGA EL PANEL DE LAS CATEGORIAS|NORMAS|ARTICULOS
        case 'Panel':
            Panel();
            break;

		//CARGA LAS CATEGORIAS ROOT DE UN PROYECTO
		case 'CategoriasRoot':
			if( isset($_POST['proyecto']) ){
				CategoriasRoot( $_POST['proyecto'] );
			}
			break;

		//OBTIENE LOS HIJOS DE UN PADRE SELECCIONADO
		case 'Hijos':
			if(isset($_POST['padre']) && isset($_POST['proyecto'])){
				Hijos( $_POST['padre'], $_POST['proyecto'] );
			}
			break;

		case 'TieneHijos':
			if( isset($_POST['categoria'])){
				TieneHijos( $_POST['categoria'] );
			}
			break;

		//OBTIENE LOS IDS DE LOS HIJOS DE UN PADRE
		case 'GetHijos':
			if( isset($_POST['padre']) ){
				$registros = new Registros();
				//el id de todos los hijos
				$hijos = $registros->getTodosHijos($_POST['padre']);
				echo json_encode($hijos);
			}
			break;

		//OBTIENE LOS IDS DE LOS HERMANOS DE UN PADRE
		case 'GetHermanos':
			if( isset($_POST['padre']) ){
				$registros = new Registros();
				//el id de todos los hijos
				$hijos = $registros->getTodosHermanos($_POST['padre']);
				echo json_encode($hijos);
			}
			break;

		//DATOS DE UNA CATEGORIA
		case 'Normas':
			if(isset($_POST['proyecto']) && isset($_POST['id']) ){
				Normas($_POST['proyecto'], $_POST['id']);
			}
			break;

		//CARGA NORMA
		case 'Articulos':
			if( isset($_POST['proyecto']) && $_POST['categoria'] &&  isset($_POST['id']) ){
				Articulos($_POST['proyecto'], $_POST['categoria'], $_POST['id']);
			}
			break;

		//DATOS DE UN ARTICULO
		case 'DatosArticulo':

			if( isset($_POST['proyecto']) && isset($_POST['categoria']) && isset($_POST['norma']) && isset($_POST['id'])){
				DatosArticulo($_POST['proyecto'], $_POST['categoria'], $_POST['norma'], $_POST['id']);
			}
			break;

		/** NOMBRE PARA EL CAMINO ***/
		
		//NOMBRE DE LA NORMA
		case 'NormaNombre':
			if(isset($_POST['id'])){
				NormaNombre($_POST['id']);
			}
			break;

		//NOMBRE DEL ARTICULO
		case 'ArticuloNombre':
			if(isset($_POST['id'])){
				ArticuloNombre($_POST['id']);
			}
			break;

		//NOMBRE DE LA CATEGORIA
		case 'CategoriaNombre':
			if(isset($_POST['id'])){
				CategoriaNombre($_POST['id']);
			}
			break;

		case 'ProyectoLog':
			if( isset( $_POST['id'] ) ){
				ProyectoLog( $_POST['id'] );
			}
			break;

		/** BUSQUEDA ****/
		case 'Buscar';
			if( $_POST['busqueda'] ){
				$buscar = new Busqueda();
				$buscar->Buscar( $_POST['busqueda'] );
		 	}
		 	break;
	}

}else{
	echo "Error: ajaxProyectos.php FUNC no especificada.";
}

/**
 * COMPONE LAS TABS DE UN PROYECTO
 * @param $proyecto
 */
function SuperTab( $id ){
    $proyecto = new Proyectos();

    /*$tabs = '<ul>
                <li class="selected" id="tab-categorias">
                    Categorias
                </li>';*/
    $tabs = '';

    if( $datos = $proyecto->getProyectoDatos($id) ){

        //tiene permisos
        if( $datos[0]['permisos'] == 1 ){
            $tabs .= '<li id="tab-permisos" >
                        Permisos
                      </li>';
        }
    }

    $tabs .= '  <li id="tab-home">
                    <span class="icon-home icon-15"></span>
                </li>
              </ul>';

    return $tabs;
}

function Panel(){

    $panel='<table class="panel" >
                <tr>
                    <th id="panel-categorias">

                    </th>
                    <th id="panel-normas">

                    </th>
                    <th id="panel-articulos">
                    </th>
                </tr>

                <tr id="panel">
                    <td >
                        <div id="td-categorias">
                        </div>
                    </td>
                    <td >
                        <div id="td-normas" >
                        </div>
                    </td>
                    <td >
                        <div id="td-articulos" >
                        </div>
                    </td>
                </tr>
          </table>';

    echo $panel;
}

/**
* CATEGORIAS ROOT DE UN PROYECTO
* @param string $proyecto -> id del proyecto
*/
function CategoriasRoot( $proyecto ){
	$registros = new Registros();

	$datos = $registros->getRegistros($proyecto);

	$lista = '';

	if(!empty($datos)){
		
		$registradas = unserialize($datos[0]['registro']);
		$categoriasTodas = $registros->getHijos( 0 );
		
		$lista .= '<ul id="supercategorias" class="categorias">';

		if( is_array($registradas) ){
			//echo '<pre>'; print_r($registradas); echo '</pre>';

			foreach ($registradas as $f => $c) {
				$path = explode(',', $c);
				if( is_array($path) ){
					foreach ($path as $fila => $categoria) {
						$categoriasRegistradas[] = $categoria;
					}
				}
			}
			$categoriasRegistradas = array_unique( $categoriasRegistradas );

		}else{
			$categoriasRegistradas = array();
		}

		//echo '<pre>'; print_r($categoriasRegistradas); echo '</pre>';

		if( !empty($categoriasRegistradas) ){

			//mustra las super categorias ordenadas
			foreach ($categoriasTodas as $key => $categoria) {

				if( in_array( $categoria['id'], $categoriasRegistradas) ){
						
					$lista .= '<li class="" id="'.$categoria['id'].'" >';
											
					$imagen = $_SESSION['datos'].$categoria['imagen'];

					$lista .= '<img title="'.$categoria['nombre'].'" src="'.$imagen.'" onerror="this.src=\'images/es.png\'" /><p>'.$categoria['nombre'].'</p>';

					$lista .= '</li>';
				}
			}
		}else{
			$lista .= '<li></li>';
		}

		$lista .= '</ul>';
	}else{
		$lista .= '<li class="nodata">No hay datos</li>
					</ul>';
	}

	echo $lista;
}

/**
* CARGA CATEGORIAS HIJAS DE UN PADRE
* @param $padre -> id del padre
*/
function Hijos($padre, $proyecto){
	$registros = new Registros();
	$hijos = $registros->getHijos($_POST['padre']);

	$lista = "";

	if(!empty($hijos)){ //tiene hijos
		$datos = $registros->getRegistros($proyecto);
		$registradas = unserialize($datos[0]['registro']);
		
		if( is_array($registradas) ){
			//echo '<pre>'; print_r($registradas); echo '</pre>';

			foreach ($registradas as $f => $c) {
				$path = explode(',', $c);
				if( is_array($path) ){
					foreach ($path as $fila => $categoria) {
						$disponibles[] = $categoria;
					}
				}
			}
			$disponibles = array_unique( $disponibles );

		}else{
			$disponibles = array();
		}

		foreach ($hijos as $f => $hijo) {

			foreach ($disponibles as $s => $incluida) {
				if($incluida == $hijo['id']){
					$lista .= '<li>
								<span id="'.$hijo['id'].'">
									'.$hijo['nombre'].'
								</span>

								<ul id="sub'.$hijo['id'].'" class="subcategoria">
								</ul>
							   </li>';
				}else{
					continue;
				}
			}
		}

	}else{
		//no tiene hijos es una hoja
		if( !$registros->EsRoot($padre) ){
			//$lista .= '<script>$listaCategorias.Normas('.$padre.');</script>';
			//$lista .= 'no tiene hijos';
		}
	}

	echo $lista;
}

/**
* VALIDA QUE UNA CATEGORIA PADRE TENGA HIJOS
* @param int $categoria -> id del padre
*/
function TieneHijos($categoria){
	$registros = new Registros();
	$hijos = $registros->getHijos($categoria);

	if( !empty($hijos) ){
		echo json_encode('true');
		//echo json_encode( $hijos );
	}else{
		echo json_encode('false');
	}
}

/**
 * LISTA DE NORMAS INCLUIDAS Y VISIBLE DE UNA CATEGORIA
 * @param $proyecto -> id del proyecto
 * @param $id -> id de la categoria
 */
function Normas($proyecto, $id){
	$registros = new Registros();
	$normas = $registros->getValidNormas($proyecto, $id);

	//$lista = '<uL class="lista">';
	$lista = '';

	if(!empty($normas)){
		//$normas = unserialize($datos[0]['normas']);

		$conteo = 0;

		foreach ($normas as $key => $norma) {
			$datosNormas = $registros->getDatosNorma($norma);

			//si la norma es visible
			if($datosNormas[0]['status'] == 1){

				$lista .= '<li id="'.$datosNormas[0]['id'].'"  title="'.$datosNormas[0]['nombre'].' #'.$datosNormas[0]['numero'].'">
				              '.$datosNormas[0]['nombre'].'
						   </li>';
				$conteo++;
			}
		}
		
		if($conteo == 0){
			$lista .= '<div class="nodata">No hay normas</div>';
		}

	}else{
		$lista .= '<div class="nodata">No hay normas</div>';
	}

	//$lista .= '  </ul>';

	echo $lista;
}

/**
 * MUESTRA LISTA DE ARTICULOS de una norma
 * @param int $proyecto -> id del proyecto
 * @param int $categoria -> id de la categoria
 * @param int $id -> id de la norma
 * @return text/html $lista -> lista de articulos de la norma
 */
function Articulos($proyecto, $categoria, $id){
	$registros = new Registros();
	$datos = $registros->getValidArticulos($proyecto, $categoria, $id);
	$norma = $registros->getDatoNorma("nombre", $id);
	

	$lista = '<ul id="Articulos'.$id.'">';

	if(!empty($datos)){
		$conteo = 0;

		foreach ($datos as $fila => $norma) {
			$lista .= '<li id="'.$norma['id'].'" title="'.$norma['nombre'].'">
						'.$norma['nombre'].'
					   </li>';
			$conteo++;
		}
		
		if($conteo == 0){
			$lista .= '<div class="nodata">No hay articulos</div>';
		}
	}else{
		$lista .= '<div class="nodata">No hay articulos</div>';
	}

	$lista .= '<ul>';

	echo $lista;
}

/**
* CARGA DATOS DE UN ARTICULO
* @param int $proyecto -> id del proyecto
* @param int $categoria -> id de la categoria
* @param int $norma -> id de la norma
* @param int $id -> id del articulo
*/
function DatosArticulo($proyecto, $categoria, $norma, $id){
	date_default_timezone_set('America/Costa_Rica');

	$registros = new Registros();
	$comentarios = new Comentarios();

	//$datos = $registros->getArticulo($id);
	
	//OBTIENE LA DATA DEL ARTICULO VALIDANDO SI EL PROYECTO ESTA ACTIVO
	$datos = $registros->getValidArticuloDatos($proyecto, $id);

	$observacion = $registros->getObservacion($proyecto, $categoria, $norma, $id);
	$lista = '';

	if( !empty($datos) ){
		$lista = '<div id="datos-articulo">
				<div class="titulo">
					<img id="solapa" class="icon izquierda rotacion" onClick="$listaCategorias.OcultarDatos()" src="images/next.png" />
					'.$datos[0]['nombre'].'
			  	</div>
			  	<div class="datos">';

		//RESUMEN
		if( !empty($datos[0]['resumen']) ){

			$lista .= Box( "Resumen", "SuperBox BoxFocus", base64_decode($datos[0]['resumen']), "resumen");
				
		}

		//OBSERVACION
		if( !empty($observacion) ){
			$observacionTitulo = $registros->getTipoObservacion($observacion[0]['tipo']);

			$lista .= Box( $observacionTitulo, "SuperBox", base64_decode($observacion[0]['observacion']), "observacion");

		}

		//ARTICULO
		if( !empty($datos[0]['articulo']) ){

			$lista .= Box( "Articulo", "SuperBox", base64_decode($datos[0]['articulo']), "articulo");
				
		}

		//PERMISOS
		if( !empty($datos[0]['permisos']) ){

			$lista .= Box( "Permisos", "MiniBox", base64_decode($datos[0]['permisos']), "permisos");
				
		}

		//ENTIDADES
		if( !empty($datos[0]['entidad']) ){
			$entidades = unserialize( $datos[0]['entidad'] );

			$nombres = Entidades( $entidades );
			
			$listaEntidades = '';

			if( !empty($nombres) ){

				foreach ($nombres as $key => $nombre) {
					$listaEntidades .= $nombre."<br/>";
				}

			}

			$lista .= Box( "Entidades", "MiniBox", $listaEntidades, "entidad");
		}

		//SANCIONES
		if( !empty($datos[0]['sanciones']) ){

			$lista .= Box( "Sanciones", "MiniBox", base64_decode($datos[0]['sanciones']), "sanciones");
				
		}

		//ADJUNTOS
		$archivos = $registros->getArchivosArticulo( $datos[0]['id'] );

		if( is_array($archivos) && !empty($archivos) ){
			$lista .= '<div class="box AdjuntoBox" id="box-adjuntos">
								<div class="dato-titulo">
									Adjuntos
								</div>
								<div class="dato" id="box-dato-adjunto">
								<ul class="archivos-adjuntos">';

			foreach ($archivos as $f => $archivo) {
				$lista .= '<li>
							<a title="Descargar Adjunto '.$archivo['nombre'].'" href="src/download.php?link='.$_SESSION['datos'].$archivo['link'].'">
									<img src="images/folder.png" />
									<p>';
				
				if( empty($archivo['nombre']) ){
					$lista .= 'Archivo';
				}else{	
					$lista .= $archivo['nombre'];
				}

				$lista .= '</p>
									
							</a>
							</li>';
			}
			$lista .= '</div>
						</div>';
		}

		$lista .= '</div><!-- end datos cargados -->
					</div><!-- end datos -->';

		//compone el panel de los comentarios
		$lista .= PanelComentarios($proyecto, $categoria, $id);

		$lista .= '
					<div id="datos-footer">
						Última Actualización '.date("m d Y - g:i a").'
						<img class="icon derecha" onClick="Comentar()" src="images/coment.png" />';

		//OBTIENE EL TOTAL DE COMENTARIOS DEL ARTICULO
		$totalComentarios = $comentarios->getTotalComentarios($proyecto, $categoria, $id);

		//TIENE COMENTARIOS SIN LEER
		if( $comentarios->comentariosSinLeer($proyecto, $categoria, $id) ){

			$lista .= '<span class="counts counts-active">'.$totalComentarios.'<span>';

		//TIENE COMENTARIOS
		}else if($totalComentarios > 0){
			$lista .= '<span class="counts">'.$totalComentarios.'<span>';
		}else{
			$lista .= '<span class="counts counts-inactive"><span>';
		}
						
		$lista .=  '</div>

					</div>
					<!-- end datos-articulo -->';

	}else{
		$lista = '<div id="datos-articulo">
				<div class="titulo">
					<img id="solapa" class="icon izquierda rotacion" onClick="$listaCategorias.OcultarDatos()" src="images/next.png" />
					No hay datos
			  	</div>
			  	<div class="datos">
			  		<div class="error">
			  			Lo sentimos no encontramos los datos que buscas <span class="error-icon">:(</span>
			  			<br/>
			  			Intenta de nuevo.
			  		</div>
			  	</div>';
	}

	echo $lista;
}

/**
* COMPONE UN BOX PARA LOS DATOS
* @param string $titulo -> titulo del nuevo box
* @param string $extraclass -> clase extra (no requerido)
* @param string $dato -> text/html del texto
* @param string $key -> key del campo del dato para el id
* @return string $box -> text/html compuesto
*/
function Box($titulo, $extraclass = '', $dato, $key){
	
	$box = '<div class="box '.$extraclass.'" id="box-'.$key.'">
					<div class="dato-titulo">
						'.$titulo.'
			   		</div>
					<div class="dato" id="box-dato-'.$key.'">
						'.$dato.'
					</div>
			   </div>';

	return $box;
}


/**
* OBTIENE LAS ENTIDADES
* @param $entidad -> array[] con las entidades
* @return $nombres -> array[] con los nombres de las entidades registradas
*/
function Entidades($entidades){
	$registro = new Registros();

	$nombres = array();

	foreach ($entidades as $fila => $id) {
		$nombre = $registro->getEntidadDato("nombre", $id);
		$nombres[] = $nombre;
	}

	return $nombres;
}


/*************** HELPERS *************/

/**
* OBTIENE EL NOMBRE DE UNA NORMA
*/
function NormaNombre($id){
	$registros = new Registros();
	$nombre =  $registros->getDatoNorma("nombre", $id);

	echo $nombre;
}

/**
* OBTIENE EL NOMBRE DE UN ARTICULO
*/
function ArticuloNombre($id){
	$registros = new Registros();
	$nombre =  $registros->getDatoArticulo("nombre", $id);

	echo $nombre;
}

/**
* OBTIENE EL NOMBRE DE UNA CATEGORIA
*/
function CategoriaNombre($id){
	$registros = new Registros();
	$nombre =  $registros->getCategoriaDato("nombre", $id);

	echo $nombre;
}

/**
* COMPONE EL PANEL PARA LOS COMENTARIOS
* @param int $proyecto -> id del proyecto
* @param int $categoria -> id categoria
* @param int $articulo -> id del articulo
* @return string $panel -> html/text
*/
function PanelComentarios($proyecto, $categoria, $articulo){
	$comentarios = new Comentarios();
	$datos = $comentarios->getComentarios($proyecto, $categoria, $articulo);
	
	$cliente = new Cliente();
	$logo = $_SESSION['datos'].$cliente->getClienteDato("imagen", $_SESSION['cliente_id']);

	$panel = '';
	$oculto = '';
	$panel .= '<div id="panel-comentario">
				';

	if(!empty($datos)){
		$oculto = 'ocultos';

		$panel .= '<div id="comentarios"> <table class="comentarios" >';
		
		//compone los comentarios
		foreach ($datos as $fila => $comentario) {
			
			$usuario = $cliente->getClienteDato("nombre", $comentario['usuario']);
			$usuarioImg = $_SESSION['datos'].$cliente->getClienteDato("imagen", $comentario['usuario']); 
			
			$panel .= '<tr>
						<td class="comentario-imagen">
							<div class="div-imagen">
								<div title="'.$usuario.'" class="img-wrapper2" >
									<img src="'.$usuarioImg.'" onerror="this.src=\'images/es.png\'" />
								</div>
							</div>
							<span>'.$usuario.'</span>
					</td>';
			

			$panel .= '<td class="comentario" >
						'.base64_decode($comentario['comentario']).'
					   </td>
					   </tr>';
		}

		$panel .= '</table>
				  <div class="botonera-comentarios">
				  	<button id="NewComentario" type="button" onClick="NewComentario()">Comentar</button>
				  </div>
				  </div><!-- comentarios -->';
		

	}else{
		//no hay comentarios
		$panel .= '<div id="comentarios" > 
					<table class="comentarios" >
					</table>
					<div class="botonera-comentarios">
				  		<button id="NewComentario" type="button" onClick="NewComentario()">Comentar</button>
				  	</div>
				   </div><!-- comentarios -->';
	}

	$panel .= '<div id="new-comentario" >
						
					<div id="panel-editor">
							<textarea id="comentario" placeholder="Comentar ..." name="comentario"></textarea>
					</div>

					<div class="div-imagen panel-imagen">
						<div title="'.$_SESSION['cliente_nombre'].'" class="img-wrapper" >
							<img src="'.$logo.'" onerror="this.src=\'images/es.png\'" />
						</div>
					</div>
					<div>';

	if($oculto == ''){
		$panel .= '<button class="button-cancelar" type="button" onClick="Comentar()">Cancelar</button>';
	}else{
		$panel .= '<button class="button-cancelar" type="button" onClick="NewComentario()">Cancelar</button>';
	}
					
	$panel .='	<button type="button" onClick="AgregarComentario('.$articulo.')">Guardar</button>

				</div>
				</div><!-- end new comentario -->';

	$panel .= '</div><!-- end panel -->';

	return $panel;
}


/****************************** LOGS *************************/

/**
* AGREGA LOG AL PROYECTO
*/
function ProyectoLog( $id ){
	$proyectos = new Proyectos();

	if ( !$proyectos->ProyectoLog( $id ) ){
		echo "Error: al registrar log de proyecto $id del cliente ".$_SESSION['cliente_id'];
	}
}

/*********************** CONTADOR DE COMENTARIOS *****************/

/**
* CUENTA CUANTOS COMENTARIOS HAY 
* @param int $proyecto -> id del proyecto
* @param int $categoria -> id de la categoria
* @param int $id -> id del articulo
* @return boolean false -> si no tiene comentarios
* @return int
*/
function ContadorComentarios($proyecto, $categoria, $id){

	$comentarios = new Comentarios();

	$totalComentarios = $comentarios->getTotalComentarios( $proyecto, $categoria, $id );

}

?>
