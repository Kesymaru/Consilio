
<noscript>
<div align="center"><a href="index.php">Go Back To Upload Form</a></div><!-- If javascript is disabled -->
</noscript>

<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);

if(isset($_POST))
{
	 //Some Settings
	$BigImageMaxSize 		= 200; //tamano maximo de la imagen
	$destino	= 'users/'; //destino de la imagen
	$Quality 				= 90;

	// check $_FILES['ImageFile'] array is not empty
	// "is_uploaded_file" Tells whether the file was uploaded via HTTP POST
	if(!isset($_FILES['ImageFile']) || !is_uploaded_file($_FILES['ImageFile']['tmp_name']))
	{
		//error si falla
		die('No se ha podido subir la imagen :(');
	}

	//datos imagen
	$nombreImagen = str_replace(' ','-',strtolower($_FILES['ImageFile']['name'])); 
	$tamanoImagen = $_FILES['ImageFile']['size'];
	$TempSrc      = $_FILES['ImageFile']['tmp_name']; // Tmp name of image file stored in PHP tmp folder
	$tipoImagen   = $_FILES['ImageFile']['type']; //Obtain file type, returns "image/png", image/jpeg, text/plain etc.

	//valida, sino termina el programa
	extencionValida($tipoImagen,$nombreImagen);

	//se le agrega el id del usuario
	$nuevoNombreImagen = 'ID.png';

	$nombreImagenFinal = $destino.$nuevoNombreImagen;

	//mueve la imagen a la carpeta
	move_uploaded_file($_FILES["ImageFile"]["tmp_name"], $nombreImagenFinal);
    
    echo "Stored in:" . $nombreImagenFinal;
    echo '<script>alert("Imagen subida.");</script>';
}

//valida si la extencion es valida
function extencionValida($tipoImagen,$nombreImagen){
	switch(strtolower($tipoImagen))
	{
		case 'image/png':
			$CreatedImage =  imagecreatefrompng($_FILES['ImageFile']['tmp_name']);
			break;		
		case 'image/jpeg':
		case 'image/pjpeg':
			$CreatedImage = imagecreatefromjpeg($_FILES['ImageFile']['tmp_name']);
			break;
		default:
			//error de imagen no permitida
			die('Intenta con una imagen .png o jpeg'); 
	}
}

//verifica si existe la imagen y el tipo, si es asi la elimina
function existeImagen($destino){
	$imagen = $destino.'ID';
	if( file_exists($imagen.'.png')){
		return true;
	}
	if( file_exists($imagen.'.pjpeg')){
		return true;
	}
	if( file_exists($imagen.'.jpeg')){
		return true;
	}
}

?>