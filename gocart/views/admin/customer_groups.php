<?php include('header.php') ?>

<script type="text/javascript">
function areyousure()
{
	return confirm('<?php echo lang('confirm_delete_group');?>');
}

</script>

<div class="button_set">
	<a href="<?php echo site_url( $this->config->item('admin_folder').'/customers/edit_group'); ?>"><?php echo lang('add_new_group');?></a>
</div>

	
	<table class="gc_table">
	<thead>
		<tr>
			<th><?php echo lang('group_name');?></th>
			<th><?php echo lang('discount');?></th>
			<th><?php echo lang('discount_type');?></th>
			<th> </th>
		</tr>
	</thead>
	<tbody>
	
	<?php 
		if(isset($groups)) :
			foreach ($groups as $group):?>
				<tr id="group_<?php echo $group->id; ?>">
					<td><?php echo $group->name;?></td>
					<td><?php echo $group->discount ?></td>
					<td><?php echo $group->discount_type ?></td>
					<td class="list_buttons">
						<?php 
						// keep the default group from being deleted
						if($group->id != 1) : ?>
						<a href="<?php echo site_url($this->config->item('admin_folder').'/customers/delete_group/'.$group->id); ?>" onclick="return areyousure();"><?php echo lang('delete');?></a>
						<?php endif; ?>
						<a href="<?php echo site_url($this->config->item('admin_folder').'/customers/edit_group/'.$group->id); ?>"><?php echo lang('edit');?></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
	</table>


</div>


<?php include('footer.php') ?>