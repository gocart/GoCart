<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<table border="0" cellpadding="5">
  <tr>
    <td width="40%"><div><?php echo lang('enabled') ?></div></td>
<td width="60%"><select name="enabled" >
    	  <option value="1"><?php echo lang('enabled') ?></option>
     <?php if($settings['enabled']) { ?>
		  <option value="0"><?php echo lang('disabled') ?></option>
    <?php } else { ?>
		  <option value="0" selected ><?php echo lang('disabled') ?></option>
    <?php } ?>
    </select>
    </td>
  </tr>
  <tr>
    <td><div><?php lang('mode') ?></div></td>
    <td><select name="authorize_net_test_mode" >
    	 <option value="TRUE"><?php echo lang('test_mode') ?></option>
   	 <?php if($settings["authorize_net_test_mode"] == "TRUE") { ?>
		  <option value="FALSE"><?php echo lang('live_mode') ?></option>
    <?php } else { ?>
		  <option value="FALSE" selected><?php echo lang('live_mode') ?></option>
    <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td><div><?php echo lang('tm_login') ?></div></td>
    <td><input class="gc_tf1" name="authorize_net_test_x_login" type="text" value="<?php echo $settings["authorize_net_test_x_login"] ?>" size="50" ></td>
  </tr>
  <tr>
    <td><div><?php echo lang('tm_key') ?></div></td>
    <td><input class="gc_tf1" name="authorize_net_test_x_tran_key" type="text" value="<?php echo $settings["authorize_net_test_x_tran_key"] ?>" size="50"></td>
  </tr>
  <tr>
    <td><div><?php echo lang('lm_login') ?></div></td>
    <td><input class="gc_tf1" name="authorize_net_live_x_login" type="text" value="<?php echo $settings["authorize_net_live_x_login"] ?>" size="50"></td>
  </tr>
  <tr>
    <td><div><?php echo lang('lm_key') ?></div></td>
    <td><input class="gc_tf1" name="authorize_net_live_x_tran_key" type="text" value="<?php echo $settings["authorize_net_live_x_tran_key"] ?>" size="50"></td>
  </tr>
</table>
