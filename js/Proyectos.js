/**
* JAVASCRIPT PARA LOS PROYECTOS
*/
	
/**
* MUESTRA PRPOYECTO
* @param id -> id del proyecto
*/
function Proyecto(id){
		
	if(!$("#menu").is(":visible")){
		ActivaMenu()
	}

	//esconde el menu 2
	if($("#menu2").is(":visible")){
		Menu2()
	}

	if($("#content").html() !== ""){
		LimpiarContent();
	}

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

	CategoriasRoot(id);
}

/**
* CATEGORIAS ROOT
* @param proyecto -> id del proyecto
*/
function CategoriasRoot(proyecto){
	var queryParams = {"func" : "CategoriasRoot", "proyecto" : proyecto};
	
	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxProyectos.php",
		beforesend: function(){
		},
		success: function(response){
			
			if(response.length > 0){
				$("#menu").html(response);
			}else{
				notificaError("Error: "+response);
			}

		},
		fail: function(response){
			notificaError("Error: "+response);
		}
	});
}


/**
* SELECCIONA UNA CATEGORIA PADRE Y CARGA SUS HIJOS
*/
function PadreHijos(padre, proyecto){
	if(!$("#menu2").is(":visible")){
		Menu2();
	}

	if($("#content").html() != ""){
		LimpiarContent();
	}

	$("#td-categorias, #td-normas, #td-articulos").html("");

	ShowCategorias();

	$("#supercategorias li").removeClass("root-selected");

	$("#supercategorias #"+padre).addClass("root-selected");

	//$("#menu2").html('<table class="panel"><tr><td colspan="3"> <ul id="camino"><li id="camino-categorias" onClick="ShowCategorias()">Categorias</li></ul> </td></tr>  <tr><td id="td-categorias" ></td> <td id="td-normas" ></td> <td id="td-articulos" ></td></tr></table>');

	Hijos(padre, proyecto);

}	


/**
* CARGA LOS HIJOS DE UN PADRE SELECCIONADO
*/
function Hijos(padre, proyecto){

	LimpiarHermanos(padre, proyecto);		

	var queryParams = {'func' : "Hijos", "padre" : padre, "proyecto" : proyecto};

	//carga hijos
	$.ajax({
		data: queryParams,
		type: "post",
		async: false,
		url: "src/ajaxProyectos.php",
		beforeSend: function(){
		},
		success: function(response){
			if(response.length > 0){

				$("#td-categorias").append(response);
				
				var totalWidth = 0;

				$("#td-categorias li").each(function(index){
					totalWidth += parseInt($(this).width(), 10);
				});
				totalWidth += $("#Padre"+padre).width();

				$("#td-categorias").animate({
					"width" : totalWidth
				},700, function(){
					$("#td-categorias").css('width', totalWidth);
				});

			}else{
				notificaError("Error: "+response);
			}
		},
		fail: function(){
			notificaError("Error: ocurrio un error :(<br/>Codigo: ajaxEdicion 001.");
		}
	});
}

/**
* LIMPIA EL CAMINO DEL ARBOL DE CATEGORIAS
* @param padre -> id del padre
*/
function LimpiarCamino(padre, proyecto){

	//BORRA HIJOS
	if( $("#Padre"+padre).length ){
		
		$("#Padre"+padre).fadeOut(500, function(){
			$("#Padre"+padre).remove();
		});
		
		//obtiene los hijos del padre seleccionado
		var queryParams = {'func' : 'GetHijos', 'padre' : padre};
		$.ajax({
			data: queryParams,
			type: "post",
			url: "src/ajaxProyectos.php",
			beforeSend: function(){
			},
			success: function(response){
				if(response.length > 0){
					var hijos = $.parseJSON(response); 
					
					//alert(response);
					$.each(hijos, function(f,c, proyecto){
						LimpiarCamino(c, proyecto);
					});

				}else{
					//no hay hijos que borrar
				}
			},
			fail: function(){
				notificaError("Error: ocurrio un error.<br/>Codigo: ajaxEdicion 001.");
			}
		});
	}

}

/**
* BORRAR LOS HERMANOS DE UN NODO
* @param padre
*/
function LimpiarHermanos(padre, proyecto){
	
	if($(".datos").is(":visible")){
		$(".datos").fadeOut();
	}

	//BORRA HERMANOS ASINCRONAMENTE
	var queryParams = {'func' : 'GetHermanos', 'padre' : padre, 'proyecto' : proyecto};
	
	$.ajax({
		data: queryParams,
		type: "post",
		async: false,
		url: "src/ajaxProyectos.php",
		beforeSend: function(){
		},
		success: function(response){
			if(response.length > 0){
				
				var hermanos = $.parseJSON(response); 
				
				$.each(hermanos, function(f,c){
					if($("#Padre"+c).length ){
						LimpiarCamino(c);						
					}
				});
			}
		},
		fail: function(){
			notificaError("Error: AJAX fail.<br/>"+response);
		}
	}).done(function ( data ) {
		  LimpiarCamino(padre);
	});

}

/**
* PONE ESTILO PARA CARGAR HIJO COMO SELECCIONADO
* @param hijo -> id hijo seleccionado
*/
function SeleccionaHijo(hijo){

	var padre = $("#"+hijo).closest("div").attr('id');

	$("#"+padre+' li').removeClass('seleccionada');
	$("#"+hijo).addClass('seleccionada');

	var padre = $('#'+hijo).closest('div').attr('id');
	
	if( padre == '0'){
		//si es supercategoria el menu varia
		//ContextMenuSuperCategoria(hijo);
	}else{
		//ContextMenuCategoria(hijo);
	}
}

/**
* CARGA LAS NORMAS DE LA CATEGORIA
* @param $id -> id categoria
*/
function Normas(id, proyecto){
	$.cookie('categoria', id);

	var queryParams = {"func" : "Normas", "id" : id};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxProyectos.php",
		beforesend: function(){
		},
		success: function(response){
			if(response.length > 0){
				$("#td-normas").html(response);
				ShowNormas();
			}else{
				notificaError("Error: "+response);
			}
		},
		fail: function(response){
			notificaError("Error: AJAX fail Proyectos.js Normas()<br/>"+response);
		}
	});

	/** NOMBRE DE LA CATEGORIA 
	var queryParams = {"func" : "CategoriaNombre", "id" : id};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxProyectos.php",
		beforesend: function(){
		},
		success: function(response){
			if(response.length > 0){
				$("#camino-categorias").html(response);
			}else{ 
				//fallback
				$("#camino-categorias").html("Categorias");
			}
		},
		fail: function(response){
			notificaError("Error: AJAX fail Proyectos.js Normas()<br/>"+response);
		}
	});**/
}

/**
 * SELECCIONA UNA NORMA
 */
function SelectNorma(id){
	$("#td-normas li").removeClass("seleccionada");
	$("#td-normas #"+id).addClass("seleccionada");

	Articulos(id);
}

/**
 * MUESTRA ARTICULOS DE UNA NORMA
 * @param $id -> id norma
 */
function Articulos(id){

	var queryParams = {"func" : "Articulos", "id" : id};

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
* SELECCIONA UN ARTICULO
*/
function SelectArticulo(id){
	$("#td-articulos li").removeClass("seleccionada");
	$("#td-articulos #"+id).addClass("seleccionada");

	//TODO DOBLE CLICK
	/*$("#"+id).dblclick(function(){
		DatosArticulo(id);
		return;
	});*/

	DatosArticulo(id);
}

/**
* CARGA LOS DATOS DE UN ARTICULO
*/
function DatosArticulo(id){
	var proyecto = $.cookie('proyecto');
	var categoria = $.cookie("categoria");

	var queryParams = {"func" : "DatosArticulo", "proyecto" : proyecto, "categoria" : categoria, "id" : id };

	$.ajax({
		cache: false,
		type: "post",
		data: queryParams,
		url: "src/ajaxProyectos.php",
		success: function(response){

			if(response.length > 0){
				
				$("#content").html(response);
				$("#datos-articulo").hide()
				$("#datos-articulo").fadeIn();
				
				Menu2();
				
				Editor('comentario');
			}else{
				notificaError("Error: "+response);
			}
		},
		fail: function(response){
			notificaError("Error: AJAX fail Proyectos.js DatosArticulo()<br/>"+response);
		}
	});
}

/**
* MUESTRA PANEL PARA COMENTARIO
*/
function Comentar(){
	var tamano = 3 + ( $("#content").outerHeight() - $("#datos-articulo .titulo").outerHeight() ) - $("#datos-footer").outerHeight() ;
	

	if( $("#panel-comentario").is(":visible") ){
		
		$("#panel-comentario").slideUp(1000, function(){
			$("#datos-footer").css("background-color", "#fff");
		});
		
	}else{
		$("#panel-comentario, #comentarios").css("max-height", tamano);

		$("#datos-footer").css("background-color", "#f3efe6");

		$("#panel-comentario").slideDown(1000);
	}
}

function NewComentario(){

	if( !$("#comentarios").is(":visible") ){
		$("#comentarios").slideDown();
		$("#new-comentario").slideUp();
		$("#NewComentario").show();
	}else{
		$("#NewComentario").hide();
		$("#new-comentario").slideDown();
		$("#comentarios").slideUp();
	}
}

/**
* GUARDA EL COMENTARIO
* @param articulo
*/
function AgregarComentario(articulo){
	var proyecto = $.cookie('proyecto');
	var categoria = $.cookie('categoria');
	EditorUpdateContent();

	var comentario = $("#comentario").val();

	if(comentario.length <= 0 && comentario != "Comentar..."){
		notificaAtencion("Escriba un comentario.");
		return;
	}

	var queryParams = { "func" : "NuevoComentario", "proyecto" : proyecto, "categoria" : categoria, "articulo" : articulo, "comentario": comentario };

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxComentarios.php",
		beforesend: function(){
		},
		success: function(response){

			if(response.length <= 3){
				notifica("Comentario Agregado");
				Comentar();
			}else{
				notificaError("Error: "+response);
			}
			
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

	$("#td-categorias").animate({
		"width" : "100%"
	},700, function(){
		$("#td-categorias").css('width', "100%");
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