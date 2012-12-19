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
	    }, { 
	    	duration: 500, 
	    	queue: false,
	    	complete: function(){
	    		notifica('asignando');
	    		notifica($("#menu").css("overflow"));
	    	}
	    });

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

   	//esconde el segundo menu
    if( $("#menu2").is(":visible") ){
    	Menu2();
	}

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
function RestaurarEdicion(){

	var vista = $.cookie('vista');
	var accion = $.cookie('accion');

	if(vista == 'edicion'){
		if(accion == 'categorias'){
			EditarCategorias();
		}else if(accion == 'normas'){
			EditarNormas();
		}else if(accion == 'entidades'){

		}else if(accion == 'tipos'){

		}else{

		}
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

/******************************** NORMAS ******************/

/**
* EDICION DE NORMAS
*/
function EditarNormas(){
	$.cookie('accion', 'normas');

	if($('#categorias').length ){
		$('#categorias').fadeOut(500, function(){
			$('#categorias').remove();
		});
	}
	
	if( !$("#menu").is(":visible") ){
		ActivaMenu();
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
* CARGA LAS NORMAS EN EL MENU
*/
function Normas(){

	var queryParams = {"func" : "Normas"};

	$.ajax({
		data: queryParams,
		type: "post",
		async: false,
		url: "src/ajaxNormas.php",
		beforeSend: function(){
		},
		success: function(response){
			if( $("#normas").is(":visible") ){

				$("#normas").fadeOut(500, function(){
					$("#normas").remove();

					$("#menu").append(response);
					$("#normas").hide();
					$("#normas").fadeIn(1000);

					$("#DeshabilitarNorma, #EditarNorma, #AgregarArticulo, #HabilitarNorma ").hide();
				});

			}else{
				$("#menu").append(response);
				$("#normas").hide();
				$("#normas").fadeIn(1000);

				$("#DeshabilitarNorma, #EditarNorma, #AgregarArticulo, #HabilitarNorma ").hide();
			}
		},
		fail: function(){

		}
	});
}

/**
* CARGA LAS NORMAS Y SELECCIONA UNA
* @param id -> id de la norma seleccionada
*/
function NormaSeleccionada(id){
	var queryParams = {"func" : "Normas"};
	
	$.ajax({
		data: queryParams,
		type: "post",
		async: false,
		url: "src/ajaxNormas.php",
		beforeSend: function(){
		},
		success: function(response){
			if( $("#normas").is(":visible") ){

				$("#normas").fadeOut(500, function(){
					$("#normas").remove();

					$("#menu").append(response);
					$("#normas").hide();
					$("#normas").fadeIn(1000);

					NormaOpciones(id);
				});

			}else{
				$("#menu").append(response);
				$("#normas").hide();
				$("#normas").fadeIn(1000);

				NormaOpciones(id);
			}
		},
		fail: function(){

		}
	});

	
}

/**
* CREA EL MENU CONTEXTUAL PARA UNA NORMA SELECCIONADA
*/
function NormaOpciones(id){

	SeleccionaHijo(id);
	
	//MUESTRA BOTONES
	if( $("#"+id).hasClass("deshabilitado") ){ //NORMAS DESHABILITADAS

		$("#DeshabilitarNorma").hide();
		$("#HabilitarNorma, #EditarNorma, #AgregarArticulo").fadeIn();

		ContextMenuNormaDeshabilitada(id);

	}else{ //NORMAS HABILITADAS
	    
		$("#HabilitarNorma").hide();
		$("#DeshabilitarNorma, #EditarNorma, #AgregarArticulo").fadeIn();

		ContextMenuNorma(id);
	}
	
}

/**
* CONETEXT MENU NORMA HABILITADA
* CREA EL MENU PARA UNA NORMA SELECCIONADA
* @param id -> id de la categoria
*/
function ContextMenuNorma(id){

	$.contextMenu({
        selector: '#'+id, 
        callback: function(key, options) {
            var m = "clicked: " + key;
            //window.console && console.log(m) || alert(m); 
            MenuNorma(m);
        },
        items: {
        	"nueva": {name: "Nueva Norma", icon: "add"},
            "editar": {name: "Editar", icon: "edit"},
            "deshabilitar": {name: "Deshabilitar", icon: "delete"},
            "sep1": "---------",
	        "articulos": {name: "Articulos", icon: "add"},
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
* CONETEXT MENU NORMA
* CREA EL MENU PARA UNA NORMA SELECCIONADA
* @param id -> id de la categoria
*/
function ContextMenuNormaDeshabilitada(id){

	$.contextMenu({
        selector: '#'+id, 
        callback: function(key, options) {
            var m = "clicked: " + key;
            //window.console && console.log(m) || alert(m); 
            MenuNorma(m);
        },
        items: {
			"nueva": {name: "Nueva Norma", icon: "add"},
            "editar": {name: "Editar", icon: "edit"},
            "habilitar": {name: "Habilitar", icon: "delete"},
            "sep1": "---------",
	        "articulos": {name: "Articulos", icon: "add"},
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
function MenuNorma(m){
	var categoria = $.cookie('categoria');

	if(m == 'clicked: nueva'){
		NuevaNorma();
	}

	if(m == 'clicked: editar'){
		EditarNorma();
	}

	if(m == 'clicked: deshabilitar'){
		DeshabilitarNorma();
	}

	if(m == 'clicked: habilitar'){
		HabilitarNorma();
	}

	if(m == 'clicked: articulos'){
		var norma = $(".seleccionada").attr('id');
		Articulos(norma);
	}
}


/**
* CARGA EL FORMULARIO DE EDICION DE UNA NUEVA NORMA
* @param 
*/
function NuevaNorma(){
	
	var queryParams = {"func" : "NuevaNorma"};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxNormas.php",
		beforeSend: function(){
		},
		success: function(response){
			$("#content").html(response);
			FormularioNuevaNorma();
		},
		fail: function(){

		}
	});

}

function FormularioNuevaNorma(){
	//validacion
	$("#FormularioNuevaNorma").validationEngine();
		
	var options = {  
		beforeSend: function(){
		},
	    success: function(response) { 

	    	if(response.length == 3){
	    		notifica("Norma Creada.");
	    	}else{
	    		$("html").html(response);
	    		$("#FormularioNuevaNorma").validate();
	    	}
		},
		fail: function(){
			notificaError("Error: ocurrio un error :(<br/>Codigo: ajaxEdicion 000.");
		}
	}; 
	$('#FormularioNuevaNorma').ajaxForm(options);
}


/**
* EDITAR NORMA
* el id de la norma se obtiene de la cookie
*/
function EditarNorma(){
	var norma = $(".seleccionada").attr('id');

	var queryParams = {"func" : "EditarNorma", "norma" : norma};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxNormas.php",
		beforeSend: function(){
		},
		success: function(response){

			//esconde el segundo menu si esta presente
			if( $("#menu2").is(":visible")){
				Menu2();
			}

			$("#content").html(response);

			//carga formulario
			FormularioNorma();
		},
		fail: function(){

		}
	});
}

/**
* INICIALIZA EL FORMULARIO DE EDICION
*/
function FormularioNorma(){
		//validacion
		$("#FormularioNorma").validationEngine();
		
		var options = {  
			beforeSend: function(){

			},
	    	success: function(response) { 

	    		if(response.length == 3){
	    			notifica("Norma Actualizada");
	    			var norma = $("#norma").val();
	    			var nombre = $("#nombre").val();
	    			var numero = $("#numero").val();

	    			//ACTUALIZA LA NORMA EN LA LISTA
	    			$("#"+norma).html(nombre+" "+numero);

	    			//quita el formulario
	    			$("#content").html("");

	    		}else{
	    			notificaError(response);
	    			$("#FormularioNorma").validate();
	    		}
		    },
		    fail: function(){
				notificaError("Error: ocurrio un error :(<br/>Codigo: ajaxEdicion 000.");
		    }
		}; 
		$('#FormularioNorma').ajaxForm(options);
}

/**
* DESHABILITAR NORMA, MUESTRA EL CUADRO DE DIALOGO
* @param norma -> id de la norma ha deshabilitar
*/
function DeshabilitarNorma(){
	var norma = $(".seleccionada").attr('id');

	var si = function (){
		DeshabilitaNorma(norma);
	}

	var no = function (){
		notificaAtencion("Operacion cancelada");
	}

	Confirmacion("Esta seguro que desea deshabilitar la norma.", si, no);

}

/**
* DESHABILITA LA NORMA
* @param norma -> id de la norma
*/
function DeshabilitaNorma(norma){
	$("#"+norma).addClass("deshabilitado");
	NormaOpciones(norma);

	var queryParams = {"func" : "DeshabilitarNorma", "norma" : norma};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxNormas.php",
		beforeSend: function(){
		},
		success: function(response){
			notifica("Norma Deshabilitada.");
		},
		fail: function(){

		}
	});
}

/**
* DESHABILITAR NORMA, MUESTRA EL CUADRO DE DIALOGO
* @param norma -> id de la norma ha deshabilitar
*/
function HabilitarNorma(){
	var norma = $(".seleccionada").attr('id');

	var si = function (){
		HabilitaNorma(norma);
	}

	var no = function (){
		notificaAtencion("Operacion cancelada");
	}

	Confirmacion("Esta seguro que desea Habilitar la norma.", si, no);

}

/**
* DESHABILITA LA NORMA
* @param norma -> id de la norma
*/
function HabilitaNorma(norma){
	$("#"+norma).removeClass("deshabilitado");
	NormaOpciones(norma);

	var queryParams = {"func" : "HabilitarNorma", "norma" : norma};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxNormas.php",
		beforeSend: function(){
		},
		success: function(response){
			notifica("Norma Habilitada.");
		},
		fail: function(){

		}
	});

}

/*
* MUESTRA EL SEGUNDO MENU
*/
function Menu2(){
	//OPTIENE EL TAMANO EN PORCENTAJE
	var w = ( 100 * parseFloat($('#menu').css('width')) / parseFloat($('#menu').parent().css('width')) ).toFixed() + '%';

	if( w == "30%"){
		if( !$("#menu2").is(":visible") ){
			$("#menu2").css({
				"display"    : "block",
				"margin-left": "0",
				"width"      : "0"
			});
		}

		//ANIMACION AL AUMENTAR EL TAMANO DEL MENU2
		$("#menu").animate({
	       width: '20%',
	    }, { duration: 500, queue: false });

	    $("#menu2").animate({
	       width: '20%'
	    }, { 
	    	duration: 500, 
	    	queue: false,
	    	complete: function(){
	    		$("#menu2").css({
					"display" : "block",
					"opacity" : "1"
				})
	    	}
	    });

	    $("#content").animate({
	       width: '50%'
	    }, { duration: 500, queue: false });

	}else{
		//ESCONDE EL SEGUNDO MENU
		$("#menu").animate({
	       width: '30%'
	    }, { duration: 500, queue: false });

	    $("#menu2").animate({
	       width: '0%'
	    }, { 
	    	duration: 500, 
	    	queue: false,
	    	complete: function(){
	    		$("#menu2").css({
					"display": "none",
					"width"  : "0"
				})
	    	}
	    });

	    $("#content").animate({
	       width: '60%'
	    }, { duration: 500, queue: false });
	}
}

/********************************* ARTICULOS *********************/

/**
* CARGA LOS ARTICULOS DE UNA NORMA SELECCIONADA
* @param $norma -> id de la norma seleccionada
*/
function Articulos(norma){

	//MUESTRA EL PANEL PARA LOS ARTICULOS CON ANIMACION
	if( !$("#menu2").is(":visible") ){
		Menu2();
	}

	//BORRA CONTENT
	$("#content").html("");

	var queryParams = {"func" : "Articulos", "norma" : norma};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxNormas.php",
		beforeSend: function(){
		},
		success: function(response){

			if( $("#articulos").length ){
				notifica("existian articulos");

				$("#articulos").fadeOut(500, function(){
					$("#articulos").remove();
					$("#menu2").append(response);
				});

			}else{

				$("#menu2").append(response);

			}
		},
		fail: function(){
		}
	});
}

/**
* AGREGAR UN ARTICULO
* @param norma -> id de la norma
*/
function NuevoArticulo(norma){
	
	var queryParams = {"func" : "NuevoArticulo", "norma" : norma};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxNormas.php",
		beforeSend: function(){
		},
		success: function(response){
			$("#content").html(response);
			$(".adjuntos").hide();
			FormularioNuevoArticulo();
		},
		fail: function(){
			notificaError("Ocurrion un error :(<br/>Al cargar edicion de nueva norma.");
		}
	});
	
}

/**
* INICIALIZA FORMULARIO PARA NUEVO ARTICULO
*/
function FormularioNuevoArticulo(){

	var options = {  
		beforeSubmit: ValidaFormularioNuevoArticulo,
		beforeSend: function(){
			EditorUpdateContent();
		},
		success: function(response) { 
			if(response.length == 3){
				notifica("Articulo Creado.");
				$("#content").html("");
				var norma = $(".seleccionada").attr('id');
				Articulos(norma);
			}else{
				notificaError(response);
			}
		},
		fail: function(){
			notificaError("Error: ocurrio un error :(<br/>Al crear el nuevo articulo.");
		}
	}; 

	$('#FormularioNuevoArticulo').ajaxForm(options);


	//CARGA EDITORES PARA LOS TEXTAREAS
	Editor('resumen');
	Editor('permisos');
	Editor('sanciones');
	Editor('articulo');
	
	$( "#tabs" ).tabs(); //crea tabs para los textareas
}

/**
* REALIZA VALIDACION DE DATOS PARA NUEVO ARTICULO
*/
function ValidaFormularioNuevoArticulo(){
	EditorUpdateContent();
	
	//VALIDACION MANUAL
	var permisos = $("#permisos").val();
	var articulo = $("#articulo").val();
	var entidades = $("#entidades").val();
	var nombre = $("#nombre").val();

	if( permisos != '' && permisos != null && articulo != '' && articulo != null && entidades != '' && entidades != null && nombre != '' && nombre != null ){

		//return true;
	}else{
				
		if(permisos == null || permisos == ''){
			notificaAtencion("Se requieren los permisos para el articulo.");
		}
		if(articulo == null || articulo == ''){
			notificaAtencion("Se requiere un articulo.");
		}
		if(entidades == null || entidades == ''){
			notificaAtencion("Se requiere almenos una entidad.");
		}
		if(nombre == null || nombre == ''){
			notificaAtencion("Se requiere un nombre para el articulo.");
		}
			return false;
	}    
}

/**
*SELECCIONA UN ARTICULO
*/ 
function SelectArticulo(articulo){
	
	$("#articulos li").removeClass("seleccionada");
	$("#articulo"+articulo).addClass("seleccionada");

	//INICIALIZA EL MENU
	ArticuloContextMenu(articulo);
}

/**
* CONTEXT MENU PARA UN ARTICULO SELECCIONADO
*/
function ArticuloContextMenu(id){

	
}

/**
* PARA BORRAR UN ARTICULO
* EL ID DEL ARTICULO SE OBTIENE DESDE EL DOM
*/
function BorrarArticulo(){
	var articulo = $("#articulos .seleccionada").attr("id");

	articulo = articulo.substring(8); //elimina "articulo" del id y deja solo el numero

	var si = function (){
		DeleteArticulo();
	}

	var no = function (){
		notificaAtencion("Operacion cancelada");
	}

	Confirmacion("Esta seguro que desea Eliminar el articulo y todos sus datos.", si, no);

}

/**
* REALIZA EL BORRADO DE UN ARTICULO AL SER CONFIRMADA LA OPCION
* @param articulo -> id del articulo
*/
function DeleteArticulo(id){
	var articulo = $("#articulos .seleccionada").attr("id");
	articulo = articulo.substring(8); //elimina "articulo" del id y deja solo el numero

	var queryParams = {"func" : "BorrarArticulo", "articulo" : articulo };

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxNormas.php",
		beforeSend: function(){

		},
		success: function(response){
			if(response.length == 3){
				notifica("Articulo Eliminado.");
				$("#articulo"+articulo).fadeOut(500, function(){
					$("#articulo"+articulo).remove();
				});
			}else{
				responseError(response);
			}
		},
		fail: function(){
			notificaError("Ocurrio un error al eliminar el articulo.<br/>Intentelo de nuevo.");
		}
	})
}

/**
* EDITA UN ARTICULOs
*/
function EditarArticulo(){
	var articulo = $("#articulos .seleccionada").attr("id");
	articulo = articulo.substring(8); //elimina "articulo" del id y deja solo el numero

	var queryParams = {"func" : "EditarArticulo", "articulo" : articulo};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxNormas.php",
		beforeSend: function(){
			Loading();
		},
		success: function(response){
			if(response.length){
				LoadingClose();

				$("#content").html(response);
				$("#content").hide();
				$("#content").fadeIn();
				FormularioEditarArticulo();

			}else{
				notificaError(response);
			}
		},
		fail: function(){
			LoadingClose();
			notificaError("Ocurrio un error :(<br/>Al intentar cargar la edicion del articulo.");
		}
	});
}

/**
* INICIALIZA FORMULARIO PARA NUEVO ARTICULO
*/
function FormularioEditarArticulo(){

	var options = {  
		beforeSubmit: ValidaFormularioNuevoArticulo, //se valida con la misma funcion que al crear nuevo articulo
		beforeSend: function(){
		},
		success: function(response) { 

			if(response.length == 3){
				notifica("Articulo Actualizado.");
				//$("#content").html("");
			}else{
				notificaError(response);
			}
		},
		fail: function(){
			notificaError("Error: ocurrio un error :(<br/>Al crear el nuevo articulo.");
		}
	}; 

	$('#FormularioEditarArticulo').ajaxForm(options);


	//CARGA EDITORES PARA LOS TEXTAREAS
	Editor('resumen');
	Editor('permisos');
	Editor('sanciones');
	Editor('articulo');
	
	$( "#tabs" ).tabs(); //crea tabs para los textareas
}


/********************************** HELPERS ******************************/

/**
* FUNCTION GENERICA PARA CANCELAR CUALQUIER ACCION EN #content
*/
function CancelarContent(){
	
	$("form").submit(function(){
		notificaAtencion("Operacion Cancelada");
		$("#content").html("");
		return false;
	});

}

/**
* MUESTRA EL FORM PARA ARCHIVOS ADJUNTOS
*/
function Adjuntos(){
	$(".adjuntos").slideDown(700,function(){
		notificaAtencion("Puede adjuntar:<br/>Imagenes,Documentos y comprimidos ZIP");
	});
}

/**
* CARGA UN INPUT MAS PARA UN ARCHIVO EXTRA
*/
function AdjuntoExtra(){
	var extra = $(".adjuntos div:last").attr("id");
	notifica(extra);
	extra = extra.substring(7);
	extra = parseInt(extra);
	extra += 1;

	//maximo
	if( extra > 9 ){
		notificaAtencion("Lo sentimos no se permiten mas de 10 archivos adjuntos.");
		return;
	}

	notifica(extra);
	var nuevo = '<div id="archivo'+extra+'" class="adjunto"><hr><span class="adjuntos-boton" onClick="EliminarAdjuntoExtra('+extra+')">-</span><input type="file" name="archivo'+extra+'"></div>'

	$(".adjuntos").append(nuevo);
	$("#archivo"+extra).hide();
	$("#archivo"+extra).slideDown(700);
}

/**
* BORRA UN INPUT EXTRA PARA UN ADJUNTO
*/
function EliminarAdjuntoExtra(id){
	$("#archivo"+id).slideUp(700, function(){
		$("#archivo"+id).remove();
	});
}

</script>
<!-- FIN JAVASCRIPT -->

			<div class="topControls" >
				
				<!-- menu proyectos -->
				<div id="categoriasControls" >

					<!-- Lista Proyectos 
					<input type="radio" id="EditarCategorias" name="radio" checked="checked" />
						<label for="EditarCategorias" onClick="EditarCategorias()">
						Categorias
						</label>

					<! -- Nuevo proyecto 
					<input type="radio" id="Editarvars" name="radio"/>
						<label for="Editarvars" onClick="EditarNormas()">
						Normas
						</label>

				</div>
				<hr> 
				<script type="text/javascript">
					SetBotones('categoriasControls');
				</script>
				<! -- end menu proyectos -->
			</div>

			<div id="vista" class="vistaEdicion" >
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
					//RestaurarCategorias();
			</script>
			<!-- end nivel 1-->