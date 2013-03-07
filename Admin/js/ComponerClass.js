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

		$.ajax({
			data: queryParams,
			type: "post",
			url: this.url,
			beforeSend: function(){
			},
			success: function(response){
				$("#content").html(response);
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

		$("#menu input:checkbox:checked").each(function(){
		    incluidas.push( $(this).val() );
		});

		console.log(incluidas);

		var send = $.ajax({
			data: {"func" : "IncluirCategorias", "proyecto": this.proyecto, "categorias[]" : incluidas},
			async: false,
			type: "post",
			url: this.url,
			success: function(response){
				$("#categoriasIncluidas").append(response);
				clase.Guardar();
			}
		});

		console.log( send );
	},

	/**
	* GUARDA LA LISTA DE CATEGORIAS INCLUIDAS
	*/
	Guardar: function(){
		var incluidasTemp = [];

		$("#categoriasIncluidas li").each(function(){
			var path = [];
			
			var excluida = $(this).attr('id');
			//console.log( excluida );

			$( "#"+excluida+' span').each(function(){
				//console.log( $(this).attr('id') );
				path.push( $(this).attr('id') );
			});

			path.push( excluida.substring(2) );
			incluidasTemp.push( path );

		});

		console.log(incluidasTemp);
		
		if( jQuery.isEmptyObject(incluidasTemp) ){
			incluidasTemp = [''];
		}

		this.incluidas = incluidasTemp;

		//GUARDA LAS CATEGORIAS
		var proyecto = $("#proyecto").val();
		$.ajax({
			data: {"func" : "ExcluirCategorias", "proyecto" : proyecto, "categorias[]" : this.incluidas},
			async: false,
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
		$("#categoriasIncluidas .seleccionada").fadeOut(function(){
			$(this).remove();
		});
	}

});

$componer = new ComponerClass();