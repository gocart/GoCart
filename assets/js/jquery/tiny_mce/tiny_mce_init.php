<?php header("Content-type: text/javascript");?>
tinyMCE.init({
	// General options
	mode : "textareas",
	theme : "advanced",
	editor_selector : "tinyMCE",
	skin : "gc",
	dialog_type : "modal",
	plugins : "Archiv,inlinepopups,table,save,advhr,advimage,advlink,searchreplace,paste,directionality,media",
	Archiv_settings_file : "<?php echo $_SERVER['DOCUMENT_ROOT'];?>/js/jquery/tiny_mce/plugins/Archiv/config.php",
	inlinepopups_skin : 'gocart',
	// Theme options
	theme_advanced_buttons1 : "bold,italic,underline,|,justifyleft,justifycenter,justifyright,|,|,tablecontrols,|,Archiv_images,Archiv_files",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword ,search,replace,|,bullist,numlist,outdent,indent,blockquote,|,link,unlink,anchor,image,media,cleanup",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "center",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	theme_advanced_resize_horizontal : false,
	width : "100%"
});

function toggleEditor(id) {
	if (!tinyMCE.get(id))
		tinyMCE.execCommand('mceAddControl', false, id);
	else
		tinyMCE.execCommand('mceRemoveControl', false, id);
}