<?php

/**
* EXPORTA INFORME EN FORMATO EXCEL
*/
/**
session_start();

//logueo
if( !isset($_SESSION['logueado']) ){
	$redireccion = "login.php";
	echo "<script type='text/javascript'>top.location.href = '$redireccion';</script>";
	exit;
}

$host      =    "localhost";
$user      =    "root";
$pass      =    "root";
$tablename =    "matriz";

//$conecta = mysql_connect($host,$user,$pass);
$conecta = mysql_connect($host,$user);

mysql_select_db($tablename, $conecta) or die (mysql_error ());

mysql_query("SET NAMES 'utf8'");

if(isset($_GET['id'])){
	exportarProyecto($_GET['id']);
}

//exporta un proyecto
function exportarProyecto($id){
	$tabla = 'style="border: 0px; width: 100%;"';
	$titulo = 'style="background-color: #6fa414; font-bold: bold; color: #fff; font-size: 18pt; text-align: center;"';
	$columnaTitulo = 'style="background-color: #a1ca4a; color: #fff; font-bold: bold; font-size: 16pt; text-align: center;"';
	$columna = 'style="text-align: left; font-size: 14pt;"';
	$tituloInfo = 'style="background-color: #a1ca4a; color: #fff; font-size: 16pt; text-align: center;"';
	$columnanInfo = 'style="background-color: #f4f4f4; color: #757374; font-size: 14pt; text-align: left;"';
	$logo = 'style="background-color: #f4f4f4; color: #757374; font-size: 14pt; text-align: center;"';
	
	//nombre por defecto, despues lo cambia
	$nombre = 'proyecto'.$id;
	$cuerpo = '';
	$detalles = '';//informacion detallada de las categorias, normas y sus detalles

	$sql = 'SELECT * FROM proyectos WHERE cliente = '.$_SESSION['id'].' AND id = '.$id;
	$result = mysql_query($sql);

	header('Content-Description: File Transfer'); 
	header("Content-Type: application/vnd.ms-excel");
	//descarga el archivo
	header("Content-disposition: attachment; filename=".$nombre.".xls");

	//crea el resumen del proyecto
	while( $row = mysql_fetch_array($result) ){
		$nombre = $row['nombre'];

		$cuerpo .= '<tr>
			<td '.$columna.'>'.$row['nombre'].'</td>
			<td colspan="3"'.$columna.'>'.$row['descripcion'].'</td>
			<td '.$columna.'>'.$row['fecha'].'</td>
			<td '.$columna.'>';

		if($row['status'] == 1){
			$cuerpo .= 'Activo';
		}else{
			$cuerpo .= 'Finalizado';
		}

		$cuerpo .= '</td>
		</tr>';
	}

	//encabezados del exell
	$encabezado = '<table '.$tabla.'>
		<tr>
			<td colspan="6" '.$titulo.'>Resumen de '.$nombre.'</td>
		</tr>
		<tr>
			<td '.$columnaTitulo.'>Nombre</td>
			<td colspan="3"'.$columnaTitulo.'>Descripcion</td>
			<td '.$columnaTitulo.'>Fecha</td>
			<td '.$columnaTitulo.'>Estado</td>
		</tr>';

	$imagen = $_SESSION['home'].'/images/logoExcel.png';

	//informacion del informe
	$footer = '<tr>
			<td colspan="6" '.$tituloInfo.'> Generado Automaticamente</td>
			</tr>';

	$footer .= '<tr>
				<td '.$columnanInfo.'>Fecha:</td>
				<td colspan="3" '.$columnanInfo.'>'.date("F j Y - g:i a").'</td>
				<td rowspan="3" colspan="2" '.$logo.'>
					<img style="text-align: center; vertical-align: center; margin: 0 auto;" src="'.$imagen.'">
				</td>';

	$footer .= '<tr>
				<td '.$columnanInfo.'>Por:</td>
				<td colspan="3" '.$columnanInfo.'>'.$_SESSION['nombre'].'</td>
				</tr>';

	$footer .= '<tr>
			<td '.$columnanInfo.'>Generado en:</td>
			<td colspan="3" '.$columnanInfo.'> 
				<a href="'.$_SESSION['home'].'">Escala.com</a>
			</td>
			</tr>
		</table>';

	//crea detalles del proyecto
	$detalles = detalles($id);
	$notas = notas($id);

	//imprime el archivo 
	echo $encabezado.$cuerpo.$notas.$detalles.$footer;
}

/*
	detalles del informe
	@param return $detalle 
*/
/*
function detalles($id){
	//estilos
	$titulo = 'style="background-color: #6fa414; font-bold: bold; color: #fff; font-size: 18pt; text-align: center;"';
	$columnaTitulo = 'style="background-color: #a1ca4a; color: #fff; font-bold: bold; font-size: 16pt; text-align: center;"';
	$tituloCategoria = 'style="background-color: #757374; font-bold: bold; color: #fff; font-size: 18pt; text-align: center;"';
	$columna = 'style="text-align: left; font-size: 14pt;"';

	$detalles = '
	<tr>
		<td colspan="6" '.$titulo.'>GENERALIDADES</td>
	</tr>
	<tr>
		<td '.$columnaTitulo.'>N DE NORMA</td>
		<td '.$columnaTitulo.'>NOMBRE DE NORMA</td>
		<td '.$columnaTitulo.'>REQUISITO LEGAL</td>
		<td '.$columnaTitulo.'>RESUMEN</td>
		<td '.$columnaTitulo.'>PERSIMOS</td>
		<td '.$columnaTitulo.'>ENTIDAD COMPETENTE</td>
	</tr>';

	$sql = 'SELECT DISTINCT categoria FROM registros WHERE proyecto = '.$id;
	$result = mysql_query($sql);

	while($row = mysql_fetch_array($result)){

		$detalles .= infoCategoria($row['categoria']);

		$sql2 = 'SELECT DISTINCT norma FROM registros WHERE categoria = '.$row['categoria'];
		$result2 = mysql_query($sql2);
		
		while($row2 = mysql_fetch_array($result2)){
			$detalles .= infoNorma($row2['norma']);
		}
	}

	return $detalles;
}

//para el titulo de la categoria
function infoCategoria($categoria){
	$tituloCategoria = 'style="background-color: #f4f4f4; font-bold: bold; color: #757374; font-size: 18pt; text-align: center;"';

	$sql = 'SELECT * FROM categorias WHERE id = '.$categoria;
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	//titulo
	return '<tr><td colspan="6" '.$tituloCategoria.'>'.$row['nombre'].'</td></tr>';
}

//devuelve la informacion de todas las normas de una categoria
function infoNorma($id){
	$columna = 'style="text-align: left; font-size: 14pt; border: 1px solid #f4f4f4;"';

	$info = '';
	$sql = 'SELECT * FROM normas WHERE id = '.$id;
	$result = mysql_query($sql);
	
	$row = mysql_fetch_array($result);
		$info .= '<tr>';
		$info .= '<td '.$columna.'>'.$row['numero'].'</td>';
		$info .= '<td '.$columna.'>'.$row['nombre'].'</td>';
		$info .= '<td '.$columna.'>'.$row['resumen'].'</td>';
		$info .= '<td '.$columna.'>'.$row['requisito'].'</td>';
		$info .= '<td '.$columna.'>'.$row['permisos'].'</td>';
		$info .= '<td '.$columna.'>'.$row['entidad'].'</td>';
		$info .= '</tr>';

	return $info;
}

function notas($id){
	$titulo = 'style="background-color: #6fa414; font-bold: bold; color: #fff; font-size: 18pt; text-align: center;"';

	$sql = 'SELECT * FROM notas WHERE proyecto = '.$id;
	$result = mysql_query($sql);

	$notas = '';
	$c = 0;
	
	$notas .= '<tr>
			<td colspan="6" '.$titulo.'>
				Notas
			</td>
		</tr>';

	$a = 0;
	while($row = mysql_fetch_array($result)){
		//intercala los colores de la fila
		if($a == 0){
			$columnaNota = 'style="background-color: #f4f4f4; color: #757374; text-align: left; font-size: 14pt;  vertical-align: middle;"';
			$a++;
		}else{
			$a = 0;
			$columnaNota = 'style="background-color: #fff; color: #757374; text-align: left; font-size: 14pt;  vertical-align: middle;"';
		}

		$notas .= '<tr class="filaNotaResumen" id="nota'.$row['id'].' ">
				<td colspan="4" '.$columnaNota.'>
					'.$row['nota'].'
				</td>
				<td '.$columnaNota.'>
				';
		$notas .= datosCliente($row['cliente']).'
				</td>
				<td '.$columnaNota.'>';
		$notas .= imagenCliente($row['cliente']).'
				</td>
			</tr>';
		$c++;
	}

	return $notas;
}

function datosCliente($id){
	$datos = '';
	$sql = 'SELECT * FROM clientes WHERE id = '.$id;
	$result = mysql_query($sql);
	
	while($row = mysql_fetch_array($result)){

		$datos .= $row['nombre'].'<br/>'.$row['fecha'];
	}
	return $datos;
}

function imagenCliente($id){
	$datos = '<img src="'.$_SESSION['home'].'/images/users/user.png" >';
	return $datos;
}

***/



require_once("classDatabase.php");
require_once("session.php");
require_once("proyectos.php");
require_once("usuarios.php");
require_once("registros.php");

$exportar = new Exportar();
$exportar->ExportarExcel($_GET['id']);

/**
* CLASE PARA EXPORTAR UN INFOME
*/
class Exportar{ 
	private $session = ''; 
	private $id = '';
	private $informe = "";
	
	public function __construct(){
		$this->session = new Session();
		//seguridad que este logueado
		$this->session->Logueado();
		//
	}

	/**
	* EXPORTA EL INFORME CREADO
	* @param $proyecto -> id del proyecto ha ser exportado
	*/
	public function ExportarExcel($proyecto){
		$this->id = $proyecto;

		$this->CrearInforme();

		/*if($this->CrearInforme()){
			$this->DescargarInforme();
		}else{	
			return false;
		}*/
	}

	/**
	* CREA EL INFORME
	* @return true si se creo el informe.
	* @return false si fallo la creacion del informe
	*/
	private function CrearInforme(){
		$this->Cabecera();
		$this->CuerpoGeneralidades();
		$this->CuerpoNotas();
		$this->Footer();
		echo $this->informe;
	}

	/**
	* CREA LA CABEZERA
	*/
	private function Cabecera(){
		$proyecto = new Proyectos();
		$base = new Database();
		$query = "SELECT descripcion, fecha, status FROM proyectos WHERE id = ".$this->id;
		$datos = $base->Select($query);

		if(!empty($datos)){
			$this->informe .= '<table style="border: 1px solid #333; width: 100%;">
							     <thead>
							     	<tr>
							     		<td colspan="'.(sizeof($datos[0])+2).'">
							     			'.$proyecto->getProyectoDato("nombre", $this->id).'
							     		</td>
							     	</tr>
							     </thead>';
			
			//TITULOS
			$this->informe .= '<tr>';
			foreach ($datos[0] as $cabecera => $c) {
				if($cabecera == 'descripcion'){
					$this->informe .= '<td colspan="3" >'.$cabecera.'</td>';
					continue;
				}
				$this->informe .= '<td>'.$cabecera.'</td>';
			}
			$this->informe .= '</tr>';

			//LLENA DATOS
			foreach ($datos as $fila => $c) {
				$this->informe .= '<tr>';
				foreach ($datos[$fila] as $campo => $valor) {

					if($campo == "descripcion"){
						$this->informe .= '<td colspan="3">';
						$this->informe .= $valor.'</td>';
						continue;
					}

					if($campo == 'status'){
						$this->informe .= '<td>';
						if($valor == 1){
							$this->informe .= 'Activo';
						}else{
							$this->informe .= 'Finalizado';
						}
						$this->informe .= '</td>';
						continue;
					}

					$this->informe .= '<td>'.$valor.'</td>';
				}
				$this->informe .= '</tr>';
			}

		}else{
			return false;
		}
	}

	/**
	* CREA EL CUERPO, CARGA LAS GENERALIDADES Y NOTAS
	*/
	private function CuerpoNotas(){
		$cliente = new Cliente();
		$base = new Database();

		$query = "SELECT nota, cliente, fecha FROM notas WHERE proyecto = ".$this->id;
		$datos = $base->Select($query);

		if(!empty($datos)){

			//TITULOS NOTAS
			$this->informe .= '<tr>
							  	<td colspan="4">
							  		Notas
							  	</td>
							  	<td colspan="2">
							  		Usuario
							  	</td>
							  </tr>';

			$this->informe .= '<tr>';
			foreach ($datos[0] as $cabecera => $c) {
				if($cabecera == 'nota'){
					$this->informe .= '<td colspan="4" >'.$cabecera.'</td>';
					continue;
				}
				if($cabecera == 'cliente'){
					$this->informe .= '<td colspan="2" >'.$cabecera.'</td>';
					continue;
				}
			}
			$this->informe .= '</tr>';


			//CARAGA NOTAS
			foreach ($datos as $fila => $c) {
				$this->informe .= '<tr>';
				foreach ($datos[$fila] as $campo => $valor) {
					if($campo == 'nota'){
						$this->informe .= '<td colspan="4">'.$valor.'</td>';
						continue;
					}
					if($campo == 'cliente'){
						$this->informe .= '<td colspan="2">';
						$this->informe .= '<img height="50px" class="userImg" src="'.$cliente->getClienteDato("imagen", $valor).'" />';
						$this->informe .= $cliente->getClienteDato("nombre", $valor).'<br/>';
						$this->informe .= $datos[$fila]['fecha'];
						$this->informe .= '</td>';
						continue;
					}
				}
				$this->informe .= '</tr>';
			}

		}else{
			return false;
		}
	}

	/**
	* CREA EL CUERPO PARA LAS GENERALIDADES
	*/
	private function CuerpoGeneralidades(){
		$registro = new Registros();
		$base = new Database();
		$query = "SELECT DISTINCT norma FROM registros WHERE proyecto = ".$this->id;
		$query2 = "SELECT DISTINCT categoria FROM registros WHERE proyecto = ".$this->id;
		
		$normas = $base->Select($query);
		$categorias = $base->Select($query2);

		if(!empty($normas)){
			//echo '<pre>';
			//print_r($normas);
			//print_r($categorias);
			//echo '</pre>';
			
			//TITULO
			$this->informe .= '<tr>
							     <td colspan="6" class="superTitulo">
							     	Generalidades
							     </td>
							   </tr>
							   <tr>
							   		<td>
							   			N de Norma
							   		</td>
							   		<td>
							   			Nombre Norma
							   		</td>
							   		<td>
							   			Requisito Legal
							   		</td>
							   		<td>
							   			Resumen
							   		</td>
							   		<td>
							   			Permisos
							   		</td>
							   		<td>
							   			Entidad Competente
							   		</td>
							   </tr>';

			//CARGA normas				
			foreach ($categorias as $fila => $categoria) {
				$this->informe .= '<tr>
										<td colspan="6" class="subTitulo">
											'.$registro->getCategoriaDato("nombre", $categorias[$fila]['categoria']).'
										</td>
								   </tr>';
				foreach ($normas as $f => $norma) {
					$this->informe .= '<tr>';
					foreach ($normas[$f] as $key => $value) {
						$this->informe .= '<td>'.$registro->getGeneralidadDato("nombre", $categoria, $norma ).'<td>';
						$this->informe .= '<td>'.$registro->getGeneralidadDato("numero", $categoria, $norma ).'<td>';
						$this->informe .= '<td>'.$registro->getGeneralidadDato("requisito", $categoria, $norma ).'<td>';
						$this->informe .= '<td>'.$registro->getGeneralidadDato("resumen", $categoria, $norma ).'<td>';
						$this->informe .= '<td>'.$registro->getGeneralidadDato("permisos", $categoria, $norma ).'<td>';
						$this->informe .= '<td>'.$registro->getGeneralidadDato("entidad", $categoria, $norma ).'<td>';
					}
					$this->informe .= '</tr>';
				}
			}
		}else{
			return false;
		}
	}

	/**
	* COMPONE EL FOOTER DEL INFORME
	* MUESTRA INFORMACION
	*/
	private function Footer(){
		$this->informe .= '<tr>
								<td>
									Fecha:
								</td>
								<td colspan="3">
									'.date("F j Y - g:i a").'
								</td>
								<td rowspan="2">
									<img src="images/logoExcel.png" />
								</td>
							</tr>
							<tr>
								<td>
									Por:
								</td>
								<td colspan="3">
									'.$_SESSION['nombre'].'
								</td>
							</tr>
							<tr>
								<td>
									Generado en:
								</td>
								<td colspan="3">
									<a href="'.$_SESSION['home'].'">Matriz.com</a>
								</td>
							</tr>';
		$this->informe .= '</table>';
	}

	/**
	* DESCARGAR INFORME
	*/
	private function DescargarInforme(){

	}

}


?>