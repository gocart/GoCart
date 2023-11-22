<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<table>
	<tr>
		<td>Enabled</td>
		<td>
			<select name="enabled" >
				<option value="1">Enabled</option>
				<?php if($settings['enabled']) { ?>
					<option value="0">Disabled</option>
				<?php } else { ?>
					<option value="0" selected >Disabled</option>
				<?php } ?>
			</select>
		</td>
	</tr>
	
	<tr>
		<td>Mode</td>
		<td>
			<select name="mode" >
				<option value="test">Test</option>
				<?php if($settings['mode'] != 'live') { ?>
					<option value="live">Live</option>
				<?php } else { ?>
					<option value="live" selected >Live</option>
				<?php } ?>
			</select>
		</td>
	</tr>
	
	<tr>
		<td>Test Secret Key</td>
		<td><input class="gc_tf1" name="test_secret_key" value="<?php echo @$settings["test_secret_key"] ?>" /></td>
	</tr>
	
	<tr>
		<td>Test Publishable Key</td>
		<td><input class="gc_tf1" name="test_publishable_key" value="<?php echo @$settings["test_publishable_key"] ?>" /></td>
	</tr>
	
	<tr>
		<td>Live Secret Key</td>
		<td><input class="gc_tf1" name="live_secret_key" value="<?php echo @$settings["live_secret_key"] ?>" /></td>
	</tr>
	
	<tr>
		<td>Live Publishable Key</td>
		<td><input class="gc_tf1" name="live_publishable_key" value="<?php echo @$settings["live_publishable_key"] ?>" /></td>
	</tr>
</table>