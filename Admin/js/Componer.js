/**
 * JAVASCRIPT PARA VISTA DE COMPOSICION
 */

/**
 * COMPONER UN PROYECTO
 * @param int id -> id del articulo ha componer
 */
function Componer(id){

	/*$componer.init(id);

	ComponerCategorias(id);*/

    $("#content").html('<div id="tree"></div>');

    $("#tree").hztree({
        title   : "Categorias",
        project : id,
        ajaxUrl : "src/ajaxComponer2.php",
    });
}

/******************************* CATEGORIAS ****************************/

/**
 * CATEGORIAS INCLUIDAS DEL PROYECTO
 * @param id -> id del proyecto
 */
function ComponerCategorias(id){

	var queryParams = {"func" : "Categorias", "proyecto" : id};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxComponer.php",
		beforeSend: function(){
		},
		success: function(response){
			$("#menu").html(response);
		},
		fail: function(){
			notificaError("Error: Componer.js ComponerCategorias(), AJAX fail.");
		}
	});
}

/**
 * MUESTRA CATEGORIAS HIJAS DE UNA SELECCIONADA
 * @param id -> id de la hija
 */
function HijosComponer(padre){
	notifica('HijosComponer');

	ComponerLimpiarHermanos(padre);

	var queryParams = {'func' : "CategoriasHijas", "padre" : padre};

	//carga hijos
	$.ajax({
		data: queryParams,
		type: "post",
		async: false,
		url: "src/ajaxComponer.php",
		beforeSend: function(){
		},
		success: function(response){
			$("#categorias-componer").append(response);

			$("#Padre"+padre).hide();
			$("#Padre"+padre).fadeIn(500);

			var totalWidth = 0;

			$('.categoria').each(function(index) {
				totalWidth += parseInt($(this).width(), 10);
			});
					
			totalWidth += $("#Padre0").width() + 100;

			$("#categorias-componer").css('width', totalWidth); //aumenta el tamano del contenedor de categorias
				
			SeleccionaCategoriaComponer(padre);

			if( $("#Padre"+padre).length ){
				$("#menu").scrollTo( $("#Padre"+padre) , 700);				
			}
		},
		fail: function(){
			notificaError("Error: Componer.js HijosComponer() AJAX fail.");
		}
	});
}

/**
* SELECCIONA UNA CATEGORIA Y CARGA MENU
* @param hijo -> id hijo seleccionado
*/
function SeleccionaCategoriaComponer(hijo){

	var padre = $("#"+hijo).closest("div").attr('id');

	if($("#"+hijo).hasClass("seleccionada")){
		if(padre == "Padre0"){
			//$("#"+hijo).removeClass('seleccionada');
			//$("#categoria"+hijo).attr('checked', false);
		}		
	}else{
		$("#"+hijo).addClass('seleccionada');
		$("#categoria"+hijo).attr('checked', true);
	}
	MenuComponerCategoria(hijo);
}

/**
 * CARGA EL CONTEXT MENU DE LA CATEGORIA SELECCIONADA
 * @param id -> id de la categoria seleccionada
 */
function MenuComponerCategoria(id){
	$.contextMenu({
        selector: '#'+id, 
        callback: function(key, options) {
            var m = key;
            $componer.EventosMenu(m, id);
        },
        items: {
        	"incluir": {name: "Incluir Selecciones", icon: "add", accesskey: "i"}
        }
    });
}

/**
* BORRAR LOS HERMANOS DE UN NODO
* @param padre
*/
function ComponerLimpiarHermanos(padre){
	var queryParams = {'func' : 'GetHermanos', 'padre' : padre};
	
	$.ajax({
		data: queryParams,
		async: false,
		type: "post",
		url: "src/ajaxEdicion.php",
		beforeSend: function(){
			//$("#menu").html('<img id="image-loader" src="images/ajax-loader.gif" />');
		},
		success: function(response){
			if(response.length > 0){
				
				var hermanos = $.parseJSON(response); 
				
				$.each(hermanos, function(f,c){
					if($("#Padre"+c).length ){
						ComponerLimpiarCamino(c);
					}
				});
			}
		},
		fail: function(){
			notificaError("Error: Componer.js ComponerLimpiarHermanos() AJAX fail.");
		}
	}).done(function ( data ) {
		  ComponerLimpiarCamino(padre);
	});
}

/**
* LIMPIA EL CAMINO DEL ARBOL DE CATEGORIAS
* @param padre -> id del padre
*/
function ComponerLimpiarCamino(padre){
	//obtiene el padre del padre, para ver si no es root
	var Padre = $("#"+padre).closest("div").attr("id");

	if(Padre == "Padre0"){ //si es root entonces limpia todos los resultados
		$(".categoria").remove();
		return;
	}

	//BORRA HIJOS
	if( $("#Padre"+padre).length ){
		
		$("#Padre"+padre).fadeOut(500, function(){
			$("#Padre"+padre).remove();
		});
		
		//obtiene los hijos del padre seleccionado
		var queryParams = {'func' : 'GetHijos', 'padre' : padre};
		$.ajax({
			data: queryParams,
			async: false,
			type: "post",
			url: "src/ajaxEdicion.php",
			beforeSend: function(){
				//$("#menu").html('<img id="image-loader" src="images/ajax-loader.gif" />');
			},
			success: function(response){
				if(response.length > 0){
					var hijos = $.parseJSON(response); 
					//alert(response);
					$.each(hijos, function(f,c){
						LimpiarCamino(c);
					});
				}else{
					//no hay hijos que borrar
				}
			},
			fail: function(){
				notificaError("Error: Componer.js ComponerLimpiarCamino() AJAX fail.");
			}
		});
	}

}

/*************************** PREVISUALIZACIONES  ********************/

/**
* MUSTRA EL PREVIEW DE LAS NORMAS DE UNA CATEGORIA
* @param id -> id de la categorias
*/
function PreviewCategoriaNormas(id, proyecto){

    if( typeof proyecto === "undefined"){
        proyecto = $componer.proyecto;
    }
    alert("enviando preview proyecto "+proyecto);
	var queryParams = {"func" : "NormasIncluidas", "proyecto" : proyecto, "categoria" : id};
	var alto = $("html").height() * 0.6;

	$.fancybox({
	 	'width'         : '70%',
	 	'height'        : alto,
        padding         : 10,
        autoSize        : false,
        fitToView       : false,
        arrows          : false,
        href            : "src/preview.php",
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
		title           : 'Composición Normas'
    });

    notificaAtencion("Seleccione las Normas y sus articulos que desea incluir en la categoria.");
}

/**
* INICIALIZA LAS NORMAS INCLUIDAS
*/
function InitNormasIncluidas(){
	var alto = ( $("html").height() * 0.6) - ( $("#NormasIncluidas .titulo").innerHeight() + $("#NormasIncluidas .preview-botones").innerHeight() + $("#panelNormasTitulo").innerHeight() +30 );

	$("#panelNormas ul li").click(function(){
		$("#panelNormas ul li").removeClass('last');

		if($(this).hasClass('seleccionada')){
			$(this).removeClass('seleccionada');
			$(this).find(':checkbox').attr('checked', false);
		}else{
			$(this).addClass('last');
			$(this).addClass('seleccionada');
			$(this).find(':checkbox').attr('checked', true);

			if( !$("#VerArticulosIncluidos").is(":visible") ){
				$("#VerArticulosIncluidos").fadeIn();
			}
		}

		RegistrarNormasIncluidas();

		$(this).dblclick(function(){
			
			if( $.cookie("cargando") == 'false'){
				$.cookie("cargando", true);
				ArticulosIncluidos( $(this).attr('id') );
			}
			
		});
	})

	$("#NormasIncluidas .panel").each(function(){
		var id = $(this).attr('id');
		$( "#"+id+" ul").css({"height":alto});
	});
	$("#DatosArticulo").css({"height":alto});

	//navegacion
	$("#VerArticulosIncluidos").click(function(){
		//VerArticulosIncluidos();
		lastNorma();
	});

	$("#VerNormasIncluidas").click(function(){
		VerNormasIncluidas()
	});

	$("#VerArticulosDatos").click(function(){
		//VerArticulosDatos();
		lastArticulo();
	});

	$("#VerArticulosIncluidosA").click(function(){
		VerArticulosIncluidos();
	});

}

/**
* REGISTRA LAS NORMAS INCLUIDAS
*/
function RegistrarNormasIncluidas(){
	
	var normas = [];
	var cuenta = 0;
	$("#panelNormas ul li").each(function(){
		if( $(this).hasClass("seleccionada") ){
			normas.push( $(this).attr('id') );
			cuenta++;
		}
	});
	if(cuenta == 0){
		normas = '';
	}

	var proyecto = $("#proyecto").val();
	var categoria = $("#categoria").val();
	var queryParams = {"func" : "RegistrarNormasIncluidas", "proyecto" : proyecto, "categoria" : categoria, "normas" : normas};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/preview.php",
		success: function(response){
			if(response.length > 3){
				notificaError("Error: Componer.js RegistrarNormasIncluidas().<br/>"+response);
			}
		},
		fail: function(response){
			notificaError("Error: AJAX fail Componer.js RegistrarNormasIncluidas()><br/>"+response)
		}
	});
}

/**
 * ULTIMA NORMA SELECCIONADA
 */
function lastNorma(){
	var norma = $("#panelNormas ul .last").attr("id");
	ArticulosIncluidos(norma)
}

/**
* ULTIMO ARTICULO SELECCIONADO
*/
function lastArticulo(){
	var articulo = $("#panelArticulos ul .last").attr("id");
	PreviewArticuloDatos(articulo)
}

/**
* CARGA LOS ARTICULOS INCLUIDOS
* @param norma -> id norma
*/
function ArticulosIncluidos(norma){
	VerArticulosIncluidos();
	
	var proyecto = $('#proyecto').val();
	var categoria = $("#categoria").val();
	var queryParams = {"func" : "ArticulosIncluidos", "proyecto" : proyecto, "categoria" : categoria, "norma" : norma};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/preview.php",
		success: function(response){
			
			$("#panelArticulos ul").html(response);
			$.cookie("cargando", false);

			if( $("#panelArticulos ul").length ){

				//evento de click para los articulos
				$("#panelArticulos ul li").click(function(){
					
					$("#panelArticulos ul li").removeClass('last');

					if($(this).hasClass('seleccionada')){
						$(this).removeClass('seleccionada');
						$(this).find(':checkbox').attr('checked', false);
					}else{
						$(this).addClass('last');
						$(this).addClass('seleccionada');
						$(this).find(':checkbox').attr('checked', true);

						if( !$("#VerArticulosDatos").is(":visible") ){
							$("#VerArticulosDatos").fadeIn();
						}
					}

					$(this).dblclick(function(){
			
						if( $.cookie("cargando") == 'false'){
							$.cookie("cargando", true);
							PreviewArticuloDatos( $(this).attr('id') );
						}

					});

					//registra selecciones
					RegistrarArticulosIncluidos();

				});
			}
		},
		fail: function(response){
			notificaError("Error: AJAX fail, Componer.js ArticulosIncluidos().<br/>"+response);
		}
	});
}

/**
* GUARDA LOS ARTICULOS SELECCIONADOS
*/
function RegistrarArticulosIncluidos(){
	//articulos incluidos, con orden
	var articulos = [];
	var cuenta = 0;
	$("#panelArticulos ul li").each(function(){
		if( $(this).hasClass("seleccionada") ){
			articulos.push( $(this).attr('id') );	
			cuenta++;	
		}
	});

	if(cuenta == 0){
		articulos = '';
	}

	var proyecto = $("#proyecto").val();
	var categoria = $("#categoria").val();
	var norma = $("#norma").val();
	var queryParams = {"func" : "RegistrarArticulosIncluidos", "proyecto" : proyecto, "categoria" : categoria, "norma" : norma, "articulos" : articulos};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/preview.php",
		success: function(response){
			if( response.length > 3 ){
				notificaError("Error: Componer.js RegistrarArticulosIncluidos().<br/>"+response);
			}
		},
		fail: function(response){
			notificaError("Error: AJAX fail Componer.js RegistrarArticulosIncluidos().<br/>"+response);
		}
	});
}

/**
* PREVIEW PARA DATOS DE UN ARTICULOS
* @param articulo -> id articulo
*/
function PreviewArticuloDatos(articulo){
	VerArticulosDatos();

	var proyecto = $("#proyecto").val();
	var categoria = $("#categoria").val();
	var norma = $("#norma").val();
	var queryParams = {"func" : "PreviewArticulo", "proyecto" : proyecto, "categoria" : categoria, "norma" : norma, "id" : articulo};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/preview.php",
		success: function(response){
			$("#DatosArticulo").html(response);
		},
		fail: function(response){
			notificaError("Error: Componer.js PreviewArticuloDatos().<br/>"+response);
		}
	}).done(function(){

		$( "#tabs" ).tabs();
		$( "#tabs2" ).tabs();
		$("#entidades").tagsInput({
			"height":"auto",
	   		"width":"100%",
	   		"defaultText":"",
		});
		$('.tag').find('a').remove();
		$('.tagsinput').find('div').remove();

		$.cookie("cargando", false);
	});


	var query = {"func" : "NombreArticulo", "id" : articulo};

	$.ajax({
		data: query,
		type: "post",
		url: 'src/preview.php',
		success: function(response){
			$("#panelArticuloDatos .subtitulo span").fadeOut(500, function(){
				$(this).html(response);
				$(this).fadeIn();
			});
		},
		fail: function(response){
			notificaError("Error: Componer.js PreviewArticuloDatos() al obtener nombre del articulo.<br/>"+response);
		}
	});
}

/**
* SELECT REGISTRAR, REGISTRA CUANDO SE SELECCIONAN TODOS
*/
function RegistrarPreview(){

	if ( $("#panelNormas").is(":visible") ){
		RegistrarNormasIncluidas();
	}
	
	if ( $("#panelArticulos").is(":visible") ){
		RegistrarArticulosIncluidos();
	}

}

/**
* MUESTRA EL PANEL DE NORMAS
*/
function VerNormasIncluidas(){
	if ( !$("#panelNormas").is(":visible") ){
		$("#panelNormas").css({"display":"inline-block"});
		$("#panelArticulos").css({"width":"99%"});

	    $("#panelArticulos").animate({
			width: "0%",
			float: "left"
		}, { 
			duration: 1400, 
			queue: false,
			complete: function(){
				$("#panelArticulos").css({"display":"none"});
			}
		});

		$("#panelNormas").animate({
			width: "100%"
		}, { 
			duration: 1500, 
			queue: false,
			complete: function(){
				$("#panelNormas").css({
					"display":"inline-block",
					"width" : "100%"
				});
			}
		});
	}
}

/**
* MUESTRA PANEL DE ARTICULOS
*/
function VerArticulosIncluidos(){
	
	if ( !$("#panelArticulos").is(":visible") && $("#panelNormas").is(":visible") ){
	    $("#panelArticulos").css({"display":"inline-block", "float":"right"});
	    $("#panelNormas").css({"width":"99%"});

	    $("#panelNormas").animate({
			width: "0%",
			float: "left"
		}, { 
			duration: 1400, 
			queue: false,
			complete: function(){
				$("#panelNormas").css({"display":"none"});
			}
		});

	    
		$("#panelArticulos").animate({
			width: "100%",
		}, { 
			duration: 1500, 
			queue: false,
			complete: function(){
				$("#panelArticulos").css({
					"display":"inline-block",
					"width" : "100%"
				});
			}
		});

	}

	if ( !$("#panelArticulos").is(":visible") && $("#panelArticuloDatos").is(":visible") ){
	    $("#panelArticulos").css({"display":"inline-block", "float":"left"});
	    $("#panelArticuloDatos").css({"width":"99%"});

	    $("#panelArticuloDatos").animate({
			width: "0%",
			float: "left"
		}, { 
			duration: 1400, 
			queue: false,
			complete: function(){
				$("#panelArticuloDatos").css({"display":"none"});
			}
		});

	    
		$("#panelArticulos").animate({
			width: "100%",
		}, { 
			duration: 1500, 
			queue: false,
			complete: function(){
				$("#panelArticulos").css({
					"display":"inline-block",
					"width" : "100%"
				});
			}
		});

		$("#NuevaObservacion").fadeOut();
	}
}

/**
* MUESTRA PANEL DE DATOS DE UN ARTICULO
*/
function VerArticulosDatos(){
	
	if ( !$("#panelArticuloDatos").is(":visible") ){
	    $("#panelArticuloDatos").css({"display":"inline-block"});
	    $("#panelArticulos").css({"width":"99%","float":"left"});

	    $("#panelArticulos").animate({
			width: "0%",
			float: "left"
		}, { 
			duration: 1400, 
			queue: false,
			complete: function(){
				$("#panelArticulos").css({"display":"none"});
			}
		});

	    
		$("#panelArticuloDatos").animate({
			width: "100%",
		}, { 
			duration: 1500, 
			queue: false,
			complete: function(){
				$("#panelArticuloDatos").css({
					"display":"inline-block",
					"width" : "100%"
				});
			}
		});

		$("#NuevaObservacion").fadeIn();
	}
}

/**
* LIMPIAR PARA PREVIEW NORMAS
*/
function LimpiarNormasIncluidas(){
	if ( $("#panelNormas").is(":visible") ){
		UnSelectAll('panelNormas ul', false, true);
	}
	
	if ( $("#panelArticulos").is(":visible") ){
		UnSelectAll('panelArticulos ul', false, true);
	}

	if ( $("#panelArticuloDatos").is(":visible") ){
		UnSelectAll('panelArticuloDatos ul', false, true);
	}

	RegistrarPreview();
}

/******************** OBSERVACIONES *****************************/

/**
* MUESTRA EL PANEL PARA UNA NUEVA OBSERVACION
*/
function Observacion(){
			
	var alto = $(".fancybox-inner").height();

	$("#observacion").css({'display':"block"});

	$("#observacion").animate({
		height: alto,
	}, { 
		duration: 1500, 
		queue: false,
		complete: function(){
			//$("#NormasIncluidas, .preview-botones").hide();
		}
	});

	var proyecto = $("#proyecto").val();
	var categoria = $("#categoria").val();
	var norma = $("#norma").val();
	var articulo = $("#articulo").val();

	var queryParams = {"func" : "NuevaObservacion", "proyecto" : proyecto, "categoria" : categoria, "norma" : norma, "articulo" : articulo};
	
	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxObservaciones.php",
		success: function(response){
			if( response.length > 0 ){
				$("#observacion").html(response);
				FormularioNuevaObservacion();
			}else{	
				notificaError("Error: Componer.js Observacion().<br/>"+response);
			}
		},
		fail: function(response){
			notificaError("Error: AJAX fail Componer.js Observacion().<br/>"+response);
		}
	});
}

/**
 * CANCELA UNA NUEVA OBSERBACION
 */
function ObservacionCancelar(){

	$("#observacion").animate({
		height: 0,
	}, { 
		duration: 1500, 
		queue: false,
		complete: function(){
			//$("#NormasIncluidas, .preview-botones").fadeIn();
			$("#observacion").html("");
		}
	});
}

/**
* INICIALIZA EL FormularioNuevaObservacion
*/
function FormularioNuevaObservacion(){
	$("#tipo").chosen();

	Editor('observacion-nueva');

	$("#FormularioNuevaObservacion").validationEngine();
		
	var options = {  
	    success: function(response) { 
	    	if(response.length <= 3 ){
	    		notifica("Nueva Observacion Creada.");
	    		ObservacionCancelar();
	    	}else{
	    		notificaError("ERROR: Componer.js FormularioNuevaObservacion().<br/>"+response);
	    	}
		},
		fail: function(){
			notificaError("ERROR: FORM FAIL Componer.js FormularioNuevaObservacion().<br/>"+response);
		}
	}; 
	$('#FormularioNuevaObservacion').ajaxForm(options);
}

/**
 * EDITA UNA OBSERVACION
 */
function EditarObservacion(id){
	var alto = $(".fancybox-inner").height();

	$("#observacion").css({'display':"block"});

	$("#observacion").animate({
		height: alto,
	}, { 
		duration: 1500, 
		queue: false,
		complete: function(){
			//$("#NormasIncluidas, .preview-botones").hide();
		}
	});
	
	var proyecto = $("#proyecto").val();
	var norma = $("#norma").val();
	var articulo = $("#articulo").val();

	var queryParams = {"func" : "EditarObservacion", "id" : id};
	
	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxObservaciones.php",
		success: function(response){
			if( response.length > 0 ){
				$("#observacion").html(response);
				
				FormularioEditarObservacion();
			}else{	
				notificaError("Error: Componer.js Observacion().<br/>"+response);
			}
		},
		fail: function(response){
			notificaError("Error: AJAX fail Componer.js Observacion().<br/>"+response);
		}
	});
}

/**
 * INICIALIZA EL FORMULARIO DE EDICION DE OBSERVACION
 */
function FormularioEditarObservacion(){
	$("#tipo").chosen();

	Editor('observacion-nueva');

	$("#FormularioEditarObservacion").validationEngine();
		
	var options = {  
	    success: function(response) { 
	    	if(response.length <= 3 ){
	    		notifica("Observacion Actualizada.");
	    		
	    		var id = $("#observacionId").val();

	    		var tipo = $("#tipo").val();
	    		EditorUpdateContent();
	    		var observacion = $("#observacion-nueva").val();

	    		$("#observacionTitulo"+id).fadeOut(function(){
	    			var elemento = $(this);
	    			
	    			//obtiene el nombre del tipo
	    			var queryParams = {"func" : "getNombreTipo", "id" : tipo};

	    			$.ajax({
	    				data: queryParams,
	    				type: "post",
	    				url: "src/ajaxObservaciones.php",
	    				success: function(response){
	    					elemento.html(response).fadeIn();
	    				},
	    				fail: function(response){
	    					notificaError("Error: AJAX FAIL, componer.js FormularioEditarObservacion.<br/>"+response);
	    				}
	    			});

	    		});

	    		$("#observacion"+id).fadeOut(function(){
	    			$(this)
	    				.html(observacion)
	    				.fadeIn();
	    		});

	    		//esconde el editor
	    		ObservacionCancelar();
	    	}else{
	    		notificaError("ERROR: Componer.js FormularioEditarObservacion().<br/>"+response);
	    	}
		},
		fail: function(){
			notificaError("ERROR: FORM FAIL Componer.js FormularioEditarObservacion().<br/>"+response);
		}
	}; 
	$('#FormularioEditarObservacion').ajaxForm(options);
}

/**
* COMFIRMACION DE LA ELIMINACION DE UNA OBSERVACION
* @param int id -> id de la observacion
*/
function EliminarObservacion(id){
	var si = function (){
		AccionEliminarObservacion(id);
	}

	var no = function (){
		notificaAtencion("Operacion cancelada");
	}

	Confirmacion("Desea Eliminar la Observacion", si, no);
}

/**
* REALIZA LA ACCION DE ELIMINAR UNA OBSERVACION
* @param int id -> id de la observacion
*/
function AccionEliminarObservacion(id){
	var queryParams = {"func" : "DeleteObservacion", "id" : id};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxObservaciones.php",
		success: function(response){
			console.log(response.length);

			if( response.length <= 3){

				$("#link-tabs4").trigger('click');

				$("#observacionTitulo"+id+", #observacion"+id).fadeOut(function(){
					$(this).remove();
				});

				notifica("Observacion Eliminada");

			}else{
				notificaError("Error: Componer.js AccionEliminarObservacion.<br/>"+response);
			}

		},
		fail: function(response){
			notificaError("Error: AJAX fail, Componer.js AccionEliminarObservacion.<br/>"+response);
		}
	});
}