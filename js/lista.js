/**
* MANEJA LAS LISTA DESPLEGABLE DE CATEGORIAS
*/

Categorias = function(){};
$.extend(Categorias.prototype, {
	proyecto: '',
	supercategoria: '',
	categoria: '',
	norma: '',
	articulo: '',

	margin: 0,
	focus: '',
	subpanel: '',

	/* 
	*  carga las supercategorias de un proyecto
	*  @param int proyecto -> id del proyecto
	*/
	SuperCategorias: function(proyecto){
		this.margin = 0;
		this.proyecto = proyecto;
		this.subpanel = 'categorias';

		$shortcuts.focus = 'panel';
		var clase = this;

		var queryParams = {"func" : "CategoriasRoot", "proyecto" : proyecto};

		$.ajax({
			data: queryParams,
			type: "post",
			url: "src/ajaxProyectos.php",
			beforesend: function(){
			},
			success: function(response){
				
				if(response.length > 0){
					$("#menu")
						.html(response)
						.mCustomScrollbar({
							scrollButtons:{
								enable:true
							},
							theme: "dark-thick"
						});

					$("#menu2").mCustomScrollbar({
						scrollButtons:{
							enable:true
						},
						theme: "dark-thick"
					});
					
					$("#menu li").click(function(){
						
						$("#menu li").removeClass('root-selected');
						$(this).addClass('root-selected');

						if( !$("#menu2").is(":visible") ){
							$animations.PanelMenus();
						}

						clase.Categorias( $(this).attr('id') );
					});

				}else{
					notificaError("Error: "+response);
				}

			},
			fail: function(response){
				notificaError("Error: "+response);
			}
		});
	},

	/**
	* REINICIA EL PANEL
	*/
	ResetPanel: function(){
		if( $("#menu2").length > 0 ){
			//resete el panel
			$("#panel-categorias, #panel-normas, #td-categorias, #td-normas, #td-articulos")
				.removeClass("panel-activo")
				.html('');
			
			$("#panel-articulos")
				.removeClass("panel-activo")
			$("#panel-articulos span").html('');

			$("#td-categorias, #td-normas").removeClass('panel-border-right');
			$("#td-articulos").removeClass('panel-border-top');
		}
		if( $("#content").is(":visible") ){
			$animations.PanelMenus()
		}
	},

	/*
	* Carga las categorias de una super categoria
	* @param string padre -> id del padre
	*/
	Categorias: function(padre){

		this.ResetPanel();

		//resetea datos
		this.supercategoria = padre;
		this.margin = 0;

		var clase = this;

		var queryParams = {"func" : "Hijos", "proyecto" : this.proyecto, "padre" : padre};
		$.ajax({
			data: queryParams,
			type: "post",
			url: "src/ajaxProyectos.php",
			success: function(response){
				console.log( response.length );

				if( 3 < response.length ){
					$("#panel-categorias")
						.addClass("panel-activo")
						.html('Categorias')
						.hide()
						.fadeIn();

					$("#td-categorias").html('<ul id="Padre'+padre+'">'+response+'</ul>')
						.addClass('panel-border-right');
					
					//al dar clik
					$("#Padre"+padre+" li span").click(function(){
						$(".hijo ul").slideUp();
						$listaCategorias.Dropdown( $(this).attr('id') );
					});
					
					$("#Padre"+padre+' li').addClass('hijo');
															
					$("#menu2").mCustomScrollbar("update");

				}else if( response.length == 3 ){
					var nodata = '<div class="nodata">No hay categorias</div>';

					$("#panel-categorias")
						.addClass("panel-activo")
						.html('Categorias')
						.hide()
						.fadeIn();

					$("#td-categorias")
						.html(nodata)
						

				}else if( response.length < 3 ){
					notificaError("Error: "+response);
				}

			},
			fail: function(response){
				notificaError("Error: "+response);
			}
		});
	},

	/**
	* MUESTRA SOB LISTAS
	* @param string padre -> id del padre ha desplejar hijos
	*/
	Dropdown: function(padre){
		//console.log('drop '+padre);

		this.categoria = padre;

		var clase = this;

		var queryParams = {"func" : "TieneHijos", 'categoria' : padre};

		$.ajax({
			data: queryParams,
			type: "post",
			url: "src/ajaxProyectos.php",
			success: function(response) {
				
				var esRoot = JSON.parse(response)
				//console.log(esRoot);
				
				//no tiene hijos
				if( esRoot == 'false'){
					$("#td-articulos ul").fadeOut(function(){
						$(this).remove();
					});
					clase.Normas(padre);
					
					return;
				}else{
					console.log('limpia');
					$("#td-normas ul, #td-articulos ul").fadeOut(function(){
						$(this).remove();
					});
				}
			},
			fail: function(response){
				notificaError("Error: lista.js Categorias.Dropdown"+response);
			}
		});
		//console.log('procesando drop');

		var queryParams = {"func" : "Hijos", "proyecto" : this.proyecto, "padre" : padre};
		$.ajax({
			data: queryParams,
			type: "post",
			url: "src/ajaxProyectos.php",
			success: function(response){
					
				if(response.length > 0){

					if( $("#Padre"+padre).is(":visible") ){ //esconde
						$("#Padre"+padre).slideUp();

						$("#menu2").mCustomScrollbar("update");

					}else if( $("#Padre"+padre).length <= 0 ){ //no se habia cargado, entonces carga
 
						$("#sub"+padre).html(response)
							.hide()
							.slideDown()
							.attr('id','Padre'+padre);

						//al dar clik
						$("#Padre"+padre+" li span").click(function(){
							clase.Dropdown( $(this).attr('id') );
						});
						
					}else{ //ya estaba cargado, muestra
						$("#Padre"+padre).slideDown();
					}

					$("#td-categorias li").removeClass('seleccionada');
					$("#"+padre).closest('li').addClass('seleccionada');

					$("#menu2").mCustomScrollbar("update");
				}else{
					notificaError("Error: "+response);
				}

			},
			fail: function(response){
				notificaError("Error: "+response);
			}
		});
	},

	/*
	* CARGA LA UNA NORMA
	*/
	Normas: function(categoria){
		this.subpanel = 'normas';

		$("#panel-normas")
		.addClass("panel-activo")
		.html('Normas').hide().fadeIn();

		var clase = this;

		var queryParams = {"func" : "Normas", "id" : categoria, "proyecto" : this.proyecto};

		$.ajax({
			data: queryParams,
			type: "post",
			url: "src/ajaxProyectos.php",
			beforesend: function(){
				$("#td-normas ul").hide();
				console.log('cargando');
			},
			success: function(response){
				if(response.length > 0){

					$("#td-normas")
						.html('<ul id="Normas'+categoria+'">'+response+'</ul>')
						.addClass('panel-border-right')
						.fadeIn();

					$("#Normas"+categoria+' li').click(function(){
						//id valido
						if( $(this).attr('id') != undefined && $(this).attr('id') != '' ){
							$("#td-normas li")
								.removeClass('seleccionada');
							$(this)
								.addClass('seleccionada');

							clase.Articulo( $(this).attr('id') );
						}
						
					});

					$("#td-categorias li").removeClass('seleccionada');
					$("#"+categoria).closest('li').addClass('seleccionada');

					$("#menu2").mCustomScrollbar("update");
				}else{
					notificaError("Error: "+response);
				}
			},
			fail: function(response){
				notificaError("Error: AJAX fail Proyectos.js Normas()<br/>"+response);
			}
		});
	},

	/**
	* CARGA LOS ARTICULOS DE UNA CATEGORIA
	*/
	Articulo: function(norma){
		this.subpanel = 'articulos';
		this.norma = norma;

		var clase = this;

		$("#panel-articulos")
			.addClass("panel-activo")
			.hide();
		$("#panel-articulos span").html('Articulos');
		$("#panel-articulos").fadeIn();

		$("#td-articulos").removeClass('panel-border-top');

		var queryParams = {"func" : "Articulos", "id" : this.norma, "categoria" : this.categoria, "proyecto" : this.proyecto};
		$.ajax({
			data: queryParams,
			type: "post",
			url: "src/ajaxProyectos.php",
			beforesend: function(){
			},
			success: function(response){
				if(response.length > 0){
					$("#td-articulos").html(response);

					$("#Articulos"+norma+' li').click(function(){
						//valida id
						if( $(this).attr('id') != undefined && $(this).attr('id') != '' ){
							$("#td-articulos li")
								.removeClass('seleccionada');
							$(this)
								.addClass('seleccionada');

							clase.Datos( $(this).attr('id') );
						}

					});

					$("#menu2").mCustomScrollbar("update");

				}else{
					notificaError("Error: "+response);
				}
			},
			fail: function(response){
				notificaError("Error: AJAX fail Proyectos.js Articulos()<br/>"+response);
			}
		});

	},

	/**
	* MUESTRA LOS DATOS DE UN ARTICULO SELECCIONADO
	* @param int articulo -> id del articulo ha mostrar
	*/
	Datos: function(articulo){
		console.log( articulo );

		this.articulo = articulo;

		var queryParams = {"func" : "DatosArticulo", "proyecto" : this.proyecto, "categoria" : this.categoria, "norma" : this.norma, "id" : this.articulo };
		$.ajax({
			cache: false,
			type: "post",
			data: queryParams,
			url: "src/ajaxProyectos.php",
			success: function(response){

				if(response.length > 0){
					
					$("#content").html(response);
					
					Editor('comentario');

					$shortcuts.focus = 'datos';

					//se encarga de animar y cargar el scroll
					$animations.PanelMenus();

				}else{
					notificaError("Error: "+response);
				}
			},
			fail: function(response){
				notificaError("Error: AJAX fail Proyectos.js DatosArticulo()<br/>"+response);
			}
		});
	},

	/***************** HELPERS *************/

	MostrarDatos: function(){
		if( this.articulo != undefined && this.articulo != '' ){
			$shortcuts.focus = 'datos';
			$animations.PanelMenus();
		}
	},

	OcultarDatos: function(){
		if( this.articulo != undefined && this.articulo != '' ){
			$shortcuts.focus = 'panel';
			$animations.PanelMenus();
		}
	}

});

$listaCategorias = new Categorias();

/************************************** CLASE PARA SHORT CUTS ******************/

ShortCuts = function(){};
$.extend(ShortCuts.prototype, {
	focus: '', //sona en la que aplicar eventos
	lock: 'false',

	init: function(){
		$(document).bind('keydown',this.Manejador);
	},

	Manejador: function(evento){
		console.log( evento.ctrlKey );
		
		console.log( evento.keyCode );
		if( $shortcuts.lock == 'false' ){

			switch ( $shortcuts.focus ){
				case ('panel'):
						
					switch (evento.keyCode){

						case (39):
							console.log('derecha');
							$listaCategorias.MostrarDatos();
							break;
					}
					break;

				case ('datos'):
					switch (evento.keyCode){
						case (37):
							console.log('izquierda');
							$listaCategorias.OcultarDatos();
							break;
					}
					break;
			}
		}
	}
});

$shortcuts = new ShortCuts();


/******************************* CLASE PARA ANIMACIONES **********************/ 
Animations = function(){};
$.extend(Animations.prototype, {
	
	//Muestra y oculta el panel con el menu, menu2 y el contenido
	PanelMenus: function(){
		if( $('#content').is(":visible") ){
			this.MostrarPanel();
		}else{
			this.OcultraPanel();
		}
	},

	/**
	* MUESTRA EL PANEL Y OCULTA EL CONETENIDO
	*/
	MostrarPanel: function(){
		$shortcuts.lock = 'true';

			$("#content").css({
				'margin' : '0',
				'display' : 'inline-block'
			});

			$("#datos-articulo, #datos-footer, .mis-proyectos, .titulo").fadeOut();

			if( !$("#menu").is(":visible") ){
				$("#menu").css({
					'display' : 'block',
					width : '0px',
				});

				$("#menu").animate({
					opacity: 1,
					//width: 'toggle'
					width: "10%"
				}, { 
					duration: 1500, 
					queue: false,
					complete: function(){
						$("#menu").css({
							'display' : 'block',
							'float' : 'left',
							'min-width' : '50px',
						});
					}
				});
			}

			$("#menu2").css({
				'display' : 'inline-block',
				width : '0px',
				'margin': '0px'
			});

			$("#menu2").animate({
		       width: '80%'
		    }, { 
		    	duration: 1500, 
		    	queue: false,
		    	complete: function(){
		    		$("#menu2").css({
						"display" : "inline-block",
						"opacity" : "1"
					});
					$shortcuts.lock = 'false';
		    	}
		    });

		    $("#content").animate({
		       width: '0%'
		    }, { 
		    	duration: 1400, 
		    	queue: false,
		    	complete: function(){
		    		$("#content").css({
						"display" : "none",
					});
		    	}
		    });
	},

	/**
	* OCULTA EL PANEL Y MUESTRA EL CONTENIDO
	*/
	OcultraPanel: function(){
		var clase = this;
		$shortcuts.lock = 'true';

			$("#content").css({
				'margin' : '0',
				'display' : 'inline-block'
			});
			
			$("#menu2").animate({
		       width: '0%'
		    }, { 
		    	duration: 1400, 
		    	queue: false,
		    	complete: function(){
		    		$("#menu2").css({
						"display" : "none",
					});
		    	}
		    });

		    $("#content").animate({
		       width: '80%'
		    }, { 
		    	duration: 1500, 
		    	queue: false,
		    	complete: function(){
		    		$("#content").css({
						"display" : "inline-block",
						'height' : 'auto',
					});
					$("#datos-articulo, #datos-footer, .titulo, .datos").fadeIn();
					
					clase.CargarScroll();

					$shortcuts.lock = 'false';
		    	}
		    });
	},

	/**
	* CARGA EL SCROLL DEL PANEL DE DATOS
	*/
	CargarScroll: function(){

		$('.dato').each(function(){

			var element = $( "#"+ $(this).attr('id') );

			console.log( element.prop('scrollHeight') );
							
			if( 200 <= element.prop('scrollHeight') ){

				element.mCustomScrollbar({
					scrollButtons:{
						enable:true
					},
					theme: "dark-thick"
				});

			}else{
				element.css("overflow","hidden");
			}
		});
	}
});

$animations = new Animations();
