<?php 

/**
* INDEX DE MATRIZ PARA CLIENTE
*/

require_once("src/master.php"); 

$master = new Master();

?>

<!DOCTYPE html>
<!--[if lt IE 7]> <html lang="en-us" class="lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>    <html lang="en-us" class="lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>    <html lang="en-us" class="lt-ie9"> <![endif]-->

<html>

<head>
	<title>Escala</title>
	
	<meta charset="utf-8">

	<link rel="shortcut icon" href="/favicon.ico"> 

	<!-- style -->
	<link rel="stylesheet" href="css/style.css" type="text/css">
	<link rel="stylesheet" href="css/jquery-ui-1.9.0.custom.css" type="text/css">
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css">

	<!-- style plugins -->
	<link rel="stylesheet" type="text/css" href="css/jquery.contextMenu.css">
	<link rel="stylesheet" type="text/css" href="css/jquery.ui.timepicker.css">
	<link rel="stylesheet" type="text/css" href="css/chosen.css">
	<link rel="stylesheet" type="text/css" href="css/selector/jquery.multiselect.css">
	<link rel="stylesheet" type="text/css" href="css/selector/jquery.multiselect.filter.css">
	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css">
	<link rel="stylesheet" type="text/css" href="css/jquery.gridster.css">

	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800italic,800,600,400italic,600italic,700italic' rel='stylesheet' type='text/css'>


	<!-- jquery local para desarrollo -->
	<script type="text/javascript" src="js/jquery-1.8.2.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.9.0.custom.js"></script>
	

	<!-- jquery google 
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.9.0.custom.js"></script>
	-->

	<!-- validacion de form -->
	<script type="text/javascript" src="js/languages/jquery.validationEngine-es.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/jquery.validationEngine.js" charset="utf-8"></script>

	<!-- placeholder para ie -->
	<script src="js/jquery.placeholder.js" type="text/javascript"></script>

	<!-- notificaciones -->
	<script type="text/javascript" src="js/noty/jquery.noty.js" ></script>
	<script type="text/javascript" src="js/noty/layouts/topCenter.js"></script>
	<script type="text/javascript" src="js/noty/layouts/center.js"></script>
	<script type="text/javascript" src="js/noty/themes/default.js"></script>

	<!-- matriz -->
		<script type="text/javascript" src="js/Proyectos.js"></script>
	<script type="text/javascript" src="js/main.js"></script>

	<!-- jquery plugins -->
	<script type="text/javascript" src="js/chosen.jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.form.js"></script>
	<script type="text/javascript" src="js/jquery.cookie.js"></script>
	<script type="text/javascript" src="js/jquery.contextMenu.js"></script>
	<script type="text/javascript" src="fancybox/jquery.fancybox.js"></script>
	<script type="text/javascript" src="js/jquery.masonry.min.js"></script>
	<script type="text/javascript" src="js/jquery.gridster.js"></script>

	<!-- plugin para selector -->
	<script type="text/javascript" src="js/selector/jquery.multiselect.js"></script>
	<script type="text/javascript" src="js/selector/jquery.multiselect.filter.js"></script>

<!--	<script type="text/javascript" src="js/jquery.ui.timepicker.js"></script> -->
	
	<!-- plugin para editor -->
	<script src="editor/ckeditor.js"></script>

</head>

<body>

<div id="loader" class="loader">

	<!-- imagen animada con css para el cargador -->
	<div class="windows8">
	<div class="wBall" id="wBall_1">
	<div class="wInnerBall">
	</div>
	</div>
	<div class="wBall" id="wBall_2">
	<div class="wInnerBall">
	</div>
	</div>
	<div class="wBall" id="wBall_3">
	<div class="wInnerBall">
	</div>
	</div>
	<div class="wBall" id="wBall_4">
	<div class="wInnerBall">
	</div>
	</div>
	<div class="wBall" id="wBall_5">
	<div class="wInnerBall">
	</div>
	</div>
	</div>
	
</div>

<?php
	//muestra bienvenida una sola ves para cada logueo
	if(!$_SESSION['cliente_bienvenida']){
		echo '<script type="text/javascript">notifica(\'Hola '.$_SESSION['cliente_nombre'].'\')</script>';
		$_SESSION['cliente_bienvenida'] = true;
	}
?>
	<!-- header -->
	<div id="header">
		<a href="index.php">
			<img src="images/logo.png" class="logo">	
		</a>
		<?php
			//$master->Logo();
		?>

		<div class="toolbar">
			<?php
			$master->Logo();
			?>
			<div id="toolbarMenu">
				<div id="menuUsuario">
					<img class="icon" src="images/user.png" />
					<?php
						echo $_SESSION['cliente_nombre'];
					?>
					<ul class="dropMenu">
						<?php
							$master->MenuCliente();
						?>
					</ul>
				</div>

				<div id="menuProyectos">
					<img class="icon" src="images/list.png" />
					Proyectos
					<ul class="dropMenu">
						<?php
							$master->MenuProyectos();
						?>
					</ul>
				</div>
			</div>

			<!-- end opciones de menu -->
			<div id="search">
				<form id="searchForm" method="get" action="index.php">
					<input type="text" class="validate[required]" data-prompt-position="bottomRight" placeholder="hacer busqueda..." required="requiered" name="buscar">
					<input type="submit" name="accion">
				</form>
			</div>
			<!-- end para search -->
		</div>
		
	</div> <!-- end header -->

	<div id="main">
		
		<div id="menu">

		</div>
		<!-- end menu -->

		<div id="menu2">
			<!-- tabla para el panel donde se muestran las lista -->
			<table class="panel" >
			<tr>
				<td colspan="3" class="panel-camino"> 
					<ul id="camino">
						<li id="camino-categorias" onClick="ShowCategorias()">			Categorias /
						</li>
						<li class="ocultos" id="camino-normas" onClick="ShowNormas()">		Normas /
						</li>
						<li class="ocultos" id="camino-articulos" onClick="ShowArticulos()">		
							Articulos
						</li>

						<!-- <button class="derecha" type="button" title="Buscar" onClick="Busqueda('busqueda', 'buscar', 'panel', false)">Buscar</button> -->
						<img class="icon derecha" title="Buscar" onClick="Busqueda('busqueda', 'buscar', 'panel', false)" src="images/search2.png" />
					</ul> 
				</td>
			</tr> 
			<tr>
				<td colspan="3">
					<div class="busqueda" id="busqueda">
						<div class="buscador">
						<input type="search" title="Escriba Para Buscar Administradores" id="buscar" placeholder="Buscar"/>
						</div>
					</div>
				</td>
			</tr>
			<tr id="panel">
				<td id="td-categorias" >
				</td> 
				<td id="td-normas" >
				</td> 
				<td id="td-articulos" >
				</td>
			</tr>
			</table>
			<!-- end table -->
		</div>
		<!-- end menu 2 -->

		<div id="content">
			
				<?php
				if(isset($_GET['buscar'])){
				?>
					<!-- BUSQUEDA -->
					<div id="resultadoBusqueda">
						<script language=javascript>
							$.cookie('vista','buscar');
						</script>
						<?php
							$master->Buscar($_GET['buscar']);
						?>
						<button onClick="Home()" id="LimpiarBusqueda">Limpiar</button>
						<script type="text/javascript">
							Boton('LimpiarBusqueda');
						</script>
					</div>
				<?php
				}else if(!isset($_GET['proyecto'])){
					//MUESTRA LOS PROYECTOS DEL CLIENTE
					
					$master->Proyectos();
				?>
				<?php 
				}

				?>

		</div><!-- end content -->
	</div><!-- end main -->

<div class="disclaim" >
	<div>
		<p>2013 Escala Consultores. Todos los derechos reservados</p>
	</div>
</div>	

</body>

</html>