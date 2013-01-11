<?php
/**
* CLASE PARA MANEJAR LOS DATOS DE LOS USUARIOS
*/
require_once('session.php');
require_once('classDatabase.php');
require_once("imageUpload.php");

/**
* PARA MANEJAR LOS CLIENTES
*/
class Cliente{

	/**
	* ASEGURA QUE SOLO SI EL USUARIO ESTA LOGUEADO PUEDA USAR EL SCRIPT
	*/
	public function __construct(){
		
		//revisa que este logueado
		$session = new Session();
		$session->Logueado();

	}

	/**
	* OBTENER UN DATO DE UN CLIENTE
	* @param $dato -> dato requerido
	* @param $id -> id del cliente
	* @return $dato
	*/
	public function getClienteDato($dato, $id){
		$base = new Database();
		$datos = $base->Select("SELECT ".$dato." FROM clientes WHERE id = '".$id."'");
		return $datos[0][$dato];
	}

	/**
	* OBTIENE TODOS LOS CLIENTES
	* @return $datos array[][] con los datos de los clientes
	*/
	public function getClientes(){
		$base = new Database();
		$query = "SELECT * FROM clientes";

		$datos = $base->Select($query);

		if(!empty($datos)){
			return $datos;
		}else{
			return false;
		}
	}

	/**
	* OBTIENE TODOS LOS DATOS DE UN CLIENTE
	* @param $id -> id del cliente
	*/
	function getDatosCliente($id){
		$base = new Database();
		$query = "SELECT * FROM clientes WHERE id = ".$id;

		$datos = $base->Select($query);
		
		if(!empty($datos)){
			return $datos;
		}else{
			return false;
		}
	}

	/**
	 * ACTUALIZA DATOS DE UN CLIENTE
	 * @param $id, $nombre, $registro, $email, $telefono, $skype
	 */
	public function UpdateCliente($id, $nombre, $email, $registro, $telefono, $skype ){
		$base = new Database();
		
		$nombre = mysql_real_escape_string($nombre);
		$email = mysql_real_escape_string($email);
		$registro = mysql_real_escape_string($registro);
		$skype = mysql_real_escape_string($skype);

		$query = "UPDATE clientes SET nombre = '".$nombre."', email = '".$email."', registro = '".$registro."', ";
		$query .= "telefono = '".$telefono."', skype = '".$skype."' WHERE id = ".$id;

		if( $base->Update($query) ){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * CREA UN NUEVO CLIENTE
	 * @param $nombre
	 * @param $email
	 * @param $registro
	 * @param $telefono
	 * @param $skype
	 * @param $imagen -> link de la imagen ya subida
	 * @param $contrasena -> sin encriptar
	 */
	public function NewCliente($nombre, $email, $registro, $telefono, $skype, $imagen, $contrasena, $usuario){
		$base = new Database();
		
		$nombre = mysql_real_escape_string($nombre);
		$email = mysql_real_escape_string($email);
		$registro = mysql_real_escape_string($registro);
		$skype = mysql_real_escape_string($skype);
		$contrasena = mysql_real_escape_string($contrasena);
		$usuario = mysql_real_escape_string($usuario);

		$contrasena = $base->Encriptar($contrasena);

		$query = "INSERT INTO clientes (nombre, email, registro, telefono, skype, imagen, contrasena, usuario)";
		$query .= " VALUES ('".$nombre."', '".$email."', '".$registro."', '".$telefono."', '".$skype."', '".$imagen."', '".$contrasena."', '".$usuario."') ";

		if($base->Insert($query)){
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
	public function UploadClienteImagen($id, $imagen){
				        
		if($link = $this->UploadImagen($imagen) ){

			$base = new Database();

			$query = "SELECT * FROM clientes WHERE id = ".$id;
			$imagenOld = $base->Select($query);

			$query = "UPDATE clientes SET imagen = '".$link."' WHERE id = ".$id;
			
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
				        
			$upload->SetUploadDirectory("../images/users/"); //DIRECTORIO PARA IMAGENES DE LOS USUARIOS

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
	* DELETE CLIENTE
	* @param $id -> id del cliente
	* @return true si se elimina el cliente y sus datos
	*/
	function DeleteCliente($id){
		$base = new Database();

		$cliente = $base->Select("SELECT * FROM clientes WHERE id = ".$id);
		$imagenCliente = "../".$cliente[0]['imagen'];

		if(!$base->DeleteImagen($imagenCliente)){
			echo '<hr>Error: usuarios.php Clientes->DeleteCliente() al eliminar imagen: '.$imagenCliente;
		}

		if($base->Delete("DELETE FROM clientes WHERE id = ".$id)){

			$query = "SELECT * FROM proyectos WHERE cliente = ".$id;
			$proyectos = $base->Select($query);

			if(!empty($proyectos)){	 //tiene proyectos			
				//ELIMINA REGISTROS
				foreach ($proyectos as $f => $proyecto) {

					//elimina imagen del proyecto
					$imagenProyecto = "../".$proyecto['imagen'];

					if(!$base->DeleteImagen($imagenProyecto)){
						echo '<hr>Error: usuarios.php Clientes->DeleteCliente() al eliminar imagen: '.$imagenProyecto;
					}

					$query = "DELETE FROM registros WHERE proyecto = ".$proyecto['id'];

					//elimina registro del proyecto
					if( !$base->Delete($query) ){
						echo 'Error: usuarios.php Clientes->DeleteCliente() al eliminar registro del proyecto '.$proyecto['id'].' del cliente: '.$id;
					}
				}

				//elimina proyectos del cliente
				if( !$base->Delete("DELETE FROM proyectos WHERE cliente = ".$id) ){
					echo 'Error: usuarios.php Clientes->DeleteCliente() al eliminar proyectos del cliente: '.$id;
				}
			}
			return true;
		}else{
			return false;
		}
	}

	/**
	* OBTIENE USERS UTILIZADOS
	*/
	public function getUsers(){
		$base = new Database();
		$query = "SELECT usuario FROM clientes";

		$datos = $base->Select($query);
		return $datos;
	}

	/**
	* DESENCRIPTA UN PASSWORD
	* @param $password -> password ha desencriptar
	*/
	public function Encriptar($password){
		$base = new Database();
		$password = $base->Encriptar($password);
		return $password;
	}

}

/**
* PARA LOS ADMINS
*/
class Admin{
	private $session = '';

	/**
	* ASEGURA QUE SOLO SI EL USUARIO ESTA LOGUEADO PUEDA USAR EL SCRIPT
	*/
	public function __construct(){
		$this->session = new Session();
	}

/*** METODOS PARA DATOS DE ADMIN ***/

	/**
	* METODO PARA OBTENER DATOS DE ADMIN
	* @param $dato -> dato a obtener
	* @return dato consultado
	*/
	public function getAdminDato($dato){
		$base = new Database();
		$datos = $base->Select("SELECT ".$dato." FROM admin WHERE id = '".$_SESSION['id']."'");
		return $datos[0][$dato];
	}

	/**
	* METODO PARA ACTUALIZAR UN DATO DEL USUARIO
	* @param $dato -> dato ha actualizar
	* @param $nuevo -> el nuevo dato
	*/
	public function setAdminDato($dato, $nuevo){
		$base = new Database();
		$query = "UPDATE admin SET ".$dato." = '".$nuevo."' WHERE id = ".$_SESSION['id'];
		$base->Update($query);

		//actualiza datos en session
		$this->session->Update("admin");
	}

	/**
	* METODO PARA CAMBIAR EL PASSWORD DEL ADMIN
	* @param $password -> password sin encriptar
	*/
	public function setAdminPassword($password){
		$base = new Database();
		$password = $base->Encriptar($password);

		$query = "UPDATE admin SET password = '".$password."' WHERE id = ".$_SESSION['id'];
		$base->Update($query);
	}

	/**
	* METODO PARA ACTUALIZAR LA IMAGEN DEL ADMIN
	* @param $link -> link de la imagen
	*/
	public function setAdminImagen($link){
		$base = new Database();

		//link viejo
		$imagenOld = $this->getAdminDato('imagen');

		$query = "UPDATE admin SET imagen = '".$link."' WHERE id = ".$_SESSION['id'];
		
		if($base->Update($query)){
			$base->DeleteImagen($imagenOld);
			return true;
		}else{
			return false;
		}
	}

}

?>