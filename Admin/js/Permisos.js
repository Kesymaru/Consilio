/**
 * MANEJA LAS AREAS DE LOS PERMISOS
 */

/**
 * INICIA LA CLASE DE AREAS DE APLIACION
 */
function ClientesAreasAplicacion(){
    if(typeof $AreasAplicacion == 'undefined'){
        $AreasAplicacion = new AreasAplicacion();
    }
    $AreasAplicacion.Show();
}

/**
 * CLASE PARA LAS AREAS DE APLIACION
 */
AreasAplicacion = function(){};
$.extend(AreasAplicacion.prototype, {

    //id area en edicion/ultima editada
    id: false,

    /**
     * MUESTRA EL PANEL DE EDICION DE AREAS DE PERMISOS
     */
    Show: function(){
        LimpiarContent();

        if( !$("#menu").is(':visible') ){
            ActivaMenu();
        }
        this.Areas();
    },

    /**
     * CARGA EL MENU DE LAS AREAS DE APLIACION
     * @constructor
     */
    Areas: function(){
        var clase = this;

        var queryParams = {"func" : "AreasAplicacion"};

        $.ajax({
            data: queryParams,
            type: "POST",
            url: "src/ajaxPermisos.php",
            success: function( response ){

                $("#menu").html( response );

                clase.AreasEventos();

            }
        });
    },

    /**
     * CREA LOS EVENTOS PARA LA LISTA DE AREAS
     * @constructor
     */
    AreasEventos: function(){
        var clase = this;

        //quita eventos agregados
        $("#areas li").off('click');
        $("#areas li").off('dblclick');

        $("#areas li").on('click', function(){
            var li = $(this);

            $("#areas li").removeClass('seleccionada')
            li.addClass('seleccionada');

            $("#areas .menu-botones .ocultos").fadeIn(function(){
                $(this).removeClass('ocultos');
            });

            clase.ContextMenu( li.attr('id') );
        });

        //DOBLE CLICK
        $("#areas li").on('dblclick', function(){
            var id = $(this).attr('id');
            clase.Editar( id );
        });
    },

    /**
     * INICIALIZA EL CONTEXT MENU PARA UNA AREA SELECCIONADA
     * @param int id
     */
    ContextMenu: function( id ){
        var clase = this;

        $.contextMenu({
            selector: '#areas #'+id,
            callback: function(key, options) {
                var m = "clicked: " + key;
                //window.console && console.log(m) || alert(m);
                clase.ContextMenuSelect(m, id);
            },
            items: {
                "nuevo": {name: "Nueva Area", icon: "add", accesskey: "n"},
                "editar": {name: "Editar", icon: "edit", accesskey: "e"},
                "eliminar": {name: "Eliminar", icon: "delete", accesskey: "l"},
            }
        });


    },

    /**
     * CALLBACK PARA EL CONTEXT MENU AL SELECCIONAR UNA OPCION
     * @param event m
     * @param int id
     */
    ContextMenuSelect: function( m, id ){
        //console.log('clicked: '+m+' | id: '+id);

        switch( m ){
            case "clicked: nuevo":
                this.Nueva();
                break;

            case "clicked: editar":
                this.Editar(id);
                break;

            case "clicked: eliminar":
                this.Eliminar(id);
                break;
        }
    },

    /**
     * MUESTRA EL FORMULARIO PARA CREAR UNA NUEVA AREA DE APLICACION
     */
    Nueva: function(){
        var clase = this;

        var queryParams = {"func" : "NuevaArea" };

        $.ajax({
            data: queryParams,
            type: "POST",
            url: "src/ajaxPermisos.php",
            success: function(response){
                $("#content").html( response );

                //inicializa el form
                clase.FormularioNuevaArea();
            }
        });
    },

    /**
     * INICIALIZA EL FORMULARIO PARA UNA NUEVA AREA DE APLIACION
     */
    FormularioNuevaArea: function(){
        var clase = this;

        $("#FormularioNuevaArea").validationEngine();

        var options = {
            beforeSend: function(){
                DeshabilitarContent();
            },
            success: function(response) {
                HabilitarContent();

                if( jQuery.isNumeric( response ) ){
                    var nombre = $("#nombre").val();
                    var id = response;
                    var descripcion = $("#descripcion").val();

                    if( descripcion.length > 50 ){
                        descripcion = descripcion.substring(0,50)+'...';
                    }

                    var lista = '<li id ="'+id+'" ';
                    if( descripcion.length ){
                        lista += ' title="'+descripcion+'" ';
                    }
                    lista += '>'+nombre+'</li>';

                    $("#areas ul").append(lista);
                    $("#"+id).hide().fadeIn(function(){
                        //refresca los eventos de la lista
                        clase.AreasEventos();
                    });

                    LimpiarContent();
                }else{
                    notificaError(response);
                }

            },
            fail: function(){
            }
        };

        $('#FormularioNuevaArea').ajaxForm(options);
    },

    /**
     * EDITAR UN AREA DE APLICACION
     * @param id
     */
    Editar: function( id ){
        var clase = this;

        if( id == undefined || id == '' || id == null ){
            id = $("#areas .seleccionada").attr('id');
        }

        var queryParams = {"func" : "EditarArea", "id" : id};

        $.ajax({
            data: queryParams,
            type: "POST",
            url: "src/ajaxPermisos.php",
            success: function( response ){

                if( response.length > 0 ){
                    $("#content").html( response );
                    clase.FormularioEditarArea();
                }
            }
        });

    },

    /**
     * INICIALIZA EL FORMULARIO DE EDICION DE UNA AREA DE APLICACION
     */
    FormularioEditarArea: function(){
        $("#FormularioEditarArea").validationEngine();

        var clase = this;

        var options = {
            beforeSend: function(){
                DeshabilitarContent();
            },
            success: function(response) {
                HabilitarContent();

                console.log( response.length );

                if( response.length <= 3 ){
                    var nombre = $("#nombre").val();
                    var id = $("#area").val();
                    var descripcion = $("#descripcion").val();

                    //actualiza el nombre
                    if( $("#"+id).length ){
                        $("#"+id).text(nombre);

                        //actualiza el title
                        if( descripcion.length > 50 ){
                            $("#"+id).attr('title', descripcion.substring(0,50)+'...' );
                        }else if( descripcion.length ) {
                            $("#"+id).attr('title', descripcion );
                        }else{
                            $("#"+id).removeAttr('title');
                        }

                    }
                    LimpiarContent();
                }else{
                    notificaError(response);
                }

            },
            fail: function(){
            }
        };

        $('#FormularioEditarArea').ajaxForm(options);
    },

    /**
     * ELIMINA UNA AREA DE APLIACION
     */
    Eliminar: function( id ){
        var clase = this;

        if( id == undefined || id == '' ){
            id = $("#areas .seleccionada").attr('id');
        }

        var si = function (){
            clase.EliminarAccion( id );
        }

        var no = function (){
            notificaAtencion("Operacion cancelada");
        }

        Confirmacion("Deseas eliminar la Area de Aplicacion.", si, no);
    },

    /**
     * ACCION DE ELIMINAR UNA AREA DE APLICACION
     * @param int id
     */
    EliminarAccion: function( id ){

        var queryParams = {"func" : "EliminarArea", "id" : id };

        //SI ESTA EDITANDO LA QUE VA A ELIMINAR
        if( $("#area").length ){
            if( $("#area").val() == id ){
                LimpiarContent();
            }
        }

        $.ajax({
            data: queryParams,
            type: "POST",
            url: "src/ajaxPermisos.php",
            success: function( response ){
                console.log( response );
                if( response.length <= 3){
                    $("#"+id).fadeOut(function(){
                        $("#"+id).remove();
                    });
                }
            }
        });
    }
});