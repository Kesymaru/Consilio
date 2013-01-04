<?php 
/**
* MUESTRA LA LISTA DE LOS PROYECTOS, CON OPCION DE BUSQUEDA
*/
	require_once("../src/master.php"); 

$master = new Master();

?>

<script type="text/javascript">

	$('#formularioListaProyectos button, input:reset, .controls button').button();

	$('input[placeholder]').placeholder();
	$('textarea[placeholder]').placeholder();
	$('#resetearBuscarProyecto').hide();

	lista();

	//muestra la lista
	function lista(){
		var query = {'func' : 'MenuProyectos'};
		$.ajax({
			data: query,
			url: 'src/ajax.php',
			type: 'post',
			success: function(response){
				$('#listaProyectos').html(response);
				$('#listaProyectos #botonNuevoProyecto').hide();

				var alto = $('#content').height() * 0.4;
				$('#listaProyectos').css('height' , alto);

				$('#resetearBuscarProyecto').fadeOut();
			}
		});

		$('#buscarProyecto').val('');
	}

	function buscarProyecto(){
		notifica('Buscando proyectos.');
		var buscar = $('#buscarProyecto').val();

		var query = {'func' : 'BuscarProyecto', 'buscar' : buscar};
		$.ajax({
			data: query,
			url: 'src/ajax.php',
			type: 'post',
			success: function(response){
				$('#listaProyectos').html(response);
				$('#resetearBuscarProyecto').fadeIn();
			}
		});
	}

</script>

<div class="titulo">
	Proyectos
</div>

<div id="formularioListaProyectos">


	<div class="center">
		<p>Seleccione un proyecto</p>
		<br/>
		
		<div class="search">
			<input type="text" id="buscarProyecto" name="buscarProyecto" placeholder="buscar"><img src="images/search.png" onClick="buscarProyecto()" title="Buscar">
		</div>

		<ul id="listaProyectos">
		</ul>

	</div>

</div> 

<div class="controls">
	<button id="resetearBuscarProyecto" onclick="lista();">Limpiar</button>
</div>
