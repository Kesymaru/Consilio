/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	//config.language = 'es';
	config.uiColor = '#ffffff';
	//config.toolbarCanCollapse = true;
	
	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
		//{ name: 'doctools', groups: [ 'document' ] },
		{ name: 'document'},
		{ name: 'undo', },
		{ name: 'editing'},
		{ name: 'basicstyles'},
		{ name: 'colors' },
		{ name: 'insert' },
		{ name: 'links' },
		//'/',
		{ name: 'paragraph'},
		{ name: 'styles' },
		//{ name: 'others' },
		{ name: 'tools' },
	];

	config.removeButtons = 'Cut,Copy,Paste,Form,Flash,Find,Replace,,Save,New_Page,Anchor,Image,About,Source,Iframe,ShowBlocks,Checkbox,Radio,TextField,Textarea,SelectionField,select,SelectAll,Button,ImageButton,HiddenField';

};
