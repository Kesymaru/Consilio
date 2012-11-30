<?php 

/**
* VISTA DE PROYECTOS
*/

require_once("../src/class/session.php"); 
require_once("../src/class/proyectos.php");

//seguridad de que esta logueado
$session = new Session();
$session->Logueado();

$proyectos = new Proyectos();

?>
<script type="text/javascript">

	function ListaProyectos(){
		$.cookie('accion', 'ListaProyectos'); //cookie para restaurar
		                                      
		var queryParams = {"func" : "ListaProyectos"};
		$.ajax({
			data: queryParams,
			type: "post",
			url: "src/ajaxProyectos.php",
			success: function(response){
				$("#vista").html("");
				$("#vista").html(response);

				$("#ListaProyectos").attr("checked", true);
				SetBotones('proyectoControls');
				
				var proyecto = $.cookie('proyecto');
				if(proyecto > 0){
					//restaura la fila seleccionada
					$("#"+proyecto).css("background-color", "rgb(161, 202, 74, 0.5)");
					ContextMenu();
				}
				
				//EDICION AL DOBLE CLICK
				$("#TablaProyectos tr").dblclick(function(){
					$.cookie('proyecto', $(this).attr("id"));
					EditarProyecto();
				});
			},
			fail: function(){
				notificaError("Error: en ajax al cargar Lista de Proyectos.");
			}
		});
	}

	/**
	* EDITAR PROYECTO EXISTENTE
	*/
	function EditarProyecto(){
		var proyecto = $.cookie('proyecto');

		if( proyecto > 0){

			$.cookie('accion', 'EditarProyecto'); //cookie para restaurar

			queryParams = {'func' : "EditarProyecto", "ProyectoId" : proyecto};
			$.ajax({
				data: queryParams,
				type: "post",
				url: "src/ajaxProyectos.php",
				success: function(response){
					$("#vista").html("");
					$("#vista").html(response);

					$("#EditarProyecto").attr("checked", true);
					SetBotones('proyectoControls');
				},
				fail: function(){
					notificaError('Eror: ajax, al cargar Edicion de proyecto.');
				}
			});

		}else{
			notificaAtencion("Seleccione un proyecto de la lista.");

			if($.cookie('accion', 'EditarProyecto') != 'ListaProyectos'){
				ListaProyectos();
			}
		}
	}

	/**
	*	CREAR UN NUEVO PROYECTO
	*/
	function NuevoProyecto(){
		$.cookie('accion', 'NuevoProyecto');

		queryParams = {'func' : "NuevoProyecto"};
		$.ajax({
			data: queryParams,
			type: "post",
			url: "src/ajaxProyectos.php",
			beforeSend: function(){

			},
			success: function (response){
				$("#vista").html("");
				$("#vista").html(response);

				$("#NuevoProyecto").attr("checked", true);
				SetBotones('proyectoControls');
			},
			fail: function(){

			}
		});
	}

	/**
	* UTILIZA LAS COOKIES PARA RESTAURAR LA VISTA COMO EL USUARIO LA DEJO
	*/
	function Restaurar(){
		var proyecto = $.cookie('proyecto');
		var accion = $.cookie('accion');

		if(proyecto > 0){
			if(accion == 'EditarProyecto'){
				EditarProyecto();
				return;
			}
		}
		if(accion == 'ListaProyectos'){
			ListaProyectos();
			return;
		}
		if(accion = 'NuevoProyecto'){
			NuevoProyecto();
			return;
		}
	}

	/**
	* SELECCIONA UNA FILA -> PROYECTO DE LA LISTA
	*/
	function SeleccionFila(id){
		var proyecto = $.cookie('proyecto');
		if(proyecto > 0){
			$("#"+proyecto).css("background-color", "");
		}
		if(proyecto == id){
			$.cookie('proyecto',0);
			$("#"+proyecto).css("background-color", "");
		}else{
			$("#"+id).css("background-color", "rgb(161, 202, 74, 0.5)");
			$.cookie('proyecto',id);
			ContextMenu();
		}
	}

	/**
	* VALIDA FORMULARIO DE EDICION DE PROYECTO EXISTENTE
	*/
	function FormularioEditarProyecto(){
		//validacion
		$("#formularioEditarProyecto").validationEngine();
		
		var options = {  
			beforeSend: function(){

			},
	    	success: function(response) { 
		        notifica("Datos Actualizados correctamente.");
		        Restaurar();
		    },
		    fail: function(){
		    	notificaError("Error: en ajax al actualizar datos.");
		    }
		}; 

		$('#formularioEditarProyecto').ajaxForm(options);
	}

	/**
	* FORMULARIO DE NUEVO PROYECTO
	*/
	function FormularioEditarNuevoProyecto(){
		//validacion
		$("#formularioEditarNuevoProyecto").validationEngine();
		
		var options = {  
			beforeSend: function(){

			},
	    	success: function(response) { 
		        notifica("Proyecto Creado.");
		        ListaProyectos();
		    },
		    fail: function(){
		    	notificaError("Error: en ajax al actualizar datos.");
		    }
		}; 

		$('#formularioEditarNuevoProyecto').ajaxForm(options);
	}

	/**
	* EXPORTAR PROYECTO
	*/
	function ExportarProyecto(tipo){
		id = $.cookie('proyecto');
		if(id > 0){
			top.location.href = 'src/class/exportar.php?id='+id+'&tipo='+tipo;
			notificaAtencion('Asegurese de guardar el archivo en el disco duro.')
		}else{
			notificaAtencion("Por favor seleccione un proyecto.");
		}
	}


	/**
	* CONETEXT MENU
	*/
	function ContextMenu(){
		var id = $.cookie('proyecto');
		$.contextMenu({
	        selector: '#'+id, 
	        callback: function(key, options) {
	            var m = "clicked: " + key;
	            //window.console && console.log(m) || alert(m); 
	            AccionMenu(m);
	        },
	        items: {
	        	"nuevo": {name: "Nuevo", icon: "add"},
	            "editar": {name: "Editar", icon: "edit"},
	            "eliminar": {name: "Eliminar", icon: "delete"},
	            "sep1": "---------",
	            "fold1a": {
                "name": "Exportar", 
                "icon": "exportar",
	                "items": {
	                    "exportar-excel": {"name": "Excell" , "icon": "excel"},
	                    "exportar-pdf": {"name": "PDF", "icon": "pdf"},
	                }
            	}

	        }
	    });
	    
	    $('#'+id).on('click', function(e){
	        //console.log('clicked', this);
	    })
	}

	/**
	* ELIMINAR PROYECTO
	*/
	function EliminarProyecto(){
		var proyecto = $.cookie('proyecto');
		if(proyecto > 0){
			queryParams = {'func' : 'EliminarProyecto', 'ProyectoId' : proyecto};
			$.ajax({
				data: queryParams,
				type: "post",
				url: "src/ajaxProyectos.php",
				success: function(response){
					notifica("Proyecto Eliminado.");
					ListaProyectos();
				},
				fail: function(){
					notificaError('Eror: ajax, al cargar Edicion de proyecto.');
				}
			});
		}else{
			notificaAtencion("Seleccione un proyecto.");
		}
	}
	/**
	* ACCION DEL MENU
	*/
	function AccionMenu(m){
		if(m == 'clicked: editar'){
			EditarProyecto();
		}
		if(m == 'clicked: nuevo'){
			NuevoProyecto();
		}
		if(m == 'clicked: eliminar'){
			var si = function (){
				EliminarProyecto();
			}

			var no = function (){
				notificaAtencion("Operacion cancelada");
			}

			Confirmacion("Esta seguro que desea eliminar el proyecto.", si, no);
		}
		if(m == 'clicked: exportar-excel'){
			ExportarProyecto('excel');
		}
		if(m == 'clicked: exportar-pdf'){
			ExportarProyecto('pdf');
		}
	}

</script>
			<div class="topControls" >
				
				<!-- menu proyectos -->
				<div id="proyectoControls" >

					<!-- Lista Proyectos -->
					<input type="radio" id="ListaProyectos" name="radio"/>
						<label for="ListaProyectos" onClick="ListaProyectos()">
						Lista
						</label>

					<!-- Nuevo proyecto -->
					<input type="radio" id="NuevoProyecto" name="radio"/>
						<label for="NuevoProyecto" onClick="NuevoProyecto()">
						Nuevo
						</label>

					<!-- Editar proyecto 
					<input type="radio" id="EditarProyecto" name="radio"/>
						<label for="EditarProyecto" onClick="EditarProyecto()">
						Editar
						</label>
					-->

					<!-- Exporat proyecto 
					<input type="radio" id="ExportarProyecto" name="radio"/>
						<label for="ExportarProyecto" onClick="ExportarProyecto()">
						Exportar
						</label>
					-->
				</div>
				<hr>
				<script type="text/javascript">
					SetBotones('proyectoControls');
				</script>

				<!-- end menu proyectos -->
			</div>

			<div id="vista">
				<div class="vista">
					<?php
					if(isset($_GET['nuevo'])){
						//$proyectos->FormularioNuevoProyecto();
					}
					if(isset($_GET['proyectos'])){
						//echo $proyectos->Lista();
					}
					if(isset($_GET['editar'])){
						if($_GET['editar'] == 0){
							//echo $proyectos->Editar();
						}else{
							//echo $proyectos->EditarProyecto($_GET['editar']);
							?>
								<script type="text/javascript">
									//Formulario("formularioEditarProyecto")
								</script>
							<?php
						}
					}
						
					?>
				</div>
			</div>
			<script type="text/javascript">
					Restaurar();
			</script>
			<!-- end nivel 1-->