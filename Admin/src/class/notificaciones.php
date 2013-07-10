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

	private $home = "";
	private $admin = "";

	private $fecha = "";
	private $base = "";

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
	    $this->mail = new Email();
		$this->base = new Database();

	    $this->fecha = date("Y-m-d",time());
    }

    /**
     *NOTIFICACIONES PARA LOS PERMISOS
     */
    public function Permisos(){

        if( $proyectos = $this->getPermisos() ){
//            echo '<pre>'; print_r($proyectos); echo '</pre>';

            foreach($proyectos as $index => $proyecto ){

                //datos para la notificacion
                $remplazar = array(
                    "{{title}}" => $proyecto['proyecto_nombre'],
                    "{{cliente_nombre}}" => $proyecto['cliente_nombre'],
                    "{{cliente_imagen}}" => $this->admin.$proyecto['cliente_imagen'],
	                "{{menssage}}" => "El proyecto ".$proyecto['proyecto_nombre'],
	                "{{permisos}}" => "",
                );

	            //notificaciones de expirados
				foreach( $proyecto['expirados'] as $f => $expirado ){
					$remplazar_expirados = $remplazar;

					if( $mensaje = $this->ComponerPermiso($expirado, "expirado") ){
						$remplazar_expirados["{{permisos}}"] = $mensaje;
						$remplazar["{{permisos}}"] .= $mensaje;

						$remplazar_expirados["{{to}}"] = "";
						foreach( $expirado['emails'] as $fila => $email ){
							$remplazar_expirados["{{to}}"] .= $email['email'].",";
						}

						$remplazar_expirados["{{menssage}}"] .= " tiene el siguiente permiso expirado.";
						$remplazar_expirados["{{subject}}"] = "Permiso Expirado: ".$expirado['nombre'];

						$this->Notificar($remplazar_expirados, "permisos");
					}
				}

	            //notificaciones de recordatorios
	            foreach( $proyecto["recordatorios"] as $f => $recordatorio ){
					$remplazar_recordatorios = $remplazar;

		            if( $mensaje = $this->ComponerPermiso($recordatorio, "recordatorio") ){
						$remplazar_recordatorios["{{permisos}}"] = $mensaje;
			            $remplazar["{{permisos}}"] .= $mensaje;

			            $remplazar_recordatorios["{{to}}"] = "";
			            foreach( $recordatorio['emails'] as $fila => $email ){
				            $remplazar_recordatorios["{{to}}"] .= $email['email'].",";
			            }

			            $remplazar_recordatorios["{{menssage}}"] .= " tiene el siguiente recordatorio sobre un permiso.";
			            $remplazar_recordatorios["{{subject}}"] = "Recordatorio para: ".$recordatorio['nombre'];

			            $this->Notificar($remplazar_recordatorios, "permisos");
		            }
	            }

	            //notificacion del proyecto
	            $total_expirados = sizeof( $proyecto['expirados'] );
	            $total_recordatorios = sizeof( $proyecto['recordatorios'] );

	            if( 1 <= $total_expirados || 1 <= $total_recordatorios ){

		            $remplazar["{{menssage}}"] .= " tiene ";
		            if( 0 < $total_expirados && $total_expirados <= 1 ){
			            $remplazar["{{menssage}}"] .= $total_expirados." permiso expirado";
		            }else if( 0<$total_expirados ){
			            $remplazar["{{menssage}}"] .= $total_expirados." permisos expirados";
		            }

		            if( 0 < $total_recordatorios && $total_recordatorios <= 1 ){
			            $remplazar["{{menssage}}"] .= " y ".$total_recordatorios." recordatorio.";
		            }else if(0<$total_recordatorios){
			            $remplazar["{{menssage}}"] .= " y ".$total_recordatorios." recordatorios";
		            }

		            $remplazar["{{menssage}}"] .= ".";

		            $remplazar["{{subject}}"] = "Permisos ".$proyecto['proyecto_nombre'];
					$remplazar["{{to}}"] = $proyecto['cliente_email'];

		            $this->Notificar($remplazar, "permisos");
	            }

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
    public function getPermisos(){

	    //datos de proyectos y su cliente, proyectos con permisos
	    $query = "SELECT
				  DISTINCT proyectos.id AS proyecto,
				  proyectos.nombre AS proyecto_nombre,

				  clientes.id as cliente,
				  clientes.nombre AS cliente_nombre,
				  clientes.imagen AS cliente_imagen,
				  clientes.email AS cliente_email
				FROM
				  clientes,
				  proyectos
				WHERE
				  proyectos.permisos = 1 AND
				  proyectos.visible = 1 AND
				  proyectos.cliente = clientes.id";

	    if( $datos_proyectos = $this->base->Select($query) ){

			foreach($datos_proyectos as $key => $proyecto ){

				//obtiene los permisos de cada proyecto
				$query = "SELECT
						  permisos.*,
						  permisos_recordatorios.fecha_inicio
						FROM
						  permisos,
						  permisos_recordatorios
						WHERE
						  permisos.proyecto = '".$proyecto['proyecto']."' AND
						  permisos_recordatorios.permiso = permisos.id";

				if( $datos_permisos = $this->base->Select($query) ){
					$expirados = array();
					$recordatorios = array();

					foreach( $datos_permisos as $f => $permiso ){
						$resultado = $permiso;

						//permiso expirado
						if( $this->Expiro($permiso['fecha_expiracion']) ){
							$resultado['emails'] = $this->getPermisoEmails($permiso['id']);
							$expirados[] = $resultado;
							continue;
						}else if( $this->Expiro($permiso['fecha_inicio']) ){
							$resultado['emails'] = $this->getPermisoEmails($permiso['id']);
							$recordatorios[] = $resultado;
						}
					}

					$datos_proyectos[$key]['expirados'] = $expirados;
					$datos_proyectos[$key]['recordatorios'] = $recordatorios;
				}

			}

		    return $datos_proyectos;
	    }

	    return false;
    }

	/**
	 * OBTIENE LOS EMAILS PARA UN PERMISO
	 * @param $permiso id del permiso
	 * @return array
	 */
	private function getPermisoEmails($permiso){
		$emails = array();

		$query = "SELECT
				  email
				FROM
				  permisos_recordatorios_emails
				WHERE
				  permiso = '$permiso'";

		if($emails = $this->base->Select($query) ){
		}
		return $emails;
	}

	/**
	 * COMPONE EL MENSAJE DE CADA PERMISO
	 * @param array $datos datos del permiso
	 * @return boolean|string false si falla
	 */
	private function ComponerPermiso($datos, $tipo){
		$mensaje = '';

		if( !empty($datos) ){

			$fecha_emision = $this->FormatearFecha($datos['fecha_emision']);
			$fecha_expiracion = $this->FormatearFecha($datos['fecha_expiracion']);
			$fecha_recordatorio = $this->FormatearFecha($datos['fecha_inicio']);

			$remplazar = array(
				"{{title}}" => $datos['nombre'],
				"{{fecha_emision}}" => $fecha_emision,
				"{{fecha_expiracion}}" => $fecha_expiracion,
				"{{fecha_recordatorio}}" => $fecha_recordatorio,
				"{{observacion}}" => $datos['observacion']
			);

			$tema = "default";
			if( $tipo == 'expirado' ){
				$tema = "permiso_expirado";
			}
			if( $tipo == 'recordatorio' ){
				$tema = "permiso_recordatorio";
			}

			$mensaje .= $this->templateManager->getTemplateData($tema, $remplazar);

			return $mensaje;
		}
		return false;
	}

	/*************************/
    /**
     * ENVIA LA NOTIFICACION
     * @param array $datos
     * @param string $template tema a usar en la notificacion
     */
    public function Notificar($datos, $template = 'default'){

	    if( $templateSrc = $this->templateManager->getTemplate($template) ){
		    $final = $this->templateManager->setData($templateSrc, $datos);

		    echo $datos["{{menssage}}"] = $final;

		    //envia el email
		    if( $this->mail->Notificar($datos) ){
			    return true;
		    }else{
			    echo "no se pudo enviar el mail";
		    }
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
        return date( 'd-m-Y', strtotime($fecha) );
    }
}


$notifcaciones = new Notificaciones();

$notifcaciones->Permisos();

