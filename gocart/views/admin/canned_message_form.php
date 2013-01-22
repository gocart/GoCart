<?php include('header.php'); ?>

<?php echo form_open($this->config->item('admin_folder').'/settings/canned_message_form/'.$id); ?>

	<label for="name"><?php echo lang('label_canned_name');?> </label>
	<?php
	$name_array = array('name' =>'name', 'class'=>'input-xlarge', 'value'=>set_value('name', $name));

	if(!$deletable) {
		$name_array['class']	= "input-xlarge disabled";
		$name_array['readonly']	= "readonly";
	}
	echo form_input($name_array);?>
	
	
	<label for="subject"><?php echo lang('label_canned_subject');?> </label>
	<?php echo form_input(array('name'=>'subject', 'class'=>'input-xlarge', 'value'=>set_value('subject', $subject)));?>
	
	<label for="description"></label>
	<?php
	$data	= array('id'=>'description', 'name'=>'content', 'class'=>'redactor', 'value'=>set_value('content', $content));
	echo form_textarea($data);
	?>
	
	<div class="form-actions">
		<input class="btn btn-primary" type="submit" value="<?php echo lang('save');?>"/>
	</div>
	

</form>
<script type="text/javascript">
	$('form').submit(function() {
		$('.btn').attr('disabled', true).addClass('disabled');
	});
</script>
<?php include('footer.php');