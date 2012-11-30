var turno = '';

$(document).ready(function(){
	$('.dropMenu button').button();
	$('.dropMenu').hide();

	/*$('#proyectos').click(function(){
		if($('#menuProyectos').is(':visible')){
			$('#menuProyectos').slideUp();
			$('#proyectos').css({
				'background-color' : '#fff',
				'color' : '#000'
			});
		}else{
			$('#proyectos').css({
				'background-color' : '#a1ca4a',
				'color' : '#fff'
			});
			$('#menuProyectos').slideDown();
		}
	});*/
	$('#proyectos').click(function(){
		ToolbarMenu('proyectos');
	});

	$('#clientes').click(function(){
		ToolbarMenu('clientes');
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
		$('#clientes').addClass('seleccionado');
		$('#proyectos').removeClass('seleccionado');
		turno = 'clientes';
		//console.log('cliente');
		//todo VistaClientes();
	}

	if( click == 'proyectos' ){
		$('#proyectos').addClass('seleccionado');
		$('#clientes').removeClass('seleccionado');
		//console.log('proyecto');
		VisatProyectos();
	}
}

/**
	VISTA DE PROYECTOS
*/

/**
* CARGA LA VISTA DE PROYECTOS
*/
function VisatProyectos(){
	$.cookie('vista', 'proyectos');
	$('#proyectos').addClass('seleccionado');
	$("#content").html("");
	$("#content").load("ajax/VistaProyectos.php");
}

//LIMPIA MUESTRAS DE AJAX
function reset(){
	$('#content #nivel1, #content #nivel2, #resumen, #edicion, #compartir').remove();
	//remueve consultas de generalidades
	$('.box').remove();
}


/**
* DIALOGO DE NOTA
*/
function nota(id){
	$( "#dialogoContenido" ).load('ajax/nuevaNota.php');
	$('#dialogo').hide();
	$('#dialogo').slideDown();
}

/*
	BUQUEDA 
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

/*
	EDITAR DATOS USUARIO
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
*/
function Confirmacion(text, si, no) {

    var n = noty({
    	text: text,
      	type: 'alert',
      	dismissQueue: true,
      	layout: 'topCenter',
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
    //console.log('html: '+n.options.id);
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

/**
* INICIALIZA TABLA CON ORDENACION
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
	/*$('#'+id).ajaxForm(function() { }); 
	// prepare Options Object 
	var options = {  
	    success:    function(response) { 
	        notifica("Datos Actualizados correctamente."+response) 
	    } 
	}; 
	 
	// pass options to ajaxForm 
	$('#'+id).ajaxForm(options);
	*/
}


function Cookies(){
	if($.cookie('vista') == null){
		$.cookie('proyecto', 0, { expires: 7 });
		$.cookie('vista', 0, { expires: 7 });
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
		VisatProyectos();
	}
}
