<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<table width="100%" border="0" cellpadding="5">
  <tr>
    <td width="21%"><div align="right"><?php echo lang('enabled') ?></div></td>
<td width="79%"><select name="enabled" >
    	  <option value="1"><?php echo lang('enabled') ?></option>
     <?php if($settings['enabled']) { ?>
		  <option value="0"><?php echo lang('disabled') ?></option>
    <?php } else { ?>
		  <option value="0" selected ><?php echo lang('disabled') ?></option>
    <?php } ?>
    </select>    </td>
  </tr>
  <tr>
    <td><div align="right"><?php echo lang('test_mode_label') ?></div></td>
    <td><select name="SANDBOX" >
    	 <option value="1"><?php echo lang('test_mode') ?></option>
   	 <?php if(@$settings["SANDBOX"] == "1") { ?>
		  <option value="0"><?php echo lang('live_mode') ?></option>
    <?php } else { ?>
		  <option value="0" selected><?php echo lang('live_mode') ?></option>
    <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td><div align="right"><?php echo lang('currency') ?></div></td>
    <td><input class="gc_tf1" name="currency" value="<?php echo @$settings["currency"] ?>" /> <?php echo lang('currency_label') ?></td>
  </tr>
  <tr>
    <td><div align="right"><?php echo lang('pp_username') ?></div></td>
    <td><input class="gc_tf1" name="username" type="text" value="<?php echo @$settings["username"] ?>" size="50" ></td>
  </tr>
  <tr>
    <td><div align="right"><?php echo lang('pp_password') ?></div></td>
    <td><input class="gc_tf1" name="password" type="text" value="<?php echo @$settings["password"] ?>" size="50"></td>
  </tr>
  <tr>
    <td><div align="right"><?php echo lang('pp_key') ?></div></td>
    <td><input class="gc_tf1" name="signature" type="text" value="<?php echo @$settings["signature"] ?>" size="50" /></td>
  </tr>
</table>
