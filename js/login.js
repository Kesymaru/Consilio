$(document).ready(function(){
    	
    $( "input[type=submit], button" ).button();

    $('#registroUsuarios').hide();
    $('#formRecuperacion').hide();
    $('#resetear').hide();
    $('.etiquetas').hide();

    $("#formID").validationEngine();

    //logIn
    $('#formID').submit(function() {
		return false;
	});

	//revisa si la compu no esta bloqueada
	EstadoBloqueado();
});

function loginbox(cambio){

	if(cambio == 2){
		$('#registroUsuarios').fadeOut(1000,function(){$('#usuarios').fadeIn();});
	}else{
		$('#usuarios').fadeOut(1000,function(){$('#registroUsuarios').fadeIn();});
	}

}

function formRecuperacion(){

	if( $('#formRecuperacion').is(':visible')){
			$('#formRecuperacion').slideUp(500);
			$('#login').slideDown(500);

			$('#resetear').fadeOut(500, function(){ $('#entrar').fadeIn(500); });
	}else{
			$('#formRecuperacion').slideDown(500);
			$('#login').slideUp(500,function(){ $('#login').hide(); });

			if( $('#entrar').length ){
				$('#entrar').fadeOut(500, function(){ $('#resetear').fadeIn(500); });
			}else{
				$('#resetear').fadeIn(500);
			}
			
	}

}

//loguear
function logIn(){

	//si son validos los datos
	if ( $('#formID').validationEngine('validate') ){
		var usuario = $('#usuario').val();
		var password = $('#password').val();

		var queryParams = { "func" : 'LogIn', "usuario" : usuario, "password" : password};
			$.ajax({
			data:  queryParams,
			url:   'src/ajaxUsuarios.php',
			type:  'post',
			success:  function (response) { 
				console.log( response );

				$('#notifica').append(response);

				/*if(response.length <= 3){
					top.location.href = 'index.php';
				}else{
				    notificaIntento(response);
				}*/
			},
			fail: function(response){
				notificaError("Error: AJAX fail login.js logIN().<br/>"+response);
			}
		});
	}else{
		notificaIntento('Datos no validos.')
	}
}

//resetea password
function resetar(){

	var usuario = $('#usuarioRecuperacion').val();
	var email = $('#emailRecuperacion').val();
	var reseteado = false;

	if(usuario != ''){
			var queryParams = { "func" : 'resetPasswordUsuario', "usuario" : usuario};
			$.ajax({
				data:  queryParams,
				async: false,
				url:   'src/ajaxUsuarios.php',
				type:  'post',
				success:  function (response) { 
					console.log( response );

					if(response.length > 0){
						alert(response);
						console.log(response);
						reseteado = true;
						return;
					}
				}
			});
	}

	if(email != '' && !reseteado){
			
		var queryParams = { "func" : 'resetPasswordEmail', "email" : email};
		$.ajax({
			data:  queryParams,
			async: false,
			url:   'src/ajaxUsuarios.php',
			type:  'post',
			success:  function (response) { 
				
				if(response.length > 0){
					$("html").append(response);
					console.log(response);
					reseteado = true;
					return;
				}
			}
		});
	}

	//muestra errores
	if(usuario != '' && email != '' && !reseteado ){
		notificaError('Error usuario y email no registrados.');
	}else if(usuario != '' && !reseteado ){
		notificaError('Error usuario no registrado.');
	}else if(email != '' && !reseteado ){
		notificaError('Error email no registrado.');
	}

}

/*
	REGISTRO
*/
function registro(){
	//si los datos son validos
	if( $('#formID').validationEngine('validate') ){

		//ya estan validadas
		var usuario = $('#registroUsuario').val();
		var email = $('#registroEmail').val();
		var password = $('#registroPassword1').val();
		
		//AJAX
		var queryParams = { "func" : 'registro', "usuario" : usuario, "email" : email, "password" : password};
		$.ajax({
			data:  queryParams,
			url:   'src/ajaxPermisos.php',
			type:  'post',
			success:  function (response) { 

				if(response.length == 0){

				    setTimeout(function() {
  						window.location.href = "login.php?usuario="+usuario+"&reset=2";
					}, 4000);

					notificaAtencion('Se ha registrado exitosamente.<br/>Ya pudes entrar a Matricez.');
				}else{
				    notificaError(response);
				}
				        
			},
			fail: function(response){
				notificaError("Error: AJAX fail login.js registro().<br/>"+response);
			}
		});
	}else{
		notificaIntento('Error datos invalidos.')
	}
}

/*
	NOTIFICACIONES
*/

//usa noty (jquery plugin) para notificar 
function notifica(text) {
	var n = noty({
	  	text: text,
	  	type: 'alert',
	    dismissQueue: true,
	  	layout: 'topCenter',
	  	closeWith: ['button'], // ['click', 'button', 'hover']
	});
	console.log('html: '+n.options.id);
	  	
	//tiempo para desaparecerlo solo 
	setTimeout(function (){
		n.close();
	},5000);
}

/**
* NOTIFICA UN ERROR
*/
function notificaError(text) {

	var queryParams = {"error" : text, "site" : "Matriz"};
	$.ajax({
		data: queryParams,
		async: false,
		type: "post",
		url: "Admin/src/class/error.php",
		success: function(response){
			text += "<br/>Notifcado al webmaster.";
		}
	});
	
  	var n = noty({
  		text: text,
  		type: 'error',
    	dismissQueue: true,
  		layout: 'topCenter',
  		closeWith: ['button'],
  	});
  	
  	//tiempo para desaparecerlo solo 
  	setTimeout(function (){
		n.close();
	},7000);
}

//notificaciones de maxima priridad
function notificaAtencion(text) {
  	var n = noty({
  		text: text,
  		type: 'information',
    	dismissQueue: true,
  		layout: 'topCenter',
  		closeWith: ['button'], // ['click', 'button', 'hover']
  	});
  	
  	//tiempo para desaparecerlo solo 
  	setTimeout(function (){
		n.close();
	},10000);
}

//NOTIFICACION PARA INTENTO FALLIDO
function notificaIntento(text) {
  	var n = noty({
  		text: text,
  		type: 'error',
    	dismissQueue: true,
  		layout: 'topCenter',
  		closeWith: ['button'], // ['click', 'button', 'hover']
  	});
  	
  	//tiempo para desaparecerlo solo 
  	setTimeout(function (){
		n.close();
	},7000);
}

/***************************************** BLOQUEO *******************************************/
/**
* BLOQUEA USUARIO
*/
function Bloqueado(text){
	
	$("#articulos").animate({
		opacity: .5,
	}, { 
		duration: 500, 
		queue: false,
		complete: function(){

			$("#articulos").animate({
				opacity: 1,
			}, { 
				duration: 500, 
				queue: false
			});

		}
	});

	$('#usuarios .titulo').html("Bloqueado");
	$('#usuarios').addClass('bloqueado');

	/*$("#usuarios .controls").fadeOut(700, function(){
		$("#usuarios .controls").remove();
	});*/
	$("#password, #usuario, #entrar").fadeOut(function(){
		$(this).remove();
	});
	
	/*$('form').onSubmit(function(){
		notifica("Buen intento");
		return false;
	});*/

	$("#login").html(text);
}

/**
* SE ENCARGA DE REVISAR ES ESTADO DE LA COMPU
*/
function EstadoBloqueado(){
	var queryParams = {"func":"EstadoBloqueado"};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxUsuarios.php",
		success: function(response){
			//response = response.replace(/(\r\n|\n|\r)/gm,""); 
			//response = response.replace(/\s+/g,"");

			//$('html').append(response);
			console.log( response );

			if( response.length > 3 ){
				notificaIntento("Lo sentimos tu ip esta bloqueada por que excediste el numero de intentos.");
				Bloqueado( response );
			}
		}
	});
}
