<?php include('header.php'); ?>
<?php echo form_open($this->config->item('admin_folder').'/admin/form/'.$id); ?>
	
		<label><?php echo lang('firstname');?></label>
		<?php
		$data	= array('name'=>'firstname', 'value'=>set_value('firstname', $firstname));
		echo form_input($data);
		?>
		
		<label><?php echo lang('lastname');?></label>
		<?php
		$data	= array('name'=>'lastname', 'value'=>set_value('lastname', $lastname));
		echo form_input($data);
		?>

		<label><?php echo lang('email');?></label>
		<?php
		$data	= array('name'=>'email', 'value'=>set_value('email', $email));
		echo form_input($data);
		?>

		<label><?php echo lang('access');?></label>
		<?php
		$options = array(	'Admin'		=> 'Admin',
							'Orders'	=> 'Orders'
		                );
		echo form_dropdown('access', $options, set_value('phone', $access));
		?>

		<label><?php echo lang('password');?></label>
		<?php
		$data	= array('name'=>'password');
		echo form_password($data);
		?>

		<label><?php echo lang('confirm_password');?></label>
		<?php
		$data	= array('name'=>'confirm');
		echo form_password($data);
		?>
		
		<div class="form-actions">
			<input class="btn btn-primary" type="submit" value="<?php echo lang('save');?>"/>
		</div>
	
</form>
<script type="text/javascript">
if ($.browser.webkit) {
    $('input').attr('autocomplete', 'off');
}
$('form').submit(function() {
	$('.btn').attr('disabled', true).addClass('disabled');
});
</script>
<?php include('footer.php');