
/**
 * CLASE PARA LOS PERMISOS
 */
Permisos = function(){};
$.extend(Permisos.prototype, {

    meses: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre'],


    /**
     * INICIALIZA EL CALENDARIO DE LOS PERMISOS
     */
    Calendario: function(){
        var clase = this;

        //var meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre'];

        $("#calendar-permisos .mes").on('click',function(){
            $("#permisos-mes").html( clase.meses[ $(this).attr('id') ] );

            if ( !$(this).hasClass('mes-actived') ){
                clase.NuevoPermiso();
                return false;
            }

            var mes = $(this).attr('id');
            var year = $("#year").text();

            //CARGA LOS PERMISOS DE UN MES
            clase.Permisos(year, mes);

        });

        //FUNCIONES DE CAMBIAR DE YEAR
        $("#previous-year-calendar").on('click', function(){
            var year = $("#year").text();
            year--;
            $("#year").text( year );
            clase.CalendarioYear(year);
        });

        $("#next-year-calendar").on('click', function(){
            var year = $("#year").text();
            year++;
            $("#year").text( year );
            clase.CalendarioYear(year);
        });

        console.log( 'eventos del calendario inicializados ');

    },

    /**
     * CARGA EL CALENDARIO DE UN YEAR ESPECIFICO
     * @param int year
     */
    CalendarioYear: function(year){
        var queryParams = {"func":"CalendarYear", "year": year};
        console.log( 'carga calendario '+year );

        $.ajax({
            data: queryParams,
            type: "post",
            url: "src/ajaxPermisos.php",
            dataType: 'JSON',
            success: function( response ){
                console.log( response[4] );
                $calendario = response;

                for( var i= 0; i <= response.length-1; i++ ){
                    console.log('cal '+i+' '+response[i]);
                    if( response[i] == 0 || response[i] == undefined ){
                        $("#"+i).removeClass('mes-actived');
                    }else{
                        $("#"+i).addClass('mes-actived');
                        $("#"+i+" .contador-permisos").text( response[i] );
                    }
                }
            },
        });

    },

    /**
     * CARGA LOS PERMISOS DE UN MES
     * @param year
     * @param month
     */
    Permisos: function(year, month){
        var clase = this;
        var queryParams = {"func" : "Permisos", "year" : year, "month": month };

        $.ajax({
            data: queryParams,
            type: "POST",
            url: "src/ajaxPermisos.php",
            success: function(response){

                clase.HideFormularioNuevoPermiso();

                $("#lista-permisos").fadeOut(500, function(){
                    $("#lista-permisos").html( response).fadeIn(500);
                });
            }
        });
    },

    NuevoPermiso: function(){

        this.ResetFormularioNuevoPermiso();
        this.ShowFormularioNuevoPermiso();
        this.InicializaFormularioNuevoPermiso();

    },

    ShowFormularioNuevoPermiso: function(){
        var alto = $("#panel-permisos").height();
        var margen = $("#lista-permisos").height();

        $("#panel-edicion").animate({
            "margin-top" : '-'+margen+'px',
            height: alto
        }, {
            duration: 700,
            queue: false,
            complete: function(){
                $("#panel-edicion").css({
                    "margin-top" : '-'+margen+'px',
                    "height" : alto+'px'
                });

                $("#FormularioNuevoPermiso").enableSelection();

                $("#panel-edicion").addClass('panel-edicion-activo');
                $("#areas, #responsables").chosen();

            }
        });
    },

    /**
     * ESCONDE EL PANEL DE EDICION
     * @constructor
     */
    HideFormularioNuevoPermiso: function(){

        $("#panel-edicion").animate({
            "margin-top" : '100%',
            height: 0
        }, {
            duration: 700,
            queue: false,
            complete: function(){
                $("#panel-edicion").css({
                    "margin-top" : '100%',
                    "height" : 0
                });
                $("#panel-edicion").removeClass('panel-edicion-activo');
            }
        });

    },

    /**
     * INICIALIZA EL FORMULARIO PARA UN NUEVO PERMISO
     */
    InicializaFormularioNuevoPermiso: function(){
        var clase = this;

        $('#FormularioNuevoPermiso').on('reset', this.ResetFormularioNuevoPermiso );

        $('#archivos').change(function(e) {
            clase.PreviewFormularioNuevoPermiso( e );
        });

        $("#FormularioNuevoPermiso").validationEngine();

        var options = {
            beforeSend: function(){
                DeshabilitarContent();
            },
            success: function(response) {
                console.log( response );
            },
            fail: function(){
            }
        };

        //$('#FormularioNuevoPermiso').ajaxForm(options);

    },

    /**
     * RESETEA EL FORMULARIO
     */
    ResetFormularioNuevoPermiso: function(){

        $('#FormularioNuevoPermiso')[0].reset();
        $("#select-archivos ul li").fadeOut(function(){
            $(this).remove();
        });
        $("#areas, #responsables").val("").trigger("liszt:updated");
    },

    /**
     * PREVIEW DE LOS ARCHIVOS DEL FORMULARIO
     * @param event e
     */
    PreviewFormularioNuevoPermiso: function(e){
        var files = $("#archivos")[0].files;
        console.log( files );
        var registros = [];
        var contador = 0;

        var lista = '';

        for (var i = 0; i < files.length; i++){
            $file = files[i];
            if( files[i].type == "image/png" || files[i].type == "image/jpg" || files[i].type == "image/gif" ){
                lista += '<li class="file" title="'+files[i].type+'" >';
                lista += '<img class="image" id="image-'+i+'" src="images/folder.png" />';
                registros.push(i);

                var reader = new FileReader();

                //carga el preview de las imagenes
                reader.onloadend = function( e ){
                    $("#image-"+registros[contador]).attr('src',e.target.result);
                    contador++;
                }
                reader.readAsDataURL(files[i]);

            }else{
                lista += '<li class="file" title="file" >';
                lista += '<img class="image" src="images/folder.png" />';
            }

            lista += '<div><span>'+files[i].name+'</span></div></li>';
        }

        $("#select-archivos ul").html( lista );
    },

});