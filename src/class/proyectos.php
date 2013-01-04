<?php
/**
* CLASE PARA EL MANEJO DE DATOS DE LOS PROYECTOS
*/
require_once("classDatabase.php");
require_once("usuarios.php"); 
require_once("registros.php"); 

class Proyectos{

	/**
	* OBTIENE LOS PROYECTOS 
	* @return $datos -> array[][] con los datos de los proyectos
	*/
	public function getProyectos(){
		$base = new Database();
		$query = "SELECT * FROM proyectos";

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
	* @return true si se guardo el nuevo proyecto correctamente
	* @return false si fallo al guardase el nuevo proyecto
	*/
	function NewProyecto($nombre, $cliente, $descripcion, $imagen){
		$base = new Database();
		
		$nombre = mysql_real_escape_string($nombre);
		$descripcion = base64_decode($descripcion);

		$query = "INSERT INTO proyectos (nombre, cliente, descripcion, imagen, status) VALUES ('".$nombre."', '".$cliente."', '".$descripcion."', '".$imagen."', 1)";
		
		if($base->Insert($query)){
			return true;
		}else{
			return false;
		}
	}

	/**
	* ACTUALIZA UN PROYECTO
	* @param $nombre -> nombre del proyecto
	* @param $descripcion -> descripcion del proyecto
	* @param $imagen -> logo adjuntado al proyecto
	* @return true si se actualiza el proyecto correctamente
	* @return false si fallo 
	*/
	function UpdateProyecto($nombre, $cliente, $descripcion, $imagen, $estado){
		$base = new Database();
		
		$nombre = mysql_real_escape_string($nombre);
		$descripcion = base64_decode($descripcion);

		if($imagen != ''){
			if( $imagen = $this->UploadImagen($imagen) ){
				$query = "SELECT * FROM proyectos WHERE id = ".$id;
				$imagenOld = $base->Select($query);
				
				$imagenOld = "../".$imagenOld;

				if(!$base->DeleteImage($imagenOld)){
					echo 'Error: No se pudo eliminar la imagen antigua del proyecto.';
				}
			}
		}

		$query = "UPDATE proyectos SET nombre = '".$nombre."', cliente = '".$cliente."', descripcion = '".$descripcion."', imagen = '".$imagen."', status = '".$estado."'";
		
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
	private function UploadImagen($imagen){
		//SUBE LA IMAGEN
		if($imagen['tmp_name'] != null && $imagen['tmp_name'] != ""){
			$upload = new Upload();
        
			$upload->SetFileName($imagen['name']);
			$upload->SetTempName($imagen['tmp_name']);

			$upload->SetValidExtensions(array('gif', 'jpg', 'jpeg', 'png')); 
				        
			$upload->SetUploadDirectory("../images/proyectos/"); //DIRECTORIO PARA IMAGENES DE LOS PROYECTOS

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
		$query = "DELETE FROM proyectos WHERE id = ".$id;

		//BORRA LA IMAGEN DEL DIRECTORIO Y ELIMINA TODOS LOS REGISTROS DEL PROYECTO
		if( $this->UpdateProyectoImagen("", $id) && $registros->DeleteRegistros($id) ){

			if($base->Delete($query)){
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
}

?>