<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<label><?php echo lang('enabled'); ?></label>
<select name="enabled" class="span3">
	<option value="1"<?php echo((bool)$settings['enabled'])?' selected="selected"':'';?>><?php echo lang('enabled');?></option>
	<option value="0"<?php echo((bool)$settings['enabled'])?'':' selected="selected"';?>><?php echo lang('disabled');?></option>
</select>

<label><?php echo lang('mode'); ?></label>
<select name="authorize_net_test_mode" class="span3">
	<option value="TRUE"><?php echo lang('test_mode'); ?></option>
	<?php if($settings["authorize_net_test_mode"] == "TRUE") { ?>
		<option value="FALSE"><?php echo lang('live_mode'); ?></option>
	<?php } else { ?>
		<option value="FALSE" selected><?php echo lang('live_mode'); ?></option>
	<?php } ?>
</select>
<label><?php echo lang('tm_login'); ?></label>
<input class="span3" name="authorize_net_test_x_login" type="text" value="<?php echo $settings["authorize_net_test_x_login"] ?>" size="50" >

<label><?php echo lang('tm_key'); ?></label>
<input class="span3" name="authorize_net_test_x_tran_key" type="text" value="<?php echo $settings["authorize_net_test_x_tran_key"] ?>" size="50">

<label><?php echo lang('lm_login'); ?></label>
<input class="span3" name="authorize_net_live_x_login" type="text" value="<?php echo $settings["authorize_net_live_x_login"] ?>" size="50">

<label><?php echo lang('lm_key'); ?></label>
<input class="span3" name="authorize_net_live_x_tran_key" type="text" value="<?php echo $settings["authorize_net_live_x_tran_key"] ?>" size="50">
</table>
