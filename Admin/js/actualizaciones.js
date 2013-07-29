
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

                if(!$("#menu2").is(":visible")){
                    Menu2();
                }

                $("#menu2").html(response);
            }
        });

    },

    /**
     * EDITA UNA ACTUALIZACION
     * @param int id
     */
    editarActualizacion: function(id){
        this.id = id;

        var queryParams = {
            func : "editarActualizacion",
            id: this.id,
        };

        $.ajax({
            data: queryParams,
            type: "POST",
            url: "src/ajaxActualizacion.php",
            success: function(response){

            }
        });

    },



})

