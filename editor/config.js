/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	config.language = 'es';
	config.uiColor = '#f3efe6';
	
	config.toolbarGroups = [
		{ name: 'undo'},
		{ name: 'editing',     groups: [ 'find', 'selection' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'forms' },
		{ name: 'tools' },
		{ name: 'others' },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
	];

	config.removeButtons = 'Maximize,Table,Cut,Copy,Paste,Form,Flash,Find,Replace,Save,New_Page,Anchor,Image,About,Source,Iframe,ShowBlocks,Checkbox,Radio,TextField,Textarea,SelectionField,SelectAll,Button,ImageButton,HiddenField';

};

CKEDITOR.on( 'dialogDefinition', function( ev ) {
    // Take the dialog name and its definition from the event data.
    var dialogName = ev.data.name;
    var dialogDefinition = ev.data.definition;

    // Check if the definition is from the dialog window you are interested in (the "Link" dialog window).
    if ( dialogName == 'link' ) {

    	notifica("Agregar un link");

        // Get a reference to the "Link Info" tab.
        var infoTab = dialogDefinition.getContents( 'info' );

        // Set the default value for the URL field.
        var urlField = infoTab.get( 'url' );
        urlField[ 'default' ] = 'www.escala.com';
    }
});

