<?php include('header.php'); ?>

<?php echo form_open($this->config->item('admin_folder').'/locations/zone_area_form/'.$zone_id.'/'.$id); ?>

<div class="button_set">
	<input type="submit" value="<?php echo lang('save');?>" />
</div>

<div id="gc_tabs">
	<ul>
		<li><a href="#gc_country_details"><?php echo lang('details');?></a></li>
	</ul>
	
	<div id="gc_country_details">
		<div class="gc_field2">
			<label for="code"><?php echo lang('code');?></label>
			<?php
			$data	= array( 'name'=>'code', 'value'=>set_value('code', $code), 'class'=>'gc_tf1');
			echo form_input($data);
			?>
		</div>
		<div class="gc_field2">
			<label for="max_product_instances"><?php echo lang('tax');?></label>
			<?php
				$data	= array('name'=>'tax', 'maxlength'=>'10', 'value'=>set_value('tax', $tax), 'class'=>'gc_tf1');
				echo form_input($data);
			?>
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
