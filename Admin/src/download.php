<?php

require_once("class/session.php");

/**
* CLASE PARA REALIZAR DESCARGAS
*/

class Download{
	
	function __construct($link){
		$session = new Session();

		//SEGURIDAD DE USUARIO LOGUEADO		
		if($session->Logueado()){

			$link = '../'.$link;
			
			//$this->Descargar( $link );
			$this->Descargar2( $link );
		}
	}

	/**
	* DESCARGA UN ARCHIVO
	* @param $nomreb -> nombre del archivo
	* @param $link -> link del archivo
	*/
	private function Descargar($link){
		
		$nombre = str_replace("../", "", $link);

		//descarga archivo
		$fp = @fopen($link, 'rb');

		if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")){
			header('Content-Type: "application/octet-stream"');
			header('Content-Disposition: attachment; filename="'.$nombre.'"');
			header('Expires: 0');
		    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
			header("Content-Length: ".filesize($link));
		}else{
			header('Content-Type: "application/octet-stream"');
			header('Content-Disposition: attachment; filename="'.$nombre.'"');
			header("Content-Transfer-Encoding: binary");
			header('Expires: 0');
			header('Pragma: no-cache');
		header("Content-Length: ".filesize($link));
		}

		fpassthru($fp);
		fclose($fp);
	}

	/**
	* REALIZA DESCARGA DE UN ARCHIVO
	* @param $link -> link del archivo
	*/
	private function Descargar2($link){
		
		//difine el link y el archivo
		$file = $link;
		$info = pathinfo($link);
			
		echo '<pre>'; print_r($info); echo '</pre>';
		
		if(!$file){
		     // archivo no existe
		     die('Archivo no encontrado.');
		}else{
		     // CABECERAS
		     /*
		     header("Cache-Control: public");
		     header("Content-Type: application/octet-strea");
		     header("Content-Description: File Transfer");
		     header("Content-Disposition: attachment; filename=$name");
		     header("Content-Transfer-Encoding: binary");
		    
		     // DESCARGA EL ARCHIVO
		     readfile($file);*/

		    header("Cache-Control: public");
		    header("Content-Type: application/force-download");
    	 	header("Content-Transfer-Encoding: Binary");
     		header("Content-Length: ".filesize($file));
     		header("Content-Disposition: attachment; filename=\"".basename($file)."\"");
     		header("Content-Description: Descarga archivo");
     		
     		// DESCARGA EL ARCHIVO
     		readfile($file);
		}
	}
}

/**
* REQUIERE PARAMETROS VIA GET 
* @param $link -> link del archivo a descargar
*/

if( isset($_GET['link']) ){
	//FORZA DESCARGA DE ARCHIVO
	$descargar = new Download( $_GET['link'] );
}else{
	//SEGURIDAD
	$session = new Session();
	$session->Logueado();
}

?>