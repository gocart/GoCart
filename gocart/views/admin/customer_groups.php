<?php include('header.php') ?>

<script type="text/javascript">
function areyousure()
{
	return confirm('Are you sure you want to delete this group?');
}

</script>

<div class="button_set">
	<a href="<?php echo site_url( $this->config->item('admin_folder').'/customers/edit_group'); ?>">Add New Group</a>
</div>

	
	<table class="gc_table">
	<thead>
		<tr>
			<th>Group Name</th>
			<th>Discount</th>
			<th>Discount Type</th>
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
						<a href="<?php echo site_url($this->config->item('admin_folder').'/customers/delete_group/'.$group->id); ?>" onclick="return areyousure();">Delete</a>
						<?php endif; ?>
						<a href="<?php echo site_url($this->config->item('admin_folder').'/customers/edit_group/'.$group->id); ?>">Edit</a>
					</td>
				</tr>
		<?php	endforeach; ?>
       <?php else :  ?>
			
		<tr><td> There are no groups </td></tr>
       <?php endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<td> </td>
			</tr>
		</tfoot>
	</table>


</div>


<?php include('footer.php') ?>