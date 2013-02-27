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
	private $proyecto = ''; //id proyecto
	private $cliente = '';
	private $informe = ""; //informe compuesto
	private $nombreProyecto = '';
	private $registros = array();
	private $superCategorias = array();
	private $subcategorias = array();

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
		$this->proyecto = $proyecto;

		$this->CrearInforme(); //compone el informe

		header('Content-Description: File Transfer'); 
		header("Content-Type: application/vnd.ms-excel; charset=utf-8");
		header("Pragma: no-cache");
		header("Expires: 0");
		
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
		
		$this->proyecto = $proyecto;

		$this->CrearInforme();

		$nombreArchivo =  str_replace(' ', '_', $this->nombreProyecto);

		//combierte el html a pdf-> utiliza html2pdf class
	    ob_start();
	    ob_end_clean();
	    $content = ob_get_clean();
	    $content = $this->informe;
	    
	    try{

	        $html2pdf = new HTML2PDF('P', 'A2', 'es');

	        $html2pdf->pdf->SetAuthor('Matrices Consilio');
			$html2pdf->pdf->SetTitle('Informe Proyecto');
			$html2pdf->pdf->SetSubject('informe proyecto matriz');
			$html2pdf->pdf->SetKeywords('informe, proyecto, matriz');

	        $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
	        $html2pdf->Output('exportar.pdf');

	    }catch(HTML2PDF_exception $e) {
	        echo 'Ocurrio un error al generar el pdf.<br/>';
	        echo $e;
	        exit;
	    }

	    //forza la descarga del PDF
		header('Content-Description: File Transfer'); 
		header("Content-Type: application/pdf");
		header("Content-disposition: attachment; filename=".$nombreArchivo.".pdf");
	}

	/**
	* CREA EL INFORME
	* @param $proyecto -> id del proyecto
	* @return true si se creo el informe.
	* @return false si fallo la creacion del informe
	*/
	public function Informe($proyecto){
		$this->proyecto = $proyecto;

		$this->CrearInforme();
		
		?>

			<html>
			<head>
				<title>Ipurdy</title>
				<meta http-equiv="Content-Type" content="text/html;charset=utf-8" /> 
			</head>
			<body>
		<?php
		echo $this->informe;
		?>
			</body>
			</html?
		<?php
	}

	/**
	* COMPONE EL INFORME
	*/
	private function CrearInforme(){
		//obtiene toda la informacion del proyecto
		$registro = new Registros();
		$this->registros = $registro->getRegistros( $this->proyecto );

		$this->Cabezera();
		$this->Cuerpo();
		$this->Footer();

		$this->Style();
	}

	/**
	* COMPONE LA CABECERA DEL INFORME
	*/ 
	private function Cabezera(){
		$proyectos = new Proyectos();
		$clientes =  new Cliente();

		$datosProyecto = $proyectos->getProyectoDatos($this->proyecto);
		
		$this->cliente = $clientes->getClienteDato( "nombre", $datosProyecto[0]['cliente'] );
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

		/*echo '<pre>';
		print_r($this->registros);
		echo '</pre>';*/

		//$this->informe .= '<tbody>';

		$categorias = unserialize( $this->registros[0]['registro'] );

		//echo '<pre>';print_r($categorias);echo '</pre>';

		$registros = new Registros();

		foreach ($categorias as $key => $categoria) {

			//obtiene los datos de las supercategorias
			if ( $datosCategoria = $registros->getCategoriaPadreDatos($categoria) ){
				$this->supercategorias[] = $categoria;
				
				$this->subcategorias = array_diff($categorias, $this->supercategorias);

				//super categoria
				$this->informe .= '<tr>
								   		<th colspan="'.$this->colspanA.'" class="SuperCategoria">
								   			'.$datosCategoria[0]['nombre'].'
								   		</th>
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
								   </tr>
								   ';

				//compone las categorias hijas
				$this->Categorias($categoria);

			}else{
				continue;
			}

		}

		//echo '<pre>';print_r($this->supercategorias);echo '</pre>';
		//echo '<hr>sub categorias<hr><pre>';print_r($this->subcategorias);echo '</pre>';

		//cierra el body de la tabla
		//$this->informe .= '</tbody>';
		
	}

	/**
	* COMPONE LOS DATOS DE LAS CATEGORIAS
	* @param $padre -> super categoria id
	*/
	private function Categorias($padre){
		$registros = new Registros();
		$hijos = $registros->getTodosHijos($padre);

		foreach ($this->subcategorias as $key => $id) {
			
			if( in_array($id, $hijos)){
				//echo 'hojas -> '.$id.'<br/>';

				$this->Normas($id);
			}else{
				continue;
			}

		}
		
	}

	/**
	* COMPONE LOS DATOS DE LAS NORMAS DE LA CATEGORIAS
	* @param $categoria -> id de la categoria
	*/
	private function Normas($categoria){

		$registros = new Registros();
		
		$nombreCategoria = $registros->getCategoriaDato('nombre',$categoria);

		$datosCategoria = $registros->getCategoria($categoria);
		
		$datosNormasTemp = $registros->getRegistrosNorma($this->proyecto, $categoria);

		$normas = unserialize( $datosNormasTemp[0]['registro'] );

		if( !is_array($normas) ){
			$normas = array();
		}

		$this->informe .= '<tr>
							 <td colspan="'.$this->colspanA.'" class="TituloCategoria">
							 	'.$nombreCategoria.'
							 </td>
						   </tr>';

		//echo '<table style="border: 1px solid #dedede;">';
		foreach ($normas as $key => $id) {
			$datosNorma = $registros->getDatosNorma($id);


			$registrosArticulos = $registros->getRegistrosArticulos($this->proyecto, $categoria, $id);

			$articulos = unserialize( $registrosArticulos[0]['registro'] );
			//echo $datosArticulo[0]['registro'];
			//echo '<pre>articulo ';print_r($articulos);echo'</pre>';

			/*$this->informe .= '<tr>
				 	<td rowspan="'.sizeof($articulos).'" class="TdNormaNumero">
				 		'.$datosNorma[0]['numero'].'
				 	</td>
				 	<td rowspan="'.sizeof($articulos).'" class="TdNormaNorma">
				 		'.$datosNorma[0]['nombre'].'
				 	</td>';*/

			foreach ($articulos as $f => $articulo) {
				//echo $articulo.',';

				$datosArticulo = $registros->getArticulo($articulo);

				//echo '<pre>datos articulo '.$articulo.'<hr> ';print_r($datosArticulo);echo'</pre>';

				$this->informe .= '<td class="TdDato">
										<p class="NombreArticulo">'.$datosArticulo[0]['nombre'].'</p>
										'.base64_decode($datosArticulo[0]['articulo']).'
									 </td>
									 <td class="TdDato">
									 	'.base64_decode($datosArticulo[0]['resumen']).'
									 </td>
									 <td class="TdDato">
									 	'.base64_decode($datosArticulo[0]['permisos']).'
									 </td>
									 <td class="TdDato">
									 	entidad
									 </td>
								   </tr>';
			}

		}

	}

	/**
	* COMPONE EL FOOTER DEL INFORME
	* MUESTRA INFORMACION
	*/
	private function Footer(){
		$cliente = new Cliente();
		$proyectos = new Proyectos();


		//$imagen = $cliente->get 

		$this->informe .= '<tr>
						   		<td colspan="'.$this->colspanC.'" class="TdFooterLeft">
						   			<img class="LogoEscala" src="'.$_SESSION['home'].'/images/escala.png">
						   		</td>
						   		<td colspan="'.$this->colspanC.'" class="TdFooter">
						   			
						   			<br/>
						   			<table class="FooterTable">
						   				<tr>
						   					<td colspan="2">
						   						Informe generado automaticamente
						   					</td>
						   				</tr>
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
						   			</table>
						   			<br/>
						   		</td>
						   		<td colspan="'.$this->colspanC.'" class="TdFooterRight">
						   			<img class="LogoCliente" src="'.$_SESSION['home'].'/images/escala.png">
						   		</td>
						   </tr>
						   </table>';
		//$this->informe .= '</table>';
	}

	/**
	* APLICA EL TEMA DE COLORES AL INFORME
	*/
	private function Style(){
		$tema = array(
			'class="Informe"' => 'style="width: 100%; border-collapse: collapse; text-align: left;"',

			//titulo head
			'class="InformeHead"' => 'style="background-color: #757273; color: #ffffff; text-align: center;"',
			'class="SuperTitulo"' => 'style="background-color: #757273; color: #ffffff; font-size 16pt; text-align: center; font-weight: bold;"',
			'class="TituloHead"' => 'style="background-color: #757273; color: #ffffff; font-size 14pt; text-align: center; font-weight: bold;"',
			'class="DatosHead"' => 'style="background-color: #757273; color: #ffffff; font-size 14pt; text-align: center;"',

			//categorias
			'class="SuperCategoria"' => 'style="background-color: #F68400; text-align: center; font-weight: bold; font-size: 14pt;"',
			'class="Titulo"' => 'style="text-align: center; font-size: 12pt; font-weight: bold;"',
			'class="TituloCategoria"' => 'style="background-color: #A1CA4A; text-align: center; color: #ffffff; font-weight: bold; font-size: 13pt;"',
			'class="CategoriaCampo"' => 'style="background-color: #F68400; color: #ffffff; text-align: center; font-weight: bold;"',

			//normas y articulos
			'class="TdNormaNumero"' => 'style="background-color: #F4F4F4; border: solid 1px #757273;"',
			'class="TdNormaNorma"' => 'style="background-color: #F4F4F4; border: solid 1px #757273;"',
			'class="NombreArticulo"' => 'style="background-color: #F4F4F4; font-weight: bold; margin: 0; padding: 0;"',
			'class="TdDato"' => 'style="background-color: #F4F4F4; border: 1px solid #757273; vertical-aling: top; padding: 0;"',

			//footer
			'class="TdFooter"' => 'style="background-color: #BAB8B9; color: #000000; text-align: text; "',
			'class="TdFooterLeft"' => 'style="background-color: #BAB8B9; color: #000000; text-align: left; "',
			'class="TdFooterRight"' => 'style="background-color: #BAB8B9; color: #000000; text-align: right; "',
			'class="FooterTable"' => 'style="background-color: #BAB8B9; color: #000000; text-align: left; margin: 0 auto;"',

			'class="LogoEscala"' => 'style="display:block; float: left; height: 80px; max-width: 250px;"',
			'class="LogoCliente"' => 'style="display:block; float: right; height: 80px; max-width: 250px;"',
			);

		foreach ($tema as $class => $style) {
			$this->informe = str_replace( $class, $style, $this->informe);
		}
	}

}


?>