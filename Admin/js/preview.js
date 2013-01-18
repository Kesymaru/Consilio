/**
 * JAVASCRIPT PARA PREVIEWS
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

	$("#articulos").css("width","0");

	$("#articulos, .siguiente, .atras, #GuardarArticulos").hide();

	//formularios
	FormularioArticulos();
	FormularioNormas();

	//carga dobles clicks
	NormaDobleClick();
});

/**
* INCIALIZA EL FORMULARIO PARA LOS ARTICULOS
*/
function FormularioArticulos(){
		
	var options = {  
		beforeSend: function(){
		},
	    success: function(response) { 
		},
		fail: function(){
		}
	}; 
	$('#FormularioArticulos').ajaxForm(options);
	
}

/**
* INICIALIZA EL FORMULARIO PARA LAS NORMAS
*/
function FormularioNormas(){
		
	var options = {  
		beforeSend: function(){
		},
	    success: function(response) { 
		},
		fail: function(){
		}
	}; 
	$('#FormularioNormas').ajaxForm(options);
	
}

/**
 * CARGA LA FUNCTION DE DOBLE CLICK PARA CADA NORMA
 */
function NormaDobleClick(){
	$("#normas li").each(function(f,c){

		$("#"+this.id).click(function(){
			SelectNormas(this.id);
			return;
		});

		$("#"+this.id).dblclick(function(){
			$("#normas #"+this.id).addClass("seleccionada");
			$("#norma"+this.id).attr('checked', true);
			Articulos(this.id);
			return;
		});



	});
}

/**
 * SELECCIONA UNA NORMA
 * @param id -> id de la norma
 */
function SelectNormas(id){
	if($("#normas #"+id).hasClass("seleccionada")){
		$("#normas #"+id).removeClass("seleccionada");
		$("#norma"+id).attr('checked', false);
	}else{
		$("#normas #"+id).addClass("seleccionada");
		$("#norma"+id).attr('checked', true);
	}
}

/**
* SELECCIONA UN ARTICULO
*/
function SelectArticulo(id){
	if($("#articulos #"+id).hasClass("seleccionada")){
		$("#articulos #"+id).removeClass("seleccionada");
		$("#articulo"+id).attr('checked', false);
	}else{
		$("#articulos #"+id).addClass("seleccionada");
		$("#articulo"+id).attr('checked', true);
	}


}

function Articulos(norma){

	Cambio();

	var proyecto = $("#proyecto").val();

	var queryParams = {"func" : "Articulos", "norma" : norma, "proyecto" : proyecto};

	$("#articulos_proyecto").val(proyecto);
	$("#articulos_norma").val(norma);

	console.log("norma: "+norma);
	console.log("proyecto "+proyecto)

	$.ajax({
		data: queryParams,
		type: "post",
		url: "previewNormas.php",
		success: function(response){
			$("#articulos-list").html(response);
		},
		fail: function(response){

		}
	});
}

function Cambio(){

	if(!$("#articulos").is(":visible")){
		$("#articulos").show();

		$("#articulos").animate({
			"width" : "100%",
			"display" : "block"
		},700, function(){
			$("#articulos").css({
				'width' : "100%",
				"display" : "block"
			});
		});

		$("#normas").animate({
			"width" : "0"
		},700, function(){
			$("#normas").css({
				'width' : "0",
				"display" : "none"
			});
		});

		$(".siguiente, #GuardarNormas").fadeOut();
		$(".atras, #GuardarArticulos").fadeIn();

	}else{

		$("#articulos").animate({
			"width" : "0"
		},700, function(){
			$("#articulos").css({
				'width' : "0",
				"display" : "none"
			});
		});
		
		$("#normas").animate({
			"width" : "100%",
			"display" : "block"
		},700, function(){
			$("#normas").css({
				'width' : "100%",
				"display" : "block"
			});
		});

		

		$(".siguiente, #GuardarNormas").fadeIn();
		$(".atras, #GuardarArticulos").fadeOut();
	}
}

/**
* GUARDA LAS SELECCIONES EN ARTICULOSs
*/
function GuardarArticulos(){
	$("#FormularioArticulos").submit();
}


/**
* GUARDA LAS SELECCIONES EN ARTICULOSs
*/
function GuardarNormas(){
	$("#FormularioNormas").submit();
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
