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

		date_default_timezone_set('America/Costa_Rica');
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
		
		if(!empty($datos)){
			return $datos[0][$dato];
		}else{
			return false;
		}
	}

	/**
	* OBTIENE TODOS LOS CLIENTES
	* @return $datos array[][] con los datos de los clientes
	*/
	public function getClientes(){
		$base = new Database();
		$query = "SELECT * FROM clientes ORDER BY nombre";

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
	 * @param int $id
	 * @param string $nombre
	 * @param string $mail 
	 * @param int $pais -> id del pais
	 * @param string $telefono -> telefono puede tener extenciones y codigos
	 * @param strign $skype 
	 * @param int $usuario -> nombre usuario
	 * @param string $contrasena -> no requerido, si cambia, texto plano
	 */
	public function UpdateCliente($id, $nombre, $email, $pais, $registro, $telefono, $skype, $usuario, $contrasena ){
		$base = new Database();
		
		$nombre = mysql_real_escape_string($nombre);
		$email = mysql_real_escape_string($email);
		$pais = mysql_real_escape_string($pais);
		$registro = mysql_real_escape_string($registro);
		$skype = mysql_real_escape_string($skype);
		$usuario = mysql_real_escape_string($usuario);

		if($contrasena != ''){
			$contrasena = $base->Encriptar($contrasena);

			$query = "UPDATE clientes SET nombre = '".$nombre."', email = '".$email."', pais = '".$pais."', registro = '".$registro."', ";
			$query .= "telefono = '".$telefono."', skype = '".$skype."', usuario = '".$usuario."', contrasena = '".$contrasena."', fecha_actualizacion = NOW() WHERE id = ".$id;

		}else{
			$query = "UPDATE clientes SET nombre = '".$nombre."', email = '".$email."', pais = '".$pais."', registro = '".$registro."', ";
			$query .= "telefono = '".$telefono."', skype = '".$skype."', usuario = '".$usuario."', fecha_actualizacion = NOW() WHERE id = ".$id;
		}

		if( $base->Update($query) ){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * CREA UN NUEVO CLIENTE
	 * @param string $nombre
	 * @param string $email
	 * @param int $pais
	 * @param string $registro
	 * @param string $telefono
	 * @param string $skype
	 * @param file $imagen -> link de la imagen ya subida
	 * @param string $contrasena -> sin encriptar
	 */
	public function NewCliente($nombre, $email, $pais, $registro, $telefono, $skype, $imagen, $contrasena, $usuario){
		$base = new Database();
		
		$nombre = mysql_real_escape_string($nombre);
		$email = mysql_real_escape_string($email);
		$pais = mysql_real_escape_string($pais);
		$registro = mysql_real_escape_string($registro);
		$skype = mysql_real_escape_string($skype);
		$contrasena = mysql_real_escape_string($contrasena);
		$usuario = mysql_real_escape_string($usuario);

		$contrasena = $base->Encriptar($contrasena);

		$query = "INSERT INTO clientes (nombre, email, pais, registro, telefono, skype, imagen, contrasena, usuario, fecha_creacion )";
		$query .= " VALUES ('".$nombre."', '".$email."', '".$pais."', '".$registro."', '".$telefono."', '".$skype."', '".$imagen."', '".$contrasena."', '".$usuario."', NOW() ) ";

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
	* ELIMINA UN CLIENTE, SUS DATOS Y PROYECTOS
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
						echo '<br/>Error: usuarios.php Clientes->DeleteCliente() al eliminar imagen: '.$imagenProyecto;
					}

					$query = "DELETE FROM registros WHERE proyecto = ".$proyecto['id'];

					//elimina registros del proyecto
					if( !$base->Delete($query) ){
						echo '<br/>Error: usuarios.php Clientes->DeleteCliente() al eliminar registro del proyecto '.$proyecto['id'].' del cliente: '.$id;
					}
				}

				//elimina proyectos del cliente
				if( !$base->Delete("DELETE FROM proyectos WHERE cliente = ".$id) ){
					echo '<br/>Error: usuarios.php Clientes->DeleteCliente() al eliminar proyectos del cliente: '.$id;
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

	/*************** helpers ***********/

	/**
	* COMPONE LOS DATOS NECESARIOS PARA UN MAIL DEL USUARIO
	* @param $id -> id del usuario
	* @return $correo -> array[] con los datos
	*/
	public function getCorreo($id){
		$datos = $this->getDatosCliente($id);

		if(!empty($datos)){
			//COMPOSICION DEL CORREO
			$correp = array();

			$correo['nombre'] = $datos[0]['nombre'];

			//imagen del proyecto, fallback imagen del cliente
			$imagen = 'images/es.png';
			$imagenLink = '../'.$datos[0]['imagen'];

			if( file_exists($imagenLink) ){
				$imagen = $datos[0]['imagen'];
			}
			
			$correo['imagen'] = $imagen;

			$correo['destinatario'] = array( $datos[0]['nombre'] => $datos[0]['email'] );

			$correo['userId'] = $datos[0]['id'];

			//DATOS DEL REMITENTE
			$correo['remitente'] = array("nombre" => $_SESSION['nombre'], "email" => $_SESSION['email']);
			$correo['nombreRemitente'] = $_SESSION['nombre'].' '.$_SESSION['apellidos'];

			if(isset($_SESSION['titulo'])){
				$correo['tituloRemitente'] = $_SESSION['titulo'];
			}

			if(isset($_SESSION['mobile'])){
				$correo['mobile'] = $_SESSION['mobile'];
			}
			if(isset($_SESSION['fax'])){
				$correo['fax'] = $_SESSION['fax'];
			}
			if(isset($_SESSION['skype'])){
				$correo['skype'] = $_SESSION['skype'];
			}

			$correo['telefono'] = $_SESSION['telefono'];

			return $correo;
		}else{
			return false; //error no hay datos para el id del cliente
		}
	}

	/**
	* OBTIENE LOS PAISES
	*/
	public function getPaises(){
		$base = new Database();

		$query = "SELECT * FROM country";

		$datos = $base->Select($query);

		return $datos;
	}


	/************************************** LOGS DE CLIENTES ************************/

	/**
	* OBTIENE LOS LOGS DE UN CLIENTE
	* @param int $id -> id del cliente
	* @return array $logs -> los registros del cliente
	* @return boolean false -> si falla o si no tiene registros
	*/
	public function getClienteLogs( $id ){
		$base = new Database();

		$id = mysql_real_escape_string( $id );

		$query = "SELECT * FROM clientes_logs WHERE cliente = '".$id."'";

		$logs = $base->Select( $query );

		if( !empty($logs) ){
			return $logs;
		}else{
			return false;
		}
	}
 
}

/**
* PARA LOS ADMINS
*/
class Admin{

	/**
	* ASEGURA QUE SOLO SI EL USUARIO ESTA LOGUEADO PUEDA USAR EL SCRIPT
	*/
	public function __construct(){
		$session = new Session();
		$session->Logueado();
	}

/*** METODOS PARA DATOS DE ADMIN ***/
	
	/**
	* OBTIENE LOS DATOS DE TODOS LOS ADMINS
	* @return $datos -> array[][] con los datos
	* @return false -> si fallas
	*/
	public function getAdmins(){
		$base = new Database();
		$query = "SELECT * FROM admin ORDER BY nombre";

		if($datos = $base->Select($query)){
			return $datos;
		}else{
			return false;
		}
	}

	/**
	* OBTIENE TODOS LOS DATOS DE UN ADMIN
	* @param $id -> id del admin
	* @return dato consultado
	*/
	public function getAdminDatos($id){
		$base = new Database();
		$query = "SELECT * FROM admin WHERE id = '".$id."'";

		if($datos = $base->Select($query)){
			return $datos;
		}else{
			return false;
		}
	}

	/**
	* ACTUALIZA UN ADMIN
	* NO ACTUALIZA LA CONTRASENA
	* @param $id -> id del admin
	* @param $usuario -> usuario
	* @param $nombre -> nombre
	* @param $apellidos -> apellidos del admin
	* @param $email -> email
	* @param $telefono -> telefono
	* @param $skype
	* @param $imagen -> link de la imagen subida
	* @param $password -> password nuevo si cambia
	* @return true si se actualiza
	* @return false si falla
	*/
	public function UpdateAdmin($id, $usuario, $nombre, $apellidos, $titulo, $email, $telefono, $mobile, $fax, $skype, $imagen, $password){
		$base = new Database();

		$nombre =  mysql_real_escape_string($nombre);
		$apellidos = mysql_real_escape_string($apellidos);
		$titulo = mysql_real_escape_string($titulo);
		$email = mysql_real_escape_string($email);
		$telefono = mysql_real_escape_string($telefono);
		$mobile = mysql_real_escape_string($mobile);
		$fax = mysql_real_escape_string($fax);
		$skype = mysql_real_escape_string($skype);
		$usuario = mysql_real_escape_string($usuario);
		$imagen = mysql_real_escape_string($imagen);
		$password = mysql_real_escape_string($password);

		if($imagen != '' && $imagen != null && !empty($imagen)){
			$query = "UPDATE admin SET nombre = '".$nombre."', apellidos = '".$apellidos."', titulo = '".$titulo."', email = '".$email."', telefono = '".$telefono."', mobile = '".$mobile."', fax = '".$fax."' skype = '".$skype."', usuario = '".$usuario."', imagen = '".$imagen."', fecha_actualizacion = NOW() ";
			
			//elimina la imagen vieja
			$query2 = "SELECT * FROM admin WHERE id = '".$id."'";
			$imagen = $base->Select($query2);
			$imagenOld = "../".$imagen[0]['imagen'];

			if(!$base->DeleteImagen($imagenOld)){
				echo '<br/>Error: imagen anterior del Admin no se pudo borrar.<br/>usuarios.php class Admin() UpdateAdmin()<br/>';
			}

		}else{
			$query = "UPDATE admin SET nombre = '".$nombre."', apellidos = '".$apellidos."', titulo = '".$titulo."', email = '".$email."', telefono = '".$telefono."', mobile = '".$mobile."', fax = '".$fax."', skype = '".$skype."', usuario = '".$usuario."', fecha_actualizacion = NOW()  ";
		}

		if($password != ''){
			$password = $base->Encriptar($password);
			$query .= ", password = '".$password."' ";
		}

		$query .= " WHERE id = '".$id."'";
		
		if($base->Update($query)){
			return true;
		}else{
			return false;
		}
	}

	/**
	* ACTUALIZA UN ADMIN
	* NO ACTUALIZA LA CONTRASENA
	* @param $usuario -> usuario
	* @param $nombre -> nombre
	* @param $apellidos -> apellidos del admin
	* @param $titulo -> titulo del admin no requerido
	* @param $email -> email
	* @param $telefono -> telefono
	* @param $mobil -> numero de cell no requerido
	* @param $fax -> nomero de fax no requerido
	* @param $skype
	* @param $imagen -> link de la imagen subida
	* @param $password -> contrasena sin ecncriptar
	* @return true si se actualiza
	* @return false si falla
	*/
	public function NewAdmin($usuario, $nombre, $apellidos, $titulo, $email, $telefono, $mobile, $fax, $skype, $imagen, $password){
		$base = new Database();

		$nombre =  mysql_real_escape_string($nombre);
		$apellidos = mysql_real_escape_string($apellidos);
		$titulo = mysql_real_escape_string($titulo);
		$email = mysql_real_escape_string($email);
		$telefono = mysql_real_escape_string($telefono);
		$mobile = mysql_real_escape_string($mobile);
		$fax = mysql_real_escape_string($fax);
		$skype = mysql_real_escape_string($skype);
		$usuario = mysql_real_escape_string($usuario);
		$imagen = mysql_real_escape_string($imagen);

		$password = mysql_real_escape_string($password);
		$password = $base->Encriptar($password);

		$query = "INSERT INTO admin (usuario, nombre, apellidos, titulo, email, telefono, mobile, fax, skype, imagen, password, fecha_creacion ) VALUES ";
		$query .= " ( '".$usuario."', '".$nombre."', '".$apellidos."', '".$titulo."', '".$email."', '".$telefono."', '".$mobile."', '".$fax."', '".$skype."', '".$imagen."', '".$password."', NOW() ) ";
		
		if($base->Insert($query)){
			return true;
		}else{
			return false;
		}
	}

	/**
	* OBTIENE TODOS LOS USUARIOS TOMADOS EXCEPTO EL DEL ADMIN
	* @param $id -> id del admin
	* @return $datos -> array[][] lista de usuarios disponibles
	*/
	function getUsersAdmin($id){
		$base = new Database();
		$query = "SELECT * FROM admin WHERE id != '".$id."'";

		$datos = $base->Select($query);
		return $datos;
	}

	/**
	* OBTIENE TODOS LOS USUARIOS YA TOMADOS
	* @return $datos -> array[][] lista de usuarios disponibles
	*/
	function getUsers(){
		$base = new Database();
		$query = "SELECT * FROM admin";

		$datos = $base->Select($query);
		return $datos;
	}

	/**
	* SUBE UNA IMAGEN PARA ADMIN
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
				        
			$upload->SetUploadDirectory("../images/admin/"); //DIRECTORIO PARA IMAGENES DE LOS USUARIOS

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
	* ELIMINA UN ADMIN
	* @param $id -> id del admin
	* @return true si se elimina
	*/
	function DeleteAdmin($id){
		$base = new Database();
		$query = "DELETE FROM admin WHERE id = '".$id."'";
		
		$query2 = "SELECT * FROM admin WHERE id = '".$id."'";
		$datos = $base->Select($query2);

		if(!empty($datos)){
			$imagen = "../".$datos[0]['imagen'];
			
			if(!$base->DeleteImagen($imagen)){
				echo "<br/>Error: No se pudo borrar la imagen del Admin.<br/>imagen:$imagen<br/>Usuarios.php DeleteAdmin().";
			}
		}

		if($base->Delete($query)){
			return true;
		}else{	
			return false;
		}
	}

	/**
	* ACTUALIA DATOS DE SESSION
	*/
	public function updateSession(){
		$base = new Database();
		$id = mysql_real_escape_string($_SESSION['id']);

		$query = "SELECT * FROM admin WHERE id = '".$id."'";

		$datos = $base->Select($query);

		if(!empty($datos)){
			foreach ($datos[0] as $key => $value) {
				if( $value != '' && $key != 'password' ){
					$_SESSION[$key] = $value;
				}else if( isset($_SESSION[$key])){
					unset($_SESSION[$key]);
				}
			}
		}
	}

	/**
	* OBTIENE LOS REGISTROS DE UN ADMIN
	* @param int $id -> id del admin
	* @return array $logs -> registros
	* @return boolean false -> si no tiene
	*/
	public function getAdminLogs( $id ){
		$base = new Database();

		$id = mysql_real_escape_string($id);

		$query = "SELECT * FROM admin_logs WHERE admin = '".$id."'";

		$logs = $base->Select( $query );

		if( !empty($logs) ){
			return $logs;
		}else{	
			return false;
		}
	}
}

?>