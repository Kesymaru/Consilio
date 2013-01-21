
<?php

require_once("class/registros.php");

if(isset($_POST['func'])){
	switch ($_POST['func']) {
		
		case 'Articulos':
			if(isset($_POST['norma']) && isset($_POST['proyecto'])){
				Articulos($_POST['proyecto'], $_POST['norma']);
			}
			break;

		//REGISTRA ARTICULOS
		case 'RegistrarArticulos':
			if( isset($_POST['proyecto']) && isset($_POST['norma']) ){
				RegistrarArticulos($_POST['proyecto'], $_POST['norma'] );
			}
			break;

		case 'RegistrarNormas':
			if( isset($_POST['proyecto']) && isset($_POST['categoria']) ){
				RegistrarNormas( $_POST['proyecto'], $_POST['categoria'] );
			}
			break;
	}
}

if(isset($_GET['categoria']) && isset($_GET['proyecto']) ){
	Cabezeras();
	Normas($_GET['categoria'], $_GET['proyecto']);
	Cierre();
}

/**
* CABEZERAS DE LA VISTA
*/
function Cabezeras(){
	?>

<html>
<head>
	<meta charset="utf-8">

	<link rel="stylesheet" href="../css/style.css" type="text/css">
	<link rel="stylesheet" href="../css/jquery-ui-1.9.0.custom.css" type="text/css">

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<script type="text/javascript" src="../js/jquery-ui-1.9.0.custom.js"></script>

	<script type="text/javascript" src="../js/preview.js"></script>
	
	<script type="text/javascript" src="../js/jquery.form.js"></script>

</head>
<body style="overflow-x: hidden;">

	<?php
}

/**
* CIERRA EL DOCUMENTO HTML Y EL BODY
*/
function Cierre(){
	?>

</body>
</html>

	<?php
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

?>