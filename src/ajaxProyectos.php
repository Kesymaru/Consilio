<?php

/**
* AJAX PARA PROYECTOS
*/
require_once("class/proyectos.php");
require_once("class/imageUpload.php");

if(isset($_POST['func'])){

	switch ($_POST['func']){

		/*
			Proyectos
		*/

		//EDICION DE PROYECTO
		case 'EditarProyecto':
			if(isset($_POST['ProyectoId'])){
				$proyecto = new Proyectos();
				echo $proyecto->EditarProyecto($_POST['ProyectoId']);
			}
			exit;

		// LISTA DE PROYECTOS
		case 'ListaProyectos':
			$proyectos = new Proyectos();
			echo $proyectos->Lista();
			exit;

		//NUEVO PROYECTO
		case 'NuevoProyecto':
			$proyecto = new Proyectos();
			echo $proyecto->EditarNuevoProyecto();
			exit;

	}
}

// SUDIDA DE ARCHIVOS
$notificacion = "";

/*** EDICION DE PROYECTO ***/

if( isset($_FILES['ProyectoImagen']['tmp_name']) && isset($_POST['ProyectoId']) ){
	$imagen = $_FILES['ProyectoImagen'];
	$id = $_POST['ProyectoId'];
			
	//actualiza la imagen
	$proyecto = new Proyectos();
	if($_FILES['ProyectoImagen']['tmp_name'] != null && $_FILES['ProyectoImagen']['tmp_name'] != ""){
		if(!$proyecto->setProyectoImagen($imagen, $id)){
			$notificacion .= "Error al actualizar la imagen del proyecto.<br/>";
		}
	}
}


//actualiza el nombre del proyecto
if( isset($_POST['ProyectoId']) && isset($_POST['ProyectoNombre']) ){
	$proyecto = new Proyectos();
	$nombre = $proyecto->getProyectoDato("nombre", $_POST['ProyectoId'] );

	if($nombre != $_POST['ProyectoNombre']){
		if( !$proyecto->setProyectoDato("nombre", $_POST['ProyectoNombre'], $_POST['ProyectoId'])){
			$notificacion .= "Error al actualizar el nombre del proyecto.<br/>";
		}
	}
}

//actualiza cliente
if( isset($_POST['ProyectoId']) && isset($_POST['ProyectoCliente']) ){
	$proyecto = new Proyectos();
	$cliente = $proyecto->getProyectoDato("cliente", $_POST['ProyectoId'] );

	if($cliente != $_POST['ProyectoCliente']){
		if( !$proyecto->setProyectoDato("cliente", $_POST['ProyectoCliente'], $_POST['ProyectoId'])){
			$notificacion .= "Error al actualizar el cliente del proyecto.<br/>";
		}
	}
}

//actualiza la descripcion del proyecto
if( isset($_POST['ProyectoId']) && isset($_POST['ProyectoDescripcion']) ){
	$proyecto = new Proyectos();
	$descripcion = $proyecto->getProyectoDato("descripcion", $_POST['ProyectoId'] );

	$descripcionNueva = mysql_real_escape_string($_POST['ProyectoDescripcion']);

	if($descripcion != $descripcionNueva){
		if( !$proyecto->setProyectoDato("descripcion", $descripcionNueva, $_POST['ProyectoId'])){
			$notificacion .= "Error al actualizar la descripcion del proyecto.<br/>";
		}
	}
}

//notificaciones de errores
if($notificacion != ""){
	echo $notificacion;
}


?>