if (typeof RedactorPlugins === 'undefined') var RedactorPlugins = {};

var redactor_instance;

RedactorPlugins.fileBrowser = {

	init: function()
	{
		this.addBtnAfter('link', 'file-browser', 'File Browser', function(obj)
		{
			redactor_instance	= obj;
			obj.modalInit('File Browser', '<iframe src="/admin/media" style="width:780px;padding:10px; height:400px; border:0px; background:transparent;"></iframe>', 800);
		});
		
		this.addBtnSeparatorBefore('advanced');
	}

}