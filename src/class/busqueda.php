<?php

require_once('classDatabase.php');
require_once('registros.php');

class Busqueda{

	private $misProyectos = array();

	public function __construct(){
		session_start();
	}

	/**
	* REALIZA UNA BUSQUEDA
	* @param string $buscar -> lo que se busca
	*/
	public function Buscar( $busqueda ){
				
		$this->getProyectos();

		$encontro = false;

		$resultados = '';
		/*$resultados = '
				<div class="titulo seleccionada" id="busqueda-titulo" >Busqueda</div>
						<div id="resultados"> ';*/

		//si tiene proyectos, solo proyectos propios
		if( $proyectos = $this->BuscarProyectos( $busqueda ) ){

			$encontro = true;

			$resultados.= '<div class="titulo" title="resultados en tus proyectos">
						   	Proyectos
						   </div>';

			foreach ($proyectos as $fila => $proyecto) {
				$resultados .= '<div class="resultado" onclick="Proyecto('.$proyecto['id'].')" title="Ir al proyecto '.$proyecto['nombre'].'" > 
									'.$proyecto['nombre'].'
								</div>';
			}

		}else{
			//no tiene proyecto no puede buscar
			if( empty($this->misProyectos) ){

				$resultados .= '<div class="titulo">No tienes proyectos</div>
					<div class="nodata">Aun no tienes proyectos</div>
					</div>';

				echo $resultados;
				return;
			}
		}

		//categorias
		if( $categorias = $this->BuscarCategorias($busqueda) ){
			$encontro = true;
			
			$resultados .= '<div class="titulo">Categorias</div>';

			foreach ($categorias as $f => $categoria) {
				$resultados .= '<div class="resultado">
									'.$categoria['nombre'].'
								</div>';
			}

		}

		//normas
		if( $normas = $this->BuscarNormas($busqueda) ){
			$encontro = true;
			
			$resultados .= '<div class="titulo" title="Resultados para normas">Normas</div>';

			foreach ($normas as $f => $norma) {
				$resultados .= '<div class="resultado">
									'.$norma['nombre'].'
								</div>';
			}

		}

		//articulos
		if( $articulos = $this->BuscarArticulos($busqueda) ){
			$encontro = true;
			
			$resultados .= '<div class="titulo" title="Resultados para articulos">Articulos</div>';

			foreach ($articulos as $f => $articulo) {
				$resultados .= '<div class="resultado">
									'.$articulo['nombre'].'
								</div>';
			}

		}

		if( !$encontro ){
			$resultados .= '<div class="titulo">Sin Resultados</div>
							<div class="nodata">
								No hay resultados para:
								<br/><br/>
								<q>'.$busqueda.'</q>
							</div>';
		}

		$resultados .= '<button type="button" onclick="closeResultados()">Cerrar</button>';

		echo $resultados;
	}

	/**
	* BUSCA EN LOS PROYECTOS DEL CLIENTE
	* @param string $busqueda -> ha buscar
	* @return array $proyectos -> con los datos
	* @return boolean false -> si falla
	*/
	private function BuscarProyectos( $busqueda ){
		$base = new Database();

		$busqueda = mysql_real_escape_string($busqueda);

		$query = "SELECT * FROM proyectos WHERE nombre LIKE '%".$busqueda."%' AND cliente = '".$_SESSION['cliente_id']."' AND visible = '1' LIMIT 0, 30";

		if( $proyectos = $base->Select( $query ) ){
			return $proyectos;
		}else{
			return false;
		}

	}

	/**
	* OBTIENE LOS PROYECTOS DEL CLIENTE
	*/
	private function getProyectos(){
		$base = new Database();

		$query = "SELECT * FROM proyectos WHERE cliente = '".$_SESSION['cliente_id']."' AND visible = '1' ";

		if( $proyectos = $base->Select( $query ) ){
			$this->misProyectos = $proyectos;
			return $proyectos;
		}else{
			return false;
		}
	}

	/**
	* BUSCA EN LAS CATEGORIAS INCLUIDAS DEL USUARIO
	* @param string $busqueda 
	* @return array $categorias -> categorias validas
	* @return boolean false -> no hay resultados
	*/
	private function BuscarCategorias( $busqueda ){
		$base = new Database();

		$busqueda = mysql_real_escape_string($busqueda);

		$query = "SELECT * FROM categorias WHERE nombre LIKE '%".$busqueda."%'";

		if( $posibles = $base->Select($query) ){
			//echo '<pre>'; print_r($posibles); echo '</pre>';
			
			$categorias = array();

			//verifica que este incluida
			foreach ($posibles as $f => $posible) {

				if( $this->validaCategoria( $posible['id'] ) ){

					$categorias[] = $posibles[$f];

				}
			}

			if( !empty($categorias) ){
				return $categorias;
			}else{
				return false;
			}

		}else{
			return false;
		}

	}

	/**
	* BUSCA NORMAS VALIDAS
	* @param string $busqueda 
	* @return string $normas -> array con las normas del cliente
	* @return boolean false -> si no tiene resultados
	*/
	private function BuscarNormas( $busqueda ){
		$base = new Database();

		$busqueda = mysql_real_escape_string($busqueda);

		$query = "SELECT * FROM normas WHERE nombre  LIKE '%".$busqueda."%' OR numero LIKE '%".$busqueda."%' AND status = '1' ";

		if( $posibles = $base->Select($query) ){
			//return $posibles;
			$normas = array();

			//valida que sea una norma incluida
			foreach ($posibles as $f => $posible) {

				if( $path = $this->validaNorma( $posible['id'] ) ){
					$posibles[$f]['nombre'] = $path . $posibles[$f]['nombre'];

					$normas[] = $posibles[$f];
				}
			}

			if( !empty($normas) ){
				return $normas;
			}else{
				return false;
			}

		}else{
			return false;
		}
	}

	/**
	* BUSCA ARTICULOS INCLUIDOS DEL CLIENTE
	* @param string 4busqueda
	* @return array $articulos -> array con los articulos validos
	* @return boolean false -> si no tiene resultados
	*/
	private function BuscarArticulos( $busqueda ){
		$base = new Database();

		$busqueda = mysql_real_escape_string($busqueda);

		$query = "SELECT * FROM articulos WHERE nombre LIKE '%".$busqueda."%' ";

		if( $posibles = $base->Select($query) ){
			//echo '<pre>'; print_r($posibles); echo '</pre>';

			//return $posibles;
			$articulos = array();

			//valida que sea una norma incluida
			foreach ($posibles as $f => $posible) {
				
				if( $path = $this->validaArticulo( $posible['id'] ) ){
					$posibles[$f]['nombre'] = $path . $posibles[$f]['nombre'];
					$articulos[] = $posibles[$f];
				}
			}

			if( !empty($articulos) ){
				return $articulos;
			}else{
				return false;
			}

		}else{
			return false;
		}
	}


	/*************** MISELANIA *************************/

	/**
	* VALIDA UN CATEGORIA
	* @param int $id -> id de la categoria
	* @return boolean true/false
	*/
	private function validaCategoria($id){
		$base = new Database;
		$id = mysql_real_escape_string($id);

		foreach ($this->misProyectos as $f => $proyecto) {
			$query = "SELECT * FROM registros WHERE proyecto = '".$proyecto['id']."' ";

			if( $categorias = $base->Select( $query )){
				//echo '<pre>'; print_r($categorias); echo '</pre>';
				
				foreach ($categorias as $fi => $categoria) {
					$incluidas = unserialize($categoria['registro']);

					foreach ($incluidas as $fil => $incluida) {
						$posible = explode( ',', $incluida);
						//echo '<pre>'; print_r($incluida); echo '</pre>';

						if( is_array($posible) ){
							//echo '<pre>'; print_r($posible); echo '</pre>';
							$x = sizeof( $posible )-1;							
							if( $posible[$x] == $id ){
								return true;
							}
						}
					}
				}
			}

		}

	}

	/**
	* VALIDA QIE UNA NORMA ESTE INCLUIDA
	* @param int $id -> id de la norma
	* @return boolean true -> si es valida
	* @return boolean false -> no es valida
	*/
	private function validaNorma( $id ){
		$base = new Database;
		$id = mysql_real_escape_string($id);

		foreach ( $this->misProyectos as $f => $proyecto ) {
			$query = "SELECT * FROM registros_normas WHERE proyecto = '".$proyecto['id']."' ";

			if( $datos = $base->Select( $query ) ){
				
				//echo 'incluidas<pre>'; print_r($datos); echo '</pre>';

				foreach ($datos as $fi => $incluida) {

					$incluidas = unserialize( $incluida['registro'] );
					
					//echo '<pre>'; print_r($incluida); echo '</pre>';
					$categoria = $incluida['categoria'];

					if( is_array($incluidas) ){
					//echo 'cat '.$categoria.'<pre>'; print_r($incluidas); echo '</pre><hr>';

						//si la norma esta incluida
						if( in_array($id, $incluidas) ){
							if( $this->validaCategoria($categoria) ){
								return $this->normaPath($categoria);
							}
						}

					}
					
				} //end foreach datos
			} //end if
		}
		return false;
	}

	/**
	* VALIDA QUE UN ARTICULO ESTE INCLUIDO
	* @param int $id -> id del articulo
	* @return int $categoria -> id de la categoria a la que pertenece
	* @return boolean false -> no es valida
	*/
	private function validaArticulo($id){
		$base = new Database;
		$id = mysql_real_escape_string($id);

		foreach ($this->misProyectos as $f => $proyecto) {

			$query = "SELECT * FROM registros_articulos WHERE proyecto = '".$proyecto['id']."' ";

			if( $datos = $base->Select( $query ) ){

				//echo '<pre>'; print_r($datos); echo '</pre>';

				foreach ($datos as $fil => $incluida) {
					//echo '<hr><pre>'; print_r($incluida); echo '</pre>';
					$incluidas = unserialize( $incluida['registro'] );
					$categoria = $incluida['categoria'];
					$norma = $incluida['norma'];

					if( is_array($incluidas) ){
						//echo '<hr><pre>'; print_r($incluidas); echo '</pre>';
						
						//si la norma esta incluida
						if( in_array($id, $incluidas) ){
							if( $this->validaCategoria($categoria) ){
								
								/*if( $x = $this->validaNorma($norma) ){
									
								}else{
									
								}*/
								return $this->articuloPath($categoria, $norma);
							}
						}

					}

				}// end foreach

			} //end if
		}
	}

	/**************** PATHS **********/

	/**
	* OBTIENE EL PATH DE UNA NORMA
	*/
	public function normaPath($categoria){
		$base = new Database();

		$query = "SELECT * FROM categorias WHERE id = '".$categoria."' ";

		if( $datos = $base->Select($query) ){
			return $datos[0]['nombre']." -> ";
		}else{
			return "";
		}

	}

	/**
	* COMPONE EL PATH DE UN ARTICULO
	* @param int $categoria -> id de la categoria
	* @param int $norma -> id de la norma
	* @return string $path -> camino compusto
	*/
	private function articuloPath($categoria, $norma ){
		$base = new Database();

		$categoria = mysql_real_escape_string($categoria);
		$norma = mysql_real_escape_string($norma);

		$query = "SELECT * FROM categorias WHERE id = '".$categoria."' ";
		$query2 = "SELECT * FROM normas WHERE id = '".$norma."' ";

		$path = "";
		if( $datos = $base->Select($query) ){
			$path .= $datos[0]['nombre'];
		}

		if( $datos2 = $base->Select($query2) ){
			$path .= " -> ". $datos2[0]['nombre']." -> ";
		}

		return $path;
	}
}

?>