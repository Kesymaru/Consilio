/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	config.language = 'es';
	config.uiColor = '#FFFFFF';
	config.toolbarStartupExpanded = false;
	config.toolbarCanCollapse = true;
	config.toolbarGroupCycling = true;
	config.disableNativeSpellChecker = true;
	
	config.toolbarGroups = [
			{ name: 'spellchecker'},
			{ name: 'document', groups: [ 'undo', 'document', 'find', 'spellchecker', 'links', 'insert' ] },
			{ name: 'editing', groups: [ 'basicstyles', 'align', 'indent', 'list', 'blocks', 'colors' ] },

			{ name: 'insert', groups: [ 'styles', 'tools' ] },
	];

	config.removeButtons = 'CreateDiv,Cut,Copy,Paste,Form,Flash,Save,New_Page,Anchor,Image,About,Source,Iframe,ShowBlocks,Checkbox,Radio,TextField,Textarea,SelectionField,SelectAll,Button,ImageButton,HiddenField';

};
