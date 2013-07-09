<?php
/**
 * Created by IntelliJ IDEA.
 * User: Andrey
 * Date: 6/24/13
 * Time: 10:48 AM
 * ESTA CLASE NO REQUIERE QUE ESTEN LOGUEADOS
 */

	error_reporting(-1);
require_once("classDatabase.php");
require_once("template.php");
require_once("mail.php");

class Notificaciones {

	private $templateManager = '';
	private $templates = array();

	private $home = "";
	private $admin = "";

    /**
     * OBTIENE LAS NOTIFICACIONES
     */
    public function __construct(){
        date_default_timezone_set('America/Costa_Rica');

	    $protocolo = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	    $dominio = $_SERVER['HTTP_HOST'];

	    $this->home = $protocolo.$dominio.'/matrizescala/';
	    $this->admin = $protocolo.$dominio.'/matrizescala/Admin/';

	    $this->templateManager = new Template();
    }

    /**
     *NOTIFICACIONES PARA LOS PERMISOS
     */
    public function Permisos(){

	    //obtiene los templates para permisos
		if( !$this->templates['permiso_expirado'] = $this->templateManager->getTemplate("permiso_expirado")){
			return false;
		}

	    if( !$this->templates['permiso_recordatorio'] = $this->templateManager->getTemplate("permiso_recordatorio")){
		    return false;
	    }

        if( $permisos = $this->getNotificacionesPermisos() ){
//            echo '<pre>'; print_r($permisos); echo '</pre>';

            foreach($permisos as $fila => $permiso ){
                //datos para la notificacion
                $datos = array(
                    "{{title}}" => 'Permisos Expirados: '.$permiso['proyecto_nombre'],
                    "{{cliente_nombre}}" => $permiso['cliente_nombre'],
                    "{{cliente_imagen}}" => $this->admin.$permiso['cliente_imagen'],
                );

                $to = '';
                foreach($permiso['emails'] as $f => $email ){
                    $to .= $email['email'].',';
                }
                $datos['{{to}}'] = $to;

	            $datos['{{menssage}}'] = "El proyecto ".$permiso['proyecto_nombre']." tiene ".$permiso['recordatorios'];

	            if( $permiso['recordatorios'] > 1 ){
		            $datos['{{menssage}}'] .= ' permisos expirados';
	            }else{
		            $datos['{{menssage}}'] .= ' permiso expirado';
	            }

	            $datos['{{permisos}}'] = $this->getPermisos($permiso['cliente'], $permiso['proyecto']);

//	            echo '<pre>'; print_r($datos); echo '</pre>';

                $this->Notificar($datos,"permisos");
            }

            return true;
        }else{
	        echo 'error';
        }

        return false;
    }

    /**
     * OBTIENE TODOS LOS PERMISOS CON NOTIFICACIONES Y SUS DATOS
     * @return array $permisos datos de los permisos
     */
    public function getNotificacionesPermisos(){
        $base = new Database();

	    $fecha = date("Y-m-d",time());

        //selecciona los permisos con recordatorios
        $query = "SELECT
          permisos.*,
          MIN( permisos_recordatorios.fecha_inicio ) AS desde,
          COUNT( DISTINCT permisos_recordatorios.id ) As recordatorios,
          proyectos.nombre as proyecto_nombre,
          proyectos.imagen as proyecto_imagen,
          clientes.nombre as cliente_nombre,
          clientes.email as cliente_email,
          clientes.imagen as cliente_imagen
        FROM
          permisos,
          permisos_recordatorios,
          proyectos,
          clientes
        WHERE
          permisos_recordatorios.permiso = permisos.id AND
          permisos_recordatorios.fecha_inicio <= '2013-07-10' AND
          proyectos.id = permisos.proyecto AND
          clientes.id = permisos.cliente
        GROUP BY permisos.proyecto";

        if( $permisos = $base->Select($query) ){

            foreach($permisos as $f => $permiso ){

                //obtiene los emails para las notifciaciones
                $query = "SELECT
                            permisos_recordatorios_emails.*
                          FROM
                              permisos,
                              permisos_recordatorios_emails
                          WHERE
                              permisos.proyecto = '".$permiso['proyecto']."' AND
                              permisos.cliente = '".$permiso['cliente']."' AND
                              permisos_recordatorios_emails.permiso = permisos.id";

                if( $datos = $base->Select($query) ){
                    $permisos[$f]['emails'] = $datos;
                }

            }

            return $permisos;
        }
		echo "Error: no se hay permisos";
        return false;
    }

	/**
	 * COMPONE EL MENSAJE DE CADA PERMISO
	 * @param int $cliente id del cliente
	 * @param int $proyecto id del proyecto
	 * @return boolean|string false si falla
	 */
	public function getPermisos($cliente, $proyecto){
		$base = new Database();
		$mensaje = '';

		$query = "SELECT
				  permisos.id,
				  permisos.nombre,
				  permisos.fecha_expiracion,
				  permisos_recordatorios.fecha_inicio
				FROM
				  permisos,
				  permisos_recordatorios
				WHERE
				  permisos.proyecto = '$proyecto' AND
				  permisos.cliente = '$cliente' AND
				  permisos.id = permisos_recordatorios.permiso";

		if( $permisos = $base->Select($query) ){

			foreach($permisos as $key => $permiso ){
				$fecha_expiracion = $this->FormatearFecha($permiso['fecha_expiracion']);
				$fecha_recordatorio = $this->FormatearFecha($permiso['fecha_inicio']);

				$remplazar = array(
					"{{title}}" => $permiso['nombre'],
					"{{fecha_expiracion}}" => $fecha_expiracion,
					"{{fecha_recordatorio}}" => $fecha_recordatorio
				);

				if( $this->Expiro($permiso['fecha_expiracion']) ){
					$mensaje .= $this->templateManager->setData($this->templates['permiso_expirado'], $remplazar);
				}else{
					$mensaje .= $this->templateManager->setData($this->templates['permiso_recordatorio'], $remplazar);
				}

			}

			return $mensaje;
		}
		return false;
	}

    /**
     * ENVIA LA NOTIFICACION
     * @param array $datos
     * @param string $template tema a usar en la notificacion
     */
    public function Notificar($datos, $template = 'default'){

	    if( $templateSrc = $this->templateManager->getTemplate($template) ){
		    $final = $this->templateManager->setData($templateSrc, $datos);

		    echo $final;
		    return true;
	    }

        return false;
    }

	/**
	 * DETERMINA SI UNA FECHA EXPIRO
	 * @param $fecha
	 * @return boolean true si expiro
	 */
	public function Expiro($fecha){
        $fecha = str_replace('/','-',$fecha);

        $ahora = strtotime( date("d-m-Y H:i:00",time()) );
        $fecha = strtotime($fecha);

        if($ahora > $fecha){
            return true;
        }
        return false;
    }

    /**
     * FORMATEA UNA FECHA
     * @param $fecha fecha en formato dd/mm/yyyy
     * @return date fecha formateada yyyy-mm-dd
     */
    public function FormatearFecha($fecha){
        $fecha = str_replace('/','-',$fecha);
        return date( 'Y-m-d', strtotime($fecha) );
    }
}


$notifcaciones = new Notificaciones();

$notifcaciones->Permisos();


