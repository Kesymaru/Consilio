<?php

require_once("../src/class/session.php"); 
require_once("../src/class/registros.php");

/**
* VISTA DE EDICION DE CATEGORIAS
*/

//SEGURIDAD DE QUE ESTA LOGUEADO
$session = new Session();
$session->Logueado();

$registros = new Registros();

?>
<!-- JAVASCRIPT NECESARIO -->
<script type="text/javascript">

/**
* EDICION DE CATEGORIAS
*/
function EditarCategorias(){
	$.cookie('vista', 'edicion');
	ActivaMenu();
	Padres();
}

/**
* EDICION DE GENERALIDADES
*/
function EditarGeneralidades(){
	$.cookie('vista', 'edicionGeneralidades');
	notifica($.cookie('vista'));
	ActivaMenu();
}

/**
* FORMULARIO DE EDICION
*/
function FormularioEdicionCategoria(){
		//validacion
		$("#FormularioEdicionCategoria").validationEngine();
		
		var options = {  
			beforeSend: function(){

			},
	    	success: function(response) { 
		        notifica("Datos Actualizados.");
		    },
		    fail: function(){
		    	notificaError("Error: en ajax al actualizar datos.");
		    }
		}; 

		$('#FormularioEdicionCategoria').ajaxForm(options);
}

/**
* CREA EL PANEL DESPLAZABLE DE CATEGORIA
*/
function Padres(){
	$("#edicionConstrols").hide();

	var queryParams = {'func' : "Padres"};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxEdicion.php",
		beforeSend: function(){
			$("#menu").html('<img id="image-loader" src="images/ajax-loader.gif" />');
		},
		success: function(response){
			$("#menu").html(response);

		},
		fail: function(){
			notificaError("Error: ocurrio un error.<br/>Codigo: ajaxEdicion 001.");
		}
	});

	if( $.cookie('categorias') != "" ){
		categorias = $.cookie('categorias');
	}
}

/**
* CARGA LOS HIJOS DE UN PADRE
*/
function Hijos(padre){
	LimpiarHermanos(padre);
	//LimpiarCamino(padre);

	var queryParams = {'func' : "Hijos", "padre" : padre};
	
	/*$('#Padre'+padre).removeClass('seleccionada');
	$('#'+padre).addClass('seleccionada');*/

	//SeleccionaHijo(padre);

	//notifica($.cookie('categorias'));

	$.ajax({
		data: queryParams,
		type: "post",
		async: false,
		url: "src/ajaxEdicion.php",
		beforeSend: function(){
			$("#categorias").append('<img id="image-loader" style="display: inline-block;" src="images/ajax-loader.gif" />');
		},
		success: function(response){
			if(response.length > 0){
				//$("#categorias").append(response);
				$("#image-loader").fadeOut(500, function(){
					$("#image-loader").remove();
					$("#categorias").append(response);

					$("#Padre"+padre).hide();
					$("#Padre"+padre).fadeIn(500);

					var totalWidth = 0;

					$('.categoria').each(function(index) {
						    totalWidth += parseInt($(this).width(), 10);
					});
					
					totalWidth += $("#Padre0").width() + 100;

					$("#categorias").css('width', totalWidth);
				});
				SeleccionaHijo(padre);
			}
		},
		fail: function(){
			notificaError("Error: ocurrio un error.<br/>Codigo: ajaxEdicion 001.");
		}
	});
}

/**
* PONE ESTILO PARA CARGAR HIJO COMO SELECCIONADO
* @param hijo -> id hijo seleccionado
*/
function SeleccionaHijo(hijo){

	var padre = $("#"+hijo).closest("div").attr('id');

	$("#"+padre+' li').removeClass('seleccionada');
	$("#"+hijo).addClass('seleccionada');
}

/**
* CARGA UNA CATEGORIA SELECCIONADA
* @param id => id de la categoria
*/
function Categoria(id){

	/*$("#image-loader").fadeOut(500, function(){
		$("#image-loader").remove();
	});*/

	var queryParams = {"func" : "GetCategoria", "categoria" : id};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxEdicion.php",
		beforeSend: function(){
			$("#vista").append('<img id="image-loader" src="images/ajax-loader.gif" />');
		},
		success: function(response){
			$.cookie('categoria', id);

			$("#vista").html(response);
			$("#edicionConstrols").fadeIn(700);

			$("#BoxArchivo").hide();
		},
		fail: function(){
			notificaError("Error: ocurrio un error.<br/>Codigo: ajaxEdicion 002.");
		}
	});

	Opciones();
	ContextMenuCategoria(id);
}

/**
* LIMPIA EL CAMINO
* @param padre -> id del padre
*/
function LimpiarCamino(padre){
	//obtiene el padre del padre, para ver si no es root
	var Padre = $("#"+padre).closest("div").attr("id");

	if(Padre == "Padre0"){ //si es root entonces limpia todos los resultados
		$(".categoria").remove();
		return;
	}

	
	//BORRA HIJOS
	if( $("#Padre"+padre).length ){

		console.log("borrando "+padre);
		
		$("#Padre"+padre).fadeOut(500, function(){
			$("#Padre"+padre).remove();
		});
		
		//obtiene los hijos del padre seleccionado
		var queryParams = {'func' : 'GetHijos', 'padre' : padre};
		$.ajax({
			data: queryParams,
			type: "post",
			url: "src/ajaxEdicion.php",
			beforeSend: function(){
				//$("#menu").html('<img id="image-loader" src="images/ajax-loader.gif" />');
			},
			success: function(response){
				if(response.length > 0){
					var hijos = $.parseJSON(response); 
					//alert(response);
					$.each(hijos, function(f,c){
						LimpiarCamino(c);
					});
				}else{
					//no hay hijos que borrar
				}
			},
			fail: function(){
				notificaError("Error: ocurrio un error.<br/>Codigo: ajaxEdicion 001.");
			}
		});
	}

	/*
	//BORRA HERMANOS ASINCRONAMENTE
	var queryParams = {'func' : 'GetHermanos', 'padre' : padre};
	$.ajax({
		data: queryParams,
		//async: false,
		type: "post",
		url: "src/ajaxEdicion.php",
		beforeSend: function(){
			//$("#menu").html('<img id="image-loader" src="images/ajax-loader.gif" />');
		},
		success: function(response){
			if(response.length > 0){
				//alert(response);
				var hermanos = $.parseJSON(response); 
				
				$.each(hermanos, function(f,c){
					if($("#Padre"+c).length ){
						LimpiarCamino(c);						
					}
				});
			}else{
				//no hay hermanos que borrar
			}
		},
		fail: function(){
			notificaError("Error: ocurrio un error.<br/>Codigo: ajaxEdicion 001.");
		}
	}).done(function ( data ) {
		  notifica("LIMPIEZA FINALIZADA"+data);
	});*/

}

function LimpiarHermanos(padre){
	//BORRA HERMANOS ASINCRONAMENTE
	var queryParams = {'func' : 'GetHermanos', 'padre' : padre};
	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxEdicion.php",
		beforeSend: function(){
			//$("#menu").html('<img id="image-loader" src="images/ajax-loader.gif" />');
		},
		success: function(response){
			if(response.length > 0){
				//alert(response);
				var hermanos = $.parseJSON(response); 
				
				$.each(hermanos, function(f,c){
					if($("#Padre"+c).length ){
						LimpiarCamino(c);						
					}
				});
			}else{
				//no hay hermanos que borrar
			}
		},
		fail: function(){
			notificaError("Error: ocurrio un error.<br/>Codigo: ajaxEdicion 001.");
		}
	}).done(function ( data ) {
		  LimpiarCamino(padre);
	});
}

/**
* CONETEXT MENU CATEGORIA
*/
function ContextMenuCategoria(id){

	$.contextMenu({
        selector: '#'+id, 
        callback: function(key, options) {
            var m = "clicked: " + key;
            //window.console && console.log(m) || alert(m); 
            MenuCategoria(m);
        },
        items: {
        	"nueva": {name: "Nueva Subcategoria", icon: "add"},
            //"editar": {name: "Editar", icon: "edit"},
            //"eliminar": {name: "Eliminar", icon: "delete"},
        }
    });
    
    /*$('#'+id).on('click', function(e){
        //console.log('clicked', this);
        var cat = $(this).attr('id');
        alert("Selec "+cat);
	});*/
	
	//PERMITE REASIGNAR EL ID DE LA CATEGORIA A LA COOKIE
	$( $('#'+id) ).mousedown(function(e) {
	    if (e.which === 3) {
	        var cat = $(this).attr('id');
	        $.cookie('categoria', $(this).attr('id') );
	        notifica( $.cookie('categoria') );
	    }
	});
}

/**
* MANEJA EL MENU DE LA CATEGORIA
*/
function MenuCategoria(m){
	var categoria = $.cookie('categoria');
	notifica(categoria);
	if(m == 'clicked: nueva'){
		BoxNuevaCategoria(categoria);
	}
}

/**
* NUEVA SUBCATEGORIA
* @param padre -> padre
*/
function BoxNuevaCategoria(padre){

	var queryParams = {"func" : "BoxNuevaCategoria", "padre" : padre};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxEdicion.php",
		beforeSend: function(){
			$("#vista").append('<img id="image-loader" src="images/ajax-loader.gif" />');
		},
		success: function(response){

			$("#vista").html(response);

			var options = {  
				beforeSend: function(){

				},
		    	success: function(response) { 
			        notifica("Datos Actualizados.");
			    },
			    fail: function(){
			    	notificaError("Error: en ajax al actualizar datos.");
			    }
			};
			
			$('#FormularioSubCategoria').ajaxForm(options);
			$('#FormularioSubCategoria').validationEngine();
		},
		fail: function(){
			notificaError("Error: ocurrio un error.<br/>Codigo: ajaxEdicion 006.");
		}
	});
}

/**
* CARGA LAS GENERALIDADES  DE MANERA DE OPCIONES
*/
function Opciones(){
	var queryParams = {'func' : 'Opciones'};
	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxEdicion.php",
		beforeSend: function(){
			$("#opciones").html('<img id="image-loader" src="images/ajax-loader.gif" />');
		},
		success: function(response){
			$("#opciones").html(response);
		},
		fail: function(){
			notificaError("Error: ocurrio un error.<br/>Codigo: ajaxEdicion 003.");
		}
	});
}

/**
* RESTAURA VISTA
*/
function RestaurarCategorias(){

	var vista = $.cookie('vista');
	notifica(vista);
	if(vista == 'edicion'){
		EditarCategorias();
	}

	if(vista == 'edicionGeneralidades'){
		EditarGeneralidades();
	}
}

/**
* BORRAR EL DATO DE LA CATEGORIA
*/
function BorrarBox(box, id){
	$.cookie('dato', id);
	$.cookie('seleccion', box);

	var si = function (){
		EliminarDato();
	}

	var no = function (){
		notificaAtencion("Operacion cancelada");
	}

	Confirmacion("Esta seguro que desea eliminar el dato.", si, no);

}

/**
* ELIMINA BOX DE DATO NUEVO SIN GUARDAR
*/
function BorrarBoxTemp(box){
	$("#box"+box).animate({
		width: 'toggle',
		height: 'toggle'
	}, 1000, function(){
		$("#box"+box).remove();
	});
}

/**
* ELIMINA UN DATO
*/
function EliminarDato(){
	var id = $.cookie('dato');
	var box = $.cookie('seleccion');
	notifica(box);
	var queryParams = {'func' : 'EliminarDato', "dato" : id};
	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxEdicion.php",
		beforeSend: function(){
			//$("#generalidades").html('<img id="image-loader" src="images/ajax-loader.gif" />');
		},
		success: function(response){
			notifica("Dato Eliminado");

			$("#box"+box).animate({
				width: 'toggle',
				height: 'toggle'
			}, 1000, function(){
				$("#box"+box).remove();
			});

		},
		fail: function(){
			notificaError("Error: ocurrio un error.<br/>Codigo: ajaxEdicion 003.");
		}
	});
}

/**
* CARAGA UN BOX PARA UN DATO
*/
function CargarBox(id){
	if( $("#box"+id).length ){
		notifica('existe');
	}
	//si no existe
	if( !$("#box"+id).length ){
		var queryParams = {'func' : "Box", "campo" : id };
		$.ajax({
			data: queryParams,
			type: "post",
			url: "src/ajaxEdicion.php",
			beforeSend: function(){
				//$("#generalidades").html('<img id="image-loader" src="images/ajax-loader.gif" />');
				notifica('Cargando');
			},
			success: function(response){
				$("#nivel2").append(response);
			},
			fail: function(){
				notificaError("Error: ocurrio un error.<br/>Codigo: ajaxEdicion 003.");
			}
		});
	}	
}

/**
* BOX PARA UN ARCHIVO
*/
function BoxArchivo(){
	if( $("#BoxArchivo").is(":visible") ){
		$("#BoxArchivo").slideUp();
	}else{
		$("#BoxArchivo").slideDown();
	}
}

function DeleteArchivo(){
	var id = $.cookie('archivo');
	notifica('borrando archivo '+id);

	var queryParams = {"func" : "DeleteArchivo", "archivo" : id};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxEdicion.php",
		beforeSend: function(){
		},
		success: function(response){
			notifica("Archivo eliminado");

			$("#archivo"+id).fadeOut(500, function(){
				$("#archivo"+id).remove();
			});

			if( $(".archivo").length == 1){
				notifica('no hay mas');

				$("#archivosAdjuntos").animate({
					width: 'toggle',
					height: 'toggle'
				},1000, function(){
					$("#archivosAdjuntos"+id).remove();
				});
			}
		},
		fail: function(){
			notificaError("Error: en ajaxEdicion.php codigo 004");
		}
	});
}

/**
* DESCARGAR ARCHIVO
*/
function BorrarArchivo(id){
	$.cookie('archivo', id);

	var si = function (){
		DeleteArchivo();
	}

	var no = function (){
		notificaAtencion("Operacion cancelada");
	}

	Confirmacion("Esta seguro que desea eliminar el archivo.", si, no);
}



</script>
			<div class="topControls" >
				
				<!-- menu proyectos -->
				<div id="categoriasControls" >

					<!-- Lista Proyectos -->
					<input type="radio" id="EditarCategorias" name="radio" checked="checked" />
						<label for="EditarCategorias" onClick="EditarCategorias()">
						Categoria
						</label>

					<!-- Nuevo proyecto -->
					<input type="radio" id="EditarGeneralidades" name="radio"/>
						<label for="EditarGeneralidades" onClick="EditarGeneralidades()">
						Generalidades
						</label>
				</div>
				<hr>
				<script type="text/javascript">
					SetBotones('categoriasControls');
				</script>

				<!-- end menu proyectos -->
			</div>

			<div id="vista">
				<!--
				<div id="nivel1">

					<div id="nombreNorma">
						TITULO
					</div>	
					<div id="generalidades">

						<input type="radio" id="radiox" name="radio"/>
						<label for="radio" onClick="xxx()">Lista Proyectos</label>

						<input type="radio" id="radio2" name="radio1"/>
						<label for="radio2" onClick="xxx()">Lista Proyectos</label>

						<input type="radio" id="radio3" name="radio2"/>
						<label for="radio3" onClick="xxx()">Lista Proyectos</label>

					</div>

				</div><!-- end nivel 1- ->

				<div id="nivel2">
						
						<div class="box">
							<div class="titulo">
								Titulo
								<img class="close" src="images/close.png" />
							</div>
							<div class="content">
								TODO ajax para mostrar informacion de subcategorias<br/>
								TODO mansory para acomodar las columnas
							</div>
						</div>
						<div class="box">
							<div class="titulo">
								Titulo
								<img class="close" src="images/close.png" />
							</div>
							<div class="content">
							TODO ajax para mostrar informacion de subcategorias<br/>
							TODO mansory para acomodar las columnas
							</div>
						</div>
						<div class="box">
							<div class="titulo">
								Titulo
								<img class="close" src="images/close.png" />
							</div>
							<div class="content">
								TODO ajax para mostrar informacion de subcategorias<br/>
								TODO mansory para acomodar las columnas
								<br/>
								<br/>
								<br/>
								<br/>
							</div>
						</div>
						<div class="box">
							<div class="titulo">Titulo</div>
							<div class="content">
								TODO ajax para mostrar informacion de subcategorias<br/>
								TODO mansory para acomodar las columnas
							</div>
						</div>

				</div><!-- end nivel 2- ->

			</div>
			-->
			<script type="text/javascript">
					RestaurarCategorias();
			</script>
			<!-- end nivel 1-->