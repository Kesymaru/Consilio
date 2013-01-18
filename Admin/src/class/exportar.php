<?php

/**
 * MANEJO DE DATOS REGISTROS Y CATEGORIAS
 */

require_once("classDatabase.php");
require_once("session.php");
require_once("proyectos.php");
require_once("usuarios.php");
require_once("registros.php");
require_once("../html2pdf.class.php");

$exportar = new Exportar();

if(isset($_GET['id']) && isset($_GET['tipo'])){
	$tipo = $_GET['tipo'];

	if($tipo == 'excel'){
		$exportar->ExportarExcel($_GET['id']);

	}else if($tipo == 'pdf'){
		$exportar->ExportarPdf($_GET['id']);

	}else if($tipo == 'html'){
		$exportar->Informe($_GET['id']);

	}

}

//exporta clientes
if( isset($_GET['tipo'])){
	$tipo = $_GET['tipo'];

	if($tipo == 'clientes'){
		$exportar->ExportarClientes();

	}
}

/**
* CLASE PARA EXPORTAR UN INFOME
*/
class Exportar{ 
	private $id = ''; //id proyecto
	private $informe = ""; //informe compuesto
	private $nombreProyecto = ''; 
	private $registros = array();
	
	public function __construct(){
		$session = new Session();
		//seguridad que este logueado
		$session->Logueado();

		date_default_timezone_set('America/Costa_Rica');
	}

	/**
	* EXPORTAR CLIENTES EN CSV
	*/
	public function ExportarClientes(){
		$base = new Database();
		$query = "SELECT nombre, email, telefono, skype FROM clientes";

		$clientes = $base->Select($query);

		$lista = "First Name,E-mail Address,Primary Phone,Notes,\n";

		if(!empty($clientes)){
			
			foreach ($clientes as $fila => $cliente) {
				$lista .= $cliente['nombre'].",".$cliente['email'].",".$cliente['telefono'].",";

				if($cliente['skype'] != ""){
					$lista .= "IM: SKYPE: ".$cliente['skype'].",\n";
				}else{
					$lista .= ",\n";
				}
			}

			$lista .="\r";

		}else{
			$lista = "No hay clientes.";
		}

		echo $lista;

		header("Content-type: text/csv");
		header("Pragma: no-cache");
		header("Expires: 0");

		//nombre lleva la fecha de la generacion
		$nombre = "ClientesMatriz".date('d_m_Y-H_m_s');
		header("Content-disposition: attachment; filename=".$nombre.".csv");

	}

	/**
	* EXPORTA EL INFORME CREADO
	* @param $proyecto -> id del proyecto ha ser exportado
	*/
	public function ExportarExcel($proyecto){
		$this->id = $proyecto;

		$this->CrearInforme(); //compone el informe

		header('Content-Description: File Transfer'); 
		header("Content-Type: application/vnd.ms-excel");
		
		//descarga el archivo
		$nombreArchivo =  str_replace(' ', '_', $this->nombreProyecto);

		header("Content-disposition: attachment; filename=".$nombreArchivo.".xls");

		echo $this->informe;
	}

	/**
	* EXPORTA EN PDF
	* @param $proyecto -> id del proyecto
	*/
	public function ExportarPdf($proyecto){
		
		$this->id = $proyecto;

		$this->CrearInforme();

		$nombreArchivo =  str_replace(' ', '_', $this->nombreProyecto);

		//combierte el html a pdf-> utiliza html2pdf class
	    ob_start();
	    $content = ob_get_clean();
	    $content = $this->informe;
	    
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

	    //forza la descarga del PDF
		header('Content-Description: File Transfer'); 
		header("Content-Type: application/pdf");
		header("Content-disposition: attachment; filename=".$nombreArchivo.".pdf");
	}

	/**
	* MUESTRA EL INFORME EN HTML
	*/
	public function Informe($proyecto){
		$this->id = $proyecto;

		$this->CrearInforme();

		echo $this->informe;
	}

	/**
	* CREA EL INFORME
	* @return true si se creo el informe.
	* @return false si fallo la creacion del informe
	*/
	private function CrearInforme(){

		//obtiene toda la informacion del proyecto
		$registro = new Registros();
		$this->registros = $registro->getRegistros($this->id);

		if(!empty($this->registros)){
			$this->Cabecera();
			$this->CuerpoRegistros();
			$this->CuerpoNotas();
			$this->Footer();

			//aplica el estilo al informe
			$this->Style();

			return true;
		}else{
			return false;
		}
	}

	/**
	* CREA LA CABEZERA DEL INFORME
	*/
	private function Cabecera(){
		$proyecto = new Proyectos();
		$this->informe = '<table class="Informe">
							<tr>
								<td class="SuperTitulo">
									'.$proyecto->getProyectoDato("nombre", $this->id).'
								</td>
							</tr>
							<tr>
								<td class="SubTitulo">
									Nombre
								</td>
								<td class="SubTitulo" colspan="4">
									Descripcion
								</td>
								<td class="SubTitulo">
									Fecha
								</td>
							</tr>
							<tr>
								<td class="SubTitulo">
									'.$proyecto->getProyectoDato("nombre", $this->id).'
								</td>
								<td class="SubTitulo" colspan="4">
									'.$proyecto->getProyectoDato("descripcion", $this->id).'
								</td>
								<td class="SubTitulo">
									'.$proyecto->getProyectoDato("fecha", $this->id).'
								</td>
							</tr>
						  </table>';
	}

	/**
	* CREA EL CUERPO PARA LAS GENERALIDADES
	*/
	private function CuerpoRegistros(){
		$registro = new Registros();
		$colspan = $this->getColspan();

		$this->informe .= '<table class="Informe">
							<tr>
								<td colspan="'.$colspan.'" class="SuperTitulo">
									Generalidades
								</td>
							</tr>';
		
		foreach ($this->registros as $f => $v) {
			//TITULO DE CATEGORIA
			$this->informe .= '<tr>
								<td colspan="'.$colspan.'" class="SubTitulo">
									'.$this->registros[$f]['categoria'].'
								</td>
							  </tr>';

			//CATEGORIAS
			$this->informe .= '<tr>';
			foreach ($this->registros[$f]['normas'] as $fi => $normas) {
				
				foreach ($normas as $campo => $valor) {
					if($campo == 'campo'){
						//echo $registro->getCampo($valor);
						$cabezera = $registro->getCampo($valor);
						$this->informe .= '<td class="Categoria">'.$cabezera.'</td>';
					}else{
						continue;
					}
				}
				
			}
			$this->informe .= '</tr>';


			//DATOS 
			$this->informe .= '<tr>';
			foreach ($this->registros[$f]['normas'] as $fi => $normas) {
				
				foreach ($normas as $campo => $valor) {
					if($campo == 'contenido'){
						$this->informe .= '<td class="Dato1">'.$valor.'</td>';
					}else{
						continue;
					}
				}
				
			}
			$this->informe .= '</tr>';

		}

		$this->informe .= '</table>';

	}

	/**
	* NUMERO DE CAMPOS DE LAS NORMAS
	*/
	private function getColspan(){
		$colspan = 0;
		foreach ($this->registros as $f => $campo) {
			if( $colspan < sizeof($this->registros[$f]['normas']) ){
				$colspan = sizeof($this->registros[$f]['normas']);
			}
		}
		return $colspan;
	}

	/**
	* CREA EL CUERPO, CARGA LAS GENERALIDADES Y NOTAS
	*/
	private function CuerpoNotas(){
		
	}

	/**
	* COMPONE EL FOOTER DEL INFORME
	* MUESTRA INFORMACION
	*/
	private function Footer(){
		$this->informe .= '<table class="Footer">
							<tr>
								<td colspan="6" class="SubTitulo">
									Generado Automaticamente
								</td>
							</tr>
							<tr>
								<td class="FooterTd" >
									Fecha:
								</td>
								<td colspan="3" class="FooterTd" >
									'.date("F j Y - g:i a").'
								</td>
								<td rowspan="3" colspan="2" class="FooterImage" >
									<img src="'.$_SESSION['home'].'/images/logoExcel.png" />
								</td>
							</tr>
							<tr>
								<td class="FooterTd" >
									Por:
								</td>
								<td colspan="3" class="FooterTd">
									'.$_SESSION['nombre'].'
								</td>
							</tr>
							<tr>
								<td class="FooterTd" >
									Generado en:
								</td>
								<td colspan="3" class="FooterTd" >
									<a href="'.$_SESSION['home'].'">Escala.com</a>
								</td>
							</tr>
							</table>';
	}

	/**
	* PARA CUANDO NO HAY DATOS
	*/
	private function LineaBasia(){
		$this->informe .= '<tr>
								<td colspan="6" class="Empty" >
									<hr class="HrEmpty">
								</td>
							</tr>';
	}

	/**
	* APLICA EL TEMA DE COLORES AL INFORME
	*/
	private function Style(){
		$tema = array(
			'class="Informe"' => 'style="width:100%; border: 0px solid transparent; font-size: 14pt;"',

			'class="SuperTitulo"' => 'style="background-color: #6fa414; font-bold: bold; color: #fff; font-size: 18pt; text-align: center;"',
			'class="SubTitulo"' => 'style="background-color: #a1ca4a; color: #fff; font-bold: bold; font-size: 16pt; text-align: center;"',

			'class="Categoria"' => 'style="background-color: #f4f4f4; font-bold: bold; color: #757273; font-size: 18pt; text-align: center;"',

			'class="Dato1"' => 'style="background-color: #fff; color: #757374; text-align: left; font-size: 14pt;  vertical-align: middle;"',
			'class="Dato2"' => 'style="background-color: #f4f4f4; color: #757374; text-align: left; font-size: 14pt;  vertical-align: middle;"',

			'class="Footer"' => 'style="width:100%; border: 0px solid transparent; font-size: 14pt; background-color: #D5D4D5; color: #333333; text-align: left;"',
			'class="FooterImage"' => 'style="background-color: #D5D4D5; color: #333; text-align: center;"',

			'class="Empty"' => 'style="text-align: center; width: 100%;"',
			'class="HrEmpty"' => 'style="text-align: center; border: 1px solid #757273; width: 70%;"'
			);

		foreach ($tema as $class => $style) {
			$this->informe = str_replace( $class, $style, $this->informe);
		}
	}

}


?>