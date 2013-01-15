/**
* JAVASCRIPT PARA VISTA DE ADMIN
*/

function Admin(){
	$.contextMenu( 'destroy' );

	if(!$("#menu").is(":visible")){
		ActivaMenu();
	}
	if($("#content").html() != ""){
		LimpiarContent();
	}

	$.cookie('vista', 'admin');

	var queryParams = {"func" : "Admin"};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxAdmin.php",
		beforesend: function(){
		},
		success: function(response){
			if(response.length > 0){
				$("#menu").html(response);
			}else{
				notificaError(response);
			}
		},	
		fail: function(response){
			notificaError("Error: AJAX fail Admin.JS Admin.<br/>"+response);
		}
	});
}

/**
* SELECCIONA UN ADMINs
* @param id -> id del admin
*/
function SelectAdmin(id){
	$("#admins li").removeClass('seleccionada');
	$("#"+id).addClass("seleccionada");

	if(!$("#EliminarAdmin").is(":visible")){
		$("#EliminarAdmin").fadeIn();
	}

	if(!$("#EditarAdmin").is(":visible")){
		$("#EditarAdmin").fadeIn();
	}

	AdminContextMenu(id);
}

/**
* CREA EL CONTEXT MENU DEL ADMINs
* @param id -> id del admin
*/
function AdminContextMenu(id){
	
	//EVITA LA AUTOELIMINACION DE UN ADMIN	
	if($("#"+id).hasClass("me")){
		
		$("#EliminarAdmin").hide();

		$.contextMenu({
	        selector: '#'+id, 
	        callback: function(key, options) {
	            var m = "clicked: " + key;
	            //window.console && console.log(m) || alert(m); 
	            MenuAdmin(m, id);
	        },
	        items: {
	        	"nuevo": {name: "Nuevo Admin", icon: "add", accesskey: "n"},
	            "editar": {name: "Editar", icon: "edit", accesskey: "e"}	        }
	    });

	}else{

		$.contextMenu({
	        selector: '#'+id, 
	        callback: function(key, options) {
	            var m = "clicked: " + key;
	            //window.console && console.log(m) || alert(m); 
	            MenuAdmin(m, id);
	        },
	        items: {
	        	"nuevo": {name: "Nuevo Admin", icon: "add", accesskey: "n"},
	            "editar": {name: "Editar", icon: "edit", accesskey: "e"},
	            "eliminar": {name: "Eliminar", icon: "delete", accesskey: "l"},
	        }
	    });

	}
	
	//doble click para editar el cliente
	$("#"+id).dblclick(function(){
		EditarAdmin();
		return;
	});

}

/**
* MANEJA LAS OPCIONES DEL MENU
*/
function MenuAdmin(m, id){
	if(m == "clicked: nuevo"){
		NuevoAdmin();
	}else if(m == "clicked: eliminar"){
		EliminarAdmin();
	}else if(m == "clicked: editar"){
		EditarAdmin();
	}
}

/**
* EDITAR UN ADMIN
*/
function EditarAdmin(){
	var id = $("#admins .seleccionada").attr("id");
	var queryParams = {"func" : "EditarAdmin", "id" : id};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxAdmin.php",
		beforesend: function(){
		},
		success: function(response){

			if(response.length > 0){
				$("#content").html(response);
				FormularioEditarAdmin(id);

			}else{
				notificaError(response);
			}
		},
		fail: function(response){
			notificaError("Error: AJAX fail Admin.js EditarAdmin()<br/>"+response);
		}
	});
}

/**
* INCIALIZA EL FORMULARIO DE EDICION DE UN ADMIN
*/
function FormularioEditarAdmin(id){
	$("#FormularioEditarAdmin").validationEngine();	
	var options = {  
		beforeSend: function(){
			DeshabilitarContent();
		},
	    success: function(response) { 
	    	
	    	if(response.length <= 3){
	    		notifica("Admin Actualizado.");

				LimpiarContent();
				var nombre = $("#nombre").val();
				
				if(nombre != $("#"+id).html()){
					$("#"+id).fadeOut(500, function(){
						$("#"+id).html(nombre);
						$("#"+id).fadeIn();
					});
				}

			}else{ 
				LimpiarContent();
				notificaError(response);
			}
		},
		fail: function(response){
			LimpiarContent();
			notificaError("Error: AJAX fail Admin.js FormularioEditarAdmin().<br/>"+response);
		}
	};

	$('#FormularioEditarAdmin').ajaxForm(options);

}

/**
* CARGA FORMULARIO PARA CREAR NUEVO ADMIN
*/
function NuevoAdmin(){
	var queryParams = {"func" : "NuevoAdmin"};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxAdmin.php",
		beforesend: function(){
		},
		success: function(response){

			if(response.length > 0){
				$("#content").html(response);
				FormularioNuevoAdmin();
			}else{
				notificaError(response);
			}
		},
		fail: function(response){
			notificaError("Error: AJAX fail Admin.js EditarAdmin()<br/>"+response);
		}
	});
}

function FormularioNuevoAdmin(){
	//validacion
	$("#FormularioNuevoAdmin").validationEngine();
		
	var options = {  
		beforeSend: function(){
			DeshabilitarContent();
		},
	    success: function(response) { 
	    	
	    	if(response.length <= 3){
	    		notifica("Admin Creado.");

				LimpiarContent();
				Admin();
			}else{ 
				LimpiarContent();
				notificaError(response);
			}
		},
		fail: function(response){
			LimpiarContent();
			notificaError("Error: AJAX fail Admin.js FormularioNuevoAdmin().<br/>"+response);
		}
	}; 
	$('#FormularioNuevoAdmin').ajaxForm(options);
}

/**
* ELIMINAR ADMIN
*/
function EliminarAdmin(){
	var id = $("#admins .seleccionada").attr("id");

	if(id == null || id == undefined ){
		notificaAtencion("Seleccione un Admin.");
		return;
	}

	var si = function (){
		AccionEliminarAdmin(id);
	}

	var no = function (){
		notificaAtencion("Operacion cancelada");
	}

	Confirmacion("Desea Eliminar Al Administrador.", si, no);
}

/**
* REALIZA LA ACCION DE ELIMINAR UN ADMIN SI ESTA ES CONFIRMADA
* @param id -> id del admin a eliminar
*/
function AccionEliminarAdmin(id){

	var queryParams = {"func" : "EliminarAdmin", "id" : id};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxAdmin.php",
		beforesend: function(){
		},
		success: function(response){

			if(response.length <= 3){
				notifica("Admin Eliminado.");
				
				$("#"+id).fadeOut(700, function(){
					$("#"+id).remove();
				});

				var edicion = $("#admin-id").val();
				if(edicion == id){
					LimpiarContent();
				}

			}else{
				notificaError(response);
			}
		},
		fail: function(response){
			notificaError("Error: AJAX fail Admin.js AccionEliminarAdmin()<br/>"+response);
		}
	});
}


/****************** HELPERS **************/

/**
* VALIDA QUE EL USUARIO ESTE DISPONIBLE, IGNORA EL USUARIO ACTUAL EN LA LISTA
*/
function UsuariosDiponiblesAdminEdicion(field, rules, i, options){
	var usuarios = '';
	
	var id = $("#admin-id").val();
	var queryParams = {'func' : "UsuariosDisponiblesAdmin", "id" : id};

	$.ajax({
		data: queryParams,
		async: false,
		type: "post",
		url: "src/ajaxAdmin.php",
		beforeSend: function(){
		},
		success: function(response){
			usuarios =  $.parseJSON(response);
		},
		fail: function(response){
			notificaError("Error: "+response);
		}
	});

	var error = false;

	$.each(usuarios, function(f,c){
		if (field.val() == c) {
			//return 'Usuario no disponible';
			error = true;
		}
	});

	if(error){
		return 'Usuario no disponible.';
	}
}

/**
* VALIDA QUE EL NUEVO USUARIO ESTE DISPONIBLE
*/
function UsuariosDiponiblesAdmin(field, rules, i, options){
	var usuarios = '';

	var queryParams = {'func' : "UsuariosDisponibles"};

	$.ajax({
		data: queryParams,
		async: false,
		type: "post",
		url: "src/ajaxAdmin.php",
		beforeSend: function(){
		},
		success: function(response){
			usuarios =  $.parseJSON(response);
		},
		fail: function(response){
			notificaError("Error: "+response);
		}
	});

	var error = false;

	$.each(usuarios, function(f,c){
		if (field.val() == c) {
			//return 'Usuario no disponible';
			error = true;
		}
	});
	if(error){
		return 'Usuario no disponible.';
	}
}