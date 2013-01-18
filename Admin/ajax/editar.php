<?php
	require_once("../db.php"); 
/*
	vista para editar, seleccionar y cargar informacion de las categorias seleccionadas en el proyecto
*/

//logueo -> seguridad por si el usuario no esta logueado

if( !isset($_SESSION['logueado']) ){
	$home = $_SESSION['home']."/login.php";
	header('Location: '.$home);
	exit;
}


if( isset($_GET['proyecto']) && isset($_GET['categoria']) ){
	//muestra vista de edicion
	editar($_GET['proyecto'], $_GET['categoria']);
}


//muestra vista de edicion
// @param proyecto -> id del proyecto
// @param categoria -> id de la categoria seleccionada
function editar($proyecto, $categoria){

	//si se ha seleccionado una categoria
	if($categoria == 0){
		//no se ha seleccionado categoria
		echo '<div>Selecciona o agrega una Categoria para editar</div>';
	}else{
		//edicion
		?>
		<div id="nivel1">

				<div id="listaNormas">
					<?php 
					$norma = listaNormas($categoria); 
					?>
				</div>
				<div id="generalidades">
					<?php generalidades(); ?>
					<script type="text/javascript">
						//inicializa opciones
						$( ".opciones" ).buttonset();
						$( "#opcion" ).click(seleccionaGeneralidad());
					</script>
				</div>

		</div><!-- end nivel 1-->

			<div id="nivel2">
				<div id="columna1">
					<!-- -->
					<?php descripcionNorma($norma); ?>
					<!-- -->
				</div> <!-- end columna1-->

				<div id="columna2">
					<!-- 
					<div class="box">
						TODO ajax para mostrar informacion de subcategorias<br/>
						TODO mansory para acomodar las columnas
					</div>
					<div class="box">
						TODO ajax para mostrar informacion de subcategorias<br/>
						TODO mansory para acomodar las columnas
						<br/>
						<br/>
					</div>
					<div class="box">
						TODO ajax para mostrar informacion de subcategorias<br/>
						TODO mansory para acomodar las columnas
						<br/>
						<br/>
						<br/>
						<br/>
					</div>
					<div class="box">
						TODO ajax para mostrar informacion de subcategorias<br/>
						TODO mansory para acomodar las columnas
					</div>
					 MODELO PARA BOX -->
					
				</div><!--end columna2 -->

			</div><!-- end nivel 2-->
		<?php
	}
}


?>