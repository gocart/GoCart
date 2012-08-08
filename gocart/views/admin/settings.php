<?php include('header.php'); ?>


<?php if(count($shipping_modules) >0): ?>
<div class="row">
	<div class="span12">
		<div class="page-header">
			<h3><?php echo lang('shipping_modules');?></h3>
		</div>
		<table class="table table-striped">
			<tbody>
			<?php foreach($shipping_modules as $module=>$enabled): ?>
				<tr>
					<td><?php echo $module; ?></td>
					<td>
						<span class="btn-group pull-right">
					<?php if($enabled): ?>
						<a class="btn" href="<?php echo site_url($this->config->item('admin_folder').'/shipping/settings/'.$module);?>"><i class="icon-wrench"></i> <?php echo lang('settings');?></a>
						<a class="btn btn-danger" href="<?php echo site_url($this->config->item('admin_folder').'/shipping/uninstall/'.$module);?>" onclick="return areyousure();"><i class=" icon-minus icon-white"></i> <?php echo lang('uninstall');?></a>
					<?php else: ?>
						<a class="btn" href="<?php echo site_url($this->config->item('admin_folder').'/shipping/install/'.$module);?>"><i class="icon-ok"></i> <?php echo lang('install');?></a>
					<?php endif; ?>
						</span>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
<?php endif; ?>

<?php if(count($payment_modules) >0): ?>
<div class="row">
	<div class="span12">
		<div class="page-header">
			<h3><?php echo lang('payment_modules');?></h3>
		</div>
		<table class="table table-striped">
			<tbody>
			<?php foreach($payment_modules as $module=>$enabled): ?>
				<tr>
					<td><?php echo $module; ?></td>
					<td>
						<span class="btn-group pull-right">
					<?php if($enabled): ?>
						<a class="btn" href="<?php echo site_url($this->config->item('admin_folder').'/payment/settings/'.$module);?>"><i class="icon-wrench"></i> <?php echo lang('settings');?></a>
						<a class="btn btn-danger" href="<?php echo site_url($this->config->item('admin_folder').'/payment/uninstall/'.$module);?>" onclick="return areyousure();"><i class=" icon-minus icon-white"></i> <?php echo lang('uninstall');?></a>
					<?php else: ?>
						<a class="btn" href="<?php echo site_url($this->config->item('admin_folder').'/payment/install/'.$module);?>"><i class="icon-ok"></i> <?php echo lang('install');?></a>
					<?php endif; ?>
						</span>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
<?php endif; ?>		


<div class="row">
	<div class="span12">
		<div class="page-header">
			<div class="button_set pull-right">
				<a class="btn" href="<?php echo site_url($this->config->item('admin_folder').'/settings/canned_message_form/');?>"><i class="icon-plus-sign"></i> <?php echo lang('add_canned_message');?></a>
			</div>
			<h3><?php echo lang('canned_messages');?></h3>
		</div>

<?php if(count($canned_messages) > 0): ?>
<table class="table table-striped">
	<tbody>
	<?php foreach($canned_messages as $message): ?>
		<tr class="gc_row">
			<td><?php echo $message['name']; ?></td>
			<td>
				<span class="btn-group pull-right">
					<a class="btn" href="<?php echo site_url($this->config->item('admin_folder').'/settings/canned_message_form/'.$message['id']);?>"><i class="icon-pencil"></i> <?php echo lang('edit');?></a>
					<?php if($message['deletable'] == 1) : ?>	
						<a class="btn btn-danger" href="<?php echo site_url($this->config->item('admin_folder').'/settings/delete_message/'.$message['id']);?>" onclick="return areyousure();"><i class="icon-trash icon-white"></i> <?php echo lang('delete');?></a>
					<?php endif; ?>
				</span>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>		

<script type="text/javascript">
function areyousure()
{
	return confirm('<?php echo lang('confirm_are_you_sure');?>');
}
</script>
<?php include('footer.php');