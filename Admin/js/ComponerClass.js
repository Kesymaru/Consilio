/**
* CLASE PARA MANEJAR LA COMPOSICION DE LOS PROYECTOS
*/

ComponerClass = function(){};
$.extend(ComponerClass.prototype, {
	url: "src/ajaxComponer.php",
	proyecto: 0,

	//objecto con el path de cada categoria incluida
	incluidas: [],

	/**
	* INICIALIZA LA COMPOSICION DE UN PROYECTO
	*/
	init: function(id){
		$.contextMenu( 'destroy' );
		
		if( id == "" || id == undefined){
			notificaError("Error: el proyecto para componer no es valido,<br/>Error: Componer.js Componer() id no valido.");
			return;
		}

		this.proyecto = id;

		//muestra el menu si no esta visible
		if(!$("#menu").is(":visible")){
			ActivaMenu();
		}

		//limpia content si este tiene algo
		if($("#content").html() != ""){
			$("#content").html("");
		}

		//carga datos
		this.ComponerProyecto();
	},

	/**
	* CARGA LOS DATOS REGISTRADOS DE UN PROYECTO
	*/
	ComponerProyecto: function(){
		var queryParams = {"func" : "ComponerProyecto", "id" : this.proyecto};
		var clase = this;

		$.ajax({
			data: queryParams,
			type: "post",
			url: this.url,
			beforeSend: function(){
			},
			success: function(response){
				$("#content").html(response);
				
				//eventos
				$("#categoriasIncluidas li").live("click", function(){
					var id = $(this).attr('id');
					console.log(id);

					if( $("#"+id).hasClass('seleccionada') ){
						$("#"+id).removeClass('seleccionada');
					}else{
						$("#"+id).addClass('seleccionada');						
					}
					
					//crea el menu contextual
					clase.Menu( $(this).attr("id") );
				});

			},
			fail: function(){
				notificaError("Error: Componer.js ComponerProyecto(), AJAX fail");
			}
		});
	},

	/**
	* INCLUYE UNA NUEVA CATEGORIA
	* @param int id -> id de la categoria
	*/
	Incluir: function(){
		var incluidas = [];
		var clase = this;
		var tiene = false;

		$("#menu .seleccionada").not('.padre').find('input:checkbox:checked').each(function(){
		    tiene = true;
		    var incluida = $(this).val();

		    //si no esta incluida
		    if( $("#in"+incluida ).length <= 0 ){
		    	incluidas.push( incluida );
		    }
		});

		console.log(incluidas);

		//si hay
		if( !jQuery.isEmptyObject(incluidas) ){
			$.ajax({
				data: {"func" : "IncluirCategorias", "proyecto": this.proyecto, "categorias[]" : incluidas},
				type: "post",
				url: this.url,
				success: function(response){
					
					if( $(".nodata").length > 0 ){
						
						$(".nodata").fadeOut(function(){
							$(this).remove();
						});
					}

					$("#categoriasIncluidas").append(response);
					
					//console.log('incluidas listas');
					notifica("Categorias agregadas.");
				}
			}).done(function(){
				
				clase.Guardar();
			});
		}else{
			if( tiene ){
				notificaAtencion("Categoria(s) ya se encuentran incluidas.");
			}else{
				notificaAtencion("Por favor seleccione una categoria.");
			}
		}
	},

	/**
	* GUARDA LA LISTA DE CATEGORIAS INCLUIDAS
	*/
	Guardar: function(){
		//console.log('guardando');

		var incluidasTemp = [];

		$("#categoriasIncluidas li").not('.nodata').each(function(){
			var path = [];
			
			var incluida = $(this).attr('id');

			$( "#"+incluida+' span').each(function(){
				var sub = $(this).attr('id');
				sub = sub.substring(4);

				//console.log( sub );

				path.push( sub );
			});

			//console.log( incluida );
			path.push( incluida.substring(2) );
			incluidasTemp.push( path );

		});

		console.log(incluidasTemp);
		this.incluidas = incluidasTemp;
		
		if( jQuery.isEmptyObject(incluidasTemp) ){
			//vacio
			var queryParams = {"func" : "ExcluirCategorias", "proyecto" : this.proyecto, "categorias" : ""};
			
			if( $(".nodata") ){
				$("#categoriasIncluidas").append('<li class="nodata">No hay categorias incluidas</li>');
			}

		}else{
			var queryParams = {"func" : "ExcluirCategorias", "proyecto" : this.proyecto, "categorias[]" : this.incluidas};
		}

		

		//GUARDA LAS CATEGORIAS
		var proyecto = $("#proyecto").val();
		$.ajax({
			data: queryParams,
			type: "post",
			url: this.url,
			success: function(response){
				if( response.length > 3 ){					
					notificaError("Error: ComponerClass.js Guardar().<hr>"+response);
				}
			},
			fail: function(response){
				notificaError("Error: ComponerClass.js Guardar() AJAX fail.<hr>"+response);
			}
		})
	},	

	/**
	* ELIMINA LAS CATEGORIAS SELECCIONADAS
	*/ 
	Excluir: function(){
		var clase = this;
		$("#categoriasIncluidas .seleccionada").fadeOut(function(){
			$(this).remove();
			clase.Guardar();
		});
	},

	/**
	* COMPONE EL MENU DE UNA CATEGORIA INCLUIDA
	* @param string id -> id de la categoria seleccionada
	*/
	Menu: function(id){
		var clase = this;
		$.contextMenu({
	        selector: '#'+id, 
	        callback: function(key, options) {
	            var m = key;
	            clase.EventosMenu(m, id);
	        },
	        items: {
	        	//"excluir": {name: "Excluir", icon: "delete"},
	        	"excluir": {name: "Excluir Selecciones", icon: "delete", accesskey: "x"},
	            "normas": {name: "Seleccionar Normas", icon: "edit", accesskey: "s"},
	        }
	    });
	},

	/**
	* MANAJA LOS EVENTOS DEL MENU CONTEXTUAL 
	* @param string m -> evento seleccionado
	* @param string id => id de la categoria
	*/
	EventosMenu: function(m, id){
		console.log('evento menu'+m);

		if(m == 'excluir'){
			this.Excluir();
		}else if(m == 'incluir'){
			this.Incluir();
		}else if(m == 'normas'){
			id = id.substring(2);
			//vista de normas
			PreviewCategoriaNormas(id);
		}
	}

});

$componer = new ComponerClass();