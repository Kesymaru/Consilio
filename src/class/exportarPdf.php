<?php
/* EXPORTA PROYECTO PARA EXCEL */
session_start();

//logueo
if( !isset($_SESSION['logueado']) ){
	$redireccion = "login.php";
	echo "<script type='text/javascript'>top.location.href = '$redireccion';</script>";
	exit;
}

if(isset($_GET['id'])){
	exportarPdf($_GET['id']);
}

function exportarPdf($id){
	//obtiene el html
    ob_start();
    include(dirname(__FILE__).'/pdf.php');
    $content = ob_get_clean();

    // convierte en PDF
    require_once(dirname(__FILE__).'/src/html2pdf.class.php');
    try{

        $html2pdf = new HTML2PDF('P', 'A4', 'es');

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

?>