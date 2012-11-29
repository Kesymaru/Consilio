<?php
	require_once("../db.php"); 
/*
	Crea el resumen del proyecto
	resive el id del proyecto 
*/

//logueo
if( !isset($_SESSION['logueado']) ){
	$home = $_SESSION['home']."/login.php";
	header('Location: '.$home);
	exit;
}

//obtiene id
if(isset($_GET['proyecto'])){
	resumenProyecto($_GET['proyecto']);
}

?>
<script type="text/javascript">
	$('#nuevaNota').button();
</script>

<?php

//crea el resumen del proyecto
// @param id -> id del proyecto
function resumenProyecto($id){
	$tabla = 'class="tablaResumen"';
	$titulo = 'class="tituloResumen"';
	$columnaTitulo = 'class="columnaTituloResumen"';
	$columna = 'class="columnaResumen"';
	
	//nombre por defecto, despues lo cambia
	$nombre = 'proyecto'.$id;
	$cuerpo = '';
	$notas = '';

	$sql = 'SELECT * FROM proyectos WHERE cliente = '.$_SESSION['id'].' AND id = '.$id;
	$result = mysql_query($sql);

	//crea el resumen del proyecto
	while( $row = mysql_fetch_array($result) ){
		$nombre = $row['nombre'];

		$cuerpo .= '<tr>
			<td '.$columna.'>'.$row['nombre'].'</td>
			<td colspan="3"'.$columna.'>'.$row['descripcion'].'</td>
			<td '.$columna.'>'.$row['fecha'].'</td>
			<td '.$columna.'>';

		if($row['status'] == 1){
			$cuerpo .= 'Activo';
		}else{
			$cuerpo .= 'Finalizado';
		}

		$cuerpo .= '</td>
		</tr>';
	}

	//encabezados del exell
	$encabezado = '<table '.$tabla.'>
		<tr>
			<td colspan="6" '.$titulo.'>Resumen de '.$nombre.'</td>
		</tr>
		<tr>
			<td '.$columnaTitulo.'>Nombre</td>
			<td colspan="3"'.$columnaTitulo.'>Descripcion</td>
			<td '.$columnaTitulo.'>Fecha</td>
			<td '.$columnaTitulo.'>Estado</td>
		</tr>';

	//footer
	$footer = '</table>';
	
	$notas = notas($id);
	
	//crea el resumen
	echo $encabezado.$cuerpo.$notas.$footer;
}

//muestra todas las notas del proyecto
// @param id -> id del proyecto
function notas($id){
	$titulo = 'class="tituloResumen"';
	$columnaTitulo = 'class="columnaTituloResumen"';
	$columnaNota = 'class="columnaNotaResumen"';
	$columnaInfo = 'class="columnaInfoResumen"';
	$columnaControls = 'class="colunmaControlsResumen"';

	$sql = 'SELECT * FROM notas WHERE proyecto = '.$id;
	$result = mysql_query($sql);

	$notas = '';
	$c = 0;
	
	$notas .= '<tr>
			<td colspan="6" '.$titulo.'>
				Notas
			</td>
		</tr>';

	while($row = mysql_fetch_array($result)){
		$notas .= '<tr class="filaNotaResumen" id="nota'.$row['id'].'">
				<td colspan="4" '.$columnaNota.'>
					'.$row['nota'].'
				</td>
				<td colspan="2" '.$columnaInfo.'>
				<img src="images/close.png" class="removeNota" id="removeNota'.$row['id'].'" onClick="removeNota('.$row['id'].')">';
		$notas .= datosCliente($row['cliente']).'
				</td>
			</tr>';
		$c++;
	}

	$notas .= '<tr>
			<td colspan="6" '.$columnaControls.'>
				<button id="nuevaNota" onClick="nota('.$id.')">Agregar Nota</button>
			</td>
			</tr>';

	return $notas;
}

//muestra nombre e imagen del usuario para la nota
// @param id -> id del cliente 
function datosCliente($id){
	$datos = '';
	$sql = 'SELECT * FROM clientes WHERE id = '.$id;
	$result = mysql_query($sql);

	while($row = mysql_fetch_array($result)){
		$datos .= '<img src="'.$_SESSION['home'].'/images/users/user.png" style="float: left; height: 50px;">';
		$datos .= $row['nombre'].'<br/>'.$row['fecha'];
	}

	return $datos;
}

?>