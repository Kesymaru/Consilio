<html>
<head>
	<meta charset="utf-8">

	<link rel="stylesheet" href="../css/style.css" type="text/css">
	<link rel="stylesheet" href="../css/jquery-ui-1.9.0.custom.css" type="text/css">

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<script type="text/javascript" src="../js/jquery-ui-1.9.0.custom.js"></script>

	<script type="text/javascript" src="../js/main.js"></script>
</head>
<body>
<?php

require_once("class/registros.php");


if(isset($_GET['categoria'])){
	$id = $_GET['categoria'];

	$registros = new Registros();

	//obtiene todos los datos de la categoria
	$categoria = $registros->getCategoria( $id );

	$normas = unserialize($categoria[0]['normas']);

	if(!empty($normas) && !empty($categoria)){
		
		echo '<div class="preview">
			<div class="titulo">
				'.$categoria[0]['nombre'].'
				<button type="button" title="Buscar Normas" onClick="BuscarGenerica(\'buscar-iframe\', \'normas\')">Buscar</button>
				<hr>
				<div class="busqueda">
					<input type="search" id="buscar-iframe" placeholder="Buscar Normas" title="Escriba para Buscar" />
				</div>
			</div>';

		echo '<ul class="normas-list">';

		foreach ($normas as $f => $norma) {

			$datos = $registros->getDatosNorma($norma);

			if(!empty($normas)){
				echo '<li>'.$datos[0]['nombre'].'</li>';
			}
		}

		echo '</ul>';
	}else{
		echo '<div class="preview">
			<div class="titulo">
				'.$categoria[0]['nombre'].'
				<hr>
			</div>';

		echo "No hay articulos.";
	}

	echo '</div>';

}else{
	echo '<script>notificaError("Error: previewNorma.php no se encontro la norma,';
}

?>
</body>
</html>