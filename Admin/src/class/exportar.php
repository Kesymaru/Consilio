<?php

/**
 * MANEJO DE DATOS REGISTROS Y CATEGORIAS
 */

//require_once("classDatabase.php");
require_once("session.php");
require_once("proyectos.php");
require_once("usuarios.php");
require_once("registros.php");
require_once("html2pdf.class.php");

if( isset($_GET['id']) && isset($_GET['tipo'])){
	$exportar = new Exportar();
	$tipo = $_GET['tipo'];
	
	if($tipo == 'excel'){
		$exportar->ExportarExcel($_GET['id']);

	}else if($tipo == 'pdf'){
		$exportar->ExportarPdf($_GET['id']);

	}else if($tipo == 'html'){
		$exportar->Informe($_GET['id']);
	}else if($tipo == 'pdfcliente'){
		//exporta el informe para el cliente del proyecto
		$exportar->ExportarPdfCliente( $_GET['id'] );
	}

}

//exporta clientes
if( isset($_GET['tipo'])){
	$exportar = new Exportar();
	$tipo = $_GET['tipo'];

	if($tipo == 'clientes'){
		$exportar->ExportarClientes();
	}
}

/**
* CLASE PARA EXPORTAR UN INFOME
*/
class Exportar{ 
	private $proyecto = ''; //id proyecto
	private $cliente = '';
	private $clienteId = '';
	private $formato = '';
	private $informe = ""; //informe compuesto
	private $nombreProyecto = '';
	private $registros = array();
	private $superCategorias = array();
	private $categorias = array();

	private $colspanA = 6;
	private $colspanB = 3;
	private $colspanC = 2;
	private $colspanD = 1;

	public function __construct(){
		$session = new Session();
		
		//seguridad que este logueado
		$session->Logueado();

		date_default_timezone_set('America/Costa_Rica');

		//maximo tiempo de ejecucion
		ini_set('max_execution_time', 600);
	}

	/**
	* PONE LOS HEADER DE HTML
	*/
	public function htmlHead(){
		?>

		<html>
			<head>
				<title>Exportar</title>
				<meta http-equiv="Content-Type" content="text/html;charset=utf-8" /> 
			</head>
		<body>

		<?php
	}

	/**
	* CIERRA LOS HEADER DE HTML
	*/
	public function htmlHeadClose(){
		?>

		</body>
		</html>

		<?php
	}

	/**
	* EXPORTAR CLIENTES EN VCART COMPATIBLE CON GOOGLE CONTACTS
	*/
	public function ExportarClientes(){
		$base = new Database();
		$query = "SELECT * FROM clientes";

		$clientes = $base->Select($query);

		$lista = "";

		if(!empty($clientes)){
			
			foreach ($clientes as $fila => $cliente) {
				$lista .= "BEGIN:VCARD\r\n";
				$lista .= "VERSION:3.0\r\n";

				//elimina comas de los datos
				$nombre = str_replace(',', '\,', $cliente['nombre']);
				$email = str_replace(',', '\,', $cliente['email']);
				$telefono = str_replace(',', '\,', $cliente['telefono']);
				$skype = str_replace(',', '\,', $cliente['skype']);
				$registro = str_replace(',', '\,', $cliente['registro']);
				$imagenDatos = pathinfo($_SESSION['home'].'/'.$cliente['imagen']);

				if( $cliente['pais'] != 0 ){
					$query = "SELECT * FROM country WHERE id = '".$cliente['pais']."'";
					$paisDatos = $base->Select( $query );
					$pais = str_replace(',', '\,', $paisDatos[0]['Name']);
				}

				$lista .= "N:$nombre;;;\r\n";
				$lista .= "FN:$nombre\r\n";
				$lista .= "EMAIL;type=INTERNET;type=WORK;type=pref:$email\r\n";
				$lista .= "TEL;type=WORK;type=pref:$telefono\r\n";

				if( $cliente['pais'] != 0 ){
					$lista .= "ADR;TYPE=WORK:;$pais\r\n";
				}
				
				$lista .= "X-SKYPE:$skype\r\n";
				
				$imagenBinaria = $this->ImagenBinaria( $cliente['imagen'] );
				
				//$lista .= "PHOTO;VALUE=URL;TYPE=".$imagenDatos['extension'].":$imagen\r\n";
				
				$lista .= "PHOTO;ENCODING=b;TYPE=".$imagenDatos['extension'].":$imagenBinaria\r\n";

				$lista .= "CATEGORIES:Work,Escala Matriz\r\n";
				$lista .= "NOTE:Registro\: $registro\r\n";

				$lista .= "END:VCARD\r\n";
			}
		}

		echo $lista;

		header("Content-type: text/x-vcard; charset=utf-8");
		header("Pragma: no-cache");
		header("Expires: 0");

		//nombre lleva la fecha de la generacion
		$nombre = "ClientesMatriz".date('d_m_Y-H_m_s');
		header("Content-disposition: attachment; filename=".$nombre.".vcf");
		
	}

	/**
	* COMBIERTE UNA IMAGEN EN BINARIA
	* @param string $imagen -> url de la imagen
	* @return string $binario -> codigo binario de la imagen
	*/
	private function ImagenBinaria($imagen){
		//echo $imagen;
		$imagen = '../../'.$imagen;

		if( !file_exists($imagen) ){
			$imagen = '../../images/es.png';
		}

		//$imagen = '../../images/es.png';
		$fd = fopen ( $imagen, 'rb' );

		$size = filesize ( $imagen );

		$codigo = fread ($fd, $size);

		fclose ($fd);

		$binario = base64_encode($codigo);
		return $binario;
	}

	/**
	* EXPORTA EL INFORME CREADO
	* @param $proyecto -> id del proyecto ha ser exportado
	*/
	public function ExportarExcel($proyecto){
		$this->proyecto = $proyecto;
		$this->formato = 'excel';

		$this->CrearInforme(); //compone el informe

		header('Content-Description: File Transfer'); 
		header("Content-Type: application/vnd.ms-excel; charset=utf-8");
		header("Pragma: no-cache");
		header("Expires: 0");
		
		//descarga el archivo
		$nombreArchivo =  str_replace(' ', '_', $this->nombreProyecto);

		header("Content-disposition: attachment; filename=".$nombreArchivo.".xls");

		echo $this->informe;
		$this->htmlHeadClose();
	}

	/**
	* EXPORTA EN PDF
	* @param $proyecto -> id del proyecto
	*/
	public function ExportarPdf($proyecto){
		$this->proyecto = $proyecto;
		$this->formato = 'pdf';

		$this->CrearInforme();

		$nombreArchivo =  str_replace(' ', '_', $this->nombreProyecto);

		//combierte el html a pdf-> utiliza html2pdf class
	    ob_start();
	    ob_end_clean();
	    $content = ob_get_clean();
	    $content = $this->informe;

	    try{
	    	//($sens = 'P', $format = 'A4', $langue='en', $unicode=true, $encoding='UTF-8', $marges = array(5, 5, 5, 8))
	        $html2pdf = new HTML2PDF('L', 'A0', 'es', true, 'UTF-8', array(1, 1, 300, 1) );
        	$html2pdf->pdf->SetDisplayMode('fullpage');

	        $html2pdf->pdf->SetAuthor($_SESSION['nombre']);
			$html2pdf->pdf->SetTitle('Informe '.$this->nombreProyecto.' '.date("m d Y - g:i a"));
			$html2pdf->pdf->SetSubject('Informe proyecto matriz');
			$html2pdf->pdf->SetKeywords('informe, proyecto, matriz');

			$nombreArchivo =  str_replace(' ', '_', $this->nombreProyecto);

	        $html2pdf->writeHTML($content, isset($_GET['vuehtml']) );
	        $html2pdf->Output( $nombreArchivo.'.pdf', 'D' );

	    }catch(HTML2PDF_exception $e) {
	        echo 'Ocurrio un error al generar el pdf.<br/>';
	        echo $e;
	        exit;
	    }

	    //forza la descarga del PDF
		header('Content-Description: File Transfer'); 
		header("Content-Type: application/pdf");
		header("Content-disposition: attachment; filename=".$nombreArchivo.".pdf");
		$this->htmlHeadClose();
	}

	/**
	* CREA EL INFORME
	* @param $proyecto -> id del proyecto
	* @return true si se creo el informe.
	* @return false si fallo la creacion del informe
	*/
	public function Informe($proyecto){
		$this->proyecto = $proyecto;
		$this->formato = 'html';

		$this->CrearInforme();
		
		echo $this->informe;
		$this->htmlHeadClose();
	}

	/**
	* COMPONE EL INFORME
	*/
	private function CrearInforme(){
		$this->htmlHead();
		$registro = new Registros();
		$this->registros = $registro->getRegistros( $this->proyecto );

		$this->Cabezera();
		$this->Cuerpo();
		$this->Footer();

		$this->Style();
	}

	/**
	* COMPONE LA CABECERA DEL INFORME
	* CON LOS DATOS DEL PROYECTO
	*/ 
	private function Cabezera(){
		$proyectos = new Proyectos();
		$clientes =  new Cliente();

		$datosProyecto = $proyectos->getProyectoDatos($this->proyecto);
		
		$this->clienteId = $datosProyecto[0]['cliente'];
		$this->cliente = $clientes->getClienteDato( "nombre", $this->clienteId );
		$this->nombreProyecto = $datosProyecto[0]['nombre'];

		//echo '<pre>'; print_r($datosProyecto);echo '</pre>';

		$this->informe = '<table class="Informe">
							<tr>
								<th colspan="'.$this->colspanA.'" class="SuperTitulo">
									'.$this->nombreProyecto.'
								</th>
							</tr>
							<tr>
								<th class="TituloHead" colspan="'.$this->colspanC.'">
									Nombre Cliente
								</th>
								<th class="TituloHead" colspan="'.$this->colspanD.'">
									Fecha creacion
								</th>
								<th class="TituloHead" colspan="'.$this->colspanB.'">
									Descripcion
								</th>
							</tr>
							<tr>
								<td colspan="'.$this->colspanC.'" class="DatosHead">
									'.$this->cliente.'
								</td>
								<td colspan="'.$this->colspanD.'" class="DatosHead">
									'.$datosProyecto[0]['fecha_creacion'].'
								</td>
								<td colspan="'.$this->colspanB.'" class="DatosHead">
									'.base64_decode($datosProyecto[0]['descripcion']).'
								</td>
							</tr>';
	}

	/**
	* COMPONE EL CUERPO DEL INFORME CON LA INFORMACION
	*/
	private function Cuerpo(){

		//echo '<pre>'; print_r($this->registros); echo '</pre>';

		if( $this->registros[0]['registro'] != '' ){
			$categoriasRegistradas = unserialize( $this->registros[0]['registro'] );
		}else{
			echo 'proyecto vacio';
			return;
		}

		//echo '<pre>';print_r($categoriasRegistradas);echo '</pre>';
		
		$this->supercategorias = array();
		$this->categorias = array();

		if( is_array( $categoriasRegistradas ) ){
			foreach ($categoriasRegistradas as $key => $value) {
				$path = explode(',', $value);
				
				//agrega las supercategorias
				if( is_array($path) ){
					$padre = $path[0];
					$hijo = $path[ sizeof($path)-1 ];

					if( !in_array($padre, $this->supercategorias) ){
						$this->supercategorias[] = $padre;
					}
					$this->categorias[ $padre ][] =  $hijo;
				}
			}
		}else{
			return;
		}

		//echo '<pre>';print_r($this->supercategorias);echo '</pre>';
		//echo '<pre>';print_r($this->categorias);echo '</pre>';

		$registros = new Registros();

		foreach ($this->supercategorias as $key => $superCategoria) {
			$datosSuperCategoria = $registros->getCategoriaPadreDatos($superCategoria);

			//super categoria
			$this->informe .= '
								<tr>
								  	<th colspan="'.$this->colspanA.'" class="SuperCategoria">
								   			'.$datosSuperCategoria[0]['nombre'].'
								  	</th>
							   </tr>
								  ';

			foreach ($this->categorias[$superCategoria] as $fila => $categoria) {
				
				if( $datosCategoria = $registros->getCategoria($categoria) ){
					
					$this->informe .= '<tr>
											<td colspan="'.$this->colspanA.'" class="TituloCategoria">
							 					'.$datosCategoria[0]['nombre'].'
									   		</td>
									   </tr>
										<tr>
										   	<td class="CategoriaCampoNorma">
										   		Numero
										   	</td>
										   	<td class="CategoriaCampoNorma">
										   		Norma
										   	</td>
										   	<td class="CategoriaCampo">
												Requisito Legal
										   	</td>
										   		<td class="CategoriaCampo">
												Resumen
										   	</td>
										   	<td class="CategoriaCampo2">
										   		Permiso o Documentación asociada
										   	</td>
										   	<td class="CategoriaCampo2">
										   		Entidad
										   	</td>
										</tr>';

					//OBTIENE LAS NORMAS
					$datosNormasTemp = $registros->getRegistrosNorma($this->proyecto, $categoria);
		
					$normas = unserialize( $datosNormasTemp[0]['registro'] );

					if( is_array($normas) ){
						//echo '<pre>';print_r($normas);echo '</pre>';

						foreach ($normas as $f => $norma) {
							$nombreNorma = $registros->getDatoNorma("nombre", $norma);
							$numeroNorma = $registros->getDatoNorma("numero", $norma);

							$articulosRegistrados = $registros->getRegistrosArticulos($this->proyecto, $categoria, $norma);
							$articulos = unserialize($articulosRegistrados[0]['registro']);

							if( is_array($articulos) ){
								

								$this->informe .= '<tr>
														   <td rowspan="'.sizeof($articulos).'" class="TdNorma">
														   		'.$numeroNorma.'
														   	</td>
														   	<td rowspan="'.sizeof($articulos).'" class="TdNorma">
														   		'.$nombreNorma.'
														   	</td>';

								$centinela = 0;
								
								//compone los datos de los articulos de la categoria
								foreach ($articulos as $fl => $articulo) {
									if( $datosArticulo = $registros->getArticulo($articulo) ){
										
										if( $centinela > 0 ){
											$this->informe .= '<tr>';
										}

										$entidades = unserialize( $datosArticulo[0]['entidad'] );

										$this->informe .= '
														   	<td class="TdDato">
																'.strip_tags(base64_decode($datosArticulo[0]['articulo']), '<ul><ol><li><strong><u>').'
														   	</td>
														   	<td class="TdDato" >
																'.strip_tags(base64_decode($datosArticulo[0]['resumen']), '<ul><ol><li><strong><u>').'
														   	</td>
														   	<td class="TdDato2" >
														   		'.strip_tags(base64_decode($datosArticulo[0]['permisos']), '<ul><ol><li><strong><u>').'
														   	</td>
														   	<td class="TdDato2" >
														   		'.strip_tags($this->entidades($entidades), '<ul><ol><li><strong><u>').'
														   	</td>
														</tr>';

										/*$this->informe .= '
														   	<td class="TdDato">
																sss
														   	</td>
														   	<td class="TdDato" >
																sss
														   	</td>
														   	<td class="TdDato2" >
														   		s
														   	</td>
														   	<td class="TdDato2" >
														   		s
														   	</td>
														</tr>';*/

										$centinela++;
									}// en if

								} // end foreach para articulos
								
							} //enf if articulos
							
						} // end foreach normas

					} // end if normas

				} // end if categorias 

				//$this->info .= '</table>'; 

			} // end foreach categorias

		} // end foreach para categorias de una supercategoria
		
	}

	/**
	* COMPONE LAS ENTIDADES
	*/
	private function Entidades($entidades){
		$registros = new Registros();
		$text = '';

		if( is_array($entidades) ){
			foreach ($entidades as $key => $entidad) {
				$datosEntidad = $registros->getEntidadDatos($entidad);
				$text .= '<p>'.$datosEntidad[0]['nombre'].'</p>';
			}
			return $text;
		}else{
			return '---';
		}
	}

	/**
	* COMPONE EL FOOTER DEL INFORME
	* MUESTRA INFORMACION
	*/
	private function Footer(){
		$cliente = new Cliente();
		

		//obtiene los datos del cliente
		$datosCliente = $cliente->getDatosCliente( $this->clienteId ); 
		$imagenCliente = $_SESSION['home'].'/'.$datosCliente[0]['imagen']; 
		
		//$this->informe .= '</table>';
		//return;
		
		$this->informe .= '
							<tr>
								<td colspan="2" class="TdFooterLeft">
									   			<br/>
									   			<img class="LogoEscala" src="'.$_SESSION['home'].'/images/escala.png">
									   			<br/>
								</td>
						   		<td colspan="2" class="TdFooter">
						   			<table class="FooterTable">
						   				<tr>
						   					<td class="SubTitulo">
						   						Fecha:
						   					</td>
						   					<td class="TdFooter2">
						   						'.date("m d Y - g:i a").'
						   					</td>
						   				</tr>
						   				<tr>
						   					<td class="SubTitulo">
						   						Administrador:
						   					</td>
						   					<td class="TdFooter2" >
						   						'.$_SESSION['nombre'].'
						   					</td>
						   				</tr>
						   				<tr>
						   					<td class="SubTitulo">
						   						Cliente:
						   					</td>
						   					<td class="TdFooter2" >
						   						'.$this->cliente.'
						   					</td>
						   				</tr>
						   				<tr>
						   					<td class="SubTitulo">
						   						Registro:
						   					</td>
						   					<td class="TdFooter2" >
						   						'.$datosCliente[0]['registro'].'
						   					</td>
						   				</tr>
						   				<tr>
						   					<td class="SubTitulo">
						   						Email:
						   					</td>
						   					<td class="TdFooter2" >
						   						<a href="mailto:'.$datosCliente[0]['email'].'?Subject='.$this->nombreProyecto.'">
						   							'.$datosCliente[0]['email'].'
						   						</a>
						   					</td>
						   				</tr>
						   				<tr>
						   					<td class="SubTitulo">
						   						Proyecto:
						   					</td>
						   					<td class="TdFooter2" >
						   						'.$this->nombreProyecto.'
						   					</td>
						   				</tr>
						   				<tr>
						   					<td colspan="2" class="Center">
						   						Informe generado automaticamente.<br/>
						   						2013 Escala Consultores. Todos los derechos reservados
						   					</td>
						   				</tr>
						   			</table>
						   		</td>
						   		<td colspan="2" class="TdFooterRight">
						   			
						   		</td>
							</tr>
						</table>';
	}

	/**
	* APLICA ESTILO AL INFORMA
	*/
	private function Style(){

		//estilo para pdf
		if( $this->formato == 'pdf'){
			$tdNorma = 'style="background-color: #F3EFE6; border: 1px solid #757273; width: 5%; vertical-aling: middle;"';
			$tdDato = 'style="background-color: #F3EFE6; border: 1px solid #757273; vertical-aling: middle; padding: 0; width: 32.5%"';
			$tdDato2 = 'style="background-color: #F3EFE6; border: 1px solid #757273; vertical-aling: middle; padding: 0; width: 12.5%"';
		}else{
			$tdNorma = 'style="background-color: #F3EFE6; border: 1px solid #757273; vertical-aling: middle;"';
			$tdDato = 'style="background-color: #F3EFE6; border: 1px solid #757273; vertical-aling: middle; padding: 0;"';
			$tdDato2 = 'style="background-color: #F3EFE6; border: 1px solid #757273; vertical-aling: middle; padding: 0;"';
		}

		$tema = array(
			'class="Informe"' => 'style="background-color: #BAB8B9; width: 100%; margin: 0 auto; border-collapse: collapse; text-align: left;"',

			//titulo head
			'class="InformeHead"' => 'style="background-color: #757273; color: #ffffff; text-align: center; font-size: 6pt;"',
			'class="SuperTitulo"' => 'style="background-color: #757273; color: #ffffff; font-size: 18pt; text-align: center; font-weight: bold;"',
			'class="TituloHead"' => 'style="background-color: #757273; color: #ffffff; font-size: 14pt; text-align: center; font-weight: bold;"',
			'class="DatosHead"' => 'style="background-color: #757273; color: #ffffff; font-size: 14pt; text-align: center;"',

			//categorias
			'class="SuperCategoria"' => 'style="background-color: #757273; color: #ffffff; text-align: center; font-weight: bold; font-size: 16pt;"',
			'class="Titulo"' => 'style="text-align: center; font-size: 15pt; font-weight: bold;"',
			'class="TituloCategoria"' => 'style="background-color: #BAB8B9; border: 1px solid #BAB8B9; text-align: center; color: #ffffff; font-weight: bold; font-size: 15pt;"',
			'class="CategoriaCampoNorma"' => 'style="background-color: #BAB8B9; border: 1px solid #BAB8B9; color: #ffffff; text-align: center; font-size: 12pt; font-weight: bold; vertical-aling: middle;"',
			'class="CategoriaCampo"' => 'style="background-color: #BAB8B9; border: 1px solid #BAB8B9; color: #ffffff; text-align: center; font-size: 12pt; font-weight: bold; vertical-aling: middle;"',
			'class="CategoriaCampo2"' => 'style="background-color: #BAB8B9; border: 1px solid #BAB8B9; color: #ffffff; text-align: center; font-size: 12pt; font-weight: bold; vertical-aling: middle;"',

			//normas y articulos
			'class="NombreArticulo"' => 'style="background-color: #F3EFE6; font-weight: bold; margin: 0; padding: 0;"',
			'class="TdNorma"' => 'style="background-color: #F3EFE6; border: 1px solid #757273; vertical-aling: middle; width: 5%; max-width: 5%; text-align: center;"',
			'class="TdDato"' => 'style="background-color: #F3EFE6; border: 1px solid #757273; vertical-aling: middle; padding: 0; width: 32.5%; max-width: 32.5%;"',
			'class="TdDato2"' => 'style="background-color: #F3EFE6; border: 1px solid #757273; vertical-aling: middle; padding: 0; width: 12.5%; max-width: 12.5%;"',

			//footer
			'class="TdFooter"' => 'style="background-color: #BAB8B9; border: 1px solid #BAB8B9; color: #000000; width: 100%; text-align: center; font-size: 12pt;"',
			'class="TdFooterLeft"' => 'style="background-color: #BAB8B9; border: 1px solid #BAB8B9;  color: #000000; text-align: left; font-size: 12pt;"',
			'class="TdFooterRight"' => 'style="background-color: #BAB8B9; border: 1px solid #BAB8B9;  color: #000000; text-align: right; font-size: 12pt;"',
			'class="FooterTable"' => 'style="background-color: #BAB8B9; border: 1px solid #BAB8B9; width: 100%; color: #000000; text-align: left; margin-left: auto; margin-right: auto; font-size: 12pt;"',
			'class="FooterTableCliente"' => 'style="background-color: #BAB8B9; border: 1px solid #BAB8B9; color: #000000; text-align: left; margin-left: auto; margin-right: auto; font-size: 12pt; border-spacing: 10px 0px"',
			'class="SubTitulo"' => 'style="background-color: #BAB8B9; border: 1px solid #BAB8B9; color: #000000; text-align: right; font-weight: bold; font-size: 12pt;"',
			'class="SubTituloCliente"' => 'style="background-color: #BAB8B9; border: 1px solid #BAB8B9; color: #000000; text-align: left; font-weight: bold; font-size: 12pt;"',
			'class="Center"' => 'style="background-color: #BAB8B9; border: 1px solid #BAB8B9; color: #000000; text-align: center; font-size: 12pt;"',
			'class="TdFooter2"' => 'style="background-color: #BAB8B9; border: 1px solid #BAB8B9; color: #000000; text-align: left; font-size: 12pt;"',

			'class="LogoEscala"' => 'style="display:block; float: left; height: 70px; margin-left: 10px; margin-top: 10px;"',
			'class="LogoCliente"' => 'style="display:block; float: right; height: 70px; margin-right: 10px; margin-top: 10px;"',

			'<p>' => '',
			'<p style="margin-left:1pt;">' => '',
			'<p style="text-align: justify; ">' => '',
			'</p>' => '',

			'class="FooterCliente"' => 'style="width: 101%; background-color: #BAB8B9; text-align: center;
				border: 1px solid #BAB8B9;
				padding: 0;
				margin: 0;
				-webkit-border-bottom-right-radius: 20px;
				-webkit-border-bottom-left-radius: 20px;
				-moz-border-radius-bottomright: 20px;
				-moz-border-radius-bottomleft: 20px;
				border-bottom-right-radius: 20px;
				border-bottom-left-radius: 20px;"',
			);

		foreach ($tema as $class => $style) {
			$this->informe = str_replace( $class, $style, $this->informe);
		}
	}

	/************************************************************ EXPORTACION PARA EL CLIENTE *******************************/

	/**
	* EXPORTA PDF DEL CLIENTE
	*/
	public function ExportarPdfCliente( $proyecto ){
		$this->proyecto = $proyecto;
		$this->formato = 'pdf';

		$this->htmlHead();
		$this->CrearInformeCliente();

		//exporta en formato pdf
		$nombreArchivo =  str_replace(' ', '_', $this->nombreProyecto);

		//combierte el html a pdf-> utiliza html2pdf class
	   	ob_start();
	    ob_end_clean();
	    $content = ob_get_clean();
	    $content = $this->informe;

	    try{
	        $html2pdf = new HTML2PDF('P', 'A1', 'es', true, 'UTF-8', array(1, 1, 1, 1) );
        	$html2pdf->pdf->SetDisplayMode('fullpage');

	        $html2pdf->pdf->SetAuthor($_SESSION['nombre']);
			$html2pdf->pdf->SetTitle('Informe '.$this->nombreProyecto.' '.date("m d Y - g:i a"));
			$html2pdf->pdf->SetSubject('Informe proyecto matriz');
			$html2pdf->pdf->SetKeywords('informe, proyecto, matriz');

			$nombreArchivo =  str_replace(' ', '_', $this->nombreProyecto);

	        $html2pdf->writeHTML($content, isset($_GET['vuehtml']) );
	        $html2pdf->Output( $nombreArchivo.'.pdf', 'D' );

	    }catch(HTML2PDF_exception $e) {
	        echo 'Ocurrio un error al generar el pdf.<br/>';
	        echo $e;
	        return false;
	    }

	    //forza la descarga del PDF
		header('Content-Description: File Transfer'); 
		header("Content-Type: application/pdf");
		header("Content-disposition: attachment; filename=".$nombreArchivo.".pdf");
		
		$this->htmlHeadClose();
		//echo $this->informe;
	}

	/**
	* CREAR PDF CLIENTE Y LO GUARDA EN temp
	* @param int $proyecto -> id del proyecto
	* @return boolean false -> si falla
	* @return string $link -> link del archivo
	*/
	public function ExportarPdfClienteFile( $proyecto ){
		$link = sys_get_temp_dir();

		$this->proyecto = $proyecto;
		$this->formato = 'pdf';

		$this->informe .= '<html>
			<head>
				<title>Exportar</title>
				<meta http-equiv="Content-Type" content="text/html;charset=utf-8" /> 
			</head>
		<body>';

		$this->CrearInformeCliente( );

		$this->informe .= '</body>
						</html>';

		//exporta en formato pdf
		$nombreArchivo =  str_replace(' ', '_', $this->nombreProyecto);

		//combierte el html a pdf-> utiliza html2pdf class
	   	ob_start();
	    ob_end_clean();
	    $content = ob_get_clean();
	    $content = $this->informe;

	    try{
	        $html2pdf = new HTML2PDF('P', 'A1', 'es', true, 'UTF-8', array(1, 1, 1, 1) );
        	$html2pdf->pdf->SetDisplayMode('fullpage');

	        $html2pdf->pdf->SetAuthor($_SESSION['nombre']);
			$html2pdf->pdf->SetTitle('Informe '.$this->nombreProyecto.' '.date("m d Y - g:i a"));
			$html2pdf->pdf->SetSubject('Informe proyecto matriz');
			$html2pdf->pdf->SetKeywords('informe, proyecto, matriz');

			$nombreArchivo =  str_replace(' ', '_', $this->nombreProyecto);

	        $html2pdf->writeHTML($content, isset($_GET['vuehtml']) );

	        $link = $link.'/'.$nombreArchivo.'.pdf';
	        $html2pdf->Output( $link, 'F' );

	    }catch(HTML2PDF_exception $e) {
	        echo 'Ocurrio un error al generar el pdf.<br/>';
	        echo $e;
	        return false;
	    }

		return $link;
	}

	public function getProyectoNombre(){
		return $this->nombreProyecto;
	}

	/**
	* COMPONE EL INFORME DEL CLIENTE
	*/
	private function CrearInformeCliente(){
		
		$registro = new Registros();
		$this->registros = $registro->getRegistros( $this->proyecto );

		$this->CabezeraCliente();
		$this->Cuerpo();
		$this->FooterCliente();

		$this->Style();
	}

	/**
	* COMPONE LA CABEZERA DEL INFORME DEL CLIENTE
	*/
	private function CabezeraCliente(){
		$proyectos = new Proyectos();
		$clientes =  new Cliente();

		$datosProyecto = $proyectos->getProyectoDatos($this->proyecto);
		
		$this->clienteId = $datosProyecto[0]['cliente'];
		$this->cliente = $clientes->getClienteDato( "nombre", $this->clienteId );
		$imagen = $clientes->getClienteDato("imagen", $this->clienteId );
		$this->nombreProyecto = $datosProyecto[0]['nombre'];

		$imagenCliente = $_SESSION['home'].'/images/es.png'; //imagen por defecto
		$imagenLink = "../../".$imagen; //link de la imagen

		//si la imagen existe
		if( file_exists( $imagenLink ) ){
			$imagenCliente = $_SESSION['home'].'/'.$imagen;
		}

		$this->informe = '<table class="Informe">
							<tr>
								<th colspan="2" class="SuperTitulo">
									<img class="LogoEscala" src="../../images/escala.png">
								</th>
								<th colspan="2" class="SuperTitulo">
									'.$this->nombreProyecto.'
								</th>
								<th colspan="2" class="SuperTitulo">
									<img class="LogoCliente" src="'.$imagenCliente.'">
								</th>
							</tr>';
	}

	/**
	* FOOTER DEL INFORME DEL CLIENTE
	*/
	private function FooterCliente(){
		$cliente = new Cliente();
		

		//obtiene los datos del cliente
		$datosCliente = $cliente->getDatosCliente( $this->clienteId ); 
		
		$imagenCliente = $_SESSION['home'].'/images/es.png'; 

		$imagenLink = "../../".$datosCliente[0]['imagen'];

		if( file_exists( $imagenLink ) ){
			$imagenCliente = $_SESSION['home'].'/'.$datosCliente[0]['imagen'];
		}
		
		$this->informe .= '</table>
							<table class="FooterCliente">
						   	<tr>
						   		<td style="width: 33%">
						   		</td>
						   		<td style="width: 33%">
						   			<table class="FooterTableCliente">
						   				<tr>
						   					<td class="SubTituloCliente">
						   						Cliente
						   					</td>
						   					<td class="SubTituloCliente">
						   						Registro
						   					</td>
						   					<td class="SubTituloCliente">
						   						Proyecto
						   					</td>
						   					<td class="SubTituloCliente">
						   						Fecha
						   					</td>
						   				</tr>
						   				<tr>
						   					<td class="TdFooter2" >
						   						'.$this->cliente.'
						   					</td>
						   					<td class="TdFooter2" >
						   						'.$this->proyecto.'
						   					</td>
						   					<td class="TdFooter2" >
						   						'.$this->nombreProyecto.'
						   					</td>
						   					<td class="TdFooter2" >
						   						'.date("m d Y - g:i a").'
						   					</td>
						   				</tr>
						   				<tr>
						   					<td colspan="4" class="Center">
						   						2013 Escala Consultores. Todos los derechos reservados
						   					</td>
						   				</tr>
						   			</table>
						   		</td>
						   		<td style="width: 33%">
						   		</td>
						   		</tr>
							</table>
						';
	}


}

?>