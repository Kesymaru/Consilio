<?php
/**
* CLASE PARA EL MANEJO DE DATOS DE LOS PROYECTOS
*/
require_once("classDatabase.php");
require_once("usuarios.php"); 
require_once("registros.php"); 
require_once("session.php");

class Proyectos{

	public function __construct(){
		//SEGURIDAD LOGUEADO
		$session = new Session();
		$session->Logueado();
	}

	/**
	* OBTIENE LOS PROYECTOS 
	* @return $datos -> array[][] con los datos de los proyectos
	*/
	public function getProyectos(){
		$base = new Database();
		$query = "SELECT * FROM proyectos ORDER BY nombre";

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos;
		}else{
			return false;
		}
	}

	/**
	* GUARDA UN NUEVO PROYECTO
	* @param $nombre -> nombre del proyecto
	* @param $descripcion -> descripcion del proyecto
	* @param $imagen -> logo adjuntado al proyecto
	* @param $estado -> estado del proyecto
	* @return true si se guardo el nuevo proyecto correctamente
	* @return false si fallo al guardase el nuevo proyecto
	*/
	function NewProyecto($nombre, $cliente, $descripcion, $imagen, $estado){
		$base = new Database();
		
		$nombre = mysql_real_escape_string($nombre);
		$descripcion = base64_encode($descripcion);

		$query = "INSERT INTO proyectos (nombre, cliente, descripcion, imagen, status)";
		$query .= " VALUES ('".$nombre."', '".$cliente."', '".$descripcion."', '".$imagen."', '".$estado."')";

		if($base->Insert($query)){
			$proyecto = $base->getUltimoId();
			$query = "SELECT * FROM proyectos WHERE id = ".$proyecto;
			
			$datos = $base->Select($query);

			$registro = new Registros();

			$reg = array();

			//crea registros para el nuevo proyecto
			if($registro->NewRegistro($proyecto, $reg, $datos[0]['fecha'])){
				return true;
			}else{
				return false;
			}
			
		}else{
			return false;
		}
	}

	/**
	* ACTUALIZA UN PROYECTO
	* @param $id -> id del proyecto ha actualizar
	* @param $nombre -> nombre del proyecto
	* @param $descripcion -> descripcion del proyecto
	* @param $imagen -> logo adjuntado al proyecto
	* @param $estado -> estado del proyecto
	* @return true si se actualiza el proyecto correctamente
	* @return false si fallo 
	*/
	function UpdateProyecto($id, $nombre, $cliente, $descripcion, $imagen, $estado){
		$base = new Database();
		
		$nombre = mysql_real_escape_string($nombre);
		$descripcion = base64_encode($descripcion);

		if($imagen != ''){ 
			//actualiza imagen
			$query = "UPDATE proyectos SET nombre = '".$nombre."', cliente = '".$cliente."', descripcion = '".$descripcion."', imagen = '".$imagen."', status = '".$estado."' WHERE id = ".$id;			
		}else{
			$query = "UPDATE proyectos SET nombre = '".$nombre."', cliente = '".$cliente."', descripcion = '".$descripcion."', status = '".$estado."' WHERE id = ".$id;
		}

		if($base->Update($query)){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * SUBE Y GUARDA EL LINK DE LA IMAGEN DE UN CLIENTE
	 * @param $id -> id del cliente
	 * @param $imagen -> file de la imagen ha subir
	 * @return true si se realiza
	 * @return false su falla
	 */
	public function UploadProyectoImagen($id, $imagen){
				        
		if($link = $this->UploadImagen($imagen) ){

			$base = new Database();

			$query = "SELECT * FROM clientes WHERE id = ".$id;
			$imagenOld = $base->Select($query);

			$query = "UPDATE proyectos SET imagen = '".$link."' WHERE id = ".$id;
			
			//actualiza imagen
			if($base->Update($query)){

				//borra imagen vieja
				$imagenOldLink = "../".$imagenOld[0]['imagen'];

				if( !$base->DeleteImagen($imagenOldLink) ){
					echo "Error: no se pudo borrar la imagen anterior, Error usuarios.php UploadClienteImagen() linea 142";
				}

				return true;
			}else{
				return false;
			}

		}else{
			return fale; //fallo al subir archivo
		}
	}

	/**
	* SUBE UNA IMAGEN
	* @param $imagen -> file de la imagen ha dubir
	* @return $link -> link de la imagen subida
	* @return false si falla
	*/
	public function UploadImagen($imagen){
		//SUBE LA IMAGEN
		if($imagen['tmp_name'] != null && $imagen['tmp_name'] != ""){
			$upload = new Upload();
        
			$upload->SetFileName($imagen['name']);
			$upload->SetTempName($imagen['tmp_name']);

			$upload->SetValidExtensions(array('gif', 'jpg', 'jpeg', 'png')); 
				        
			$upload->SetUploadDirectory("../images/proyectos/"); //DIRECTORIO PARA IMAGENES DE LOS USUARIOS

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
	* ELIMINA UN PROYECTO
	* @param $id -> id del proyecto ha ser eliminado
	*/
	public function DeleteProyecto($id){
		$registros = new Registros();
		
		$base = new Database();

		$imagen = $base->Select("SELECT * FROM proyectos WHERE id = ".$id);
		$imagen = "../".$imagen[0]['imagen'];
			
		//BORRA LA IMAGEN DEL DIRECTORIO Y ELIMINA TODOS LOS REGISTROS DEL PROYECTO
		if( $base->DeleteImagen($imagen) && $registros->DeleteRegistros($id) ){
			$base2 = new Database();
			$query = "DELETE FROM proyectos WHERE id = ".$id;

			if($base2->Delete($query)){
				return true;
			}else{
				return false;
			}
		}
	}

	/**
	* OBTIEN LOS DATOS DE UN PROYECTO
	* @param $id -> id del proyecto
	* @return $datos -> array[][] datos del proyecto
	*/
	public function getProyectoDatos($id){
		$base = new Database();
		$query = "SELECT * FROM proyectos WHERE id = '".$id."'";

		$datos = $base->Select($query);

		if(!empty($datos)){
			 return $datos;
		}else{
			return false;
		}
	}

	/**
	 * DUPLICA UN PROYECTO
	 * @param $id -> id del proyecto a duplicar
	 * @return id del nuevo proyecto
	 * @return false si falla
	 */
	public function DuplicarProyecto($id){
		$base = new Database();

		$query = "SELECT * FROM proyectos WHERE id = ".$id;

		$datos = $base->Select($query);

		if(!empty($datos)){
			//duplica la imagen
			$imagen = $this->DuplicarImagen($datos[0]['imagen']);

			$query = "INSERT INTO proyectos (nombre, descripcion, cliente, imagen, status) VALUES ";
			$query .= "('".$datos[0]['nombre']."', '".$datos[0]['descripcion']."', '".$datos[0]['cliente']."', '".$imagen."', '".$datos[0]['status']."' )";
			
			if($nuevo = $base->Insert($query)){
				$nuevo = $base->getUltimoId();

				$registros = new Registros();
				$registros->DuplicarRegistros($id, $nuevo);
				
				return $nuevo;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	 * DUPLICA LA IMAGEN DE UN PROYECTO
	 * @param $link -> link de la imagen a copiar
	 * @return $destino -> link de la imagen copiada
	 * @return imagen por defecto
	 */
	private function DuplicarImagen($link){
		$link = "../".$link;
		
		$destino = basename($link);
		$destino = "images/proyectos/".rand().$destino;

		//si la imagen no existe por alguna razon
		if(file_exists($link)){
			return "images/es.png";
		}

		if(copy($link, "../".$destino)){
			return $destino;
		}else{
			return "images/es.png";
		}
	}
}

?>