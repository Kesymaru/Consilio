<?php
	require_once("../db.php"); 

/*
	Muestra un vista amigable y con opciones para la exportacion 
	muestra opciones de compartir el informe del proyecto
*/

//logueo
if( !isset($_SESSION['logueado']) ){
	$home = $_SESSION['home']."/login.php";
	header('Location: '.$home);
	exit;
}

if(isset($_GET['proyecto'])){
	compartirMenu($_GET['proyecto']);
}

//muestra el dashboard
// @param $proyecto -> id del proyecto
function compartirMenu($proyecto){

	echo '<div class="box">
		<div class="titulo">Exportar o Compartir</div>
		<br/>
		Puedes exportar el informe en archivo excel o pdf.
		 Seleccione un formato para exportar.
		<br/><br/>

		<div id="iconsExportar">
			<img class="icon" src="images/excel.png" alt="Excel" title="Excel" onClick="exportarProyecto('.$proyecto.')">
			<img class="icon" src="images/pdf.png" alt="PDF" title="PDF" onClick="exportarProyectoPdf('.$proyecto.')">
		</div>

		<br/><br/>
		<hr> o <hr>
		<br/><br/>

		Puedes compartir el informe por medio de un enlace.
		 Las personas podran ver y descargar el informe pero no editarlo.
		<br/><br/>

		<img class="icon" title="Compartir con enlace" alt="Compartir con enlace" src="images/link.png">
		
		<br/><br/>
	</div>';
}


?>