<?php

/**
* CLASE PARA MANEJAR LOS DATOS REGISTRADOS PARA CLIENTES
*/

require_once("classDatabase.php");
require_once("usuarios.php");
require_once("session.php");

/**
* MANEJA LOS REGISTROS DE LAS NORMAS Y CATEGORIAS
*/
class Registros{
	private $registros = array(); //array[][][][];

	public function __construct(){
		//SEGURIDAD LOGUEADO
		$session = new Session();
		$session->Logueado();
	}


	/**
	* OBTIEN LOS REGISTROS DE UN PROYECTO 
	* @param $proyecto -> id del proyecto 
	* @return $datos -> array[][] con los datos
	* @return false -> fallo
	*/
	public function getRegistros($proyecto){

		$base = new Database();

		$query = "SELECT * FROM registros WHERE proyecto = ".$proyecto;
		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos;
		}else{
			return false;
		}
	}

/************** OBSERVACIONES DE UNA CATEGORIA EN UN PROYECTO **************/

	/**
	* OBTIENE LOS DATOS DE LA OBSERVACION
	* @param $proyecto -> id del proyecto
	* @param $categoria -> id de la categoria
	* @return $datos[][] -> datos de la observacion
	* @return false si falla o no tiene datos
	*/
	public function getObservacion($proyecto, $categoria){
		$base = new Database();
		$query = "SELECT * FROM observaciones WHERE proyecto = '".$proyecto."' AND categoria = '".$categoria."'";
		
		$datos = $base->Select($query);
		
		if(!empty($datos)){
			return $datos;
		}else{
			return false;
		}
	}

/************** ARCHIVOS ADJUNTOS DE UNA CATEGORIA **************/

	/**
	* OBTIENE LOS ARCHIVOS ADJUNTO DE UN REGISTRO
	* @param $dato -> dato solicitado
	* @param $id -> id categoria
	* @return $datos -> dato consultado
	* @return false si falla
	*/
	public function getArchivoDato($dato, $id){
		$base = new Database();
		$query = "SELECT * FROM archivos WHERE id = ".$id;

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos[0][$dato];
		}else{
			return false;
		}
	}

	/**
	* OBTIENE LOS ARCHIVOS DE UN ARTICULO
	* @param $articulo -> id del articulo
	* @return $datos -> array[][]
	*/
	public function getArchivosArticulo($articulo){
		$base = new Database();
		$query = "SELECT * FROM archivos WHERE articulo = '".$articulo."'";

		$datos = $base->Select($query);
		return $datos;
	}

/************** CATEGORIAS **************/
	
	/**
	* OBTIENE LOS DATOS DE UNA CATEGORIA
	* @param $categoria -> id de la categoria
	* @return $datos[][] -> datos de las normas
	*/
	public function getCategoriaDatos($categoria){
		$base = new Database();
		$query = "SELECT * FROM categorias WHERE id = '".$categoria."'";

		$datos = $base->Select($query);
		
		if(!empty($datos)){
			return $datos;
		}else{
			return false;
		}
	}

	/**
	* OBTIENE LOS DATOS DE LA NORMAS INCLUIDAS DE UNA CATEGORIA
	* @param $proyecto -> id del proyecto
	* @param $categoria -> id de la categoria
	* @return $normas -> array[] con los ids de las normas incluidas
	* @return false si falla
	*/
	public function getValidNormas($proyecto, $categoria){
		$base = new Database();

		$proyecto = mysql_real_escape_string($proyecto);
		$categoria = mysql_real_escape_string($categoria);

		$query = "SELECT * FROM registros_normas WHERE proyecto = '".$proyecto."' AND categoria = '".$categoria."'";

		$datosIncluidas = $base->Select($query);
		$normas = unserialize( $datosIncluidas[0]['registro'] );

		if( !is_array($normas)){
			
			//USA TODAS LAS NORMAS DE LA CATEGORIA
			$normas = array();
			
			$query = "SELECT * FROM categorias WHERE id = '".$categoria."'";
			$datos = $base->Select($query);

			if( !empty($datos)){
				$normas = unserialize( $datos[0]['normas'] );
			}

		}
		return $normas;

	}

	/**
	* OBTIENE DATOS DE UN HIJO
	* @param $padre -> id del padre
	* @return $hijos[][]
	*/
	public function getHijos($padre){
		$base = new Database();
		$query = "SELECT * FROM categorias WHERE padre = ".$padre;

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos;
		}else{
			return false;
		}
	}

	/**
	* OBTIENE LOS ID DE TODOS LOS HIJOS DE UN PADRE
	* @param $padre -> id del padre
	* @return $hijos[]
	*/
	public function getTodosHijos($padre){
		$hijos = array();
		$base = new Database();
		$query = "SELECT * FROM categorias WHERE padre = ".$padre;

		$datos = $base->Select($query);

		if(!empty($datos)){
			foreach ($datos as $fila => $c) {
				$hijos[] = $datos[$fila]['id'];
			}
			return $hijos;
		}else{
			return false;
		}
	}

	/**
	* OBTIENE LOS ID DE TODOS LOS HIJOS DE UN PADRE
	* @param $padre -> id del padre
	* @return $hijos[]
	*/
	public function getTodosHermanos($hijo){
		$resultado = array();
		$base = new Database();

		//el padre del hijo
		$query = "SELECT DISTINCT padre, id FROM categorias WHERE id = ".$hijo;

		$datos = $base->Select($query);

		if(!empty($datos)){
			foreach ($datos as $fila => $c) {

				$query = "SELECT DISTINCT id FROM categorias WHERE padre = ".$datos[$fila]['padre'];
				$hermanos = $base->Select($query);

				if(!empty($hermanos)){
					foreach ($hermanos as $fi => $va) {
						$resultado[] = $hermanos[$fi]['id'];
					}
				}
			}
			return $resultado;
		}else{
			return false;
		}
	}

	/**
	* OBTIENE EL ID DEL PADRE DE UN HIJO
	* @param $hijo -> el hijo para buscar el padre
	*/
	public function getPadre($hijo){
		$base = new Database();
		$query = "SELECT * FROM categorias WHERE id = ".$hijo;

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos[0]['padre'];
		}else{
			return false;
		}
	}

	/**
	* OBTIENE TODOS LOS DATOS DE UNA CATEGORIA
	* @param $categoria -> id de la categoria
	*/
	public function getCategoria($categoria){
		$base = new Database();
		$query = "SELECT * FROM categorias WHERE id = ".$categoria;

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos;
		}else{	
			return false;
		}
	}

	/**
	* OBTIENE UN DATO DE UNA CATEGORIA
	* @param $dato -> el dato solicitado
	* @param $categoria -> id de la categoria
	*/
	public function getCategoriaDato($dato, $categoria){
		$base = new Database();
		$query = "SELECT * FROM categorias WHERE id = ".$categoria;

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos[0][$dato];
		}else{	
			return false;
		}
	}

	/**
	* DETERMINA SI UNA CATEGORIA ES UNA HOJA
	* @param id -> id de la categoria
	* @return true si es una hoja
	* @return false si no es hoja o falla
	*/
	public function EsHoja($id){
		$base = new Database();
		$query = "SELECT * FROM categorias WHERE id = ".$id;

		$datos = $base->Select($query);

		if(!empty($datos)){
			if( $datos[0]['hoja'] == 1 && $datos[0]['padre'] != 0 ){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	* DETERMINA SI UNA CATEGORIA ES ROOT 
	* @param id -> id de la categori
	* @return true si es root
	* @return false si no es root
	*/
	public function EsRoot($id){
		$base = new Database();
		$query = "SELECT * FROM categorias WHERE id = ".$id;

		$datos = $base->Select($query);

		if(!empty($datos)){
			if($datos[0]['padre'] == 0){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

/************************** NORMAS *********************/
	
	/**
	* OBTIENE TODAS LAS NORMAS
	*/
	public function getNormas(){
		$base = new Database();
		$query = "SELECT * FROM normas";

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos;
		}else{
			return false;
		}
	}

	/**
	* OBTIENE TODAS LAS NORMAS HABILITADAS
	*/
	public function getNormasHabilitadas(){
		$base = new Database();
		$query = "SELECT * FROM normas WHERE status = 1 ORDER by nombre";

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos;
		}else{
			return false;
		}
	}

	/**
	* OBTIENE LAS NORMAS SELECCIONADAS    
	*/
	public function getSelectedNormas($categoria){
		$base = new Database();
		$query = "SELECT * FROM categorias WHERE id = ".$categoria;

		$datos = $base->Select($query);
		
		if(!empty($datos)){
			return unserialize($datos[0]['normas']);
		}else{

		}
	}

	/**
	* OBTIENE LOS DATOS DE UNA NORMA
	* @param $norma -> id de la norma
	* @return $datos -> array[][]
	*/
	function getDatosNorma($norma){
		$base = new Database();
		$query = "SELECT * FROM normas WHERE id = ".$norma;

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos;
		}else{
			return false;
		}
	}

	/**
	* OBTIENE UN DATO DE UNA NORMA
	* @param $dato -> dato solicitado
	* @param $id -> id de la norma
	* @return $dato -> dato solicitado
	*/
	public function getDatoNorma($dato, $id){
		$base = new Database();
		$query = "SELECT * FROM normas WHERE id = ".$id;

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos[0][$dato];
		}else{
			return false;
		}
	}

	/**
	* OBTIENE LOS TIPOS DE LA NORMA
	* @param $norma -> id de la norma
	*/
	public function getTipoNorma($norma){
		$base = new Database();
		$query = "SELECT tipo FROM normas WHERE id =".$norma;

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos[0]['tipo'];
		}else{
			return 0;
		}
	}

/************************** ARTICULOS *********************/
	
	/**
	* OBTIENE TODOS LOS ARTICULOS
	* @param $norma -> id de la norma
	* @return $datos -> array[][]
	*/
	public function getArticulos($norma){
		$base = new Database();
		$query = "SELECT * FROM articulos WHERE norma = ".$norma." AND borrado = 0";

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos;
		}else{
			return false;
		}
	}

	/**
	* OBTIENE LOS ARTICULOS INCLUIDOS DE UN PROYECTO
	* @param $proyecto -> id del proyecto
	* @param $norma -> id de la norma
	* @return $articulos -> array[][] con los datos
	* @return false -> si falla 
	*/
	public function getValidArticulos($proyecto, $norma){
		$base = new Database();

		$proyecto = mysql_real_escape_string($proyecto);
		$norma = mysql_real_escape_string($norma);

		$query = "SELECT * FROM registros_articulos WHERE proyecto = ".$proyecto." AND norma = '".$norma."'";

		$datosRegistrados = $base->Select($query);
		$registrados = unserialize($datosRegistrados[0]['registro']);

		if( !is_array($registrados)){
			$registrados = array();
		}

		$articulos = array();

		//selecciona todos
		$query = "SELECT * FROM articulos WHERE norma = '".$norma."' AND borrado = 0";

		$datos = $base->Select($query);

		if(!empty($datos)){
			foreach ($datos as $fila => $articulo) {
				if( in_array($articulo['id'], $registrados)){
					$articulos[] = $datos[$fila];
				}
			}
			return $articulos; //articulos incluidos solamente
		}else{
			return false;
		}
	}

	/**
	* OBTIENE TODA LA INFO DE UN ARTICULO
	* @param $articulo -> id del articulo
	* @return $datos -> array[][] con los datos
	* @return false si falla
	*/
	function getArticulo($articulo){
		$base = new Database();
		$query = "SELECT * FROM articulos WHERE id = ".$articulo." AND borrado = 0";

		$datos = $base->Select($query);

		if( !empty($datos) ){
			return $datos;
		}else{
			return false;
		}
	}

	/**
	* OBTIENE DATO DE UN ARTICULO
	* @param $dato -> dato solicitado
	* @param $id -> id del articulo
	* @return $dato -> valor del dato solicitado
	*/
	public function getDatoArticulo($dato, $id){
		$base = new Database();
		$query = "SELECT * FROM articulos WHERE id = ".$id;

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos[0][$datos];
		}else{
			return false;
		}
	}

/*********************************** TIPOS NORMAS ************************/
	
	/**
	* OBTIENE LOS TIPOS DISPONIBLES
	*/
	public function getTipos(){
		$base = new Database();
		$query = "SELECT * FROM tipos";

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos;
		}else{
			return false;
		}
	}

	/**
	* OBTIENE TODOS LOS DATOS DE UN TIPO
	* @param id -> id del tipo
	*/
	public function getTipo($id){
		$base = new Database();
		$query = "SELECT * FROM tipos WHERE id = ".$id;

		$datos = $base->Select($query);

		return $datos;
	}


/*********************************** ENTIDADES NORMAS ************************/

	/**
	* OBTIENE TODAS LAS ENTIDADES
	* @return $datos -> array[][]
	*/
	public function getEntidades(){
		$base = new Database();
		$query = "SELECT * FROM entidades"; //OBTIENE LAS PADRES

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos;
		}else{	
			return false;
		}
	}

	/**
	* OBTIEN LAS ENTIDADES CON GRUPOS
	*/
	public function getPadresEntidades(){
		$padres = array();
		$base = new Database();
		$query = "SELECT * FROM entidades WHERE grupo = 1 OR padre = 0";

		$entidades = $base->Select($query);

		if(!empty($entidades)){
			return $entidades;
		}else{
			return false;
		}
	}

	/**
	* OBTIENE LOS DATOS DE UNA ENTIDAD
	* @param $id -> id de la entidad
	* @return $datos -> array[][]
	* @return false si falla
	*/
	public function getEntidadDatos($id){
		$base = new Database();
		$query = "SELECT * FROM entidades WHERE id = ".$id;

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos;
		}else{
			return false;
		}
	}

	/**
	* OBTIENE UN DATO DE UNA ENTIDAD
	* @param $id -> id de la entidad
	* @return $datos -> array[][]
	* @return false si falla
	*/
	public function getEntidadDato($dato, $id){
		$base = new Database();
		$query = "SELECT * FROM entidades WHERE id = ".$id;

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos[0][$dato];
		}else{
			return false;
		}
	}

	/**
	* OBTIENE LAS ENTIDADES HIJAS PARA UN GRUPO
	* @param $padre -> id del padre
	* @retun $entidades -> array[][]
	* @return false si falla o no tiene entidades hijas
	*/
	function getEntidadesHijas($padre){
		$base = new Database();
		$query = "SELECT * FROM entidades WHERE padre = ".$padre;

		$entidades = $base->Select($query);

		if(!empty($entidades)){
			return $entidades;
		}else{
			return false;
		}
	}

/*********************** HELPERS ************/
	/**
	 * SUBE UNA IMAGEN
	 * @param $imagen -> imagen a subir
	 * @param $destino -> directorio de destino
	 * @return $link -> link de la nueva imagen
	 * @return false -> si falla
	 */
	public function UploadImage($imagen, $destino){
		//SUBE LA IMAGEN
		if($imagen['tmp_name'] != null && $imagen['tmp_name'] != ""){
			$upload = new Upload();
        
			$upload->SetFileName($imagen['name']);
			$upload->SetTempName($imagen['tmp_name']);

			$upload->SetValidExtensions(array('gif', 'jpg', 'jpeg', 'png')); 
			
			$destino = "../".$destino;
			$upload->SetUploadDirectory($destino); //DESTINO

			$upload->SetMaximumFileSize(90000000); //TAMANO MAXIMO PERMITIDO
				        
			if($upload->UploadFile()){
				//SE OPTIENE EL LINK DE LA IMAGEN SUBIDA Y SE FORMATEA
				$link = str_replace("../", "", $upload->GetUploadDirectory().$upload->GetFileName() );

				return $link;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	 * OBTIENE LOS DATOS DEL ARTICULO VALIDADOS POR EL PROYECTO
	 * @param $proyecto -> id del proyecto
	 * @param $articulo -> id del articulo
	 */
	function getValidArticuloDatos($proyecto, $articulo){
		$base = new Database();

		$proyecto = mysql_real_escape_string($proyecto);
		$query = "SELECT * FROM proyectos WHERE id = '".$proyecto."'";

		$status = $base->Select($query);

		if( $status[0]['status'] == 0){ //desactivo
			$query = "SELECT * FROM snapshots_articulos WHERE id = '".$articulo."' ";
			$query .= " AND fecha_snapshot <= '".$status[0]['fecha_desactivacion']."' ORDER BY fecha_snapshot DESC  LIMIT 0,1";
		}else{
			$query = "SELECT * FROM articulos WHERE id = '".$articulo."' AND borrado = 0";
		}

		$datos = $base->Select($query);

		if( !empty($datos) ){
			return $datos;
		}else{
			return false;
		}
	}
}

?>