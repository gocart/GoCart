<?php include('header.php'); ?>
<script type="text/javascript">
function areyousure()
{
	return confirm('<?php echo lang('confirm_delete_file');?>');
}
</script>
<div class="button_set">
	<a href="<?php echo site_url($this->config->item('admin_folder').'/digital_products/form');?>"><?php echo lang('add_file');?></a>
</div>

<table class="gc_table" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th class="gc_cell_left"><?php echo lang('filename');?></th>
				<th><?php echo lang('title');?></th>
				<th style="width:60px;"><?php echo lang('size');?></th>
				<th class="gc_cell_right"></th>
			</tr>
		</thead>
		<tbody>
		<?php echo (count($file_list) < 1)?'<tr><td style="text-align:center;" colspan="6">'.lang('no_files').'</td></tr>':''?>
		<?php foreach ($file_list as $file):?>
			<tr>
				<td class="gc_cell_left"><?php echo $file->filename ?></td>
				<td><?php echo $file->title ?></td>
				<td><?php echo $file->size ?></td>
				<td class="gc_cell_right list_buttons">
					<a href="<?php echo  site_url($this->config->item('admin_folder').'/digital_products/delete/'.$file->id);?>" onclick="return areyousure();"><?php echo lang('delete');?></a>
					<a href="<?php echo  site_url($this->config->item('admin_folder').'/digital_products/form/'.$file->id);?>"><?php echo lang('edit');?></a>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
</table>

<?php include('footer.php'); ?>