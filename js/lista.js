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

	/* 
	*  carga las supercategorias de un proyecto
	*  @param int proyecto -> id del proyecto
	*/
	SuperCategorias: function(proyecto){
		this.margin = 0;
		this.proyecto = proyecto;

		var queryParams = {"func" : "CategoriasRoot", "proyecto" : proyecto};

		$.ajax({
			data: queryParams,
			type: "post",
			url: "src/ajaxProyectos.php",
			beforesend: function(){
			},
			success: function(response){
				
				if(response.length > 0){
					$("#menu").html(response);
					
					$("#menu li").click(function(){
						
						$("#menu li").removeClass('root-selected');
						$(this).addClass('root-selected');

						if( !$("#menu2").is(":visible") ){
							PanelMenus();
						}

						$listaCategorias.Categorias( $(this).attr('id') );
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

	/*
	* Carga las categorias de una super categoria
	* @param string padre -> id del padre
	*/
	Categorias: function(padre){

		//resete el panel
		$("#panel-categorias, #panel-normas, #td-categorias, #td-normas, #td-articulos")
			.removeClass("panel-activo")
			.html('');
		
		$("#panel-articulos")
			.removeClass("panel-activo")
		$("#panel-articulos span").html('');

		$("#td-categorias, #td-normas").removeClass('panel-border-right');
		$("#td-articulos").removeClass('panel-border-top');

		//resetea datos
		this.supercategoria = padre;
		this.margin = 0;

		var queryParams = {"func" : "Hijos", "proyecto" : this.proyecto, "padre" : padre};
		$.ajax({
			data: queryParams,
			type: "post",
			url: "src/ajaxProyectos.php",
			success: function(response){
				
				if(response.length > 0){
					$("#panel-categorias")
						.addClass("panel-activo")
						.html('Categorias')
						.hide()
						.fadeIn();

					$("#td-categorias").html('<ul id="Padre'+padre+'">'+response+'</ul>')
						.addClass('panel-border-right');
					
					//al dar clik
					$("#Padre"+padre+" li span").click(function(){
						$listaCategorias.Dropdown( $(this).attr('id') );
					});
					
					$("#Padre"+padre+' li').addClass('hijo');

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
	* MUESTRA SOB LISTAS
	* @param string padre -> id del padre ha desplejar hijos
	*/
	Dropdown: function(padre){
		console.log('drop '+padre);

		this.categoria = padre;

		var queryParams = {"func" : "TieneHijos", 'categoria' : padre};

		$.ajax({
			data: queryParams,
			type: "post",
			url: "src/ajaxProyectos.php",
			success: function(response) {
				
				var esRoot = JSON.parse(response)
				console.log(esRoot);
				
				//no tiene hijos
				if( esRoot == 'false'){
					console.log('normas');
					$listaCategorias.Normas(padre);
					return;
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
										
					$("#sub"+padre).html(response)
						.hide()
						.slideDown()
						.attr('id','Padre'+padre);

					this.margin = this.margin + 10;
						
					//al dar clik
					$("#Padre"+padre+" li span").click(function(){
						$listaCategorias.Dropdown( $(this).attr('id') );
					})

					$("#td-categorias li").removeClass('seleccionada');
					$("#"+padre).closest('li').addClass('seleccionada');

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

		$("#panel-normas")
		.addClass("panel-activo")
		.html('Normas').hide().fadeIn();

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

							$listaCategorias.Articulo( $(this).attr('id') );
						}
						
					});

					$("#td-categorias li").removeClass('seleccionada');
					$("#"+categoria).closest('li').addClass('seleccionada');
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
		this.norma = norma;

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

							$listaCategorias.Datos( $(this).attr('id') );
						}

					});

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

		var queryParams = {"func" : "DatosArticulo", "proyecto" : this.proyecto, "categoria" : this.categoria, "id" : this.articulo };
		$.ajax({
			cache: false,
			type: "post",
			data: queryParams,
			url: "src/ajaxProyectos.php",
			success: function(response){

				if(response.length > 0){
					
					$("#content").html(response);
					//$("#datos-articulo").hide()
					//$("#datos-articulo").fadeIn();
					
					//Menu2();
					
					Editor('comentario');

					PanelMenus();
				}else{
					notificaError("Error: "+response);
				}
			},
			fail: function(response){
				notificaError("Error: AJAX fail Proyectos.js DatosArticulo()<br/>"+response);
			}
		});
	}

});

$listaCategorias = new Categorias();