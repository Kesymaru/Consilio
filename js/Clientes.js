/**
* JAVASCRIPT PARA VISTA DE CLIENTES
*/

/**
* CARGA LA EDICION DE CLIENTES
*/
function Clientes(){
	if($("#menu2").is(":visible")){
		Menu2();
	}

	LimpiarContent();

	var queryParams = {"func" : "Clientes"};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxClientes.php",
		beforeSend: function(){
		},
		success: function(response){
			$("#menu").html(response);
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
}

/**
* CARGA EL CONTEXT MENU DE UN CLIENTE SELECCIONADO
*/
function ContextMenuCliente(){

}

function EditarCliente(){
	var id = $("#clientes .seleccionada").attr('id');
	notifica(id);
	
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
	    	console.log(response.length);

	    	if(response.length == 0){

	    		//actualiza el nombre del cliente si cambia
	    		var nombre = $("#nombre").val()
	    		var cliente = $("#cliente").val();

	    		if(nombre !== $("#"+cliente)){
	    			$("#"+cliente).fadeOut(500, function(){
	    				$("#"+cliente).html(nombre);
	    				$("#"+cliente).fadeIn();
	    			});
	    		}

				LimpiarContent();
			}else{
				notificaError(response);
			}
		},
		fail: function(){
		}
	}; 
	$('#FormularioEditarCliente').ajaxForm(options);
}
