/**
* JAVASCRIPT PARA LOS PROYECTOS
*/
	
/**
* MUESTRA PRPOYECTO
* @param id -> id del proyecto
*/
function Proyecto(id){
	//$listaCategorias.Proyecto(id);
	
	if( !$("#menu").is(":visible") ){
		//ActivaMenu()
	}

	//esconde el menu 2
	if( !$("#menu").is(":visible") && !$("#menu2").is(":visible") ){
		PanelMenus()
	}

	/*if($("#content").html() !== ""){
		LimpiarContent();
	}*/

	//selecciona el proyecto en el toolbar
	$("#menuProyectos ul li").removeClass("seleccionado");
	$("#menuProyectos #menuProyecto"+id).addClass("seleccionado");

	if( $("#menuProyecto"+id).html() != $("#menuProyectos span").html() ){
		
		$("#menuProyectos span").fadeOut(500, function(){
			$("#menuProyectos span").html( $("#menuProyecto"+id).html() );
			$("#menuProyectos span").fadeIn();
		});
		
	}

	$.cookie('proyecto', id);

	//CategoriasRoot(id);
	
	//inicializa el panel del proyecto
	$listaCategorias.SuperCategorias(id);
}


/**
 * MUESTRA ARTICULOS DE UNA NORMA
 * @param proyecto -> id del proyecto
 * @param id -> id norma
 */
function Articulos(proyecto, id){
	var categoria = $.cookie('categoria');
	var queryParams = {"func" : "Articulos", "id" : id, "categoria" : categoria, "proyecto" : proyecto};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxProyectos.php",
		beforesend: function(){
		},
		success: function(response){
			if(response.length > 0){
				$("#td-articulos").html(response);
				ShowArticulos();
			}else{
				notificaError("Error: "+response);
			}
		},
		fail: function(response){
			notificaError("Error: AJAX fail Proyectos.js Articulos()<br/>"+response);
		}
	});

	/** NOMBRE DEL ARTIOCULO 
	var queryParams = {"func" : "NormaNombre", "id" : id};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxProyectos.php",
		beforesend: function(){
		},
		success: function(response){
			if(response.length > 0){
				$("#camino-normas").html(response);
			}else{ 
				//fallback
				$("#camino-normas").html("Normas");
			}
		},
		fail: function(response){
			notificaError("Error: AJAX fail Proyectos.js Articulos()<br/>"+response);
		}
	});**/
}


/**
* CARGA LOS DATOS DE UN ARTICULO
*/
function DatosArticulo(id){
	var proyecto = $.cookie('proyecto');
	var categoria = $.cookie("categoria");

	
}

/**
* MUESTRA PANEL PARA COMENTARIO
*/
function Comentar(){
	var tamano = 3 + ( $("#content").outerHeight() - $("#datos-articulo .titulo").outerHeight() ) - $("#datos-footer").outerHeight() ;
	
	if( $("#panel-comentario").is(":visible") ){
		
		if( $("#comentarios table td").length > 0 ){

			if( $("#new-comentario").is(":visible") ){
				$("#comentarios").slideDown();
				$("#new-comentario").slideUp();
			}else{
				$("#panel-comentario").slideUp(1000, function(){
					$("#datos-footer").css("background-color", "#fff");
				});
			}
			
		}else{
			$("#panel-comentario").slideUp(1000, function(){
				$("#datos-footer").css("background-color", "#fff");
			});
		}
		
	}else{
		$("#panel-comentario, #comentarios").css("max-height", tamano);

		$("#datos-footer").css("background-color", "#f3efe6");

		//si hay comentarios
		if( $("#comentarios table td").length > 0 ){
		    $("#comentarios").show();
		    $("#new-comentario").hide();
		}else{
			$("#comentarios").hide();
			$("#new-comentario").show();
		}

		$("#panel-comentario").slideDown(1000);
	}
}

function NewComentario(){

	/*if( !$("#comentarios").is(":visible") ){
		$("#comentarios").slideDown();
		$("#new-comentario").slideUp();
		$("#NewComentario").show();
	}else{
		$("#NewComentario").hide();
		$("#new-comentario").slideDown();
		$("#comentarios").slideUp();
	}*/

	$("#new-comentario").slideToggle();
	$("#comentarios").slideToggle();
}

/**
* GUARDA EL COMENTARIO
* @param articulo
*/
function AgregarComentario(articulo){
	console.log( $listaCategorias.proyecto );
	console.log( $listaCategorias.categoria );
	console.log( $listaCategorias.articulo );

	EditorUpdateContent();

	var comentario = $("#comentario").val();

	if(comentario.length <= 0 && comentario != "Comentar..."){
		notificaAtencion("Escriba un comentario.");
		return;
	}

	var queryParams = { "func" : "NuevoComentario", "proyecto" : $listaCategorias.proyecto, "categoria" : $listaCategorias.categoria, "norma" : $listaCategorias.norma, "articulo" : $listaCategorias.articulo, "comentario": comentario };

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxComentarios.php",
		beforesend: function(){
		},
		success: function(response){

			notifica("Comentario Agregado");

			$("#comentarios table").append(response);

			$("#new-comentario").slideUp();
			$("#comentarios").slideDown();

			EditorReset();
		},
		fail: function(response){
			notificaError("Error: AJAX fail Proyectos.js AgregarComentario.<br/>"+response);
		}
	});

}
	

/****************************** HELPERS *******************/

/**
* MUESTRA Y OCULTA NORMAS
*/
function ShowNormas(){
	$("#camino-normas").html("Normas /");

	$("#td-normas div").hide();
	
	$("#camino-normas").fadeIn();
	$("#camino-articulos").fadeOut();

	$("#td-normas").animate({
		"width" : "100%"
	},700, function(){
		$("#td-normas").css('width', "100%");
	});
	$("#td-normas div").fadeIn(700);


	$("#td-categorias div").fadeOut();
	$("#td-categorias").animate({
		"width" : "0px"
	},700, function(){
		$("#td-categorias").css('width', "0px");
	});
	

	$("#td-articulos div").fadeOut();
	$("#td-articulos").animate({
		"width" : "0px"
	},700, function(){
		$("#td-articulos").css('width', "0px");
	});
}

/**
* MUESTRA Y OCULTA NORMAS
*/
function ShowArticulos(html){

	$("#td-articulos").html(html);
	$("#td-articulos div").hide();

	$("#camino-articulos").fadeIn();

	$("#td-articulos").animate({
		"width" : "100%"
	},700, function(){
		$("#td-articulos").css('width', "100%");
	});
	$("#td-articulos div").fadeIn(700);


	$("#td-categorias div").fadeOut();
	$("#td-categorias").animate({
		"width" : "0px"
	},700, function(){
		$("#td-categorias").css('width', "0px");
	});


	$("#td-normas div").fadeOut();
	$("#td-normas").animate({
		"width" : "0px"
	},700, function(){
		$("#td-normas").css('width', "0px");
	});
}

/**
* MUESTRA Y OCULTA NORMAS
*/
function ShowCategorias(){
	/*$("#td-categorias").html(html);
	$("#td-categorias ul li").hide();*/

	$("#camino-categorias").html("Categorias /");

	$("#camino-normas, #camino-articulos").fadeOut();

	var ancho = $.cookie("ancho");
	if( ancho == 0 ){
		ancho = '100%';
	}

	$("#td-categorias").animate({
		"width" : ancho
	},700, function(){
		$("#td-categorias").css('width', ancho);
	});
	$("#td-categorias div").fadeIn(700);


	$("#td-normas div").fadeOut()
	$("#td-normas").animate({
		"width" : "0px"
	},700, function(){
		$("#td-normas").css('width', "0px");
	});
	

	$("#td-articulos div").fadeOut()
	$("#td-articulos").animate({
		"width" : "0px"
	},700, function(){
		$("#td-articulos").css('width', "0px");
	});
	
}