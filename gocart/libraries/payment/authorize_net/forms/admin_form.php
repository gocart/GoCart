<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<table border="0" cellpadding="5">
  <tr>
    <td width="40%"><div>Enabled</div></td>
<td width="60%"><select name="enabled" >
    	  <option value="1">Enabled</option>
     <?php if($enabled) { ?>
		  <option value="0">Disabled</option>
    <?php } else { ?>
		  <option value="0" selected >Disabled</option>
    <?php } ?>
    </select>
    </td>
  </tr>
  <tr>
    <td><div> Mode</div></td>
    <td><select name="authorize_net_test_mode" >
    	 <option value="TRUE">TEST MODE</option>
   	 <?php if($settings["authorize_net_test_mode"] == "TRUE") { ?>
		  <option value="FALSE">LIVE MODE</option>
    <?php } else { ?>
		  <option value="FALSE" selected>LIVE MODE</option>
    <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td><div>Test Mode Login ID</div></td>
    <td><input class="gc_tf1" name="authorize_net_test_x_login" type="text" value="<?php echo $settings["authorize_net_test_x_login"] ?>" size="50" ></td>
  </tr>
  <tr>
    <td><div>Test Mode Transaction Key</div></td>
    <td><input class="gc_tf1" name="authorize_net_test_x_tran_key" type="text" value="<?php echo $settings["authorize_net_test_x_tran_key"] ?>" size="50"></td>
  </tr>
  <tr>
    <td><div>Live Mode Login ID</div></td>
    <td><input class="gc_tf1" name="authorize_net_live_x_login" type="text" value="<?php echo $settings["authorize_net_live_x_login"] ?>" size="50"></td>
  </tr>
  <tr>
    <td><div>Live Mode Transaction Key</div></td>
    <td><input class="gc_tf1" name="authorize_net_live_x_tran_key" type="text" value="<?php echo $settings["authorize_net_live_x_tran_key"] ?>" size="50"></td>
  </tr>
</table>
