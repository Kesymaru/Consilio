<?php 
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

	<link rel="stylesheet" href="css/style.css" type="text/css">
	<link rel="stylesheet" href="css/jquery-ui-1.9.0.custom.css" type="text/css">
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css">

	<!-- plugins -->
	<link rel="stylesheet" type="text/css" href="css/jquery.contextMenu.css">
	<link rel="stylesheet" type="text/css" href="css/jquery.ui.timepicker.css">
	<link rel="stylesheet" type="text/css" href="css/selector/jquery.multiselect.css">
	<link rel="stylesheet" type="text/css" href="css/selector/jquery.multiselect.filter.css">

	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800italic,800,600,400italic,600italic,700italic' rel='stylesheet' type='text/css'>


	<!-- jquery local -->
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
	<script type="text/javascript" src="js/main.js"></script>
	<script type="text/javascript" src="js/style.js"></script>

	<!-- jquery plugins -->
	<script type="text/javascript" src="js/jquery.form.js"></script>
	<script type="text/javascript" src="js/jquery.cookie.js"></script>
	<script type="text/javascript" src="js/jquery.contextMenu.js"></script>
	<script type="text/javascript" src="js/jquery.dataTables.js"></script>

	<!-- plugin para selector -->
	<script type="text/javascript" src="js/selector/jquery.multiselect.js"></script>
	<script type="text/javascript" src="js/selector/jquery.multiselect.filter.js"></script>

<!--	<script type="text/javascript" src="js/jquery.ui.timepicker.js"></script> -->
	
	<!-- plugin para editor -->
	<script src="editor/ckeditor.js"></script>
	<script type="text/javascript" src="editor/adapters/jquery.js"></script>

</head>

<body title="hola">

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
	if(!$_SESSION['bienvenida']){
		echo '<script type="text/javascript">notifica(\'Hola '.$_SESSION['nombre'].'\')</script>';
		$_SESSION['bienvenida'] = true;
	}
?>
	<!-- dialogo emerjente -->
	<div id="dialogo">
		<div id="dialogoPrincipal">
			<a href="#" id="closeDialogo" onClick="closeDialogo()">
				<img src="images/close.png">
			</a>
			<!-- close button -->
			<div id="dialogoContenido">
				<!-- contenido AJAX -->
				
			</div>
		</div>
	</div>

	<!-- header -->
	<div id="header">
		<a href="index.php">
			<img src="images/logo.png" class="logo">
		</a>

		<div class="toolbar">
			<div id="toolbarMenu">
				<div id="usuario">
					<?php
						echo $_SESSION['nombre'];
					?>
					<ul class="dropMenu" id="menuUsuario">
						<?php
							$master->MenuAdmin();
						?>
					</ul>
				</div>
				<div id="edicion">
					Edicion
					<ul class="dropMenu" id="menuEdicion">
						<?php
							$master->MenuEdicion();
						?>
					</ul>
				</div>
				<div id="proyectos">
					Proyectos
				</div>
				<div id="cliente">
					Clientes
				</div>
			</div>

			<!-- end opciones de menu -->
			<div id="search">
				<form id="searchForm" method="get" action="index.php">
					<input type="text" class="validate[required]" data-prompt-position="bottomRight" placeholder="hacer busqueda" required="requiered" name="buscar">
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
				?>
					<div id="mensajeInicial">
						Selecione un proyecto o cree uno nuevo para empezar.
						<br/>
						<button onClick="proyectoNuevo()">Crear Proyecto</button>
						<?php
							//determina si el cliente tiene proyectos
							/*$sql = 'SELECT * FROM proyectos WHERE cliente = '.$_SESSION['id'];
							$result = mysql_query($sql);
							if($row = mysql_fetch_array($result)){
								echo '<button onClick="verProyectos()">Seleccionar Proyecto</button>';
							}*/
						?>
					</div>
				<?php 
				}

				?>
			<div id="nivel1">

				<div id="listaNormas">
					
				</div>
				<div id="generalidades">
					
				</div>

			</div><!-- end nivel 1-->

			<div id="nivel2">
				<div id="columna1">
					<!--
					<div id="descripcionNorma">
						
						<div class="nombreNorma">
							TODO titulo ajax categoria
						</div>
						<div>
							TODO descripcion
						</div>
						
					</div>
					-->
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

		</div><!-- end content -->

	</div><!-- end main -->

</body>

</html>