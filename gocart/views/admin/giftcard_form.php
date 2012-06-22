<?php include('header.php'); ?>

<?php echo form_open($this->config->item('admin_folder').'/giftcards/form/'); ?>

	<label for="to_name"><?php echo lang('recipient_name');?> </label>
	<?php
	$data	= array('name'=>'to_name', 'value'=>set_value('code'), 'class'=>'span3');
	echo form_input($data);
	?>

	<label for="to_email"><?php echo lang('recipient_email');?></label>
	<?php
	$data	= array('name'=>'to_email', 'value'=>set_value('to_email'), 'class'=>'span3');
	echo form_input($data);
	?>

	<label class="checkbox">
	<?php
	$data	= array('name'=>'send_notification', 'value'=>'true');
	echo form_checkbox($data);
	?>
	<?php echo lang('send_notification');?></label>

	<label for="sender_name"><?php echo lang('sender_name');?></label>
	<?php
	$data	= array('name'=>'from', 'value'=>set_value('from'), 'class'=>'span3');
	echo form_input($data);
	?>

	<label for="personal_message"><?php echo lang('personal_message');?></label>
	<?php
	$data	= array('name'=>'personal_message', 'value'=>set_value('personal_message'), 'class'=>'span3');
	echo form_textarea($data);
	?>

	<label for="beginning_amount"><?php echo lang('amount');?></label>
	<?php
	$data	= array('name'=>'beginning_amount', 'value'=>set_value('beginning_amount'), 'class'=>'span3');
	echo form_input($data);
	?>
	
	<div class="form-actions">
		<input class="btn btn-primary" type="submit" value="<?php echo lang('save');?>"/>
	</div>
	
</form>

<?php include('footer.php');