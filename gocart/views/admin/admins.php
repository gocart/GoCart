<?php include('header.php'); ?>
<script type="text/javascript">
function areyousure()
{
	return confirm('<?php echo lang('confirm_delete');?>');
}
</script>

<div class="button_set">
	<a href="<?php echo site_url($this->config->item('admin_folder').'/admin/form'); ?>"><?php echo lang('add_new_admin');?></a>
</div>

<table class="gc_table" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="gc_cell_left"><?php echo lang('firstname');?></th>
			<th><?php echo lang('lastname');?></th>
			<th><?php echo lang('email');?></th>
			<th><?php echo lang('access');?></th>
			<th class="gc_cell_right"></th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($admins as $admin):?>
		<tr>
			<td><?php echo $admin->firstname; ?></td>
			<td><?php echo $admin->lastname; ?></td>
			<td><a href="mailto:<?php echo $admin->email;?>"><?php echo $admin->email; ?></a></td>
			<td><?php echo $admin->access; ?></td>
			<td class="gc_cell_right list_buttons">
				<?php
				$current_admin	= $this->session->userdata('admin');
				$margin			= 30;
				if ($current_admin['id'] != $admin->id): ?>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/admin/delete/'.$admin->id); ?>" onclick="return areyousure();"><?php echo lang('delete');?></a>
				<?php endif; ?>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/admin/form/'.$admin->id);?>"><?php echo lang('edit');?></a>	
			</td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php include('footer.php'); ?>
