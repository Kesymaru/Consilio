
/**
 * CLASE PARA LOS PERMISOS
 */
Permisos = function(){};
$.extend(Permisos.prototype, {
    proyecto: 0,
    meses: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre'],

    archivo_id: 0,

    responsables: [],
    emails: [],

    //almacena el formulario
    formularioNuevo: '',

    init: function(proyecto){
        this.proyecto = proyecto;
        var clase = this;
        var queryParams = { "func" : "TabPermisos", "proyecto" : clase.proyecto};

        $.ajax({
            data: queryParams,
            type: "post",
            url: "src/ajaxPermisos.php",
            success: function(response){
                //console.log( response );
                $("#menu2").html(response);

                var alto = $("#content").height() - $("#titulos div").height();
                $("#panel-permisos").height(alto);

                clase.Calendario();

            }
        });
    },

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

        //console.log( 'eventos del calendario inicializados ');
        this.formularioNuevo = $("#FormularioNuevoPermiso").clone(true);

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
                    //console.log('cal '+i+' '+response[i]);

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

                clase.HidePanelEdicion();

                $("#lista-permisos").fadeOut(500, function(){
                    $("#lista-permisos").html( response).fadeIn(500);
                });
            }
        });
    },

    /**
     * MUESTRA EL FORMULARIO PARA UN PERMISO NUEVO
     * @returns {boolean}
     */
    NuevoPermiso: function(){
        var clase = this;

        var queryParams = {"func": "NuevoPermiso", "proyecto" : clase.proyecto};

        //si ya existe no lo recarga lo resetea
        if( $("#FormularioNuevoPermiso").length ){
            console.log('reset formulario');

            this.ResetFormulario();

            //return true;
        }

        $.ajax({
            data: queryParams,
            type: "POST",
            url: "src/ajaxPermisos.php",
            success: function( response ){
                $("#panel-edicion").html(response);
                clase.InicializaFormularioNuevoPermiso();
                clase.ShowPanelEdicion();
            }
        });

    },

    /**
     * MUESTRA EL FORMULARIO CON ANIMACION
     */
    ShowPanelEdicion: function(){
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
     * ESCONDE EL PANEL DE EDICION
     * @constructor
     */
    HidePanelEdicion: function(){

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
                    tokenSeparators: [","],
                    createSearchChoice: function(term, data) {
                        if ( $(data).filter(function() {
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
     * CARGA LOS RESPONSABLES SELECCIONADOS DE UN PERMISO
     * @param int id -> id del permiso
     */
    SelectedResponsables: function(id){

        var clase = this;

        var queryParams = {"func" : "getResponsables", "id" : id};

        $.ajax({
            data: queryParams,
            type: "POST",
            dataType: "JSON",
            url: "src/ajaxPermisos.php",
            success: function( response ){
                console.log( "SELECTED RESPONSABLES "+response );
                $respuesta = response;

                $("#responsables").select2("destroy");

                //carga select con opcion de agregar
                $("#responsables").select2({
                    initSelection : function (element, callback) {
                        $data = [];

                        $( element.val().split(",")).each(function (f) {
                            for( var i = 0; i < response.selected.length; i++ ){
                                if( response.selected[i]['id'] == f ){
                                        $data.push( response.selected[i] );
                                }
                            }
                        });
                        callback($data);
                    },
                    tags: response.tags,
                    allowClear: true,
                    multiple: true,
                    tokenSeparators: [","],
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

        //console.log("EMAILS: "+emails);
    },

    /**
     * INICIALIZA LOS COMPONENTES EN COMUN PARA EL FORM DE EDITAR Y DE NUEVO PERMISO
     */
    InicializaFormulario: function(){
        var clase = this;

        $("#add-file").off('click');
        $("#add-file").on('click', function(){
            clase.AddFile();
        } );

        $('#input0').change(function(e) {
            clase.PreviewFormularioPermiso( e, 0 );
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

    },

    /**
     * INICIALIZA EL FORMULARIO PARA UN NUEVO PERMISO
     */
    InicializaFormularioNuevoPermiso: function(){
        var clase = this;

        this.InicializaFormulario();

        //$('#FormularioNuevoPermiso').on('reset', this.ResetFormulario );

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

                clase.HidePanelEdicion();
                clase.ResetFormulario();

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
    ResetFormulario: function(){
        var clase = this;

        //$('#FormularioNuevoPermiso')[0].reset();
        $("#select-archivos ul li").fadeOut(function(){
            $(this).remove();
        });

        //resetea los archivos
        $("#archivos-inputs").html('<input type="file" id="input0" name="archivo0" />');
        this.archivo_id = 0;

        $("#emails").select2('data',[]).val("");
        $("#responsables").select2('data',[]).val("");
        $("#areas").select2('data',[]).val("");


    },

    /**
     * PREVIEW DE UN ARCHIVO AGREGADO
     * @param event e
     * @param int id -> del input del archivo
     */
    PreviewFormularioPermiso: function(e, id){
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
                clase.PreviewFormularioPermiso( e, id );
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

    /**
     * EDITA UN PERMISO
     * @param id -> id del permiso
     * @returns {boolean}
     * @constructor
     */
    Editar: function( id ){
        if( id == undefined ){
            return false;
        }

        var clase = this;

        var queryParams = {"func" : "EditarPermiso", "id" : id };

        $.ajax({
            data: queryParams,
            type: "POST",
            url: "src/ajaxPermisos.php",
            success: function( response ){
                console.log( response );
                $("#panel-edicion").html( response );

                clase.InicializaFormularioEditarPermiso(id);

                clase.ShowPanelEdicion();
            }
        });
    },

    /**
     * INICIALIZA EL FORMULARIO DE EDICION DE UN PERMISO
     */
    InicializaFormularioEditarPermiso: function(id){
        var clase = this;

        //componentes en comun
        this.InicializaFormulario();
        this.SelectedResponsables(id);

        //$('#FormularioNuevoPermiso').on('reset', this.ResetFormulario );

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

                clase.HidePanelEdicion();
                clase.ResetFormulario();

                //actualiza calendario
                clase.RefreshCalendar();
            },
            fail: function(){
            }
        };

        //$('#FormularioEditarPermiso').ajaxForm(options);
    },

    /**
     * ELIMINA UN PERMISO
     * @param id
     */
    Eliminar: function(id){
        this.RemovePermiso( id );
    },

    /**
     * ELIMINA UN PERMISO
     * @param id
     * @constructor
     */
    RemovePermiso: function(id){
        $("#permiso-"+id).fadeOut(function(){
            $(this).remove();
        });
    },

    /**
     * ELIMINA UN ARCHIVO ADJUNTADO DE UN PERMISO
     * @param int id -> id del archivo
     */
    RemoveArchivo: function(id){
        var queryParams = {"func" : "EliminarArchivo", "id" : id};

        $.ajax({
            data: queryParams,
            type: "POST",
            url: "src/ajaxPermisos.php",
            success: function(response){
                console.log( response );
                $("#archivo"+id).fadeOut(700,function(){
                    $(this).remove();
                })
            }
        });
    }

});