<?php
/**
* CLASE MASTER PARA UN CLIENTE
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
		//EL USUARIO NO REQUIERE ESTAR LOGUEADO
		$session = new Session();
		//seguridad de que el usuario este logueado
		$session->Logueado();
	}

/*** METODOS PARA PROYECTOS ***/

	/**
	* MENU CON TODOS LOS PROYECTOS
	*/
	public function MenuProyectos(){

		$master = new Database();
		$datos = $master->Select("Select * FROM proyectos");

		if(!empty($datos)){
			
			foreach ($datos as $fila => $c) {
				echo '<li onClick="proyecto('.$datos[$fila]['id'].')">'.$datos[$fila]['nombre'].'</li>';
			}
		
		}else{
			echo '<li><button id="botonNuevoProyecto" onClick="proyectoNuevo();">Crear Nuevo</button>';
		}
	}

	/**
	* MENU PROYECTOS CON SELECCION DE BUSQUEDA
	*/
	public function MenuProyectosBuscar($busqueda){

		$master = new Database();
		$datos = $master->Select("Select * FROM proyectos WHERE nombre LIKE '%".$busqueda."%' LIMIT 0, 30");

		if(!empty($datos)){
			
			foreach ($datos as $fila => $c) {
				echo '<li onClick="proyecto('.$datos[$fila]['id'].')">'.$datos[$fila]['nombre'].'</li>';
			}
		
		}else{
			echo '<li><button id="botonNuevoProyecto" onClick="proyectoNuevo();">Crear Nuevo</button>';
		}
	}

	/**
	* GUARDA EL PROYECTO
	*/
	public function nuevoProyecto($proyecto, $descripcion){
		$descripcion = mysql_real_escape_string($descripcion);
		$fecha = date("d-m-Y");

		$query = "INSERT INTO proyectos (nombre, descripcion, fecha, cliente, status) VALUES ('".$proyecto."','".$descripcion."', '".$fecha."', '".$_SESSION['id']."', 1 )";

		$base = new Database();
		$base->Insert($query);
	}

/*** METODOS DE BUSQUEDA ***/

	/**
	* FUNCTIONALIDAD DE BUSQUEDA
	* @param $busqueda 
	*/
	public function Buscar($buscar){
		$normas = $this->BuscarNormas($buscar);
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

		$query = "SELECT * FROM categorias WHERE nombre LIKE '%".$busqueda."%' LIMIT 0, 30";
		$base = new Database();
		
		$datos = $base->Select($query);

		if(!empty($datos)){
			foreach ($datos as $fila => $c) {
				$resultadoTemp .= '<div class="resultado"><ul class="etiqueta"><li><a href="#">Proyecto';
				$resultadoTemp .= '</a></li></ul>'.$datos[$fila]['nombre'].'</div>';
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


/***

/*** METODOS UTILITARIOS ***/

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

}

?>