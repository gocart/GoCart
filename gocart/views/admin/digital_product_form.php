<?php include('header.php');

echo form_open_multipart($this->config->item('admin_folder').'/digital_products/form/'.$id); ?>

	<?php if($id==0) : ?>
		<label for="file"><?php echo lang('file_label');?> </label>
		<?php 	$data = array('id'=>'file', 'name'=>'userfile');
				echo form_upload($data);
		?>
	<?php else : ?>
		<label for="file"><?php echo lang('file_label');?> </label>
		<?php echo $filename ?>
	<?php endif; ?>
	
	<label for="title"><?php echo lang('title');?> </label>
	<?php
	$data	= array('id'=>'title', 'name'=>'title', 'value'=>set_value('title', $title), 'class'=>'gc_tf1');
	echo form_input($data);
	?>

	<label for="title"><?php echo lang('max_downloads');?> </label>
	<?php
	$data	= array('id'=>'max_downloads', 'name'=>'max_downloads', 'value'=>set_value('max_downloads', $max_downloads), 'class'=>'gc_tf1');
	echo form_input($data);
	?>
	<span class="help-inline"><?php echo lang('max_downloads_note');?></span>
	
	<div class="form-actions">
		<input class="btn btn-primary" type="submit" value="<?php echo lang('save');?>"/>
	</div>

</form>

<?php include('footer.php');