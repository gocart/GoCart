<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<label><?php echo lang('enabled');?></label>
<select name="enabled" class="span3">
	<option value="1"<?php echo((bool)$settings['enabled'])?' selected="selected"':'';?>><?php echo lang('enabled');?></option>
	<option value="0"<?php echo((bool)$settings['enabled'])?'':' selected="selected"';?>><?php echo lang('disabled');?></option>
</select>

<label><?php echo lang('test_mode_label') ?></label>
<select name="SANDBOX" class="span3">
	<option value="1"<?php echo((bool)$settings['SANDBOX'])?' selected="selected"':'';?>><?php echo lang('test_mode');?></option>
	<option value="0"<?php echo((bool)$settings['SANDBOX'])?'':' selected="selected"';?>><?php echo lang('live_mode');?></option>
</select>

<label><?php echo lang('pp_username') ?></label>
<input class="span3" name="username" type="text" value="<?php echo @$settings["username"] ?>" size="50" >

<label><?php echo lang('pp_password') ?></label>
<input class="span3" name="password" type="text" value="<?php echo @$settings["password"] ?>" size="50">

<label><?php echo lang('pp_key') ?></label>
<input class="span3" name="signature" type="text" value="<?php echo @$settings["signature"] ?>" size="50" />
