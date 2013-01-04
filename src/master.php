<?php
/**
* CLASE MASTER 
*/
	require_once("class/mail.php"); 
	require_once('class/session.php');
	require_once('class/classDatabase.php');
	require_once('class/usuarios.php');

class Master{

	/**
	* AL SER DECLARADO DETERMINA SI EL USUARIO ESTA LOGUEADO
	*/
	public function __construct(){
		$session = new Session();
		//seguridad de que el usuario este logueado
		$session->Logueado();
	}

/*** METODOS DE BUSQUEDA ***/

	/**
	* FUNCTIONALIDAD DE BUSQUEDA
	* @param $busqueda 
	*/
	public function Buscar($buscar){
		//$normas = $this->BuscarNormas($buscar);
		$normas = '';
		$categorias = $this->BuscarCategorias($buscar);
		$proyectos = $this->BuscarProyectos($buscar);
		
		if( $normas == '' && $categorias == '' && $proyectos == ''){
			echo '<div id="mensajeInicial">
					No hay resultados para '.$buscar.'
				  </div>';
		}else{
			echo $normas;
			echo $categorias;
			echo $proyectos;
		}
	}

	//realiza busqueda en normas
	private function BuscarNormas($busqueda){

		$consultas = array( 0 => 'nombre', 1 => 'numero', 2 => 'requisito', 3 => 'permisos', 4 => 'entidad', 5 => 'resumen');
		$resultadoTemp = '';
		$resultado = '';
		$contador = 0;

		$base = new Database();

		foreach ($consultas as $consulta => $value) {

				$query = "SELECT * FROM normas WHERE ".$consultas[$consulta]." LIKE '%".$busqueda."%' LIMIT 0, 30";
				$datos = $base->Select($query);

				if(!empty($datos)){
					foreach ($datos as $fila => $c) {

						//etiqueta
						$resultadoTemp .= '<div class="resultado">
												<ul class="etiqueta"><li><a href="#">';
						
						if($consultas[$consulta] == 'nombre'){
							$resultadoTemp .= 'Norma';
						} else if($consultas[$consulta] == 'numero'){
							$resultadoTemp .= 'N° Norma';
						}else{
							$resultadoTemp .= $consultas[$consulta];
						}

						//resultado
						$resultadoTemp .= '</a></li></ul>
						 '.$datos[$fila][$consultas[$consulta]].'</div>';
						
						$contador++;
					}
				}	
		}

		if(!empty($resultadoTemp)){
			$resultado .= $this->Plural($contador, "Norma");
			$resultado .= $resultadoTemp.'</div>';

			return $resultado;
		}else{
			return '';
		}
	}


	/**
	* BUSCA EN CATEGORIAS
	* @param $busqueda
	* @return false -> sino hay resultados
	*/
	private function BuscarCategorias($busqueda){
		$contador = 0;
		$resultado = '';
		$resultadoTemp = '';

		//SELECCIONA LAS CATEGORIAS
		$query = "SELECT * FROM categorias WHERE nombre LIKE '%".$busqueda."%' LIMIT 0, 30";
		$base = new Database();
		
		$categorias = $base->Select($query);

		if(!empty($categorias)){
			foreach ($categorias as $fila => $categoria) {
				$resultadoTemp .= '<div class="resultado"><ul class="etiqueta"><li><a href="#">Categoria';
				$resultadoTemp .= '</a></li></ul>'.$categoria['nombre'].'</div>';
				$contador++;
			}

			$resultado .= $this->Plural($contador, "Categoria");
			$resultado .= $resultadoTemp."</div>";
			return $resultado;
		}else{
			return '';
		}
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


/******************************** METODOS UTILITARIOS ********************/

	/**
	* DATOS PARA EL MENU DEL ADMIN
	*/
	public function MenuAdmin(){
		$admin = new Admin();

		echo '<li onClick="editar();"><img src="';
		echo $admin->getAdminDato("imagen");
		echo '" /></li>';

		echo '<li><button onClick="EditarAdmin();">Editar</button>';
		echo '<button onClick="LogOut();">Salir</button></li>';

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
	* IMPRIME LOS DATOS DE LA SESSION
	*/
	public function ImprimirSession(){
		echo $_SESSION['nombre'];
		echo $_SESSION['id'];
	}


	/**
	* MENU DE EDICIONs
	*/
	public function MenuEdicion(){
		echo '<li onClick="VistaEdicion(\'categorias\')">Categorias</li>';
		echo '<li onClick="VistaEdicion(\'normas\')">Normas</li>';
		echo '<li onClick="VistaEdicion(\'entidades\')">Entidades</li>';
		echo '<li onClick="VistaEdicion(\'tipos\')">Tipos normas</li>';
		//echo '<li></li>';
	}

	/**
	 * MENU DE CLIENTES
	 */
	public function MenuClientes(){
			echo '<li onClick="Clientes()">Clientes</li>';
	}

	/**
	 * MENU DE PROYECTOS
	 */
	public function MenuProyectos(){
			echo '<li onClick="Proyectos()">Proyectos</li>';
	}

}

?>