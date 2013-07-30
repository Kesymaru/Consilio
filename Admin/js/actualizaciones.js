
function Actualizaciones(){
    if( typeof($Actualizaciones) == "undefined" ){
        $Actualizaciones = new Actualizacion();
    }

    $Actualizaciones.init();
}

/**
 * CLASE PARA EL MANEJO DE LAS ACTUALIZACIONES
 */
Actualizacion = function(){};
$.extend(Actualizacion.prototype, {

    proyecto: null,
    id: null,

    /**
     * INICIALIZA
     * @param int proyecto
     */
    init: function(proyecto){
        this.proyecto = proyecto;

        this.Proyectos();
    },

    /**
     * MUESTRA TODOS LOS PROYECTOS CON ACTUALIZACIONES HABILITADAS
     */
    Proyectos: function(){
        var seft = this;

        var queryParams = {
            func : "Proyectos",
        }

        $.ajax({
            data: queryParams,
            type: "POST",
            url: "src/ajaxActualizaciones.php",
            success: function(response){
                if(!$("#menu").is(":visible")){
                    ActivaMenu();
                }

                $("#menu").html(response);

                seft.proyectosEvents();
            }
        });
    },

    proyectosEvents: function(){
        var seft = this;

        $("#proyectos-actualizaciones li").off('click');

        $("#proyectos-actualizaciones li").on(
            "click",
            {
                seft:seft
            },
            this.proyectoClick
        );
    },

    /**
     * CLICK EN PROYECTO
     * @param object event
     */
    proyectoClick: function(event){
        var seft = event.data.seft;

        seft.proyecto = $(this).attr('id');

        seft.Actualizaciones();
    },

    /**
     * MUESTRA LAS ACTUALIZACIONES DEL PROYECTO
     * @type int this.proyecto id del proyecto
     */
    Actualizaciones: function(){
        var seft = this;

        if(typeof(this.proyecto) != "string"){
            throw new Error("Actualizaciones::proyecto no es un numero");
        }

        var queryParams = {
            func : "Actualizaciones",
            proyecto: this.proyecto,
        }
        console.log( queryParams );
        $.ajax({
            data: queryParams,
            type: "POST",
            url: "src/ajaxActualizaciones.php",
            success: function(response){
                console.log( response );

                if(!$("#menu2").is(":visible")){
                    Menu2();
                }

                $("#menu2").html(response);

                seft.actualizacioneEvents();
            }
        });

    },

    /**
     * CREA LOS EVENTOS
     */
    actualizacioneEvents: function(){
        var seft = this;

        $("#actualizaciones ul li").off("click");

        $("#actualizaciones ul li").on(
            "click",
            {
                seft:seft
            },
            this.actualizacionClick
        );

    },

    /**
     * EVENTOS AL DAR CLICK EN UNA ACTUALIZACION
     * @param object e event
     */
    actualizacionClick: function(e){
        var seft = e.data.seft;

        seft.id = $(this).attr('id');

        seft.editarActualizacion();
    },

    /**
     * EDITA UNA ACTUALIZACION
     */
    editarActualizacion: function(){
        var queryParams = {
            func     : "editarActualizacion",
            proyecto : this.proyecto,
            id       : this.id,
        };

        $.ajax({
            data: queryParams,
            type: "POST",
            url: "src/ajaxActualizaciones.php",
            beforeSend: function(){
                Loading();
            },
            success: function(response){

                $("#content").html(response);

                LoadingClose();
            },
            error: function(e){
                LoadingClose();
            },
            fail: function(e){
                LoadingClose(e);
            }
        });

    },

    guardarActualizacion: function(){

    },

    eliminarActualizacion: function(){

    }



})

