var menu = '';

/**
* PARA EL MENU COMO PANEL DESPLAZABLE CON SCROLL
*/
$(window).scroll(function () { 
      $("#manu .scollers").css("display", "inline").fadeOut("slow"); 
});


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
	});

	$("#searchForm").validationEngine();
    $('input[placeholder]').placeholder();

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
*/
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
				if($(".datos").is(":visible")){
					Box();
				}
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
				if($(".datos").is(":visible")){
					Box();
				}
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
 * PREVIEW IMAGEN 
 * @param input -> id del input
 * @param imagen -> id de la imagen donde se carga el preview
 */
function PreviewImage(input, imagen) {

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
* BUSQUEDA GENERICA
* @param id -> id del 
*/
function BuscarGenerica(id, target){
	if($("#"+id).is(":visible")){
		$("#"+id).slideUp();
		$("#"+id).val("");
		$("#"+target).fadeIn();
	}else{
		$("#"+id).slideDown();
	}
	
	//busqueda en vivo
	BuscarLive(id, target);
}

/**
* BUSQUEDA EN VIVO GENERICA
* @param input -> id del input 
* @param target -> lugar donde buscar id
*/
function BuscarLive(input, target){
	//actualiza al ir escribiendo
	$("#"+input).keyup(function(){
		var busqueda = $("#"+input).val(), count = 0;

		//recorre opciones para buscar
        $("#"+target).each(function(){
 
            //esconde a los que no coinciden
            if($(this).text().search(new RegExp(busqueda, "i")) < 0){
                $(this).fadeOut();
 
            //sino lo muestra
            } else {
                $(this).show();
                count++;
            }
        });
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

/********************** MANSORY **********/

function Box(){

	$('.datos').masonry({
	  gutterWidth: 20,
	  isAnimated: true,
	  itemSelector: '.box',
	  columnWidth: function( containerWidth ) {

		if(containerWidth <= 1000 ){
	  		return containerWidth / 1;
	  	}else{
	  		return containerWidth / 2;
	  	}
	  },
	  animationOptions: {
	    duration: 700
	  },
	  isResizable: true,
	  isFitWidth: true
	});

}
/*
function Box(){
	$(".datos .box").gridster({
        widget_margins: [10, 10],
        widget_base_dimensions: [140, 140],
        min_cols: 6
    });
}*/