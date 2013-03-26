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

						if( !$("#ComentariosEliminar").is(":visible") ){
							$("#ComentariosEliminar").fadeIn();
						}

						if( !$("#ComentariosVer").is(":visible") ){
							$("#ComentariosVer").fadeIn();
						}

					});

					$(this).dblclick(function(){
						
						if( $.cookie('cargando') == "false"){
							$.cookie('cargando', true);
							//Comentario( $(this).attr('id') );
							Comentario();
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
function Comentario( ){

	var id = $("#comentarios tr.seleccionada").attr('id');
	console.log( id );

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

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxComentarios.php",
		success: function(response){
			if(response.length > 3){
				$('#content')
					.addClass('content-no')
					.html(response);
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

/**
* CONFIRMACION DE ELIMINAR TODOS LOS COMENTARIOS DE UN PROYECTO
* @poram int id -> id del proyecto
*/
function EliminarComentariosProyecto( ){
	var id = $("#comentarios tr.seleccionada").attr('id');

	var si = function (){
		AccionEliminarComentariosProyecto( id );
	}

	var no = function (){
		notificaAtencion("Operacion cancelada");
	}

	Confirmacion("Desea Eliminar los comentarios del proyecto", si, no);
}


/**
* CONFIRMACION DE ELIMINAR COMENTARIO
* @poram int id -> id del comentario
*/
function EliminarComentario( id ){
	var si = function (){
		AccionEliminarComentario( id );
	}

	var no = function (){
		notificaAtencion("Operacion cancelada");
	}

	Confirmacion("Desea Eliminar el comentario", si, no);
}

/**
* ELIMINA UN COMENTARIO
* @param string id -> id del comentario a eliminar
*/
function AccionEliminarComentario( id ){

	if( id !== undefined && id !== '' ){

		var queryParams = { "func" : "EliminarComentario", 'id' : id };

		$.ajax({
			data: queryParams,
			url: 'src/ajaxComentarios.php',
			type: "post",
			success: function( response ){

				console.log( response );

				$('#'+id).addClass('seleccionada');

				$('#'+id+' td')
					.wrapInner('<div style="display: block;" />')
					.parent()
					.find('td > div')
					.slideUp(1000, function(){

						//$(this).parent().parent().remove();
						$('#'+id).css({
							'display':'block',
							'width' : '100%'
						}).slideUp(100, function(){
							$(this).remove();
						});

					});

			},	
			fail: function( response ){
				notificaError("Error: AJAX FAIL Comentarios.js AccionEliminarComentario().<br/>"+response);
			}
		})

			
	}else{
		notificaError("Error: Comentarios.js EliminarComentario, param id no es valido.<br/>"+id)
	}

}

