<?php
/**
* FUNCIONALIDAD DE PREVIEW, PARA CATEGORIAS, ARTICULOS Y NORMAS
* TAMBIEN PARA LA SELECCION DE NORMAS Y ARTICULOS EN COMPOSICION DE UN PROYECTO
*/

require_once("class/registros.php");

if(isset($_POST['func'])){
	switch ($_POST['func']) {
		
		//PREIEW PARA UN ARTICULO
		case 'PreviewArticulo':
			if( isset($_POST['id']) ){
				PreviewArticulo($_POST['id']);
			}

		//MUESTRA LISTA DE NORMAS INCLUIDAS
		case 'NormasIncluidas':
			if( isset($_POST['proyecto']) && isset($_POST['categoria']) ){
				NormasIncluidas($_POST['proyecto'], $_POST['categoria']);
			}
			break;

		/******** REGISTROS *********/

		//REGISTRA NORMAS INCLUIDAS
		case 'RegistrarNormasIncluidas':
			if( isset($_POST['proyecto']) && isset($_POST['categoria']) ){

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

	$lista = '<div class="preview">
			  	<div class="titulo">
			  		Categoria
			  	</div>';

	if(!empty($normas)){
		$lista .= '<div clas="datos-preview">
					  <div class="panel" id="panelNormas">
					  <div class="subtitulo">
					  	<img class="icon izquierda" src="images/previous.png" title="normas" onClick="VerNormasIncluidas()" >
					  	<img class="icon derecha" src="images/next.png" title="normas" onClick="VerArticulosIncluidos()" >

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
					  	<div class="subtitulo">
					  		<img class="icon izquierda" src="images/previous.png" title="normas" onClick="VerNormasIncluidas()" >
					  		<img class="icon derecha" src="images/next.png" title="normas" onClick="VerArticulosDatos()" >

					  		Articulos
					  	</div>
					  	<ul>
					  		<li>a</li><li>a</li><li>a</li><li>a</li><li>a</li><li>a</li>
					  		<li>a</li><li>a</li><li>a</li><li>a</li><li>a</li><li>a</li>
					  		<li>a</li><li>a</li><li>a</li><li>a</li><li>a</li><li>a</li>
					  	</ul>
					  </div>
					  <div class="panel" id="panelArticuloDatos">
					  	<div class="subtitulo">
					  		Datos Articulo
					  	</div>
					  	<ul>
					  		<li>a</li><li>a</li><li>a</li><li>a</li><li>a</li><li>a</li>
					  		<li>a</li><li>a</li><li>a</li><li>a</li><li>a</li><li>a</li>
					  		<li>a</li><li>a</li><li>a</li><li>a</li><li>a</li><li>a</li>
					  	</ul>
					  </div>
			  	  <div>
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

	$lista .= '</div> <!-- end preview -->';

	echo $lista;
}

/**
* MUESTRA LA LISTA DE LAS NORMAS DE LA CATEGORIA
* @param $categoria -> id de la categoria
* @param $proyecto -> id del proyecto
*/
function Normas($categoria, $proyecto){

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
		$incluidas = '';
	}

	//titulo
	echo '<div class="preview">
			<div class="titulo">

				<input type="hidden" name="proyecto" id="proyecto" value="'.$proyecto.'" />
				
				<img id="preview" src="../images/previous.png" class="izquierda icon" onClick="Cambio()" title="Ir Atras">
				'.$categoriaDatos[0]['nombre'].'
				<img id="next" src="../images/next.png" class="derecha icon" onClick="Articulos()" title="Ir Siguiente">
				
				
			</div>
			<div class="datos-preview">';

	$td_articulos = '
				<div id="articulos">
			  <form id="FormularioArticulos" enctype="multipart/form-data" method="post" action="previewNormas.php" >
			    <input type="hidden" name="func" value="RegistrarArticulos" />
			  	<input type="hidden" id="articulos_proyecto" name="proyecto" value="" />
			  	<input type="hidden" id="articulos_norma" name="norma" value="" />
			  	
			  	<div class="subtitulo">
			  			
			  			<button id="Selectarticulos" class="izquierda icon" onClick="SelectAll(\'articulos\')" titl="Seleccionar Todo" src="images/">Todo</button>
						<button id="Unselectarticulos" class="izquierda icon" onClick="UnSelectAll(\'articulos\')" titl="Deseleccionar Todo" src="images/">Todo</button>

				  		Articulos

						<img class="boton-buscar icon" title="Buscar Articulos" onClick="Busqueda(\'busqueda-articulos\', \'buscar-articulos\', \'articulos\', false)" src="../images/search2.png">

				</div>		
					<div class="busqueda" id="busqueda-articulos">
						<div class="buscador">
							<input type="search" title="Escriba Para Buscar Normas" id="buscar-articulos" placeholder="Buscar Articulos"/>
						</div>
					</div>

					<ul id="articulos-list">
						<li>No hay Articulos</li>
					</ul>

			  </form> <!--  end form articulos 
			  </tr>
			  </table>  end  normas y articulos -->
			  </div>
			 
			 </div><!-- end datos-preview -->

			 <div  class="preview-botones">
			 	<button type="button" id="GuardarNormas" onClick="GuardarNormas()">Guardar</button>
			 	<button type="button" id="GuardarArticulos" onClick="GuardarArticulos();">Guardar</button>
			 </div>';

	if( !empty($incluidas)){
		
		echo '<div id="normas">
				<form id="FormularioNormas" enctype="multipart/form-data" method="post" action="previewNormas.php" >
					<input type="hidden" name="func" value="RegistrarNormas" />
					<input type="hidden" id="normas_categoria" name="categoria" value="'.$categoria.'" />
					<input type="hidden" id="normas_proyecto" name="proyecto" value="'.$proyecto.'" />

					<div class="subtitulo">
						<button id="Selectnormas" class="izquierda icon" onClick="SelectAll(\'normas\')" titl="Seleccionar Todo" src="images/">Todo</button>
						<button id="Unselectnormas" class="izquierda icon" onClick="UnSelectAll(\'normas\')" titl="Seleccionar Todo" src="images/">Todo</button>
						
						Normas

						<img class="boton-buscar icon" title="Buscar Normas" onClick="Busqueda(\'busqueda-normas\', \'buscar-normas\', \'normas\', false)" src="../images/search2.png" >
					</div>

					<div class="busqueda" id="busqueda-normas">
						<div class="buscador">
							<input type="search" title="Escriba Para Buscar Normas" id="buscar-normas" placeholder="Buscar Normas"/>
						</div>
					</div>';

		echo '<ul>';

		//lista de normas de la categoria
		foreach ($normas as $f => $norma) {

			$datos = $registros->getDatosNorma($norma);

			if(!empty($datos)){
				
				$tipo = $registros->getTipoDato("nombre", $datos[0]['tipo']);

				//si esta seleccionada
				if(in_array($datos[0]['id'], $incluidas)){

					echo '<li class="seleccionada" id="'.$datos[0]['id'].'" title="'.$tipo.' #'.$datos[0]['numero'].'"  >
					<input checked type="checkbox" id="norma'.$datos[0]['id'].'" name="normas[]" value="'.$datos[0]['id'].'" />
					'.$datos[0]['nombre'].'
					</li>';

				}else{
					echo '<li id="'.$datos[0]['id'].'" title="'.$tipo.' #'.$datos[0]['numero'].'"  >
					<input type="checkbox" id="norma'.$datos[0]['id'].'" name="normas[]" value="'.$datos[0]['id'].'" />
					'.$datos[0]['nombre'].'
					</li>';
				}
			}
		}

		echo '</ul>
			  </form><!-- end form normas -->
			  </div>';
			  
		echo $td_articulos;

	}else{
		//NO HAY INCLUIDAS
		
		if(!empty($normas)){

			echo '<div id="normas">
				<form id="FormularioNormas" enctype="multipart/form-data" method="post" action="previewNormas.php" >
					<input type="hidden" name="func" value="RegistrarNormas" />
					<input type="hidden" id="normas_categoria" name="categoria" value="'.$categoria.'" />
					<input type="hidden" id="normas_proyecto" name="proyecto" value="'.$proyecto.'" />

					<div class="subtitulo">
						
						<button id="Selectnormas" class="izquierda icon" onClick="SelectAll(\'normas\')" titl="Seleccionar Todo" src="images/">Todo</button>
						<button id="Unselectnormas" class="izquierda icon" onClick="UnSelectAll(\'normas\')" titl="Seleccionar Todo" src="images/">Todo</button>

						Normas

						<img class="boton-buscar icon" title="Buscar Normas" onClick="Busqueda(\'busqueda-normas\', \'buscar-normas\', \'normas\', false)" src="../images/search2.png" >
					</div>

					<div class="busqueda" id="busqueda-normas">
						<div class="buscador">
							<input type="search" title="Escriba Para Buscar Normas" id="buscar-normas" placeholder="Buscar Normas"/>
						</div>
					</div>';

		echo '<ul>';

		//lista de normas de la categoria
		foreach ($normas as $f => $norma) {

			$datos = $registros->getDatosNorma($norma);

			if(!empty($normas)){
				$tipo = $registros->getTipoDato("nombre", $datos[0]['tipo']);
				echo '<li id="'.$datos[0]['id'].'" title="'.$tipo.' #'.$datos[0]['numero'].'"  >
				<input type="checkbox" id="norma'.$datos[0]['id'].'" name="normas[]" value="'.$datos[0]['id'].'" />
				'.$datos[0]['nombre'].'
				</li>';
			}
		}

		echo '</ul>
			  </form><!-- end form normas -->
			  </div>';

		echo $td_articulos;

		}else{
			echo 'No hay Normas.
				 </div><!-- end datos-preview -->';
		}
	}

	echo '</div><!-- end -->';
}

/**
* REGISTRA LAS NORMAS SELECCIONADAS
*/
function RegistrarNormas($proyecto, $categoria){
	$registros = new Registros();

	if(isset($_POST['normas'])){
		$incluidos = $_POST['normas'];

		//registra sino existe sino actualiza
		if( !$registros->RegistrarRegirstroNorma($proyecto, $categoria, $incluidos) ){
		}
	}else{
		//no se selecciono nada
		$incluidos = array();

		if( !$registros->RegistrarRegirstroNorma($proyecto, $categoria, $incluidos) ){
		}
	}
}


/**
 * ARTICULOS DE UNA NORMA
 * @param $norma -> id norma
 */
function Articulos($proyecto, $norma){
	$registros = new Registros();

	$datos = $registros->getArticulos($norma);
	
	//obtiene los articulos ya incluidos
	$datosIncluidas = $registros->getRegistrosArticulos($proyecto, $norma);

	if(!empty($datosIncluidas)){
		$incluidas = unserialize($datosIncluidas[0]['registro']);
	}else{
		$incluidas = '';
	}

	if( !empty($incluidas) ){

		if(!empty($datos)){

			foreach ($datos as $fila => $articulo) {
				
				//si esta incluida
				if(in_array($articulo['id'], $incluidas)){

					echo '<li class="seleccionada" id="'.$articulo['id'].'" onClick="SelectArticulo('.$articulo['id'].')">
						
						<input checked id="articulo'.$articulo['id'].'" type="checkbox" name="articulos[]" value="'.$articulo['id'].'" />

						'.$articulo['nombre'].'
					</li>';

				}else{

					echo '<li id="'.$articulo['id'].'" onClick="SelectArticulo('.$articulo['id'].')">
						<input id="articulo'.$articulo['id'].'" type="checkbox" name="articulos[]" value="'.$articulo['id'].'">
						'.$articulo['nombre'].'
					</li>';

				}

			}

		}else{
			echo '<li>No hay Articulos</li>';
		}

	}else{

		//no hay incluidas
		if(!empty($datos)){

			foreach ($datos as $fila => $articulo) {
				echo '<li id="'.$articulo['id'].'" title="" onClick="SelectArticulo('.$articulo['id'].')">
					<input id="articulo'.$articulo['id'].'" type="checkbox" name="articulos[]" value="'.$articulo['id'].'">
					'.$articulo['nombre'].'
				</li>';
			}

		}else{
			echo '<li>No hay Articulos</li>';
		}

	}
		
}


/**
* REGISTRA LOS ARTICULOS SELECCIONADOS
*/
function RegistrarArticulos($proyecto, $norma){
	$registros = new Registros();
	
	if(isset($_POST['articulos'])){
		$incluidos = $_POST['articulos'];

		//registra sino existe sino actualiza
		if( !$registros->RegistrarRegirstroArticulo($proyecto, $norma, $incluidos) ){
		}
	}else{
		//no se selecciono nada
		$incluidos = array();

		if( !$registros->RegistrarRegirstroArticulo($proyecto, $norma, $incluidos) ){
		}
	}
	
}

/**************************** PREVIEW PARA ARTICULOS *********************/

/**
* MUESTRA UN PREVIEW CON LOS DATOS DE UN ARTICULO PARA CONSULTAR
* @param $id -> id del articulo
*/
function PreviewArticulo($id){
	$registros = new Registros();

	$datos = $registros->getArticulo($id);

	if(!empty($datos)){
		$lista = '<div class="titulo preview-datos">
				  	'.$datos[0]['nombre'].'
			     </div>
			     <div class="datos-preview">

			     </div>';
	}else{
		$lista .= 'Error: no hay datos para el articulo id: '.$id.'
				<script>
					notificaError("Error: preview.php PreviewArticulos() no hay datos para id :'.$id.'");
				</script>';
	}

	echo $lista;
}

/**
* COMPONE LAS ENTIDADES SELECCIONADAS PARA EL ARTICULO
* @param $entidades -> array con los ids de las entidades
* @return $select -> select con 
*/ 
function PreviewEntidades(){

}

?>