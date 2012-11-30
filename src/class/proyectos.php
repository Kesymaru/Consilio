<?php
/**
* CLASE PARA EL MANEJO DE DATOS DE LOS PROYECTOS
*/
require_once("classDatabase.php");
require_once("usuarios.php"); 

class Proyectos{
	/**
	*	LISTA DE PRPYECTOS
	*/
	public function Lista(){
		$base = new Database();
		$datos = $base->Select("SELECT * FROM proyectos");

		$cliente = new Cliente();

		$resultado = '<table class="tablaVista" id="TablaProyectos">';
		
		if(!empty($datos)){

			$resultado .= '<thead><tr class="titulo" >';
			
			foreach ($datos[0] as $cabezera => $valor) {
				if($cabezera == 'id'){
					continue;
				}
				$resultado .= '<th>'.$cabezera.'</th>';
			}
			$resultado .= '</tr></thead>';

			foreach ($datos as $fila => $c) {
				
				$resultado .= '<tr onClick="SeleccionFila('.$datos[$fila]['id'].')" id="'.$datos[$fila]['id'].'">';

				foreach ($datos[$fila] as $campo => $x) {
					
					if($campo == 'status'){
						if($datos[$fila][$campo] == 1){
							$resultado .= '<td>Activo</td>';
						}else{
							$resultado .= '<td>Finalizado</td>';
						}
						continue;
					}

					if($campo == 'id'){
						continue;
					}

					if($campo == 'descripcion'){
						$resultado .= '<td class="td40">'.$datos[$fila][$campo].'</td>';
						continue;
					}

					if($campo == 'cliente'){
						$resultado .= '<td>';
						$resultado .= $cliente->getClienteDato("nombre", $datos[$fila][$campo]);
						$resultado .= '</td>';
						continue;
					}

					$resultado .= '<td>'.$datos[$fila][$campo].'</td>';
				}

				$resultado .= '</tr><script type="text/javascript"> Tabla("TablaProyectos");</script>';

			}

			return $resultado;
		}else{
			return "No hay proyectos.";
		}

	}

	/**
	* VISTA DE EDICION DE UN PROYECTO
	* @param $id -> id del proyecto ha ser editado
	*/
	public function EditarProyecto($id){
		$cliente = new Cliente();
		$base = new Database();
		$query = "SELECT nombre, cliente, imagen, descripcion FROM proyectos WHERE id = '".$id."'";
		$datos = $base->Select($query);

		if(!empty($datos)){

			/*$formulario = '<form id="formularioEditarProyecto" enctype="multipart/form-data" method="post" action="src/class/imageUpload.php">
					      <table class="tablaForm">
					      	<thead><tr class="titulo" >
					      		<th colspan="3">Edición Proyecto</th>
					      	</tr></thead>';*/

			//formulario y tabla
			$formulario = '<form id="formularioEditarProyecto" enctype="multipart/form-data" method="post" action="src/ajaxProyectos.php">
					      <table class="tablaForm">
					      	<thead>
					      		<tr class="titulo" >
					      			<th colspan="3">
					      				Edición Proyecto
					      			</th>
					      		</tr>
					      	</thead><!-- fin cabecera -->';

			$formulario .= '<tr>
								<td rowspan="4" class="tdImagen" >
									<img height="200px" src="'.$datos[0]['imagen'].'">
								<br/><br/>';

			//para subir imagen
			$formulario .= '		<input type="file" name="ProyectoImagen" id="ProyectoImagen" />
							   		<input type="hidden" name="ProyectoId" value="'.$id.'" id="ProyectoId" />
							   </td>
						   </tr><!-- fin columna imagen -->';
			//nombre
			$formulario .= '<tr>
								<td class="campo borderAlto"><p>Nombre</p>
									<input class="validate[required]" type="text" id="ProyectoNombre" name="ProyectoNombre" value="'.$datos[0]['nombre'].'" />
							    </td>
							</tr><!-- fin nombre -->';

			//cliente
			$formulario .= '<tr>
								<td class="campo" ><p>Cliente</p>';
			//lista de clientes
			$formulario .= $this->getListaClientes($datos[0]['cliente']);
			$formulario .= '	</td>
							</tr>';

			//descripcion
			$formulario .= '<tr>
								<td class="campo campoTexto borderBajo"><p>Descripcion</p>';
			$formulario .= '		<textarea class="validate[optional,maxSize[800]]" id="ProyectoDescripcion" name="ProyectoDescripcion" >'.$datos[0]['descripcion'].'</textarea>
								</td>
							</tr><!-- fin descripcion -->';

			//controles
			$formulario .= '<tr>
								<td colspan="3" class="tdControls">
									<input type="reset" value="Borrar" />
									<!-- <button onClick="GuardarEdicionProyecto()">Guardar</button> -->
									<input type="submit" value="Guardar" />
								</td>
						    </tr>';
			$formulario .= '</table>
						</form>';

			$formulario .= '<script type="text/javascript">
								//INICIALIZA EL FORMULARIO
								FormularioEditarProyecto();
							</script>';				

			return $formulario;

		}else{
			return "El proyecto seleccionado no existe.";
		}
	}

	/**
	* VISTA PARA CREAR UN NUEVO PROYECTO
	* @return $formulario -> formulario 
	*/
	public function EditarNuevoProyecto(){

			//formulario y tabla
			$formulario = '<form id="formularioEditarNuevoProyecto" enctype="multipart/form-data" method="post" action="src/ajaxProyectos.php">
					      <table class="tablaForm">
					      	<thead>
					      		<tr class="titulo" >
					      			<th colspan="3">
					      				Nuevo Proyecto
					      			</th>
					      		</tr>
					      	</thead><!-- fin cabecera -->';

			$formulario .= '<tr>
								<td rowspan="4" class="tdImagen" >
									<img height="200px" src="images/es.png">
								<br/><br/>';

			//para subir imagen
			$formulario .= '		<input type="file" name="ProyectoNuevoImagen" id="ProyectoNuevoImagen" />
							   </td>
						   </tr><!-- fin columna imagen nuevo proyecto -->';
			//nombre
			$formulario .= '<tr>
								<td class="campo borderAlto"><p>Nombre</p>
									<input class="validate[required]" type="text" id="ProyectoNuevoNombre" name="ProyectoNuevoNombre" value="" />
							    </td>
							</tr><!-- fin nombre -->';

			//cliente
			$formulario .= '<tr>
								<td class="campo" ><p>Cliente</p>';
			//lista de clientes
			$formulario .= $this->getListaClientes();
			$formulario .= '	</td>
							</tr>';

			//descripcion
			$formulario .= '<tr>
								<td class="campo campoTexto borderBajo"><p>Descripcion</p>';
			$formulario .= '		<textarea class="validate[optional,maxSize[800]]" id="ProyectoNuevoDescripcion" name="ProyectoNuevoDescripcion" ></textarea>
								</td>
							</tr><!-- fin descripcion -->';

			//controles
			$formulario .= '<tr>
								<td colspan="3" class="tdControls">
									<input type="reset" value="Borrar" />
									<input type="submit" value="Guardar" />
								</td>
						    </tr>';
			$formulario .= '</table>
						</form>';

			$formulario .= '<script type="text/javascript">
								//INICIALIZA EL FORMULARIO
								FormularioEditarNuevoProyecto();
							</script>';				

			return $formulario;
	}

	/**
	* METODO PARA GENERAR UN SELECT CON LAS LISTA DE CLIENTES
	* @param $cliente -> cliente por defecto o seleccionado
	* @return $lista -> el select
	*/
	private function getListaClientes($cliente = 1){
		$base = new Database();
		$datos = $base->Select("SELECT nombre, id FROM clientes");

		$lista = '<select class="validate[required]" id="ProyectoCliente" name="ProyectoCliente">';
		if(!empty($datos)){
			foreach ($datos as $fila => $v) {
				$lista .= '<option id="'.$datos[$fila]['id'].'" value="'.$datos[$fila]['id'].'" ';

				$lista .= ( $cliente == $datos[$fila]['id'] ) ? 'selected="selected" >' : '>';

				$lista .= $datos[$fila]['nombre'].'</option>';
			}
			$lista .= '</select>';

			return $lista;
		}else{
			return "No hay Clientes";
		}
	}

	/**
	* GUARDA UN NUEVO PROYECTO
	* @param $nombre -> nombre del proyecto
	* @param $descripcion -> descripcion del proyecto
	* @param $imagen -> logo adjuntado al proyecto
	*/
	function NuevoProyecto($nombre, $descripcion, $imagen){
		$base = new Database();
		$descripcion =mysql_real_escape_string($descripcion);
		$query = "INSERT INTO proyectos (nombre, decripcion, imagen) VALUES ('".$nombre."', '".$descripcion."', '".$imagen."')";
		$base->Insert($query);
	}

	/**
	* ELIMINA UN PROYECTO
	* @param $id -> id del proyecto ha ser eliminado
	*/
	public function EliminarProyecto($id){
		$base = new Database();
		$query = "DELETE FROM proyectos WHERE id = ".$id;
		
		if($base->Delete($query)){
			return true;
		}else{
			return false;
		}
	}

/** SETTERS Y GETTERS **/

	/**
	* ACTUALIZA LA IMAGEN DEL PROYECTO
	* @param $imagn -> file de la imagen ha subir
	* @param $id -> id del proyecto
	* @return true -> si la operacion se realizo exitosamente
	* @return false -> si ocurrio un error o fallo
	*/
	public function setProyectoImagen($imagen, $id){
        $upload = new Upload();
        
        $upload->SetFileName($imagen['name']);
        $upload->SetTempName($imagen['tmp_name']);

        $upload->SetValidExtensions(array('gif', 'jpg', 'jpeg', 'png')); 
        
        $upload->SetUploadDirectory("../images/proyectos/"); //Directorio para imagenes de los proyectos

        $upload->SetMaximumFileSize(90000000); //tamano maximo permitido
        
        if($upload->UploadFile()){
        	//link donde se subio la imagen
            $link = $upload->GetUploadDirectory().$upload->GetFileName();
            
            //actualiza la base de datos del proyecto
            if($this->updateProyectoImagen($link, $id)){
            	return true;
            }else{
            	return false;
            }
        }else{
        	return false;
        }
	}

	/**
	* ACTUALIZA LA BASE DE DATOS CON LA NUEVA IMAGEN
	* @param $link -> link de la imagen
	* @apram $id -> id del proyecto
	*/
	private function updateProyectoImagen($link, $id){
		$base = new Database();
		$link = str_replace("../", "", $link);

		//link viejo que sera actualizado
		$imagenOld = $this->getProyectoDato('imagen', $id);
		$imagenOld = "../".$imagenOld;

		$query = "UPDATE proyectos SET imagen = '".$link."' WHERE id = ".$id;
		
		//actualiza imagen
		if($base->Update($query)){
			//borra la imagen vieja del directorio
			if($base->DeleteImagen($imagenOld)){
				$base->Update($query);
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	* METODO PARA OBTENER UN DATO DE UN PROYECTO
	* @param $dato -> dato requerido
	* @param $proyecto -> id proyecto
	*/
	public function getProyectoDato($dato, $proyecto){
		$base = new Database();
		$query = "SELECT ".$dato." FROM proyectos WHERE id = '".$proyecto."'";

		if($base->Existe($query)){
			 $datos = $base->Select($query);

			 if(!empty($datos)){
			 	return $datos[0][$dato];
			 }
		}else{
			//no existe el dato consultado
			return false;
		}
	}

	/**
	* METODO PARA ACTUALIZAR UN DATO DE UN PROYECTO
	* @param $dato -> dato ha ser actualizado
	* @param $valor -> nuevo valor para el dato
	* @param $id -> id del proyecto
	* @return true si se realiza exitosamente
	*/
	public function setProyectoDato($dato, $valor, $id){
		$base = new Database();
		$query = "UPDATE proyectos SET ".$dato." = '".$valor."' WHERE id = ".$id;

		if($base->Update($query)){
			return true;
		}else{
			return false;
		}
	}
}

?>