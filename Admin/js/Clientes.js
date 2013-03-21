/**
* JAVASCRIPT PARA VISTA DE CLIENTES
*/

/**
* CARGA LA EDICION DE CLIENTES
*/
function Clientes(){
	$.cookie('vista', 'clientes');

	$.contextMenu( 'destroy' ); //limpia context menus de otras vistas

	if($("#menu2").is(":visible")){
		Menu2();
	}

	if(!$("#menu").is(":visible")){
		console.log("mostrando menu");
		ActivaMenu();
	}

	if($("#content").html("") != ""){
		LimpiarContent();		
	}

	var queryParams = {"func" : "Clientes"};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxClientes.php",
		beforeSend: function(){
			Loading();
		},
		success: function(response){
			LoadingClose();
			$("#menu").html(response);
			MenuScroll();
			$("#EliminarCliente, #EditarCliente").hide();
		},
		fail: function(){
		}
	});
}

/**
* SELECCIONA UN CLIENTE
*/
function SelectCliente(id){
	$("#clientes li").removeClass("seleccionada");
	$("#"+id).addClass("seleccionada");

	if(!$("#EliminarCliente, #EditarCliente").is(":visible")){
		$("#EliminarCliente, #EditarCliente").fadeIn();
	}

	ContextMenuCliente(id);
}

/**
* CARGA EL CONTEXT MENU DE UN CLIENTE SELECCIONADO
*/
function ContextMenuCliente(id){
	$.contextMenu({
        selector: '#'+id, 
        callback: function(key, options) {
            var m = "clicked: " + key;
            //window.console && console.log(m) || alert(m); 
            MenuCliente(m, id);
        },
        items: {
			"nuevo": {name: "Nuevo Cliente", icon: "add", accesskey: "n"},
            "editar": {name: "Editar", icon: "edit", accesskey: "e"},
            "eliminar": {name: "Eliminar", icon: "delete", accesskey: "l"},
            "sep1": "---------",
            "exportarClientes": {name: "Exportar Clientes", icon: "edit", accesskey: "x"}
        }
    });

	//doble click para editar el cliente
	$("#"+id).dblclick(function(){
		EditarCliente();
		return;
	});
}

/**
* MANEJADOR DE LAS ACCIONES DEL MENU DE UN ARTICULO
* @param m -> evento seleccionado
* @param id -> cliente seleccionado
*/
function MenuCliente(m, id){

	if(m == "clicked: nuevo"){
		NuevoCliente();
	}else if(m == "clicked: eliminar"){
		EliminarCliente(id);
	}else if(m == "clicked: editar"){
		EditarCliente();
	}else if(m == "clicked: exportarClientes"){
		ExportarClientes();
	}
}

/**
* CARGA LA VISTA DE EDICION DE UN CLIENTE
*/
function EditarCliente(){
	var id = $("#clientes .seleccionada").attr('id');

	var queryParams = {"func" : "EditarCliente", "id" : id};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxClientes.php",
		beforeSend: function(){
		},
		success: function(response){
			$("#content").html(response);
			FormularioEditarCliente();

		},
		fail: function(){
			notificaError("Error: Clientes.js EditarCliente().");
		}
	});
}

/**
* INICIALIZA EL FORMULARIO DEL CLIENTE
*/
function FormularioEditarCliente(){
	//validacion
	$("#FormularioEditarCliente").validationEngine();
		
	var options = {  
		beforeSend: function(){
			DeshabilitarContent();
		},
	    success: function(response) { 

	    	if(response.length <= 3){
	    		notifica("Cliente Actualizado.");

	    		//actualiza el nombre del cliente si cambia
	    		var nombre = $("#nombre").val()
	    		var cliente = $("#cliente-id").val();

	    		if(nombre !== $("#"+cliente).html() ){
	    			$("#"+cliente).fadeOut(500, function(){
	    				$("#"+cliente).html(nombre);
	    				$("#"+cliente).fadeIn();
	    			});
	    		}

				LimpiarContent();
			}else{
				notificaError(response);
				$("#content").html(response);
			}
		},
		fail: function(){
		}
	}; 
	$('#FormularioEditarCliente').ajaxForm(options);

	//imagenes
	$(".td-user-image img").hide();
	var height = $(".td-user-image").closest("td").height();
	var width = $(".td-user-image").closest("td").width();
	$('.td-user-image img').css({
		"max-height" : height,
		"max-width" : width
	});
	$("#pais").chosen( { search_contains: true } );
	$(".td-user-image img").fadeIn();
}

/**
 * NUEVO CLIENTE
 */
function NuevoCliente(){
	var queryParams = {"func" : "NuevoCliente"};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxClientes.php",
		beforeSend: function(){
		},
		success: function(response){
			$("#content").html(response);
			FormularioNuevoCliente();
		},
		fail: function(){
			notificaError("Error: Clientes.js NuevoCliente().")
		}
	});
}

/**
 * INICIALIZA EL FORMULARIO PARA NUEVO CLIENTE
 */
function FormularioNuevoCliente(){
	//validacion
	$("#FormularioNuevoCliente").validationEngine();
		
	var options = {  
		beforeSend: function(){
			DeshabilitarContent();
		},
	    success: function(response) { 
	    	
	    	if(response.length <= 3){
	    		notifica("Cliente Creado.");

				LimpiarContent();
				Clientes();
			}else{
				notificaError(response);
			}
		},
		fail: function(){
		}
	};
	$("#pais").chosen( { search_contains: true } );
	$('#FormularioNuevoCliente').ajaxForm(options);
}

/**
* EXPORTAR TODOS LOS CLIENTES
*/
function ExportarClientes(){
	var si = function (){
		AccionExportarClientes();
	}

	var no = function (){
		notificaAtencion("Exportacion cancelada");
	}

	Confirmacion("Exportar Clientes y descargar archivo.", si, no);
}

/**
* ACCION DE EXPORTAR
*/
function AccionExportarClientes(){
	top.location.href = 'src/class/exportar.php?tipo=clientes';
	notificaAtencion('Asegurese de guardar el archivo en el disco duro.');
}

/**
* ELIMINA CLIENTE
*/
function EliminarCliente(id){
	
	if(id == '' || id == undefined){
		id = $("#clientes .seleccionada").attr("id");
	}

	var si = function (){
		AccionEliminarCliente(id);
	}

	var no = function (){
		notificaAtencion("Operacion cancelada");
	}

	Confirmacion("Desea Eliminar El Cliente y todos sus proyectos", si, no);
}

/**
* ACCION DE ELIMINAR EL CLIENTE Y SUS PROYECTOS
*/
function AccionEliminarCliente(id){
	var queryParams = {"func" : "EliminarCliente", "id" : id};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxClientes.php",
		beforeSend: function(){
		},
		success: function(response){
			$("#content").html(response);
			if(response.length <= 3){
				notifica("Cliente Eliminado.");

				$("#"+id).fadeOut(700, function(){
					$("#"+id).remove();
				})
			}else{
				notificaError(response);
			}
		},
		fail: function(){
			notificaError("Error: Clientes.js AccionEliminarCliente() AJAX fail.")
		}
	});
}

/**
* VALIDA USUARIO
*/
function ClienteUsuario(field, rules, i, options){
	var usuarios = '';
	var queryParams = {'func' : "GetUsers"};

	$.ajax({
		data: queryParams,
		async: false,
		type: "post",
		url: "src/ajaxClientes.php",
		beforeSend: function(){
		},
		success: function(response){
			usuarios =  $.parseJSON(response);
		},
		fail: function(response){
			notificaError("Error: "+response);
		}
	}).done(function(){
	});

	/*if (field.val() == "andrey") {
		// this allows to use i18 for the error msgs
		//return options.allrules.validate2fields.alertText;
		return 'Usuario no disponible';
	}*/
	var error = false;
	$.each(usuarios, function(f,c){
		if (field.val() == c) {
			//return 'Usuario no disponible';
			error = true;
		}
	});
	if(error){
		return 'Usuario no disponible';
	}
}

/**
* MUESTRA LOS REGISTROS DE INGRESOS DE LOS CLIENTES
*/
function ClientesLogs(){
	if( $("#menu").is(":visible") ){
		ActivaMenu();
	}
	if( $("#menu2").is(":visible") ){
		Menu2();
	}
	if( $("#content").length > 0){
		$("#content").html('');
	}

	var queryParams = {"func" : "Logs"};
	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxClientes.php",
		success: function(response){

			$("#content").append(response);
			
			$("#cliente-logs tr").on('click',function(){
				var element = $(this);

				$("#cliente-logs tr").removeClass('seleccionada');
				element.addClass("seleccionada");

			}).dblclick(function(){
				ClienteEstadisticas( $(this).attr('id') );
			});
		},
		fail: function(response){
			notificaError("AJAX FAIL Clientes.js ClientesLogs.<br/>"+response);
		}
	});
}

/**
* ESTADISTICAS DE UN CLIENTE
* @param int id -> id del cliente
*/
function ClienteEstadisticas( id ){
	if( !$("#menu").is(":visible") ){
		ActivaMenu();
	}

	var queryParams = {"func" : "Clientes"};
	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxClientes.php",
		success: function(response){

			if( 3 <= response.length ){

				$("#menu")
					.hide()
					.html( response )
					.fadeIn();
			}else{
				notificaError("Error: clientes.js ClienteEstadisticas(), al obtener lista de clientes.<br/>"+response);
			}

		},
		fail: function(response){
			notificaError("Error: AJAX FAIL clientes.js ClienteEstadisticas().<br/>"+response);
		}
	});

	queryParams = {"func" : "ClienteEstadisticas", "id" : id};
	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxClientes.php",
		success: function(response){

			console.log( response );
			
			$("#content").html( response );

			ClienteLogsDates( id );
		},
		fail: function(response){
			notificaError("Error: AJAX FAIL, clientes.js ClienteEstadisticas()<br/>"+response);
		}
	});
}

/*
* CARGA EL CALENDARIO
* @param int id -> id del cliente
*/
function ClienteLogsDates( id ){
	var queryParams = {"func" : "ClienteDates", "id" : id};

	$.ajax({
		data: queryParams,
		dataType: "json",
		url: "src/ajaxClientes.php",
		type: "post",
		beforeSend: function(){

		},
		success: function(data){
			console.log( data );

			/*var dias = jQuery.parseJSON( data );
			console.log( dias );*/

			CargarDate( 'date' , data );
		},
		fail: function( response ){
			notificaError("Error: AJAX FAIL Clientes.js ClienteLogsDates().<br/>"+response);
		}
	});
}
	
/**
* CARGA UN DATEPICKER CON TODOS DIAS SELECCIONADOS
* @param string id -> id del DOM
* @param object events -> objecto con los dias y informacion
	var events = [ 
	    { Title: "titulo del evento", Date: new Date("03/19/2013"), Hour: '12:55:02' },
	];
*/
function CargarDate( id, dias ){
	var events = dias;

	$("#"+id).datepicker({
	    beforeShowDay: function(date) {
	        var result = [true, '', null];
	        
	        var matching = $.grep(events, function(event) {
	        	event.Date = new Date( event.Date.valueOf() );
	        	
	            return event.Date.valueOf() === date.valueOf();
	        });
	        
	        //console.log( matching );

	        if (matching.length) {
	            result = [true, 'highlight', null];
	        }
	        //console.log( result );
	        return result;
	    },
	    onSelect: function(dateText) {
	    	//console.log( dateText );

	    	var resultados = [];
	      	// alert( dateText);
	    	var selectedDate = new Date(dateText);

	        //console.log( selectedDate );

	        for( i = 0; i < events.length; i++ ){
	        	var tempDate = new Date( events[i].Date );

	        	//console.log( events[i].Date.valueOf() );
	        	console.log( tempDate.valueOf() );
	        	console.log( selectedDate.valueOf() );

	        	if( tempDate.valueOf() === selectedDate.valueOf() ) {
	        		resultados.push( i );
	        	}
	        }
	        console.log( resultados );
	        //Log( resultados, dateText );
	    }
	});
}