<?php

require_once("class/session.php");

/**
* CLASE PARA REALIZAR DESCARGAS
*/
error_reporting(0);

class Download{
	
	function __construct($link){
		//$session = new Session();

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
	* @param $file -> link del archivo
	*/
	private function Descargar2($file){
		
		//difine el link y el archivo
		$info = pathinfo($file);
			
		echo '<pre>info: '; print_r($info); echo '</pre>';
		//echo 'tamano: '.filesize($file);
		echo 'nombre: '.basename($file);

		//si el archivo existe
		if( $this->ExisteArchivo($file) ){
			echo 'esxiste';

			header('Content-Description: File Transfer');
		    header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename='.basename($file));
		    header('Content-Transfer-Encoding: binary');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    header('Content-Length: ' . filesize($file));
		    
		    ob_clean();
		    flush();

		    readfile($file);
		    exit;

		}else{
			echo 'no existe';
		}
	}

	/**
	* DETERMINA SI UN ARCHIVO EXISTE
	* @param string $file -> link del archivo
	* @return boolean true -> is existe
	*/
	private function ExisteArchivo($file){
		$file_headers = @get_headers($file);
		
		if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
		    return false;
		}else {
		    return true;
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