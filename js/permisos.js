
/**
 * CLASE PARA LOS PERMISOS
 */
Permisos = function(){};
$.extend(Permisos.prototype, {

    meses: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre'],

    archivo_id: 0,

    responsables: [],
    emails: [],

    /**
     * INICIALIZA EL CALENDARIO DE LOS PERMISOS
     */
    Calendario: function(){
        var clase = this;

        //var meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre'];

        $("#calendar-permisos .mes").on('click',function(){
            $("#permisos-mes span").html( clase.meses[ $(this).attr('id') ] );

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
                console.log( response );
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
     * REFRESCA EL CALENDARIO
     */
    RefreshCalendar: function(){
        var year = $("#year").text();
        this.CalendarioYear(year);
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

    /**
     * MUESTRA EL FORMULARIO CON ANIMACION
     */
    ShowFormularioNuevoPermiso: function(){
        var clase = this;

        var alto = $("#panel-permisos").height();
        var margen = $("#lista-permisos").height();

        $("#panel-edicion").animate({
            "margin-top" : '-'+alto+'px',
            height: alto
        }, {
            duration: 700,
            queue: false,
            complete: function(){
                $("#panel-edicion").css({
                    "margin-top" : '-'+alto+'px',
                    "height" : alto+'px'
                });

                $("#panel-edicion").addClass('panel-edicion-activo');

//                $("#areas, #responsables").chosen();

                $("#areas").select2({
                    width: "100%",
                    allowClear: true,
                    placeholder: $(this).attr('placeholder'),
                });

                clase.SelectResponsables();
                clase.SelectMails();
            }
        });
    },

    /**
     * CARGA EL SELECT DE RESPONSABLES
     */
    SelectResponsables: function(){
        var clase = this;

        var queryParams = {"func" : "getResponsables"};

        $.ajax({
            data: queryParams,
            type: "POST",
            dataType: "JSON",
            url: "src/ajaxPermisos.php",
            success: function( response ){
                console.log( response );

                clase.responsables = response;

                $("#responsables").select2("destroy");

                //carga select con opcion de agregar
                $("#responsables").select2({
                    tags: clase.responsables,
                    allowClear: true,
                    multiple: true,
                    tokenSeparators: [",", " "],
                    createSearchChoice: function(term, data) {
                        if ($(data).filter(function() {
                            return this.text.localeCompare(term) === 0;
                        }).length === 0) {
                            return {
                                id: term,
                                text: term,
                                title: term
                            };
                        }
                    },
                });

            }
        });
    },

    /**
     * INICIALIZA EL SELECT DE EMAILS CON OPCION PARA AGREGAR
     */
    SelectMails: function(){
        var clase = this;

        var queryParams = {"func" : "getMails"};

        $.ajax({
            data: queryParams,
            type: "POST",
            dataType: "JSON",
            url: "src/ajaxPermisos.php",
            success: function( response ){
                console.log( response );

                clase.emails = response;

                $("#emails").select2("destroy");

                //carga select con opcion de agregar
                $("#emails").select2({
                    tags: clase.emails,
                    allowClear: true,
                    multiple: true,
                    tokenSeparators: [",", " "],
                    createSearchChoice: function(term, data) {
                        if ($(data).filter(function() {
                            return this.text.localeCompare(term) === 0;
                        }).length === 0) {
                            return {
                                id: term,
                                text: term,
                                title: term
                            };
                        }
                    },
                });

            }
        });
    },

    /**
     * VALIDA LOS SELECTS DEL FORMULARIO
     */
    ValidaSelects: function(){
        var emails = $("#emails").val().split(',');
        var areas = [];

        $("#areas").find(":selected").each(function(){
            areas.push($(this).val());
        });

        if( !emails.length){
            $('#emails').validationEngine('showPrompt', 'Se Reguiere almenos un email', 'load');
            return false;
        }
        if( !areas.length ){
            $('#areas').validationEngine('showPrompt', 'Se Reguiere almenos una area de aplicacion', 'load');
            return false;
        }

        var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

        for( var i = 0; i < emails.length; i++ ){
            console.log( i+" "+emails[i].replace(/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}$/i) );

            if( emails[i] == '' || emails[i] == undefined ){
                $('#emails').validationEngine('showPrompt', 'Se Reguiere almenos un email', 'load');
                return false;
            }else{
                var coincidencias = emails[i].match(regex);

                if( coincidencias == '' || coincidencias == null){
                    $('#emails').validationEngine('showPrompt', 'Email incorrecto', 'load');
                    return false;
                }
                if( coincidencias.length ){
                    console.log( coincidencias );
                    return true;
                }
            }


        }

        console.log("EMAILS: "+emails);
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

        $("#add-file").off('click');
        $("#add-file").on('click', function(){
            clase.AddFile();
        } );

        $('#input0').change(function(e) {
            clase.PreviewFormularioNuevoPermiso( e, 0 );
        });

        $("#FormularioNuevoPermiso").validationEngine({
            promptPosition : "topLeft",
            scroll: true,
            prettySelect : true,
            useSuffix: "_chzn, ",
            showOneMessage: true,
            ignore: ".ignore, .select2-offscreen",
        });

        $( "#fecha_expiracion, #fecha_emision, #recordatorio" ).datepicker( {
            dateFormat: 'dd/mm/yy',
        });

        /*//selector de fecha para fecha de emision
        $( "#fecha_emision" ).datepicker( {
            dateFormat: 'dd/mm/yy',
            onSelect: function(){
                console.log('cambio para fecha expiracion');
                var date = new Date( $("#fecha_emision").val() );

                $("#fecha_expiracion").datepicker( "option", "minDate", date );
                //$("#fecha_expiracion").datepicker( "refresh" );
            }
        });

        //selector de fecha para expiracion
        $( "#fecha_expiracion" ).datepicker( {
            dateFormat: 'dd/mm/yy',
            onSelect: function(){
                console.log('cambio para recordatorio expiracion');
                var date = new Date( $("#fecha_expiracion").val() );

                $("#recordatorio").datepicker( "option", "minDate",date );
                //$("#recordatorio").datepicker( "refresh" );
            }
        });

        //selector de fecha para el recordatorio
        $( "#recordatorio" ).datepicker( {
            dateFormat: 'dd/mm/yy',
        });*/

        var options = {
            beforeSend: function(){
                if( $("#select-archivos input").length <= 0 ){
                    $('#select-archivos').validationEngine('showPrompt', '*Este campo es obligatorio', 'load');
                    return false;
                }
            },
            beforeSubmit: clase.ValidaSelects,
            success: function(response) {
                console.log( response );
                clase.HideFormularioNuevoPermiso();
                clase.ResetFormularioNuevoPermiso();

                //actualiza calendario
                clase.RefreshCalendar();
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
        var clase = this;

        $('#FormularioNuevoPermiso')[0].reset();
        $("#select-archivos ul li").fadeOut(function(){
            $(this).remove();
        });

        //resetea los archivos
        $("#archivos-inputs").html('<input type="file" id="input0" name="archivo0" />');
        this.archivo_id = 0;

//        $("#emails").select2('data',[]).val("");
        $("#responsables").select2('data',[]).val("");
        $("#areas").select2('data',[]).val("");

//        $("#areas, #responsables").val("").trigger("liszt:updated");

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

            var nombre = file[0].name;
            if( nombre.length > 17){
                nombre = file[0].name.substring(17,0) + "...";
            }

            lista = '<li class="file" title="'+title+'" id="file'+id+'" >' +
                        '<img class="image" src="'+imagen+'" />' +
                        '<img class="close" src="images/close.png" title="Quitar '+title+'" onclick="$Permisos.RemoveFile('+id+')" />' +
                        '<div>' +
                            '<span>'+nombre+'</span>' +
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
            var nuevo = '<input type="file" id="input'+this.archivo_id+'" name="archivo'+this.archivo_id+'" />';
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