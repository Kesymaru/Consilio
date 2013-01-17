/**
 * JAVASCRIPT PARA OBSERVACIONES
 */

$(document).ready(function(){
	//tooltips
	$(document).tooltip({
		tooltipClass: "arrow",
	  	position: {
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
	FormularioObservaciones();

	
});

/**
* INCIALIZA EL FORMULARIO PARA LOS ARTICULOS
*/
function FormularioObservaciones(){
		
	var options = {  
		beforeSend: function(){
		},
	    success: function(response) {

	    	if(response.length <= 3){
	    		parent.$.fancybox.close();
	    	}else{
	    		notificaError("Error:"+response);
	    	}
		},
		fail: function(){
			notificaError("Error: AJAX FAIL observacion.js FormularioObservaciones()<br/>"+response);
		}
	};

	$('#FormularioObservaciones').ajaxForm(options);
	Editor("observacion")
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

    var tamano = ($('body').height() - $("#datos-preview").height()) - 100 ;

    CKEDITOR.replace(id, {height: tamano+'px'});

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


function Limpiar(){
	var proyecto = $("#proyecto").val();
	var categoria = $("#categoria").val();

	var queryParams = {"func" : "Reset", "proyecto" : proyecto, "categoria" : categoria};
	
	$.ajax({
		data: queryParams,
		type: "post",
		url: "observaciones.php",
		success: function(response){
			CKEDITOR.instances['observacion'].setData(response);
		},
		fail: function(response){
			notificaError("Error: observaciones.js Limpiar().<br/>"+response);
		}
	});
	//CKEDITOR.instances['observacion'].setData("blabalbala");
}


/*****************  */

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
		url: "class/error.php",
		success: function(response){
			text += "<br/>Notifcado al webmaster.";
		}
	});

}