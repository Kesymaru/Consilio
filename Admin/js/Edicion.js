
/**
* EDICION DE CATEGORIAS
*/
function EditarCategorias(){
	
	$.contextMenu( 'destroy' );

	if(!$("#menu").is(":visible")){
		ActivaMenu();
	}
	if($("#content").html() != ""){
		LimpiarContent();
	}

	//OPTIENE EL TAMANO EN PORCENTAJE
	var w = ( 100 * parseFloat($('#menu').css('width')) / parseFloat($('#menu').parent().css('width')) ) + '%';

	if( "30%" <= w ){

		//ANIMACION AL AUMENTAR EL TAMANO DEL MENU
		$("#menu").animate({
	       width: '30%'
	    }, { 
	    	duration: 500, 
	    	queue: false,
	    	complete: function(){
	    	}
	    });

	    $("#content").animate({
	       width: '60%'
	    }, { duration: 500, queue: false });
	}

	$.cookie('accion', 'categorias');
	Padres();
}

/**
* CREA EL PANEL DESPLAZABLE DE CATEGORIA
* CARGA SUPERCATEGORIAS
*/
function Padres(){

   	//esconde el segundo menu
    if( $("#menu2").is(":visible") ){
    	Menu2();
	}

	var queryParams = {'func' : "Padres"};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxEdicion.php",
		beforeSend: function(){
			//$("#menu").html('<img id="image-loader" src="images/ajax-loader.gif" />');
		},
		success: function(response){
			$("#menu").html(response);
			$("#categorias").hide();
			$("#categorias").fadeIn(1500);
		},
		fail: function(){
			notificaError("Error: ocurrio un error :(<br/>Codigo: ajaxEdicion 001.");
		}
	});

	if( $.cookie('categorias') != "" ){
		categorias = $.cookie('categorias');
	}

}

/**
* CARGA LOS HIJOS DE UN PADRE SELECCIONADO
*/
function Hijos(padre){

	//LIMPIA RUTAS DE CATEGORIAS DE HERMANOS
	LimpiarHermanos(padre);
	//LimpiarCamino(padre);

	var queryParams = {'func' : "Hijos", "padre" : padre};

	//carga hijos
	$.ajax({
		data: queryParams,
		type: "post",
		async: false,
		url: "src/ajaxEdicion.php",
		beforeSend: function(){
			$("#categorias").append('<img id="image-loader" style="display: inline-block;" src="images/ajax-loader.gif" />');
		},
		success: function(response){
			if(response.length > 0){

				$("#image-loader").fadeOut(500, function(){
					$("#image-loader").remove();
					$("#categorias").append(response);

					$("#Padre"+padre).hide();
					$("#Padre"+padre).fadeIn(500);

					var totalWidth = 0;

					$('.categoria').each(function(index) {
						totalWidth += parseInt($(this).width(), 10);
					});
					
					totalWidth += $("#Padre0").width() + 100;

					$("#categorias").css('width', totalWidth); //aumenta el tamano del contenedor de categorias
					
					var height = $("#categorias").height();
					$("#categorias ul").each(function(){
						$(this).css("min-height", height);
					});	
					$("#menu").scrollTo( $("#Padre"+padre) , 700);
							
				});
				SeleccionaHijo(padre);
			}
		},
		fail: function(){
			notificaError("Error: ocurrio un error :(<br/>Codigo: ajaxEdicion 001.");
		}
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
	
	if( padre == 'Padre0'){
		//si es supercategoria el menu varia
		ContextMenuSuperCategoria(hijo);
	}else{
		ContextMenuCategoria(hijo);
	}
}

/**
* CARGA LAS NORMAS DE UNA CATEGORIA SELECCIONADA
* @param id => id de la categoria
*/
function NormasCategoria(id){

	var queryParams = {"func" : "NormasCategoria", "categoria" : id};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxEdicion.php",
		beforeSend: function(){
			//$("#content").append('<img id="image-loader" src="images/ajax-loader.gif" />');
		},
		success: function(response){
			$.cookie('categoria', id);

			$("#content").html(response);
			FormularioNormasCategoria();
		},
		fail: function(){
			notificaError("Error: ocurrio un error.<br/>Codigo: ajaxEdicion 002.");
		}
	});

}

/**
* INICIALIZA EL FORMULARIO PARA NORMAS CATEGORIA
*/
function FormularioNormasCategoria(){
	//validacion
	$("#FormularioNormasCategoria").validationEngine();
		
	var options = {  
		beforeSend: function(){
			DeshabilitarContent();
		},
	    success: function(response) { 

	    	if(response.length <= 3){
	    		notifica("Norma Actualizada.");

	    		var nombre = $("#nombre").val();
	    		var categoria = $("#categoria").val();

	    		if($("#"+categoria).html() != nombre){
	    			$("#"+categoria).fadeOut(700, function(){
	    				$("#"+categoria).html(nombre);
	    				$("#"+categoria).fadeIn();
	    			});
	    		}

	    		LimpiarContent();

	    	}else{
	    		$("html").html(response);
	    		$("#FormularioNormasCategoria").validate();
	    	}
		},
		fail: function(){
			notificaError("Error: ocurrio un error :(<br/>Codigo: ajaxEdicion 000.");
		}
	}; 
	$('#FormularioNormasCategoria').ajaxForm(options);
}

/**
* SELECCIONA UNA NORMA DE LA LISTA 
*/
function SelectNorma(id){
	if( $("#norma"+id).hasClass('seleccionada') ){
		$("#norma"+id).removeClass('seleccionada');
	}else{
		$("#norma"+id).addClass('seleccionada');
	}
}

/**
* AGREGA LAS NORMAS DISPONIBLES
*/
function AgregarNormasSeleccionadas(){
	//$("#td-disponibles").css("width",$("#td-disponibles").width())

	$('#disponibles .seleccionada').each(function() {
   		
   		$("#"+this.id).animate({
	       	width: "0",
   			"font-size" : "0px"
	    }, { 
	    	duration: 1000, 
	    	queue: false,
	    	complete: function(){
	    		
	    		var id = this.id;
	    		id = id.substring(5);
	    		var norma = $("#"+this.id).html();

	    		$("#"+this.id).remove();

	    		$("#seleccionadas").append('<li id="norma'+id+'" onClick="SelectNorma(\''+id+'\')">'+norma+'</li>');
	    		//agrega el input
	    		$("#td-seleccionadas").append('<input id="normaSelected'+id+'" type="hidden" name="normas[]" value="'+id+'" />')
	    	} 
	    });
	});
}

/**
* DESAGREGAR NORMA SELECCIONADA
*/
function QuitarNormasSeleccionadas(){
	//$("#td-seleccionadas").css("width",$("#td-seleccionadas").width())

	$('#seleccionadas .seleccionada').each(function() {

   		$("#"+this.id).animate({
	       	width: "0",
	       	marginLeft: "100%",
	       	marginRight: "0",
   			"font-size" : "0px"
	    }, { 
	    	duration: 1000, 
	    	queue: false,
	    	complete: function(){
	    		
	    		var id = this.id;
	    		id = id.substring(5);
	    		var norma = $("#"+this.id).html();

	    		$("#"+this.id).remove();
	    		$("#normaSelected"+id).remove();

	    		$("#disponibles").append('<li id="norma'+id+'" onClick="SelectNorma(\''+id+'\')">'+norma+'</li>');
	    	} 
	    });
	});
}

/**
* MUESTRA LA BUSQUEDA EN EL CUADRO DE SELECCION DE NORMAS
* @param tipo -> tipo de busqueda
*/
function BuscarNormaCategoria(tipo){
		
	if(tipo == 'incluidas'){
		if( $("#buscar-seleccionadas input").is(":visible") ){
			$("#buscar-seleccionadas input").val("");
			$("#buscar-seleccionadas input").slideUp();
			$("#td-seleccionadas li").fadeIn();
		}else{
			$("#buscar-seleccionadas input").slideDown();
		}
	}else if(tipo == 'disponibles'){
		if($("#buscar-disponibles input").is(":visible")){
			$("#buscar-disponibles input").val("");
			$("#buscar-disponibles input").slideUp();
			$("#td-disponibles li").fadeIn();
		}else{
			$("#buscar-disponibles input").slideDown();
		}
	}

	BuscarNormaCategoriaAcccion(tipo);
}

/**
* ACCION DE BUSCAR EN LIVE
*/
function BuscarNormaCategoriaAcccion(tipo){
	var selector = '';
	var input = "";
	var busqueda = "";

	if(tipo == 'incluidas'){
		selector = "td-seleccionadas";
		input = "#buscar-seleccionadas input";
	}else if(tipo == 'disponibles'){
		selector = "td-disponibles";
		input = "#buscar-disponibles input";
	}

	//actualiza al ir escribiendo
	$(input).keyup(function(){

		if(tipo == 'incluidas'){
			busqueda = $("#buscar-seleccionadas input").val();
			var count = 0;
		}else if(tipo == 'disponibles'){
			busqueda = $("#buscar-disponibles input").val();
			var count = 0;
		}
		
		//recorre opciones para buscar
        $("#"+selector+" li").each(function(){
 
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
* ORDENAR CATEGORIAS, PERMITE ORDENAR LAS NORMAS
* @param id -> id de la categoria dentro del nivel
*/
function OrdenarCategorias(id){
	var queryParams = {"func" : "ListaCategorias", "id" : id};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxEdicion.php",
		beforeSend: function(){
		},
		success: function(response){
			$("#content").html(response);
			$( "#listaCategorias" ).sortable({
		      placeholder: "ui-state-highlight"
		    });
    		//$( "#listaCategorias" ).disableSelection();
    		
		},
		fail: function(response){
			notificaError("Error: AJAX fail,<br/>"+response);
		}
	});

}

/**
* GUARDA EL ORDEN DE LA LISTA DE CATEGORIAS
* @param padre -> id del padre para refrescar
*/
function GuardarOrdenCategorias(padre){
	var categorias = new Array();

	$("#listaCategorias li").each(function(){

		var id = $(this).attr("id");
		var id = id.substring(4);

		categorias.push(id);

	});

	var queryParams = {"func" : "OrdenarCategorias", "categorias" : categorias};
	
	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxEdicion.php",
		beforeSend: function(){
		},
		success: function(response){
			
			if(response.length <= 3){
				notifica("Orden Guardado.");

				LimpiarContent();
				
				if(padre == 0){
					Padres();
				}else{
					Hijos(padre);
				}
				
			}else{
				notificaError("Error: Edicion.js GuardarOrdenCategorias().<br/>"+response);
			}
		},
		fail: function(response){
			notificaError("Error: AJAX fail,<br/>"+response);
		}
	});

}

/**
* LIMPIA EL CAMINO DEL ARBOL DE CATEGORIAS
* @param padre -> id del padre
*/
function LimpiarCamino(padre){
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
				notificaError("Error: ocurrio un error.<br/>Codigo: ajaxEdicion 001.");
			}
		});
	}

}

/**
* BORRAR LOS HERMANOS DE UN NODO
* @param padre
*/
function LimpiarHermanos(padre){
	//BORRA HERMANOS ASINCRONAMENTE
	var queryParams = {'func' : 'GetHermanos', 'padre' : padre};
	$.ajax({
		data: queryParams,
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
						LimpiarCamino(c);						
					}
				});
			}
		},
		fail: function(){
			notificaError("Error: ocurrio un error.<br/>Codigo: ajaxEdicion 001.");
		}
	}).done(function ( data ) {
		  LimpiarCamino(padre);
	});
}

/**
* CONETEXT MENU CATEGORIA
* CREA EL MENU DE UNA CATEGORIA SELECCIONADA
* @param id -> id de la categoria
*/
function ContextMenuCategoria(id){

	$.contextMenu({
        selector: '#'+id, 
        callback: function(key, options) {
            var m = "clicked: " + key;
            //window.console && console.log(m) || alert(m); 
            MenuCategoria(m, id);
        },
        items: {
        	"nueva": {name: "Nueva Subcategoria", icon: "add", accesskey: "n"},
        	"ordenar": {name: "Ordenar Nivel", icon: "add", accesskey: "o"},
            "editar": {name: "Editar", icon: "edit", accesskey: "e"},
            "eliminar": {name: "Eliminar", icon: "delete", accesskey: "l"},
        }
    });
	
	//PERMITE REASIGNAR EL ID DE LA CATEGORIA A LA COOKIE
	//UTIL PARA TENER MULTIPLES MENUS CONTEXTUALES
	$( $('#'+id) ).mousedown(function(e) {
	    //si es doble click
	    if (e.which === 3) {
	        var cat = $(this).attr('id');
	        $.cookie('categoria', $(this).attr('id') );
	    }

	});
}

/**
* CONTEXT MENU DE SUPERCATEGORIA
* @param id -> id de la categoria
*/
function ContextMenuSuperCategoria(id){

	$.contextMenu({
        selector: '#'+id, 
        callback: function(key, options) {
            var m = "clicked: " + key;
            //window.console && console.log(m) || alert(m); 
            MenuCategoria(m, id);
        },
        items: {
            "nuevoPadre": {name: "Nueva SuperCategoria", icon: "add", accesskey: "s"}, //opcion solo para supercategorias
            "nueva": {name: "Nueva Subcategoria", icon: "add", accesskey: "n"},
            "ordenar": {name: "Ordenar Nivel", icon: "add", accesskey: "o"},
            "editar": {name: "Editar", icon: "edit", accesskey: "e"},
            "eliminar": {name: "Eliminar", icon: "delete", accesskey: "l"},
        }
    });
	
	//PERMITE REASIGNAR EL ID DE LA CATEGORIA A LA COOKIE
	//UTIL PARA TENER MULTIPLES MENUS CONTEXTUALES
	$( $('#'+id) ).mousedown(function(e) {
	    //si es doble click
	    if (e.which === 3) {
	        var cat = $(this).attr('id');
	        $.cookie('categoria', $(this).attr('id') );
	    }

	});
}

/**
* MANEJA EL MENU DE LA CATEGORIA
* @param m -> opcion seleccionada desde el context menu
* @param id -> id de l categoria
*/
function MenuCategoria(m, id){
	var categoria = $.cookie('categoria');

	if(m == 'clicked: nuevoPadre'){
		NuevaCategoria(0);
	}else if(m == 'clicked: nueva'){
		NuevaCategoria(id);
	}else if(m == 'clicked: eliminar'){

		var si = function (){
			DeleteCategoria(id);
		}

		var no = function (){
			notificaAtencion("Operacion cancelada");
		}

		Confirmacion("Esta seguro que desea eliminar la categoria y todos sus subcategorias.", si, no);

	}else if(m == 'clicked: editar'){
		EditarCategoria(id);
	}else if(m == 'clicked: ordenar'){
		OrdenarCategorias(id);
	}
}

/**
* ELIMINA UNA CATEGORIA
* @param categoria -> id de lc ategoria ha eliminar
*/
function DeleteCategoria(categoria){
	//LA CATEGORIA VIENE EN LA COOKIE
	//var categoria = $.cookie('categoria');
	notifica(categoria);
	var queryParams = {"func" : "DeleteCategoria", "categoria" : categoria};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxEdicion.php",
		beforeSend: function(){
		},
		success: function(response){
			notifica("Categoria Eliminada");
			var padre = $("#"+categoria).closest("div").attr('id');

			$("#"+categoria).fadeOut(500, function(){
				
				//LIMPIA EL CAMINO DE LA CATEGORIA ELIMINADA
				if(padre == 'Padre0'){
					$("#Padre0").fadeOut(500);
					Padres();
				}else{
					 LimpiarCamino(categoria);
					 $("#"+categoria).remove();
				}

			});
		},
		fail: function(){
			notificaError("Error: ocurrio un error :(<br/>Codigo: ajaxEdicion 006, al crear subcategoria.");
		}
	});
}

/**
* CARGA EDITOR PARA NUEVO SUBCATEGORIA
* @param padre -> padre a la que pertenece, padre = 0 entonces es superCategoria
*/
function NuevaCategoria(padre){

	var queryParams = {"func" : "NuevaCategoria", "padre" : padre};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxEdicion.php",
		beforeSend: function(){
		},
		success: function(response){

			if(response.length > 0){
				$("#content").html(response);
			}else{
				notificaError("Error: Edicion.js NuevaCategoria() no se resivieron los datos.");
			}
			
			//esconde las opciones de buscar
			$("#buscar-disponibles input").hide();
			$("#buscar-seleccionadas input").hide();

			FormularioNuevaCategoria(padre);			
		},
		fail: function(){
			notificaError("Error: Edicion.js NuevaCategoria() AJAX fail.");
		}
	});
}

/**
 * INICIALIZA EL FORMULARIO DE NUEVA CATEGORIA
 */
function FormularioNuevaCategoria(padre){
	var options = {  
		beforeSend: function(){
			DeshabilitarContent();
		},
		success: function(response) { 
			if(padre == 0 && response.length <= 3){
				notifica("Categoria Creada.");
				Padres();
				HabilitarContent();
				LimpiarContent();
			}else if(response.length <= 3) {
				notifica("Categoria Creada.");
				Hijos(padre);
				HabilitarContent();
				LimpiarContent();
			}else{
			   HabilitarContent();
			   notificaError("Error: Edicion.js FormularioNuevaCategoria() <br/>"+response);
			}
		},
		fail: function(response){
			HabilitarContent();
			notificaError("Error: Edicion.js FormularioNuevaCategoria() AJAX fail.<br/>"+response);
		}
	};
	$('#FormularioNuevaCategoria').ajaxForm(options);
	$('#FormularioNuevaCategoria').validationEngine();
}

/**
* CANCELAR NUEVA CATEGORIA
* @param padre -> id del padre
*/
function CancelarNuevaCateogria(padre){
	$("#FormularioSubCategoria").submit(function(){

		$("#FormularioSubCategoria").fadeOut(700, function(){
			$("#FormularioSubCategoria").remove();
			Categoria(padre);
		});
    	return false;
	});
}

/**
* EDITA UNA CATEGORIA
* @param id -> categoria
*/
function EditarCategoria(id){
	var queryParams = {"func" : "EditarCategoria", "categoria" : id};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxEdicion.php",
		beforeSend: function(){
		},
		success: function(response){

			if(response.length > 0){
				$("#content").html(response);
				FormularioEditarCategoria(id);
			}else{
				notificaError("Error: Edicion.js EditarCategoria() no se resivieron los datos.");
			}		
		},
		fail: function(){
			notificaError("Error: Edicion.js EditarCategoria() AJAX fail.");
		}
	});
}

/**
* FORMULARIO EDITAR CATEGORIA
*/
function FormularioEditarCategoria(id){
	console.log("formulario edicion categoria");
	var options = {  
		beforeSend: function(){
			if( !$.cookie("autosave") ){
				DeshabilitarContent();
			}
		},
		success: function(response) { 

			if( response.length <= 3 ){
				//actualiza nombre si cambia
				var nombre = $("#nombre").val();
					
				if($("#"+id).html() != nombre){
					$("#"+id).fadeOut(500, function(){
						$("#"+id).html(nombre);
						$("#"+id).fadeIn();
					});
				}

				if( $.cookie("autosave") ){

				}else{
					notifica("Categoria Actualizada");

					HabilitarContent();
					LimpiarContent();
				}
				
			}else{
				HabilitarContent();
				notificaError("Error: Edicion.js FormularioEditarCategoria()<br/>"+response);
			}
		},
		fail: function(response){
			HabilitarContent();
			notificaError("Error: Edicion.js FormularioEditarCategoria() AJAX fail.<br/>"+response);
		}
	};
	$('#FormularioEditarCategoria').validationEngine();
	$('#FormularioEditarCategoria').ajaxForm(options);
	//AutoSave('FormularioEditarCategoria');
}

/**
* RESTAURA VISTA UTILIZANDO LAS COOKIES
*/
function RestaurarEdicion(){

	var vista = $.cookie('vista');
	var accion = $.cookie('accion');

	if(vista == 'edicion'){
		if(accion == 'categorias'){
			EditarCategorias();
		}else if(accion == 'normas'){
			EditarNormas();
		}else if(accion == 'entidades'){
			Entidades();
		}else if(accion == 'tipos'){
			Tipos();
		}else{
			//default
			EditarCategorias();
		}
	}
}

/******************************** NORMAS ******************/

/**
* EDICION DE NORMAS
*/
function EditarNormas(){
	$.cookie('accion', 'normas');

	if($('#categorias').length ){
		$('#categorias').fadeOut(500, function(){
			$('#categorias').remove();
		});
	}
	
	if( !$("#menu").is(":visible") ){
		ActivaMenu();
	}

	//CARGA CONTENIDO
	if( $("#vista").is(":visible") ){
		$("#vista").html("");
		Normas();
	}else{
		Normas();
	}

}

/**
* CARGA LAS NORMAS EN EL MENU
*/
function Normas(){
	$.contextMenu( 'destroy' );

	if(!$("#menu").is(":visible")){
		ActivaMenu();
	}
	if($("#content").html() != ""){
		LimpiarContent();
	}

	var queryParams = {"func" : "Normas"};

	$.ajax({
		data: queryParams,
		type: "post",
		async: false,
		url: "src/ajaxNormas.php",
		beforeSend: function(){
		},
		success: function(response){

			if( $("#normas").is(":visible") ){

				$("#normas").fadeOut(500, function(){
					$("#normas").remove();

					$("#menu").append(response);
					$("#normas").hide();
					$("#normas").fadeIn(1000, function(){
						MenuScroll();
					});

					$("#ArticulosNorma, #DeshabilitarNorma, #EditarNorma, #AgregarArticulo, #HabilitarNorma ").hide();
				});				

			}else{
				$("#menu").html("");
				$("#menu").append(response);
				$("#normas").hide();
				$("#normas").fadeIn(1000, function(){
					MenuScroll();
				});

				$("#ArticulosNorma, #DeshabilitarNorma, #EditarNorma, #AgregarArticulo, #HabilitarNorma ").hide();
			}

		},
		fail: function(){

		}
	});
}

/**
* CARGA LAS NORMAS Y SELECCIONA UNA
* @param id -> id de la norma seleccionada
*/
function NormaSeleccionada(id){
	var queryParams = {"func" : "Normas"};
	
	$.ajax({
		data: queryParams,
		type: "post",
		async: false,
		url: "src/ajaxNormas.php",
		beforeSend: function(){
		},
		success: function(response){
			if( $("#normas").is(":visible") ){

				$("#normas").fadeOut(500, function(){
					$("#normas").remove();

					$("#menu").append(response);
					$("#normas").hide();
					$("#normas").fadeIn(1000);

					NormaOpciones(id);
				});

			}else{
				$("#menu").append(response);
				$("#normas").hide();
				$("#normas").fadeIn(1000);

				NormaOpciones(id);
			}
		},
		fail: function(){

		}
	});

	
}

/**
* CREA EL MENU CONTEXTUAL PARA UNA NORMA SELECCIONADA
* id -> id de la norma
*/
function NormaOpciones(id){

	//SELECCIONA
	var padre = $("#"+id).closest("div").attr('id');

	$("#"+padre+' li').removeClass('seleccionada');
	$("#"+id).addClass('seleccionada');

	//MUESTRA BOTONES
	if( $("#"+id).hasClass("deshabilitado") ){ //NORMAS DESHABILITADAS

		$("#DeshabilitarNorma").hide();
		$("#ArticulosNorma, #HabilitarNorma, #EditarNorma, #AgregarArticulo").fadeIn();

		ContextMenuNormaDeshabilitada(id);

	}else{ //NORMAS HABILITADAS
	    
		$("#HabilitarNorma").hide();
		$("#ArticulosNorma, #DeshabilitarNorma, #EditarNorma, #AgregarArticulo").fadeIn();

		ContextMenuNorma(id);
	}
	
}

/**
* CONETEXT MENU NORMA HABILITADA
* CREA EL MENU PARA UNA NORMA SELECCIONADA
* @param id -> id de la categoria
*/
function ContextMenuNorma(id){

	$.contextMenu({
        selector: '#'+id, 
        callback: function(key, options) {
            var m = "clicked: " + key;
            //window.console && console.log(m) || alert(m); 
            MenuNorma(m);
        },
        items: {
        	"nueva": {name: "Nueva Norma", icon: "add", accesskey: "n"},
            "editar": {name: "Editar", icon: "edit", accesskey: "e"},
            "deshabilitar": {name: "Deshabilitar", icon: "delete", accesskey: "d"},
            "sep1": "---------",
	        "articulos": {name: "Articulos", icon: "add", accesskey: "a"},
        }
    });
	
	$("#"+id).dblclick(function(){
		Articulos(id);
		return;
	});
}

/**
* CONETEXT MENU NORMA
* CREA EL MENU PARA UNA NORMA SELECCIONADA
* @param id -> id de la categoria
*/
function ContextMenuNormaDeshabilitada(id){

	$.contextMenu({
        selector: '#'+id, 
        callback: function(key, options) {
            var m = "clicked: " + key;
            //window.console && console.log(m) || alert(m); 
            MenuNorma(m);
        },
        items: {
			"nueva": {name: "Nueva Norma", icon: "add", accesskey: "n"},
            "editar": {name: "Editar", icon: "edit", accesskey: "e"},
            "habilitar": {name: "Habilitar", icon: "delete", accesskey: "h"},
            "sep1": "---------",
	        "articulos": {name: "Articulos", icon: "add", accesskey: "a"},
        }
    });

	//doble click
	$("#"+id).dblclick(function(){
		Articulos(id);
		return;
	});
}

/**
* MANEJA EL MENU DE NORMAS
* @param m -> opcion seleccionada desde el context menu
*/
function MenuNorma(m){
	var categoria = $.cookie('categoria');

	if(m == 'clicked: nueva'){
		NuevaNorma();
	}

	if(m == 'clicked: editar'){
		EditarNorma();
	}

	if(m == 'clicked: deshabilitar'){
		DeshabilitarNorma();
	}

	if(m == 'clicked: habilitar'){
		HabilitarNorma();
	}

	if(m == 'clicked: articulos'){
		Articulos();
	}
}


/**
* CARGA EL FORMULARIO DE EDICION DE UNA NUEVA NORMA
* @param 
*/
function NuevaNorma(){
	//esconde el menu2 si esta presente
	if($("#menu2").is(":visible")){
		Menu2();
	}

	var queryParams = {"func" : "NuevaNorma"};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxNormas.php",
		beforeSend: function(){
		},
		success: function(response){
			if(response.length > 0){
				
				$("#content").html(response);
				FormularioNuevaNorma();
				
				$("#radio-estado").buttonset();

				//esconde el cuadro de adjuntos
				$(".adjuntos").hide();

				$("#tipo").chosen();
			}else{
				notificaError("Error. Al cargar fomrulario para Nueva Norma.");
			}
			
		},
		fail: function(){

		}
	});

}

function FormularioNuevaNorma(){
	//validacion
	$("#FormularioNuevaNorma").validationEngine();
	
	var options = {
		beforeSend: function(){
		},
	    success: function(response) { 
	    	console.log(response)
	    	if(response.length <= 3){
	    		notifica("Norma Creada.");
	    		LimpiarContent();
	    		Normas();
	    	}else{
	    		$("#FormularioNuevaNorma").validate();
	    	}
		},
		fail: function(){
			notificaError("Error: al enviar FormularioNuevaNorma()");
		}
	}; 
	$('#FormularioNuevaNorma').ajaxForm(options);
}

/**
* EDITAR NORMA
* el id de la norma se obtiene de la cookie
*/
function EditarNorma(){
	var norma = $(".seleccionada").attr('id');

	var queryParams = {"func" : "EditarNorma", "norma" : norma};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxNormas.php",
		beforeSend: function(){
		},
		success: function(response){

			//esconde el segundo menu si esta presente
			if( $("#menu2").is(":visible")){
				Menu2();
			}

			$("#content").html(response);

			$("#radio-estado").buttonset();

			//carga formulario
			FormularioNorma();

			$(".adjuntos").hide();

			$("#tipo").chosen();
		},
		fail: function(){

		}
	});
}

/**
* INICIALIZA EL FORMULARIO DE EDICION DE UNA NORMA
*/
function FormularioNorma(){
		//validacion
		$("#FormularioNorma").validationEngine();
		
		var options = {  
			beforeSend: function(){
				DeshabilitarContent();
			},
	    	success: function(response) { 

	    		if(response.length <= 3){
	    			notifica("Norma Actualizada");
	    			var norma = $("#norma").val();
	    			var nombre = $("#nombre").val();
	    			var numero = $("#numero").val();

	    			//ACTUALIZA LA NORMA EN LA LISTA
	    			$("#"+norma).html(nombre+" "+numero);

	    			//si cambia el estado de la norma
	    			var estado = $("#radio-estado :radio:checked").val();

	    			if( $("#"+norma).hasClass("deshabilitado") && estado == 1){
	    				$("#"+norma).removeClass("deshabilitado");
	    			}else if( !$("#"+norma).hasClass("deshabilitado") && estado == 0 ){
	    				$("#"+norma).addClass("deshabilitado");
	    			}

	    			//actualiza botones
	    			NormaOpciones(norma);

	    			//quita el formulario
	    			LimpiarContent();
	    		}else{
	    			notificaError(response);
	    			//$("#FormularioNorma").validate();
	    		}
		    },
		    fail: function(){
				notificaError("Error: Edicion.js FormularioNorma() AJAX fail.");
		    }
		}; 
		$('#FormularioNorma').ajaxForm(options);
}

/**
* DESHABILITAR NORMA, MUESTRA EL CUADRO DE DIALOGO
* @param norma -> id de la norma ha deshabilitar
*/
function DeshabilitarNorma(){
	var norma = $(".seleccionada").attr('id');

	var si = function (){
		//$.contextMenu( 'destroy' );
		DeshabilitaNorma(norma);
	}

	var no = function (){
		notificaAtencion("Operacion cancelada");
	}

	Confirmacion("Esta seguro que desea deshabilitar la norma.", si, no);

}

/**
* DESHABILITA LA NORMA
* @param norma -> id de la norma
*/
function DeshabilitaNorma(norma){

	$("#"+norma).addClass("deshabilitado");

	var queryParams = {"func" : "DeshabilitarNorma", "norma" : norma};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxNormas.php",
		beforeSend: function(){
		},
		success: function(response){
			notifica("Norma Deshabilitada.");
			
			//si tiene los articulos abiertos
			if($("#articulos").is(":visible")){
				if(!$("#articulos").hasClass("deshabilitado")){
					$("#articulos").addClass("deshabilitado");
				}
			}

			NormaOpciones(norma);
		},
		fail: function(){

		}
	});
}

/**
* DESHABILITAR NORMA, MUESTRA EL CUADRO DE DIALOGO
* @param norma -> id de la norma ha deshabilitar
*/
function HabilitarNorma(){

	var norma = $(".seleccionada").attr('id');

	var si = function (){
		//$.contextMenu( 'destroy' );
		HabilitaNorma(norma);
	}

	var no = function (){
		notificaAtencion("Operacion cancelada");
	}

	Confirmacion("Esta seguro que desea Habilitar la norma.", si, no);

}

/**
* DESHABILITA LA NORMA
* @param norma -> id de la norma
*/
function HabilitaNorma(norma){

	$("#"+norma).removeClass("deshabilitado");

	var queryParams = {"func" : "HabilitarNorma", "norma" : norma};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxNormas.php",
		beforeSend: function(){
		},
		success: function(response){
			notifica("Norma Habilitada.");

			//si tiene los articulos abiertos
			if($("#articulos").is(":visible")){
				if($("#articulos").hasClass("deshabilitado")){
					$("#articulos").removeClass("deshabilitado");
				}
			}

			NormaOpciones(norma);
		},
		fail: function(){

		}
	});

}

/**
* VALIDA QUE LA NORMA ESTE DISPONIBLE
*/
function NormaDisponible(field, rules, i, options){
	var normas = '';

	var queryParams = {'func' : "NormasDisponibles"};

	$.ajax({
		data: queryParams,
		async: false,
		type: "post",
		url: "src/ajaxNormas.php",
		beforeSend: function(){
		},
		success: function(response){
			normas =  $.parseJSON(response);
		},
		fail: function(response){
			notificaError("Error: Edicion.js NormaDisponible().<br/>"+response);
		}
	});

	var error = false;

	$.each(normas, function(f,c){

		if ( field.val().toLowerCase() == c.toLowerCase() ) {
			error = true;
		}

	});
	if(error){
		return 'Nombre De Norma no disponible.';
	}
}

/**
* VALIDA NORMAS DISPONIBLES IGNORANDO LA PROPIA
*/
function NormaDisponibleEdicion(field, rules, i, options){
	var normas = '';
	var norma = $("#norma").val();

	var queryParams = {'func' : "NormasDisponibles", "norma" : norma};

	$.ajax({
		data: queryParams,
		async: false,
		type: "post",
		url: "src/ajaxNormas.php",
		beforeSend: function(){
		},
		success: function(response){
			normas =  $.parseJSON(response);
		},
		fail: function(response){
			notificaError("Error: Edicion.js NormaDisponibleEdicion().<br/>"+response);
		}
	});

	var error = false;

	$.each(normas, function(f,c){

		if ( field.val().toLowerCase() == c.toLowerCase() ) {
			error = true;
		}

	});
	if(error){
		return 'Nombre De Norma no disponible.';
	}
}

/**
* VALIDA QUE LA NORMA ESTE DISPONIBLE
*/
function NumeroNormaDisponible(field, rules, i, options){
	var normas = '';
	var tipo = $("#tipo").val();

	var queryParams = {'func' : "NumeroNormasDisponibles", "tipo" : tipo};

	$.ajax({
		data: queryParams,
		async: false,
		type: "post",
		url: "src/ajaxNormas.php",
		beforeSend: function(){
		},
		success: function(response){
			normas =  $.parseJSON(response);
		},
		fail: function(response){
			notificaError("Error: Edicion.js NormaDisponible().<br/>"+response);
		}
	});

	var error = false;

	$.each(normas, function(f,c){

		if ( field.val().toLowerCase() == c.toLowerCase() ){
			error = true;
		}

	});
	if(error){
		return 'Numero De Norma no disponible.';
	}
}

/**
* VALIDA QUE LA NORMA ESTE DISPONIBLE, IGNORANDO LA PROPIA
*/
function NumeroNormaDisponibleEdicion(field, rules, i, options){
	var normas = '';
	var tipo = $("#tipo").val();
	var norma = $("#norma").val();

	var queryParams = {'func' : "NumeroNormasDisponibles", "tipo" : tipo, "norma" : norma};

	$.ajax({
		data: queryParams,
		async: false,
		type: "post",
		url: "src/ajaxNormas.php",
		beforeSend: function(){
		},
		success: function(response){
			normas =  $.parseJSON(response);
		},
		fail: function(response){
			notificaError("Error: Edicion.js NormaDisponible().<br/>"+response);
		}
	});

	var error = false;

	$.each(normas, function(f,c){

		if ( field.val().toLowerCase() == c.toLowerCase() ){
			error = true;
		}

	});
	if(error){
		return 'Numero De Norma no disponible.';
	}
}

/*
* MUESTRA EL SEGUNDO MENU
*/
function Menu2(){
	//OPTIENE EL TAMANO EN PORCENTAJE
	var w = ( 100 * parseFloat($('#menu').css('width')) / parseFloat($('#menu').parent().css('width')) ).toFixed() + '%';

	if( w == "30%"){
		if( !$("#menu2").is(":visible") ){
			$("#menu2").css({
				"display"    : "block",
				"margin-left": "0",
				"width"      : "0"
			});
		}

		//ANIMACION AL AUMENTAR EL TAMANO DEL MENU2
		$("#menu").animate({
	       width: '20%',
	    }, { duration: 500, queue: false });

	    $("#menu2").animate({
	       width: '20%'
	    }, { 
	    	duration: 500, 
	    	queue: false,
	    	complete: function(){
	    		$("#menu2").css({
					"display" : "block",
					"opacity" : "1"
				})
	    	}
	    });

	    $("#content").animate({
	       width: '50%'
	    }, { duration: 500, queue: false });

	}else{
		//ESCONDE EL SEGUNDO MENU
		$("#menu").animate({
	       width: '30%'
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
				})
	    	}
	    });

	    $("#content").animate({
	       width: '60%'
	    }, { duration: 500, queue: false });
	}
}

/********************************* ARTICULOS *********************/

/**
* CARGA LOS ARTICULOS DE UNA NORMA SELECCIONADA
* @param $norma -> id de la norma seleccionada
*/
function Articulos(){
	var norma = $(".seleccionada").attr('id');

	//MUESTRA EL PANEL PARA LOS ARTICULOS CON ANIMACION
	if( !$("#menu2").is(":visible") ){
		Menu2();
	}

	//BORRA CONTENT
	$("#content").html("");

	var queryParams = {"func" : "Articulos", "norma" : norma};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxNormas.php",
		beforeSend: function(){
		},
		success: function(response){

			//si ya los articulos estaban visibles
			if( $("#articulos").length ){

				$("#articulos").fadeOut(500, function(){
					$("#articulos").remove();
					$("#menu2").append(response);
					Menu2Scroll()
				});

			}else{

				$("#menu2").append(response);
				Menu2Scroll();
			}
		},
		fail: function(){
		}
	});
}

/**
* AGREGAR UN ARTICULO
* @param norma -> id de la norma
*/
function NuevoArticulo(norma){
	
	var queryParams = {"func" : "NuevoArticulo", "norma" : norma};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxNormas.php",
		beforeSend: function(){
		},
		success: function(response){
			$("#content").html(response);
			$(".adjuntos").hide();
			FormularioNuevoArticulo(norma);
		},
		fail: function(){
			notificaError("Ocurrion un error :(<br/>Al cargar edicion de nueva norma.");
		}
	});
	
}

/**
* INICIALIZA FORMULARIO PARA NUEVO ARTICULO
*/
function FormularioNuevoArticulo(norma){

	var options = {  
		beforeSubmit: ValidaFormularioNuevoArticulo,
		beforeSend: function(){
			DeshabilitarContent();
		},
		success: function(response) {

			if(response.length <= 3){
				notifica("Articulo Creado.");
				LimpiarContent();
				
				//refresca articulos
				Articulos();
			}else{
				notificaError(response);
			}
		},
		fail: function(){
			notificaError("Error: ocurrio un error :(<br/>Al crear el nuevo articulo.");
		}
	}; 

	$('#FormularioNuevoArticulo').ajaxForm(options);

	//CARGA EDITORES PARA LOS TEXTAREAS
	Editor('resumen');
	Editor('permisos');
	Editor('sanciones');
	Editor('articulo');
	
	$( "#tabs" ).tabs(); //crea tabs para los textareas
	$('#entidades').chosen();
	
	$("#FormularioNuevoArticulo #nombre").change(function(){
		ArticuloDisponible(norma);
	});

	jQuery('#FormularioNuevoArticulo').validationEngine('hideAll')
}

/**
* REALIZA VALIDACION DE DATOS PARA NUEVO ARTICULO
*/
function ValidaFormularioNuevoArticulo(){
	EditorUpdateContent();

	//VALIDACION MANUAL
	var articulo = $("#articulo").val();
	var entidades = $("#entidades").val();
	var nombre = $("#nombre").val();

	if( articulo != '' && articulo != null && entidades != '' && entidades != null && nombre != '' && nombre != null ){
		console.log("todos validados");
		jQuery('#articulo').validationEngine('hide');
		jQuery('#entidades').validationEngine('hide');
		jQuery('#nombre').validationEngine('hide');
		return true;
	}else{

		if(entidades == null || entidades == ''){
			jQuery('#entidades').validationEngine('showPrompt', "Se requiere almenos una entidad.", 'error', true);
			
			$("#entidades").change(function(){
				console.log("cambio");
				ValidaFormularioNuevoArticulo();
			});
		}else{
			jQuery('#entidades').validationEngine('hide');
		}

		if(nombre == null || nombre == ''){
			jQuery('#nombre').validationEngine('showPrompt', "Se requiere un nombre para el articulo.", 'error', true);
		}else{
			jQuery('#nombre').validationEngine('hide');
		}

		if(articulo == null || articulo == ''){
			jQuery('#articulo').validationEngine('showPrompt', "Se requiere un articulo.", 'error', true);
			
			CKEDITOR.instances['articulo'].on('blur', function() {
				ValidaFormularioNuevoArticulo();
			});

		}else{
			jQuery('#articulo').validationEngine('hide');
		}

		return false;
	}    
}

/**
* VALIDACION DEL ARTICULO
*/
function ArticuloDisponible(norma){
	var normas = '';

	var queryParams = {'func' : "ArticuloDisponible", "norma" : norma};

	$.ajax({
		data: queryParams,
		async: false,
		type: "post",
		url: "src/ajaxNormas.php",
		beforeSend: function(){
		},
		success: function(response){
			normas =  $.parseJSON(response);
		},
		fail: function(response){
			notificaError("Error: Edicion.js ArticuloDisponible().<br/>"+response);
		}
	});

	var error = false;

	$.each(normas, function(f,c){

		if ( $("#FormularioNuevoArticulo #nombre").val().toLowerCase() == c.toLowerCase() ) {
			error = true;
		}

	});
	if(error){
		jQuery('#FormularioNuevoArticulo #nombre').validationEngine('showPrompt', 'Articulo no disponible', 'error', true);
	}else{
		jQuery('#FormularioNuevoArticulo #nombre').validationEngine('hide');
	}
}

/**
*SELECCIONA UN ARTICULO
*/ 
function SelectArticulo(articulo){
	
	$("#articulos li").removeClass("seleccionada");
	$("#articulo"+articulo).addClass("seleccionada");

	if(!$("#articulos .ocultos").is(":visible")){
		$("#articulos .ocultos").fadeIn();
	}

	//INICIALIZA EL MENU
	ArticuloContextMenu(articulo);
}

/**
* CONTEXT MENU PARA UN ARTICULO SELECCIONADO
*/
function ArticuloContextMenu(id){

	$.contextMenu({
        selector: '#articulo'+id, 
        callback: function(key, options) {
            var m = "clicked: " + key;
            //window.console && console.log(m) || alert(m); 
            MenuArticulo(m);
        },
        items: {
			"nuevo": {name: "Nuevo Articulo", icon: "add", accesskey: "n"},
            "editar": {name: "Editar", icon: "edit", accesskey: "e"},
            "eliminar": {name: "Eliminar", icon: "delete", accesskey: "l"}
        }
    });

	//doble click para editar articulo seleccionado
	$("#articulo"+id).dblclick(function(){
		if( $.cookie('cargando') == "false"){
			$.cookie('cargando', true);
			EditarArticulo();
		}
		return;
	});
}

/**
* MANEJADOR DE LAS ACCIONES DEL MENU DE UN ARTICULO
*/
function MenuArticulo(m){

	if(m == "clicked: nuevo"){
		norma = $("#normas .seleccionada").attr("id");
		NuevoArticulo(norma);
	}else if(m == "clicked: eliminar"){
		BorrarArticulo()
	}else if(m == "clicked: editar"){
		EditarArticulo();
	}
}

/**
* PARA BORRAR UN ARTICULO
* EL ID DEL ARTICULO SE OBTIENE DESDE EL DOM
*/
function BorrarArticulo(){
	var articulo = $("#articulos .seleccionada").attr("id");

	articulo = articulo.substring(8); //elimina "articulo" del id y deja solo el numero

	var si = function (){
		DeleteArticulo();
	}

	var no = function (){
		notificaAtencion("Operacion cancelada");
	}

	Confirmacion("Esta seguro que desea Eliminar el articulo y todos sus datos.", si, no);

}

/**
* REALIZA EL BORRADO DE UN ARTICULO AL SER CONFIRMADA LA OPCION
* @param articulo -> id del articulo
*/
function DeleteArticulo(){
	var articulo = $("#articulos .seleccionada").attr("id");
	articulo = articulo.substring(8); //elimina "articulo" del id y deja solo el numero

	var queryParams = {"func" : "BorrarArticulo", "articulo" : articulo };

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxNormas.php",
		beforeSend: function(){
		},
		success: function(response){

			if(response.length <= 3){
				notifica("Articulo Eliminado.");

				$("#articulo"+articulo).fadeOut(500, function(){
					$("#articulo"+articulo).remove();
				});

				if($("#content").length){
					LimpiarContent();
				}
			}else{
				//notificaError(response);
				$("#content").html(response);
			}
		},
		fail: function(){
			notificaError("Ocurrio un error al eliminar el articulo.<br/>Intentelo de nuevo.");
		}
	})
}

/**
* EDITA UN ARTICULO
*/
function EditarArticulo(){
	var articulo = $("#articulos .seleccionada").attr("id");
	articulo = articulo.substring(8); //elimina "articulo" del id y deja solo el numero

	var queryParams = {"func" : "EditarArticulo", "articulo" : articulo};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxNormas.php",
		beforeSend: function(){
			Loading();
		},
		success: function(response){
			if(response.length){
				LoadingClose();

				$("#content").html(response);
				FormularioEditarArticulo();

			}else{
				notificaError(response);
			}
			$.cookie('cargando', false);
		},
		fail: function(){
			LoadingClose();
			notificaError("Ocurrio un error :(<br/>Al intentar cargar la edicion del articulo.");
		}
	});
}

/**
* INICIALIZA FORMULARIO PARA NUEVO ARTICULO
*/
function FormularioEditarArticulo(){

	var options = {  
		beforeSubmit: ValidaFormularioNuevoArticulo, //se valida con la misma funcion que al crear nuevo articulo
		beforeSend: function(){
			DeshabilitarContent();
		},
		success: function(response) { 

			if(response.length <= 3){
				notifica("Articulo Actualizado.");
				
				//actualiza nombre articulo
				var id = $("#id").val();
				var nombre = $("#nombre").val();

				if($("#articulo"+id).html() != nombre ){
					$("#articulo"+id).fadeOut(700, function(){
						$("#articulo"+id).html(nombre);
						$("#articulo"+id).fadeIn();
					});
				}
				LimpiarContent();
			}else{
				notificaError(response);
				HabilitarContent();
			}
		},
		fail: function(){
			notificaError("Error: ocurrio un error :(<br/>Al crear el nuevo articulo.");
		}
	}; 

	$('#FormularioEditarArticulo').ajaxForm(options);


	//CARGA EDITORES PARA LOS TEXTAREAS
	Editor('resumen');
	Editor('permisos');
	Editor('sanciones');
	Editor('articulo');
	
	$( "#tabs" ).tabs(); //crea tabs para los textareas
	$('#entidades').chosen();

	var norma = $("#norma").val();
	$("#FormularioEditarArticulo #nombre").change(function(){
		ArticuloDisponibleEdicion(norma);
	});
}

/**
* VALIDACION DEL ARTICULO, NO ES SENSIBLE A MAYOUSCULAS O MINUSCULAS
*/
function ArticuloDisponibleEdicion(norma){
	var normas = '';
	var articulo = $("#id").val();

	var queryParams = {'func' : "ArticuloDisponible", "norma" : norma, "articulo" : articulo};

	$.ajax({
		data: queryParams,
		async: false,
		type: "post",
		url: "src/ajaxNormas.php",
		beforeSend: function(){
		},
		success: function(response){
			normas =  $.parseJSON(response);
		},
		fail: function(response){
			notificaError("Error: Edicion.js ArticuloDisponible().<br/>"+response);
		}
	});

	var error = false;

	$.each(normas, function(f,c){

		if ( $("#FormularioEditarArticulo #nombre").val().toLowerCase() == c.toLowerCase() ) {
			error = true;
		}

	});
	if(error){
		jQuery('#FormularioEditarArticulo #nombre').validationEngine('showPrompt', 'Articulo no disponible', 'error', true);
	}else{
		jQuery('#FormularioEditarArticulo #nombre').validationEngine('hide');
	}
}

/**
* ELIMINA UN ARCHIVO ADJUNTOs
*/
function EliminarAdjunto(id){
	$("#adjuntado"+id).slideUp(700, function(){
		$("#adjuntado"+id).remove();
	});

	var queryParams = {"func" : "EliminarArchivo", "archivo" : id};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxNormas.php",
		beforeSend: function(){
		},
		success: function(response){
			notifica(response);
		},
		fail: function(){
		}
	})
}

/********************************** TIPOS NORMAS ******************************/

/**
* CARAG LA EDICION DE TIPOS DE NORMA
*/
function Tipos(){
	$.cookie('accion', 'tipos');

	$.contextMenu( 'destroy' );

	//activa el menu
	if( !$("#menu").is(":visible")){
		ActivaMenu();
	}

	//esconde el menu2
	if( $("#menu2").is(":visible")){
		Menu2();
	}

	if( $("#content").is(":visible") ){
		$("#content").html("");
	}

	var queryParams = {"func" : "Tipos"};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxTipos.php",
		beforeSend: function(){
			Loading();
		},
		success: function(response){
			LoadingClose();
			$("#menu").html(response);
			MenuScroll();
			$("#EliminarTipo, #EditarTipo").hide();
		},
		fail: function(){
			notificaError("Error: AJAX fail Edicion.js Tipos().<br/>"+response);
		}
	});
	
}

/**
* CARGA EL FORMULARIO PARA UN NUEVO TIPO
*/
function NuevoTipo(){
	var queryParams = {"func" : "NuevoTipo"};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxTipos.php",
		beforeSend: function(){
		},
		success: function(response){
			$("#content").html(response);
			FormularioNuevoTipo();
		},
		fail: function(){
		}
	});
}

/**
* INICIALIZA EL FORMULARIO PARA NU NUEVO TIPO
*/
function FormularioNuevoTipo(){
	//validacion
	$("#FormularioNuevoTipo").validationEngine();
		
	var options = {  
		beforeSend: function(){
		},
	    success: function(response) { 
	    	console.log(response.length);
	    	if(response.length <= 5 ){
	    		notifica("Nuevo Tipo de Norma Creado.");
	    		Tipos();
	    		
	    		$("#content").html("");
	    	}else{
	    		notificaError("Error: Edicion.js FormularioNuevoTipo() <br/>"+response);
	    	}
		},
		fail: function(response){
			notificaError("Error: AJAX fail, Edicion.js FormularioNueoTipo().<br/>"+response)
		}
	}; 
	$('#FormularioNuevoTipo').ajaxForm(options);
}

/**
* CARGA LA EDICION DE UN TIPO
* obtiene el id del tipo seleccionado
*/
function EditarTipo(){
	var tipo = $("#tipos .seleccionada").attr('id'); 

	var queryParams = {"func" : "EditarTipo", "id" : tipo};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxTipos.php",
		beforeSend: function(){
		},
		success: function(response){
			$("#content").html(response);
			FormularioEditarTipo();
		},
		fail: function(){
		}
	})
}

/**
* ELIMINAR TIPO
*/
function EliminarTipo(){
	var tipo = $("#tipos .seleccionada").attr('id');

	var si = function (){
		DeleteTipo(tipo);
	}

	var no = function (){
		notificaAtencion("Operacion cancelada");
	}

	Confirmacion("Esta seguro que desea eliminar el tipo.", si, no);
}

/**
* REALIZA LA ACCION DE ELIMINAR
*/
function DeleteTipo(tipo){
	var queryParams = {"func" : "EliminarTipo", "id" : tipo};

	$.ajax({
		data: queryParams,
		type: "post",
		url : "src/ajaxTipos.php",
		beforeSend: function(){
		},
		success: function(response){
			if(response.length <= 5){
				notifica("Tipo Borrado");
				
				//borra tipo
				$("#"+tipo).fadeOut(500, function(){
					$("#"+tipo).remove();
				});

				//borra content
				$("#content").html("");
			}else{
				notificaError("Error: Edicion.js DeleteTipo().<br/>"+response);
			}
		},
		fail: function(response){
			notificaError("Error: AJAX fail.<br/>"+response);
		}
	})
}

/**
* INICIALIZA EL FORMULARIO DE EDICION
*/
function FormularioEditarTipo(){
	//validacion
	$("#FormularioEditarTipo").validationEngine();
		
	var options = {  
		beforeSend: function(){
		},
	    success: function(response) { 
	    	if(response.length <= 5){
	    		notifica("Tipo De Norma Actualizada.");
	    		
	    		var nombre = $("#nombre").val();
	    		var tipo = $("#tipo").val();

	    		if(nombre != $("#"+tipo).html()){
	    			$("#"+tipo).fadeOut(500, function(){
		    			$("#"+tipo).html(nombre).fadeIn();
		    		});
	    		}

	    		LimpiarContent();
	    	}else{
	    		notificaError("Error: Edicion.js FormularioEditarTipo().<br/>"+response);
	    	}
		},
		fail: function(response){
			notificaError("Error: AJAX fail, Edicion.js FomrularioEditarTipo().<br/>"+response);
		}
	}; 
	$('#FormularioEditarTipo').ajaxForm(options);
}

/**
* SELECCIONA UN TIPO Y CARGA SU CONTEXT MENU
*/
function SelectTipo(id){
	$("#tipos li").removeClass("seleccionada");
	$("#"+id).addClass("seleccionada");

	//MUESTRA LOS BOTONES
	$("#EliminarTipo, #EditarTipo").fadeIn();

	//INICIALIZA EL MENU
	TipoContextMenu(id);
}

/**
* CONTEXT MENU DE UN TIPO 
*/
function TipoContextMenu(id){
	$.contextMenu({
        selector: '#'+id, 
        callback: function(key, options) {
            var m = "clicked: " + key;
            //window.console && console.log(m) || alert(m); 
            MenuTipos(m);
        },
        items: {
        	"nuevo": {name: "Nuevo", icon: "add", accesskey: "n"},
            "editar": {name: "Editar", icon: "edit", accesskey: "e"},
            "eliminar": {name: "Eliminar", icon: "delete", accesskey: "l"},
        }
    });

    //doble click para editar el tipo
	$("#"+id).dblclick(function(){
		EditarTipo();
		return;
	});
}

/**
* PROCESA OPCION SELECCIONADA EN EL CONTEXT MENU DE TIPOS
*/
function MenuTipos(m){
	if(m == 'clicked: nuevo'){
		NuevoTipo();
	}

	if(m == 'clicked: editar'){
		EditarTipo();
	}

	//opcion de eliminar tipo
	if(m == 'clicked: eliminar'){
		EliminarTipo();
	}
}

/********************************** ENTIDADES ******************************/

/**
* CARGA LAS ENTIDADES
*/ 
function Entidades(){
	$.cookie("accion", 'entidades');

	$.contextMenu( 'destroy' );

	if( !$("#menu").is(":visible")){
		ActivaMenu();
	}

	if($("#menu2").is(":visible")){
		Menu2();
	}

	if( $("#content").is(":visible") ){
		$("#content").html("");
	}

	var queryParams = {"func" : "Entidades"};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxEntidades.php",
		beforeSend: function(){
			Loading();
		},
		success: function(response){
			LoadingClose();
			$("#menu").html(response);
			$("#EliminarEntidad, #EditarEntidad").hide();
			MenuScroll();
		},
		fail: function(response){
			notificaError("Error: AJAX FAIL Edicion.js Entidades().<br/>"+response);
		}	
	});
}

/**
* SELECCIONA UNA ENTIDAD Y CARGA EL CONTEXT MENU
*/
function SelectEntidad(id){
	$("#entidades li").removeClass("seleccionada");
	$("#"+id).addClass("seleccionada");

	//MUESTRA LOS BOTONES
	$("#EliminarEntidad, #EditarEntidad").fadeIn();

	//carga el context menu
	EntidadContextMenu(id);
}

/**
* CARGA FORMULARIO PARA UN NUEVO GRUPO
*/
function NuevoGrupo(){
	var queryParams = {"func" : "NuevoGrupo"};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxEntidades.php",
		beforeSend: function(){
		},
		success: function(response){
			$("#content").html(response);
			FormularioNuevoGrupo();
		},
		fail: function(){

		}
	});
}

/**
* INICIALIZA EL FORMULARIO PARA UN NUEVO GRUPO
*/
function FormularioNuevoGrupo(){
	//validacion
	$("#FormularioNuevoGrupo").validationEngine();
		
	var options = {  
		beforeSend: function(){
		},
	    success: function(response) { 
	    	
	    	if(response.length <= 3){
	    		$("#content").html(response);
	    		notifica("Nuevo Grupo de Entidades Agregado.");
	    		Entidades();
	    	}else{
	    		notificaError(response);
	    	}
		},
		fail: function(){
			notificaError("Error: Edicion.js FormularioNuevoGrupo() AJAX fail");
		}
	}; 
	$('#FormularioNuevoGrupo').ajaxForm(options);
}

/**
* CARGA EL FORMULARIO PARA UNA NUEVA ENTIDAD
*/
function NuevaEntidad(){
	var queryParams = {"func" : "NuevaEntidad"};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxEntidades.php",
		beforeSend: function(){
		},
		success: function(response){
			$("#content").html(response);
			FormularioNuevaEntidad();
			$("#grupos").chosen();
		},
		fail: function(){

		}
	});
}

/**
* INICIALIZA EL FORMULARIO
*/
function FormularioNuevaEntidad(){
	//validacion
	$("#FormularioNuevaEntidad").validationEngine();
		
	var options = {  
		beforeSend: function(){
		},
	    success: function(response) { 
	    	if(response.length <= 3){
	    		Entidades();
	    		notifica("Entidad creada.")
	    		$("#content").html();
	    	}else{
	    		notificaError(response);
	    	}
	    	
		},
		fail: function(){
		}
	}; 
	$('#FormularioNuevaEntidad').ajaxForm(options);
}

/**
* ELIMINA UNA ENTIDAD
*/
function EliminarEntidad(){
	var entidad = $("#entidades .seleccionada").attr('id');

	var si = function (){
		DeleteEntidad(entidad);
	}

	var no = function (){
		notificaAtencion("Operacion cancelada");
	}
	if( $("#entidades .seleccionada").hasClass('grupo')){
		Confirmacion("Esta seguro que desea eliminar el grupo y todas sus entidades. ", si, no);
	}else{
		Confirmacion("Esta seguro que desea eliminar la entidad. ", si, no);
	}
	
}

/**
* ELIMINA LA ENTIDAD
* @param entidad -> id de la entidad ha eliminar
*/ 
function DeleteEntidad(entidad){
	var queryParams = {"func" : "DeleteEntidad", "id" : entidad};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxEntidades.php",
		beforeSend: function(){
		},
		success: function(response){
			if(response.length <= 3){
				$("#"+entidad+', .'+entidad).fadeOut(500, function(){
					$("#"+entidad+', .'+entidad).remove();
				});
				notifica("Entidad Borrada")
			}else{
				notificaError(response);
			}
		},
		fail: function(){
		}
	})
}

/**
* EDITAR UNA ENTIDAD
*/
function EditarEntidad(){
	var entidad = $("#entidades .seleccionada").attr('id');
	var queryParams = {"func" : "EditarEntidad", "id" : entidad};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxEntidades.php",
		beforeSend: function(){
		},
		success: function(response){
			$("#content").html(response);
			FormularioEditarEntidad();
			$("#padre").chosen();
		},
		fail: function(){
		}
	});
}

/**
* INICIALIZA EL FORMULARIO PARA EDITAR LA ENTIDAD
*/
function FormularioEditarEntidad(){
	//validacion
	$("#FormularioEditarEntidad").validationEngine();
		
	var options = {  
		beforeSend: function(){
			DeshabilitarContent();
		},
	    success: function(response) { 
	    	if(response.length == 0){
	    		notifica("Entidad Actualizada.");

	    		var entidad = $("#entidades .seleccionada").attr('id');
				var nombre = $("#nombre").val();
				$("#"+entidad).fadeOut(500, function(){
					$("#"+entidad).html(nombre);
					$("#"+entidad).fadeIn();
				});

				if($("#padre").is(":visible")){
					var padre = $("#padre").val();
					notifica($("#"+entidad).closest().attr('id'));

					//si se movio de padre
					if(padre != $("#"+entidad).closest().attr('id') ){
						Entidades();
					}
				}

				//LimpiarContent();
			}else{
				notificaError(response);
			}
		},
		fail: function(){
		}
	}; 
	$('#FormularioEditarEntidad').ajaxForm(options);
}

/**
* CREA EL CONTEXT MENU DE UNA ENTIDAD
*/
function EntidadContextMenu(id){
	$.contextMenu({
        selector: '#'+id, 
        callback: function(key, options) {
            var m = "clicked: " + key;
            //window.console && console.log(m) || alert(m); 
            MenuEntidades(m);
        },
        items: {
        	"nuevo": {name: "Nuevo", icon: "add", accesskey: "n"},
            "editar": {name: "Editar", icon: "edit", accesskey: "e"},
            "eliminar": {name: "Eliminar", icon: "delete", accesskey: "l"},
        }
    });

    //doble click para editar la entidad
	$("#"+id).dblclick(function(){
		EditarEntidad();
		return;
	});
}

/**
* PROCESA OPCION SELECCIONADA DEL MENU DE UNA ENTIDAD
*/
function MenuEntidades(m){
	if(m == 'clicked: nuevo'){
		NuevaEntidad();
	}

	if(m == 'clicked: editar'){
		EditarEntidad();
	}

	//opcion de eliminar tipo
	if(m == 'clicked: eliminar'){
		EliminarEntidad();
	}
}