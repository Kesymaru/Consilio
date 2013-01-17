<?php

/**
* CLASE MASTER METODOS PARA EL INDEX.PHP
*/

require_once('class/session.php');
require_once('class/classDatabase.php');
require_once('class/usuarios.php');
require_once('class/proyectos.php');

class Master{

	/**
	* AL SER DECLARADO DETERMINA SI EL USUARIO ESTA LOGUEADO
	*/
	public function __construct(){
		$session = new Session();
		//seguridad de que el usuario este logueado
		$session->Logueado();
	}

	/**
	* PONE EL LOGO DEL CLIENTE AL LADO DEL DE ESCALA
	*/
	public function Logo(){
		$cliente = new Cliente();

		$logo = $cliente->getClienteDato("imagen", $_SESSION['cliente_id']);
		$logo = $_SESSION['datos'].$logo;
		
		//SI EL CLIENTE TIENE IMAGEN
		if(file_exists($logo)){
			echo '<div class="logoCliente">
				<img id="logoCliente" src="'.$logo.'" />
				</div>';
		}
	}

	/**
	* BUSQUEDA PARA UN CLIENTE
	* PERMITE LA BUSQUEDA DE PROYECTOS, NORMAS Y LEYES
	*/
	public function Buscar($busqueda){

	}

	//busca en proyectos, presenta solo los del cliente logueado
	private function BuscarProyectos($busqueda){
		$contador = 0;
		$resultado = '';
		$resultadoTemp = '';

		$query = "SELECT * FROM proyectos WHERE nombre LIKE '%".$busqueda."%' LIMIT 0, 30";
		$base = new Database();
		
		$datos = $base->Select($query);

		if(!empty($datos)){
			foreach ($datos as $fila => $c) {
				$resultadoTemp .= '<div class="resultado"><ul class="etiqueta"><li><a href="#">Proyecto';
				$resultadoTemp .= '</a></li></ul>'.$datos[$fila]['nombre'].'</div>';
				$contador++;
			}
			$resultado .= $this->Plural($contador, "Proyecto");
			$resultado .= $resultadoTemp."</div>";
			return $resultado;
		}else{
			return '';
		}
	}


/******************************** MENUS ********************/

	/**
	* DATOS PARA EL MENU DEL ADMIN
	*/
	public function MenuCliente(){
		$cliente = new Cliente();

		//$imagen = $cliente->getClienteDato("imagen", $_SESSION['cliente_id']);

		//echo '<li class="user-imge"><img src="'.$_SESSION['datos'].$imagen.'" /></li>';
		echo '<li onClick="LogOut();">Salir</li>';

	}

	/**
	* HERRAMIENTA PARA LA BUSQUEDA PONE PLURALES Y LA CANTIDAD DE RESULTADOS
	*/
	private function Plural($contador, $titulo){
		$plural = '';
		if($contador > 0){
			$plural .= '<div class="resultados">
							<div class="titulo">'.$contador.' Resultado';
			if($contador > 1){
				$plural .='s'; //plural para resultado(s)
			}

			$plural .= ' para '.$titulo;
			if($contador > 1){
				$plural .='s'; //plural para Categoria(s)
			}
			$plural .= "</div>";

			return $plural;
		}
	}

	/**
	 * MENU DE PROYECTOS
	 */
	public function MenuProyectos(){
		$proyectos = new Proyectos();
		$datos = $proyectos->getProyectos($_SESSION['cliente_id']);

		if(!empty($datos)){
			foreach ($datos as $key => $proyecto) {
				echo '<li onClick="Proyecto('.$proyecto['id'].')">'.$proyecto['nombre'].'</li>';
			}
		}else{
			echo '<li>No hay proyectos</li>';
		}
	}

/************************ PROYECTOS *************/
	
	/**
	* MUESTRA LA LISTA DE PROYECTOS DEL USUARIO
	*/
	public function Proyectos(){
		$proyectos = new Proyectos();
		$datos = $proyectos->getProyectos($_SESSION['cliente_id']);

		$lista = '<div class="titulo" >
					Mis Proyectos
				</div>
					<table class="table-list">';

		if(!empty($datos)){
			$lista .= '<tr>
							<td>
								Nombre
							</td>
							<td>
								Descripcion
							</td>
						</tr>';

			foreach ($datos as $key => $proyecto) {
				$lista .= '<tr onClick="Proyecto('.$proyecto['id'].')" class="custom-tooltip" title="'.$_SESSION['datos'].$proyecto['imagen'].'" >
								<td>
									'.$proyecto['nombre'].'
								</td>
								<td>
									'.base64_decode($proyecto['descripcion']).'
								</td>
						   </tr>';
			}

		}else{
			$lista .= '<tr>
						<td colspan="3">
							No Tienes Proyectos Aun.
							<br/>
						</td>
						</tr>';
		}

		$lista .= '</table>';

		echo $lista;
	}

}

?>