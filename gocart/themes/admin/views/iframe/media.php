<?php include('header.php');?>

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

 	#upload-field {
		 -moz-opacity:0;
		 opacity:0;
		position:absolute;
		width:100%;
		padding:10px;
		top:0px;
		left:0px;
		margin-top:0px;
		z-index:999;
	}
	.upload-file-container {
		position:relative;
		overflow:hidden;
		margin:0px;
	}
	#upload-loader {
		float:right;
		margin-top:15px;
		margin-right:5px;
		display:none;
	}
	.navbar-inner {
		padding-right:10px;
		padding-left:10px;
	}
	.container-fluid {
		padding:0px;
	}
	body {padding:0px}
</style>
<div class="navbar navbar-inverse">
	<div class="navbar-inner">
		<div class="container-fluid">
			<ul class="nav">
				<?php
				if(!empty($root))
				{
					echo '<li class="droppable" title=""><a href="'.site_url(config_item('admin_folder').'/media').'"><i class="icon-home icon-white"></i> '.lang('goedit_root').'</a></li>';
					$back_link	= explode('/', $root);
					array_pop($back_link);
					$path	= '';
					foreach($back_link as $bl): $path.= $bl.'/';?>
					<li><a>/</a></li>
					<li class="droppable" title="<?php echo $path;?>"><a href="<?php echo site_url(config_item('admin_folder').'/media/index/'.$path);?>"><?php echo $bl;?></a></li>
					<?php endforeach;
				}
				?>
			</ul>
			<?php echo form_open(config_item('admin_folder').'/media/create_subfolder/', 'class="navbar-form pull-right"');?>
				<input type="hidden" name="root" value="<?php echo $root;?>">
				<div class="input-append">
					<input type="text" name="folder-name" class="input-small" placeholder="<?php echo lang('goedit_folder_name');?>">
					<button type="submit" class="btn">+<i class="icon-folder-close"></i></button>
				</div>
			</form>

			<?php echo form_open_multipart(site_url(config_item('admin_folder').'/media/upload'), 'id="upload-form" class="form-inline navbar-form pull-right" style="margin-right:5px;"');?>
				<input type="hidden" name="root" value="<?php echo $root;?>">
				<div class="upload-file-container">
					<input type="file" name="userfile" size="20" id="upload-field"/>
					<button type="button" class="btn"><?php echo lang('goedit_upload');?></button>
				</div>
			</form>
			<img src="<?php echo base_url('assets/img/media-loader.gif');?>" alt="<?php echo lang('goedit_loading');?>" id="upload-loader"/>
		</div>
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

	<div class="span12" style="text-align:center;">
		<?php

		$image_extensions	= array('jpg', 'jpeg', 'gif', 'png');

		foreach($files as $f):?>
			<?php
			$uri_root	= $root;
			if(!empty($root))
			{
				$uri_root	.= '/';
			}
			?>
			<div class="span3 draggable" style="overflow:hidden;" title="<?php echo $f;?>">

			<?php
			if(is_dir($this->path.'/'.$root.'/'.$f)):?>
					<div class="img-thumbnail droppable" title="<?php echo $uri_root.$f;?>">
						<img src="<?php echo base_url('assets/img/media-loader.gif');?>" style="margin:37px auto 0px auto; display:none;">
					</div>
					<div class="btn-group" style="margin-top:5px;">
						<a href="<?php echo site_url(config_item('admin_folder').'/media/index/'.$uri_root.$f);?>" class="btn btn-mini"><?php echo $f;?></a>
						<button class="btn btn-mini" onclick="rename('<?php echo $f;?>');"><i class="icon-pencil"></i></button>
					</div>

				<?php elseif(in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), $image_extensions)):?>
					<a href="<?php echo site_url(config_item('admin_folder').'/media/embed/'.$uri_root.$f);?>"><img class="img-thumbnail" src="<?php echo base_url('/uploads/wysiwyg/'.$uri_root.$f);?>" alt="<?php echo htmlentities($f);?>" /></a>
					<div class="btn-group" style="margin-top:5px;">
						<a href="<?php echo site_url(config_item('admin_folder').'/media/embed/'.$uri_root.$f);?>" class="btn btn-mini"><?php echo $f;?></a>
						<button class="btn btn-mini" onclick="rename('<?php echo $f;?>');"><i class="icon-pencil"></i></button>
					</div>
				<?php else:?>
					<a href="<?php echo site_url(config_item('admin_folder').'/media/embed/'.$uri_root.$f);?>" ><img class="img-thumbnail" src="<?php echo base_url('assets/img/file.png');?>" /></a>
					<div class="btn-group" style="margin-top:5px;">
						<a href="<?php echo site_url(config_item('admin_folder').'/media/index/'.$root.'/'.$f);?>" class="btn btn-mini"><?php echo $f;?></a>
						<button class="btn btn-mini" onclick="rename('<?php echo $f;?>');"><i class="icon-pencil"></i></button>
					</div>
				<?php endif;?>
			</div>
		<?php endforeach;?>
	</div>

</div>

<div class="modal" id="folder-name" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display:none;">

	<div class="modal-body">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<?php echo form_open(config_item('admin_folder').'/media/rename_file/', 'class="form-inline"');?>
			<div class="container-fluid">
				<div class="row-fluid">
					<input type="hidden" name="root" value="<?php echo $root;?>" >
					<input id="original-filename" name="original" type="hidden">
					<input id="new-filename" name="new" type="text" class="span12">
				</div>
				<div class="row-fluid" style="margin-top:10px;">
					<button type="submit" class="btn btn-large btn-block"><?php echo lang('goedit_rename');?></button>
				</div>
			</div>
		</form>
		<?php echo form_open(config_item('admin_folder').'/media/delete/');?>
			<div class="container-fluid">
				<div class="row-fluid">
					<div class="span12" style="text-align:center;">

						<div id="delete-warning" style="text-align:center; display:none; padding-top:10px; margin-bottom:10px;">
							<div class="alert alert-block">
								<h4><?php echo lang('goedit_warning');?></h4>
								<?php echo lang('goedit_warning_text');?>
							</div>
							<button type="submit" class="btn btn-danger btn-large btn-block"><?php echo lang('goedit_delete_button');?></button>
						</div>

						<input type="hidden" name="root" value="<?php echo $root;?>">
						<input id="delete-filename" name="filename" type="hidden">
						<button type="button" class="btn btn-inverse" onclick="$('#delete-warning').show(); $(this).hide();"> <i class="icon-trash icon-white"></i></button>
					</div>
				</div>
			</div>
		</form>
	</div>

</div>

<script type="text/javascript">
	/* new folder action */
	function rename(filename)
	{
		$('#original-filename').val(filename);
		$('#new-filename').val(filename);
		$('#delete-filename').val(filename);
		$('#folder-name').modal();
	}

	$('#upload-form').on('change', '#upload-field', function(){
		$('#upload-loader').show();
		$('#upload-form').submit();
	});

	$('#upload-field').change(function(){
		$('#upload-form')[0].submit().reset();
	});

	$( ".draggable" ).draggable({ delay:200, revert: true, handle:'.img-thumbnail', zIndex:9999, opacity:.50 });
	$( ".droppable" ).droppable({	hoverClass:'img-thumbnail-hover',
									drop: function(event, ui) {

										var t = $(this).children('img');
										t.show();
										$.post(	'<?php echo site_url(config_item('admin_folder').'/media/move_file');?>',
												{	filename: ui.draggable.attr('title'),
													move_to: $(this).attr('title'),
													root: '<?php echo $root;?>'
												},
												function(data){
													if(data)
													{
														ui.draggable.remove();
													}
													else
													{
														ui.draggable.draggable( "option", "revert", true )
													}
													t.hide();
												}, 'json' );
									}
								});
</script>
