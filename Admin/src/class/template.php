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

	private $siteName = "Escala Consultores";
	private $site = 'http://escalaconsultores.com';

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
        $info = 'Consultores Escala';
        $mobile = '';
        $phone = '+(506) 2290-2716';
	    $fax = "+(506) 2296-2778";
        $email = 'support@escala.com';

        $direccion_edificio = 'Oficentro Ejecutivo la sabana Torre 7, Piso 2';
        $direccion = 'Sabana Sur, San Jos$eacute;, Costa Rica';

	    $disclaim_title = "Aviso de Confidencialidad.";
	    $disclaim_text = "Este correo electrónico y/o el material adjunto es para el usu exclusivo de la persona o entidad a la que expresamente se le ha enviado y puede contener información confidencial o material privilegiado. Si usted no es el destinatario legítimo del mismo por favor reportélo inmediatamente al remitente del correo y borrelo. Cualquier revisión queda expresamente prohibido. Este correo electrónico no pretende ni debe ser considerado como constitutivo de ninguna relación legal contractual o de otra índole similar.";

	    $remplazar = array(
					"{{info_from}}" => $info,
					"{{info_mobile}}" => $mobile,
					"{{info_phone}}" => $phone,
		            "{{info_fax}}" => $fax,
					"{{info_email}}" => $email,
		            "{{info_homeLink}}" => $this->site,
					"{{info_home}}" =>  $this->siteName,
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