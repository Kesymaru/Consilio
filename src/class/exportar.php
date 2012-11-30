<?php


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
	}

	/**
	* EXPORTA EL INFORME CREADO
	* @param $proyecto -> id del proyecto ha ser exportado
	*/
	public function ExportarExcel($proyecto){
		$this->id = $proyecto;

		$this->CrearInforme();

		header('Content-Description: File Transfer'); 
		header("Content-Type: application/vnd.ms-excel");
		//descarga el archivo
		header("Content-disposition: attachment; filename=".$this->nombreProyecto.".xls");

		echo $this->informe;
	}

	/**
	* EXPORTA EN PDF
	* @param $proyecto -> id del proyecto
	*/
	public function ExportarPdf($proyecto){

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
		//aplica el estilo al informe
		$this->Style();
	}

	/**
	* CREA LA CABEZERA
	*/
	private function Cabecera(){
		$proyecto = new Proyectos();
		$base = new Database();
		$query = "SELECT nombre, descripcion, fecha, status FROM proyectos WHERE id = ".$this->id;
		$datos = $base->Select($query);

		if(!empty($datos)){
			$this->nombreProyecto = $proyecto->getProyectoDato("nombre", $this->id);
			$this->informe .= '<table class="Informe" >
							     <thead>
							     	<tr>
							     		<td colspan="'.(sizeof($datos[0])+2).'" class="SuperTitulo" >
							     			Resumen De '.$this->nombreProyecto.'
							     		</td>
							     	</tr>
							     </thead>';
			
			//TITULOS
			$this->informe .= '<tr>';
			foreach ($datos[0] as $cabecera => $c) {
				if($cabecera == 'descripcion'){
					$this->informe .= '<td colspan="3" class="SubTitulo" >'.$cabecera.'</td>';
					continue;
				}
				if($cabecera == 'status'){
					$this->informe .= '<td class="SubTitulo" >Estado</td>';
					continue;
				}
				$this->informe .= '<td class="SubTitulo" >'.$cabecera.'</td>';
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

		//TITULOS NOTAS
		$this->informe .= '<tr>
								<td colspan="6" class="SuperTitulo" >
									Notas
								</td>
							</tr>
							<tr>
							  <td colspan="4" class="SubTitulo" >
							  	Notas
							  </td>
							  <td colspan="2" class="SubTitulo" >
							  	Usuario
							  </td>
							</tr>';

		if(!empty($datos)){

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
						$this->informe .= $cliente->getClienteDato("nombre", $valor).'<br/>';
						$this->informe .= $datos[$fila]['fecha'];
						$this->informe .= '</td>';
						continue;
					}
				}
				$this->informe .= '</tr>';
			}

		}else{
			$this->LineaBasia();
		}
	}

	/**
	* CREA EL CUERPO PARA LAS GENERALIDADES
	*/
	private function CuerpoGeneralidades(){
		$registro = new Registros();
		$base = new Database();

		$query = "SELECT DISTINCT categoria FROM registros WHERE proyecto = ".$this->id;
		$categorias = $base->Select($query);

		//TITULO
		$this->informe .= '<tr>
							    <td colspan="6" class="SuperTitulo" >
							     	Generalidades
							    </td>
							</tr>
							<tr>
							   		<td class="SubTitulo" >
							   			N de Norma
							   		</td>
							   		<td class="SubTitulo" >
							   			Nombre Norma
							   		</td>
							   		<td class="SubTitulo" >
							   			Requisito Legal
							   		</td>
							   		<td class="SubTitulo" >
							   			Resumen
							   		</td class="SubTitulo" >
							   		<td class="SubTitulo" >
							   			Permisos
							   		</td>
							   		<td class="SubTitulo" >
							   			Entidad Competente
							   		</td>
							</tr>';

		if(!empty($categorias)){
			//echo '<pre>';
			//print_r($categorias);
			//echo '</pre>';

            foreach ($categorias as $fila => $v) {
            	$this->informe .= '<tr>
            							<td colspan="6" class="Categoria" >
            								'.$registro->getCategoriaDato("nombre",$categorias[$fila]['categoria']).'
            							</td>
            						</tr>';
            	
            	$this->Generalidades($categorias[$fila]['categoria']);

            }

		}else{
			$this->LineaBasia();
		}
	}

	/**
	* METODO PARA CARGAR LOS DATOS DE UNA GENERALIDAD
	* @param $categoria -> id de la categoria
	* @return true si se compuso correctamente
	* @return false si fallo
	*/
	private function Generalidades($categoria){
		$registro = new Registros();

		$norma = $registro->getNormas($categoria, $this->id);
            	
        if(!empty($norma)){
        	foreach ($norma as $f => $c) {
            	$this->informe .= '<tr>';

            	foreach ($norma[$f] as $campo => $valor) {
            		if($campo == 'id'){
            				continue;
            		}
            		$this->informe .= '<td>'.$valor.'</td>';
            	}

            	$this->informe .= '</tr>';
            }

            return true;
        }else{
        	$this->LineaBasia();
        }   	
	}

	/**
	* COMPONE EL FOOTER DEL INFORME
	* MUESTRA INFORMACION
	*/
	private function Footer(){
		$this->informe .= '<tr>
								<td colspan="6" class="SubTitulo">
									Generado Automaticamente
								</td>
							</tr>
							<tr>
								<td class="Footer" >
									Fecha:
								</td>
								<td colspan="3" class="Footer" >
									'.date("F j Y - g:i a").'
								</td>
								<td rowspan="3" colspan="2" class="FooterImage" >
									<img src="'.$_SESSION['home'].'/images/logoExcel.png" />
								</td>
							</tr>
							<tr>
								<td class="Footer" >
									Por:
								</td>
								<td colspan="3" class="Footer">
									'.$_SESSION['nombre'].'
								</td>
							</tr>
							<tr>
								<td class="Footer" >
									Generado en:
								</td>
								<td colspan="3" class="Footer" >
									<a href="'.$_SESSION['home'].'">Escala.com</a>
								</td>
							</tr>';
		$this->informe .= '</table>';
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

			'class="Footer"' => 'style="background-color: #D5D4D5; color: #333333; text-align: left;"',
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