<?php

/**
 * MANEJO DE DATOS REGISTROS Y CATEGORIAS
 */

//require_once("classDatabase.php");
require_once("session.php");
require_once("proyectos.php");
require_once("usuarios.php");
require_once("registros.php");
require_once("../html2pdf.class.php");

$exportar = new Exportar();

if( isset($_GET['id']) && isset($_GET['tipo'])){
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
	        $html2pdf = new HTML2PDF('L', 'A2', 'es', true, 'UTF-8', array(1, 1, 1, 1) );
        	$html2pdf->pdf->SetDisplayMode('fullpage');

	        $html2pdf->pdf->SetAuthor('Matrices Consilio');
			$html2pdf->pdf->SetTitle('Informe '.$this->nombreProyecto);
			$html2pdf->pdf->SetSubject('Informe proyecto matriz');
			$html2pdf->pdf->SetKeywords('informe, proyecto, matriz');

			$nombreArchivo =  str_replace(' ', '_', $this->nombreProyecto);

	        $html2pdf->writeHTML($content, isset($_GET['vuehtml']) );
	        $html2pdf->Output($nombreArchivo.'.pdf', 'D');

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
			$this->informe .= '<tr>
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
										   	<td class="CategoriaCampo">
										   		Numero
										   	</td>
										   	<td class="CategoriaCampo">
										   		Norma
										   	</td>
										   	<td class="CategoriaCampo">
												Requisito Legal
										   	</td>
										   		<td class="CategoriaCampo">
												Resumen
										   	</td>
										   	<td class="CategoriaCampo">
										   		Permiso o Documentación asocia
										   	</td>
										   	<td class="CategoriaCampo">
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
																'.base64_decode($datosArticulo[0]['articulo']).'
														   	</td>
														   	<td class="TdDato" >
																'.base64_decode($datosArticulo[0]['resumen']).'
														   	</td>
														   	<td class="TdDato2" >
														   		'.base64_decode($datosArticulo[0]['permisos']).'
														   	</td>
														   	<td class="TdDato2" >
														   		'.$this->entidades($entidades).'
														   	</td>
														</tr>';
										/*$this->informe .= '
														   	<td class="TdDato" >
																1111
														   	</td>
														   	<td class="TdDato" >
																222
														   	</td>
														   	<td class="TdDato" >
														   		333
														   	</td>
														   	<td class="TdDato" >
														   		444
														   	</td>
														</tr>';*/

										$centinela++;
									}

								} // end foreach para articulos
								
								//$this->informe .= '</tr>';
								
							}
							
						} // end foreach normas

					} // end if

				}

			} // end foreach para categorias de una supercategoria

		}
		
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

		//$this->informe .= '</table>';
		$this->informe .= '<tr>
						   		<td id="fo" colspan="'.$this->colspanC.'" class="TdFooterLeft">
						   			<br/>
						   			<img class="LogoEscala" src="'.$_SESSION['home'].'/images/escala.png">
						   			<br/>
						   		</td>
						   		<td colspan="'.$this->colspanC.'" class="TdFooter">
						   			
						   			<table class="FooterTable">
						   				<tr>
						   					<td class="SubTitulo">
						   						Fecha:
						   					</td>
						   					<td>
						   						'.date("m d Y - g:i a").'
						   					</td>
						   				</tr>
						   				<tr>
						   					<td class="SubTitulo">
						   						Administrador:
						   					</td>
						   					<td>
						   						'.$_SESSION['nombre'].'
						   					</td>
						   				</tr>
						   				<tr>
						   					<td class="SubTitulo">
						   						Cliente:
						   					</td>
						   					<td>
						   						'.$this->cliente.'
						   					</td>
						   				</tr>
						   				<tr>
						   					<td class="SubTitulo">
						   						Registro:
						   					</td>
						   					<td>
						   						'.$cliente->getClienteDato("registro",$this->clienteId).'
						   					</td>
						   				</tr>
						   				<tr>
						   					<td class="SubTitulo">
						   						Email:
						   					</td>
						   					<td>
						   						<a href="mailto:'.$cliente->getClienteDato("email",$this->clienteId).'?Subject='.$this->nombreProyecto.'">
						   							'.$cliente->getClienteDato("email",$this->clienteId).'
						   						</a>
						   					</td>
						   				</tr>
						   				<tr>
						   					<td class="SubTitulo">
						   						Proyecto:
						   					</td>
						   					<td>
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
						   			<br/>
						   		</td>
						   		<td colspan="'.$this->colspanC.'" class="TdFooterRight">
						   			<br/>
						   			<img class="LogoCliente" src="'.$_SESSION['home'].'/'.$cliente->getClienteDato("imagen",$this->clienteId).'" >
						   			<br/>
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
			$tdNorma = 'style="background-color: #F3EFE6; border: 1px solid #757273; width: 5%;"';
			$tdDato = 'style="background-color: #F3EFE6; border: 1px solid #757273; vertical-aling: top; padding: 0; width: 32.5%"';
			$tdDato2 = 'style="background-color: #F3EFE6; border: 1px solid #757273; vertical-aling: top; padding: 0; width: 12.5%"';
		}else{
			$tdNorma = 'style="background-color: #F3EFE6; border: 1px solid #757273;"';
			$tdDato = 'style="background-color: #F3EFE6; border: 1px solid #757273; vertical-aling: top; padding: 0;"';
			$tdDato2 = 'style="background-color: #F3EFE6; border: 1px solid #757273; vertical-aling: top; padding: 0;"';
		}

		$tema = array(
			'class="Informe"' => 'style="width: 100%; margin: 0 auto; border-collapse: collapse; text-align: left;"',

			//titulo head
			'class="InformeHead"' => 'style="background-color: #757273; color: #ffffff; text-align: center;"',
			'class="SuperTitulo"' => 'style="background-color: #757273; color: #ffffff; font-size 16pt; text-align: center; font-weight: bold;"',
			'class="TituloHead"' => 'style="background-color: #757273; color: #ffffff; font-size 14pt; text-align: center; font-weight: bold;"',
			'class="DatosHead"' => 'style="background-color: #757273; color: #ffffff; font-size 14pt; text-align: center;"',

			//categorias
			'class="SuperCategoria"' => 'style="background-color: #757273; color: #ffffff; text-align: center; font-weight: bold; font-size: 14pt;"',
			'class="Titulo"' => 'style="text-align: center; font-size: 12pt; font-weight: bold;"',
			'class="TituloCategoria"' => 'style="background-color: #BAB8B9; border: 1px solid #BAB8B9; text-align: center; color: #ffffff; font-weight: bold; font-size: 13pt;"',
			'class="CategoriaCampo"' => 'style="background-color: #BAB8B9; border: 1px solid #BAB8B9; color: #ffffff; text-align: center; font-weight: bold;"',

			//normas y articulos
			'class="NombreArticulo"' => 'style="background-color: #F3EFE6; font-weight: bold; margin: 0; padding: 0;"',
			'class="TdNorma"' => $tdNorma,
			'class="TdDato"' => $tdDato,
			'class="TdDato2"' => $tdDato2,

			//footer
			'class="TdFooter"' => 'style="background-color: #BAB8B9; color: #000000; text-align: text; "',
			'class="TdFooterLeft"' => 'style="background-color: #BAB8B9; color: #000000; text-align: left; "',
			'class="TdFooterRight"' => 'style="background-color: #BAB8B9; color: #000000; text-align: right; "',
			'class="FooterTable"' => 'style="background-color: #BAB8B9; color: #000000; text-align: left; margin-left: auto; margin-right: auto;"',

			'class="LogoEscala"' => 'style="display:block; float: left; height: 80px;"',
			'class="LogoCliente"' => 'style="display:block; height: 80px;"',
			);

		foreach ($tema as $class => $style) {
			$this->informe = str_replace( $class, $style, $this->informe);
		}
	}

}

?>