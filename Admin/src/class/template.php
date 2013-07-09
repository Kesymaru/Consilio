<?php
/**
 * Created by IntelliJ IDEA.
 * User: Andrey
 * Date: 6/24/13
 * Time: 3:46 PM
 * CLASE PARA LOS TEMPLATES
 */

/**
 * CLASE PARA USAR LOS TEMPLATES
 */
class Template{

    private $template = "";
    private $header = '';
    private $footer = '';
    private $message = '';
    private $from = 'matriz@matriz.com';
    private $to = '';
    private $bcc = '';
    private $title = '';

    //links
    private $admin = '';
    private $home = '';

    public function __construct(){

        date_default_timezone_set('America/Costa_Rica');

        $protocolo = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $dominio = $_SERVER['HTTP_HOST'];

        $this->admin = $protocolo.$dominio.'/matrizescala/Admin';
        $this->home = $protocolo.$dominio.'/matrizescala';
    }

    /**
     * OBTIENE EL TEMPLATE A USAR
     * @param $name nombre del template a usar
     * @return boolean|string
     */
    public function getTemplate($name = "default"){

	    if( $template = file_get_contents("templates/$name.php") ){
			$template = $this->setInfo($template);
		    return $template;
	    }else{
		    echo 'no se pudo obtener el template';
	    }
        return false;
    }

	/**
	 * OBTIENE EL TEMPLATE CON LA DATA PUESTA
	 * @param string $name nombrel del template a usar
	 * @param array  $data datos a poner
	 * @return boolean|string
	 */
	public function getTemplateData($name = "default", $data){

		if( $template = file_get_contents("templates/$name.php") ){
			if( is_array($data) ){
				$template = $this->setInfo($template);
				$template = $this->setData($template, $data);
				return $template;
			}else{
				echo "data debe ser un array";
			}
		}else{
			echo 'no se pudo obtener el template';
		}
		return false;
	}

    /**
     * PONE LA INFO POR DEFECTO
     */
    private function setInfo($template){
        $info = 'Matriz Escala';
        $mobile = '123 456';
        $phone = '987 654';
        $email = 'support@ecala.com';

        $direccion_edificio = 'Edificio 2, piso 3';
        $direccion = 'La sabana, San JOSE, Costa Rica';

	    $disclaim_title = "Disclaim";
	    $disclaim_text = "Texto del disclaim";

	    $remplazar = array(
					"{{info_from}}" => $info,
					"{{info_mobile}}" => $mobile,
					"{{info_phone}}" => $phone,
					"{{info_email}}" => $email,
					"{{info_home}}" =>  $this->home,
					"{{direccion_edificio}}" => $direccion_edificio,
					"{{direccion}}" => $direccion,
		            "{{disclaim_title}}" => $disclaim_title,
		            "{{disclaim_text}}" => $disclaim_text
	    );

	    return $this->setData($template, $remplazar);
    }

	/************* MODIFICADORES *************/

	/**
	 * PERMITE REMPLAZAR PALABRAS CLAVES CON VALORES
	 * @param string $template
	 * @param array $remplazar la key es el campo ha remplazar
	 */
	public function setData($template, $remplazar){
		if( is_array($remplazar) ){
			foreach($remplazar as $key => $dato ){
				$template = str_replace("$key", $dato, $template);
			}

			return $template;
		}

		return false;
	}

}