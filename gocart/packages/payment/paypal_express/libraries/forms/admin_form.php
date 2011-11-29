<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<table width="100%" border="0" cellpadding="5">
  <tr>
    <td width="21%"><div align="right">Enabled</div></td>
<td width="79%"><select name="enabled" >
    	  <option value="1">Enabled</option>
     <?php if($enabled) { ?>
		  <option value="0">Disabled</option>
    <?php } else { ?>
		  <option value="0" selected >Disabled</option>
    <?php } ?>
    </select>    </td>
  </tr>
  <tr>
    <td><div align="right">Test Mode</div></td>
    <td><select name="SANDBOX" >
    	 <option value="1">Test Mode</option>
   	 <?php if(@$settings["SANDBOX"] == "1") { ?>
		  <option value="0">Live Mode</option>
    <?php } else { ?>
		  <option value="0" selected>Live Mode</option>
    <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td><div align="right">Currency</div></td>
    <td><select name="currency" >
      <option value="USD">US Dollar</option>
      <option value="EUR" <?php if(@$settings["currency"] == "EUR") echo 'selected';  ?>>Euro</option>
    </select></td>
  </tr>
  <tr>
    <td><div align="right">Paypal Username</div></td>
    <td><input class="gc_tf1" name="username" type="text" value="<?php echo @$settings["username"] ?>" size="50" ></td>
  </tr>
  <tr>
    <td><div align="right">Paypal Password</div></td>
    <td><input class="gc_tf1" name="password" type="text" value="<?php echo @$settings["password"] ?>" size="50"></td>
  </tr>
  <tr>
    <td><div align="right">Paypal API Signature</div></td>
    <td><input class="gc_tf1" name="signature" type="text" value="<?php echo @$settings["signature"] ?>" size="50" /></td>
  </tr>
</table>
