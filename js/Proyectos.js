/**
* PROYECTOS
*/

/**
* MUSTRA LISTA DE PROYECTOS
*/
function Proyectos(){
	$.cookie('vista', 'proyectos');

	if($("#menu2").is(":visible")){
		Menu2();
	}

	if(!$("#menu").is(":visible")){
		ActivaMenu();
	}

	LimpiarContent();

	var queryParams = {"func" : "Proyectos"};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxProyectos.php",
		beforeSend: function(){
		},
		success: function(response){
			$("#menu").html(response);
			$("#EliminarProyecto, #EditarProyecto").hide();
		},
		fail: function(){
		}
	});
}

/**
* SELECCIONA UN PROYECTO
*/
function SelectProyecto(id){
	$("#proyectos li").removeClass("seleccionada");
	$("#"+id).addClass("seleccionada");

	if(!$("#EliminarProyecto, #EditarProyecto").is(":visible")){
		$("#EliminarProyecto, #EditarProyecto").fadeIn();
	}

	ContextMenuProyecto(id);
}

/**
* CARGA EL CONTEXT MENU DE UN PROYECTO
*/
function ContextMenuProyecto(id){
	$.contextMenu({
        selector: '#'+id, 
        callback: function(key, options) {
            var m = "clicked: " + key;
            //window.console && console.log(m) || alert(m); 
            MenuProyecto(m);
        },
        items: {
			"nuevo": {name: "Nuevo Proyecto", icon: "add"},
            "editar": {name: "Editar", icon: "edit"},
            "eliminar": {name: "Eliminar", icon: "delete"},
            "sep1": "---------",
            "duplicar": {name: "Duplicar Proyecto", icon: "edit"},
            "fold1a": {
                "name": "Exportar", 
                "icon": "exportar",
	                "items": {
	                    "exportar-excel": {"name": "Excell" , "icon": "excel"},
	                    "exportar-pdf": {"name": "PDF", "icon": "pdf"},
	                }
            	},
            	"fold2a": {
                "name": "Enviar", 
                "icon": "compartir",
	                "items": {
	                    "informe-cliente": {"name": "A cliente" , "icon": "informe"},
	                    "informe-link": {"name": "Por link" , "icon": "email"},
	                    "informe-email": {"name": "Por email" , "icon": "email"},
	                }
            	}
        }
    });

	//doble click para editar el cliente
	$("#"+id).dblclick(function(){
		EditarProyecto();
		return;
	});
}

/**
* MANEJADOR DE LAS ACCIONES DEl PROYECTO
*/
function MenuProyecto(m){

	if(m == "clicked: nuevo"){
		NuevoProyecto();
	}else if(m == "clicked: eliminar"){
		EliminarProyecto();
	}else if(m == "clicked: editar"){
		EditarProyecto();
	}
}

/**
* NUEVO PROYECTO
*/
function NuevoProyecto(){
	var queryParams = {"func" : "NuevoProyecto"};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxProyectos.php",
		beforeSend: function(){
		},
		success: function(response){
			$("#content").html(response);
			FormularioNuevoProyecto();
			//SelectorMultipleFiltro();
		},
		fail: function(){
		}
	});
}

/**
* INCIALIZA FORMULARIO PARA NUEVO PROYECTO
*/
function FormularioNuevoProyecto(){
	//validacion
	$("#FormularioNuevoProyecto").validationEngine();
		
	var options = { 
		beforeSend: function(){
			EditorUpdateContent();
			DeshabilitarContent();
		},
	    success: function(response) { 
	    	notifica("enviado");
	    	if(response.length <= 3){
	    		notifica("Proyecto Creado.");

	    		Proyectos();

				LimpiarContent();
			}else{
				$("#content").html(response);
			}
		},
		fail: function(){
		}
	}; 
	$('#FormularioNuevoProyecto').ajaxForm(options);

	Editor('descripcion');
}

function EditarProyecto(){
	var id = $("#proyectos .seleccionada").attr("id");

	var queryParams = {"func" : "EditarProyecto", "id" : id};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxProyectos.php",
		beforeSend: function(){
		},
		success: function(response){
			$("#content").html(response);
			FormularioEditarProyecto();
			//SelectorMultipleFiltro();
		},
		fail: function(){
		}
	});
}

/**
* INICIALIZA FORMULARIO DE EDICION DE UN PROYECTO
*/
function FormularioEditarProyecto(){
	//validacion
	$("#FormularioNuevoProyecto").validationEngine();
		
	var options = {  
		beforeSend: function(){
			EditorUpdateContent();
			DeshabilitarContent();
		},
	    success: function(response) { 

	    	if(response.length <= 3){
	    		notifica("Proyecto Actualizado.");

	    		//actualiza nombre
	    		var nombre = $("#nombre").val();
	    		var proyecto = $("#proyecto").val();

	    		if(nombre !== $("#"+proyecto).html() ){
	    			$("#"+proyecto).fadeOut(500, function(){
	    				$("#"+proyecto).html(nombre);
	    				$("#"+proyecto).fadeIn();
	    			});
	    		}

	    		Proyectos();

				LimpiarContent();
			}else{
				$("#content").html(response);
			}
		},
		fail: function(){
		}
	}; 
	$('#FormularioNuevoProyecto').ajaxForm(options);

	Editor('descripcion');
}

/**
 * CONFIRMACION DE ELIMINAR PROYECTO
 */
function EliminarProyecto(){
	var si = function (){
		DelteProyecto();
	}

	var no = function (){
		notificaAtencion("Operacion cancelada");
	}

	Confirmacion("Desae Eliminar el Proyecto y todos sus datos.", si, no);
}

/**
 * ACCION DE ELIMINAR PROYECTO
 */
function DelteProyecto(){
	var id = $("#proyectos .seleccionada").attr("id");
	
	var queryParams = {"func" : "EliminarProyecto", "id" : id};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxProyectos.php",
		beforeSend: function(){
		},
		success: function(response){
			$("#content").html(response);
			FormularioEditarProyecto();
			//SelectorMultipleFiltro();
		},
		fail: function(){
		}
	});
}