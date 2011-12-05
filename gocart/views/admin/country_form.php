<?php include('header.php'); ?>

<?php echo form_open($this->config->item('admin_folder').'/locations/country_form/'.$id); ?>

<div class="button_set">
	<input type="submit" value="<?php echo lang('save');?>" />
</div>

<div id="gc_tabs">
	<ul>
		<li><a href="#gc_country_details"><?php echo lang('details');?></a></li>
	</ul>
	
	<div id="gc_country_details">
		<div class="gc_field2">
		<label><?php echo lang('name');?></label>
			<?php
			$data	= array('id'=>'name', 'name'=>'name', 'value'=>set_value('name', $name), 'class'=>'gc_tf1');
			echo form_input($data);
			?>
		</div>
		
		<div class="gc_field2">
			<label for="max_uses"><?php echo lang('iso_code_2');?></label>
			<?php
			$data	= array( 'name'=>'iso_code_2', 'maxlength'=>'2', 'value'=>set_value('iso_code_2', $iso_code_2), 'class'=>'gc_tf1');
			echo form_input($data);
			?>
		</div>
		<div class="gc_field2">
			<label for="max_product_instances"><?php echo lang('iso_code_3');?></label>
			<?php
				$data	= array('name'=>'iso_code_3', 'maxlength'=>'3', 'value'=>set_value('iso_code_3', $iso_code_3), 'class'=>'gc_tf1');
				echo form_input($data);
			?>
		</div>
		<div class="gc_field2">
			<label for="start_date"><?php echo lang('address_format');?></label><br/>
			<?php
				$data	= array('name'=>'address_format', 'value'=>set_value('address_format', $address_format), 'class'=>'gc_tf1');
				echo form_textarea($data);
			?>
		</div>
		<div class="gc_field2">
			<?php $postcode_required = array('name'=>'postcode_required', 'value'=>1, 'checked'=>set_checkbox('postcode_required', 1, (bool)$postcode_required));?>
			<?php echo form_checkbox($postcode_required); ?> <label><?php echo lang('require_postcode');?></label>
		</div>
		<div class="gc_field2">
			<label for="max_product_instances"><?php echo lang('tax');?></label>
			<?php
				$data	= array('name'=>'tax', 'maxlength'=>'10', 'value'=>set_value('tax', $tax), 'class'=>'gc_tf1');
				echo form_input($data);
			?>
		</div>
		<div class="gc_field2">
			<?php $status		= array('name'=>'status', 'value'=>1, 'checked'=>set_checkbox('status', 1, (bool)$status));?>
			<?php echo form_checkbox($status); ?> <label><?php echo lang('enabled');?></label>
		</div>
	</div>
	
</div>
</form>

<script type="text/javascript">
$(document).ready(function(){
	$("#gc_tabs").tabs();
});
</script>


<?php include('footer.php'); ?>
