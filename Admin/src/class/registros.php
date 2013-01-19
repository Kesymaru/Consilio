<?php

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

	/**
	 * CREA NUEVO REGISTRO
	 * @param $proyecto -> id del proyecto
	 * @param $registro -> array[] con los ids de los registros sin serializar
	 * @param $fecha -> fecha de creacion del proyecto
	 */
	public function NewRegistro($proyecto, $registro, $fecha){
		$base = new Database();

		$registro = serialize($registro);

		$query = "INSERT INTO registros (proyecto, registro, fecha_creacion) VALUES";
		$query .= " ('".$proyecto."', '".$registro."', '".$fecha."' )";

		if($base->Insert($query)){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * ACTUALIZA UN REGISTRO
	 * @param $id _> id del registro
	 * @param $registro -> array[] con los registros sin serializar
	 */
	function UpdateRegistro($id, $registro){
		$base = new Database();

		$registro = serialize($registro);

		$query = "UPDATE registros SET registro = '".$registro."', fecha_actualizacion = NOW() WHERE id = '".$id."'";

		if($base->Update($query)){
			return true;
		}else{
			return false;
		}
	}

	/**
	* DUPLICA UN REGISTRO
	* @param $id -> id del registro a duplicar
	* @param $nuevo -> id del nuevo registro
	*/
	function DuplicarRegistros($id, $nuevo){
		$base = new Database();
		$query = "SELECT * FROM registros WHERE id = ".$id;

		$datos = $base->Select($query);
		if(!empty($datos)){
			if($this->NewRegistro($nuevo, $datos[0]['registro'], $datos[0]['fecha_creacion'])){
				return true;
			}else{
				return false;
			}
		}else{
			$query = "SELECT proyectos WHERE id = ".$nuevo;
			$datos = $base->Select($query);

			$registro = array();

			//crea registro vacio
			if($this->NewRegistro($nuevo, $registro, $datos[0]['fecha_creacion'])){
				return true;
			}else{
				return false;
			}
		}
	}

	/**
	* ELIMINA LOS REGISTROS DE UN PROYECTO
	* @param $proyecto -> id del proyecto
	*/
	public function DeleteRegistros($proyecto){
		$base = new Database();
		$query = "DELETE FROM registros WHERE proyecto = ".$proyecto;

		if($base->Delete($query)){
			return true;
		}else{
			return false;
		}
	}

	/**
	* OBTIENE LOS REGISTROS DE LAS NORMAS INCLUIDAS DE UNA CATGORIA
	* @param $categoria -> id de la categoria
	* @param $proyecto -> id del proyecto
	* @return $datos -> array[][] con los datos
	* @return false si  falla
	*/
	public function getRegistrosNorma($proyecto, $categoria){
		$base = new Database();
		$query = "SELECT * FROM registros_normas WHERE proyecto = '".$proyecto."' AND categoria = '".$categoria."'";

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos;
		}else{
			//no hay datos
			return false;
		}
	}

	/**
	* REGISTRA SINO EXISTE O ACTUALIZA SI EXISTE NORMAS SELECCIONADAS
	* @param $proyecto -> id del proyecto
	* @param $categoria -> id de la categoria
	* @param $registro -> array sin serializar
	* @return true si se registra o actualiza
	* @return false si fala
	*/
	public function RegistrarRegirstroNorma($proyecto, $categoria, $registro){
		$base = new Database();
		
		$registro = serialize($registro);

		$query = "SELECT * FROM registros_normas WHERE proyecto = '".$proyecto."' AND categoria = '".$categoria."'";

		if($base->Existe($query)){
			$query = "UPDATE registros_normas SET registro = '".$registro."' WHERE proyecto = '".$proyecto."' AND categoria = '".$categoria."'";

			if($base->Update($query)){
				return true;
			}else{
				return false;
			}
		}else{
			$query = "INSERT INTO registros_normas ( proyecto, categoria, registro ) VALUES ";
			$query .= "( '".$proyecto."', '".$categoria."', '".$registro."' )";

			if($base->Insert($query)){
				return true;
			}else{
				return false;
			}
		}

	}

	/**
	* OBTIENE LOS REGISTROS DE LOS ARTICULOS INCLUIDOS DE UNA NORMA
	* @param $proyecto -> id del proyecto
	* @param $norma -> id de la norma
	* @return $datos -> array[][] con los datos
	* @return false si  falla
	*/
	public function getRegistrosArticulos($proyecto, $norma){
		$base = new Database();
		$query = "SELECT * FROM registros_articulos WHERE proyecto = '".$proyecto."' AND norma = '".$norma."'";

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos;
		}else{
			//no hay datos
			return false;
		}
	}

	/**
	* REGISTRA SINO EXISTE O ACTUALIZA SI EXISTE
	* @param $proyecto -> id del proyecto
	* @param $norma -> id de la norma
	* @param $registro -> array sin serializar
	* @return true si se registra o actualiza
	* @return false si fala
	*/
	public function RegistrarRegirstroArticulo($proyecto, $norma, $registro){
		$base = new Database();
		
		$registro = serialize($registro);

		$query = "SELECT * FROM registros_articulos WHERE proyecto = '".$proyecto."' AND norma = '".$norma."'";

		if($base->Existe($query)){
			$query = "UPDATE registros_articulos SET registro = '".$registro."' WHERE proyecto = '".$proyecto."' AND norma = '".$norma."'";

			if($base->Update($query)){
				return true;
			}else{
				return false;
			}
		}else{
			$query = "INSERT INTO registros_articulos ( proyecto, norma, registro ) VALUES ";
			$query .= "( '".$proyecto."', '".$norma."', '".$registro."' )";

			if($base->Insert($query)){
				return true;
			}else{
				return false;
			}
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

	/**
	* REGISTRA O ACTUALIZA UNA OBSERVACION SI EXISTE O NO
	* @param $proyecto -> id proyecto
	* @param $categoria -> categoria
	* @param $observacin -> texto de la observacion sin base 64
	* @return true si se actualiza o registra
	* @return false si falla
	*/
	public function RegistrarObservacion($proyecto, $categoria, $observacion){
		$base = new Database();
		$proyecto = mysql_real_escape_string($proyecto);
		$categoria = mysql_real_escape_string($categoria);
		$observacion =base64_encode($observacion);

		$query = "SELECT * FROM observaciones WHERE proyecto = '".$proyecto."' AND categoria = '".$categoria."'";

		if($base->Existe($query)){
			$query = "UPDATE observaciones SET observacion = '".$observacion."', fecha_actualizacion = NOW() WHERE proyecto = '".$proyecto."' AND categoria = '".$categoria."'";
			
			if($base->Update($query)){
				return true;
			}else{
				return false;
			}
		}else{
			$query = "INSERT INTO observaciones ( observacion, categoria, proyecto, fecha_creacion ) VALUES ('".$observacion."', '".$categoria."', '".$proyecto."', NOW() )";
			
			if($base->Insert($query)){
				return true;
			}else{
				return false;
			}
		}
	}

	/**
	* OBTIEN UN DATO DE UNA OBSERVACION
	* @param $dato -> dato solicitado
	* @param $proyecto -> id del prooyecto
	* @param $categoria -> id de la categoria
	* @return $dato 
	*/
	public function getObservacionDato($dato, $proyecto, $categoria){
		$base = new Database();
		$query = "SELECT * FROM observaciones WHERE proyecto = '".$proyecto."' AND categoria = '".$categoria."'";

		$datos = $base->Select($query);
		
		if(!empty($datos)){
			return $datos[0][$dato];
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
	* SUBE UN ARCHIVO NUEVO
	* @param $tipo -> tipo de archivo, norma o articulo
	* @param $archivo -> file ha subir
	* @param $nombre -> nombre del archivo *opcional
	* @param $pertenece -> id de la norma/articulo al que pertenece
	* @return true -> si la operacion se realizo exitosamente
	* @return false -> si ocurrio un error o fallo
	*/
	public function NuevoArchivo($tipo, $archivo, $nombre, $pertenece){
        $upload = new Upload();
        
        $upload->SetFileName($archivo['name']);
        $upload->SetTempName($archivo['tmp_name']);
        
        //FORMATOS DE ARCHIVOS PERMITIDOS
        $upload->SetValidExtensions(array('gif', 'jpg', 'jpeg', 'png', 'zip', 'rar', 'pdf', 'txt', 'xls', 'xlsx', 'ods', 'docx', '.odt', 'rtf', 'pptx', 'ppt', 'pptm')); 
        
        //DIRECTORIO PARA ARCHIVOS
        $upload->SetUploadDirectory("../archivos/"); 

        $upload->SetMaximumFileSize(90000000); //TAMANO MAXIMO PERMITIDO
        
        //SUBE EL ARCHIVO
        if($upload->UploadFile()){
        	//LINK DONDE SE SUBIO EL ARCHIVO
            $link = $upload->GetUploadDirectory().$upload->GetFileName();
            
            $link = str_replace("../", "", $link);

            //GUARDA EL LINK Y LOS DATOS EN LA BASE DE DATOS
            if($this->setArchivo($tipo, $nombre, $link, $pertenece)){
            	return true;
            }else{
            	//NO SE GUARDO EN DB PERO SE SUBIO, SE ELIMINA EL ARCHIVO SUBIDO
				$link = '../'.$link;
				$base->DeleteImagen($link);
            	return false;
            }
        }else{
        	return false;
        }
	}

	/**
	* GUARDA LOS DATO DE UN NUEVO ARCHIVO PARA ARTICULO
	* @param $tipo -> tipo archivo, norma o archivo
	* @param $nombre -> nombre archivo
	* @param $link -> link archivo subido
	* @param $pertenece -> id del articulo/norma a la que pertenece
	* @return true si se guarda correctamente
	* @return false si falla
	*/
	private function setArchivo($tipo, $nombre, $link, $pertenece){
		$base = new Database();

		if($tipo == 'norma'){
			$query = "INSERT INTO archivos (nombre, link, norma, fecha_creacion ) VALUES ('".$nombre."', '".$link."', '".$pertenece."', NOW() )";
		}else if($tipo == 'articulo'){
			$query = "INSERT INTO archivos (nombre, link, articulo, fecha_creacion ) VALUES ('".$nombre."', '".$link."', '".$pertenece."', NOW() )";
		}

		if($base->Insert($query)){
			return true; //SE GUARDO
		}else{
			return false;
		}
	}

	/**
	* ELIMINA UN ARCHIVO CON SU ID
	* @param $id -> id del archivo a eliminar
	* @return true si se elimina correctamente
	* @return false si falla
	*/
	public function DeleteArchivo($id){
		$archivo = "";
		$base = new Database();
		$query = "DELETE FROM archivos WHERE id = ".$id;
		
		$archivos = $base->Select("SELECT * FROM archivos WHERE id = ".$id);

		//LINK DEL ARCHIVO
		$link = $archivos[0]['link'];
		$link = '../'.$link;

		//BORRA EL ARCHIVO
		if( $base->DeleteImagen($link) ){
			
			if( $base->Delete($query) ){
				return true;
			}else{
				return false;				
			}
		}else{
			return false;
		}
	}

	/**
	* CREA SNAPSHOT DE UN ARCHIVO ELIMINADO, MUEVE EL ARCHIVO A LA CARPETA DE ARCHIVADOS Y CREA EL SNAPSHOT EN LA BASE DE DATOS
	* @param $tipo -> tipo de archivo, articulo o norma
	* @param $link -> link del archivo a archivar
	* @param $pertence -> id de la norma o articulo
	* @return true si crea el snapshot del archivo
	* @return false si falla
	*/
	private function SnapshotArchivo($tipo, $link, $pertence){
		$base = new Database();
		if($tipo == 'norma'){
			$query = "SELECT * FROM normas WHERE id = ".$pertence;
		}else if($tipo == 'articulo'){
			$query = "SELECT * FROM articulos WHERE id = ".$pertence;
		}else{
			echo "Error: ".$tipo." no valido, registros.php 249";
			return false;
		}

		$datos = $base->Select($query);

		//mueve el archivo viejo/archivado a la carpeta de archivar
		if( $nuevoLink = $base->Archivar($link)){
			
			//crea snapshot del archivo archivado
			if(!empty($datos)){

				//crea el snapshot del archivo con los datos
				$query = "INSERT INTO snapshots_archivos (nombre, link, norma, articulo, id, fecha_creacion, fecha_snapshot ) VALUES ";

				if($tipo == 'norma'){
					$query .= "( '".$datos[0]['nombre']."', '".$datos[0]['link']."', '".$datos[0]['norma']."', 0, '".$nuevoLink."', '".$datos[0]['fecha_creacion']."', NOW() )";
				}else{
					$query .= "( '".$datos[0]['nombre']."', '".$datos[0]['link']."', 0, '".$datos[0]['articulo']."', '".$nuevoLink."', '".$datos[0]['fecha_creacion']."', NOW() )";
				}
					
				if($base->Insert($query)){
					//snapshot creado exitosamente
					return true; 
				}else{
					return false;
				}

			}
		}else{
			echo "Error: al archivar un archivo.";
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
		$query = "SELECT * FROM archivos WHERE articulo = ".$articulo;

		$datos = $base->Select($query);
		return $datos;
	}

/************** CATEGORIAS **************/
	
	/**
	* OBTIENE LOS DATOS DE UNA CATEGORIA
	* @param $categoria -> id de la categoria
	* @return $datos[][] -> datos de las normas
	*/
	public function getDatos($categoria){
		$base = new Database();
		$query = "SELECT * FROM datos WHERE categoria = ".$categoria;

		$datos = $base->Select($query);
		
		if(!empty($datos)){
			return $datos;
		}else{
			return false;
		}
	}

	/**
	* ACTUALIZA UNA CATEGORIA
	* @param $nombre -> nuevo nombre
	* @param $normas -> array[] con las normas seleccionadas sin serializar
	* @param $id -> id de la categoria
	*/
	public function UpdateCategoria($nombre, $normas, $id){
		$base = new Database();

		$nombre = mysql_real_escape_string($nombre);
		$normas = serialize($normas);

		$query = "UPDATE categorias SET nombre = '".$nombre."', normas = '".$normas."' WHERE id = '".$id."'";

		if( $base->Existe("SELECT * FROM categorias WHERE id = ".$id )){
			if($base->Update($query)){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	* ACTUALIZA DATOS DE UNA CATEGORIA SIN MODIFICAR LAS NORMAS 
	* @param $nombre
	* @param $imagen -> link imagen subida
	* @param $id
	* @return true si actualiza
	*/
	public function UpdateDatosCategoria($nombre, $imagen, $id){
		$base = new Database();

		$nombre = mysql_real_escape_string($nombre);

		$query = "UPDATE categorias SET nombre = '".$nombre."', imagen = '".$imagen."' WHERE id = '".$id."'";
		
		if($imagen != ""){
			$datos = $base->Select("SELECT * FROM categorias WHERE id = ".$id);

			//borra imagen anterior
			$imagenOld = "../".$datos[0]['imagen'];

			if(!$base->DeleteImagen($imagenOld)){
				echo "<br/>Error: class Registros UpdateDatosCategoria() imagen no se pudo borrar.<br/>imagen: ".$imagenOld;
			}
		}
		
		if($base->Update($query)){
			return true;
		}else{
			return false;
		}
	}

	/**
	* ELIMINA TODOS LOS DATOS DE UNA CATEGORIA, ARCHIVOS Y DATOS ASOCIADOS DE LA CATEGORIA
	* @param $id -> id de la categoria ha eliminar
	*/
	public function DeleteCategoria($id){
		$base = new Database();
		$query = "DELETE FROM categorias WHERE id = ".$id;

		if( $base->Existe("SELECT * FROM categorias where id = ".$id) ){
			if($base->Delete($query)){
				return true;
			}else{
				//ERROR AL BORRAR CATEGORIA
				return false;
			}
		}else{
			//ERRRO CATEGORIA NO EXISTE
			return false;
		}
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

/************** DATOS DE CATEGORIAS **************/

	/**
	* REGISTRA UN DATO, SE ASEGURA DE REGISTRARLO SI ES NUEVO O ACTUALIZARLO SI EXISTE
	* @param $nuevo -> dato a registrar
	* @param $campo -> id del campo ha registrar
	* @param $categoria -> id de la categoria
	* @return true si se actualizo o gurado
	* @return false si falla
	*/
	public function setDato($nuevo, $categoria){
		$base = new Database();
		$query = "SELECT * FROM datos WHERE categoria = '".$categoria."'";

		$nuevo = base64_encode($nuevo);

		if( $base->Existe($query) ){ //EXISTE SE ACTUALIZA
		    
			$query = "UPDATE datos SET contenido = '".$nuevo."' WHERE categoria ='".$categoria."'";
			if($base->Update($query)){
				return true;
			}else{
				return false;
			}
		}else{ //NO EXISTE SE INGRESA
			
			$query = "INSERT INTO datos (contenido, categoria ) VALUES ('".$nuevo."', '".$categoria."' ) ";
			if($base->Insert($query)){
				return true;
			}else{
				return false;
			}
		}
	}

	/**
	* ELIMINA UN DATO
	* @param $id -> id del dato
	*/
	public function DeleteDato($id){
		$base =  new Database();
		$query = "DELETE FROM datos WHERE id = ".$id;

		if($base->Delete($query)){
			return true;
		}else{
			return false;
		}
	}

	/**
	* CREA UNA NUEVA SUBCATEGORIA
	* @param $nombre -> nombre de la categoria
	* @param $imagen -> link de la imagen subida *no requerida
	* @param $padre -> id del padre
	*/
	public function newCategoria($nombre, $imagen, $padre){
		$base = new Database();

		$nombre = mysql_real_escape_string($nombre);

		$query = "INSERT INTO categorias (nombre, imagen, padre) VALUES ( '".$nombre."', '".$imagen."', '".$padre."')";
				
		if( $base->Insert($query)){
			return true;
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

	/**
	* ACTUALIZA UNA NORMA
	* @param $norma -> id de la norma
	* @param $nombre -> nombre nuevo
	* @param $numero -> numero de la norma
	* @param $tipo -> id del tipo de norma seleccionado
	* @return true si se actualiza correctamente
	* @return false si falla en algo
	*/
	public function UpdateNorma($norma, $nombre, $numero, $tipo, $status){
		$base = new Database();

		$nombre = mysql_real_escape_string($nombre);
		$numero = mysql_real_escape_string($numero);
		
		$query = "UPDATE normas SET nombre = '".$nombre."', numero = '".$numero."', tipo = '".$tipo."', status = '".$status."' WHERE id = ".$norma;

		if( $base->Update($query)){
			return true;
		}else{
			return false;
		}

	}

	/**
	* DESHABILITA UNA NORMA
	* @param $norma -> id de la norma
	*/
	public function DeshabilitarNorma($norma){
		$base = new Database();
		$query = "UPDATE normas SET status = 0 WHERE id = ".$norma;

		if($base->Update($query)){
			return true;
		}else{
			return false;
		}
	}

	/**
	* HABILITA UNA NORMA
	* @param $norma -> id de la norma
	*/
	public function HabilitarNorma($norma){
		$base = new Database();
		$query = "UPDATE normas SET status = 1 WHERE id = '".$norma."'";

		if($base->Update($query)){
			return true;
		}else{
			return false;
		}
	}

	/**
	* REGISTRA UNA NORMA NUEVA
	* @param $nombre
	* @param $numero
	* @param $tipo
	* @param $estado
	* @return true si se registra
	*/
	public function RegistrarNorma($nombre, $numero, $tipo, $estado){
		$base = new Database();
		$nombre = mysql_real_escape_string($nombre);
		$numero = mysql_real_escape_string($numero);

		$query = "INSERT INTO normas (nombre, numero, tipo, status) VALUES ('".$nombre."', '".$numero."', '".$tipo."', '".$estado."')";

		if($base->Insert($query)){
			return true;
		}else{
			return false;
		}
	}

/************************** ARTICULOS *********************/
	
	/**
	* OBTIENE TODOS LOS ARTICULOS DE UNA NORMA
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

	/**
	* REGISTRA UN NUEVO ARTICULO
	* @param $norma -> id de la norma
	* @param $nombre -> nombre del nuevo articulo
	* @param $entidades -> array[] id de las identidades seleccionadas
	* @param $permisos -> texto html de permisos
	* @param $sanciones -> texto html de sanciones
	* @param $articulos -> texto html del articulo
	* @return true si se registra correctamente
	* @return false si falla
	*/
	public function RegistrarArticulo($norma, $nombre, $entidades, $resumen, $permisos, $sanciones, $articulo ){
		$base = new Database();
		$nombre = mysql_real_escape_string($nombre);

		//CODIFICA EL HTML DEL TEXTO EN BASE 64 PARA SEGURIDAD AL GUARDARLO
		$resumen = base64_encode($resumen);
		$permisos = base64_encode($permisos);
		$sanciones = base64_encode($sanciones);
		$articulo = base64_encode($articulo);

		$entidades = serialize($entidades); //serializa el array para guardarlo en la base de datos

		$query = "INSERT INTO articulos (norma, nombre, entidad, resumen, permisos, sanciones, articulo) ";
		$query .= "VALUES ( '".$norma."', '".$nombre."', '".$entidades."', '".$resumen."', '".$permisos."', '".$sanciones."', '".$articulo."' )";
		
		if($base->Insert($query)){
			return $base->getUltimoId();
		}else{
			return false;
		}
	}

	/**
	* CREA SNAPSHOT DE UN ARTICULO AL SER ACTUALIZADO
	* @param $norma -> id de la norma
	* @param $id -> id del articulo
	* @param $nombre -> nombre del nuevo articulo
	* @param $entidades -> array[] id de las identidades seleccionadas
	* @param $permisos -> texto html de permisos
	* @param $sanciones -> texto html de sanciones
	* @param $articulos -> texto html del articulo
	* @return true si se registra correctamente
	* @return false si falla
	*/
	public function UpdateArticulo($norma, $id, $nombre, $entidades, $resumen, $permisos, $sanciones, $articulo ){

		//crea snapshot con las datos viejos
		if( $this->SnapshotArticulo($id) ){

			$base = new Database();
			$nombre = mysql_real_escape_string($nombre);

			//CODIFICA EL HTML DEL TEXTO EN BASE 64 PARA SEGURIDAD AL GUARDARLO
			$resumen = base64_encode($resumen);
			$permisos = base64_encode($permisos);
			$sanciones = base64_encode($sanciones);
			$articulo = base64_encode($articulo);

			$entidades = serialize($entidades); //serializa el array para guardarlo en la base de datos

			$query = "UPDATE articulos set norma = '".$norma."', nombre = '".$nombre."', entidad = '".$entidades."', resumen = '".$resumen."', permisos = '".$permisos."', sanciones = '".$sanciones."', articulo = '".$articulo."' WHERE id = '".$id."'";
			
			//GUARDA NUEVOS DATOS
			if($base->Update($query)){
				return true;
			}else{
				return 'error al actualizar articulo';
			}
		}else{
			//ERROR NO SE PUDO CREAR EL SNAPSHOT
			return false;
		}
	}

	/**
	* CAMBIA EL ESTADO DEL ARTICULO A ELIMINADO, CREA SNAPSHOT DEL ARTICULO Y CREA SNAPHOT DE LOS ARCHIVOS DEL ARTICULO
	* @param $articulo -> id del articulo
	* @return true si se elimina el articulo
	* @return false si falla
	*/
	public function DeleteArticulo($articulo){

		$base = new Database();
		$query = "SELECT * FROM archivos WHERE articulo = ".$articulo;

		$archivos = $base->Select($query);

		//crea snapshot de los archivos adjuntos del articulo, si tiene
		if(!empty($archivos)){
			foreach ($archivos as $fila => $archivo) {
				$this->SnapshotArchivo('articulo', $archivo['link'], $archivo['id']);
			}
		}

		//crea snapshot del articulo antes de eliminarlo
		if( $this->SnapshotArticulo($articulo) ){
			$base2 = new Database();

			//seudo elimina el articulo, cambia el estado a borrado
			$query = "UPDATE articulos set borrado = 1 WHERE id = ".$articulo;

			if($base2->Update($query)){
				return true;
			}else{
				return false;
			}

		}else{
			return false;
		}
	}

	/**
	* GUARDA UN SNAPSHOT DE UN ARTICULO
	* @param $id -> id del articulo
	* @return true si se crear el snapshot correctamente
	*/
	private function SnapshotArticulo($id){
		$base = new Database();
		$query = "SELECT * FROM articulos WHERE id = ".$id;

		$datos = $base->Select($query); //datos viejos

		if(!empty($datos)){
			//copia datos a la tabla de snapshots de articulos
			$query = "INSERT INTO snapshots_articulos (norma, nombre, entidad, resumen, permisos, sanciones, articulo, id) ";
			$query .= "VALUES ( '".$datos[0]['norma']."', '".$datos[0]['nombre']."', '".$datos[0]['entidad']."', '".$datos[0]['resumen']."', '".$datos[0]['permisos']."', '".$datos[0]['sanciones']."', '".$datos[0]['articulo']."', '".$datos[0]['id']."' )";
			//guarda snapshot
			if( $base->Insert($query) ){
				return true;
			}else{
				return false;
			}
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

	/**
	* OBTIEN UN DATO DE UN TIPO
	* @param $id -> id del tipo
	* @param $dato -> dato consultado
	* @return $dato
	* @return false si falla
	*/
	public function getTipoDato($dato, $id){
		$base = new Database();
		$query = "SELECT * FROM tipos WHERE id = '".$id."'";

		$datos = $base->Select($query);
		if(!empty($datos)){
			return $datos[0][$dato];
		}else{
			return false;
		}
	}

	/**
	* CREA UN NUEVO TIPO
	* @param $nombre -> nombre de l nuevo tipo
	* @return true si se realiza exitosamente
	* @return false si falla
	*/
	function NuevoTipo($nombre){
		$base = new Database();
		$nombre = mysql_real_escape_string($nombre);

		$query = "INSERT INTO tipos (nombre) VALUES ('".$nombre."')";

		if( $base->Insert($query) ){
			return true;
		}else{
			return false;
		}
	}

	/**
	* ELIMINA UN TIPO
	* @param $id -> id del tipo ha borrar
	* @return true si se elimina correctamente
	* @return false si falla
	*/
	public function DeleteTipo($id){
		$base = new Database();
		$query = "DELETE FROM tipos WHERE id = ".$id;

		if($base->Delete($query)){
			return true;
		}else{
			return false;
		}
	}

	/**
	* ACTUALIZA UN TIPO
	* @param $nombre -> nombre nuevo
	* @param $id -> id del tipo ha actualizar
	*/
	public function UpdateTipo($nombre, $id){
		$base = new Database();
		
		$nombre = mysql_real_escape_string($nombre);

		$query = "UPDATE tipos SET nombre = '".$nombre."' WHERE id = ".$id;

		if($base->Update($query)){
			return true;
		}else{
			return false;
		}
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

	/**
	* REGISTRA UNA NUEVA ENTIDAD GRUPO
	* @param $nombre -> nombre de la entidad nueva
	*/
	public function NewEntidadGrupo($nombre){
		$base = new Database();
		$nombre = mysql_real_escape_string($nombre);

		$query = "INSERT INTO entidades (padre, nombre, grupo) VALUES (0, '".$nombre."', 1)";

		if($base->Insert($query)){
			return true;
		}else{
			return false;
		}
	}

	/**
	* REGISTRA UNA NUEVA ENTIDAD
	* @param $padre -> id del padre
	* @param $nombre -> nombre de la entidad nueva
	*/
	public function NewEntidad($padre, $nombre){
		$base = new Database();
		$nombre = mysql_real_escape_string($nombre);

		$query = "INSERT INTO entidades (padre, nombre) VALUES ('".$padre."', '".$nombre."')";

		if($base->Insert($query)){
			return true;
		}else{
			return false;
		}
	}

	/**
	* ELIMINA UNA ENTIDAD
	* @param $id -> id de la entidad ha eliminar
	* @return true si se elimina
	* @return false si falla
	*/
	public function DeleteEntidad($id){
		$base = new Database();
		$query = "SELECT * FROM entidades WHERE id = ".$id;

		$entidad = $base->Select($query);

		$query = "DELETE FROM entidades WHERE id = ".$id;

		if($base->Delete($query)){
			//elimina entidades hijas si es padre
			if($entidad[0]['grupo'] == 1){
				
				$hijas = $this->getEntidadesHijas($id);
				
				if(!empty($hijas)){
					foreach ($hijas as $fila => $hija) {
						$this->DeleteEntidad($hija['id']);
					}
				}
			}
			return true;
		}else{
			return false;
		}
	}

	/**
	* ACTUALIZA UNA ENTIDAD
	* @param $id -> id de la entidad*
	* @param $nombre -> nombre de la entidad
	* @param $padre -> padre o grupo de la entidad
	* @return true si se actualiza correctamente
	* @return false si falla
	*/
	public function UpdateEntidad($id, $nombre, $padre){
		$base = new Database();
		$nombre = mysql_real_escape_string($nombre);

		$query = "UPDATE entidades SET nombre = '".$nombre."', padre = '".$padre."' WHERE id = ".$id;

		if($base->Update($query)){
			return true;
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

}

/*
$registros = new Registros();
$registros->getRegistros(51);
$registros->MostrarArray();
*/

?>