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
	ComponerCategorias();

}

/**
 * CATEGORIAS DE COMPOSICION, MUESTRA LAS CATEGORIAS SELECCIONABLES
 */
function ComponerCategorias(){

	var queryParams = {"func" : "Categorias"};

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
 * CARGA LOS DATOS REGISTRADOS DEL PROYECTO
 * @param id -> id del proyecto
 */
function ComponerProyecto(id){

	var queryParams = {"func" : "Categorias", "id" : id};

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