/**
* PROYECTOS
*/

/**
* MUSTRA LISTA DE PROYECTOS EN CONTENT
* VISTA AVANZADA CON TABLA Y DATOS DE LOS PROYECTOS
*/
function Proyectos(){
	$.cookie('vista', 'proyectos');

	if($("#menu2").is(":visible")){
		Menu2();
	}

	if($("#menu").is(":visible")){
		ActivaMenu();
	}

	if($("#menu").html() !== ''){
		$("#menu").html("");
	}

	var queryParams = {"func" : "ProyectosAvance"};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxProyectos.php",
		beforeSend: function(){
		},
		success: function(response){
			$("#content").html(response);
			$("#EliminarProyecto, #EditarProyecto, #DuplicarProyecto").hide();
		},
		fail: function(){
		}
	});
}

/**
 * LISTA DE PROYECTOS EN MENU
 * @param id -> id del proyecto seleccionado
 */
function ProyectosMenu(id){
	
	if(!$("#menu").is(":visible")){
		ActivaMenu();
		
		var queryParams = {"func" : "Proyectos"};

		//AJAX lineal
		$.ajax({
			data: queryParams,
			type: "post",
			url: "src/ajaxProyectos.php",
			beforeSend: function(){
			},
			success: function(response){
				$("#menu").html(response);
				console.log(id);
				//$("#EliminarProyecto, #EditarProyecto, #DuplicarProyecto").hide();
				
				SelectProyecto(id);
			},
			fail: function(){
			}
		});
	}
}

/**
 * CARGA LOS PROYECTOS DE MANAERA LINEAL EN MENU
 * @param id -> id del proyecto
 * @return true al terminar
 */
function ProyectosMenuLineal(id){
	//limpia content sin efectos es lineal
	if($("#content").html() != ''){
		$("#content").html(""); 
	}

	if(!$("#menu").is(":visible")){
		ActivaMenu();
	}

	var queryParams = {"func" : "Proyectos"};

	//AJAX lineal
	$.ajax({
		data: queryParams,
		async: false, 
		type: "post",
		url: "src/ajaxProyectos.php",
		beforeSend: function(){
		},
		success: function(response){
			$("#menu").html(response);
			$("#EliminarProyecto, #EditarProyecto, #DuplicarProyecto").hide();
			SelectProyecto(id);
		},
		fail: function(){
		}
	});

	return true;
}

/**
* SELECCIONA UN PROYECTO
*/
function SelectProyecto(id){
	if(0 <= id && id != "" && id != undefined){
		$("#proyectos li, #proyectos tr").removeClass("seleccionada");
		$("#"+id).addClass("seleccionada");

		if(!$("#EliminarProyecto, #EditarProyecto, #DuplicarProyecto").is(":visible")){
			$("#EliminarProyecto, #EditarProyecto, #DuplicarProyecto").fadeIn();
		}

		ContextMenuProyecto(id);
	}
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
            "componer": {name: "Componer Proyecto", icon: "edit"},
            "duplicar": {name: "Duplicar Proyecto", icon: "edit"},
            "sep2": "---------",
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
	}else if(m == "clicked: duplicar"){
		DuplicarProyecto();
	}else if(m == "clicked: componer"){
		//cambia a la vista de composicion del proyecto
		var id = $("#proyectos .seleccionada").attr("id");
		Componer(id);
	}
}

/**
* NUEVO PROYECTO
*/
function NuevoProyecto(){
	ProyectosMenu();

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
 * EDICION DE UN PROYECTO
 */
function EditarProyecto(){
	var id = $("#proyectos .seleccionada").attr("id");

	ProyectosMenu(id);

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
			
			SelectProyecto(id);
		},
		fail: function(){
		}
	});
}

/**
 * EDICION DE UN PROYECTO RECIEN DUPLICADO
 * @param id -> id del proyecto
 */
function EditarProyectoDuplicado(id){

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
			
			$("#content .titulo").html("Edición Proyecto Duplicado<hr/>");

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
	$("#FormularioEditarProyecto").validationEngine();
		
	var options = {  
		beforeSend: function(){
			EditorUpdateContent();
			DeshabilitarContent();
		},
	    success: function(response) { 

	    	if(response.length <= 3){
	    		notifica("Proyecto Actualizado.");

	    		Proyectos();
			}else{
				$("#content").html(response);
			}
		},
		fail: function(){
		}
	}; 
	$('#FormularioEditarProyecto').ajaxForm(options);

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
			notifica("Proyecto Eliminado.");
			$("#"+id).fadeOut(700, function(){
				$("#"+id).remove();
			});
		},
		fail: function(){
			notificaError("Error: Proyectos.js DelteProyecto()");
		}
	});
}

/**
 * CONFIRMACION DE CUPLICAR EL PROYECTO
 */
function DuplicarProyecto(){
	var si = function (){
		AccionDuplicarProyecto();
	}

	var no = function (){
		notificaAtencion("Operacion cancelada");
	}

	Confirmacion("Desea Duplicar el Proyecto y todos sus datos.", si, no);
}

/**
 * DUPLICAR PROYECTO
 */
function AccionDuplicarProyecto(){
	var id = $("#proyectos .seleccionada").attr("id");

	var queryParams = {"func" : "DuplicarProyecto", "id" : id};

	var nuevo = '';

	$.ajax({
		data: queryParams,
		async: false,
		type: "post",
		url: "src/ajaxProyectos.php",
		beforeSend: function(){
			 Loading();
		},
		success: function(response){
			//devuelve el id del nuevo proyecto duplicado
			if($.isNumeric(response)){
				notifica("Proyecto Duplicado.");

				nuevo = response;
				nuevo = parseInt(response);

				if(ProyectosMenuLineal(nuevo)){ //termino
					LoadingClose();
					EditarProyectoDuplicado(nuevo);
				}
				
			}else{
				$("content").html(response);
			}
		},
		fail: function(){
			LoadingClose();
			$("#content").html("Error: Proyectos.js AccionDuplicarProyecto() AJAX fail.")
		}
	});
}

/******************** HELPERS ******************/
/**
 * LIMPIA EL CONTENIDO DE UNA VISTA QUE SEA AVANZADA
 */
function CancelarProyecto(){
	notificaAtencion("Operacion Cancelada.");

	//elimina el submit en un form
	$("form").submit(function(e){
		e.preventDefault();
		return false;
	});

	if($("#menu").is(":visible")){
		ActivaMenu();
		$("#menu").html("");
	}

	Proyectos();
}