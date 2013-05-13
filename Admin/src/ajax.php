<?php

//base de datos
require_once("master.php");
require_once("class/session.php");

switch ($_POST['func']){

	case 'ListarNormas':
		if( isset($_POST['id']) ){
			$master = new Master();
			//$master->ListarNormas($_POST['id']));
		}
		break;

	case 'generalidades':
		generalidades();
		break;

	case 'descripcionNorma':
		if(isset($_POST['id'])){
			descripcionNorma($_POST['id']);
		}
		break;
	case 'seleccionaGeneralidad':
		if( isset($_POST['superId']) && isset($_POST['id'])){
			seleccionaGeneralidad( $_POST['superId'], $_POST['id'] );
		}
		break;

	//busqueda
	case 'Buscar':
		if( isset($_POST['id'])){
			$master = new Master();
			$master->Buscar($_POST['id']);
		}
		break;

	/* 
		PROYECTOS 
	*/
	case 'getProyectos':
		echo json_encode(getProyectos());
		break;

	case 'nuevoProyecto':
		if(isset($_POST['nombre']) && isset($_POST['descripcion'])){
			nuevoProyecto($_POST['nombre'], $_POST['descripcion']);
		}
		break;

	case 'MenuProyectos':
		$master = new Master();
		return $master->MenuProyectos();
		break;

	case 'BuscarProyecto':
		if(isset($_POST['buscar'])){
			$master = new Master();
			return $master->MenuProyectosBuscar($_POST['buscar']);
		}
		break;

	case 'nuevaNota':
		if(isset($_POST['proyecto']) && isset($_POST['nota'])){
			nuevaNota($_POST['proyecto'], $_POST['nota']);
		}
		break;

	case 'removeNota':
		if(isset($_POST['nota'])){
			removeNota($_POST['nota']);
		}
		break;

	/*
		AUTOGUARDADO DE ACTIVIDAD O CONSULTA DEL PROYECTO
	*/
	case 'actividadRegistrar':
		if(isset($_POST['proyecto']) && isset($_POST['norma']) && isset($_POST['id'])){
			actividadRegistrar($_POST['proyecto'], $_POST['categoria'], $_POST['norma'], $_POST['id']);
		}
		break;

	/*
		CARGA DE DATOS PROYECTO SELECCIONADO
	*/
	case 'menuDatos':
		if(isset($_POST['proyecto'])){
			menuDatos($_POST['proyecto']);
		}
		break;
	
	//menu normal
	case 'menu':
		menu();
		break;

	case 'listaNormasDatos':
		if(isset($_POST['id'])){

		}
		break;

	case 'proyectoControls':
		if(isset($_POST['id'])){
			echo proyectoControls($_POST['id']);
		}
		break;

	/*
		CATEGORIAS
	*/
	case 'cargarCategorias':
		if(isset($_POST['categorias'])){
			cargarCategorias($_POST['categorias']);
		}
		break;

	case 'buscarCategoriasSeleccion':
	if(isset($_POST['buscar'])){
		buscarCategoriasSeleccion($_POST['buscar']);
	}
	break;

	case 'categorias':
		categorias();
		break;
}

?>