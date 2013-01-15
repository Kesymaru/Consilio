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

	AdminContextMenu(id);
}

/**
* CREA EL CONTEXT MENU DEL ADMINs
* @param id -> id del admin
*/
function AdminContextMenu(id){
	
	//EVITA LA AUTOELIMINACION DE UN ADMIN	
	if($("#"+id).hasClass("me")){

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

}

/**
* EDITAR UN ADMIN
*/
function EditarAdmin(){
	var id = $("#admins .seleccionada").attr("id");
	console.log(id);
	var queryParams = {"func" : "EditarAdmin", "id" : id};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxAdmin.php",
		beforesend: function(){
		},
		successs: function(response){
			$("#menu").html(response);
			if(response.length){
				$("#menu").html(response);
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
function FormularioEditarAdmin(){

}

/**
*
*/
function NuevoAdmin(){

}