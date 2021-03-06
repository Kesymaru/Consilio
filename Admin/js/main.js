jQuery(function($){
	$.datepicker.regional['es'] = {clearText: 'Effacer', clearStatus: '',
		closeText: 'Cerrar', 
		closeStatus: 'Cerrar',
		prevText: 'Anterior',
		prevStatus: 'Anterior mes',
		nextText: 'Siguiente',
		nextStatus: 'Siguiente mes',
		currentText: 'Hoy',
		currentStatus: 'Hoy',
		monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
		'Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre'],
		monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
                'Jul','Ago','Sep','Oct','Nov','Dic'],
		monthStatus: '',
		weekHeader: 'Semana', 
		weekStatus: '',
		dayNames: ['Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado'],
		dayNamesShort: ['Dom','Lun','Mar','Mi&eacute;','Juv','Vie','S&aacute;b'],
		dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S&aacute;'],
		dayStatus: 'DD d MM',
		dateStatus: 'DD d MM',
		dateFormat: 'yy/mm/dd', 
		firstDay: 1, 
		initStatus: 'Seleccione el dia', 
		isRTL: false
	};
	$.datepicker.setDefaults($.datepicker.regional['es']);
});

/**
* PARA EL MENU COMO PANEL DESPLAZABLE CON SCROLL
*/
$(window).resize(function () { 
	
	if($("#menu").is(":visible")){
		MenuScroll();
	}

	if($("#menu2").is(":visible")){
		Menu2Scroll();
	}
});
/*
$.ajaxSetup({
    beforeSend: function() {
        // show loading dialog // works
    },
    complete: function(xhr, stat) {
       	//MenuScroll();
    },
    success: function(result,status,xhr) {
        alert('success');
    }
});*/

//errore de ajax
$(document).ajaxError(function(event, request, settings) {
	if($("#loader").is(":visible")){
		LoadingClose();
	}
	notificaError("Error: AJAX error.<br/>"+settings.data+"<br/>"+settings.url+"");

});

function MenuScroll(){
	var altoMenu = $("#menu").innerHeight() - ( $("#menu .titulo").outerHeight() + $("#menu .menu-botones").outerHeight(true) );
	
	$("#menu .scroll").css({
		'height' : altoMenu,
		'overflow' : "auto"
	});
}

function Menu2Scroll(){
	var altoMenu = $("#menu2").innerHeight() - ( $("#menu2 .titulo").outerHeight() + $("#menu2 .menu-botones").outerHeight(true) );
	
	$("#menu2 .scroll").css({
		'height' : altoMenu,
		'overflow' : "auto"
	});
}

function getip(json){
    alert(json.ip); // alerts the ip address
}

$(document).ready(function(){

	//tooltips
	$(document).tooltip({
		tooltipClass: "arrow",
	  	position: {
            /*my: "center top-70",
            at: "center bottom",
            collision: "flipfit"*/
            my: "center bottom-2",
            at: "center top",
            collision: "flipfit"
        },
        track: false,
        show:{
	    	effect:'slideDown',
	    	delay: 700
		},
		open: function( event, ui ) {
			//se cierran despues de 2 segundos
	    	setTimeout(function(){
	      		$(ui.tooltip).hide('clip');
	      		$(".ui-effects-wrapper").remove();
	   		}, 2000);
	  	},
	  	items: "img, [data-geo], [title]",
            content: function() {
                var element = $( this );
                if ( element.hasClass( "custom-tooltip" ) ) {
                    var imagen = element.attr( "title" );
                    var text = element.text();
                    return '<img class="custom-tooltip-image" alt="'+text+'" src="'+imagen+'" />';
                }
                if ( element.is( "[title]" ) ) {
                    return element.attr( "title" );
                }
                if ( element.is( "img" ) ) {
                    return element.attr( "title" );
                }
            }
	});

	$('.dropMenu button').button();
	$('.dropMenu').hide();

	$("#menuUsuario, #menuProyectos, #menuClientes, #menuEdicion").bind('click',function(){
		
		if($("#searchbar").is(":visible")){
			BuscarGlobalHide()
		}
		
		$(".dropMenu").clearQueue();

		if($(".dropMenu").is(":visible")){
			$(".dropMenu").slideUp();
			$(".dropMenu").closest("div").css({
				'background-color' : '#fff',
				'color' : '#000'
			});
		}

		if($('#'+this.id+" .dropMenu").is(':visible')){
			$('#'+this.id+" .dropMenu").slideUp();
			$('#'+this.id).css({
				'background-color' : '#fff',
				'color' : '#000'
			});
		}else{
			$('#'+this.id).css({
				'background-color' : '#a1ca4a',
				'color' : '#fff'
			});
			$('#'+this.id+" .dropMenu").slideDown();
		}
	}).bind("mouseleave",function(){
		var id = this.id;
		$('#'+this.id+" .dropMenu").delay(1000).slideUp(700, function(){
			$('#'+id).css({
				'background-color' : '#fff',
				'color' : '#000'
			});
		});
	});

	$("#searchForm").validationEngine();
    $('input[placeholder]').placeholder();
    $("#searchbar").hide();

    //set cookies
    Cookies();

    
});

/**
* ACTIVA EL MENU
*/
function ActivaMenu(){
	//ActivaMenuFixIe(); //FIX PARA IE

	//ESCONDE
	if( $('#menu').is(':visible') && $.cookie('vista') ){

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
	}else { //muestra

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
* REGISTRA EL ERROR 
* @param text -> texto del error
*/
function notificaError(text) {

	var queryParams = {"error" : text, "site" : "Admin"};
	$.ajax({
		data: queryParams,
		async: false,
		type: "post",
		url: "src/class/error.php",
		success: function(response){
			text += "<br/>Notifcado al webmaster.";
		}
	});

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

	if( $("content-disable").is(":visible") ){
		HabilitarContent();
	}
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

/********************************** HELPERS ******************************/

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

/**
* INICIALIZA LAS COOKIES
*/
function Cookies(){
	if($.cookie('cargando') == null){
		$.cookie('autosave', false, { expires: 7 });
		$.cookie('cargando', false);
		$.cookie('id', false, { expires: 7 });

		$.cookie("super", 'false');
	}
	//Inicializa();
}

/**
* RESTAURA LA VISTA CON COOKIES
*/
function Inicializa(){
	if($.cookie('vista') == 'proyectos'){
		VistaProyecto();
	}

	if($.cookie('vista') == 'edicion'){
		VistaEdicion();
	}

	if($.cookie('vista') == 'clientes'){
		VistaClientes();
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
	var id = document.getElementById(id);
	/*old loader
	CKEDITOR.replace( id );
	CKEDITOR.on("instanceReady", function(event){
			$(".cke_path").remove();
	});
	*/
	var editor = CKEDITOR.instances[id];
    if (editor) {
    	CKEDITOR.remove(editor);
    	editor.destroy(true);
    }

    CKEDITOR.replace(id);
    CKEDITOR.on("instanceReady", function(event){
		$(".cke_path").remove();
	});
}

/**
* PERSONALIZAR EL EDITOR
* @param id -> id 
* @param alto -> tamano, 0 para default
* @param resize -> true/false
*/
function EditorCustom(id, alto, resize){
	//config.height = alto; 
	
	var id = document.getElementById(id);

	if(alto == 0){
		alto == 200;
	}

    CKEDITOR.replace(id, {
        height: alto,
        uiColor: '#f4f4f4',
        resize_enabled: resize
	});

    CKEDITOR.on("instanceReady", function(event){
		$(".cke_path").remove();
	});

}

/*
* ACTUALIZA LOS CAMBIOS ECHOS EN EL EDITOR!
*/
function EditorUpdateContent() {
    for (instance in CKEDITOR.instances) {
        CKEDITOR.instances[instance].updateElement();
    }
}

/**
* CREA SELECTOR MULTIPLE CON FILTRO DE BUSQUEDA
* @param id -> id del selectoR
*/
function SelectorMultipleFiltro(){
	$("select").multiselect().multiselectfilter({
		//filtro
	    filter: function(event, matches){
	        if( !matches.length ){
	            //notificaAtencion("Deve seleccionar almenos una opcion.");
	        }else{
	        	
	        }
	    }
	});
}


/**
* FUNCION PARA MOSTRAR EL LOADER DE JQUERY
*/
function Loading(){
  	if($.browser.msie && jQuery.browser.version < 10){
		var imagen = '<img id="loader-imagen" src="images/ajax_loader_green_128.gif" />';
		$("#loader").html(imagen);
	}else{
		$("#loader").css("display" , "block");
	}
}

/**
 * QUITA EL LOADER
 * @return true cuando termina
 */
function LoadingClose(){
	
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

/**
* FUNCTION GENERICA PARA CANCELAR CUALQUIER ACCION EN #content
*/
function CancelarContent(){
	notificaAtencion("Operacion Cancelada.");

	//limpia el contenido, con effecto
	$("#content").fadeOut(500, function(){
		$("#content").html("");
		$("#content").fadeIn();
	});
	
	//elimina el submit en un form
	$("form").submit(function(e){
		e.preventDefault();
		return false;
	});
}

/**
* LIMPIAR CONTENT
*/
function LimpiarContent(){
	//limpia el contenido, con effecto
	$("#content, #content-disable").fadeOut(500, function(){
		$("#content-disable").remove();
		$("#content").html("");
		$("#content").fadeIn();
	});
}

/**
* DESHABILITA CONTENT
*/
function DeshabilitarContent(){
	$("#content").prepend('<div class="content-disable" id="content-disable"><p><img src="images/ajax_loader_green_128.gif"/></p></div>');
}

/**
* HABILITA CONTENT SIN LIMPIARLO
*/
function HabilitarContent(){
	$(".content-disable").remove();
}

/**
* MUESTRA EL FORM PARA ARCHIVOS ADJUNTOS
*/
function Adjuntos(){

	if($(".adjuntos").is(":visible")){
		$(".adjuntos").slideUp(700);
	}else{
		$(".adjuntos").slideDown(700,function(){
			
			if( !$("#mensajeAdjuntos").is(":visible") ){
				notificaAtencion("<span id='mensajeAdjuntos'></span>Puede adjuntar:<br/>Imagenes,Documentos y comprimidos ZIP");
			}

		});
	}
}

/**
* CARGA UN INPUT MAS PARA UN ARCHIVO EXTRA
*/
function AdjuntoExtra(){
	var extra = $(".adjuntos div:last").attr("id");
	extra = extra.substring(7);
	extra = parseInt(extra);
	extra += 1;

	//maximo
	if( extra > 9 ){
		notificaAtencion("Lo sentimos no se permiten mas de 10 archivos adjuntos.");
		return;
	}

	var nuevo = '<div id="archivo'+extra+'" class="adjunto"><hr><span class="adjuntos-boton" onClick="EliminarAdjuntoExtra('+extra+')">-</span><input type="text" name="archivoNombre'+extra+'" placeholder="Nombre" /> <input type="file" name="archivo'+extra+'" /></div>'

	$(".adjuntos").append(nuevo);
	$("#archivo"+extra).hide();
	$("#archivo"+extra).slideDown(700);
}

/**
* BORRA UN INPUT EXTRA PARA UN ADJUNTO
*/
function EliminarAdjuntoExtra(id){
	$("#archivo"+id).slideUp(700, function(){
		$("#archivo"+id).remove();
	});
}

/**
 * PREVIEW IMAGEN 
 * @param input -> id del input
 * @param imagen -> id de la imagen donde se carga el preview
 */
function PreviewImage(input, imagen) {
    console.log( input );
	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$("#"+imagen).fadeOut(500, function(){
				$('#'+imagen).attr('src', e.target.result);

				$('#'+imagen).fadeIn();
			});
			
		};

		reader.readAsDataURL(input.files[0]);
	}
}


/**
* BUSQUEDA AVANZADA CON OPCIONES
* @param id -> id del div contenedor a mostrar y ocultar
* @param input -> id del input para el search
* @param target -> id del lugar donde realuzar la busqueda
* @param table -> true busqueda en tabla, false en lista
*/
function Busqueda(id, input, target, table){

	if(table){
		target += " tr";
	}else{
		target += " li";
	}

	var parent = $("#"+id).parent("div").parent("div").attr("id");

	if( $("#"+id).is(":visible") ){

		if(parent == "menu" || parent == "menu2"){
			
			var scroll = $("#"+parent).innerHeight() - ( $("#"+parent+" .titulo").outerHeight() + $("#"+parent+" .menu-botones").outerHeight(true) );

			$("#"+parent+" .scroll").animate({
				"height": scroll,
			}, { 
				duration: 400, 
				queue: false,
				complete: function(){
					$("#"+parent+" .scroll").css({
						"height" : scroll
					});
				}
			});
		}
		
		$("#"+id).slideUp();
		
		$("#"+input).val("");
		$("#"+target).fadeIn();

		$("#"+target).removeClass('no');
		$("#"+target).removeClass('si');
	}else{
		
		if(parent == "menu" || parent == "menu2"){

			var scroll = $("#"+parent+" .scroll").innerHeight() - $("#"+id).innerHeight();

			$("#"+parent+" .scroll").animate({
				"height": scroll,
			}, { 
				duration: 400, 
				queue: false,
				complete: function(){
					$("#"+parent+" .scroll").css({
						"height" : scroll
					});
				}
			});
		}
		$("#"+id).slideDown();
	}

	//busqueda en vivo
	BusquedaLive(input, target);

}

/**
* BUSQUEDA AVANZADA
* @param input -> id del input de search
* @param target -> id del lugar donde buscar
*/
function BusquedaLive(input, target){

	//actualiza al ir escribiendo
	$("#"+input).keyup(function(){
		
		var busqueda = $("#"+input).val();
		//var busqueda = $("#"+input).val().split(","), count = 0;
		busqueda = busqueda.replace(/\s/g, ""); //quita espacios en blanco
		busqueda = busqueda.split(","); //compone array separando por las comas
		busqueda = busqueda, count = 0;

		//recorre opciones para buscar
        $("#"+target).each(function(){
        	var element = $(this);

        	$.each(busqueda,function(fila, valor){
        		var title = element.attr('title');
        		var clase = element.attr('class');

        		if(title ==  undefined || title == null){
        			title = '';
        		}
        		if(clase ==  undefined || clase == null){
        			clase = '';
        		}

        		//busqueda
        		if(element.text().search(new RegExp(valor, "i")) < 0 && title.search(new RegExp(valor, "i")) < 0  && clase.search(new RegExp(valor, "i")) < 0 ){
	                
	                element.hide();
	                
	                if( !element.hasClass('si') ){
		 				element.addClass('no');	                	
	                }

	            //muestra considencias
	            } else {

	            	if( !element.hasClass('no') ){
	            		element.show();
	                	count++;
	            	}else{
	            		element.removeClass('no');
	            		element.addClass('si');
	            	}

	            	//element.fadeIn();
	                //count++;
	            }
        	});

        });
        $("#"+target).removeClass('no');
        $("#"+target).removeClass('si');
	});

}

/**
* BUSQUEDA QUE ENFOCA EN LUGAR DE MOSTRAR LOS RESULTADOS
* @param id -> id del div contenedor a mostrar y ocultar
* @param input -> id del input para el search
* @param target -> id del lugar donde realuzar la busqueda
* @param table -> true busqueda en tabla, false en lista
*/
function BusquedaFocus(id, input, target, table){

	if(table){
		target += " tr";
	}else{
		target += " li";
	}

	if($("#"+id).is(":visible")){
		$("#"+id).slideUp();
		
		$("#"+input).val("");
		$("#"+target).fadeIn();

		$("#"+target).removeClass('no');
		$("#"+target).removeClass('si');
		$("#"+target).removeClass('focus');

	}else{
		$("#"+id).slideDown();
	}

	//busqueda en vivo
	BusquedaFocusLive(input, target);

}

/**
* BUSQUEDA AVANZADA
* @param input -> id del input de search
* @param target -> id del lugar donde buscar
*/
function BusquedaFocusLive(input, target){

	//actualiza al ir escribiendo
	$("#"+input).keyup(function(){
		var busqueda = $("#"+input).val();
		//var busqueda = $("#"+input).val().split(","), count = 0;
		busqueda = busqueda.replace(/\s/g, ""); //quita espacios en blanco
		busqueda = busqueda.split(","); //compone array separando por las comas
		busqueda = busqueda, count = 0;

		//recorre opciones para buscar
        $("#"+target).each(function(){
        	var element = $(this);

        	$.each(busqueda,function(fila, valor){
        		var title = element.attr('title');
        		var clase = element.attr('class');

        		if(title ==  undefined || title == null){
        			title = '';
        		}
        		if(clase ==  undefined || clase == null){
        			clase = '';
        		}

        		//busqueda
        		if(element.text().search(new RegExp(valor, "i")) < 0 && title.search(new RegExp(valor, "i")) < 0  && clase.search(new RegExp(valor, "i")) < 0 ){
	                
	               	//element.removeClass('focus');
	                
	                if( element.hasClass('focus') ){
		 				element.removeClass('focus'); 
	                }

	            //muestra considencias
	            } else {

	            	if( !element.hasClass('focus') ){
	            		element.addClass('focus');
	                	count++;
	            	}else{
	            		element.addClass('focus');
	            	}
	            }
        	});

			if(busqueda == ''){
				$("#"+target).removeClass('focus');
			}

        });
	});
}

/**
* FUNCION PARA EL AUTO SALVADO DE LOS FORMULARIOS
*/
function AutoSave(form){

    setInterval(function() {
    	$("#"+form).find("input, select").change(function(){
    		notifica("cambio en input")
    	});

    	$.cookie("autosave", true);
    	notifica("auto guardando");
    	$("#"+form).submit();
    	$.cookie("autosave", false);
	}, 5000);

}


/**
* MUESTRA EL BUSCADOR GLOBAL
*/
function BuscarGlobal(){

	if($("#searchbar").is(":visible")){
		BuscarGlobalHide()
	}else{
		BuscarGlobalShow()
	}

}

function BuscarGlobalHide(){
	$("#searchbar").hide();
	$("#searchbar input").val('');
	$("#toolbarMenu, #toolbarMenu div").css("background-color", "#fff");
}

function BuscarGlobalShow(){
	$("#searchbar").show();
	$("#toolbarMenu, #toolbarMenu div").css("background-color", "#F4F4F4");
}


/**
* SELECT ALL GENERICO
* @param string id -> id donde actuar
* @param boolean table -> true es tabla -> select td's, false -> select li's
* @param boolean checkox -> true hay que chekear checbox dentro de target
*/
function SelectAll(id, table, checkbox){
	
	if(table){
		target = '#'+id+' td';
	}else{
		target = '#'+id+' li';
	}
	console.log(target);

	$(target).each(function(){
		$(this).addClass('seleccionada');

		if(checkbox){
			$(this).find(':checkbox').attr('checked', true);
		}
	});
}

/**
* SELECT ALL GENERICO
* @param id -> id donde actuar
* @param table -> true es tabla -> select td's, false -> select li's
* @param checkox -> true hay que chekear checbox dentro de target
*/
function SelectAllPreview(id, table, checkbox){
	
	if(table){
		target = '#'+id+' td';
	}else{
		target = '#'+id+' li';
	}
	console.log(target);

	$(target).each(function(){
		$(this).addClass('seleccionada');

		if(checkbox){
			$(this).find(':checkbox').attr('checked', true);
		}
	});
	
	RegistrarPreview();
}

/**
* DESSELECT ALL GENERICO
* @param id -> id donde actuar
* @param table -> true es tabla -> select td's, false -> select li's
* @param checkox -> true hay que chekear checbox dentro de target
*/
function UnSelectAll(id, table, checkbox){
	
	if(table){
		target = '#'+id+' td';
	}else{
		target = '#'+id+' li';
	}
	console.log(target);

	$(target).each(function(){
		$(this).removeClass('seleccionada');

		if(checkbox){
			$(this).find(':checkbox').attr('checked', false);
		}
	});

	RegistrarPreview();
}
