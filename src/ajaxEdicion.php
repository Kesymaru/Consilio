<?php

require_once("class/imageUpload.php");
require_once("class/registros.php");

if(isset($_POST['func'])){
	
	switch ($_POST['func']){

		case 'Padres':
			echo '<hr>Categorias<hr>';
			echo '<div id="categorias">';
			echo '<div class="root" id="Padre0">';

			$registros = new Registros();
			$padres = $registros->getHijos(0);

			if(!empty($padres)){
				$id = 0;
				$nombre = "";
				echo '<ul>';
				foreach ($padres as $f => $c) {
					foreach ($padres[$f] as $campo => $valor) {
						
						if($campo == 'id'){
							$id = $valor;
						}
						if($campo == 'nombre'){
							$nombre = $valor;
						}else{
							continue;
						}
					}
					echo '<li id="'.$id.'" onClick="Hijos('.$id.')">'.$nombre.'</li>';
				}
				echo '</ul>';
			}else{
				echo 'No hay datos.';
			}
			echo '</div>';
			echo '</div>';
			break;

		//OBTIENE LOS HIJOS DE UN PADRE SELECCIONADO
		case 'Hijos':
			if(isset($_POST['padre'])){
				$registros = new Registros();
				$hijos = $registros->getHijos($_POST['padre']);

				if(!empty($hijos)){
					echo '<div class="categoria" id="Padre'.$_POST['padre'].'">';

					$id = 0;
					$nombre = "";

					echo '<ul>';
					foreach ($hijos as $f => $c) {
						foreach ($hijos[$f] as $campo => $valor) {
							
							if($campo == 'id'){
								$id = $valor;
							}
							if($campo == 'nombre'){
								$nombre = $valor;
							}else{
								continue;
							}
						}
						echo '<li id="'.$id.'" onClick="Hijos('.$id.')">'.$nombre.'</li>';
					}

					echo '</ul>';
					echo '</div>';
					echo '<script>Categoria('.$_POST['padre'].');</script>';
				}else{
					echo '<script>Categoria('.$_POST['padre'].');</script>';
				}
				
			}
			break;

		//OBTIENE LOS IDS DE LOS HIJOS DE UN PADRE
		case 'GetHijos':
			if( isset($_POST['padre']) ){
				$registros = new Registros();
				//el id de todos los hijos
				$hijos = $registros->getTodosHijos($_POST['padre']);
				echo json_encode($hijos);
			}
			break;

		//OBTIENE LOS IDS DE LOS HERMANOS DE UN PADRE
		case 'GetHermanos':
			if( isset($_POST['padre']) ){
				$registros = new Registros();
				//el id de todos los hijos
				$hijos = $registros->getTodosHermanos($_POST['padre']);
				echo json_encode($hijos);
			}
			break;

		//OBTIENE EL PADRE DE UN HIJO
		case 'GetPadre':
			if( isset($_POST['hijo']) ){
				$registros = new Registros();
				$padre = $registros->getPadre($_POST['hijo']);
				echo $padre;
			}
			break;

		//CARGA LOS DATOS DE UNA CATEGORIA
		case 'GetCategoria':
			if( isset($_POST['categoria']) ){
				echo EditarCategoria( $_POST['categoria'] );
			}
			break;

		//CARGA LAS GENERALIDADES COMO OPCIONES
		case 'Opciones':
			echo Opciones();
			break;

		//ACTUALIZA DATOS, REGISTRA DATOS Y/O SUBE ARCHIVO
		case 'RegistrarCategorias':
			if( isset($_POST['categoria']) ){
				RegistrarCategorias( $_POST['categoria'] );
			}
			break;

		//ELIMINA UN DATO
		case 'EliminarDato':
			if( isset($_POST['dato']) ){
				$registro = new Registros();
				$registro->DeleteDato($_POST['dato']);
			}
			break;

		//COMPONE UN NUEVO BOX CON EL ID DEL CAMPO
		case 'Box':
			if( isset($_POST['campo']) ){
				echo Box($_POST['campo']);
			}
			break;

		case 'DeleteArchivo':
			if( isset($_POST['archivo']) ){
				$registro = new Registros();
				$registro->DeleteArchivo($_POST['archivo']);
			}
			break;

		//BOX PRA NUEVA SUBCATEGORIA
		case 'BoxNuevaCategoria':
			if( isset($_POST['padre'])){
				echo BoxNuevaCategoria( $_POST['padre'] );				
			}
			break;
		
		//CRE UNA NUEVA SUBCATEGORIA
		case 'NuevaSubCategoria':
			if( isset($_POST['padre']) && isset($_POST['nombre']) ){
				$registro = new Registros();

				//crea la nueva subcategoria
				$registro->NuevaSubCategoria($_POST['padre'], $_POST['nombre']);
			}
			break;
	}
}

/**
* MUESTRA FORMULARIO DE EDICION CATEGORIA
*/
function EditarCategoria($categoria){
	$formulario = '';
	$registro = new Registros();
	$datos = $registro->getCategoria($categoria);

	//DATOS
	$formulario .= '<form id="FormularioEdicionCategoria" enctype="multipart/form-data" method="post" action="src/ajaxEdicion.php" >
							<div id="nivel1">
							<div id="nombreNorma">
								<input name="nombre" value="'.$datos[0]['nombre'].'" />
							</div>
							<div id="opciones">
								<script type="text/javascript">
									Opciones();
								</script>
							</div>
							</div><!-- end nivel 1-->
							<input type="hidden" value="RegistrarCategorias" name="func" />
							<input type="hidden" value="'.$categoria.'" name="categoria" />
							<div id="nivel2">
							<div id="BoxArchivo" >
								<input type="text" name="archivoNombre" id="archivoNombre" />
								<input type="file" name="archivo" id="archivo" />
								<input type="submit" value="Ajuntar" />
							</div>';

	//ARCHIVOS ADJUNTOS
	$archivos = $registro->getArchivos($categoria);

	if(!empty($archivos)){
		$formulario .= '<div class="box" id="archivosAdjuntos">
								<div class="titulo">
									Archivos Adjuntos
									
								</div>
								<div class="content">
									<div class="archivos" >';

		foreach ($archivos as $fila => $archivo) {
			$formulario .= '<div class="archivo" id="archivo'.$archivo['id'].'" >
							<img class="closeArchivo" src="images/close.png" onClick="BorrarArchivo('.$archivo['id'].')" />
							<a target="_blank" href="src/download.php?link='.$archivo['link'].'"> 
								<img src="images/folder.png" />
								<span>'.$archivo['nombre'].'</span>
							</a>
							
							</div>
							</div>';
		}

		$formulario .= '</div>
						</div>
						</div>';
	}

	//SI HAY DATOS REGISTRADOS PARA LA CATEGORIA
	
	if( is_array($datos) && !empty($datos) ){	

		$registros = $registro->getDatos($datos[0]['id']);

		if( is_array($registros) && !empty($registros) ){

			//compone la edicion de los datos
			foreach ( $registros as $fila => $norma ) {
				$idBox = 0;

				$formulario .= '<div class="box" id="box'.$norma['campo'].'">
									<div class="titulo">
										'.$registro->getCampoDato("nombre", $norma['campo']).'
										<img class="close" src="images/close.png" onClick="BorrarBox('.$norma['campo'].','.$norma['id'].')" />
									</div>
									<div class="content">
									<textarea data-prompt-position="topLeft" class="validate[required]" name="dato'.$norma['campo'].'">'.$norma['contenido'].'</textarea>
									</div>
								</div>';
			}
		}
	}

	//cierre 
	$formulario .= '</div>
					<!-- end nivel 2-->
					<input type="reset" value="borrar" /><input type="submit" value="Guardar" />
					</form>
					<script type="text/javascript">
						FormularioEdicionCategoria();
					</script>';

	return $formulario;
}

/**
* COMPONE UN NUEVO BOX PARA UN CAMPO SELECCIONADO
* @param $id -> id del campo seleccionado
* @return $box -> Box compuesto para el campo
* @return false si falla o no existe el campo
*/
function Box($id){
	$box = '';
	$registro = new Registros();
	
	//se obtienen los datos del campo
	if($datos = $registro->getCampoDatos($id)){

		foreach ($datos as $fila => $campo) {
			$box .= '<div class="box" id="box'.$id.'">
						<div class="titulo">
							'.$registro->getCampoDato("nombre", $id).'
										<img class="close" src="images/close.png" onClick="BorrarBoxTemp('.$id.')" />
						</div>
						<div class="content">
							<textarea data-prompt-position="topLeft" class="validate[required]" name="dato'.$id.'"></textarea>
							</div>
						</div>';
		}
		return $box;
	}else{
		return false;
	}
}

/**
* OBTIENE LOS DATOS ENVIADOS
* @param $categoria -> id de la categoria ha registrar
* @return true -> si actualiza bien
*/
function RegistrarCategorias($categoria){

	$registro = new Registros();

	$datosRegistrados = $registro->getDatos($categoria);

	if(!empty($datosRegistrados)){
		foreach ($datosRegistrados as $fila => $norma) {
			
			if( isset($_POST["dato".$norma['campo']]) ){
				
				/*echo 'DATO: '.$_POST["dato".$norma['campo']]."<br/>";
				echo 'RegistrarDato( '.$_POST["dato".$norma['campo']].' , '.$norma['campo'].', '.$categoria.' )<br/>';
				echo $nuevo = $_POST["dato".$norma['campo']];*/

				//se encarga del registro o actualizacion
				if($registro->RegistrarDato( $nuevo, $norma['campo'], $categoria )){
				}
			}
		}
		
	}

	$campos = $registro->getCampos();

	if(!empty($campos)){
		foreach ($campos as $fila => $campo) {
			
			if( isset($_POST["dato".$campo['id']]) ){
				//se encarga del registro o actualizacion
				if($registro->RegistrarDato( $_POST["dato".$campo['id']], $campo['id'], $categoria )){
					
				}
			}
		}
		
	}

	if(isset($_POST['nombre'])){
		echo $_POST['nombre'];
		//actualiza nombre de la categoria
		$registro->UpdateCategoria($_POST['nombre'], $categoria);
	}

	//sube archivo
	NuevoArchivo($categoria);
}

/**
* COMPONE LAS GENERALIDADES
*/
function Opciones(){
	$opciones = '';
	$registro = new Registros();
	
	$campos = $registro->getCampos();

	if(!empty($campos)){
		foreach ($campos as $fila => $campo) {
			$opciones .= '<input type="radio" id="opcion'.$campo['id'].'" name="opcion'.$campo['id'].'"/>
						<label for="opcion'.$campo['id'].'" onClick="CargarBox('.$campo['id'].')">'.$campo['nombre'].'</label>';
		}
		//para archivos adjuntos
		$opciones .= '<img src="images/pdf.png" onClick="BoxArchivo();" id="botonAdjuntar" title="Adjuntar archivo" alt="Adjuntar archivo" />';
		return $opciones;
	}else{
		return false;
	}
}

/**
* GUARDA DATOS DEL NUEVO ARCHIVO Y SUBE EL ARCHIVOS A /archivos
*/
function NuevoArchivo($categoria){
	$registro = new Registros();

	//SI ENVIA UN ARCHIVO
	if( isset($_FILES['archivo']['tmp_name']) && isset($_POST['archivoNombre']) ){
		
		//SUBE EL ARCHIVO Y GUARDA LOS DATOS
		$registro->NuevoArchivo( $_FILES['archivo'] , $_POST['archivoNombre'], $categoria);
	}
}

/**
* BOX PARA NUEVA SUBCATEGORIA
*/
function BoxNuevaCategoria($padre){
	$box = '';
	$box .= '<form id="FormularioSubCategoria" enctype="multipart/form-data" method="post" action="src/ajaxEdicion.php" >
			<input type="hidden" name="padre" value="'.$padre.'"/>
			<input type="hidden" name="func" value="NuevaSubCategoria" />
			<input type="text" data-prompt-position="topLeft" class="validate[required]" name="nombre" placeholder="Nombre" />
			<br/><br/>
			<input type="reset" value="Borrar" />
			<input type="submit" value="Guardar" />
			</form>';

	return $box;
}
						
?>