/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	//config.language = 'es';
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
