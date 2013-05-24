
/**
 * CLASE PARA LOS PERMISOS
 */
Permisos = function(){};
$.extend(Permisos.prototype, {

    meses: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre'],

    archivo_id: 0,

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

        $("#add-file").on('click', function(){
            clase.AddFile();
        } );

        $('#input0').change(function(e) {
            clase.PreviewFormularioNuevoPermiso( e, 0 );
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

        //resetea los archivos
        $("#archivos-inputs").html('<input type="file" id="input0" name="archivos" />');
        this.archivo_id = 0;

        $("#areas, #responsables").val("").trigger("liszt:updated");
    },

    /**
     * PREVIEW DE UN ARCHIVO AGREGADO
     * @param event e
     * @param int id -> del input del archivo
     */
    PreviewFormularioNuevoPermiso: function(e, id){
        console.log('evento preview '+id);

        var file = $("#input"+id)[0].files;
        $file = file;

        var lista = '';

        if( file[0] ){
            var title = 'Documento';
            var imagen = 'images/folder.png';

            if( file[0].type == 'image/png' || file[0].type == 'image/jpeg' ){
                title = 'Imagen';

                //lee la imagen y la carga en el prview
                var reader = new FileReader();
                //carga el preview de las imagenes
                reader.onloadend = function( e ){
                    console.log('termino carga');
                    $("#file"+id+' .image').attr('src',e.target.result);
                }
                reader.readAsDataURL(file[0]);
            }

            if( file[0].type == 'application/zip' || file[0].type == 'application/rar' ){
                title = 'Archivo';
            }
            if( file[0].type == "application/pdf" ){
                title = "Documento PDF";
            }
            lista = '<li class="file" title="'+title+'" id="file'+id+'" >' +
                        '<img class="image" src="'+imagen+'" />' +
                        '<img class="close" src="images/close.png" title="Quitar '+title+'" onclick="$Permisos.RemoveFile('+id+')" />' +
                        '<div>' +
                            '<span>'+file[0].name+'</span>' +
                        '</div>' +
                    '</li>';
        }

        $("#select-archivos ul").append( lista).hide().fadeIn();
    },

    /**
    * ACCION DE AGREGAR UN NUEVO ARCHIVO
    */
    AddFile: function(){
        var clase = this;
        console.log( 'add file '+this.archivo_id );

        if( clase.archivo_id > 0 ){
            var nuevo = '<input type="file" id="input'+this.archivo_id+'" name="archivos" />';
            var id = this.archivo_id;
            $("#archivos-inputs").append(nuevo);
            $('#input'+this.archivo_id).change(function(e) {
                clase.PreviewFormularioNuevoPermiso( e, id );
            });
        }

        $("#input"+this.archivo_id).trigger('click');
        this.archivo_id++;
    },

    /**
    * REMUEVE DE LA LISTA DE ARCHIVOS UN ARCHIVO INCLUIDO
    */
    RemoveFile: function( id ){
        $("#input"+id).remove();

        $("#file"+id).fadeOut(function(){
            $(this).remove();
        });
    },

});