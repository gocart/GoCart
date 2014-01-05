<?php echo form_open($this->config->item('admin_folder').'/locations/country_form/'.$id); ?>

<fieldset>
	
		<label for="name"><?php echo lang('name');?></label>
		<?php
		$data	= array('name'=>'name', 'value'=>set_value('name', $name), 'class'=>'span12');
		echo form_input($data);
		?>
		
		<label for="iso_code_2"><?php echo lang('iso_code_2');?> / <?php echo lang('iso_code_3');?></label>
		<?php
		$data	= array( 'name'=>'iso_code_2', 'maxlength'=>'2', 'value'=>set_value('iso_code_2', $iso_code_2), 'class'=>'span1');
		echo form_input($data);
		?>
		<?php
		$data	= array('name'=>'iso_code_3', 'maxlength'=>'3', 'value'=>set_value('iso_code_3', $iso_code_3), 'class'=>'span1');
		echo form_input($data);
		?>
		

		<label for="address_format"><?php echo lang('address_format');?></label>
		<?php
		$data	= array('name'=>'address_format', 'value'=>set_value('address_format', $address_format), 'rows'=>6, 'class'=>'span12');
		echo form_textarea($data);
		?>

		<?php $zip_required = array('name'=>'zip_required', 'value'=>1, 'checked'=>set_checkbox('zip_required', 1, (bool)$zip_required));?>
		<label class="checkbox"><?php echo form_checkbox($zip_required); ?> <?php echo lang('require_zip');?></label>
		
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
	<button type="submit" class="btn btn-primary"><?php echo lang('save');?></button>
</div>
</form>

<script type="text/javascript">
$('form').submit(function() {
	$('.btn').attr('disabled', true).addClass('disabled');
});
</script>