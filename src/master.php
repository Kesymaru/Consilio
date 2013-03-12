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

		$datos = $cliente->getDatosCliente( $_SESSION['cliente_id'] );
		$logo = $_SESSION['datos'].$datos[0]['imagen'];

		echo '<div class="logoCliente">
				<img title="'.$datos[0]['nombre'].'" id="logoCliente" src="'.$logo.'" />
				<p>#'.$datos[0]['registro'].'</p>
			</div>';
	}

	/**
	* BUSQUEDA PARA UN CLIENTE
	* PERMITE LA BUSQUEDA DE PROYECTOS, NORMAS Y LEYES
	*/
	public function Buscar($busqueda){
		$resultados = '<div class="titulo" >
							Busqueda
						</div>';


		echo $resultados;
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

				echo '<li id="menuProyecto'.$proyecto['id'].'" onClick="Proyecto('.$proyecto['id'].')">'.$proyecto['nombre'].'</li>';

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

		$lista = '<div class="titulo">
					Mis Proyectos
				  </div>';
		

		if(!empty($datos)){

			$columna = '';

			if(3 <= sizeof($datos) ){
				$columna = 3;
				$lista .= '<table class="mis-proyectos td3">';
			}else if( 2 == sizeof($datos) ){
				$columna = 2;
				$lista .= '<table class="mis-proyectos td2">';
			}else{ 
				$columna = 1;
				$lista .= '<table class="mis-proyectos">';
			}

			$cuenta = 1;

			foreach ($datos as $fila => $proyecto) {

				if($cuenta == 1){
					$lista == '<tr>';
				}
				if($cuenta <= $columna){
					$lista .= '<td onClick="Proyecto('.$proyecto['id'].')" class="columna'.$columna.' cl'.$cuenta.'">';
				}

				$cliente = new Cliente();
					//primer fallback usa la del usuario
				$imagen = $_SESSION['datos'].$cliente->getClienteDato("imagen", $_SESSION['cliente_id']);
				$imagen = $_SESSION['datos'].$proyecto['imagen'];

				$lista .= '<table class="proyecto-detalles">
							<tr>
								<td class="proyecto-img" title="'.$proyecto['nombre'].'">';

				$lista .= '<img src="'.$imagen.'" onerror="this.src=\'images/es.png\'" >';

				$lista .= '</td><td>
								<div class="proyecto-titulo">
						   	 		'.$proyecto['nombre'].'
						   		</div>';
				
				$lista .= '		<div class="proyecto-decripcion">
						   	 		'.base64_decode($proyecto['descripcion']).'
						   		</div>';

				$lista .= '		</td>
						   </tr>
						   </table><!-- end detalles proyecto -->';

				if($cuenta <= $columna){
					$lista .= '</td>';
				}
				if($cuenta == $columna ){
					$lista .= '</tr>';
					$cuenta = 0;
				}

				$cuenta++;
			}

			$lista .= '</div><!-- end proyecto detalles -->';
		}else{
			$lista .= "<tr>
						<td>
						No tienes proyectos.
					   </td>
					   <tr>";
		}

		$lista .= '</table>';

		echo $lista;
	}

	/**
	* VALIDA UN PROYECTO DE UN CLIENTE
	* @param int $proyecto -> id del proyecto
	* @return boolean true es valido
	* @return boolean false no lo es
	*/
	public function proyectoValido($proyecto){
		$proyectos = new Proyectos();

		if( $proyectos->getProyectoUsuario($proyecto, $_SESSION['cliente_id']) ){
			return true;
		}else{
			return false;
		}
	}
}

?>