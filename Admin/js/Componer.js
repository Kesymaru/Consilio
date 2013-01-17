/**
 * JAVASCRIPT PARA VISTA DE COMPOSICION
 */

/**
 * COMPONER UN PROYECTO
 * @param id -> id del articulo ha componer
 */
function Componer(id){
	$.contextMenu( 'destroy' );
	
	if( id == "" || id == undefined){
		notificaError("Error: el proyecto para componer no es valido,<br/>Error: Componer.js Componer() id no valido.");
		return;
	}

	//muestra el menu si no esta visible
	if(!$("#menu").is(":visible")){
		ActivaMenu();
	}

	//limpia content si este tiene algo
	if($("#content").html() != ""){
		$("#content").html("");
	}

	//carga datos
	ComponerProyecto(id);
	ComponerCategorias(id);

}

/**
 * CARGA LOS DATOS REGISTRADOS DEL PROYECTO
 * @param id -> id del proyecto
 */
function ComponerProyecto(id){

	var queryParams = {"func" : "ComponerProyecto", "id" : id};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxComponer.php",
		beforeSend: function(){
		},
		success: function(response){
			$("#content").html(response);
		},
		fail: function(){
			notificaError("Error: Componer.js ComponerProyecto(), AJAX fail");
		}
	});
}

/******************************* CATEGORIAS ****************************/

/**
 * CATEGORIAS DE COMPOSICION, MUESTRA LAS CATEGORIAS SELECCIONABLES
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
			FormularioComponerCategorias();
		},
		fail: function(){
			notificaError("Error: Componer.js ComponerCategorias(), AJAX fail.");
		}
	});
}

/**
* INCIALIZA EL FORMULARIO DE LAS CATEGORIAS
*/
function FormularioComponerCategorias(){
	//validacion
	//$("#FormularioComponerCategorias").validationEngine();
	
	var options = {
		beforeSubmit: FormularioComponerCategoriasValidar,
		beforeSend: function(){
		},
	    success: function(response) { 
	    	$("#content").html(response);
			if(response.length > 3){
				notifica("Categorias Incluidas.");

				//obtiene el id del proyecto en composicion
				var id = $("#proyecto").val();
				
				//actualiza la vista del proyecto
				ComponerProyecto(id);
				$("#content").hide();
				$("#content").fadeIn();

			}else{
				notificaError("Error: "+response);
			}
		},
		fail: function(){
			notificaError("Error: Componer.js FormularioComponerCategorias() AJAX fail");
		}
	}; 
	$('#FormularioComponerCategorias').ajaxForm(options);
}

/**
* VIDA FormularioComponerCategorias\
* @return true si es valido
* @return false sino, ademas muestra notificacion
*/
function FormularioComponerCategoriasValidar(){
	var total = 0;

	//cuentas las categorias seleccionadas
	$(".seleccionada").each(function(){
		total++;
	});

	if(total == 0){
		notificaAtencion("Seleccione alguna categoria");
		return false;
	}else{
		return true;
	}
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
            var m = "clicked: " + key;
            //window.console && console.log(m) || alert(m); 
            MenuComponer(m, id);
        },
        items: {
        	//"excluir": {name: "Excluir", icon: "delete"},
        	"incluir": {name: "Incluir Selecciones", icon: "add", accesskey: "i"}
            //"editar": {name: "Editar", icon: "edit"},
            //"eliminar": {name: "Eliminar", icon: "delete"},
        }
    });
}

/**
 * MENEJA FUNCIONES DEL MENU DE COMPOSICION
 * @param m -> evento del click a seleccionar opcion
 * @param id -> id de la categoria
 */
function MenuComponer(m, id){
	//esxcluir selecciones
	if(m == 'clicked: excluir'){
		ExcluirCategorias();
	}else if(m == 'clicked: incluir'){
		GuardarCategorias();
	}else if(m == 'clicked: normas'){
		//vista de normas
		PreviewCategoriaNormas(id);
	}
	else if(m == 'clicked: observacion'){
		Observacion(id);
	}
	else if(m == 'clicked: camino'){
		CategoriaPath(id);
	}
}

/**
* MUSTRA PATH DE UNA CATEGORIA SELECCIONADA
* @param id -> id de la categoria incluida
*/
function CategoriaPath(id){
	notifica(id);
	if($("#in"+id+" .path").is(":visible")){
		//efecto progresivo
		$("#in"+id+" .path").fadeOut();
	}else{
		//efecto progresivo
		$("#in"+id+" .path").fadeIn();
	}
}

/**
* ENVIA FORMULARIO DE COMPONER CATEGORIAS PARA GUARDARLAS
*/
function GuardarCategorias(){
	$('#FormularioComponerCategorias').submit();
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

/**************** CATEGORIAS INCLUIDAS *********************/

/**
 * SELECCIONA UNA CATEGORIA 
 * @param $id -> id de la seleccion
 */
function SelectCategoriaIncluida(id){

	if($("#in"+id).hasClass('seleccionada')){
		$("#in"+id).removeClass("seleccionada");
	}else{
		$("#in"+id).addClass("seleccionada");
	}

	MenuCategoriaIncluida(id);
}

/**
* PONE CONTEXT MENU DE UNA CATEGORIA SELECCIONADA
*/
function MenuCategoriaIncluida(id){

	$.contextMenu({
        selector: '#in'+id, 
        callback: function(key, options) {
            var m = "clicked: " + key;
            //window.console && console.log(m) || alert(m); 
            MenuComponer(m, id);
        },
        items: {
        	//"excluir": {name: "Excluir", icon: "delete"},
        	"excluir": {name: "Excluir Selecciones", icon: "delete", accesskey: "x"},
            "normas": {name: "Seleccionar Normas", icon: "edit", accesskey: "s"},
            "observacion": {name: "Observacion", icon: "edit"},
            "camino": {name: "Ver camino", icon: "edit", accesskey: "c"},
        }
    });
}

/**
* EXCLUYE LAS CATEGORIAS SELECCIONADAS DE LAS INCLUIDAS
*/
function ExcluirCategorias(){
	var excluidas = [];
	
	$("#categorias-incluidas .seleccionada").each(function(){
		//notifica(this.id.substring(2) );
		excluidas.push( this.id.substring(2) );
	});
	
	var proyecto = $("#proyecto").val();

	var queryParams = {"func" : "ExcluirCategorias", "proyecto" : proyecto, "categorias[]" : excluidas};

	$.ajax({
		data: queryParams,
		async: false,
		type: "post",
		url: "src/ajaxComponer.php",
		beforeSend: function(){
		},
		success: function(response){

			if(response.length <= 3){

				//actualiza la vista del proyecto
				ComponerProyecto(proyecto);
				$("#content").hide();
				$("#content").fadeIn();

			}else{
				notificaError(response);
			}

		},
		fail: function(response){
			notificaError("Error: Componer.js ExcluirCategorias() AJAX fail.<hr>"+response);
		}
	});
}

/*************************** PREVISUALIZACIONES  ********************/

/**
* MUSTRA EL PREVIEW DE LAS NORMAS DE UNA CATEGORIA
* @param id -> id de la categorias
*/
function PreviewCategoriaNormas(id){
	var proyecto = $("#proyecto").val();
	$.fancybox({
		'href'         : 'src/previewNormas.php?categoria='+id+'&proyecto='+proyecto,
		'width'        : '50%',
		'height'       : '500',
		'autoScale'    : false,
		'transitionIn' : 'fade',
		'transitionOut': 'elastic',
		'type'         : 'iframe',
		'title'        : 'Normas'
   });
}

/**
* MUSTRA LAS OBSERVACIONES DE LA CATEGORIA INCLUIDA
* @param id -> id de la categorias
*/
function Observacion(id){
	var proyecto = $("#proyecto").val();

	$.fancybox({
		'href'         : 'src/observaciones.php?categoria='+id+'&proyecto='+proyecto,
		'width'        : '70%',
		'height'       : '500',
		'autoScale'    : false,
		'transitionIn' : 'fade',
		'transitionOut': 'elastic',
		'type'         : 'iframe',
		'title'        : 'Observaciones'
   });
}