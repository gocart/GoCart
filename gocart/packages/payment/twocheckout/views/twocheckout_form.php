<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<label><?php echo lang('enabled');?></label>
<select name="enabled" class="span3">
	<option value="1"<?php echo((bool)$settings['enabled'])?' selected="selected"':'';?>><?php echo lang('enabled');?></option>
	<option value="0"<?php echo((bool)$settings['enabled'])?'':' selected="selected"';?>><?php echo lang('disabled');?></option>
</select>

<label><?php echo lang('currency') ?></label>
<input class="span3" name="currency" value="<?php echo @$settings["currency"] ?>" /> <?php echo lang('currency_label') ?>

<label><?php echo lang('sid') ?></label>
<input class="span3" name="sid" type="text" value="<?php echo @$settings["sid"] ?>" size="50" >

<label><?php echo lang('secret') ?></label>
<input class="span3" name="secret" type="text" value="<?php echo @$settings["secret"] ?>" size="50">
