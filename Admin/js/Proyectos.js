/**
* PROYECTOS
*/

/**
* MUSTRA LISTA DE PROYECTOS EN CONTENT
* VISTA AVANZADA CON TABLA Y DATOS DE LOS PROYECTOS
*/
function Proyectos(){
	$.cookie('vista', 'proyectos');

	$.contextMenu( 'destroy' );

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
			//$("#EliminarProyecto, #EditarProyecto, #DuplicarProyecto, #ComponerProyecto").hide();
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
				MenuScroll();
				//$("#EliminarProyecto, #EditarProyecto, #DuplicarProyecto").hide();
				
				SelectProyecto(id);
			},
			fail: function(response){
				notificaError("Error: AJAX fail Proyectos.js ProyectosMenu().<br/>"+response);
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
			//$("#EliminarProyecto, #EditarProyecto, #DuplicarProyecto").hide();
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

		if(!$("#EliminarProyecto, #EditarProyecto, #DuplicarProyecto, #ComponerProyecto").is(":visible")){
			$("#EliminarProyecto, #EditarProyecto, #DuplicarProyecto, #ComponerProyecto").fadeIn();
		}

		ContextMenuProyecto(id);
	}
}

/**
* CARGA EL CONTEXT MENU DE UN PROYECTO
* @param id -> id del proyecto
*/
function ContextMenuProyecto(id){

	$.contextMenu({
        selector: '#'+id, 
        //trigger: 'left',
        callback: function(key, options) {
            var m = "clicked: " + key;
            //window.console && console.log(m) || alert(m); 
            MenuProyecto(m, id);
        },
        items: {
			"nuevo": {name: "Nuevo Proyecto", icon: "add", accesskey: "n"},
            "editar": {name: "Editar", icon: "edit", accesskey: "e"},
            "eliminar": {name: "Eliminar", icon: "delete", accesskey: "l"},
            "sep1": "---------",
            "componer": {name: "Componer Proyecto", icon: "edit", accesskey: "c"},
            "duplicar": {name: "Duplicar Proyecto", icon: "edit", accesskey: "d"},
            "sep2": "---------",
            "fold1a": {
                "name": "Exportar", 
                "icon": "exportar",
                accesskey: "x",
	                "items": {
	                    "exportar-excel": {"name": "Excell" , "icon": "excel"},
	                    "exportar-pdf": {"name": "PDF", "icon": "pdf"},
	                }
            	},
            "fold2a": {
                "name": "Enviar", 
                "icon": "compartir",
                accesskey: "v",
	                "items": {
	                    "enviar-cliente": {"name": "A cliente" , "icon": "informe"},
	                    "enviar-link": {"name": "Por link" , "icon": "email"},
	                    "enviar-email": {"name": "Por email" , "icon": "email"},
	                }
            	}
        }
    });

	//doble click para editar el cliente
	$("#"+id).dblclick(function(){

		//console.log($.cookie('cargando'));
		
		if( $.cookie('cargando') == "false"){
			$.cookie('cargando', true);
			EditarProyecto();
		}
		
		return;
	});
}

/**
* MANEJADOR DE LAS ACCIONES DEl PROYECTO
* @param m -> evento seleccionado
* @param int id -> id del proyecto
*/
function MenuProyecto(m, id){

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
		ComponerProyectoSeleccionado()
	}else if(m == "clicked: enviar-cliente"){
		NotificarProyectoCliente(id);
	}
	else if(m == "clicked: enviar-link"){
		NotificarProyectoLink(id);
	}
	else if(m == "clicked: enviar-email"){
		NotificarProyectoMail(id);
	}
	else if( m == "clicked: exportar-excel"){
		top.location.href = 'src/class/exportar.php?id='+id+'&tipo=excel';
		notificaAtencion("Asegurese de guardar el archivo en el disco duro.");
	}
	else if( m == "clicked: exportar-pdf"){
		top.location.href = 'src/class/exportar.php?id='+id+'&tipo=pdf';
		notificaAtencion("Asegurese de guardar el archivo en el disco duro.");
	}
}

/**
* ENVIA C COMPONER
*/
function ComponerProyectoSeleccionado(){
	var id = $("#proyectos .seleccionada").attr("id");
	Componer(id);
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
			$("#cliente").chosen();
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
	$("#radio-estado").buttonset();
	$("#radio-visible").buttonset();
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
			$("#cliente").chosen();
			SelectProyecto(id);
			$.cookie('cargando', false);
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
	$("#radio-estado").buttonset();
	$("#radio-visible").buttonset();

	//imagenes
	$(".td-project-image img").hide();
	var height = $(".td-project-image").closest("td").height();
	var width = $(".td-project-image").closest("td").width();
	$('.td-project-image img').css({
		"max-height" : height,
		"max-width" : width
	});
	$(".td-project-image img").fadeIn();
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
			
			if(response.length <= 3){
				notifica("Proyecto Eliminado.");
				$("#"+id).fadeOut(700, function(){
					$("#"+id).remove();
				});
			}else{
				notificaError("Error: "+response);
			}
				
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
			if( $.isNumeric(response) ){
				notifica("Proyecto Duplicado.");

				nuevo = response;
				nuevo = parseInt(response);
				console.log(response);
				
				if( ProyectosMenuLineal(nuevo) ){ //termino
					LoadingClose();
					EditarProyectoDuplicado(nuevo);
				}
				
			}else{
				LoadingClose();
				notificaError("Error: AccionDuplicarProyecto response incorrecto."+response);
				$("content").html(response);
			}
		},
		fail: function(){
			LoadingClose();
			$("#content").html("Error: Proyectos.js AccionDuplicarProyecto() AJAX fail.")
		}
	});
}

/************* NOTIFICACIONES DEL PROYECTO **********/

/**
* ENVIA AL CLIENTE
* @param proyecto -> id proyecto
*/
function NotificarProyectoCliente(proyecto){
	var si = function (){
		AccionNotificarProyectoCliente(proyecto);
	}

	var no = function (){
		notificaAtencion("Operacion cancelada");
	}

	Confirmacion("Desea Enviarle el link para el proyecto al cliente.", si, no);
}

function AccionNotificarProyectoCliente(proyecto){

	var queryParams = {"func" : "NotificarProyectoCliente", "proyecto" : proyecto};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxProyectos.php",
		success: function(response){
			if(response.length <= 3){
				notifica("Proyecto Enviado Al cliente");
			}else{
				notificaError("Error: Proyectos.js AccionAnviarProyectoCliente.<br/>"+response);
			}
		},
		fail: function(response){
			notificaError("Error: AJAX fail Proyectos.js AccionAnviarProyectoCliente().<br/>"+response);
		}
	});
}

/**
* ENVIA PROYECTO A MAILES ESPECIFICADOS
* @param proyecto -> id del proyecto
*/
function NotificarProyectoMail(proyecto){
	
	var queryParams = {"func" : "ProyectoMail", "proyecto" : proyecto};
	var alto = $("html").height() * 0.8;
	
	 $.fancybox({
	 	'width'         : '80%',
		'height'        : alto,
        padding         : 10,
        autoSize        : false,
        fitToView       : false,
        arrows          : false,
        href            : "src/componerMail.php",
        type            : 'ajax',
        ajax            : {
                type    : "POST",
                cache   : false,
                data    : queryParams,
        },
        scrolling       : 'yes',
        scrollOutside   : true,
		autoScale       : false,
		transitionIn    : 'fade',
		transitionOut   : 'elastic',
    });

	notificaAtencion("Puede editar libremente el mail para la notificacion del proyecto.");
}

/**
* INICIALIZA EL FORMULARIO PARA NOTIFICAR PROYECTO MAIL EDITADO
*/
function FormularioProyectoMail(){
	var alto = $("html").height() * 0.7;
				
	$("#FormularioProyectoMail").css('height', alto+"px");
								
	$("#destinatario").tagsInput({
		"height":"auto",
   		"width":"100%",
   		"defaultText":"agregar destinatario",
	});

	$("#cc, #bcc").tagsInput({
		"height":"auto",
   		"width":"100%",
   		"defaultText":"agregar",
	});

	alto = alto - ( $("#FormularioProyectoMail .tabla-mail").innerHeight() 
					+ $(".table-botonera").innerHeight() 
					+ $("#FormularioProyectoMail .titulo").innerHeight() 
					+ 20 );
	
	if( alto > 200 ){
		EditorCustom('mail', alto, false);
		
		CKEDITOR.on("instanceReady", function(event){
		     $("#FormularioProyectoMail").parent().css({"overflow":"hidden"});
		});

	}else{
		EditorCustom('mail',0,false);
	}
	
	//validacion formulario
	$("#FormularioProyectoMail").validationEngine();
		
	var options = {  
		beforeSend: function(){
			EditorUpdateContent();
		},
	    success: function(response) { 

	    	if(response.length <= 3){
	    		notifica("Notificacion Enviada");
	    		$.fancybox.close();
			}else{
				notificaError("Error Proyectos.js FormularioProyectoMail()<br/> "+response);
			}
		},
		fail: function(response){
			notificaError("Error: AJAX fail Proyectos.js FormularioProyectoMail() <br/>"+response);
		}
	}; 
	$('#FormularioProyectoMail').ajaxForm(options);
}

/**
* OBTIENE EL LINK DEL PROYECTO PARA COPIARLO
* @param proyecto -> id del proyecto
**/
function NotificarProyectoLink(proyecto){
	var queryParams = {"func" : "ProyectoLink", "proyecto" : proyecto};

	$.fancybox({
	 	'width'         : '70%',
	 	'height'        : '100',
        padding         : 10,
        autoSize        : false,
        fitToView       : false,
        arrows          : false,
        href            : "src/componerMail.php",
        type            : 'ajax',
        ajax            : {
                type    : "POST",
                cache   : false,
                data    : queryParams,
        },
        scrolling       : 'no',
		autoScale       : false,
		transitionIn    : 'fade',
		transitionOut   : 'elastic',
		title           : 'Link Proyecto'
    });

	notificaAtencion("Asegurese de copiar el link.");
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