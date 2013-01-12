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
	var mensaje = 'Sitio para el Admin.<br/>Si no eres administrador ve al siguiente link:<br/><b><a href="../login.php">Matriz</a></b>';
	notificaAtencion(mensaje);
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
	console.log('llamada LogIn');

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
				
				if(response.length <= 3){
					top.location.href = 'index.php';
				}else{
				    notificaError(response);
				}
			}
		});
	}else{
		console.log('Datos no validos');
		notificaError('Datos no validos.')
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
			url:   'src/ajax.php',
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
				        
			}
		});
	}else{
		notificaError('Error datos invalidos.')
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

