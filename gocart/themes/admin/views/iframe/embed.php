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
		padding:0px;
	}
	
	#image {
		max-height:300px;
		max-width:500px;
	}
	.navbar-inner {
		padding-right:10px;
		padding-left:10px;
	}
	.container-fluid {
		padding:0px;
	}
	
</style>
<div class="navbar navbar-inverse">
	<div class="navbar-inner">
		<ul class="nav">
			<?php
			echo '<li class="droppable" title=""><a href="'.site_url(config_item('admin_folder').'/media').'"><i class="icon-home icon-white"></i> '.lang('goedit_root').'</a></li>';
			if(!empty($root))
			{
				$path	= '';
				foreach($root as $bl): $path.= $bl.'/';?>
				<li><a>/</a></li>
				<li class="droppable" title="<?php echo $path;?>"><a href="<?php echo site_url(config_item('admin_folder').'/media/index/'.$path);?>"><?php echo $bl;?></a></li>
				<?php endforeach;
			}
			?>
		</ul>
	</div>
</div>

<?php
//lets have the flashdata overright "$message" if it exists
if($this->session->flashdata('message'))
{
	$message	= $this->session->flashdata('message');
}

if($this->session->flashdata('error'))
{
	$error	= $this->session->flashdata('error');
}

if(function_exists('validation_errors') && validation_errors() != '')
{
	$error	= validation_errors();
}
?>
<?php if (!empty($message)): ?>
	<div class="alert alert-success">
		<a class="close" data-dismiss="alert">×</a>
		<?php echo $message; ?>
	</div>
<?php endif; ?>

<?php if (!empty($error)): ?>
	<div class="alert alert-error">
		<a class="close" data-dismiss="alert">×</a>
		<?php echo $error; ?>
	</div>
<?php endif; ?>

<div class="row-fluid">
	
	<div class="span6">
		<img id="image" src="<?php echo base_url('uploads/wysiwyg/'.$file);?>" />
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

			<button type="button" class="btn btn-primary btn-large btn-block" onclick="insert_image();"><?php echo lang('goedit_insert_image');?></button>
		</fieldset>
	</div>

</div>

<script type="text/javascript">

	//get the relative URL
	<?php $img	= str_replace('http://'.$_SERVER['HTTP_HOST'], '', base_url('uploads/wysiwyg/'.$file));?>
	
	function insert_image()
	{
		var img			= new Image();
		img.src			= '<?php echo $img;?>';
	
		var div			= document.createElement('div');
		
		var alt			= $('#goedit-img-alt').val();
		var cls			= $('#goedit-img-cls').val();
		var title		= $('#goedit-img-title').val();
		var width		= $('#goedit-img-width').val();
		var height		= $('#goedit-img-height').val();;
		
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
		
		parent.run_command('insertHTML', false, div.innerHTML);
		
		//copy over to the textarea
		parent.$('#description').val(parent.$('#editable').html());
		
		//close the lightbox
		parent.goedit_close_modal();
	}
</script>