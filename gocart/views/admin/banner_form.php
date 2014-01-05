<?php
$name			= array('name'=>'name', 'value' => set_value('name', $name));
$enable_date	= array('name'=>'enable_date', 'id'=>'enable_date', 'value'=>set_value('enable_on', set_value('enable_date', $enable_date)));
$disable_date	= array('name'=>'disable_date', 'id'=>'disable_date', 'value'=>set_value('disable_on', set_value('disable_date', $disable_date)));
$f_image		= array('name'=>'image', 'id'=>'image');
$link			= array('name'=>'link', 'value' => set_value('link', $link));	
$new_window		= array('name'=>'new_window', 'value'=>1, 'checked'=>set_checkbox('new_window', 1, $new_window));
?>

<?php echo form_open_multipart(config_item('admin_folder').'/banners/banner_form/'.$banner_collection_id.'/'.$banner_id); ?>
	<label for="name"><?php echo lang('name');?> </label>
	<?php echo form_input($name); ?>

	<label for="link"><?php echo lang('link');?> </label>
	<?php echo form_input($link); ?>

	<label for="enable_date"><?php echo lang('enable_date');?> </label>
	<?php echo form_input($enable_date); ?>

	<label for="disable_date"><?php echo lang('disable_date');?> </label>
	<?php echo form_input($disable_date); ?>

	<label class="checkbox">
	    <?php echo form_checkbox($new_window); ?> <?php echo lang('new_window');?>
	</label>

	<label for="image"><?php echo lang('image');?> </label>
	<?php echo form_upload($f_image); ?>

	<?php if($banner_id && $image != ''):?>
	<div style="text-align:center; padding:5px; border:1px solid #ccc;"><img src="<?php echo base_url('uploads/'.$image);?>" alt="current"/><br/><?php echo lang('current_file');?></div>
	<?php endif;?>

	<div class="form-actions">
		<input class="btn btn-primary" type="submit" value="<?php echo lang('save');?>"/>
	</div>
</form>
<script type="text/javascript">
	$(document).ready(function() {
		$("#enable_date").datepicker({ dateFormat: 'mm-dd-yy'});
		$("#disable_date").datepicker({ dateFormat: 'mm-dd-yy'});
	});
	
	$('form').submit(function() {
		$('.btn').attr('disabled', true).addClass('disabled');
	});
</script>
