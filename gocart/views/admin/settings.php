<?php include('header.php'); ?>

<?php if(count($shipping_modules) >0): ?>
<table class="gc_table" style="margin-bottom:10px" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="gc_cell_left">Shipping Modules</th>
			<th class="gc_cell_right"></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($shipping_modules as $module=>$enabled): ?>
		<tr>
			<td><?php echo $module; ?></td>
			<td class="gc_cell_right list_buttons">
			<?php if($enabled): ?>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/shipping/uninstall/'.$module);?>" onclick="return areyousure();">Uninstall</a>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/shipping/settings/'.$module);?>">Settings</a>
			<?php else: ?>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/shipping/install/'.$module);?>">Install</a>
			<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>

<?php if(count($payment_modules) >0): ?>
<table class="gc_table" cellspacing="0" cellpadding="0" style="margin-bottom:10px">
	<thead>
		<tr>
			<th class="gc_cell_left">Payment Modules</th>
			<th class="gc_cell_right"></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($payment_modules as $module=>$enabled): ?>
		<tr>
			<td><?php echo $module; ?></td>
			<td class="gc_cell_right list_buttons">
			<?php if($enabled): ?>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/payment/uninstall/'.$module);?>" onclick="return areyousure();">Uninstall</a>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/payment/settings/'.$module);?>">Settings</a>
			<?php else: ?>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/payment/install/'.$module);?>">Install</a>
			<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>		

<div class="button_set">
	<a href="<?php echo site_url($this->config->item('admin_folder').'/settings/canned_message_form/');?>">Add Canned Message</a>
</div>

<?php if(count($canned_messages) > 0): ?>
<table class="gc_table" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="gc_cell_left">Canned Messages</th>
			<th class="gc_cell_right"> </th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($canned_messages as $message): ?>
		<tr class="gc_row">
			<td><?php echo $message['name']; ?></td>
			<td class="gc_cell_right list_buttons">
			<?php if($message['deletable'] == 1) : ?>	
				<a href="<?php echo site_url($this->config->item('admin_folder').'/settings/delete_message/'.$message['id']);?>" onclick="return areyousure();">Delete</a>
			<?php endif; ?>	
				<a href="<?php echo site_url($this->config->item('admin_folder').'/settings/canned_message_form/'.$message['id']);?>">Edit</a>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>		

<script type="text/javascript">
function areyousure()
{
	return confirm('Are you sure you want to do this?');
}
</script>
<?php include('footer.php');