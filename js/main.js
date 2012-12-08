var menu = '';

$(document).ready(function(){
	$('.dropMenu button').button();
	$('.dropMenu').hide();

	$('#proyectos').click(function(){
		ToolbarMenu('proyectos');
	});

	$('#clientes').click(function(){
		ToolbarMenu('clientes');
	});

	$('#edicion').click(function(){
		ToolbarMenu('edicion');
	});

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

		VistaEdicion();
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
	ImageLoader();
	$("#content").load("ajax/vistaProyectos.php", function(){
		$("#image-loader").remove();
		ActivaMenu();
	});
}

/**********************
* VISTA DE CATEGORIAS
*/

/**
* CARGA LA VISTA DE EDICION
*/
function VistaEdicion(){
	if($.cookie('vista') != 'edicion' && $.cookie('vista')!= 'edicionGeneralidades'){
		$.cookie('vista', 'edicion');
	}

	$('#edicion').addClass('seleccionado');

	ImageLoader();

	$("#content").load("ajax/vistaEdicion.php", function(){
		$("#image-loader").remove();
	});
}

/**
* ACTIVA EL MENU
*/
function ActivaMenu(){
	ActivaMenuFixIe();

	//esconde
	if( $('#menu').is(':visible') && $.cookie('vista') != 'edicion' ){

		$("#menu").animate({
			width: 'toggle'
		}, 1500, function(){

			$("#menu").css({
				'display' : 'none',
				'float' : 'left',
			});

			$("#content").css({
				'width' : '90%',
				'margin' : '0 auto'
			});

		});

		return;
	}

	//muestra
	if( $.cookie('vista') == 'edicion' && !$('#menu').is(':visible') ){
		
		$("#menu").animate({
			width: 'toggle'
		}, 1500);

		$("#menu").css({
			'display' : 'block',
			'float' : 'left',
		});

		$("#content").css({
			'width' : '60%',
			'margin' : '0 0'
		});
	}

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


//logOut
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
	$("html button").button();
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
	if($.cookie('vista') == 'edicion' || $.cookie('vista') == 'edicionGeneralidades'){
		VistaEdicion();
	}
}

function Editor(id){
	var id = document.getElementById(id);
	CKEDITOR.replace( id );
	CKEDITOR.on("instanceReady", function(event){
		if( $('#uploader').length ){
		}else{
			$(".cke_bottom").append('<img id="uploader" src="images/folder-upload.png" onClick="BoxArchivo()" />');
		}
		
	});
}