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

/**
 * CLASE PARA LAS NOTIFICACIONES
 */
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

	    $dominio = "http://development.77digital.com";

	    $this->home = $dominio.'/matrizescala/';
	    $this->admin = $dominio.'/matrizescala/Admin/';

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
	            /**
	             * ||dato|| para datos que no remplazan
	             * {{dato}} para dato que sera remplazado
	             */
	            $remplazar = array(
	                "||proyecto||" => $proyecto['proyecto'],
	                "||cliente||" => $proyecto['cliente'],
                    "{{title}}" => $proyecto['proyecto_nombre'],
                    "{{cliente_nombre}}" => $proyecto['cliente_nombre'],
                    "{{cliente_imagen}}" => $this->admin.$proyecto['cliente_imagen'],
	                "{{menssage}}" => "El proyecto ".$proyecto['proyecto_nombre'],
	                "{{permisos}}" => "",
	                "{{bcc}}" => array(
		                array(
			                "email" => "pquesada@consultoresescala.com",
			                "name" => "Paola Quesada"
		                ),
		                array(
			                "email"=> "mfesquivel@consultoresescala.com",
			                "name" => "Maria Fernada Esquivel"
		                )
	                ),
                );

	            //notificaciones de expirados
				foreach( $proyecto['expirados'] as $f => $expirado ){
					$remplazar_expirados = $remplazar;

					if( $mensaje = $this->ComponerPermiso($expirado, "expirado") ){
						$remplazar_expirados["{{permisos}}"] = $mensaje;

						$to = array();
						foreach( $expirado['emails'] as $fila => $email ){
							$to[] = $email['email'];
						}
						$remplazar_expirados["{{to}}"] = $to;

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

			            $to = array();
			            foreach( $recordatorio['emails'] as $fila => $email ){
				            $to[] = $email['email'];
			            }
			            $remplazar_recordatorios["{{to}}"] = $to;

			            $remplazar_recordatorios["{{menssage}}"] .= " tiene el siguiente recordatorio sobre un permiso.";
			            $remplazar_recordatorios["{{subject}}"] = "Recordatorio para: ".$recordatorio['nombre'];

			            $this->Notificar($remplazar_recordatorios, "permisos");
		            }
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
		    if( $notificacion = $this->templateManager->setData($templateSrc, $datos) ){
			    $datos["{{body}}"] = $notificacion;

				//debugea envio
			    $datos["{{to}}"] = array(
				    array(
					    "email" => "aalfaro@77digital.com",
					    "name" => "77Digital"
				    ),
/*				    array(
					    "email"=> "andreyalfaro@gmail.com",
					    "name" => "Andrey"
				    )*/
			    );

			    $datos["{{bcc}}"] = array(
				    array(
					    "email"=> "andreyalfaro@gmail.com",
					    "name" => "Andrey"
				    )
			    );

			    //envia el email
			    if( $this->mail->Notificar($datos) ){
					echo 'mail enviado';
			        //registra el envio
				    if( $this->Registrar("permiso", $datos ) ){
						echo 'registrado';
					    return true;
			        }
			    }else{
				    echo "<br/><b>Error:</b> No se envio el mail";
			    }

		    }
	    }

        return false;
    }

	/**
	 * DETERMINA SI UNA FECHA EXPIRO
	 * @param string $date fecha
	 * @return boolean true si expiro
	 */
	public function Expiro($date){
        /*$fecha = str_replace('/','-',$fecha);

        $ahora = strtotime( date("d-m-Y H:i:00",time()) );
        $fecha = strtotime($fecha);

        if($ahora > $fecha){
            return true;
        }
        return false;*/

		if( $date == ""){
			$date = strtotime("now");
		}else{
			$date = strtotime($date);
		}

		$today = date('Y-m-d');
		$start = strtotime($today." 00:00:00");

		$end = strtotime( $today." 23:59:59");

		if($start <= $date && $date <= $end ){
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

	/**
	 * REGISTRA UNA NOTIFICACION
	 * @param string $tipo tipo de notificacion
	 * @param array $data all notification data
	 * @return boolean
	 */
	private function Registrar($tipo = "notificacion", $data){
		$fecha = date('Y-m-d G:i:s',time());

		$proyecto = "";
		if( array_key_exists("||proyecto||",$data) ){
			$proyecto = $data['||proyecto||'];
		}

		$cliente = "";
		if( array_key_exists("||cliente||", $data) ){
			$cliente = $data['||cliente||'];
		}

		$to = "";
		if( is_array($data["{{to}}"]) ){
			foreach( $data["{{to}}"] as $f => $mail ){
				if( is_array($mail) ){
					$to .= "<".$mail['name'].">".$mail['email'];
				}else{
					$to .= $mail.",";
				}
			}
		}

		$bcc = "";
		if( array_key_exists("{{bcc}}", $data) ){
			if( is_array($data["{{bcc}}"]) ){
				foreach( $data["{{bcc}}"] as $f => $mail ){
					if( is_array($mail) ){
						$bcc .= "<".$mail['name'].">".$mail['email'];
					}else{
						$bcc .= $mail.",";
					}
				}
			}
		}

		$mail = "";
		if( array_key_exists("{{body}}", $data) ){
			$mail = base64_encode($data["{{body}}"]);
		}

		$tipo = mysql_real_escape_string($tipo);
		$proyecto = mysql_real_escape_string($proyecto);
		$cliente = mysql_real_escape_string($cliente);
		$to = mysql_real_escape_string($to);
		$bcc = mysql_real_escape_string($bcc);
		$mail = mysql_real_escape_string($mail);

		$query = "INSERT
				  INTO notificaciones
				  (tipo, proyecto, cliente, para, bcc, mail, fecha)
				  VALUES
				  ('$tipo', '$proyecto', '$cliente', '$to', '$bcc', '$mail', '$fecha' )";

		if( $this->base->Insert($query) ){
			echo 'registra';
			return true;
		}
		return false;
	}
}


