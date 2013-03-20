$(document).ready(function(){
    	
    $( "input[type=submit], button" ).button();

    $('#registroUsuarios').hide();
    $('#formRecuperacion').hide();
    $('#resetear').hide();
    $('.etiquetas').hide();

    $("#formID").validationEngine();
    $('input[placeholder]').placeholder();

    //compatibilidad opera -> es el unico browser que no permite color en placeholder
    if($.browser.opera){
    	$('.etiquetas').show();
    }

    //logIn
    $('#formID').submit(function() {
		return false;
	});

	//notificacion 
	var mensaje = 'Sitio para el Admin.<br/>Si no eres administrador<br/>ve al siguiente link:<br/><b><a href="../login.php"> Matriz </a></b>';
	notificaAtencion(mensaje);

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
			$('#login').slideUp(500,function(){ $('#login').hide();});

			$('#entrar').fadeOut(500, function(){ $('#resetear').fadeIn(500); });
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
				
				$('html').append(response);
				console.log( response );

				/*if(response.length <= 3){
					top.location.href = 'index.php';
				}else{
				    notificaIntento(response);
				    //$('html').html(response);
				}*/
			},
			fail: function( response ){
				notificaError("Error: AJAX FAIL login.js logIn.<br/>"+response);
			}
		});
	}else{
		//console.log('Datos no validos');
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
				url:   'src/ajax.php',
				type:  'post',
				success:  function (response) { 
					if(response.length > 0){
						notifica(response);
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
			url:   'src/ajax.php',
			type:  'post',
			success:  function (response) { 
				if(response.length > 0){
					notifica(response);
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

//notifica errores
function notificaError(text) {
	var queryParams = {"error" : text, "site" : "Admin"};
	$.ajax({
		data: queryParams,
		async: false,
		type: "post",
		url: "src/class/error.php",
		success: function(response){
			text += "<br/>Notifcado al webmaster.";
		}
	});

	/* no muestra el error
	var n = noty({
	  	text: text,
	  	type: 'error',
	    dismissQueue: true,
	  	layout: 'topCenter',
	  	closeWith: ['button'], // ['click', 'button', 'hover']
	});
	//console.log('html: '+n.options.id);
	  	
	//tiempo para desaparecerlo solo 
	setTimeout(function (){
		n.close();
	},7000);*/
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
	console.log('inten');

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
function Bloqueado(ip){
	
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
	var bloquedo =  'Has excedido el numero de intentos.<br/>'+
					'Tu ip : '+ip+' ha sido bloqueda.<br/>'+
					'Para poder entrar deberas esperar almenos 1 hora.<br/>'+
					'<br/><hr><br/>'+
					'Si crees que esto es un error puedes contartar a:<br/>'+
					'aalfaro@77digital.com'+
					'<br/><br/>';

	$('#usuarios .titulo').html("Bloqueado");
	$("#usuarios .controls").fadeOut(700, function(){$("#usuarios .controls").remove();});
	$("#login").html(bloquedo);
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
			response = response.replace(/(\r\n|\n|\r)/gm,""); 
			response = response.replace(/\s+/g,"");

			//$('html').append(response);
			console.log( response );

			if( response != "false" ){
				notificaIntento("Lo sentimos tu ip esta bloqueada por que excediste el numero de intentos.");
				Bloqueado( response );
			}
		}
	})
}
