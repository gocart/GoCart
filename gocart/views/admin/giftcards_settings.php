<?php include('header.php'); ?>

<?php echo form_open($this->config->item('admin_folder').'/giftcards/settings'); ?>

<div class="button_set">
	<input type="submit" value="<?php echo lang('save');?>"/>
</div>
<div id="gc_tabs">
	<ul>
		<li><a href="#gc_giftcard_settings"><?php echo lang('settings');?></a></li>
	</ul>
	<div id="gc_giftcard_settings">
		<div class="gc_field2">
			<label><?php echo lang('predefined_card_amounts');?></label>
			<?php 
			$data	= array('name'=>'predefined_card_amounts', 'value'=>set_value('predefined_card_amounts', $predefined_card_amounts), 'class'=>'gc_tf1');
			echo form_input($data);
			echo form_error('predefined_card_amounts');
			 ?>
			<small style="margin-left:20px;"><?php echo lang('predefined_exmples');?></small>
		</div>
		<div class="gc_field2">
			<label><?php echo lang('allow_custom_ammounts');?> </label>
			<?php
			$options = array('1'=>lang('yes'),'0'=>lang('no'));
			echo form_dropdown('allow_custom_amount', $options,  $allow_custom_amount);
			 ?>
		</div>
	</div>
</div>

<?php include('footer.php');