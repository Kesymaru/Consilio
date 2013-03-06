/**
 * JAVASCRIPT PARA PREVIEWS
 */


/**
* INCIALIZA EL FORMULARIO PARA LOS ARTICULOS
*/
function FormularioArticulos(){
		
	var options = {  
		beforeSend: function(){
		},
	    success: function(response) { 
		},
		fail: function(){
		}
	}; 
	$('#FormularioArticulos').ajaxForm(options);
	
}

/**
* INICIALIZA EL FORMULARIO PARA LAS NORMAS
*/
function FormularioNormas(){
		
	var options = {  
		beforeSend: function(){
		},
	    success: function(response) { 
		},
		fail: function(){
		}
	}; 
	$('#FormularioNormas').ajaxForm(options);
	
}

/**
 * CARGA LA FUNCTION DE DOBLE CLICK PARA CADA NORMA
 */
function NormaDobleClick(){
	$("#normas li").each(function(f,c){

		$("#"+this.id).click(function(){
			SelectNormas(this.id);
			return;
		});

		$("#"+this.id).dblclick(function(){
			$("#normas #"+this.id).addClass("seleccionada");
			$("#norma"+this.id).attr('checked', true);
			Articulos(this.id);
			return;
		});



	});
}


function Cambio(){

	if(!$("#articulos").is(":visible")){

		$("#articulos").css("display" , "inline-block");
		$("#articulos").css("width" , "0%");
		$("#normas").css("display" , "inline-block");
		

		$("#normas").animate({
			//opacity: 0,
			width: "0%"
		}, { 
			duration: 1500, 
			queue: false,
			complete: function(){
				$("#normas").css({
					'width' : "0",
					"display" : "none"
				});
			}
		});

    	$("#articulos").animate({
			width: "100%",
			"display" : "inline-block"
		}, { 
			duration: 1500, 
			queue: false,
			complete: function(){
				$("#articulos").css({
					'width' : "100%",
					"display" : "inline-block"
				});
			}
		});

		$("#next, #GuardarNormas").fadeOut();
		$("#preview, #GuardarArticulos").fadeIn();

	}else{
		$("#normas").css("display" , "inline-block");
		$("#normas").css("width" , "0%");
		$("#articulos").css("display" , "inline-block");
		

		$("#articulos").animate({
			//opacity: 0,
			width: "0%"
		}, { 
			duration: 1500, 
			queue: false,
			complete: function(){
				$("#articulos").css({
				'width' : "0",
				"display" : "none"
			});
			}
		});

    	$("#normas").animate({
			width: "100%",
			"display" : "inline-block"
		}, { 
			duration: 1500, 
			queue: false,
			complete: function(){
				$("#normas").css({
					'width' : "100%",
					"display" : "inline-block"
				});
			}
		});

		$("#next, #GuardarNormas").fadeIn();
		$("#preview, #GuardarArticulos").fadeOut();
	}
}


/**
* SELECCIONA TODO LOS ELEMENTOS
* @param $id -> lugar donde actuar
*/
function SelectAll(id){
	$("#"+id+" li").each(function(){
    	$(this).addClass("seleccionada");
    	$(this).find(':checkbox').attr('checked', true);
    });

    $("#Select"+id).hide();
    $("#Unselect"+id).show();
}

function UnSelectAll(id){
	$("#"+id+" li").each(function(){
    	$(this).removeClass("seleccionada");
    	$(this).find(':checkbox').attr('checked', false);
    });

    $("#Select"+id).show();
    $("#Unselect"+id).hide();
}