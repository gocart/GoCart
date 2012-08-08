<?php include('header.php'); ?>
<script type="text/javascript">
function areyousure()
{
	return confirm('<?php echo lang('confirm_delete_file');?>');
}
</script>

<a class="btn" style="float:right;" href="<?php echo site_url($this->config->item('admin_folder').'/digital_products/form');?>"><i class="icon-plus-sign"></i> <?php echo lang('add_file');?></a>


<table class="table table-striped">
		<thead>
			<tr>
				<th><?php echo lang('filename');?></th>
				<th><?php echo lang('title');?></th>
				<th><?php echo lang('size');?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php echo (count($file_list) < 1)?'<tr><td style="text-align:center;" colspan="4">'.lang('no_files').'</td></tr>':''?>
		<?php foreach ($file_list as $file):?>
			<tr>
				<td><?php echo $file->filename; ?></td>
				<td><?php echo $file->title; ?></td>
				<td><?php echo $file->size; ?> kb</td>
				<td>
					<div class="btn-group" style="float:right">
						<a class="btn" href="<?php echo  site_url($this->config->item('admin_folder').'/digital_products/form/'.$file->id);?>"><i class="icon-pencil"></i> <?php echo lang('edit');?></a>
						
						<a class="btn btn-danger" href="<?php echo  site_url($this->config->item('admin_folder').'/digital_products/delete/'.$file->id);?>" onclick="return areyousure();"><i class="icon-trash icon-white"></i> <?php echo lang('delete');?></a>
					</div>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
</table>

<?php include('footer.php'); ?>