<?php include('header.php'); ?>

<?php echo form_open($this->config->item('admin_folder').'/locations/zone_form/'.$id); ?>

	<label for="country_id"><?php echo lang('country');?></label>
	<?php
	
	$country_ids	= array();
	foreach($countries as $c)
	{
		$country_ids[$c->id] = $c->name;
	}
	
	?>
	<?php echo form_dropdown('country_id', $country_ids, set_value('country_id', $country_id) ,'class="span12"');?>

	<label for="name"><?php echo lang('name');?></label>
	<?php
	$data	= array('id'=>'name', 'name'=>'name', 'value'=>set_value('name', $name), 'class'=>'span12');
	echo form_input($data);
	?>
	
	<label for="code"><?php echo lang('code');?></label>
	<?php
	$data	= array( 'name'=>'code', 'value'=>set_value('code', $code));
	echo form_input($data);
	?>

	<label for="tax"><?php echo lang('tax');?></label>
	<div class="input-append">
		<?php
		$data	= array('name'=>'tax', 'maxlength'=>'10', 'value'=>set_value('tax', $tax));
		echo form_input($data);
		?>
		<span class="add-on">%</span>
	</div>
	
	<?php $status		= array('name'=>'status', 'value'=>1, 'checked'=>set_checkbox('status', 1, (bool)$status));?>
	<label class="checkbox"><?php echo form_checkbox($status); ?> <?php echo lang('enabled');?></label>


	<div class="form-actions">
		<button type="submit" class="btn btn-primary"><?php echo lang('form_save');?></button>
	</div>
</form>

<script type="text/javascript">
$('form').submit(function() {
	$('.btn').attr('disabled', true).addClass('disabled');
});
</script>

<?php include('footer.php');
