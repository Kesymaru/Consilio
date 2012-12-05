<?php

require_once("../html2pdf.class.php");

//genera un pdf
class Pdf{

	/**
	* GENERA UN ARCHIVO PDF DE UN CONTENIDO EN HTML
	* UTILIZA LA CLASE HTML2PDF
	* @param $contenido -> String con el contenido en formato html
	*/
	public function __construct($contenido){
		
		//obtiene el html
	    ob_start();
	    $content = ob_get_clean();
	    $content = $contenido;
	    
	    try{

	        $html2pdf = new HTML2PDF('P', 'A2', 'es');

	        $html2pdf->pdf->SetAuthor('Matrices Consilio');
			$html2pdf->pdf->SetTitle('Informe Proyecto');
			$html2pdf->pdf->SetSubject('informe proyecto matriz');
			$html2pdf->pdf->SetKeywords('informe, proyecto, matris');

	        $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
	        $html2pdf->Output('exportar.pdf');
	    }

	    //si pasa algun error
	    catch(HTML2PDF_exception $e) {
	        echo $e;
	        exit;
	    }
	}

	/*
	* REALIZA LA DESCARGA DEL ARCHIVO PDF GENERADO
	*/
	public function Descargar($nombreArchivo){
		header('Content-Description: File Transfer'); 
		header("Content-Type: application/pdf");
		header("Content-disposition: attachment; filename=".$nombreArchivo.".pdf");
	}
}

?>