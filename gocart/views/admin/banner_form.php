<?php include('header.php'); ?>

<?php

$title			= array('name'=>'title', 'value' => set_value('title', $title));
$enable_on		= array('name'=>'enable_on', 'id'=>'enable_on', 'value'=>set_value('enable_on', set_value('enable_on', $enable_on)));
$disable_on		= array('name'=>'disable_on', 'id'=>'disable_on', 'value'=>set_value('disable_on', set_value('disable_on', $disable_on)));
$f_image		= array('name'=>'image', 'id'=>'image');
$link			= array('name'=>'link', 'value' => set_value('link', $link));	
$new_window		= array('name'=>'new_window', 'value'=>1, 'checked'=>set_checkbox('new_window', 1, $new_window));
?>

<?php echo form_open_multipart($this->config->item('admin_folder').'/banners/form/'.$id); ?>
	<label for="title"><?php echo lang('title');?> </label>
	<?php echo form_input($title); ?>

	<label for="link"><?php echo lang('link');?> </label>
	<?php echo form_input($link); ?>

	<label for="enable_on"><?php echo lang('enable_on');?> </label>
	<?php echo form_input($enable_on); ?>

	<label for="disable_on"><?php echo lang('disable_on');?> </label>
	<?php echo form_input($disable_on); ?>

	<label class="checkbox">
	    <?php echo form_checkbox($new_window); ?> <?php echo lang('new_window');?>
	</label>

	<label for="image"><?php echo lang('image');?> </label>
	<?php echo form_upload($f_image); ?>

	<?php if($id && $image != ''):?>
	<div style="text-align:center; padding:5px; border:1px solid #ccc;"><img src="<?php echo base_url('uploads/'.$image);?>" alt="current"/><br/><?php echo lang('current_file');?></div>
	<?php endif;?>

	<div class="form-actions">
		<input class="btn btn-primary" type="submit" value="<?php echo lang('save');?>"/>
	</div>
</form>
<script type="text/javascript">
	$(document).ready(function() {
		$("#enable_on").datepicker({ dateFormat: 'mm-dd-yy'});
		$("#disable_on").datepicker({ dateFormat: 'mm-dd-yy'});
	});
	
	$('form').submit(function() {
		$('.btn').attr('disabled', true).addClass('disabled');
	});
</script>
<?php include('footer.php'); ?>