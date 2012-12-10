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

		//CARGA LOS DATOS DE UNA CATEGORIA EN UN FORMULARIO PARA LA EDICION
		case 'GetCategoria':
			if( isset($_POST['categoria']) ){
				echo EditarCategoria( $_POST['categoria'] );
			}
			break;

		//CARGA LAS GENERALIDADES COMO OPCIONES
		/*case 'Opciones':
			echo Opciones();
			break;*/

		//ACTUALIZA DATOS, REGISTRA DATOS Y/O SUBE ARCHIVO
		case 'RegistrarCategorias':
			if( isset($_POST['categoria']) ){
				RegistrarCategorias( $_POST['categoria'] );
			}
			break;

		case 'DeleteArchivo':
			if( isset($_POST['archivo']) ){
				$registro = new Registros();
				$registro->DeleteArchivo($_POST['archivo']);
			}
			break;

		// CARGA EL BOX PARA EDITAR NUEVA SUBCATEGORIA
		case 'BoxNuevaCategoria':
			if( isset($_POST['padre'])){
				echo BoxNuevaCategoria( $_POST['padre'] );				
			}
			break;
		
		//GUARDA UNA NUEVA SUBCATEGORIA
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
* CREA EL FORMULARIO DE UNA CATEGORIA
* @param $categoria -> id de la categoria 
*/
function EditarCategoria($categoria){
	$formulario = '';
	$registro = new Registros();

	//OPTIENE DATOS DE LA CATEGORIA
	$datos = $registro->getDatos($categoria);
	$nombre = $registro->getCategoriaDato("nombre", $categoria);

	//FORMULARIO DE DATOS
	$formulario .= '<form id="FormularioEdicionCategoria" enctype="multipart/form-data" method="post" action="src/ajaxEdicion.php" >
							<div id="nivel1">
							<div id="nombreNorma">
							<!-- nombre de la categoria -->
								<input name="nombre" class="validate[required]" value="'.$nombre.'" />
							</div>
							<!-- datos fijos escondidos -->
							<input type="hidden" value="RegistrarCategorias" id="func" name="func" />
							<input type="hidden" value="'.$categoria.'" id="categoria" name="categoria" />
							<div class="datos">';

	
	//SI HAY DATOS REGISTRADOS PARA LA CATEGORIA
	if( is_array($datos) && !empty($datos) ){	

		foreach ($datos as $fila => $dato) {
			$formulario .= '<textarea id="contenido" name="contenido" cols="80"  rows="10" >';
			$formulario .= base64_decode($dato['contenido']);
			$formulario .= '</textarea>';
		}

	}else{
		$formulario .= '<textarea id="contenido" name="contenido" cols="80"  rows="10" ></textarea>';
	}

	$formulario .= '</div>
					</div>
					<!-- end nivel 1-->

					<div id="nivel2">
						<div id="BoxArchivo" >
							<input type="text" name="archivoNombre" id="archivoNombre" placeholder="Nombre" />
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

							<!-- descarga archivo -->
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

	//CIERRE FORMULARIO 
	$formulario .= '</div>
					<!-- end nivel 2-->

					<input type="reset" value="borrar" />

					<!-- se requiere la funcion EditorUpdateContent para actualizar cambios desde el editor antes de enviar -->
					<input type="submit" value="Guardar" onClick="EditorUpdateContent()" />
					</form>

					<!-- carga el editor y el formulario -->
					<script type="text/javascript">
						FormularioEdicionCategoria();
						Editor("contenido");
					</script>';

	return $formulario;
}

/**
* OBTIENE LOS DATOS ENVIADOS Y LOS GUARDA
* @param $categoria -> id de la categoria ha registrar
* @return true -> si actualiza bien
*/
function RegistrarCategorias($categoria){

	if( isset($_POST['contenido']) ){
		$registro = new Registros();
		$registro->setDato($_POST['contenido'], $categoria);
	}

	if(isset($_POST['nombre'])){
		//actualiza nombre de la categoria
		$registro->UpdateCategoria($_POST['nombre'], $categoria);
	}

	//sube archivo
	NuevoArchivo($categoria);
}

/**
* GUARDA DATOS DEL NUEVO ARCHIVO Y SUBE EL ARCHIVOS A /archivos
*/
function NuevoArchivo($categoria){
	$registro = new Registros();

	//SI ENVIA UN ARCHIVO
	if( isset($_FILES['archivo']['tmp_name']) && isset($_POST['archivoNombre']) ){
		
		if( !$_FILES['archivo']['tmp_name'] == '' && !empty($_FILES['archivo']['tmp_name']) ){
			//SUBE EL ARCHIVO Y GUARDA LOS DATOS
			$registro->NuevoArchivo( $_FILES['archivo'] , $_POST['archivoNombre'], $categoria);
		}else{
			//echo 'archivo vacio';
		}
		
	}
}

/**
* BOX PARA NUEVA SUBCATEGORIA
* @param $padre -> id del padre al que pertenece
*/
function BoxNuevaCategoria($padre){
	$box = '';
	$box .= '<form id="FormularioSubCategoria" enctype="multipart/form-data" method="post" action="src/ajaxEdicion.php" >
			<input type="hidden" name="padre" value="'.$padre.'"/>
			<input type="hidden" name="func" id="func" value="NuevaSubCategoria" />
			<input type="text" data-prompt-position="topLeft" class="validate[required]" name="nombre" placeholder="Nombre" />
			<br/><br/>
			<input type="reset" value="Borrar" />
			<input type="submit" value="Guardar" />
			</form>';

	return $box;
}
						
?>