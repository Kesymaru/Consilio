<?php 
	require_once("../db.php"); 
/*
	muestra una lista en el dialogo, con todas las categorias disponibles para agregar al proyecto
*/

//logueo
if( !isset($_SESSION['logueado']) ){
	$home = "/Matrices/login.php";
	echo "<script type='text/javascript'>top.location.href = '$home';</script>";
	exit;
}

?>

<script type="text/javascript">
	
	var selecciones = []; //seleccionadas
	var c = 0;
	categorias();

	$('#formularioNuevaCategoria button, input:reset, .controls button').button();

	$('input[placeholder]').placeholder();
	$('#limpiarCategoriasBusqueda').hide();

	function categorias(){
		//carga las categorias
		var query = {'func' : 'categorias'};
		$.ajax({
			data: query,
			url: 'ajax.php',
			type: 'post',
			success: function (response){
				$('#categorias').html(response);
				var alto = $('#content').height() * 0.4;
				$('#categorias').css('height' , alto);
			}
		});
	}

	function resetea(){
		$('.seleccion-option').removeClass('seleccionada');
		selecciones = [];
		c = 0;

		$('#buscarCategoria').val('');
		
		//resetea cualquier busqueda
		categorias();
		$('#limpiarCategoriasBusqueda').fadeOut();
	}

	//guarda en el array las selecciones
	function selecciona(id){

		if($('#seleccion'+id).hasClass('seleccionada')){
			$('#seleccion'+id).removeClass('seleccionada');
			
			var temp = [];
			var x = 0;

			//limpia el valor deseleccionado
			for(var i = 0; i <= selecciones.length; i++){
				if(selecciones[i] !== id && selecciones[i] != ' ' && selecciones[i] != '' && selecciones[i] != null){
					temp[x] = selecciones[i];
					x++;
				}
			}

			seleccciones = [];

			//carga el nuevo
			for(var i = 0; i <= temp.length; i++){
				selecciones[i] = temp[i];
				c = i;
			}

		}else{
			$('#seleccion'+id).addClass('seleccionada');
			selecciones[c] = id;
			c++;
		}
		
	}

	function enviarCategorias(){
		if( c > 0){
			cargarCategorias(selecciones);
			closeDialogo();
		}else{
			notificaError('Error<br/>Debe seleccionar almenos una opcion.'); 
		}
	}

	//busca categorias
	function buscarCategoriasSeleccion(){
		var buscar = $('#buscarCategoria').val();
		notifica('Buscando Categorias.');
		$('#limpiarCategoriasBusqueda').fadeIn();

		var query = {'func' : 'buscarCategoriasSeleccion', 'buscar' : buscar};
		$.ajax({
			data: query,
			url: 'ajax.php',
			type: 'post',
			success: function (response){
				$('#categorias').html(response);
			}
		});
	}

</script>

<div class="titulo">
	Categorias
</div>

<div id="formularioNuevaCategoria">


	<div class="center">
		<p>Puede seleccionar mas de una opción</p>
		<br/>


		<div class="search">
			<input type="text" id="buscarCategoria" name="buscarCategoria" placeholder="buscar">
			<img src="images/search.png" onClick="buscarCategoriasSeleccion();" title="Buscar">
		</div>

		<div class="seleccion" id="categorias">

		</div>

	</div>

</div> 

<div class="controls">
	<button id="limpiarCategoriasBusqueda" onclick="resetea();">Limpiar</button>
	<button onclick="enviarCategorias();">Seleccionar</button>
</div>