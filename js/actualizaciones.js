/**
 * CASE PARA LA TAB DE ACTUALIZACION
 * User: Andrey Alfaro Alvarap
 */

Actualizacion = function(){};
$.extend(Actualizacion.prototype, {
    proyecto: null,
    timeOut: null,

    /**
     * @param int proyecto
     */
    init: function(proyecto){
        this.proyecto = proyecto;

        if( !$("#menu2").is(":visible") ){
            Menu2();
        }

        this.load();
    },

    /**
     * CARGA LAS ACTUALIZACIONES DEL PROYECTO
     * @returns boolean
     */
    load: function(){
        var seft = this;

        if( typeof(this.proyecto) != "number"){
            console.log("Error: Actualizacion.proyecto is not a number.");
            return false;
        }

        var queryParams = {
            func : "ActualizacionesProyecto",
            proyecto : this.proyecto,
        };

        $.ajax({
            data: queryParams,
            type: "POST",
            url: "src/ajaxActualizaciones.php",
            success: function(response){
                $("#menu2").html( response );

                seft.CalendarEvents();
                seft.hidePanel();
                $("#content").html("");
            }
        });

    },

    /**
     * LOAD DE EVENTS
     * @constructor
     */
    CalendarEvents: function(){
        var seft = this;

        $("#actualizacion-calendario ul li").off('click');

        $("#actualizacion-calendario ul li").on('click', function(){
            var year = $(this).attr('class');

            if( year != "year"){
                var month = $(this).attr('id');

                seft.Actualizacion(year, month);
            }

        });
    },

    /**
     * CARGA UNA VISTA DE ACTUALIZACION
     * @param int year
     * @param int month
     * @return boolean
     */
    Actualizacion: function( year, month ){
        var seft = this;

        var queryParams = {
            func: "ActualizacionesMonth",
            year: year,
            month: month,
        }

        $.ajax({
            data: queryParams,
            type: "POST",
            url: "src/ajaxActualizaciones.php",
            success: function(response){

                $("#content").html("");
                $("#content").html(response);

                seft.showPanel();
            }
        });

    },

    /**
     * TOGGLE THE PANEL
     * @param object callback function
     */
    togglePanel: function(callback){

        if( $("#content").is(":visible") ){
            this.hidePanel();
        }else{
            if( typeof(callback) == "function" ){
                this.showPanel(callback);
            }else{
                this.showPanel();
            }
        }
    },

    /**
     * MUESTRA EL PANEL
     * @param object callback function
     */
    showPanel: function(callback){
        var seft = this;
        var panel = $('#content');
        var calendario = $('#menu2');

        $("#actualizacion-content li").css({
            opacity: 0,
            width: 0,
        });

        if(!$("#content").is(":visible")){

            panel.css({
                float: "left",
                display: "inline-block",
            });

            panel.animate({
                width: "50%",
                opacity: 1,
                float: "left",
            },{
                duration: 1000,
                queue: false,
                complete: function(){
                    panel.css({
                        width: "50%",
                        opacity: 1,
                        float: "left",
                    });

                    $("#actualizacion-content").height($("#content").height());

                    seft.animateList( $('#actualizacion-content li:first') );
                }
            });

            calendario.animate({
                width: "30%",
                opacity: 1,
            },{
                duration: 1000,
                queue: false,
                complete: function(){
                    calendario.css({
                        width: "30%",
                        opacity: 1,
                    });
                }
            });
        }else{
            $("#actualizacion-content").height($("#content").height());

            this.animateList( $('#actualizacion-content li:first') );
        }

    },

    /**
     * OCULTA EL PANEL
     */
    hidePanel: function(){
        var panel = $('#content');
        var calendario = $('#menu2');

        if( $("#content").is(":visible") ){

            calendario.animate({
                width: "80%",
                opacity: 1,
            },{
                duration: 1000,
                queue: false,
                complete: function(){
                    calendario.css({
                        width: "80%",
                        opacity: 1,
                    });
                }
            });

            panel.animate({
                width: "0%",
                opacity: 0,
            },{
                duration: 1000,
                queue: false,
                complete: function(){
                    panel.css({
                        width: "0%",
                        opacity: 1,
                        display: "none",
                    });
                }
            });

        }
    },

    /**
     * ANIMACION PARA CUANDO SE CARGA UNA LISTA
     * @param object li first li element
     */
    animateList: function( li ){
        console.log('animate list');
        var seft = this;

        //animacion para la lista
        li.animate({
            opacity: 1,
            width: "100%",
        },700);

        //anima el siguiente
        seft.timeOut = setTimeout(function(){

            if( li.next('li').length ){
                console.log('tiene', li.next('li').length );
                seft.animateList( li.next('li') );
            }else{
                console.log('no tiene');
                clearTimeout(seft.timeOut);
                seft.timeOut = null;
                return false;
            }

        },500);

    },

});
