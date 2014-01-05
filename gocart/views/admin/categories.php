<script type="text/javascript">
function areyousure()
{
	return confirm('<?php echo lang('confirm_delete_category');?>');
}
</script>

<div style="text-align:right">
	<a class="btn" href="<?php echo site_url($this->config->item('admin_folder').'/categories/form'); ?>"><i class="icon-plus-sign"></i> <?php echo lang('add_new_category');?></a>
</div>

<table class="table table-striped">
    <thead>
		<tr>
			<th><?php echo lang('category_id');?></th>
			<th><?php echo lang('name')?></th>
			<th><?php echo lang('enabled');?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php echo (count($categories) < 1)?'<tr><td style="text-align:center;" colspan="4">'.lang('no_categories').'</td></tr>':''?>
		<?php
		define('ADMIN_FOLDER', $this->config->item('admin_folder'));
		function list_categories($parent_id, $cats, $sub='') {
			
			foreach ($cats[$parent_id] as $cat):?>
			<tr>
				<td><?php echo  $cat->id; ?></td>
				<td><?php echo  $sub.$cat->name; ?></td>
				<td><?php echo ($cat->enabled == '1') ? lang('enabled') : lang('disabled'); ?></td>
				<td>
					<div class="btn-group" style="float:right">

						<a class="btn" href="<?php echo  site_url(ADMIN_FOLDER.'/categories/form/'.$cat->id);?>"><i class="icon-pencil"></i> <?php echo lang('edit');?></a>

						<a class="btn" href="<?php echo  site_url(ADMIN_FOLDER.'/categories/organize/'.$cat->id);?>"><i class="icon-move"></i> <?php echo lang('organize');?></a>
						
						<a class="btn btn-danger" href="<?php echo  site_url(ADMIN_FOLDER.'/categories/delete/'.$cat->id);?>" onclick="return areyousure();"><i class="icon-trash icon-white"></i> <?php echo lang('delete');?></a>
					</div>
				</td>
			</tr>
			<?php
			if (isset($cats[$cat->id]) && sizeof($cats[$cat->id]) > 0)
			{
				$sub2 = str_replace('&rarr;&nbsp;', '&nbsp;', $sub);
					$sub2 .=  '&nbsp;&nbsp;&nbsp;&rarr;&nbsp;';
				list_categories($cat->id, $cats, $sub2);
			}
			endforeach;
		}
		
		if(isset($categories[0]))
		{
			list_categories(0, $categories);
		}
		
		?>
	</tbody>
</table>