/**
 * HORIZONTAL TREE
 * @param object options
 */
(function ( $ ) {

    $.fn.hztree = function( options ) {

        /******************************************
         * DEFAULT SETTINGS
         ******************************************/

        this.level = 0; //current level
        this.previousLevel = this.level;
        this.parent = 0; //current parent
        this.element = false; //element clicked
        this.containerWidth = $(this).width(); //container width

        //CONTENT AND ANIMATIONS SETTINGS
        this.title = "Categorias"; //title
        this.toolbar = true;
        this.project = 0;

        //ANIMATIONS SETTINGS
        this.animations = true; //show animations
        this.spin = true; //show animate load spin

        //CHECKBOX SETTINGS
        this.checkbox = true; //has checkbox
        this.checkOnClick = false; //check the checkbox on click the li
        this.checkboxCallback = false; //callback when a checkbox is checked
        this.track = true; //track checked checkboxes
        this.tracked = {} // id of checkboxes tracked

        //MENU SETTINGS
        this.menu = true; //has menu
        this.menuItems = false;
        this.menuCallbacks = false;

        //AJAX SETTINGS
        this.ajaxUrl = "src/ajax.php"; //ajax url
        this.ajaxData = false;
        this.ajaxType = "POST"; //ajax type

        /******************************************
         * METHODS
         ******************************************/

        /**
         * LOAD PLUGIN
         * @param object options settings
         */
        this.init = function(options){

            //load settings
            this.setPropieties(options);
            this.status();

            //clear html
            $(this).html("");

            $(this).addClass("horizontaltree");

            var toolbar = '';

            if( this.toolbar ){
                toolbar += '<span id="hztreeToolbar">'+
                                '<button id="hztreeButtonData" class="icon izquierda">' +
                                    'Incluidas' +
                                '</button>';

                if( typeof this.title === "string" ){
                    toolbar += this.title;
                }

                toolbar += '</span>';
            }else{
                //if has title
                if( typeof this.title === "string" ){
                    toolbar = '<span id="hztreeTitle">' +
                                this.title +
                               '</span>';
                }
            }

            var table =
                toolbar +
                '<span id="hztreeContainer">'+
                    '<table>' +
                        '<tbody>' +
                            '<tr id="hztreeRow">' +
                                '<td id="level'+this.level+'" >' +
                                '</td>'+
                            '</tr>' +
                        '</tbody>'+
                    '</table>' +
                '</span>';

            if( toolbar !== "" ){
                table += '<div id="hztreeData">' +
                         '</div>';
            }

            $(this).html(table);

            this.loadLevel();

            this.addEvent(this.level);
        };

        /**
         * SET THE PROPIETIES
         * @param object optiones
         */
        this.setPropieties = function(options){
            if( typeof options === "object" ){

                //load settings
                if( typeof options["level"] !== "undefined" ){
                    this.level = options["level"];
                }
                if( typeof options["parent"] !== "undefined" ){
                    this.parent = options["parent"];
                }
                if( typeof options["title"] === "string" ){
                    this.title = options["title"];
                }
                if( typeof options["project"] !== "undefined" ){
                    if( typeof options["project"] === "number" ){
                        this.project = options["project"];
                    }else if( typeof options["project"] === "string" ){
                        this.project = parseInt(options["project"]);
                    }
                }
                //content settings
                if( typeof options["animations"] === "boolean" ){
                    this.animations = options["animations"];
                }
                if( typeof options["spin"] === "boolean" ){
                    this.spin = options["spin"];
                }
                //checkbox settigns
                if( typeof options["checkbox"] === "boolean" ){
                    this.checkbox = options["checkbox"];
                }
                if( typeof options["checkOnClick"] === "boolean" ){
                    this.checkOnClick = options["checkOnClick"];
                }
                if( typeof options["checkboxCallback"] === "function" ){
                    this.checkboxCallback = options["checkboxCallback"];
                }
                if( typeof options["track"] === "boolean" ){
                    this.track = options["track"];
                }
                //menu settings
                if( typeof options["menu"] === "object" ){
                    this.menu = options["menu"];
                }
                if( typeof options["menuItems"] === "object" ){
                    this.menuItems = options["menuItems"];
                }
                if( typeof options["menuCallbacks"] === "object" ){
                    this.menuCallbacks = options["menuCallbacks"];
                }
                //ajax settings
                if( typeof options["ajaxUrl"] === "string" ){
                    this.ajaxUrl = options["ajaxUrl"];
                }
                if( typeof options["ajaxType"] === "string" ){
                    this.ajaxType = options["ajaxType"];
                }
                if( typeof options["ajaxData"] !== "undefined" ){
                    this.ajaxData = options["ajaxData"];
                }
                if( typeof options["ajaxCallback"] === "function" ){
                    this.ajaxCallback = options["ajaxCallback"];
                }
            }
        }

        /**
         * PRINT STATUS
         */
        this.status = function(){
            var status = {
                title            : this.title,
                project          : this.project,
                level            : this.level,
                previousLevel    : this.previousLevel,
                parent           : this.parent,
                element          : this.element,
                containerWidth   : this.containerWidth,
                animations       : this.animations,
                spin             : this.spin,
                checkbox         : this.checkbox,
                checkOnClick     : this.checkOnClick,
                checkboxCallback : this.checkboxCallback,
                track            : this.track,
                tracked          : this.tracked,
                menu             : this.menu,
                menuItems        : this.menuItems,
                menuCallbacks    : this.menuCallbacks,
                ajaxUrl          : this.ajaxUrl,
                ajaxData         : this.ajaxData,
                ajaxType         : this.ajaxType,
                ajaxCallback     : this.ajaxCallback,
            };
            $status = status;
            console.log( status );
        };

        /**
         * ADD AN EVENT TO A LEVEL
         */
        this.addEvent = function(){
            var seft = this;

            //clean previus click events
            $("#level"+this.level+" ul li").off("click");

            $("#level"+this.level+" ul li").on("click", function(){
                seft.element = $(this);
                var li = $(this);
                var ul = li.parent();
                var td = li.parent().parent();

                ul.children("li").removeClass("selected");
                li.addClass("selected");

                //set level
                seft.level = td.attr('id').substr(5);
                seft.parent = li.attr("id");

                //if check the checkbox on click
                if( seft.checkOnClick ){
                    seft.checkboxCheck(li);
                }

                //if has menu
                if( seft.menu ){
                    seft.setMenu(li);
                }

                seft.newLevel();
            });

            if( this.toolbar ){
                $("#hztreeButtonData").off("click");
                $("#hztreeButtonData").on("click", function(){
                    seft.loadTracked();
                    seft.toggleTree();
                })
            }

            //if has checbox
            if( this.checkbox ){
                this.checkboxEvent();
            }

        };

        /**
         * ADD CHECKBOXS EVENTS
         */
        this.checkboxEvent = function(){
            var seft = this;

            //clear previus click events
            $("#level"+this.level+" ul li input[type=checkbox]").off("click");

            //click event for each checkbox
            $("#level"+this.level+" ul li input[type=checkbox]").on("click", function(){
                var checkbox = $(this);
                var level = checkbox.parent().parent().parent();
                var li = checkbox.parent();

                li.addClass('selected');

                if( checkbox.is(":checked") ){
                    seft.selectPrevius(level);
                }

                if( seft.track ){
                    if( li.hasClass('noNext') ){
                        seft.saveTrack();
                    }
                }

            });
        };

        /**
         * SELECT LEVELS PREVIUS CHECKBOX
         * @param object level
         */
        this.selectPrevius = function(level){

            var id = parseInt(level.attr("id").replace("level",""));
            id--;

            //if is a valid level
            if( id >= 0 ){
                level = $("#level"+id);

                if(level.length){
                    var li = level.children("ul").children("li.selected");

                    this.checkboxCheck(li);

                    this.selectPrevius(level);
                }

            }

        };

        /**
         * CHECK AND CHECKBOX
         * @param object li list item element
         */
        this.checkboxCheck = function(li){
            var checkbox = $(li).children("input[type=checkbox]");
            if( checkbox.length ){
                checkbox.prop('checked', true);
            }
        };

        /**
         * LOAD TRACKED
         */
        this.loadTracked = function(){
            var seft = this;

            var data = {
                func : "Incluidas",
                proyecto : this.project,
            }

            $.ajax({
                data : data,
                url  : this.ajaxUrl,
                type : this.ajaxType,
                beforeSend : function(){
                    if( this.animations ){
                        this.newSpin('hztreeData');
                    }
                },
                success : function(response){
                    $("#hztreeData").html(response);
                    seft.trackedEvent();
                }
            })
        };

        /**
         * TRACKED LIST EVENT
         */
        this.trackedEvent = function(){
            var seft = this;

            $("#hztreeData ul li").off("click");
            $("#hztreeData ul li").on("click", function(){
                var li = $(this);
                li.parent().find("li").removeClass("selected");
                li.addClass("selected");

                seft.setMenu(li, 'data');
            });
        };



        /**
         * SEND TRACKED CHECKBOXES VIA AJAX
         */
        this.saveTrack = function(){
            //get selected path
            var li = $(this).find(".selected");
            var path = [];
            var included = [];

            if( li.length ){
                for(var i = 0; i <= li.length-1; i++){
                    var id = $(li[i]).children("input[type=checkbox]").val();
                    path.push(id);
                }
                included.push(path.toString())
            }else{
                return false;
            }

            var data = {
                func           : "GuardarCategorias",
                proyecto       : this.project,
                "categorias[]" : included,
            }

            $included = included;

            $.ajax({
                url  : this.ajaxUrl,
                type : this.ajaxType,
                data : data,
                beforeSend : function(){
                    alert("guardando");
                },
                success : function(response){
                    alert(response);
                }
            });

        };

        this.saveExcluded = function(){
            var li = $("#hztreeData ul li.selected");


            if( !li.length ){
                return false;
            }
            var included = $("#hztreeData ul li.selected span:last").attr("id");

            var data = {
                func           : "ExcluirCategorias",
                proyecto       : this.project,
                "categorias[]" : included,
            }

            $included = included;

            $.ajax({
                url  : this.ajaxUrl,
                type : this.ajaxType,
                data : data,
                beforeSend : function(){
                    alert("excluyendo");
                },
                success : function(response){
                    alert(response);
                }
            });
        };

        /**
         * CREATE A NEW LEVEL
         */
        this.newLevel = function(){
            this.previousLevel = parseInt(this.level);
            //new level
            this.level++;

            //if next level already exist
            if( $("#level"+this.level).length ){
                this.removeLevel(this.level);
            }

            var newLevel = '<td id="level'+this.level+'" >' +
                           '</td>';

            $("#hztreeRow").append(newLevel);

            //ajax load the level content
            this.loadLevel();
        }

        /**
         * REMOVE ALL THE LEVES
         * @param int level to remove
         */
        this.removeLevel = function(level){
            //start inverted
            $( $("#hztreeRow td").get().reverse() ).each(function(){
                var id = $(this).attr("id");
                id = id.substr(5);

                if( level <= id ){

                    $(this).remove()
                }

            });

            this.level = level;
        };

        /**
         * LOAD LEVEL
         */
        this.loadLevel = function(){
            var seft = this;
            var element = $("#level"+this.level);

            var id = 0;

            if( this.parent > 0){
                id = this.parent;
            }

            var data = {
                func     : "Categorias",
                proyecto : this.project,
                padre    : id,
            };

            $params = data;

            $.ajax({
                data   : data,
                type   : this.ajaxType,
                url    : this.ajaxUrl,
                beforeSend: function(){
                    //has spin
                    if( seft.spin ){
                        seft.newSpin(element.attr("id"));
                    }
                },
                success: function(response){
                    //replace
                    isEmpty = response.replace(/\s/g, '');

                    if( isEmpty === ""){
                        element.html("");
                        element.addClass("nodata");
                    }else{

                        element.html(response);

                        //fix container width
                        seft.setWidth();
                        if( seft.animations ){
                            seft.animateTd(element.children());
                        }

                        //add event to new level
                        seft.addEvent();
                    }

                }
            })
        };

        /**
         * SET THE CONTAINER WIDTH
         */
        this.setWidth = function(){
            var width = $(this).children('#hztreeContainer').children("table").width();

            if( this.containerWidth < width ){
                $(this).width( width+15 );
            }else{
                $(this).width( "auto" );
            }

        };

        /**
         * SET A MENU
         * @param object li element
         */
        this.setMenu = function(li, tipo){
            var seft = this;

            if( typeof tipo === "undefined" ){
                tipo = "panel"
            }

            $li = li;
            var level = li.parent().parent().attr("id");
            var id = li.attr("id");

            var items = {};

            if( tipo === "panel" ){
                if(li.hasClass('noNext')){
                    items = {
                        "incluirCategoria": {
                            name: "Incluir Categoria",
                            icon: "edit",
                            accesskey: "S"
                        },
                        "seleccionarNormas": {
                            name: "Seleccionar Normas",
                            icon: "edit",
                            accesskey: "N"
                        }
                    }
                }else{
                    items = {
                        "incluirCategoria": {
                            name: "Incluir Categoria",
                            icon: "edit",
                            accesskey: "S"
                        },
                    }
                }
            }
            if( tipo === "data" ){
                items = {
                    "excluirCategoria": {
                        name: "Excluir Categoria",
                        icon: "edit",
                        accesskey: "E"
                    },
                }
            }

            $.contextMenu({
                selector: "#"+level+" #"+id,
                callback: function(key, options) {
                    seft.menuEvent(key, li);
                },
                items: items,
            });
        };

        /**
         * CLICK EVENT IN MENU ITEM
         * @param string key
         * @param object element
         */
        this.menuEvent = function(key, element){

            switch (key){
                case "incluirCategoria":
                    element.children("input[type=checkbox]").trigger("click");
                    break;

                case "excluirCategoria":
                    if( this.animations ){
                        element.fadeOut(700, function(){
                            element.remove();
                        });
                    }else{
                        element.remove();
                    }
                    this.saveExcluded();
                    break;

                case "seleccionarNormas":
                    var id = element.attr('id');
                    alert("seleccionar normas "+id);
                    PreviewCategoriaNormas(id);
                    break;
            }

        };

        /**
         * UPDATE THE TITLE
         * @param string title new
         */
        this.updateTitle = function(title){
            if( typeof title === "string"){
                this.title = title;
                $(this).find("#hztreeTitle").text(this.title);
            }else{
                throw new Error("typeof title invalid");
            }
        };

        /**
         * HIDE THE TREE
         */
        this.hide = function(){
            if( this.animations ){
                $(this).fadeOut(700);
            }else{
                $(this).hide();
            }
        };

        /**
         * SHOW THE TREE
         */
        this.show = function(){
            if( this.animations ){
                $(this).fadeIn(700);
            }else{
                $(this).show();
            }
        };

        /**
         * TOOGLE THE TREE AND DATA PANELS
         */
        this.toggleTree = function(){
            var table = $(this).children("#hztreeContainer");

            if( table.is(":visible") ){
                this.hideTree();
            }else{
                this.showTree();
            }
        };

        /**
         * HIDE THE TREE AND SHOW THE DATA
         */
        this.hideTree = function(){
            var table = $(this).children("#hztreeContainer");
            var data = $(this).children("#hztreeData");

            if( this.animations ){
                table.slideUp(500, function(){
                    table.hide();
                });

                data.css({
                    height  : "0px",
                    display : "block",
                    opacity : 0,
                });
                data.animate({
                    opacity      : 1,
                    "min-height" : table.height(),
                    "max-height" : table.parent().parent().height(),
                },{
                    duration: 700,
                    queue: false,
                    complete: function(){
                        //callback
                        data.css({
                            height : "auto",
                        });
                        data.show();
                    }
                });
            }else{
                table.hide();
                data.css({
                    "min-height"  : table.height(),
                    display       : "block",
                    opacity       : 1,
                });
                data.show();
            }
        };

        /**
         * HIDE THE DATA AND SHOW THE TREE
         */
        this.showTree = function(){
            var table = $(this).children("#hztreeContainer");
            var data = $(this).children("#hztreeData");

            if( this.animations ){
                table.slideDown(700, function(){
                    table.show();
                });
                data.animate({
                    opacity      : 0,
                    "min-height" : "0",
                },{
                    duration: 700,
                    queue: false,
                    complete: function(){
                        //callback
                        data.hide();
                    }
                });
            }else{
                table.show();
                data.hide();
            }
        };

        /**
         * RELOAD THE PLUGIN
         * @param object options
         */
        this.refresh = function(options){
            alert("refresh");
            this.init(options);
        }

        /******************************************
         * GETTERS
         ******************************************/

        this.getTracked = function(){
            alert("getTracked");
            return this.tracked;
        }

        /******************************************
         * ANIMATIONS
         ******************************************/

        /**
         * ANIMATE A NEW TD
         * @param object element
         */
        this.animateTd = function(ul){
            var seft = this;
            var width = ul.width();

            ul.css({
                opacity : 0,
                width   : "0px"
            })

            ul.animate({
                opacity    : 1,
                width      : width+"px",
            },{
                duration: 1000,
                queue: false,
                complete: function(){
                    //callback
                    seft.moveTo(ul.parent());
                }
            });
        };

        /**
         * MOVE THE SCROLL TO AN ELEMENT
         * @param object to
         */
        this.moveTo = function(id){
            this.setWidth();

            $(this).parent().animate(
                {
                    scrollLeft : id.offset().left,
                    scrollTop  : id.offset().top,
                },
                'slow');
        };

        /**
         * CREATE A NEW SPIN LOADER
         * @param string id
         */
        this.newSpin = function(id){
            var top = ($("#"+id).height())/2-20;
            if(top === 0){
                top = 50;
            }

            var opts = {
                lines: 13, // The number of lines to draw
                length: 5, // The length of each line
                width: 3, // The line thickness
                radius: 10, // The radius of the inner circle
                corners: 1, // Corner roundness (0..1)
                rotate: 0, // The rotation offset
                direction: 1, // 1: clockwise, -1: counterclockwise
                color: '#A1CA4A', // #rgb or #rrggbb
                speed: 1, // Rounds per second
                trail: 60, // Afterglow percentage
                shadow: false, // Whether to render a shadow
                hwaccel: true, // Whether to use hardware acceleration
                className: 'spinner', // The CSS class to assign to the spinner
                zIndex: 2e9, // The z-index (defaults to 2000000000)
                top: top+'px', // Top position relative to parent in px
                left: '10px' // Left position relative to parent in px
            };
            var target = document.getElementById(id);
            var spinner = new Spinner(opts).spin(target);
        }

        /******************************************
         * METHODS
         ******************************************/

        //ALLOWED METHODS TO CALL
        this.methods ={
            refresh     : this.refresh,
            show        : this.show,
            hide        : this.hide,
            toggleTree  : this.toggleTree,
            status      : this.status,
            updateTitle : this.updateTitle,
            set         : this.set,
            getTracked  : this.getTracked,
        };

        //methods calls
        if( this.methods[options] ){
            this.methods[options].apply( this, Array.prototype.slice.call( arguments, 1 ));
        }else{
            //auto init
            this.init(options);
        }

    };

}( jQuery ));