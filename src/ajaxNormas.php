<?php

/**
* AJAX PARA NORMAS Y ARTICULOS
*/

require_once("class/imageUpload.php");
require_once("class/registros.php");


if(isset($_POST['func'])){
	
	switch ($_POST['func']){

	/************************ NORMAS *****************/
		//CARGA LISTA DE NORMAS
		case 'Normas':
			Normas();
			break;

		//CARGA FORMULARIO PARA NUEVA NORMA
		case 'NuevaNorma':
			NuevaNorma();
			break;

		//CAREA UNA NUEVA NORMA
		case 'CrearNorma':
			RegistrarNorma();
			break;

		//FORMULARIO DE EDICION DE NORMA
		case 'EditarNorma':
			if( isset($_POST['norma'])){
				EditarNorma($_POST['norma']);
			}
			break;

		//ACTUALIZA NORMA, PROVIENE DEL FORMULARIO
		case 'ActualizarNorma':
			if( isset($_POST['norma']) ){
				ActualizarNorma($_POST['norma']);
			}	
			break;

		//DESHABILITA UNA NORMA
		case 'DeshabilitarNorma':
			if(isset($_POST['norma'])){
				DeshabilitarNorma($_POST['norma']);
			}
			break;

		//HABILITA UNA NORMA
		case 'HabilitarNorma':
			if(isset($_POST['norma'])){
				echo 'normas';
				HabilitarNorma($_POST['norma']);
			}
			break;

	/************************ ARTICULOS *****************/

		//FORMULARIO PARA NUEVO ARTICULO
		case 'NuevoArticulo':
			if( isset($_POST['norma']) ){
				NuevoArticulo( $_POST['norma'] );
			}
			break;

		//LISTA DE ARTICULOS DE UNA NORMA SELECCIONADA
		case 'Articulos':
			if(isset($_POST['norma'])){
				Articulos($_POST['norma']);
			}
			break;

		//REGISTRA UN NUEVO ARTICULO
		case 'CrearArticulo':
			if( isset($_POST['norma']) ){
				RegistrarArticulo($_POST['norma']);
			}
			break;

		//CARGA PLANTILLA DE EDICION DE UN ARTICULOs
		case 'EditarArticulo':
			if(isset($_POST['articulo'])){
				EdicionArticulo($_POST['articulo']);
			}
			break;

		//BORRA UN ARTICULO EXISTENTE
		case 'BorrarArticulo':
			if(isset($_POST['articulo'])){
				BorrarArticulo( $_POST['articulo']);
			}
			break;

		//ACTUALIZA UN ARTICULO, CREANDO UN SNAPSHOT DE LOS DATOS VIEJOS 
		case 'ActualizarArticulo':
			if( isset($_POST['norma']) && isset($_POST['id'])){
				ActualizarArticulo($_POST['norma'], $_POST['id']);
			}
			break;

	/************************ ARCHIVOS *****************/

		case 'EliminarArchivo':
			if( isset($_POST['archivo']) ){
				EliminarArchivo($_POST['archivo']);
			}
			break;

	}
}

/****************************** NORMAS **********************************/

/**
* CARGA LAS NORMAS
*/
function Normas(){
	echo '<div id="normas" class="normas">
		  	<div class="titulo">
				Normas
				<hr>
		  	</div>';
	echo '<div class="root2" id="PadreNormas">';

	$registros = new Registros();
	$normas = $registros->getNormas();

	if(!empty($normas)){
		$id = 0;
		$nombre = "";
		echo '<ul>';

		foreach ($normas as $fila => $norma) {
			
			if($norma['status'] == 1){
				echo '<li id="'.$norma['id'].'" onClick="NormaOpciones('.$norma['id'].')">'.$norma['nombre']." ".$norma['numero'].'</li>';
			}else{
				echo '<li id="'.$norma['id'].'" class="deshabilitado" onClick="NormaOpciones('.$norma['id'].')">'.$norma['nombre']." ".$norma['numero'].'</li>';
			}
			
		}
		echo '</ul>';
	}else{
		echo '--- No hay normas ---';
	}
	echo '</div>
		 <div>
		 <button id="EditarNorma" onClick="EditarNorma()">Editar</button>
		 <button id="DeshabilitarNorma" onClick="DeshabilitarNorma()">Deshabilitar</button>
		 <button id="HabilitarNorma" onClick="HabilitarNorma()">Habilitar</button>
		 <button onClick="NuevaNorma()">Nueva Norma</button>
		 </div>';
	echo '</div>';
}

/**
* PRESENTA EL FORMULARIO DE EDICION DE LA NORMA
* @param $norma -> id de la norma
*/
function EditarNorma($norma){
	$formulario = "";

	$registros = new Registros();
	$datos = $registros->getDatosNorma($norma); //obtien los datos de una norma

	if(!empty($datos)){

		$formulario .= '<div class="titulo">
							Edicion Norma
							<hr>
						</div>
						<form id="FormularioNorma" enctype="multipart/form-data" method="post" action="src/ajaxNormas.php">
							<div class="datos">
								<input type="hidden" value="ActualizarNorma" name="func" />
								<input type="hidden" value="'.$norma.'" id="norma" name="norma" />
								<table>
								<tr>
									<td>Nombre</td>
									<td>
										<input type="text" id="nombre" name="nombre" placeholder="Nombre" class="validate[required]" value="'.$datos[0]['nombre'].'" />
									</td>
								</tr>
								<tr>
									<td>Numero</td>
									<td>
										<input type="number" id="numero" name="numero"  placeholder="Numero" class="validate[required, number]" value="'.$datos[0]['numero'].'" />
									</td>
								</tr>
								<tr>
									<td>Tipo</td>
									<td>';

		$formulario .= TiposNorma($norma); //selects seleccionados y disponibles para los tipos de normas

		$formulario .= '			</td>
								</tr>
								</table>
								<br/><br/>
							</div>
							<div class="datos-botones">
								<button type="button" onClick="CancelarContent()">Cancelar</button>
								<input type="reset" value="Borrar" />
								<input type="submit" value="Guardar" />
							</div>
						</form>';

	}

	echo $formulario;
}

/**
* FORMULARIO PARA NUEVA NORMA
*/
function NuevaNorma(){
	$formulario = "";
	$formulario .= '<div class="titulo">
						Nueva Norma
						<hr>
					</div>
					<form id="FormularioNuevaNorma" enctype="multipart/form-data" method="post" action="src/ajaxNormas.php">
							<div class="datos">
								<input type="hidden" value="CrearNorma" name="func" />
								<table>
								<tr>
									<td>Nombre</td>
									<td>
										<input type="text" id="nombre" name="nombre" placeholder="Nombre" class="validate[required]" />
									</td>
								</tr>
								<tr>
									<td>Numero</td>
									<td>
										<input type="number" id="numero" name="numero" placeholder="Numero" class="validate[required, number]" />
									</td>
								</tr>
								<tr>
									<td>
										Tipo
									</td>
									<td>';

	$formulario .= Tipos(); //selects disponibles para los tipos de normas

	$formulario .= '				</td>
								</tr>
							</table>
							<br/><br/>
							</div>
							<div class="datos-botones">
								<button type="button" onClick="CancelarContent()">Cancelar</button>
								<input type="reset" value="Borrar" />
								<input type="submit" value="Guardar" />
							</div>
						</form>';
	echo $formulario;
}

/**
* OBTIENE LOS TIPOS DE NORMAS Y LOS COMPONE EN UN SELECT
*/
function Tipos(){
	$tipos = "";
	$registros = new Registros();

	$datos = $registros->getTipos();

	if(!empty($datos)){
		$tipos .= '<select name="tipo" class="validate[required]">';

		foreach ($datos as $fila => $tipo) {
			$tipos .= '<option value="'.$tipo['id'].'">'.$tipo['nombre'].'</option>';
		}

		$tipos .= '</select>';

	}else{
		$tipos .= '<div>No hay tipos.</div>';
	}

	//EL SELECT COMPUESTO
	return $tipos;
}

/**
* BOTIENE LOS TIPOS DISPONIBLES Y SELECCIONADOS PARA LA NORMA
* @param $norma -> id de a norma
*/
function TiposNorma($norma){
	$tipos = "";
	$registros = new Registros();

	$seleccionado = $registros->getTipoNorma($norma); //obtiene el tipo de norma seleccionada
	$datos =  $registros->getTipos();

	if(!empty($datos)){
		$tipos .= '<select name="tipo" class="validate[required]" >';

		foreach ($datos as $fila => $tipo) {
			if($tipo['id'] == $seleccionado){
				$tipos .= '<option value="'.$tipo['id'].'" selected="selected" >'.$tipo['nombre'].'</option>';
				continue;
			}
			$tipos .= '<option value="'.$tipo['id'].'">'.$tipo['nombre'].'</option>';
		}

		$tipos .= '</select>';

	}else{
		$tipos .= Tipos(); //NO SE HA SELECCIONADO NINGUNO
	}

	//EL SELECT COMPUESTO
	return $tipos;
}

/**
* ACTUALIZA NORMA, DATOS PROVIENEN DEL FORMULARIO
* $norma
*/
function ActualizarNorma($norma){
	$registros = new Registros();

	//ACTUALIZA NORMA
	$registros->UpdateNorma($norma, $_POST['nombre'], $_POST['numero'], $_POST['tipo']);
}

/**
* DESHABILITA UNA NORMA
* @param $norma -> id de la normas
*/
function DeshabilitarNorma($norma){
	$registros = new Registros();
	$registros->DeshabilitarNorma($norma);
}

/**
* HABILITA UNA NORMA
* @param $norma -> id de la normas
*/
function HabilitarNorma($norma){
	$registros = new Registros();
	$registros->HabilitarNorma($norma);
}


/**
* REGISTRA UNA NUEVA NORMA
*/
function RegistrarNorma(){
	if( isset($_POST['nombre']) && isset($_POST['numero']) && isset($_POST['tipo']) ){
		$registros = new Registros();
		$registros->RegistrarNorma($_POST['nombre'], $_POST['numero'], $_POST['tipo']);
	}
}

/************************************ ARTICULOS *************************/

/**
* LISTA DE ARTICULOS DE UNA NORMA
*/
function Articulos($norma){
	$lista = '';
	$registros = new Registros();

	$articulos = $registros->getArticulos($norma);
	$estado = $registros->getDatoNorma("status", $norma);

	if($estado == 0){
		$visibilidad = "deshabilitado";
	}else{
		$visibilidad = "";
	}

	if(!empty($articulos)){

		$lista .= '<div id="articulos" class="'.$visibilidad.'">
					  <div class="titulo">
					  	Articulos de '.$registros->getDatoNorma("nombre", $norma).' '.$registros->getDatoNorma("numero", $norma).'
					  	<hr>
					  </div>
				      <ul>';

		//carga la lista
		foreach ($articulos as $fila => $articulo) {
			$lista .= '<li id="articulo'.$articulo['id'].'" onClick="SelectArticulo('.$articulo['id'].')">'.$articulo['nombre'].'</li>';
		}

	}else{
		$lista .= '<div id="articulos" class="'.$visibilidad.'">
					  <div class="titulo">
					  	Articulos de '.$registros->getDatoNorma("nombre", $norma).' '.$registros->getDatoNorma("numero", $norma).'
					  	<hr>
					  </div>
					  No hay articulos.
					  <br/>';
	}

	$lista .= '<div class="datos-botones">
				<button type="button" onClick="BorrarArticulo()">Borrar</button>
				<button type="button" onClick="EditarArticulo()">Editar</button>
			   	<button type="button" onClick="NuevoArticulo('.$norma.')">Nuevo Articulo</button>
			   </div>
			   </div>';

	echo $lista;
}

function NuevoArticulo($norma){
	$formulario = "";
	$formulario .= '<div class="titulo">
						Nuevo Articulo
						<hr>
					</div>
					<form id="FormularioNuevoArticulo" enctype="multipart/form-data" method="post" action="src/ajaxNormas.php" >
							<div class="datos">
								<input type="hidden" value="CrearArticulo" name="func" />
								<input type="hidden" value="'.$norma.'" name="norma" />
								<br/>
								<input type="text" id="nombre" name="nombre" placeholder="Nombre" class="validate[required]" />
								<br/><br/>';

	$formulario .= Entidades(); //entidades disponibles

	$formulario .= '<br/><br/>

								<!-- tabs para los datos -->
								<div id="tabs">
								    <ul>
								        <li><a href="#tabs-1">Resumen</a></li>
								        <li><a href="#tabs-2">Permisos</a></li>
								        <li><a href="#tabs-3">Sanciones</a></li>
								        <li><a href="#tabs-4">Articulos</a></li>
								    </ul>

								    <div id="tabs-1">
								    	<textarea class="validate[required]" id="resumen" name="resumen" ></textarea>
								    </div>
								    <div id="tabs-2">
								    	<textarea class="validate[required]" id="permisos" name="permisos" ></textarea>
								    </div>
								    <div id="tabs-3">
								    	<textarea class="validate[opcional]" id="sanciones" name="sanciones" ></textarea>
								    </div>
								    <div id="tabs-4">
								    	<textarea class="validate[required]" id="articulo" name="articulo" ></textarea>
								    </div>

							    </div>
							    <div class="editor-footer">
							    	<img id="adjuntar-icon" src="images/folder-upload.png" onClick="Adjuntos()" />
							    </div>
							</div>
							<!-- fin del cuadro -->

							<div class="adjuntos">
								<input type="hidden" name="totalArchivos" value="0" />
								<span class="adjuntos-boton" onClick="AdjuntoExtra()">+</span>

								<div id="archivo0" class="adjunto">
									<input type="text" name="archivoNombre0" placeholder="Nombre" />
									<input type="file" name="archivo0" />
								</div>
							</div>

							<div class="datos-botones">
								<button type="button" onClick="CancelarContent()">Cancelar</button>
								<input type="reset" value="Borrar" />
								<input type="submit" value="Guardar" />
							</div>
						</form>';
	echo $formulario;
}


/**
* OBTIENE LAS ENTIDADES DISPONIBLES Y LAS COMPONE EN UN SELECT
* @return $select -> el select compuesto
*/
function Entidades(){
	$select = "";

	$registros = new Registros();
	$entidades = $registros->getEntidades();

	if(!empty($entidades)){
		$select .= '<select id="entidades" name="entidades[]" multiple="multiple" style="width:400px">';

		foreach ($entidades as $fila => $entidad){

			echo $key = array_search($entidad['id'], $entidades);

			if( TieneHijos($entidades, $entidad['id']) ){

				$select .= '<optgroup label="'.$entidad['nombre'].'">';

				foreach ($entidades as $fi => $sub) {

					if($sub['padre'] == $entidad['id']){
						$select .= '<option value="'.$sub['id'].'">'.$sub['nombre'].'</option>';
					}
				}

				$select .= '</optgroup>';

			}else if( $entidad['padre'] == 0){
				//no tiene hijos
				$select .= '<option value="'.$entidad['id'].'">'.$entidad['nombre'].'</option>';
			}
		}

		$select .= '</select>';

	}else{
		$select .= '<div>No hay entidades.</div>';
	}
	$select .= '<script>
					SelectorMultipleFiltro();
				</script>';
	//EL SELECT COMPUESTO
	return $select;
}

/**
* DETERMINA SI UNA ENTIDAD TIENE HIJOS
* @param $datos -> array
* @param $padre -> id del padre a comprobar
* @return true si tiene hijos
* @return false sino tiene
*/
function TieneHijos($datos, $padre){
	
	if(!empty($datos) && is_array($datos)){
		foreach ($datos as $fila => $dato){

			if( $dato['padre'] == $padre){
				return true;
			}
		}
		return false;
	}
	return false;
}

/**
* REGISTRA UN NUEVO ARTICULO
* @param $norma -> id de la norma a la que pertence el nuevo articulo
*/
function RegistrarArticulo($norma){
	$registros = new Registros();

	if($articulo = $registros->RegistrarArticulo($norma, $_POST['nombre'], $_POST['entidades'], $_POST['resumen'], $_POST['permisos'], $_POST['sanciones'], $_POST['articulo'] )){
		//registra archivos adjuntos
		AdjuntarArchivos("articulo", $articulo);
	}else{
		echo 'Error al registrar nuevo articulo.';
	}

}

/**
* ELIMINA UN ARTICULO
* @param $articulo -> id del articulo a eliminar
*/
function BorrarArticulo($articulo){
	$registros = new Registros();
	
	//BORRA ARTICULO
	if( !$registros->DeleteArticulo($articulo) ){
		echo "Error al borrar articulo.";
	}

}

/**
* FORMULARIO DE EDICION DE ARTICULO
* @param $articulo -> id del articulo ha editar
*/
function EdicionArticulo($articulo){
	$registros = new Registros();
	$datos = $registros->getArticulo($articulo); //obtiene la ultima version del articulo

	$formulario = "";

	if( !empty($datos) ){

		//OBTIENE LOS ARCHIVOS ADJUNTOS
		$archivos = $registros->getArchivosArticulo($datos[0]['id']);

		$formulario .= '<div class="titulo">
							Nuevo Articulo
							<hr>
						</div>
						<form id="FormularioEditarArticulo" enctype="multipart/form-data" method="post" action="src/ajaxNormas.php" >
								<div class="datos">
									<input type="hidden" value="ActualizarArticulo" name="func" />
									<input type="hidden" value="'.$datos[0]['norma'].'" name="norma" />
									<input type="hidden" value="'.$datos[0]['id'].'" name="id" />
									<br/>
									<input type="text" id="nombre" name="nombre" placeholder="Nombre" class="validate[required]" value="'.$datos[0]['nombre'].'" />
									<br/><br/>';

		$formulario .= EntidadesArticulo(unserialize($datos[0]['entidad'])); //entidades seleccionada	

		$formulario .= '<br/><br/>

								<!-- tabs para los datos -->
								<div id="tabs">
								    <ul>
								        <li><a href="#tabs-1">Resumen</a></li>
								        <li><a href="#tabs-2">Permisos</a></li>
								        <li><a href="#tabs-3">Sanciones</a></li>
								        <li><a href="#tabs-4">Articulos</a></li>
								    </ul>

								    <div id="tabs-1">
								    	<textarea class="validate[required]" id="resumen" name="resumen" >'.base64_decode($datos[0]['resumen']).'</textarea>
								    </div>
								    <div id="tabs-2">
								    	<textarea class="validate[required]" id="permisos" name="permisos" >'.base64_decode($datos[0]['permisos']).'</textarea>
								    </div>
								    <div id="tabs-3">
								    	<textarea class="validate[opcional]" id="sanciones" name="sanciones" >'.base64_decode($datos[0]['sanciones']).'</textarea>
								    </div>
								    <div id="tabs-4">
								    	<textarea class="validate[required]" id="articulo" name="articulo" >'.base64_decode($datos[0]['articulo']).'</textarea>
								    </div>';
		if(empty($archivos)){
			$formulario .= ' 		<div class="editor-footer">
							    		<img id="adjuntar-icon" src="images/folder-upload.png" onClick="Adjuntos()" />
							    	</div>';
		}
								   
		$formulario .= '			</div><!-- fin tabs -->
								</div>
								<!-- fin del datos -->';

		$formulario .= '		<!-- archivos adjuntos -->
								<div class="adjuntos">';
		
		if(!empty($archivos)){
			$formulario .= '<ul>';
			foreach ($archivos as $fi => $archivo) {
				
				$formulario .=      '<li id="adjuntado'.$archivo['id'].'">
										<a href="src/download.php?link='.$archivo['link'].'">
											'.$archivo['nombre'].'
											<img src="images/folder.png">
										</a>
										<img class="close" src="images/close.png" onClick="EliminarAdjunto('.$archivo['id'].')" />
									</li>';
			}
			
			$formulario .= '</ul>';
		}
									
		$formulario .= '
									<input type="hidden" name="totalArchivos" value="0" />
									<span class="adjuntos-boton" onClick="AdjuntoExtra()">+</span>

									<div id="archivo0" class="adjunto">
										<input type="text" name="archivoNombre0" placeholder="Nombre" />
										<input type="file" name="archivo0" />
									</div>
								</div>
								<!-- FIN ADJUNTO -->

								<div class="datos-botones">
									<button type="button" onClick="CancelarContent()">Cancelar</button>
									<input type="reset" value="Borrar" />
									<input type="submit" value="Guardar" onClick="EditorUpdateContent()" />
								</div>
							</form>';
	}else{
		$formulario .= 'El articulo no existe.';
	}
	echo $formulario; 
}

/**
* COMPONE EL SELECT 
* @registradas -> array[] con los las opciones seleccionadas
* @return $select -> select compuesto
*/
function EntidadesArticulo($seleccionadas){
		$select = "";

	$registros = new Registros();
	$entidades = $registros->getEntidades();  //obtiene todas las entidades

	if(!empty($entidades)){
		$select .= '<select id="entidades" name="entidades[]" multiple="multiple" style="width:400px">';

		foreach ($entidades as $fila => $entidad){

			if( TieneHijos($entidades, $entidad['id']) ){

				$select .= '<optgroup label="'.$entidad['nombre'].'">';

				foreach ($entidades as $fi => $sub) {
					$listo = false;
					//compara con selecciones
					foreach ($seleccionadas as $f => $seleccion) {

						//seleccionada
						if($seleccion == $sub['id'] && $sub['padre'] == $entidad['id'] ){
							$select .= '<option value="'.$sub['id'].'" selected="selected" >'.$sub['nombre'].'</option>';
							$listo = true;
							break; //termina foreach
						}

					}
					if($listo){
						continue; //ya se agrego al select
					}
					if($sub['padre'] == $entidad['id']){
						$select .= '<option value="'.$sub['id'].'">'.$sub['nombre'].'</option>';
					}
				}

				$select .= '</optgroup>';

			}else if( $entidad['padre'] == 0){
				//no tiene hijos
				foreach ($seleccionadas as $f => $seleccion) {

					//seleccionada
					if($seleccion == $entidad['id'] ){
						$select .= '<option value="'.$entidad['id'].'" selected="selected" >'.$entidad['nombre'].'</option>';
						$listo = true;
						break;
					}else{
						$select .= '<option value="'.$entidad['id'].'">'.$entidad['nombre'].'</option>';
					}
				}
			}
		}

		$select .= '</select>';

	}else{
		$select .= '<div>No hay entidades.</div>';
	}
	$select .= '<script>
					SelectorMultipleFiltro();
				</script>';
	//EL SELECT COMPUESTO
	return $select;
}

/**
* ACTUALIZA UN ARTICULO EDITADO, EN REALIDAD CREA UN SNAPSHOT DEL ARTICULO
* @param $norma -> id de la norma
* @param $id -> id del articulo
*/
function ActualizarArticulo($norma, $id){
	$registros = new Registros();
	
	//ACTUALIZA ARTICULO
	$registros->UpdateArticulo($norma, $id, $_POST['nombre'], $_POST['entidades'], $_POST['resumen'], $_POST['permisos'], $_POST['sanciones'], $_POST['articulo'] );

	//registra archivos adjuntos si tiene
	AdjuntarArchivos("articulo", $id);

}


/**
* ELIMINA UN ARCHIVO ADJUNTO
* @param id -> id del archivo a eliminars
*/
function EliminarArchivo($id){
	$registros = new Registros();
	if(!$registros->DeleteArchivo($id)){
		echo 'Error no se pudo borrar el archivo.';
	}
}

/***************** MANEJO DE ARCHIVOS **************/

/**
* SUBE Y GUARDA LOS ARCHIVOS ADJUNTOS
* @param $tipo -> tipo de adjunto, para norma o articulo
* @param $pertences -> id de la norma/articulo al que pertenecen los adjuntos
*/
function AdjuntarArchivos($tipo, $pertenece){
	$registro = new Registros();

	for ($i=0; $i < 10; $i++) { 
		//SI ENVIA UN ARCHIVO
		if( isset($_FILES['archivo'.$i]['tmp_name']) && isset($_POST['archivoNombre'.$i]) ){
			
			if( !$_FILES['archivo'.$i]['tmp_name'] == '' && !empty($_FILES['archivo'.$i]['tmp_name']) ){
				//SUBE EL ARCHIVO Y GUARDA LOS DATOS
				if($tipo == 'articulo'){
					$registro->NuevoArchivo( "articulo", $_FILES['archivo'.$i] , $_POST['archivoNombre'.$i], $pertenece);
				}else if($tipo == 'norma'){
					$registro->NuevoArchivo( "norma", $_FILES['archivo'.$i] , $_POST['archivoNombre'.$i], $pertenece);
				}
			}
		}
	}
		
}

?>