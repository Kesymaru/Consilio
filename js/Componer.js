/**
 * JAVASCRIPT PARA VISTA DE COMPOSICION
 */

/**
 * COMPONER UN PROYECTO
 * @param id -> id del articulo ha componer
 */
function Componer(id){

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
				
				//actualiza la vista del prpoyecto
				ComponerProyecto(id);
				$("#content").hide();
				$("#content").fadeIn();

			}else{
				notificaError("Error: "+response);
			}
		},
		fail: function(){
			//notificaError("Error: Componer.js FormularioComponerCategorias() AJAX fail");
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
	if(m == 'clicked: excluir'){
		notifica("la categoria seleccionada es "+id);
		//ComponerExcluir();
	}else if(m == 'clicked: incluir'){
		GuardarCategorias();
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

/**
 * SELECCIONA UNA CATEGORIA 
 * @param $id -> id de la seleccion
 */
function SelectCategoriaIncluida(id){
	$("#categorias-incluidas td").removeClass("seleccionada");
	$("#in"+id).addClass("seleccionada");
}