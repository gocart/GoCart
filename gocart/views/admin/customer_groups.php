<?php include('header.php') ?>

<script type="text/javascript">
function areyousure()
{
	return confirm('<?php echo lang('confirm_delete_group');?>');
}

</script>

<a class="btn" style="float:right;" href="<?php echo site_url( $this->config->item('admin_folder').'/customers/edit_group'); ?>"><i class="icon-plus-sign"></i> <?php echo lang('add_new_group');?></a>
	
<table class="table table-striped">
	<thead>
		<tr>
			<th><?php echo lang('group_name');?></th>
			<th><?php echo lang('discount');?></th>
			<th><?php echo lang('discount_type');?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	
	<?php foreach ($groups as $group):?>
	<tr>
		<td><?php echo $group->name;?></td>
		<td><?php echo $group->discount ?></td>
		<td><?php echo $group->discount_type ?></td>
		<td>
			<div class="btn-group" style="float:right;">

				<a class="btn" href="<?php echo site_url($this->config->item('admin_folder').'/customers/edit_group/'.$group->id); ?>"><i class="icon-pencil"></i> <?php echo lang('edit');?></a>
				
				<?php if($group->id != 1) : ?>
				<a class="btn btn-danger" href="<?php echo site_url($this->config->item('admin_folder').'/customers/delete_group/'.$group->id); ?>" onclick="return areyousure();"><i class="icon-trash icon-white"></i> <?php echo lang('delete');?></a>
				<?php endif; ?>
			</div>
		</td>
	</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<?php include('footer.php');