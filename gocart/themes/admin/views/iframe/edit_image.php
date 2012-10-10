<style type="text/css">
	.img-thumbnail {
		height:64px;
	}
	
	div.img-thumbnail {
		width:64px;
		height:64px;
		display:block;
		margin:auto;
		background-image:url('<?php echo base_url('assets/img/folder.png');?>');
	}
	div.img-thumbnail-hover {
		background-image:url('<?php echo base_url('assets/img/folder-hover.png');?>');
	}
	.navbar .nav li.img-thumbnail-hover a,li.img-thumbnail-hover a:hover{
		color:#2470b7;
	}
	body {
		margin-top:50px;
	}
	
	#goedit-embed-image {
		max-height:300px;
		max-width:500px;
	}
	
</style>
<div class="row-fluid">
	
	<div class="span6">
		<img id="goedit-embed-image" src="" />
	</div>
	<div class="span6">
		<fieldset>
			<legend><?php echo lang('goedit_image_details');?></legend>
			<label><?php echo lang('goedit_alt');?></label>
			<input type="text" id="img_alt" value="" class="span12"/>
			<label><?php echo lang('goedit_title');?></label>
			<input type="text" id="img_title" value="" class="span12"/>
			<label><?php echo lang('goedit_class');?></label>
			<input type="text" id="img_cls" value="" class="span12"/>
			<div class="row-fluid">
				<div class="span6">
					<label><?php echo lang('goedit_width');?></label>
					<input type="text" id="img_width" value="" class="span12"/>
				</div>
				<div class="span6">
					<label><?php echo lang('goedit_height');?></label>
					<input type="text" id="img_height" value="" class="span12"/>
				</div>
			</div>
			<button type="button" class="btn btn-primary btn-large btn-block" onclick="goedit_update_image();"><?php echo lang('goedit_update_image');?></button>
		</fieldset>
	</div>

</div>

<script type="text/javascript">

	//get the relative URL
	$('#goedit-embed-image').attr('src', goedit_selected.attr('src'));
	$('#goedit-img-alt').val(goedit_selected.attr('alt'));
	$('#goedit-img-title').val(goedit_selected.attr('title'));
	$('#goedit-img-cls').val(goedit_selected.attr('class'));
	$('#goedit-img-width').val(goedit_selected.css('width'));
	$('#goedit-img-height').val(goedit_selected.css('height'));
	
	function goedit_update_image()
	{
		var img			= new Image();
		img.src			= goedit_selected.attr('src');
		
		var div			= document.createElement('div');
		
		var alt			= $('#goedit-img-alt').val();
		var cls			= $('#goedit-img-cls').val();
		var title		= $('#goedit-img-title').val();
		var width		= $('#goedit-img-width').val();
		var height		= $('#goedit-img-height').val();
				
		if(alt != '')
		{
			img.alt			= alt;
		}
		if(title != '')
		{
			img.title		= title;
		}
		if(cls != '')
		{
			img.className	= cls;
		}
		if(height != '')
		{
			img.style.height	= height;
		}
		if(width != '')
		{
			img.style.width	= width;
		}
		
		div.appendChild(img);
		
		goedit_selected.replaceWith(div.innerHTML);
		//set goedit_selected to false
		goedit_selected	= false;
		
		//update the textarea to match
		$('#description').val($('#editable').html());
		
		goedit_close_modal();
	}
</script>