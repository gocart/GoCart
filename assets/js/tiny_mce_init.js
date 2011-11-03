tinyMCE.init({
	// General options
	mode : "textareas",
	theme : "advanced",
	editor_selector : "tinyMCE",
	skin : "o2k7",
	skin_variant : "silver",
	dialog_type : "modal",
	plugins : "inlinepopups,table,save,advhr,advimage,advlink,searchreplace,paste,directionality,media",

	// Theme options
	theme_advanced_buttons1 : "bold,italic,underline,|,justifyleft,justifycenter,justifyright,|,|,tablecontrols",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword ,search,replace,|,bullist,numlist,outdent,indent,blockquote,|,link,unlink,anchor,image,media,cleanup",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "center",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	theme_advanced_resize_horizontal : false,
	width : "97%"
});

function toggleEditor(id) {
	if (!tinyMCE.get(id))
		tinyMCE.execCommand('mceAddControl', false, id);
	else
		tinyMCE.execCommand('mceRemoveControl', false, id);
}