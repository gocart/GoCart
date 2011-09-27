<?php include('header.php'); ?>

<?php echo form_open($this->config->item('admin_folder').'/giftcards/settings'); ?>

<div class="button_set">
	<input type="submit" value="Save Settings"/>
</div>
<div id="gc_tabs">
	<ul>
		<li><a href="#gc_giftcard_settings">Settings</a></li>
	</ul>
	<div id="gc_giftcard_settings">
		<div class="gc_field2">
			<label>Predefined Values: </label>
			<?php 
			$data	= array('name'=>'predefined_card_amounts', 'value'=>set_value('predefined_card_amounts', $predefined_card_amounts), 'class'=>'gc_tf1');
			echo form_input($data);
			echo form_error('predefined_card_amounts');
			 ?>
			<small style="margin-left:20px;">(ex. 10,20,30)</small>
		</div>
		<div class="gc_field2">
			<label>Allow Custom Values: </label>
			<?php
			$options = array('1'=>'Yes','0'=>'No');
			echo form_dropdown('allow_custom_amount', $options,  $allow_custom_amount);
			 ?>
		</div>
	</div>
</div>

<?php include('footer.php'); ?>
