/**
* JAVASCRIPT PARA ESCALA MATRIZ
*/

$(document).ready(function(){
	$('html, body, div, input, li').bind('cut copy paste', function(event) {
        event.preventDefault();
    });

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

	$("#menuProyectos, #menuUsuario").click(function(){
		
		if($(".dropMenu").is(":visible")){
			$(".dropMenu").slideUp();
			$(".dropMenu").closest("div").css({
				'background-color' : '#fff',
				'color' : '#000',
				'border-color' : '#fff',
				'color' : '#000'
			});
		}

		if($('#'+this.id+" .dropMenu").is(':visible')){
			$('#'+this.id+" .dropMenu").slideUp();
			$('#'+this.id).css({
				'background-color' : '#fff',
				'color' : '#000',
				'border-color' : '#fff',
				'color' : '#000'
			});
		}else{
			$('#'+this.id).css({
				'background-color' : '#a1ca4a',
				'color' : '#fff',
				'border-color' : '#a1ca4a',
				'color' : '#fff'
			});
			$('#'+this.id+" .dropMenu").slideDown();
		}
	});

	$("#searchbar").hide();
	$("#searchForm").validationEngine();
    $('input[placeholder]').placeholder();

    //set cookies
    Cookies();

    // no seleccionable
    $('#main, #menu, #content, table, .disclaim, #menuUsuario, #menuProyectos').disableSelection();

    $shortcuts.init();
});
/*
$(window).load(function(){

	if( $("#logoCliente").height() < 100 ){
    	var padding = ( 100 - $("#logoCliente").height() ) / 2;
    	
    	$("#logoCliente").css("padding-top", padding);
    }
});*/


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
			width: "10%"
		}, { 
			duration: 1500, 
			queue: false,
			complete: function(){
				$("#menu").css({
					'display' : 'block',
					'float' : 'left',
					'min-width' : '50px',
				});
			}
		});

		$("#content").animate({
	       		width: '80%'
	    	}, { 
	    		duration: 1500, 
	    		queue: false,
	    		complete: function(){
	    			$("#content").css({
						'width' : '80%',
						'margin' : '0',
						'display' : 'inline-block'
					});
	    		}
	    });
	}	
}

/*
* MUESTRA EL SEGUNDO MENU
*//*
function Menu2(){
	//OPTIENE EL TAMANO EN PORCENTAJE
	var w = ( 100 * parseFloat($('#content').css('width')) / parseFloat($('#content').parent().css('width')) ).toFixed() + '%';

	if( w == "80%"){
		if( !$("#menu2").is(":visible") ){
			$("#menu2").css({
				"display"    : "block",
				"margin-left": "0",
				"width"      : "0"
			});
		}

		//ANIMACION AL AUMENTAR EL TAMANO DEL MENU2
		$("#content").animate({
	       width: '50%',
	    }, { duration: 500, queue: false });

	    $("#menu2").animate({
	       width: '30%'
	    }, { 
	    	duration: 500, 
	    	queue: false,
	    	complete: function(){
	    		$("#menu2").css({
					"display" : "block",
					"opacity" : "1"
				});
	    	}
	    });

	}else{
		//ESCONDE EL SEGUNDO MENU
		$("#content").animate({
	       width: '80%'
	    }, { duration: 500, queue: false });

	    $("#menu2").animate({
	       width: '0%'
	    }, { 
	    	duration: 500, 
	    	queue: false,
	    	complete: function(){
	    		$("#menu2").css({
					"display": "none",
					"width"  : "0"
				});
	    	}
	    });
	}
}*/

/*
* MUESTRA EL PANBEL DE MENUS CON ANIMACIONES
*/
function PanelMenus(){
			
	if( $('#content').is(":visible") ){
		$shortcuts.lock = 'true';

		$("#content").css({
			'margin' : '0',
			'display' : 'inline-block'
		});

		$("#datos-articulo, #datos-footer, .mis-proyectos, .titulo").fadeOut();

		if( !$("#menu").is(":visible") ){
			$("#menu").css({
				'display' : 'block',
				width : '0px',
			});

			$("#menu").animate({
				opacity: 1,
				//width: 'toggle'
				width: "10%"
			}, { 
				duration: 1500, 
				queue: false,
				complete: function(){
					$("#menu").css({
						'display' : 'block',
						'float' : 'left',
						'min-width' : '50px',
					});
				}
			});
		}

		$("#menu2").css({
			'display' : 'inline-block',
			width : '0px',
			'margin': '0px'
		});

		$("#menu2").animate({
	       width: '80%'
	    }, { 
	    	duration: 1500, 
	    	queue: false,
	    	complete: function(){
	    		$("#menu2").css({
					"display" : "inline-block",
					"opacity" : "1"
				});
				$shortcuts.lock = 'false';
	    	}
	    });

	    $("#content").animate({
	       width: '0%'
	    }, { 
	    	duration: 1400, 
	    	queue: false,
	    	complete: function(){
	    		$("#content").css({
					"display" : "none",
				});
	    	}
	    });

	}else{
		$shortcuts.lock = 'true';

		$("#content").css({
			'margin' : '0',
			'display' : 'inline-block'
		});
		
		$("#menu2").animate({
	       width: '0%'
	    }, { 
	    	duration: 1400, 
	    	queue: false,
	    	complete: function(){
	    		$("#menu2").css({
					"display" : "none",
				});
	    	}
	    });

	    $("#content").animate({
	       width: '80%'
	    }, { 
	    	duration: 1500, 
	    	queue: false,
	    	complete: function(){
	    		$("#content").css({
					"display" : "inline-block",
					'height' : 'auto',
				});
				$("#datos-articulo, #datos-footer").fadeIn();
				$shortcuts.lock = 'false';
	    	}
	    });

	}
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
*/
function notificaError(text) {
	var queryParams = {"error" : text, "site" : "Matriz"};
	$.ajax({
		data: queryParams,
		async: false,
		type: "post",
		url: "Admin/src/class/error.php",
		success: function(response){
			text += "<br/>Notifcado al webmaster.";
		}
	});

  	/*var n = noty({
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
	},7000);*/
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

/**
* INICIALIZA LAS COOKIES
*/
function Cookies(){
	if($.cookie('vista') == null){
		$.cookie('vista', 'home', { expires: 7 });
		$.cookie('proyecto', '', { expires: 7 });
		$.cookie('categoria', 'home', { expires: 7 });
		$.cookie('norma', 'home', { expires: 7 });
		$.cookie('ancho', '100%');
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
	var editor = CKEDITOR.instances[id];
    if (editor) {
    	CKEDITOR.remove(editor);
    	editor.destroy(true);
    }

    CKEDITOR.replace(id);
    CKEDITOR.on("instanceReady", function(event){
		$(".cke_path, .cke_bottom").remove();
	});

	return true;
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
* RESETEA EL EDITOR
*/
function EditorReset(){
	for (instance in CKEDITOR.instances) {
        CKEDITOR.instances[instance].setData("");
    }
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
	//$('#content *').prop('disabled', true);
	$("#content").prepend('<div class="content-disable"><p><img src="images/ajax_loader_green_128.gif"/></p></div>');
}

/**
* HABILITA CONTENT SIN LIMPIARLO
*/
function HabilitarContent(){
	//$('#content *').prop('disabled', false);
	$(".content-disable").remove();
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
		target += "tr";
	}else{
		target += " li";
	}

	if($("#"+id).is(":visible")){
		$("#"+id).slideUp();
		
		$("#"+input).val("");
		$("#"+target).fadeIn();

		$("#"+target).removeClass('no');
		$("#"+target).removeClass('si');
	}else{
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
	                
	                if(!element.hasClass('si')){
		 				element.addClass('no');	                	
	                }

	            //muestra considencias
	            } else {

	            	if(!element.hasClass('no')){
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
* ROTACION CREOSSBROWSER
*/
function Rotar(id, angulo){
	setInterval(
	    function () {
	        $('#'+id).animate({rotate: '+='+angulo+'deg'}, 0);
	    },
	    200
	);
}

/**
* MUESTRA EL BUSCADOR GLOBAL
*/
function BuscarGlobal(){

	if($("#searchbar").is(":visible")){
		BuscarGlobalHide();
	}else{
		BuscarGlobalShow();
	}

}

/**
 * MUESTRA EL BUSCADOR GLOBAL CON ANIMACION
 */
function BuscarGlobalHide(){

	$("#searchbar").animate({
		width : 0,
		//'margin-right': '0px',
		'margin-left': '0px'
	},{
		duration: 700,
		queue: false,
		complete: function(){
			$("#toolbarMenu").css({"margin-right":'10px'});
			$("#searchbar").hide();
		}
	});	
	
	$("#searchbar input").val('');
	
	//$("#toolbarMenu, #toolbarMenu div").css("background-color", "#fff");
}

/**
 * ESCONDE EL BUSCADOR GLOBAL CON ANIMACION
 */
function BuscarGlobalShow(){
	$("#toolbarMenu").css({"margin-right":'-10px'});

	var alto = $('#toolbarMenu').outerWidth();

	//$("#searchbar").css({'margin-left': '0'+'px', 'display' : 'inline-block'});
	$("#searchbar").css({'display' : 'inline-block'});

	$("#searchbar").animate({
		width : alto,
		'margin-left': '0px',
		'display' : 'inline-block',
		'margin-left': '-'+alto+'px'
	},{
		duration: 700,
		queue: false,
		complete: function(){
			$("#searchbar").show();
		}
	});

	//$("#searchbar").show();
	//$("#toolbarMenu, #toolbarMenu div").css("background-color", "#F4F4F4");
}

/**
 * MUESTRA EL MENU 2 CON ANIMACION
 */
function ShowMenu2(){
	 if( !$("#menu2").is(":visible") ){
	 	Menu2();
	 	$("#solapa").removeClass('rotacionInversa');
	 	$("#solapa").addClass('rotacion');
	 }else{
	 	Menu2();
	 	$("#solapa").removeClass('rotacion');
	 	$("#solapa").addClass('rotacionInversa');
	 }
}