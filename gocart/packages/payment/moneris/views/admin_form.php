<div class="row">
	<div class="span3">
		<label><?php echo lang('enabled');?></label>
		<select name="enabled" class="span3">
			<option value="1" <?php echo((bool)$settings['enabled'])?' selected="selected"':'';?>><?php echo lang('enabled');?></option>
			<option value="0" <?php echo((bool)$settings['enabled'])?'':' selected="selected"';?>><?php echo lang('disabled');?></option>
		</select>
	</div>
</div>
<div class="row">
	<div class="span3">
		<label><?php echo lang('mode');?></label>
		<select name="mode" class="span3">
			<option value="test" <?php echo($settings['mode']=='test')?'selected="selected"':'';?>><?php echo lang('test_mode');?></option>
			<option value="production" <?php echo($settings['mode']!='production')?'':'selected="selected"';?>><?php echo lang('production');?></option>
		</select>
	</div>
</div>
<div class="row">
	<div class="span3">
		<label><?php echo lang('site_id') ?></label>
		<?php
			echo form_input('site_id', $settings['site_id']);
		?>
	</div>
</div>
<div class="row">
	<div class="span3">
		<label><?php echo lang('api_key') ?></label>
		<?php
			echo form_input('api_key', $settings['api_key']);
		?>
	</div>
</div>
<div class="row">
	<div class="span3">
		<label><?php echo lang('descriptor') ?></label>
		<?php
			echo form_input('descriptor', $settings['descriptor']);
		?>
	</div>
</div>