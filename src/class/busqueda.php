<?php

require_once('classDatabase.php');
require_once('registros.php');

class Busqueda{

	/**
	* CONFIGURACION INICIAL
	*/
	public function __contruct(){

	}

	/**
	* REALIZA UNA BUSQUEDA
	* @param string $buscar -> lo que se busca
	*/
	public function Buscar( $busqueda ){
		
		$resultados = '<div class="titulo">Busqueda</div>';

		//si tiene proyectos, solo proyectos propios
		if( $proyectos = $this->BuscarProyectos( $busqueda ) ){

			$resultados.= '<div class="subtitulo" title="resultados en tus proyectos">
						   	Proyectos
						   </div>';

			foreach ($proyecto as $fila => $proyecto) {
				$resultados .= '<div> 
									<span onclick="proyecto('.$proyecto['id'].')"> 
										'.$proyecto['nombre'].'
									</span>
								</div>';
			}

		}

		//tiene categorias, solo categorias incluidas
		if( $categorias = $this->BuscarCategorias( $busqueda ) ){
			$resultados.= '<div class="subtitulo" title="resultados en categorias">
						   	Categorias
						   </div>';

			foreach ($categorias as $fila => $categoria) {
				$resultados .= '<div> 
									<span> 
										'.$proyecto['nombre'].'
									</span>
								</div>';
			}
		}

		//tiene normas, solo normas incluidas
		if( $categorias = $this->BuscarCategorias( $busqueda ) ){
			$resultados .= '<div class="subtitulo" title="resultados en categorias">
						   	Categorias
						   </div>';

			foreach ($categorias as $fila => $categoria) {
				$resultados .= '<div> 
									<span> 
										'.$proyecto['nombre'].'
									</span>
								</div>';
			}
		}

		echo $resultados;
	}

	/**
	* BUSCA EN LOS PROYECTOS DEL CLIENTE
	* @param string $busqueda -> ha buscar
	* @return array $proyectos -> con los datos
	* @return boolean false -> si falla
	*/
	public function BuscarProyectos( $busqueda ){
		$base = new Database();

		$busqueda = mysql_real_escape_string($busqueda);

		$query = "SELECT * FROM proyectos WHERE nombre LIKE '".$busqueda."' AND cliente = '".$_SESSION['cliente_id']."' LIMIT 0, 10";

		if( $proyectos = $base->Select( $query ) ){
			return $proyectos;
		}esle{
			return false;
		}

	}

	/**
	* BUSCA EN LAS CATEGORIAS INCLUIDAS DEL USUARIO
	* @param string $busqueda 
	*/
	private function BuscarCategorias( $busqueda ){
		$base =  new Database();

		$busqueda = mysql_real_escape_string($busqueda);

		$query = "SELECT * FROM categorias WHERE nombre LIKE '".$busqueda."' LIMIT 0. 10";
		$categorias = $base->Select( $query );

		//si hay resultados
		if( !empty($categorias) ){
			$resultado = array();

			foreach ($categorias as $fila => $categoria) {
				$query = "SELECT * FROM registros WHERE categoria = '".$categoria['id']."'";

				if( $incluida = $base->Select( $query ) ){

					//si el proyecto pertenece al usuario
					if( $this->proyectoPropio( $incluida[0]['proyecto']) ){
						$resultado[] = array('nombre'=>$categoria['nombre'], 'id' => $categoria['id'], 'proyecto'=>$incluida[0]['proyecto'], 'registro'=>$incluida[0]['registro'] );
					}

				}
			}

			return $resultado;

		}else{
			return false;
		}
	}


	/*************** MISELANIA *************************/

	/**
	* REVISA QUE EL PROYECTO SEA DEL USUARIO
	* @param string $proyecto -> id del proyecto
	*/
	private function proyectoPropio( $proyecto ){
		$base = new Database();

		$query = "SELECT * FROM proyectos WHERE cliente = '".$_SESSION['cliente_id']."' ";

		$datos = $base->Select( $query );

		if( !empty($datos) ){
			return true;
		}else{
			return false;
		}

	}

	/**
	* VERIFICA QUE UA NORMA SEA PROPIA
	* @param int $norma -> id de la norma
	* @return boolean true -> si es valida
	* @return boolean false -> si es falsa
	*/
	private function normaPropia( $normaId ){

		$base = new Database();

		$normaId = mysql_real_escape_string( $normaId );

		$query = "SELECT * FROM registros_normas WHERE normas = '".$normaId."'";

		if( $incluidas = $base->Select( $query ) ){
			
			foreach ($incluidas as $f => $norma) {
				if( $this->proyectoPropio( $norma['proyecto'] ) ){

				}
			}
		}else{
			return false;
		}
	}
}


?>