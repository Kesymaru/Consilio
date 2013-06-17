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

    /**
     * REINICIA EL PANEL DE LAS CATEGORIAS/NORMAS/ARTICULOS
     */
    init: function(){
        console.log('init');
        var clase = this;

        var queryParams = {"func" : "Panel"};

        $.ajax({
            data: queryParams,
            type: "POST",
            url: "src/ajaxProyectos.php",
            success: function( response ){

                $("#menu2").html( response );

                //eventos de las tabs
                clase.Tabs();
            }
        });

    },

    /**
     * INICIALIZA LOS EVENTOS DE LAS TABS
     * @constructor
     */
    Tabs: function(){
        var clase = this;

        var queryParams = {"func" : "SuperTab", "proyecto" : clase.proyecto };

        $.ajax({
            data: queryParams,
            type: "POST",
            url: "src/ajaxProyectos.php",
            success: function( response ){
                //$("#tabs").html(response).show(1500);

                //clase.TabsEvents();
            }
        });
    },

    TabsEvents: function(){
        var clase = this;

        //CATEGORIAS
        $("#tab-categorias").off("click");
        $("#tab-categorias").on("click", function(){
            $("#tabs li").removeClass('selected');
            $(this).addClass('selected');

            LimpiarContent();
            Proyecto( clase.proyecto );
        });

        //PERMISOS
        $("#tab-permisos").off("click");
        $("#tab-permisos").on("click", function(){
            $("#tabs li").removeClass('selected');
            $(this).addClass('selected');

            //inicializa la clase de permisos
            if(typeof $Permisos == 'undefined'){
                console.log( 'inicializando clase permisos');
                $Permisos = new Permisos();
            }
            $Permisos.init(clase.proyecto);
        });

        $("#tab-home").off("click");
        $("#tab-home").on("click", function(){
            location.reload();
        });
    },

	/* 
	*  carga las supercategorias de un proyecto
	*  @param int proyecto -> id del proyecto
	*/
	SuperCategorias: function(proyecto){
        this.init();

		this.margin = 0;
		this.proyecto = proyecto;
		this.subpanel = 'categorias';

		$shortcuts.focus = 'panel';
		var clase = this;

		var queryParams = {"func" : "CategoriasRoot", "proyecto" : proyecto};

		$.ajax({
			data: queryParams,
			type: "POST",
			url: "src/ajaxProyectos.php",
			beforesend: function(){
			},
			success: function(response){
				
				if(response.length > 3){
					$("#menu")
						.html(response)
						.mCustomScrollbar({
							scrollButtons:{
								enable:true
							},
							theme: "dark-thick"
						});

					/*$(".panel #panel td div").mCustomScrollbar({
						scrollButtons:{
							enable:true
						},
						theme: "dark-thick"
					});*/
					
					$("#menu li").click(function(){

						$("#menu li").removeClass('root-selected');
						$(this).addClass('root-selected');

						if( !$("#menu2").is(":visible") ){
							$animations.PanelMenus();
						}

						clase.Categorias( $(this).attr('id') );
					});

				}else{
					notificaError("Error: lista.js SuperCategorias.<br/>"+response);
				}

			},
			fail: function(response){
				notificaError("Error: "+response);
			},
			error: function(response){
				notificaError( 'Error lista.js SuperCategorias: '+response);
			}
		});
	},

	/**
	* REINICIA EL PANEL
	*/
	ResetPanel: function(){
		if( $("#menu2").length > 0 ){
			//resete el panel
			$("#panel-categorias, #panel-normas, #panel-articulos, #td-categorias, #td-normas, #td-articulos")
				.removeClass("panel-activo")
				.html('');

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
			.hide()
			.html('Articulos')
			.fadeIn();

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
		var clase = this;
		console.log( articulo );

		this.articulo = articulo;

		var queryParams = {"func" : "DatosArticulo", "proyecto" : this.proyecto, "categoria" : this.categoria, "norma" : this.norma, "id" : this.articulo };
		$.ajax({
			cache: false,
			type: "post",
			data: queryParams,
			url: "src/ajaxProyectos.php",
			success: function(response){
				console.log( response );
				console.log( response.length );

				if( response.length > 3){
					
					$("#content").html(response);
					
					if( $("#comentario").length ){
						Editor('comentario');
					}else{
						console.log('comentario no existe');
					}

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
	},

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
					clase.Grid();

					$shortcuts.lock = 'false';
		    	}
		    });
	},

	/**
	* CARGA EL SCROLL DE UN ELEMENTO
	*/
	Scroll: function( id ){
		var element = $("#"+id);

		if( element.prop('scrollHeight') > element.height() ){
			element.css("overflow","hidden");

			element.mCustomScrollbar({
				scrollButtons:{
					enable:true
				},
				theme: "dark-thick"
			});
		}
		
	},

	/**
	* CARGA EL SCROLL DEL PANEL DE DATOS
	*/
	CargarScroll: function(){
		$('.dato').each(function(){

			var element = $( "#"+ $(this).attr('id') );

			//console.log( element.attr('id')+' '+element.prop('scrollHeight') );
							
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

		//pone screoll para adjuntos
		if( $("#box-adjuntos .dato").is(':visible') ){
			$("#box-adjuntos .dato").mCustomScrollbar({
				scrollButtons:{
					enable:true
				},
				theme: "dark-thick"
			});
		}
	},

	/**
	* CREA GRID
	*/
	Grid: function(){

		var sw =  $('#content .datos').width();

		var estado = this.EstadoGrid();
		var div = 0;
		var adjunto = 0;
		//console.log( estado );

		//tiene permisos, entidades y sanciones
		if( estado[3] == "P" && estado[4] == "E" && estado[5] == "S" ){
			div = 3;
		}else if( estado[3] == "P" && estado[4] == "E" || estado[3] == "P" && estado[5] == "S" || estado[4] == "E" && estado[5] == "S" ){
			div = 2;
		}else if( estado[3] == "P" || estado[4] == "E" || estado[5] == "S" ){
			div = 1;
		}
		if( estado[6] == "D" ){
			adjunto = 1;
		}
		console.log( adjunto );
		if( div == 1 && adjunto == 1 ){
			adjunto = 2;
			div = 2;
		}
		//console.log( 'div' + div);

		if( 1500 <= sw ){
			var sb = sw-6;
			
			if( div == 1 || div == 0){
				var mb = sw-6;
			}else if( div != 3){
				var mb = (sw-30)/div;
			}else if( div == 3 && adjunto != 1){
				var mb = (sw-30)/div;
			}else if( div == 3 && adjunto == 1){
				var mb = (sw-30)/4;
				var ab = mb;
			}

			if( adjunto == 1 && div != 3 || adjunto == 1 && div == 0){
				var ab = sw-6;
			}else if( adjunto == 2){
				var ab = (sw-30)/div;
			}

		}else if( 900 <= sw ){
			var sb = sw-1;

			if( div == 1 || div == 0){
				var mb = sw-6;
			}else if( div != 3){
				var mb = (sw-30)/div;
			}else if( div == 3 && adjunto != 1){
				var mb = (sw-30)/div;
			}else if( div == 3 && adjunto == 1){
				var mb = (sw-30)/2;
				var ab = mb;
			}
			
			if( adjunto == 1 && div != 3 || adjunto == 1 && div == 0){
				var ab = sw-6;
			}else if( adjunto == 2){
				var ab = (sw-30)/div;
			}

		}else if( 700 <= sw ) {
			var sb = sw-1;

			if( div == 1 || div == 0){
				var mb = sw-6;
			}else if( div != 3){
				var mb = (sw-30)/div;
			}else if( div == 3 && adjunto != 1){
				var mb = (sw-30)/2;
			}else if( div == 3 && adjunto == 1){
				var mb = (sw-30)/2;
				var ab = mb;
			}
			
			if( adjunto == 1 && div != 3 || adjunto == 1 && div == 0){
				var ab = sw-6;
			}else if( adjunto == 2){
				var ab = (sw-30)/div;
			}

		}else{
			var sb = sw-6;
			var mb = sw-6;
			var ab = sw-6;
		}

		//console.log( sb, mb, ab );

		$('.datos div').each(function() {
			if( $(this).hasClass('SuperBox') ){
				$(this).width(sb);
			}
			if( $(this).hasClass('MiniBox') ){
				$(this).width(mb);
			}
			if( $(this).hasClass('AdjuntoBox') ){
				$(this).width(ab);
			}
		});

		$('#content .datos').freetile({
			animate: false,
			elementDelay: 0,
		});

	},

	/**
	* COMPONE EL ESTADO DE BOXES AGREGADOS
	* @return array estado
	*/
	EstadoGrid: function(){
		var estado = [];
		if( $("#box-resumen").length > 0){
			estado[0] = 'R'
		}else{
			estado[0] = 'X'
		}
		if( $("#box-observacion").length > 0){
			estado[1] = 'O'
		}else{
			estado[1] = 'X'
		}
		if( $("#box-articulo").length > 0){
			estado[2] = 'A'
		}else{
			estado[2] = 'X'
		}
		if( $("#box-permisos").length > 0){
			estado[3] = 'P'
		}else{
			estado[3] = 'X'
		}
		if( $("#box-entidad").length > 0){
			estado[4] = 'E'
		}else{
			estado[4] = 'X'
		}
		if( $("#box-sanciones").length > 0){
			estado[5] = 'S'
		}else{
			estado[5] = 'X'
		}
		if( $("#box-adjuntos").length > 0){
			estado[6] = 'D'
		}else{
			estado[6] = 'X'
		}

		return estado;
	},

	ResizeGrid: function(){
		if( $("#content").is(':visible') ){
			this.Grid();
		}
		return;
	},
});

$animations = new Animations();
