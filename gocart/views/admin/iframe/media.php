<?php include('header.php');?>

<style type="text/css">
	tr.ui-draggable-dragging {display: block !important}
	.file, .img {
		cursor:pointer;
	}
	.file-icon {
		width:32px;
	}
	.file-icon img {
		max-height:32px;
		max-width:32px;
	}
	.btns {
		text-align:right !important;
	}
	
	.btns i {
		cursor:pointer;
	}
	
	.img-thumbnail {
		height:64px;
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
	td {
		vertical-align:middle !important;
	}
	.modal {
		top:20px;
		margin-top:0px;
	}
</style>
<div class="navbar navbar-inverse">
	<div class="navbar-inner">
		<div class="container-fluid">
			<ul class="nav">
				<?php
				if(!empty($root))
				{
					echo '<li><a href="'.site_url(config_item('admin_folder').'/media').'"><i class="icon-home icon-white"></i> '.lang('goedit_root').'</a></li>';
					$back_link	= explode('/', $root);
					array_pop($back_link);
					$path	= '';
					foreach($back_link as $bl): $path.= $bl.'/';?>
					<li><a>/</a></li>
					<li title="<?php echo $path;?>"><a href="<?php echo site_url(config_item('admin_folder').'/media/index/'.$path);?>"><?php echo $bl;?></a></li>
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

<table class="table">
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
		<tr>
		<?php
		if(is_dir($this->path.'/'.$root.'/'.$f)):?>
				<td class="file-icon" title="<?php echo $uri_root.$f;?>">
					<a href="<?php echo site_url(config_item('admin_folder').'/media/index/'.$uri_root.$f);?>"><img src="<?php echo base_url('/assets/img/folder.png');?>" alt="<?php echo htmlentities($f);?>"></a>
				</td>
				<td><a href="<?php echo site_url(config_item('admin_folder').'/media/index/'.$uri_root.$f);?>"><?php echo $f;?></a></td>
				<td class="btns">
					<i onclick="rename('<?php echo $f;?>');" class="icon-pencil"></i>
				</td>

			<?php elseif(in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), $image_extensions)):?>
				<td class="file-icon img">
				<img src="<?php echo base_url('/uploads/wysiwyg/'.$uri_root.$f);?>" alt="<?php echo htmlentities($f);?>">
				</td>
				<td><?php echo $f;?></td>
				<td class="btns">
					<i onclick="rename('<?php echo $f;?>');" class="icon-pencil"></i>
				</td>
			<?php else:?>
				<td class="file-icon file">
					<img class="img-thumbnail" src="<?php echo base_url('assets/img/file.png');?>" onclick="insert_link('<?php echo $f;?>')">
				</td>
				<td><?php echo $f;?></td>
				<td class="btns">
					<i onclick="rename('<?php echo $f;?>');" class="icon-pencil"></i>
				</td>
			<?php endif;?>
		</tr>
	<?php endforeach;?>
</table>

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
						<button id="delbtn" type="button" class="btn btn-inverse" onclick="$('#delete-warning').show(); $('#delbtn').hide(); setTimeout('$(\'#delete-warning\').hide(); $(\'#delbtn\').show();', 5000);"> <i class="icon-trash icon-white"></i></button>
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
	
	$('.img').click(function(){
		parent.redactor_instance.insertHtml($(this).html().trim());
		parent.redactor_instance.modalClose();
	});
	
	function insert_link(filename)
	{
		parent.redactor_instance.insertHtml(' <a href="<?php echo $this->path.'/'.$root;?>'+filename+'">'+filename+'</a> ');
		parent.redactor_instance.modalClose();
	}
</script>
<?php include('footer.php');