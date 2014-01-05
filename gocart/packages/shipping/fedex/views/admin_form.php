
<div class="row">
	
	<div class="span3">
		<h4>Account Credentials</h4>
		<label><?php echo lang('fedex_key');?></label>
		<?php echo form_input('key', $key, 'class="span3"');?>

		<label><?php echo lang('fedex_account');?></label>
		<?php echo form_input('shipaccount', $shipaccount, 'class="span3"');?>

		<label><?php echo lang('fedex_meter');?></label>
		<?php echo form_input('meter', $meter, 'class="span3"');?>

		<label><?php echo lang('password');?></label>
		<?php echo form_input('password', $password, 'class="span3"');?>
	</div>

	<div class="span3">
		<label><h4><?php echo lang('fedex_services');?></h4></label>
		<?php  foreach($service_list as $id=>$opt):?>
			<label class="checkbox">
				<input type="checkbox" name="service[]" value="<?php echo $id;?>" <?php echo (in_array($id, $service))?'checked="checked"':'';?> />
			<?php echo $opt;?>
		</label>
		<?php endforeach;?>
	</div>

	<div class="span3">
		<h4><?php echo lang('container');?></h4>
		<?php echo form_dropdown('package', $package_types, $package, 'class="span3"');?>

		<strong><?php echo lang('dimensions');?></strong>
		<label><?php echo lang('height');?> (<?php echo $this->config->item('dimension_unit');?>)</label>
		<?php echo form_input('height', $height, 'class="span3"');?>

		<label><?php echo lang('width');?> (<?php echo $this->config->item('dimension_unit');?>)</label>
		<?php echo form_input('width', $width, 'class="span3"');?>

		<label><?php echo lang('length');?> (<?php echo $this->config->item('dimension_unit');?>)</label>
		<?php echo form_input('length', $length, 'class="span3"');?>
	</div>
	
	<div class="span3">
		<h4><?php echo lang('packageopts') ?></h4>

		<label><?php echo lang('dropofftype') ?></label>
		<?php echo form_dropdown('dropofftype', $dropoff_types, $dropofftype, 'class="span3"');?>


		<label><?php echo lang('fee');?></label>
		<?php echo form_dropdown('handling_method', array('Price'=>'Price', 'Percent'=>'Percent'), $handling_method, 'class="span3"');?>

		<label><?php echo lang('fee_amount') ?></label>
		<?php echo form_input('handling_fee', $handling_amount) ?>

		<label><?php echo lang('insurance');?> <?php echo form_checkbox(array(
									'name' => 'insurance',
									'value' => 'yes',
									'checked' => ($insurance=='yes')
								)); ?> </label>
		

		<label><?php echo lang('enabled');?></label>
		<?php echo form_dropdown('enabled', array(lang('disabled'), lang('enabled')), $enabled, 'class="span3"');?>
	</div>

</div>
