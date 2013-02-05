/**
* JAVASCRIPT PARA COMENTARIOS
*/ 

/**
 * MUESTRA LA VISTA DE LOS COMENTARIOS PRO PROYECTO
 */
function Comentarios(){

	$.cookie('vista', 'proyectos');

	$.contextMenu( 'destroy' );

	if($("#menu2").is(":visible")){
		Menu2();
	}

	if($("#menu").is(":visible")){
		ActivaMenu();
	}
	
	var queryParams = {"func" : "Comentarios"};

	$.ajax({
		data: queryParams,
		type: 'post',
		url : 'src/ajaxComentarios.php',
		beforeSend: function(){
			Loading();
		},
		success: function(response){
			LoadingClose();

			if( response.length > 0){
				$("#content").html(response);
				
				$("#comentarios tr").each(function(){
					
					$(this).click(function(){

						$("#comentarios tr").removeClass('seleccionada');
						$(this).addClass('seleccionada');

					});

					$(this).dblclick(function(){
						
						if( $.cookie('cargando') == "false"){
							$.cookie('cargando', true);
							Comentario( $(this).attr('id') );
						}
						
					});
				});

			}else{
				notificaError("Error: Comentarios.js Comentarios().<br/>"+response);
			}
		},
		fail: function(response){
			LoadingClose();
			notificaError("Error: AJAX FAIL Comentarios.js Comentarios().<br/>"+response);
		}
	});
}

/**
 * CARGA LOS COMENTARIOS DE UN PROYECTO
 * @param id -> id del proyecto
 */
function Comentario(id){
	if( !$("#menu").is(":visible")){
		ActivaMenu();
	}

	if( $("#content").length ){
		LimpiarContent();
	}

	var queryParams = {"func" : "Comentario", "proyecto" : id};

	$.ajax({
		data: queryParams,
		type: 'post',
		url: "src/ajaxComentarios.php",
		success: function(response){
			if(response.length > 0){
				$("#menu").html(response);

				$("#comentarios-articulos li").each(function(){
					
					$(this).click(function(){
						$('#comentarios-articulos li').removeClass("seleccionada");
						$(this).addClass('seleccionada');
					});

					$(this).dblclick(function(){
						if( $.cookie('cargando') == "false"){
							$.cookie('cargando', true);
							ComentariosArticulo( id, $(this).attr('id') );
						}
					});
				});

			}else{
				notificaError("ERROR: Comentarios.js Comentario().<br/>"+response);
			}
		},
		fail: function(response){
			notificaError("ERROR: AJAX fail Comentarios.js Comentario().<br/>"+response);
		}
	});

	$.cookie('cargando', false);
}

/**
 * CARGA LOS COMENTARIO DE UN ARTICULO
 * @param proyecto -> id del proyecto
 * @param articulo -> id del articulo
 */
function ComentariosArticulo( proyecto, articulo ){
	var queryParams = {"func" : "ComentariosArticulo", "proyecto" : proyecto, "articulo" : articulo};

	notifica(proyecto + ' ' + articulo );

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxComentarios.php",
		success: function(response){
			if(response.length > 3){
				$('#content').html(response);
			}else{
				notificaError("Error: Comentarios.js ComentariosArticulo().<br/>"+response);
			}
		},
		fail: function(response){
			notificaError("ERROR: Comentarios.js ComentariosArticulo().<b/>"+response);
		}
	});

	$.cookie('cargando', false);
}

