<?php

require_once("classDatabase.php");
require_once("usuarios.php");
require_once("session.php");

/**
* MANEJA LOS REGISTROS DE LAS NORMAS Y CATEGORIAS
*/
class Registros{
	private $registros = array(); //array[][][][];

	/**
	* OBTIENE TODOS LOS REGISTROS DE UN PROYECTO Y LOS COMPONE EN UN SOLO ARRAY
	* COMPONE CATEGORIAS, DATOS, OBSERVACION Y ARCHIVOS ADJUNTOS
	* @param $proyecto -> id del proyecto 	
	*/
	public function getRegistros($proyecto){
		//SEGURIDAD LOGUEADO
		$session = new Session();
		$session->Logueado();

		$base = new Database();
		$consulta = $base->Select("SELECT * FROM registros WHERE proyecto = ".$proyecto);

		if(!empty($consulta)){
			
			//COMPONE TODOS LOS REGISTROS DE UN PROYECTO
			foreach ($consulta as $fila => $valors) {
				$this->registros[$fila]['categoria'] = $this->getNorma($consulta[$fila]['categoria']);
				
				$this->registros[$fila]['datos'] = $this->getDatosNorma($consulta[$fila]['categoria']);

				$this->registros[$fila]['observacion'] = $this->getObservacion($consulta[$fila]['observacion']);
				
				$this->registros[$fila]['archivos'] = $this->getArchivos($proyecto, $consulta[$fila]['categoria']);
			}

			return $this->registros;
		}else{
			//NO HAY DATOS PARA EL PROYECTO
			return false;
		}
	}

	/**
	* HELPER
	*/
	public function MostrarArray(){
		echo '<pre>';
		print_r($this->registros);
		echo '</pre>';
	}

/************** OBSERVACIONES DE UNA CATEGORIA EN UN PROYECTO **************/

	/**
	* OBTIENE LOS DATOS DE LA OBSERVACION
	* @param $id -> id de la observacion
	* @return $datos[][] -> datos de la observacion
	* @return false si falla
	*/
	public function getObservacion($id){
		$base = new Database();
		$query = "SELECT * FROM observaciones WHERE id = ".$id;
		
		$datos = $base->Select($query);
		
		if(!empty($datos)){
			return $datos;
		}else{
			return null;
		}
	}

	/**
	* GUARDA UNA NUEVA OBSERVACION
	* @param $nuevo -> nueva observacion
	* @param $categoria -> id de la categoria de la observacion
	* @param $proyecto -> id del proyectos
	* @return true si se guarda exitosamente
	*/
	public function SetObservacion($nuevo, $categoria, $proyecto){
		$nuevo = mysql_real_escape_string($nuevo);
		$base = new Database();
		$query = "INSERT INTO observaciones ( nombre, categoria, proyecto ) VALUES ( '".$nuevo."', '".$categoria."', '".$proyecto."')";

		if($base->Insert($query)){
			return true;
		}else{
			return false;
		}
	}

	/**
	* ACTUALIZA OBSERVACION
	* @param $nuevo -> nuevo valor para la observacion
	* @param $id -> id de la observacion
	* @return true si se actualiza exitosamente
	*/
	public function UpdateObservacion($nuevo, $id){
		$nuevo = mysql_real_escape_string($nuevo);
		$base = new Database();
		$query = "UPDATE observaciones SET observacion = '".$nuevo."' WHERE id = ".$id;

		if($base->Update($query)){
			return true;
		}else{
			return false;
		}
	}

/************** ARCHIVOS ADJUNTOS DE UNA CATEGORIA **************/

	/**
	* OBTIENE LOS ARCHIVOS ADJUNTO DE UN REGISTRO
	* @param $categoria -> id categoria
	* @return $datos[][] => datos de los archivos
	* @return false si falla
	*/
	public function getArchivos($categoria){
		$base = new Database();
		$query = "SELECT * FROM archivos WHERE categoria = ".$categoria;

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos;
		}else{
			return false;
		}
	}

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
	* SUBE UN ARCHIVO 
	* @param $archivo -> file ha subir
	* @param $nombre -> nombre del archivo *opcional
	* @param $categoria -> id de la categoria del archivo
	* @return true -> si la operacion se realizo exitosamente
	* @return false -> si ocurrio un error o fallo
	*/
	public function NuevoArchivo($archivo, $nombre, $categoria){
        $upload = new Upload();
        
        $upload->SetFileName($archivo['name']);
        $upload->SetTempName($archivo['tmp_name']);

        $upload->SetValidExtensions(array('gif', 'jpg', 'jpeg', 'png', 'zip', 'rar', 'pdf', 'txt', 'xls')); 
        
        $upload->SetUploadDirectory("../archivos/"); //DIRECTORIO PARA ARCHIVOS

        $upload->SetMaximumFileSize(90000000); //TAMANO MAXIMO PERMITIDO
        
        //SUBE EL ARCHIVO
        if($upload->UploadFile()){
        	//LINK DONDE SE SUBIO EL ARCHIVO
            $link = $upload->GetUploadDirectory().$upload->GetFileName();
            
            $link = str_replace("../", "", $link);

            //GUARDA EL LINK Y LOS DATOS EN LA BASE DE DATOS
            if($this->setArchivo($nombre, $link, $categoria)){
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
	* GUARDA LOS DATO DE UN NUEVO ARCHIVO
	* @param $nombre -> nombre archivo
	* @param $link -> link archivo subido
	* @param $categoria -> id de la categoria a la que pertenece
	* @return true si se guarda correctamente
	* @return false si falla
	*/
	private function setArchivo($nombre, $link, $categoria){
		$base = new Database();
		$query = "INSERT INTO archivos (nombre, link, categoria) VALUES ('".$nombre."', '".$link."', '".$categoria."')";

		if($base->Insert($query)){
			return true; //SE GUARDO
		}else{
			return false;
		}
	}

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
	* @param $id -> id de la categoria
	*/
	public function UpdateCategoria($nombre, $id){
		$base = new Database();
		$query = "UPDATE categorias SET nombre = '".$nombre."' WHERE id = '".$id."'";

		$nombre = mysql_real_escape_string($nombre);

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
	* ELIMINA TODOS LOS DATOS DE UNA CATEGORIA, ARCHIVOS Y DATOS ASOCIADOS DE LA CATEGORIA
	* @param $id -> id de la categoria ha eliminar
	*/
	public function DeleteCategoria($id){
		$base = new Database();
		$query = "DELETE FROM categorias WHERE id = ".$id;

		if( $base->Existe("SELECT * FROM categorias where id = ".$id) ){
			if($base->Delete($query)){

				//BORRA ARCHIVOS ASOCIADOS
				if($base->Existe("SELECT * FROM archivos WHERE categoria = ".$id)){
					$query = "SELECT * FROM archivos WHERE categoria = ".$id;
					$archivos = $base->Select($query);
					//elimina archivos
					foreach ($archivos as $fila => $archivo) {
						$this->DeleteArchivo($archivo['id']);
					}
				}

				$base->conect(); //PORQUE SINO CIERRA LA CONEXION
				                        
				//BORRA DATOS ASOCIADOSs
				if($base->Existe("SELECT * FROM datos WHERE categoria = ".$id)){
					$query = "DELETE FROM datos WHERE categoria = ".$id;
					if($base->Delete($query)){
					}
				}
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
	* @param $hijos[][]
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
	* ELIMINA TODOS LOS REGISTROS DE UN PROYECTO
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
	* @param $padre -> id del padre
	* @param $nombre -> nombre nuevo
	*/
	public function NuevaSubCategoria($padre, $nombre){

		$base = new Database();
		$query = "INSERT INTO categorias (nombre, padre) VALUES ( '".$nombre."', '".$padre."')";

		$nombre = mysql_real_escape_string($nombre);
		
		if( $base->Insert($query)){
			return true;
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