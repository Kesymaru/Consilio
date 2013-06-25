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
     * COMPONE EL UN EMAIL PARA UNA NOTIFICACION
     * @param array $datos datos para la notificacion
     * @param string $themplate nombre del template a usar
     * @return bool
     */
    public function Email($datos, $template = 'default'){

        if( !$this->getTemplate($template) ){
            return false;
        }

        //pone los datos
        $this->Set( $datos );

        //obtiene el destinatario
        $this->To($datos['emails']);

        //botiene el bcc
        if( array_key_exists('bcc', $datos)){
            $this->Bcc($datos['bcc']);
        }

        return $this->template;
    }

    /**
     * OBTIENE EL TEMPLATE A USAR
     * @param $name nombre del template a usar
     */
    private function getTemplate($name){
        $base = new Database();

        $query = "SELECT
                    *
                  FROM
                    email_templates
                  WHERE
                    nombre = '".$name."' ";

        if( $template = $base->Select($query) ){
//            echo '<pre>'; print_r($template); echo '<pre>';
            $this->template =  $template[0]['template'];
            return true;
        }
        return false;
    }

    /**
     * PONE LOS DATOS
     * @param $datos
     */
    private function Set($datos){
        //pone el titulo
        $this->template = str_replace("{{title}}", $datos['title'],$this->template);

        //pone el mensaje
        $this->template = str_replace("{{menssage}}", $datos['menssage'], $this->template);

        //datos cliente
        $this->template = str_replace("{{cliente_nombre}}", $datos['cliente_nombre'], $this->template);
        $this->template = str_replace("{{cliente_imagen}}", $this->admin.'/'.$datos['cliente_imagen'], $this->template);

        //informacion de soporte
        if( array_key_exists('info_from', $datos) &&
            array_key_exists('info_mobile', $datos) &&
            array_key_exists('info_phone', $datos) &&
            array_key_exists('info_email', $datos) &&
            array_key_exists('info_home', $datos)
        ){

            $this->template = str_replace("{{info_from}}", $datos['info_from'],$this->template);
            $this->template = str_replace("{{info_mobile}}", $datos['info_mobile'],$this->template);
            $this->template = str_replace("{{info_phone}}", $datos['info_phone'],$this->template);
            $this->template = str_replace("{{info_email}}", $datos['info_email'],$this->template);
            $this->template = str_replace("{{info_home}}", $datos['info_home'],$this->template);

            //direcciones
            $this->template = str_replace("{{direccion_edificio}}", $datos['direccion_edificio'],$this->template);
            $this->template = str_replace("{{direccion}}", $datos['direccion'],$this->template);

        }else{
            //info por defecto
            $this->setInfo();
        }

        $this->setDisclaim();
    }

    /**
     * PONE LA INFO POR DEFECTO
     */
    private function setInfo(){
        $info = 'Matriz Escala';
        $mobile = '123 456';
        $phone = '987 654';
        $email = 'support@ecala.com';

        $direccion_edificio = 'Edificio 2, piso 3';
        $direccion = 'La sabana, San JOSE, Costa Rica';

        $this->template = str_replace("{{info_from}}", $info, $this->template);
        $this->template = str_replace("{{info_mobile}}", $mobile, $this->template);
        $this->template = str_replace("{{info_phone}}", $phone, $this->template);
        $this->template = str_replace("{{info_email}}", $email, $this->template);
        $this->template = str_replace("{{info_home}}", $this->home, $this->template);

        $this->template = str_replace("{{direccion_edificio}}", $direccion_edificio, $this->template);
            $this->template = str_replace("{{direccion}}", $direccion, $this->template);
    }

    /**
     * PONE EL DISCALIM POR DEFECTO
     */
    private function setDisclaim(){
        $title = 'disclain';
        $text = 'Texto del disclain, blabla blabla';

        $this->template = str_replace("{{disclaim_title}}", $title,$this->template);
        $this->template = str_replace("{{disclaim_text}}", $text,$this->template);
    }

    /**
     * COMPONE EL PARA
     * @param $datos
     */
    private function To($datos){
        $to = '';

        if( is_array($datos) ){
            if( array_key_exists('name',$datos[0]) ){
                foreach($datos as $f => $para ){
                    $to .= '<'.$para['name'].'>'.$para['email'].',';
                }
            }else{
                foreach($datos as $f => $para ){
                    $to .= $para.',';
                }
            }
        }else{
            $to = $datos;
        }

        $this->to = $to;
    }

    /**
     * COMPONE EL BCC
     * @param $emails
     */
    private function Bcc($emails){
        $bcc = '';

        if( is_array($emails) ){
            if( array_key_exists('name', $emails[0]) && array_key_exists('email', $datos[0]) ){
                foreach($emails as $f => $para ){
                    $bcc .= '<'.$para['name'].'>'.$para['email'].',';
                }
            }else{
                foreach($emails as $f => $para ){
                    $bcc .= $para['email'].',';
                }
            }
        }else{
            $bcc = $emails;
        }

        $this->bcc = $bcc;
    }
}