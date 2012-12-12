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
	if($('#normas').is(":visible")){
		$('#normas').fadeOut(500, function(){
			$('#normas').remove();
		})
	}

	//OPTIENE EL TAMANO EN PORCENTAJE
	var w = ( 100 * parseFloat($('#menu').css('width')) / parseFloat($('#menu').parent().css('width')) ) + '%';

	if( "30%" <= w ){
		//ANIMACION AL AUMENTAR EL TAMANO DEL MENU
		$("#menu").animate({
	       width: '30%'
	    }, { duration: 500, queue: false });

	    $("#content").animate({
	       width: '60%'
	    }, { duration: 500, queue: false });
	}

	$.cookie('vista', 'edicion');
	ActivaMenu();
	Padres();
}

/**
* INICIALIZA EL FORMULARIO DE EDICION
*/
function FormularioEdicionCategoria(){
		//validacion
		$("#FormularioEdicionCategoria").validationEngine();
		
		var options = {  
			beforeSend: function(){

			},
	    	success: function(response) { 

	    		if(response.length <= 3){
			        notifica("Datos Guardados.");
			        
			        //ACTUALIZA VISTA
			        var id = $("#categoria").val();
			        var padre = $("#categoria").closest("div").attr('id');

			        if(padre == "Padre0"){
			        	Padres();
			        }else{
			        	Hijos(id);
			        	//ACTUALIZA NOMBRE DEL HIJO EDITADO
			        	var nombre = $("#nombre").val();
			        	$("#"+id).html(nombre);
			        }

	    		}else{
	    			notificaError(response);
	    		}
		    },
		    fail: function(){
				notificaError("Error: ocurrio un error :(<br/>Codigo: ajaxEdicion 000.");
		    }
		}; 
		$('#FormularioEdicionCategoria').ajaxForm(options);
}

/**
* CREA EL PANEL DESPLAZABLE DE CATEGORIA
* CARGA SUPERCATEGORIAS
*/
function Padres(){

	var queryParams = {'func' : "Padres"};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxEdicion.php",
		beforeSend: function(){
			//$("#menu").html('<img id="image-loader" src="images/ajax-loader.gif" />');
		},
		success: function(response){
			$("#menu").html(response);
			$("#categorias").hide();
			$("#categorias").fadeIn(1500);
		},
		fail: function(){
			notificaError("Error: ocurrio un error :(<br/>Codigo: ajaxEdicion 001.");
		}
	});

	if( $.cookie('categorias') != "" ){
		categorias = $.cookie('categorias');
	}
}

/**
* CARGA LOS HIJOS DE UN PADRE SELECCIONADO
*/
function Hijos(padre){

	//LIMPIA RUTAS DE CATEGORIAS DE HERMANOS
	LimpiarHermanos(padre);
	//LimpiarCamino(padre);

	var queryParams = {'func' : "Hijos", "padre" : padre};
	
	/*$('#Padre'+padre).removeClass('seleccionada');
	$('#'+padre).addClass('seleccionada');*/

	//SeleccionaHijo(padre);

	//notifica($.cookie('categorias'));

	//carga hijos
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

					$("#categorias").css('width', totalWidth); //aumenta el tamano del contenedor de categorias
				});
				SeleccionaHijo(padre);
			}
		},
		fail: function(){
			notificaError("Error: ocurrio un error :(<br/>Codigo: ajaxEdicion 001.");
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

			$("#BoxArchivo").hide();
		},
		fail: function(){
			notificaError("Error: ocurrio un error.<br/>Codigo: ajaxEdicion 002.");
		}
	});

	//Opciones();
	var padre = $('#'+id).closest('div').attr('id');
	
	if( padre == 'Padre0'){
		//si es supercategoria el menu varia
		ContextMenuSuperCategoria(id);
	}else{
		ContextMenuCategoria(id);
	}

}

/**
* LIMPIA EL CAMINO DEL ARBOL DE CATEGORIAS
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

}

/**
* BORRAR LOS HERMANOS DE UN NODO
* @param padre
*/
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
* CREA EL MENU DE UNA CATEGORIA SELECCIONADA
* @param id -> id de la categoria
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
            "eliminar": {name: "Eliminar", icon: "delete"},
        }
    });
    
    /*$('#'+id).on('click', function(e){
        //console.log('clicked', this);
        var cat = $(this).attr('id');
        alert("Selec "+cat);
	});*/
	
	//PERMITE REASIGNAR EL ID DE LA CATEGORIA A LA COOKIE
	//UTIL PARA TENER MULTIPLES MENUS CONTEXTUALES
	$( $('#'+id) ).mousedown(function(e) {
	    //si es doble click
	    if (e.which === 3) {
	        var cat = $(this).attr('id');
	        $.cookie('categoria', $(this).attr('id') );
	    }

	});
}

/**
* CONTEXT MENU DE SUPERCATEGORIA
* @param id -> id de la categoria
*/
function ContextMenuSuperCategoria(id){

	$.contextMenu({
        selector: '#'+id, 
        callback: function(key, options) {
            var m = "clicked: " + key;
            //window.console && console.log(m) || alert(m); 
            MenuCategoria(m);
        },
        items: {
            "nuevoPadre": {name: "Nueva SuperCategoria", icon: "add"}, //opcion solo para supercategorias
            "nueva": {name: "Nueva Subcategoria", icon: "add"},
            "eliminar": {name: "Eliminar", icon: "delete"},
        }
    });
    
    /*$('#'+id).on('click', function(e){
        //console.log('clicked', this);
        var cat = $(this).attr('id');
        alert("Selec "+cat);
	});*/
	
	//PERMITE REASIGNAR EL ID DE LA CATEGORIA A LA COOKIE
	//UTIL PARA TENER MULTIPLES MENUS CONTEXTUALES
	$( $('#'+id) ).mousedown(function(e) {
	    //si es doble click
	    if (e.which === 3) {
	        var cat = $(this).attr('id');
	        $.cookie('categoria', $(this).attr('id') );
	    }

	});
}

/**
* MANEJA EL MENU DE LA CATEGORIA
* @param m -> opcion seleccionada desde el context menu
*/
function MenuCategoria(m){
	var categoria = $.cookie('categoria');

	if(m == 'clicked: nuevoPadre'){
		BoxNuevaCategoria(0);
	}

	if(m == 'clicked: nueva'){
		BoxNuevaCategoria(categoria);
	}

	//opcion de eliminar la categoria y sus hijos
	if(m == 'clicked: eliminar'){

		var si = function (){
			DeleteCategoria();
		}

		var no = function (){
			notificaAtencion("Operacion cancelada");
		}

		Confirmacion("Esta seguro que desea eliminar la categoria y todos sus subcategorias.", si, no);
	}
}

/**
* ELIMINA UNA CATEGORIA
*/
function DeleteCategoria(){
	//LA CATEGORIA VIENE EN LA COOKIE
	var categoria = $.cookie('categoria');

	var queryParams = {"func" : "DeleteCategoria", "categoria" : categoria};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxEdicion.php",
		beforeSend: function(){
		},
		success: function(response){
			notifica("Categoria Eliminada");
			var padre = $("#"+categoria).closest("div").attr('id');

			$("#"+categoria).fadeOut(500, function(){
				
				//LIMPIA EL CAMINO DE LA CATEGORIA ELIMINADA
				if(padre == 'Padre0'){
					$("#Padre0").fadeOut(500);
					Padres();
				}else{
					 LimpiarCamino(categoria);
					 $("#"+categoria).remove();
				}

			});
		},
		fail: function(){
			notificaError("Error: ocurrio un error :(<br/>Codigo: ajaxEdicion 006, al crear subcategoria.");
		}
	});
}

/**
* CARGA EDITOR PARA NUEVO SUBCATEGORIA
* @param padre -> padre a la que pertenece, padre = 0 entonces es superCategoria
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

			        if(padre == 0 && response.length == 3){
			        	Padres();			        	
			        }else if(response.length == 3) {
			        	Hijos(padre);
			        }else{
			        	$('#FormularioSubCategoria').validate();
			        }
			    },
			    fail: function(){
			    	notificaError("Error: en ajax al crear nueva categoria.");
			    }
			};
			
			$('#FormularioSubCategoria').ajaxForm(options);
			$('#FormularioSubCategoria').validationEngine();
		},
		fail: function(){
			notificaError("Error: ocurrio un error :(<br/>Codigo: ajaxEdicion 006, al crear subcategoria.");
		}
	});
}

/**
* CANCELAR NUEVA CATEGORIA
* @param padre -> id del padre
*/
function CancelarNuevaCateogria(padre){
	$("#FormularioSubCategoria").submit(function(){

		$("#FormularioSubCategoria").fadeOut(700, function(){
			$("#FormularioSubCategoria").remove();
			Categoria(padre);
		});
    	return false;
	});
}

/**
* RESTAURA VISTA UTILIZANDO LAS COOKIES
*/
function RestaurarCategorias(){

	var vista = $.cookie('vista');
	notifica(vista);
	if(vista == 'edicion'){
		EditarCategorias();
	}
}

/**
* BOX PARA ARCHIVOS ADJUNTOS
*/
function BoxArchivo(){
	if( $("#BoxArchivo").is(":visible") ){
		$("#BoxArchivo").slideUp();
	}else{
		$("#BoxArchivo").slideDown();
	}
}

/**
* ELIMINA UN ARCHIVO ADJUNTO
*/
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

			//si ya no hay mas archivos adjuntos se esconde el Box
			if( $(".archivo").length == 1){

				$("#archivosAdjuntos").animate({
					width: 'toggle',
					height: 'toggle'
				},1000, function(){
					$("#archivosAdjuntos"+id).remove();
				});
			}
		},
		fail: function(){
			notificaError("Error: en ajaxEdicion.php codigo 004, al eliminar un archivo adjunto.");
		}
	});
}

/**
* BORRAR UN ARCHIVO, CREA DIALOGO DE CONFIRMACION
*/
function BorrarArchivo(id){
	//SE OCUPA LA COOKIE PARA ENVIAR EL ID A DIALOGO DE CONFIRMACION
	$.cookie('archivo', id);

	var si = function (){
		DeleteArchivo();
	}

	var no = function (){
		notificaAtencion("Operacion cancelada");
	}

	Confirmacion("Esta seguro que desea eliminar el archivo.", si, no);
}

/**************************************** NORMAS ***************************/

/**
* EDICION DE NORMAS
*/
function EditarNormas(){
	if($('#categorias').is(":visible")){
		$('#categorias').fadeOut(500, function(){
			$('#categorias').remove();
		})
	}

	$.cookie('accion', 'normas');
	
	//OPTIENE EL TAMANO EN PORCENTAJE
	var w = ( 100 * parseFloat($('#menu').css('width')) / parseFloat($('#menu').parent().css('width')) ) + '%';

	if( w <= "30%"){
		//ANIMACION AL AUMENTAR EL TAMANO DEL MENU
		$("#menu").animate({
	       width: '45%'
	    }, { duration: 500, queue: false });

	    $("#content").animate({
	       width: '45%'
	    }, { duration: 500, queue: false });
	}

	//CARGA CONTENIDO
	if( $("#vista").is(":visible") ){
		$("#vista").html("");
		Normas();
	}else{
		Normas();
	}
}
	
/**
* CARGAS TODAS LAS NORMAS DEL ARBOL
*/
function Normas(){
	var queryParams = {"func" : "Normas" };

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxEdicion.php",
		beforeSend: function(){
		},
		success: function(response){
			$("#menu").html(response);
			$("#normas").hide();
			$("#normas").fadeIn(1500);
		},
		fail: function(){
			notificaError("Error: En ajaxEdidicon.php al mostrar las normas.")
		}
	});
}

/**
* CARAGA UNA NORMA HIJA SELECCIONADA
* @param padre-> id de la norma padre seleccionada
*/
function Norma(padre){
	notifica(padre);

	//LIMPIA RUTAS DE NORMAS HERMANAS
	LimpiarHermanosNorma(padre);

	var queryParams = {"func" : "Norma", "padre" : padre};
	
	//carga una norma hija
	$.ajax({
		data: queryParams,
		type: "post",
		async: false,
		url: "src/ajaxEdicion.php",
		beforeSend: function(){
			$("#normas").append('<img id="image-loader" style="display: inline-block;" src="images/ajax-loader.gif" />');
		},
		success: function(response){
			if(response.length > 0){

				$("#image-loader").fadeOut(500, function(){
					$("#image-loader").remove();
					$("#normas").append(response);

					$("#Padre"+padre).hide();
					$("#Padre"+padre).fadeIn(500);

					var totalWidth = 0;

					$('.categoria').each(function(index) {
						totalWidth += parseInt($(this).width(), 10);
					});
					
					totalWidth += $("#Padre0").width() + 100;

					$("#normas").css('width', totalWidth); //aumenta el tamano del contenedor de categorias
				});
				SeleccionaHijo(padre);
			}
		},
		fail: function(){
			notificaError("Error: ocurrio un error :(<br/>Codigo: ajaxEdicion 001.");
		}
	});
}

/**
* LIMPIA EL CAMINO DEL ARBOL DE NORMAS
* @param padre -> id del padre
*/
function LimpiarCaminoNorma(padre){
	//obtiene el padre del padre, para ver si no es root
	var Padre = $("#"+padre).closest("div").attr("id");

	if(Padre == "Padre0"){ //si es root entonces limpia todos los resultados
		$(".categoria").remove();
		return;
	}

	//BORRA HIJOS DE UNA NORMA
	if( $("#Padre"+padre).length ){

		console.log("borrando "+padre);
		
		$("#Padre"+padre).fadeOut(500, function(){
			$("#Padre"+padre).remove();
		});
		
		//obtiene los hijos del padre seleccionado
		var queryParams = {'func' : 'GetHijosNorma', 'padre' : padre};
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

}

/**
* BORRAR LOS HERMANOS DE UN NODO PARA NORMAS
* @param padre
*/
function LimpiarHermanosNorma(padre){
	//BORRA HERMANOS ASINCRONAMENTE
	var queryParams = {'func' : 'GetHermanosNorma', 'padre' : padre};
	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxEdicion.php",
		beforeSend: function(){
		},
		success: function(response){
			if(response.length > 0){
				//alert(response);
				var hermanos = $.parseJSON(response); 
				
				$.each(hermanos, function(f,c){
					if( $("#Padre"+c).length ){ //EXISTEN HIJOS
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


</script>
<!-- FIN JAVASCRIPT -->

			<div class="topControls" >
				
				<!-- menu proyectos -->
				<div id="categoriasControls" >

					<!-- Lista Proyectos -->
					<input type="radio" id="EditarCategorias" name="radio" checked="checked" />
						<label for="EditarCategorias" onClick="EditarCategorias()">
						Categoria
						</label>

					<!-- Nuevo proyecto -->
					<input type="radio" id="EditarNormas" name="radio"/>
						<label for="EditarNormas" onClick="EditarNormas()">
						Normas
						</label>

				</div>
				<hr> 
				<script type="text/javascript">
					SetBotones('categoriasControls');
				</script>
				<! -- end menu proyectos -->
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