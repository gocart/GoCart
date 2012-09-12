
/*
GoEdit
version 1.0
Built for GoCart by Noah Mormino
*/

var goedit_toolbar	= $('<div class="goedit-toolbar"></div>');
var goedit_selected	= false;
var goedit			= new Object();
goedit.sel			= false;
goedit.range		= false;
goedit.up			= false;

var toolbar_rows	={	0:{
							0:{
								 0:{type:'command', command:'bold', btn_icon:'sprite-text-bold', btn_text:false}
								,1:{type:'command', command:'italic', btn_icon:'sprite-text-italic', btn_text:false}
								,2:{type:'command', command:'underline', btn_icon:'sprite-text-underline', btn_text:false}
							}
							,1:{
								 0:{type:'command', command:'justifyleft', btn_icon:'sprite-text-align-left', btn_text:false}
								,1:{type:'command', command:'justifycenter', btn_icon:'sprite-text-align-center', btn_text:false}
								,2:{type:'command', command:'justifyright', btn_icon:'sprite-text-align-right', btn_text:false}
								,3:{type:'command', command:'justifyfull', btn_icon:'sprite-text-align-justify', btn_text:false}
							}
							,2:{
								 0:{type:'onclick', command:'goedit_anchor', btn_icon:'sprite-link', btn_text:false}
								,1:{type:'command', command:'unlink', btn_icon:'sprite-link-break', btn_text:false}
							}
							,3:{
								 0:{type:'command', command:'insertorderedlist', btn_icon:'sprite-text-list-numbers', btn_text:false}
								,1:{type:'command', command:'insertunorderedlist', btn_icon:'sprite-text-list-bullets', btn_text:false}
							}
							,4:{
								 0:{type:'onclick', command:'goedit_media_manager', btn_icon:'sprite-picture', btn_text:false}
							}
						}
						/* Additional rows could go here */
						/*
						1: {
							0: {
								0:{type:'command', command:'bold', btn_icon:'sprite-text-bold', btn_text:false}
								,1:{type:'command', command:'italic', btn_icon:'sprite-text-italic', btn_text:false}
								,2:{type:'command', command:'underline', btn_icon:'sprite-text-underline', btn_text:false}
							}
						}
						*/
					}

function goedit_reset_tracking()
{
	goedit_selected	= false;
	goedit			= new Object();
	goedit.sel		= false;
	goedit.range	= false;
	goedit.up		= false;
}

$.each(toolbar_rows, function(index, groups){
	
	//toolbar rows
	var toolbar	= $('<div class="btn-toolbar"></div>');

	//btn-groups
	$.each(groups, function(index, btns){
		var toolbar_group	= $('<div class="btn-group"></div>');
		
		//btns
		$.each(btns, function(index, btn){
			var button	= $('<button />');
			button.attr('type', 'button');
			button.addClass('btn btn-mini');
			//add an icon
			if(btn.btn_icon)
			{
				button.append('<i class="'+btn.btn_icon+'"></i>');
			}
			//add text (if needed)
			if(btn.btn_text)
			{
				button.append(btn.btn_text);
			}
			//add a call to execCommand (via run_command) or add an onclick to an alternate function
			if(btn.type == 'command')
			{
				button.click(function(){
					run_command(btn.command, null, false, $(this).closest('.goedit_wrap').attr('rel'));
				});
			}
			else if (btn.type == 'onclick')
			{
				button.click(function(){
					eval(btn.command+'()');
				});
			}
			toolbar_group.append(button);
		});
		toolbar.append(toolbar_group);
	});
	
	//append the toolbar row to the global goedit_toolbar variable
	goedit_toolbar.append(toolbar);
});

function goedit_toggle(id)
{
	$('#goedit_wrap_'+id).children('div').toggle();
	$('.goedit_'+id).toggle();
	goedit_reset_tracking();
}

function run_command(cmd, intfc, val, editor)
{
	document.execCommand(cmd, intfc, val)
	
	if(editor != undefined)
	{
		$('.goedit_'+editor).val($('#goedit_'+editor).html());
	}
	goedit_reset_tracking();
}

function goedit_media_attributes()
{
	goedit_modal(goedit_media_attributes_url, false, false);
}

function goedit_media_manager()
{
	goedit_modal(goedit_media_manager_url, true);
}

function goedit_insert_link()
{
	var url		= $('#goedit-form-anchor-url').val();
	var target	= $('#goedit-form-anchor-target').val();
	var cls		= $('#goedit-form-anchor-class').val();
	
	if(goedit_selected)
	{
		goedit_selected.attr('href', url);
		goedit_selected.attr('class', cls);
		goedit_selected.attr('target', target);
	}
	else
	{
		var link	= document.createElement('a');
		link.appendChild(document.createTextNode(goedit.range));
		link.setAttribute('href', url);
		link.setAttribute('target', target);
		if(cls != '')
		{
			link.setAttribute('class', cls);
		}

		goedit.range.deleteContents();
		goedit.range.insertNode(link);
	}
	
	
	goedit_close_modal();
}

function goedit_anchor()
{
	goedit.sel		= document.getSelection();
	goedit.range	= goedit.sel.getRangeAt(0);
	goedit_modal(goedit_create_anchor_form, false, true);
}

var goedit_create_anchor_form = false;
//onload generate the goedit editors
$(document).ready(function(){
	
	goedit_create_anchor_form	= $('#goedit-create-anchor').html();
	$('#goedit-create-anchor').remove()
	//kill the form
	$('#goedit-create-anchor').html();
	
	//loop through textareas that have the goedit 
	var goeditid	= 0;
	$('.goedit').each(function(){
		var wrapper	= $('<div/>');
		wrapper.attr('id', 'goedit_wrap_'+goeditid);
		wrapper.attr('rel', goeditid);
		wrapper.addClass('goedit_wrap');
		wrapper.append(goedit_toolbar);
		
		var editor	= $('<div></div>');
		editor.attr({
			 'contentEditable':true
			,'class':'goedit'
			,'id':'goedit_'+goeditid
			,'rel':goeditid
		});
		
		$(this).addClass('goedit_'+goeditid).removeClass('goedit').attr('rel', goeditid);
		wrapper.append(editor);
		wrapper.append('<button type="button" class="btn btn-block" onclick="goedit_toggle('+goeditid+')">'+goedit_language_toggle_editor+'</button>');

		$('.goedit_'+goeditid).after(wrapper).hide();
		
		$('#goedit_'+goeditid).html($('.goedit_'+goeditid).val()).bind('keyup mouseover mouseout', function(){
			
			$('.goedit_'+$(this).attr('rel')).val($(this).html());
			
		});

		$('.goedit_'+goeditid).bind('keyup mouseover mouseout', function(){
			$('#goedit_'+$(this).attr('rel')).html($(this).val());
		});
		
		goeditid++;
	});
	document.execCommand("enableObjectResizing", false, false);
	
	$('.goedit').on("click", "img", function () {
		goedit_selected	= $(this);
		$('<button class="btn btn-mini"><i class="icon-pencil"></i></button>').css({position:'absolute', top:$(this).position().top+5, left:$(this).position().left+5}).appendTo('body').click(function(){
			goedit_modal(goedit_media_attributes_url, false, false);
		}).delay(3000).fadeOut(	500, function(){
			$(this).remove();
		});
	}).on('click', "a", function () {
		goedit_selected	= $(this);
		goedit_modal(goedit_create_anchor_form, false, true);
		$('#goedit-form-anchor-url').val(goedit_selected.attr('href'));
		$('#goedit-form-anchor-target').val(goedit_selected.attr('target'));
		$('#goedit-form-anchor-class').val(goedit_selected.attr('class'));
		
	});
});

function goedit_modal(src, iframe, html)
{
	var background	= $('<div/>').attr('id', 'goedit-modal-bg').click(function(){
		goedit_close_modal();
	});
	
	$('html').css({height:'100%', width:'100%'});
	$('body').css({height:'100%', width:'100%', overflow:'hidden'}).append(background);
	
	var modal	= $('<div/>').attr('id', 'goedit-modal');
	
	//create the close button
	var close_button	= $('<a href="#" id="goedit-modal-close" onclick="goedit_close_modal(); return false;" class="goedit-close">&times;</a>');
	
	if(iframe)
	{
		//remote iframe
		
		var iframe	= $('<iframe/>');
		iframe.attr('src', src);
		//iframe wrapper
		var iframe_wrapper	= $('<div class="goedit-content-container"></div>').append(iframe);
		modal.append(iframe_wrapper);
	}
	else if(html)
	{
		//needs HTML plugged to it
		var html_content	= $('<div class="goedit-content-container"></div>').html(src)
		modal.append(html_content);
	}
	else
	{
		//use ajax
		var ajax_content	= $('<div class="goedit-content-container"></div>');
		ajax_content.load(src)
		modal.append(ajax_content);
	}
	
	$('body').append(modal);
	$('body').append(close_button);
}

function goedit_close_modal()
{
	$('#goedit-modal').fadeOut(300, function(){
		$(this).remove();
	});
	$('#goedit-modal-bg').fadeOut(300, function(){
		$(this).remove();
	});
	$('#goedit-modal-close').fadeOut(300, function(){
		$(this).remove();
	});
	$('body').css({height:'auto', width:'auto', overflow:'auto'});
	$('html').css({height:'auto', width:'auto'});

	//reset the selected elements
	goedit_reset_tracking();
}
