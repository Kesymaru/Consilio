<?php
/**
* FUNCIONALIDAD DE PREVIEW, PARA CATEGORIAS, ARTICULOS Y NORMAS
* TAMBIEN PARA LA SELECCION DE NORMAS Y ARTICULOS EN COMPOSICION DE UN PROYECTO
*/

require_once("class/registros.php");

if(isset($_POST['func'])){
	switch ($_POST['func']) {

		//MUESTRA LISTA DE NORMAS INCLUIDAS
		case 'NormasIncluidas':
			if( isset($_POST['proyecto']) && isset($_POST['categoria']) ){
				NormasIncluidas($_POST['proyecto'], $_POST['categoria']);
			}
			break;

		case 'ArticulosIncluidos':
			if( isset($_POST['proyecto']) && isset($_POST['norma']) ){
				ArticulosIncluidos( $_POST['proyecto'], $_POST['norma'] );
			}
			break;

		//PREVIEW DE LOS DATOS DE UN ARTICULO 
		case 'PreviewArticulo':
			if( isset($_POST['id']) ){
				PreviewArticulo( $_POST['id'] );
			}
			break;

		/******** REGISTROS *********/

		//REGISTRA NORMAS INCLUIDAS
		case 'RegistrarNormasIncluidas':
			if( isset($_POST['proyecto']) && isset($_POST['categoria']) && isset($_POST['normas']) ){
				RegistrarNormasIncluidas( $_POST['proyecto'], $_POST['categoria'], $_POST['normas'] );
			}
			break;

		//REGISTRA ARTICULOS INCLUIDODS
		case 'RegistrarArticulosIncluidos':
			if( isset($_POST['proyecto']) && isset($_POST['norma']) && isset($_POST['articulos']) ){
				RegistrarArticulosIncluidos($_POST['proyecto'], $_POST['norma'], $_POST['articulos'] );
			}
			break;
	}
}

/***************** PREVIEW PARA CATEGORIAS INCLUIDAS  **********************/

/**
* MUESTRA LAS LISTA DE NORMAS INCLUIDAS DE UN CATEGORIA
* @param $proyecto
* @param $categoria
*/
function NormasIncluidas($proyecto, $categoria){
	$registros = new Registros();

	//obtiene todos los datos de la categoria
	$categoriaDatos = $registros->getCategoria( $categoria );

	//normas de la categoria
	$normas = unserialize($categoriaDatos[0]['normas']);

	//normas incluidas
	$incluidas = $registros->getRegistrosNorma($proyecto, $categoria);

	if(!empty($incluidas)){
		$incluidas = unserialize( $incluidas[0]['registro'] );
	}else{
		$incluidas = array();
	}

	$lista = '<div class="preview" id="NormasIncluidas">
				<input type="hidden" name="proyecto" id="proyecto" value="'.$proyecto.'" >
			  	<input type="hidden" name="categoria" id="categoria" value="'.$categoria.'" >
			  	<div class="titulo">

			  		'.$categoriaDatos[0]['nombre'].'
			  	</div>';

	if(!empty($normas)){
		$lista .= '<div clas="datos-preview">
					  <div class="panel" id="panelNormas">
					  <div class="subtitulo" id="panelNormasTitulo">
					  	
					  	<button type="button" class="icon izquierda" onClick="SelectAllPreview(\'panelNormas ul\',false, true);">Todo</button>
					  	
					  	<img class="icon derecha ocultos" src="images/next.png" title="Articulos" id="VerArticulosIncluidos" >

					  	Normas
					  </div>';
		
		$lista .= '<ul class="listIzquierda">';
		foreach ($normas as $fila => $norma) {
			$nombre = $registros->getDatoNorma("nombre", $norma['id']);

			//esta incluida
			if( in_array($norma['id'], $incluidas) ){
				$lista .= '<li class="seleccionada" id="'.$norma['id'].'">
							<input checked type="checkbox" id="norma'.$norma['id'].'" name="normas[]" value="'. $norma['id'] .'" />
							'.$nombre.'
						   </li>';
			}else{
				$lista .= '<li id="'.$norma['id'].'">
							<input type="checkbox" id="norma'.$norma['id'].'" name="normas[]" value="'.$norma['id'].'" />
							'.$nombre.'
						   </li>';
			}
		}
		$lista .= '</ul>'; //fin lista normas

		$lista .= '	  </div>
					  <div class="panel" id="panelArticulos">
					  	<div class="subtitulo" id="panelArticuloTitulo">
					  		
					  		<img class="icon izquierda" src="images/previous.png" title="normas" id="VerNormasIncluidas"  >

					  		<button type="button" class="icon izquierda" onClick="SelectAllPreview(\'panelArticulos ul\',false, true);">Todo</button>

					  		<img class="icon derecha ocultos" src="images/next.png" title="Preview Datos Articulo" id="VerArticulosDatos" >

					  		Articulos
					  	</div>
					  	<ul class="listIzquierda" >
					  	</ul>
					  </div>
					  <div class="panel" id="panelArticuloDatos">
					  	<div class="subtitulo">
					  		<img class="icon izquierda" src="images/previous.png" title="normas" id="VerArticulosIncluidosA" >

					  		Datos Articulo
					  	</div>
					  	<ul>
					  		<li>a</li><li>a</li><li>a</li><li>a</li><li>a</li><li>a</li>
					  		<li>a</li><li>a</li><li>a</li><li>a</li><li>a</li><li>a</li>
					  		<li>a</li><li>a</li><li>a</li><li>a</li><li>a</li><li>a</li>
					  	</ul>
					  </div>
			  	  </div>
			  	  <script>
			  	  	InitNormasIncluidas();
			  	  </script>';
	}else{
		$lista .= '<div clas="datos-preview">
					  <p class="tip">
					  	No hay Normas para esta categoria.<br/><br/>
					  	Asegurese de incluir normas en Edición -> Normas<br/>
					  	Y de asociarlas con una categoria en Edición -> Categorias<br/>
					  </p>
			  	  <div>';
	}

	$lista .= '</div> <!-- end preview -->
			<div class="preview-botones">
				<button type="button" onClick="LimpiarNormasIncluidas();">Limpiar</button>
				<button type="button" onClick="$.fancybox.close();">Terminar</button>
			</div>';

	echo $lista;
}

/**
* ARTICULOS INCLUIDOS DE UNA NORMA\
* @param $proyecto -> id del proyecto
* @param $norma -> id de la norma
*/
function ArticulosIncluidos($proyecto, $norma){
	$registros = new Registros();

	$datos = $registros->getRegistrosArticulos($proyecto, $norma);
	$articulos = unserialize($datos[0]['registro']);

	$disponibles = $registros->getArticulos($norma);

	/*echo $proyecto." ".$norma."<br/>";

	echo '<pre>';
	print_r($disponibles);
	echo '</pre><hr><pre>';
	print_r($articulos);
	echo '</pre>';*/

	$lista = '';

	if(!empty($disponibles)){
		
		if(empty($articulos)){
			$articulos = array();
		}
		
		$lista .= '<input type="hidden" id="norma" value="'.$norma.'" >';

		foreach ($disponibles as $fila => $articulo) {
			
			if( in_array($articulo['id'], $articulos)){

				$lista .= '<li class="seleccionada" id="'.$articulo['id'].'">
							<input checked type="checkbox" id="articulo'.$articulo['id'].'" name="articulos[]" value="'. $articulo['id'] .'" />
							'.$articulo['nombre'].'
						   </li>';
			}else{
				$lista .= '<li id="'.$articulo['id'].'">
							<input type="checkbox" id="articulo'.$articulo['id'].'" name="articulos[]" value="'. $articulo['id'] .'" />
							'.$articulo['nombre'].'
						   </li>';
			}
		}

	}else{
		$lista .= 'No hay Articulos';
	}

	echo $lista;
}

/******************** REGISTROS ******************/

/**
* REGISTRA NORMAS INCLUIDAS O SELECCIONADAS
* @param $proyecto -> id proyecto
* @param $categoria -> id de la categoria de las normas
* @param $normas -> array[] -> con el orden de como se deben guardar
*/
function RegistrarNormasIncluidas($proyecto, $categoria, $normas){
	$registros = new Registros();

	if($normas == ''){
		$normas = array();
	}

	if( !$registros->RegistrarRegirstroNorma($proyecto, $categoria, $normas) ){
		echo 'Error: preview.php RegistrarNormasIncluidas()<br/>No se pudo guardar los resgistros de las normas incluidas.<br/>Proyecto: '.$proyecto;
	}
}

/**
* REGISTRAR ARTICULOS INCLUIDOS DE UNA NORMA
* @param $proyecto -> id del proyecto
* @param $norma -> id del al norma
* @param $articulos -> array[] con los sarticulos, en orden
*/
function RegistrarArticulosIncluidos($proyecto, $norma, $articulos){
	$registros = new Registros();

	if($articulos == ''){
		$articulos = array();
	}

	if( !$registros->RegistrarRegirstroArticulo($proyecto, $norma, $articulos) ){
		echo 'Error: preview.php RegistrarNormasIncluidas()<br/>No se pudo guardar los resgistros de las normas incluidas.<br/>Proyecto: '.$proyecto;
	}
}


/******************* PREVIEW ARTICULO *****************/

/**
* COMPONE EL PRVIEW CON LOS DATOS DE UN ARTICULO, LA EDICION ESTA DESHABILITADA
* @param $id -> id del articulo
*/
function PreviewArticulo($id){
	$registros = new Registros();

	$datos = $registros->getArticulo($id);
	
	$preview = '';

	if(!empty($datos)){
		$entidades = unserialize( $datos[0]['entidad'] );

		$articulo = base64_decode( $datos[0]['articulo'] );
		$resumen = base64_decode( $datos[0]['resumen'] );
		$permisos = base64_decode( $datos[0]['permisos'] );
		$sanciones = base64_decode( $datos[0]['sanciones'] );

		$archivos = $registros->getArchivosArticulo($datos[0]['id']);

		$preview .= '<table>
						<tr>
							<td>
								Nombre
							</td>
							<td>
								'.$datos[0]['nombre'].'
							</td>
						</tr>
						<tr>
							<td>
								Entidades
							</td>
							<td>';

		$preview .= Entidades( $entidades );

		$preview .= '		</td>
						</tr>
					</table>
					
					<!-- tabs para los datos -->
					<div id="tabs">
						<ul>
							<li>
								<a href="#tabs-1" title="Articulos Del Articulo">
								Articulos
								</a>
							</li>
							<li>
								<a href="#tabs-2" title="Resumen Del Articulo">
								Resumen
								</a>
							</li>
							<li>
								<a href="#tabs-3" title="Sanciones Del Articulo">
								Sanciones
								</a>
							</li>
							<li>
								<a href="#tabs-4" title="Permiso o Documentación Asociada Del Articulo">
								Permiso o Documentación
								</a>
							</li>
						</ul>

						<div id="tabs-1">
							<div class="texto">
							'.$articulo.'
							</div>
						</div>
						<div id="tabs-2">
							<div class="texto">
							'.$resumen.'
							</div>
						</div>
						<div id="tabs-3">
							<div class="texto">
							'.$sanciones.'
							</div>
						</div>
						 <div id="tabs-4">
							<div class="texto">
							'.$permisos.'
							</div>
						</div>

					</div>
					<!-- end tabs -->';
		
		if(!empty($archivos)){
			
			$preview .= '<div class="adjuntos">
							<ul>';
			foreach ($archivos as $fi => $archivo) {
				
				$preview .= '<li id="adjuntado'.$archivo['id'].'">
								<a href="src/download.php?link='.$archivo['link'].'">
									'.$archivo['nombre'].'
									<img src="images/folder.png">
								</a>
								</li>';
			}
			
			$preview .= '</ul>
					</div><!-- end archivos -->';
		}

	}else{
		$preview .= 'No hay datos
			<script>
			notificaError("Error: preview.php PreviewArticulo() id: '.$id.'<br/>No se encontraron datos del articulo seleccionado.<br/>Intente de nuevo.")
			</script>';
	}

	echo $preview;
}

/**
* COMPONE LAS ENTIDADES DE UN ARTICULO
* @param $entidades -> array[]
* @return $lista -> lista compuesta
*/
function Entidades($entidades){
	$registros = new Registros();

	$datos = $registros->getEntidades();

	$lista = '<input type="text" id="entidades" value="';
	if( !empty($datos) ){
		
		foreach ($datos as $f => $entidad) {
			
			if( in_array($entidad['id'], $entidades)){
				$lista .= $entidad['nombre'].', ';
			}else{
				continue;
			}
		}
	}
	
	$lista .= '" >';

	return $lista;
}

?>