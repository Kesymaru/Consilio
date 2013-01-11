/**
* JAVASCRIPT PARA LOS PROYECTOS
*/
	
/**
* MUESTRA PRPOYECTO
* @param id -> id del proyecto
*/
function Proyecto(id){
	notifica("mostrando proyecto "+id);
		
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

	$("#supercategorias li").removeClass("root-selected");

	$("#supercategorias #"+padre).addClass("root-selected");

	$("#menu2").html('<table class="panel"><tr><td id="td-categorias" ></td> <td id="td-normas" ></td> <td id="td-articulos" ></td></tr></table>');

	Hijos(padre, proyecto);

}	


/**
* CARGA LOS HIJOS DE UN PADRE SELECCIONADO
*/
function Hijos(padre, proyecto){

	//LimpiarHermanos(padre, proyecto);		

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
	//BORRA HERMANOS ASINCRONAMENTE
	var queryParams = {'func' : 'GetHermanos', 'padre' : padre, 'proyecto' : proyecto};
	
	$.ajax({
		data: queryParams,
		type: "post",
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
function Normas(id){

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
}

/**
 * SELECCIONA UNA NORMA
 */
function SelectNorma(id){
	$("#td-normas li").removeClass("seleccionada");
	$("#td-normas #"+id).addClass("seleccionada");

	//doble click para mostrar la norma
	/*$("#"+id).dblclick(function(){
		Articulos(id);
		return;
	});*/

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
}


/**
* SELECCIONA UN ARTICULO
*/
function SelectArticulo(id){
	$("#td-articulos").removeClass("seleccionada");
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

	var queryParams = {"func" : "DatosArticulo", "id" : id};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxProyectos.php",
		beforesend: function(){
		},
		success: function(response){
			if(response.length > 0){
				$("#content").html(response);
				$("#datos-articulo").hide()
				$("#datos-articulo").fadeIn();
			}else{
				notificaError("Error: "+response);
			}
		},
		fail: function(response){
			notificaError("Error: AJAX fail Proyectos.js DatosArticulo()<br/>"+response);
		}
	});
}

/****************************** HELPERS *******************/

/**
* MUESTRA Y OCULTA NORMAS
*/
function ShowNormas(){
	$("#td-normas div").hide();

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