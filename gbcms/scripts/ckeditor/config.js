/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.toolbar = [
		[ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ],
		[ 'Find', 'Replace', '-', 'SelectAll' ],
		[ 'Link', 'Unlink', 'Anchor' ],
		[ 'HorizontalRule', 'SpecialChar', 'PageBreak' ],
		[ 'Image', 'Table' ],			
		[ 'Maximize','ShowBlocks' ],
		[ 'Source' ],
		'/',
		[ 'Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat' ],
		[ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent' ],		
		[ 'Format', 'FontSize' ],
		[ 'TextColor', 'BGColor' ],	
		[ 'About' ]
	];	
	config.disableNativeSpellChecker = false;
	config.coreStyles_bold = { element : 'b', overrides : 'strong' };
	config.pasteFromWordRemoveFontStyles=false;
	config.pasteFromWordRemoveStyles=false;
};
