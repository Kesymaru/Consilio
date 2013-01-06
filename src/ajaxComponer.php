<?php

/**
* AJAX PARA PROYECTOS
*/

//CLASES REQUERIDAS
require_once("class/proyectos.php");
require_once("class/imageUpload.php");
require_once("class/registros.php");

if(isset($_POST['func'])){
	
	switch ($_POST['func']){

		//categorias seleccionables para el proyecto
		case "Categorias":
			Categorias();
			break;

		//DATOS DEL PROYECTO
		case 'ComponerProyecto':
			if(isset($_POST['id'])){
				ComponerProyecto($_POST['id']);
			}
			ProyectosAvance();
			break;

	}
}
/**
 * MUESTRA LAS CATEGORIAS DISPONIBLES PARA SELECCIONAR
 * EN UN PROYECTO
 */
function Categorias(){
	
}

/**
 * MUESTRA VISTA DE COMPOSICION DE UN PROYECTO
 * @param $id -> id del proyecto
 */
function ComponerProyecto($id){
	$registros = new Registros();

	$datos = $registros->getRegistros($id);

	if(!empty($datos)){

	}else{

	}
}



?>