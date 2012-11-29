<?php 
	require_once("../db.php"); 
/*
	validacion en tiempo real del nombre del proyecto
*/

//logueo
if( !isset($_SESSION['logueado']) ){
	$home = "login.php";
	echo "<script type='text/javascript'>top.location.href = '$home';</script>";
	exit;
}


$validateValue = $_REQUEST['fieldValue'];
$validateId    = $_REQUEST['fieldId'];
/*
$validateValue = '1';
$validateId    = 'id';
*/

$validateError   = "* Este proyecto ya existe";
$validateSuccess = "Nombre de proyecto disponible";


/* RETURN VALUE
$arrayToJs = array();
$arrayToJs[0] = $validateId;

if($validateValue =="karnius"){		// validate??
	$arrayToJs[1] = true;			// RETURN TRUE
	echo json_encode($arrayToJs);			// RETURN ARRAY WITH success
}else{
	for($x=0;$x<1000000;$x++){
		if($x == 990000){
			$arrayToJs[1] = false;
			echo json_encode($arrayToJs);		// RETURN ARRAY WITH ERROR
		}
	}
	
}
*/
$arrayToJs = array();
$arrayToJs[0] = $validateId;

if( valida($validateValue) ){		// no es valido
	$arrayToJs[1] = true;
	echo json_encode($arrayToJs);
}else{
	for($x=0;$x<1000000;$x++){
		if($x == 990000){
			$arrayToJs[1] = true;
			echo json_encode($arrayToJs);		// RETURN ARRAY WITH ERROR
		}
	}
	
}

function valida($valor){
	$nombres = getProyectos();
	foreach ($nombres as $nombre) {
		if($nombre == $valor){
			return true; //ya existe
		}
	}
	return false;
}

?>