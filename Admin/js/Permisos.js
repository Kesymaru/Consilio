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
                console.log( response );

                $("#menu").html( response );

                $("#areas li").on('click', function(){
                    var li = $(this);

                    $("#areas li").removeClass('seleccionada')
                    li.addClass('seleccionada');

                    $("#areas .menu-botones .ocultos").fadeIn(function(){
                        $(this).removeClass('ocultos');
                    });

                    clase.ContextMenu( li.attr('id') );
                });

                //doble click
                $("#areas li").dblclick(function(){
                    console.log( 'doble click en '+ $(this).attr('id') );
                });

            }
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
        console.log('clicked: '+m+' | id: '+id);

        if( m == "clicked: eliminar" ){
            this.Eliminar();
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
        $("#FormularioNuevaArea").validationEngine();

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
                    var descripcion = $("#descripcion").val();

                    $("#areas ul").append("<li>"+nombre+"</li>");
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
        console.log( "Accion de eliminar: "+id );

        var queryParams = {"func" : "EliminarArea", "id" : id };

        $.ajax({
            data: queryParams,
            type: "POST",
            url: "src/ajaxPermisos.php",
            success: function( response ){

                if( response.length <= 3){
                    $("#"+id).fadeOut(function(){
                        $("#"+id).remove();
                    });
                }
            }
        });
    }
});