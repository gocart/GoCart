<?php include('header.php'); ?>
<script type="text/javascript">
function areyousure()
{
	return confirm('<?php echo lang('confirm_delete_category');?>');
}
</script>

<div class="button_set">
	<a class="button" href="<?php echo site_url($this->config->item('admin_folder').'/categories/form'); ?>"><?php echo lang('add_new_category');?></a>
</div>

<table class="gc_table" cellspacing="0" cellpadding="0">
    <thead>
		<tr>
			<th class="gc_cell_left"><?php echo lang('category_id');?></th>
			<th><?php echo lang('name')?></th>
			<th class="gc_cell_right"></th>
		</tr>
	</thead>
	<tbody>
		<?php echo (count($categories) < 1)?'<tr><td style="text-align:center;" colspan="3">'.lang('no_categories').'</td></tr>':''?>
		<?php
		define('ADMIN_FOLDER', $this->config->item('admin_folder'));
		function list_categories($cats, $sub='') {
			
			foreach ($cats as $cat):?>
			<tr class="gc_row">
				<td class="gc_cell_left" style="width:16px;"><?php echo  $cat['category']->id; ?></td>
				<td><?php echo  $sub.$cat['category']->name; ?></td>
				<td class="gc_cell_right list_buttons">
					<a href="<?php echo  site_url(ADMIN_FOLDER.'/categories/delete/'.$cat['category']->id);?>" onclick="return areyousure();"><?php echo lang('delete');?></a>

					<a href="<?php echo  site_url(ADMIN_FOLDER.'/categories/form/'.$cat['category']->id);?>" class="ui-state-default ui-corner-all"><?php echo lang('edit');?></a>

					<a href="<?php echo  site_url(ADMIN_FOLDER.'/categories/organize/'.$cat['category']->id);?>"><?php echo lang('organize');?></a>
				</td>
			</tr>
			<?php
			if (sizeof($cat['children']) > 0)
			{
				$sub2 = str_replace('&rarr;&nbsp;', '&nbsp;', $sub);
					$sub2 .=  '&nbsp;&nbsp;&nbsp;&rarr;&nbsp;';
				list_categories($cat['children'], $sub2);
			}
			endforeach;
		}
		
		list_categories($categories);
		?>
	</tbody>
</table>
<?php include('footer.php');