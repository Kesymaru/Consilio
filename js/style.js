/*
	ARREGLA LA MAQUETACION EN IE Y OPERA
	ademas le da estilo personalizado al scrollbar del menu
*/

$(window).load(function() {
	if($.browser.opera || $.browser.msie ){
    	var body = $('body').height();
    	$('.menu').css('height', (body*0.8));
    	$('.content').css({
	    		'height' : (body*0.8),
	    		'overflow' : "auto"
	    	});
    	$('.super').css('height', (body*0.1));
    }

    $(window).resize(function() {

		if($.browser.opera || $.browser.msie ){
			var body = $('body').height();
	    	$('#menu').css('height', (body*0.8));
	    	//$('#content').css('height', (body*0.8));
	    	//$('.super').css('height', (body*0.1));
		}

	});
	
});

function ActivaMenuFixIe(){
	var body = $('body').height();
	if($.browser.opera || $.browser.msie ){
		$('.menu').css({
			'min-height'          : (body*0.8),
			'background-color': '#fff',
			'border'          : '1px solid #dedede'
		});
	}
}
