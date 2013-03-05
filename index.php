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
	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css">

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
		<script type="text/javascript" src="js/lista.js"></script>
	<script type="text/javascript" src="js/main.js"></script>

	<!-- jquery plugins -->

	<script type="text/javascript" src="js/jquery.cookie.js"></script>

	<script type="text/javascript" src="js/jquery.scrollTo-min.js"></script>

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
					<span>Proyectos</span>
					<ul class="dropMenu">
						<?php
							$master->MenuProyectos();
						?>
					</ul>
				</div>
			</div>

			<div id="searchbar">
				<input type="search" placeholder="Buscar..." id="buscar" />
			</div>
			<!-- end opciones de menu -->
			<div id="search" title="Buscar">
				<img class="icon" src="images/search2.png" onClick="BuscarGlobal()" />
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
				<th id="panel-categorias">
					
				</th>
				<th id="panel-normas">
					
				</th>
				<th id="panel-articulos">
					<span></span>
					<img class="icon derecha" title="Buscar" onClick="Busqueda('busqueda', 'buscar', 'panel', false)" src="images/search2.png" />
				</th>
			</tr>
			<tr>
				<td colspan="3">
					<div class="busqueda" id="busqueda">
						<div class="buscador">
						<input type="search" title="Escriba par buscar" id="buscar" placeholder="Buscar"/>
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

		<!-- contenido -->
		<div id="content">
			
			<?php
			if(!isset($_GET['proyecto'])){
					//MUESTRA LOS PROYECTOS DEL CLIENTE
					$master->Proyectos();
			?>
			<?php 
			}else{
			?>
				<script>
					Proyecto(<?php echo $_GET['proyecto'];?>);
				</script>
				<?php
			}
			?>

		</div><!-- end content -->
	</div><!-- end main -->

<div class="disclaim">
	<div>
		<p>
			Estimado (a) Usuario (a): Consultores Escala es el titular exclusivo del contenido de la Interfaz de Cumplimiento. Por ende, cualquier adaptación, modificación, extracto o uso en cualquiera de sus modalidades de su contenido, requerirá la autorización expresa del titular del derecho. La presente base de datos está protegida como una compilación de acuerdo con la Ley de Derechos de Autor y Derechos Conexos.
		</p>
	</div>
</div>

</body>	
</html>