var menu = '';

/**
* PARA EL MENU COMO PANEL DESPLAZABLE CON SCROLL
*/
$(window).scroll(function () { 
      $("#manu .scollers").css("display", "inline").fadeOut("slow"); 
});


$(document).ready(function(){
	//tooltips
	$(document).tooltip({
		tooltipClass: "arrow",
	  	position: {
            my: "center top-70",
            at: "center bottom",
            collision: "flipfit"
        },
        track: false,
        show:{
	    	effect:'slideDown',
	    	delay: 700
		},
		open: function( event, ui ) {
			//se cierran despues de 2 segundos
	    	setTimeout(function(){
	      		$(ui.tooltip).hide('clip');
	   		}, 2000);
	  	}
	});

	$('.dropMenu button').button();
	$('.dropMenu').hide();

	$('#proyectos').click(function(){
		ToolbarMenu('proyectos');
	});

	$('#clientes').click(function(){
		ToolbarMenu('clientes');
	});

	/*$('#edicion').click(function(){
		ToolbarMenu('edicion');
	});*/

	//menu dropdown para usuario
	$('#usuario').click(function(){
		if($('#menuUsuario').is(':visible')){
			$('#menuUsuario').slideUp();
			$('#usuario').css({
				'background-color' : '#fff',
				'color' : '#000'
			});
		}else{
			$('#usuario').css({
				'background-color' : '#a1ca4a',
				'color' : '#fff'
			});
			$('#menuUsuario').slideDown();
		}
	});

	//menu dropdown para edicion
	$('#edicion').click(function(){
		if($('#menuEdicion').is(':visible')){
			$('#menuEdicion').slideUp();
			$('#edicion').css({
				'background-color' : '#fff',
				'color' : '#000'
			});
		}else{
			$('#edicion').css({
				'background-color' : '#a1ca4a',
				'color' : '#fff'
			});
			$('#menuEdicion').slideDown();
		}
	});

	//menu dropdown para clientes
	$('#cliente').click(function(){
		if($('#menuCliente').is(':visible')){
			$('#menuCliente').slideUp();
			$('#cliente').css({
				'background-color' : '#fff',
				'color' : '#000'
			});
		}else{
			$('#cliente').css({
				'background-color' : '#a1ca4a',
				'color' : '#fff'
			});
			$('#menuCliente').slideDown();
		}
	});

	$("#searchForm").validationEngine();
    $('input[placeholder]').placeholder();

    //oculta dialogo
    $('#dialogo').hide();

    //set cookies
    Cookies();

});


/**
* PARA SELECCIONAR UNA VISTA
*/
function ToolbarMenu(click){
	if( click == 'clientes' ){
		$('#toolbar div').removeClass('seleccionado');
		$('#clientes').addClass('seleccionado');
		
		//todo VistaClientes();
	}

	if( click == 'proyectos' ){
		$('#toolbarMenu div').removeClass('seleccionado');
		$('#proyectos').addClass('seleccionado');
		
		VistaProyecto();
	}

	if( click == 'edicion' ){
		$('#toolbarMenu div').removeClass('seleccionado');
		$('#edicion').addClass('seleccionado');
		//VistaEdicion();
	}
}

/**********************
* VISTA DE PROYECTOS
*/

/**
* CARGA LA VISTA DE PROYECTOS
*/
function VistaProyecto(){
	$.cookie('vista', 'proyectos');

	$('#proyectos').addClass('seleccionado');
	
	Loading();

	$("#content").load("ajax/vistaProyectos.php", function(){
		LoadingClose();
		ActivaMenu();
	});
}

/**********************
* VISTA DE CATEGORIAS
*/

/**
* CARGA LA VISTA DE EDICION
*/
function VistaEdicion(accion){
	if($.cookie('vista') != 'edicion' && $.cookie('vista')!= 'edicionGeneralidades'){
		$.cookie('vista', 'edicion');
	}

	if($("#menu2").is(":visible")){
		Menu2();
	}
	//si no se ha cargado
	if( !$(".vistaEdicion").length ){
		
		Loading();

		//se carga solo una vez

			

			if(accion == 'normas'){
				LoadingClose();
				EditarNormas();
			}else if(accion == "categorias"){
				LoadingClose();
				EditarCategorias();
			}else if(accion == "entidades"){
				LoadingClose();
				Entidades();
			}else if(accion == "tipos"){
				LoadingClose();
				Tipos();
			}else{
				LoadingClose();
				//restaura edicion con cookies
				RestaurarEdicion();
			}

			ToolbarMenu('edicion');


	}else{

		if(accion == 'normas'){
			EditarNormas();
		}else if(accion == "categorias"){
			EditarCategorias();
		}else if(accion == "entidades"){
			Entidades();
		}else if(accion == "tipos"){
			Tipos();
		}else{
			//restaura edicion con cookies
			RestaurarEdicion();
		}

		ToolbarMenu('edicion');
	}
}

/**
* CARGAR VISTAEDICION
*/
function CargaVistaEdidcion(){

}

/**
* ACTIVA EL MENU
*/
function ActivaMenu(){
	ActivaMenuFixIe(); //FIX PARA IE

	//ESCONDE
	if( $('#menu').is(':visible') && $.cookie('vista') != 'edicion' && $.cookie('vista') != 'clientes' ){

		$("#menu").animate({
			opacity: 0,
			width: "0%",
		}, { 
			duration: 1500, 
			queue: false,
			complete: function(){
				$("#menu").css({
					'display' : 'none',
					'float' : 'left'
				});
			}
		});


		$("#content").animate({
       		width: '90%',
       		display : 'block'
    	}, { duration: 1500,
    		queue: false,
    		complete: function(){
    			$("#content").css({
					'width' : '90%',
					'margin' : '0',
					'display' : 'block'
				});
    		} 
    	});
		//esconde el segundo menu si esta presente
    	if( $("#menu2").is(":visible") ){
    		$("#menu2").animate({
			opacity: 0,
				//width: 'toggle'
				width: "0%"
			}, { 
				duration: 1500, 
				queue: false,
				complete: function(){
					$("#menu2").css({
						'display' : 'none',
						'float' : 'left',
					});
				}
			});
    	}

		return;
	}

	//MUESTRA
	if( $.cookie('vista') == 'edicion' && !$('#menu').is(':visible') || $.cookie('vista') == 'composicion' || $.cookie('vista') == 'clientes'){

		$("#content").css({
			'margin' : '0',
			'display' : 'inline-block'
		});

		$("#menu").css({
			'display' : 'block',
			width : '0px',
		});

		$("#menu").animate({
			opacity: 1,
			//width: 'toggle'
			width: "30%"
		}, { 
			duration: 1500, 
			queue: false,
			complete: function(){
				$("#menu").css({
					'display' : 'block',
					'float' : 'left',
				});
			}
		});

		$("#content").animate({
	       		width: '60%'
	    	}, { 
	    		duration: 1500, 
	    		queue: false,
	    		complete: function(){
	    			$("#content").css({
						'width' : '60%',
						'margin' : '0',
						'display' : 'inline-block'
					});
	    		}
	    });
	}	
}

/**********************
* VISTA DE COMPOSICION
*/

/**
* CARGA LA VISTA DE COMPOSICION
*/
function VistaComposicion(){
	$.cookie('vista', 'composicion');

	$('#proyectos').addClass('seleccionado');

	ImageLoader();

	notifica('menu activado');

	$("#content").load("ajax/vistaComposicion.php", function(){
		$("#image-loader").remove();
		ActivaMenu();
	});
}

/**
* IMAGEN DE LOADER
*/
function ImageLoader(){
	$("#content").html("");
	$("#content").html('<img id="image-loader" src="images/ajax-loader.gif" />');
}

/**
* DIALOGO DE NOTA
*/
function nota(id){
	$( "#dialogoContenido" ).load('ajax/nuevaNota.php');
	$('#dialogo').hide();
	$('#dialogo').slideDown();
}

/**
* BUSQUEDA 
*/
function Buscar(busqueda){
	var queryParams = {"func" : "Buscar", "busqueda" : busqueda};
	$.ajax({
		data: queryParams,
		url: "src/ajax.php",
		type: "post",
		beforeSend: function(){
			cont.html('<img class="loader" src="http://77digital.com/Desarrollo/dipo/images/loader.gif" alt="cargando" />');
		},
		success: function(response){
			$('resultadoBusqueda').html(response);
		},
		fail: function(){

		}
	});
}

/**
* EDITAR DATOS USUARIO
*/
function EditarAdmin(){
	$( "#dialogoContenido" ).load('ajax/admin.php');
	$('#dialogo').hide();
	$('#dialogo').slideDown();
}

/*
	EDITAR PROYECTOS
*/

//proyecto nuevo
function proyectoNuevo(){
	$( "#dialogoContenido" ).load('ajax/nuevoProyecto.php');
	$('#dialogo').hide();
	$('#dialogo').slideDown();
}

//formulario nuevo proyecto
function nuevoProyecto(){
	//validacion datos
	if ($('#formularioNuevoProyecto').validationEngine('validate')){
		nombre = $('#proyecto').val();
		descripcion = $('#descripcion').val();

		//consulta proyectos del usuario
		proyectos = [];
		valido = false;

		var queryParams = { "func" : 'getProyectos'};
	  	$.ajax({
	        data:  queryParams,
	        url:   'ajax.php',
	        type:  'post',
	        success:  function (response) { 
	        	proyectos = jQuery.parseJSON(response);
	        	valido = validaProyectos(proyectos);

	        	if(valido){
					var queryParams = { "func" : 'nuevoProyecto', "nombre" : nombre, "descripcion" : descripcion};
				  	$.ajax({
				        data:  queryParams,
				        async: false,
				        url:   'ajax.php',
				        type:  'post',
				        success:  function (response) { 
				        	resetMenuProyectos();
				        	notifica('Proyecto creado exitosamente.');
				        	//cierra el dialogo
				        	closeDialogo();
				        }
					});
				}else{
					notificaError('Error el proyecto ya existe.');
				}
	        } 
		});

	}else{
		notificaError('Error datos invalidos.');
	}
}


/*
	PROYECTOS
*/

//muestra la lista de proyectos en un dialogo
function verProyectos(){
	$( "#dialogoContenido" ).load('ajax/listaProyectos.php');
	$('#dialogo').hide();
	$('#dialogo').slideDown();
}

/*
	DIALOGOS
*/

//cierra el dialogo
function closeDialogo(){
	$('#dialogo').slideUp();
}


/*
	NOTIFICACIONES
*/

//usa noty (jquery plugin) para notificar 
function notifica(text) {
  	var n = noty({
  		text: text,
  		type: 'alert',
    	dismissQueue: true,
  		layout: 'topCenter',
  		closeWith: ['button'], // ['click', 'button', 'hover']
  	});
  	//console.log('html: '+n.options.id);
  	
  	//tiempo para desaparecerlo solo 
  	setTimeout(function (){
		n.close();
	},5000);
}

//notificaciones de maxima priridad
function notificaAtencion(text) {
  	var n = noty({
  		text: text,
  		type: 'information',
    	dismissQueue: true,
  		layout: 'topCenter',
  		closeWith: ['button'], // ['click', 'button', 'hover']
  	});
  	//console.log('html: '+n.options.id);
  	
  	//tiempo para desaparecerlo solo 
  	setTimeout(function (){
		n.close();
	},10000);
}


/**
* NOTIFICACION DE ERRORES
*/
function notificaError(text) {
  	var n = noty({
  		text: text,
  		type: 'error',
    	dismissQueue: true,
  		layout: 'topCenter',
  		closeWith: ['button'], // ['click', 'button', 'hover']
  	});
  	//console.log('html: '+n.options.id);
  	
  	//tiempo para desaparecerlo solo 
  	setTimeout(function (){
		n.close();
	},7000);
}

/**
* DIALOGO DE CONFIRMACION
* @param text String para el texto a mostrar en el dialogo
* @param si Object con la funcion a realizar en caso de click en ok
* @param no Object con la funcion en caso de cancelacion
*/
function Confirmacion(text, si, no) {

    var n = noty({
    	text: text,
      	type: 'information',
      	dismissQueue: true,
      	layout: "center",
      	theme: 'defaultTheme',
      	buttons: [
        	{addClass: 'btn btn-primary', text: 'Ok', onClick: function($noty){
        		$noty.close();
        		si();
        		}
        	},
        	{addClass: 'btn btn-danger', text: 'Cancelar', onClick: function($noty){
        		$noty.close();
        		no();
        		}
        	}
      	]
    });
    console.log('html: '+n.options.layout	);
 }


/**
* LOGOUT DEL USUARIO
*/
function LogOut(){
	var queryParams = { "func" : 'LogOut'};
	  	$.ajax({
	        data:  queryParams,
	        url:   'src/ajaxUsuarios.php',
	        type:  'post',
	        success:  function (response) { 
	        	notifica('Hasta la proxima.');
	        		setTimeout(function (){
						$('body').fadeOut(1500, function(){
	        				top.location.href = 'login.php';
	        		});
				},2000);
	        }
		});
}

/**
* SESSION CADUCADA FORZA LOGOUT
*/
function ForceLogOut(){
	notifica('Su session ha caducado.<br/>Loguese de nuevo.');
	setTimeout(function (){
			$('body').fadeOut(1500, function(){
		    	top.location.href = 'login.php';
		});
	},2000);
}

/********************************** HELPERS ******************************/

/**
* LLEVA A HOME
*/
function Home(){

	$("body").fadeOut(500, function() {
		window.location = 'index.php';
	});
}

/**
* INICIALIZA BOTONES
*/
function Botones(){
	$("html button, input:reset, input:submit").button();
}

function Boton(id){
	$("#"+id).button();
}

function SetBotones(id){
	$("#"+id).buttonset();
}

/**
* INICIALIZA TABLA CON ORDENACION
* UTILIZA EL PLUGIN table
*/
function Tabla(id){
	$('#'+id).dataTable( {
		"bDestroy"  : true,
		"bStateSave": true,
		"bPaginate": false,
        "bLengthChange": false
	} );
}

/**
* INICIALIZA LAS COOKIES
*/
function Cookies(){
	if($.cookie('vista') == null){
		$.cookie('proyecto', 0, { expires: 7 });
		$.cookie('vista', 0, { expires: 7 });
		$.cookie('categoria', 0, { expires: 7 });
		$.cookie('dato', 0, { expires: 7 });
		$.cookie('archivo', 0, { expires: 7 });
		$.cookie('seleccion', 0, { expires: 7 });
		$.cookie('accion', 'home',{ expires: 7 });
		$.cookie('restaurado', 0, { expires: 7 });
	}
	Inicializa();
}

/**
* RESTAURA LA VISTA CON COOKIES
*/
function Inicializa(){
	if($.cookie('vista') == 'proyectos'){
		VistaProyecto();
	}

	if($.cookie('vista') == 'edicion'){
		VistaEdicion(0);
	}

	if($.cookie('vista') == 'composicion'){
		VistaComposicion();
	}
}

/**
* CARGA EL EDITOR DE TEXTO ENRIQUESIDO
* LA CONFIGURACION SE ENCUENTRA EN /editor/config.js
*/
function Editor(id){
	var id = document.getElementById(id);
	/*old loader
	CKEDITOR.replace( id );
	CKEDITOR.on("instanceReady", function(event){
			$(".cke_path").remove();
	});
	*/
	var editor = CKEDITOR.instances[id];
    if (editor) {
    	CKEDITOR.remove(editor);
    	editor.destroy(true);
    }

    CKEDITOR.replace(id);
    CKEDITOR.on("instanceReady", function(event){
		$(".cke_path").remove();
	});
}

/*
* ACTUALIZA LOS CAMBIOS ECHOS EN EL EDITOR
*/
function EditorUpdateContent() {
    for (instance in CKEDITOR.instances) {
        CKEDITOR.instances[instance].updateElement();
    }
}

/**
* CREA SELECTOR MULTIPLE CON FILTRO DE BUSQUEDA
* @param id -> id del selector
*/
function SelectorMultipleFiltro(){
	$("select").multiselect().multiselectfilter({
		//filtro
	    filter: function(event, matches){
	        if( !matches.length ){
	            //notificaAtencion("Deve seleccionar almenos una opcion.");
	        }else{
	        	
	        }
	    }
	});
}


/**
* FUNCION PARA MOSTRAR EL LOADER DE JQUERY
*/
function Loading(){
  	$("#loader").css("display" , "block");
}

function LoadingClose(id){
	//$("#loader").css("display" , "none").delay(8000);
	
	$("#loader").animate({
		"display" : "none",
		opacity : 0
	}, { 
		duration: 1500, 
		queue: false,
		complete: function(){
			$("#loader").css({
				'display' : 'none'
			});
		}
	});
}

/**
* FUNCTION GENERICA PARA CANCELAR CUALQUIER ACCION EN #content
*/
function CancelarContent(){
	notificaAtencion("Operacion Cancelada.");

	//limpia el contenido, con effecto
	$("#content").fadeOut(500, function(){
		$("#content").html("");
		$("#content").fadeIn();
	});
	
	//elimina el submit en un form
	$("form").submit(function(e){
		e.preventDefault();
		return false;
	});
}

/**
* LIMPIAR CONTENT
*/
function LimpiarContent(){
	//limpia el contenido, con effecto
	$("#content, #content-disable").fadeOut(500, function(){
		$("#content-disable").remove();
		$("#content").html("");
		$("#content").fadeIn();
	});
}

/**
* DESHABILITA CONTENT
*/
function DeshabilitarContent(){
	$('#content *').prop('disabled', true);
	$("#content").prepend('<div class="content-disable"><p><img src="images/ajax_loader_green_128.gif"/></p></div>');
}

/**
* HABILITA CONTENT SIN LIMPIARLO
*/
function HabilitarContent(){
	$('#content *').prop('disabled', false);
	$("#content").remove();
}

/**
* MUESTRA EL FORM PARA ARCHIVOS ADJUNTOS
*/
function Adjuntos(){

	if($(".adjuntos").is(":visible")){
		$(".adjuntos").slideUp(700);
	}else{
		$(".adjuntos").slideDown(700,function(){
			
			if( !$("#mensajeAdjuntos").is(":visible") ){
				notificaAtencion("<span id='mensajeAdjuntos'></span>Puede adjuntar:<br/>Imagenes,Documentos y comprimidos ZIP");
			}

		});
	}
	
}

/**
* CARGA UN INPUT MAS PARA UN ARCHIVO EXTRA
*/
function AdjuntoExtra(){
	var extra = $(".adjuntos div:last").attr("id");
	extra = extra.substring(7);
	extra = parseInt(extra);
	extra += 1;

	//maximo
	if( extra > 9 ){
		notificaAtencion("Lo sentimos no se permiten mas de 10 archivos adjuntos.");
		return;
	}

	var nuevo = '<div id="archivo'+extra+'" class="adjunto"><hr><span class="adjuntos-boton" onClick="EliminarAdjuntoExtra('+extra+')">-</span><input type="text" name="archivoNombre'+extra+'" placeholder="Nombre" /> <input type="file" name="archivo'+extra+'" /></div>'

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

/**
* FUNCION GENERICA PARA REALIZAR BUSQUEDAS EN EL MENU
* PARA BUSQUEDAS DE NORMAS, ENTIDADES Y TIPOS
*/
function BuscarMenu(id){
	if($("#"+id).is(":visible")){
		$("#"+id).slideUp();
		$("#"+id).val("");
		$("#menu li").fadeIn();
	}else{
		$("#"+id).slideDown();
	}
	
	//busqueda en vivo
	BuscarMenuLive(id);
}

/**
* FUNCION GENERICA PARA REALIZAR BUSQUEDAS EN EL MENU2
* PARA BUSQUEDAS DE ARTICULOS
*/
function BuscarMenu2(id){
	if($("#"+id).is(":visible")){
		$("#"+id).slideUp();
		$("#"+id).val("");
		$("#menu2 li").fadeIn();
	}else{
		$("#"+id).slideDown();
	}
	
	//busqueda en vivo
	BuscarMenu2Live(id);
}

/**
* BUSQUEDA EN VIVO
*/
function BuscarMenuLive(input){
	//actualiza al ir escribiendo
	$("#"+input).keyup(function(){
		var busqueda = $("#"+input).val(), count = 0;

		//recorre opciones para buscar
        $("#menu li").each(function(){
 
            //esconde a los que no coinciden
            if($(this).text().search(new RegExp(busqueda, "i")) < 0){
                $(this).fadeOut();
 
            //sino lo muestra
            } else {
                $(this).show();
                count++;
            }
        });
	});
}

/**
* BUSQUEDA EN VIVO MENU2
*/
function BuscarMenu2Live(input){
	//actualiza al ir escribiendo
	$("#"+input).keyup(function(){
		var busqueda = $("#"+input).val(), count = 0;

		//recorre opciones para buscar
        $("#menu2 li").each(function(){
 
            //esconde a los que no coinciden
            if($(this).text().search(new RegExp(busqueda, "i")) < 0){
                $(this).fadeOut();
 
            //sino lo muestra
            } else {
                $(this).show();
                count++;
            }
        });
	});
}
