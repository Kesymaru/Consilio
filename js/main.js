var menu = '';

/**
* PARA EL MENU COMO PANEL DESPLAZABLE CON SCROLL
*/
$(window).scroll(function () { 
      $("#manu .scollers").css("display", "inline").fadeOut("slow"); 
});


$(document).ready(function(){
	//tooltips
	/*$(document).tooltip({
		tooltipClass: "arrow",
	  	position: {
            my: "center bottom-20",
            at: "center top",
        },
        track: true,
        show:{
	    	effect:'slideDown',
	    	delay: 1000
		},
		open: function( event, ui ) {
			//se cierran despues de 5 segundos
	    	setTimeout(function(){
	      		$(ui.tooltip).hide('clip');
	   		}, 5000);
	  	}
	});*/

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
		$("#content").load("ajax/vistaEdicion.php", function(){
			LoadingClose();

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
		});

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
	if( $('#menu').is(':visible') && $.cookie('vista') != 'edicion' ){

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
	if( $.cookie('vista') == 'edicion' && !$('#menu').is(':visible') || $.cookie('vista') == 'composicion'){

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

/*** FUNCIONES GENERICAS ***/

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

function SetDatePicker(clase){
	 $( "."+clase ).datepicker();
}

function SetTimePicker(clase){
	$( "."+clase ).timepicker();
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

/** INICIALIZA VALIDACION FORMULARIO **/
function Formulario(id){
	$("#"+id).validationEngine();
}

function ScrollBar(id){
	$("#"+id).scrollbar();
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
	var id2 = id;
	var id = document.getElementById(id);

	if($.browser.msie && $.browser.version < 9.0 ){
		//no crea el editor si es ie8 o inferior
		return;
		//$("#"+id2).addClass("CKEditor ");
		//$("#"+id).attr("title","Edicon texto plano.");
		
		//espera hasta que sea visible y entonces inicializa el editor
		/*if(!$("#"+id2).is(":visible")){
			setTimeout(function() {
				if($("#"+id2).is(":visible")){
					notifica("es visible"+id2);
					//CKEDITOR.replace( id );
					return;
				}
			}, 1000);
		}else{
			CKEDITOR.replace( id );
			return;
		}*/
	}

	CKEDITOR.replace( id );
	CKEDITOR.on("instanceReady", function(event){
		if( $('#uploader').length ){
		}else{
			$(".cke_path").remove();
		}
		
	});
}

/*
* ACTUALIZA LOS CAMBIOS ECHOS EN EL EDITOR
*/
function EditorUpdateContent() {
	notificaAtencion("actualizando editores");
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
	        	notifica('hay');
	        }
	    }
	});
	$(".ui-multiselect span:last").html("Entidades");
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