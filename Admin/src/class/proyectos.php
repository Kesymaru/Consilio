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
	* @param $visible -> estado de visibilidad del proyecto
	* @return true si se guardo el nuevo proyecto correctamente
	* @return false si fallo al guardase el nuevo proyecto
	*/
	function NewProyecto($nombre, $cliente, $descripcion, $imagen, $estado, $visible){
		$base = new Database();
		
		$nombre = mysql_real_escape_string($nombre);
		$descripcion = base64_encode($descripcion);

		$query = "INSERT INTO proyectos (nombre, cliente, descripcion, imagen, status, visible, fecha_creacion )";
		$query .= " VALUES ('".$nombre."', '".$cliente."', '".$descripcion."', '".$imagen."', '".$estado."', '".$visible."', NOW() )";

		if($base->Insert($query)){
			$proyecto = $base->getUltimoId();
			$query = "SELECT * FROM proyectos WHERE id = ".$proyecto;
			
			$datos = $base->Select($query);

			$registro = new Registros();

			$reg = array();

			//crea registros para el nuevo proyecto
			if($registro->NewRegistro($proyecto, $reg, $datos[0]['fecha_creacion'])){
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
	* @param $visible -> estado de visibilidad del proyecto
	* @return true si se actualiza el proyecto correctamente
	* @return false si fallo 
	*/
	function UpdateProyecto($id, $nombre, $cliente, $descripcion, $imagen, $estado, $visible){
		$base = new Database();
		
		$nombre = mysql_real_escape_string($nombre);
		$descripcion = base64_encode($descripcion);

		//si se desactiva
		$desactivo = '';
		if($estado == 0){
			$desactivo = ", fecha_desactivacion = NOW() ";
		}

		if($imagen != ''){ 
			//actualiza imagen
			$query = "UPDATE proyectos SET nombre = '".$nombre."', cliente = '".$cliente."', descripcion = '".$descripcion."', imagen = '".$imagen."', status = '".$estado."', visible = '".$visible."', fecha_actualizacion = NOW() ".$desactivo." WHERE id = ".$id;			
		}else{
			$query = "UPDATE proyectos SET nombre = '".$nombre."', cliente = '".$cliente."', descripcion = '".$descripcion."', status = '".$estado."', visible = '".$visible."', fecha_actualizacion = NOW() ".$desactivo." WHERE id = ".$id;
		}

		if($base->Update($query)){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * SUBE Y GUARDA EL LINK DE LA IMAGEN DE UN PROYECTO
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
	* ELIMINA TODOS LOS DATOS DE UN PROYECTO
	* @param int $id -> id del proyecto ha ser eliminado
	* @param boolean true -> la eliminacion fue exitosa
	* @param boolean false -> fallo
	*/
	public function DeleteProyecto($id){
		$registros = new Registros();
		$base = new Database();

		$id = mysql_real_escape_string($id);

		$datosImagen = $base->Select("SELECT * FROM proyectos WHERE id = ".$id);
		$imagen = "../".$datosImagen[0]['imagen'];
		
		$error = '';

		//borra los registros del proyecto, comentarios, observaciones, normas incluidas, articulos incluidos, categorias incluidas
		if( !$registros->DeleteRegistros($id) ){
			$error .= 'Error: no se pudo eliminar los registros del proyecto id '.$id.'<br/>proyectos.php DeleteProyecto <br/>';
		}

		//elimina la imagen del proyecto
		if( !$base->DeleteImagen($imagen) ){
			$error .= 'Error: no se pudo eliminar la imagen del proyecto id '.$id.'<br/>imagen: '.$imagen.'<br/>';
		}

		if( $error != ''){
			echo $error;
			return false;
		}else{
			return true;
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
	* OBTIEN UN DATO DE UN PROYECTO
	* @param $dato -> datos olicitado
	* @param $id -> id del proyecto
	* @return dato solicitado
	*/
	public function getProyectoDato($dato, $id){
		$base = new Database();
		$query = "SELECT * FROM proyectos WHERE id = '".$id."'";

		$datos = $base->Select($query);

		if(!empty($datos)){
			 return $datos[0][$dato];
		}else{
			return false;
		}
	}

	/**
	 * DUPLICA UN PROYECTO Y TODOS SUS REGISTROS
	 * @param $id -> id del proyecto a duplicar
	 * @return int $nuevo -> id del nuevo proyecto duplicado
	 * @return boolean false -> si falla
	 */
	public function DuplicarProyecto($id){
		$base = new Database();

		$id = mysql_real_escape_string($id);

		$query = "SELECT * FROM proyectos WHERE id = '".$id."'";

		$datos = $base->Select($query);

		if( !empty($datos) ){
			//duplica la imagen
			$imagen = $this->DuplicarImagen( $datos[0]['imagen'] );

			$query = "INSERT INTO proyectos (nombre, descripcion, cliente, imagen, status) VALUES ";
			$query .= "('".$datos[0]['nombre'].' COPIA'."', '".$datos[0]['descripcion']."', '".$datos[0]['cliente']."', '".$imagen."', '".$datos[0]['status']."' )";
			
			//obtiene el id del nuevo proyecto
			if( $base->Insert($query) ){
				$nuevo = $base->getUltimoId();

				$registros = new Registros();
				
				if( $registros->DuplicarRegistros($id, $nuevo) ){
					return $nuevo;
				}else{
					return false;
				}

			}else{
				return false;
			}
		}else{
			//no existe el proyecto
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

		if(copy($link, "../".$destino)){
			return $destino;
		}else{
			return "images/es.png";
		}
	}
}

?>