<?php

class Database{
	
	private $dbHost 	= "localhost";
	private $dbUser 	= "root";
	private $dbPassword = "root";
	private $dbDatabase = "matriz";
	
	private $dbLink      = 0;
	private $dbRecordSet = 0;
	public  $dbResult    = false;
	public $exite        = false;


/* Metodos principales */

	public function __construct(){
		//verificar configuracion
			
		//Conectar 
		$this->conect();
					
		if($this->dbDatabase != ""){
			$this->setBase();
			mysql_query("SET NAMES 'utf8'");
		}
	}
	
	//Conexion
	public function conect(){
		$this->dbLink= mysql_connect($this->dbHost, $this->dbUser) or die ("1. No funciona por " . mysql_error()); 
		//$this->dbLink= mysql_connect($this->dbHost, $this->dbUser, $this->dbPassword) or die ("1. No funciona por " . mysql_error()); 
	}
	//Seleccionar base	
	private function setBase(){
		mysql_select_db($this->dbDatabase) or die ("2. No funciona por " . mysql_error()); 
	}
		
	public function __destruct(){
		$this->disconnect();	
	}
	
	//Desconexion
	private function disconnect(){
		if($this->dbLink){
			//mysql_close($this->dbLink);	
		}
	}
	//Ejecuta consulta
	private function query($query){
		$resultado = mysql_query($query) or die ("3. Error en consulta " . $query . mysql_error());
		
		if(is_bool($resultado)){
			$this->dbResult = $resultado;
		}else{
			$this->dbRecordSet = $resultado;
		}
	}	
	//Devuelve numero de filas del recordset
	public function getRows(){
		return mysql_num_rows($this->dbRecordSet);
	}

	//Devuelve el recordSet en un arreglo
	public function getRecordSet(){
		$registros = array();
		if($this->getRows()){
			while($registro = mysql_fetch_assoc($this->dbRecordSet)){
				$registros[]=$registro;
			}
		}
		return $registros;
	}
		

	/**
	* MANEJO DE CONSULTAS
	* @param $query
	* @return $resultados['index']['valor']
	*/
	public function Select($query){
		$this->query($query);
		return $this->getRecordSet();
	}
	
	//Sentencia INSERT
	public function queryInsert($tabla,$datos){
	
        $campos  = "";
        $valores = "";
        
        foreach ($datos as $field => $value)
        {
            $campos 	.= "".$field.",";
            $valores 	.= ( is_numeric( $value )) ? $value."," : "'".$value."',";			
        }
		
		$campos 	= substr($campos, 0, -1);
        $valores 	= substr($valores, 0, -1);
		
        $sentencia = "INSERT INTO " . $tabla ."(".$campos.") VALUES( ".$valores.");";
		
		//echo $sentencia;
		$this->query($sentencia);
	}

	/**
	* INGRESA DATOS
	*/
	public function Insert($query){
		$this->query($query);
		return true;
	}

	/**
	* UPDATE
	*/
	public function Update($query){
		$this->query($query);
		return true;
	}

	/**
	* TRUNCA UNA TABLA
	* @param $tabla -> tabla ha limpiar
	*/
	public function Limpiar($tabla){
		$query = "TRUNCATE TABLE ".$tabla;
		mysql_query($query) or die('Error classDatabase.php: 04 en clear. '. mysql_errno());
	}
	
	/**
	* REVISA SI EXISTE UN DATO DENTRO DE UNA TABLA
	* @param $query -> query a ejecutar
	* @return tru -> si el query se ejecuto 
	*/
	public function Existe($query){

		$resultado = mysql_query($query) or die ("Error: 05 en Existe. " . mysql_error());
		
		if($resultado = mysql_fetch_array($resultado)){
			return true;
		}else{
			return false;
		}

	}

	/**
	* ENCRIPTA Y DESENCRIPTA UN TEXTO
	* @param $text => texto a encriptar o desencriptar
	* @return $text => texto encriptado
	*/
	public function Encriptar($text){
		//quita / y etiquetas html
		$text = stripcslashes($text);
		$text = strip_tags($text);
		$text = md5 ($text); 
		$text = crc32($text);
		$text = crypt($text, "xtemp"); 
		$text = sha1("xtemp".$text);
		return $text;
	}

	/**
	* METODO PARA BORRAR LA IMAGEN VIEJA DEL FOLDER images/users/
	* @param $link -> link imagen ha eliminar
	*/
	public function DeleteImagen($link){
		//session_start();
		//$link = str_replace($_SESSION['home'], "", $link);
		//$link = "../..".$link;
		//$link = "../../".$link;
		//echo '<br/>'.$_SERVER['DOCUMENT_ROOT'].$link;

		if(unlink($link)){
			return true;
		}else{
			return false;
		}
	}

}