
<?php

require_once("class/registros.php");

if(isset($_GET['categoria'])){
?>


<html>
<head>
	<meta charset="utf-8">

	<link rel="stylesheet" href="../css/style.css" type="text/css">
	<link rel="stylesheet" href="../css/jquery-ui-1.9.0.custom.css" type="text/css">

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<script type="text/javascript" src="../js/jquery-ui-1.9.0.custom.js"></script>

	<script type="text/javascript" src="../js/preview.js"></script>
</head>
<body>

<?php
	$id = $_GET['categoria'];

	$registros = new Registros();

	//obtiene todos los datos de la categoria
	$categoria = $registros->getCategoria( $id );

	$normas = unserialize($categoria[0]['normas']);

	if(!empty($normas) && !empty($categoria)){
		
		echo '<div class="preview">
			<div class="titulo">
				<button type="button" class="atras" onClick="Cambio()">Atras</button>
				'.$categoria[0]['nombre'].'
				<button type="button" class="siguiente" onClick="Articulos()">Siguiente</button>
				<hr>
			</div>
			<div class="datos-preview">

			<table class="table-preview">
			<tr>
				<td id="normas">
					Normas
					<button class="boton-buscar" type="button" title="Buscar Normas" onClick="Busqueda(\'busqueda-normas\', \'buscar-normas\', \'normas\', false)">Buscar</button>

					<div class="busqueda" id="busqueda-normas">
						<div class="buscador">
							<input type="search" title="Escriba Para Buscar Normas" id="buscar-normas" placeholder="Buscar Normas"/>
						</div>
					</div>';

		echo '<ul>';

		foreach ($normas as $f => $norma) {

			$datos = $registros->getDatosNorma($norma);

			if(!empty($normas)){
				$tipo = $registros->getTipoDato("nombre", $datos[0]['tipo']);
				echo '<li id="'.$datos[0]['id'].'" title="'.$tipo.' #'.$datos[0]['numero'].'" onClick="SelectNormas('.$datos[0]['id'].')" >'.$datos[0]['nombre'].'</li>';
			}
		}

		echo '</ul>
			  </td>
			  <td id="articulos">
			  		Articulos
					<button class="boton-buscar" type="button" title="Buscar Articulos" onClick="Busqueda(\'busqueda-articulos\', \'buscar-articulos\', \'articulos\', false)">Buscar</button>
					
					<div class="busqueda" id="busqueda-articulos">
						<div class="buscador">
							<input type="search" title="Escriba Para Buscar Normas" id="buscar-articulos" placeholder="Buscar Articulos"/>
						</div>
					</div>
					<ul id="articulos-list">
						<li>No hay Articulos</li>
					</ul>
			  </td>
			  </tr>
			 </table>';
	}else{
		echo '<div class="preview">
			<div class="titulo">
				'.$categoria[0]['nombre'].'
				<hr>
			</div>';

		echo "No hay articulos.";
	}

	echo '</div>';
	?>

</body>
</html>

	<?php
}

if(isset($_POST['func'])){
	switch ($_POST['func']) {
		case 'Articulos':
			if(isset($_POST['norma'])){
				Articulos($_POST['norma']);
			}
			break;
		
		default:
			break;
	}
}

/**
 * ARTICULOS DE UNA NORMA
 * @param $norma -> id norma
 */
function Articulos($norma){
	echo '<li>articulos norma</li>';
}

?>