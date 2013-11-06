<script type="text/javascript">
function areyousure()
{
	return confirm('<?php echo lang('confirm_delete_filter');?>');
}
</script>

<div style="text-align:right">
	<a class="btn" href="<?php echo site_url($this->config->item('admin_folder').'/filters/form'); ?>"><i class="icon-plus-sign"></i> <?php echo lang('add_new_filter');?></a>
</div>

<?php //die(var_dump($filters)) ?>

<table class="table table-striped">
    <thead>
		<tr>
			<th><?php echo lang('filter_id');?></th>
			<th><?php echo lang('name')?></th>
			<th><?php echo lang('slug')?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php echo (count($filters) < 1)?'<tr><td style="text-align:center;" colspan="4">'.lang('no_filters').'</td></tr>':''?>
		<?php
		define('ADMIN_FOLDER', $this->config->item('admin_folder'));
		
		function list_filters($parent_id, $filts, $sub='') {
			foreach ($filts[$parent_id] as $fil):?>
			<tr>
				<td><?php echo  $fil->id; ?></td>
				<td><?php echo  $sub.$fil->name; ?></td>
				<td><?php echo  $sub.$fil->slug; ?></td>
				<td>
					<div class="btn-group" style="float:right">

						<a class="btn" href="<?php echo  site_url(ADMIN_FOLDER.'/filters/form/'.$fil->id);?>"><i class="icon-pencil"></i> <?php echo lang('edit');?></a>

						<a class="btn" href="<?php echo  site_url(ADMIN_FOLDER.'/filters/organize/'.$fil->id);?>"><i class="icon-move"></i> <?php echo lang('organize');?></a>
						
						<a class="btn btn-danger" href="<?php echo  site_url(ADMIN_FOLDER.'/filters/delete/'.$fil->id);?>" onclick="return areyousure();"><i class="icon-trash icon-white"></i> <?php echo lang('delete');?></a>
					</div>
				</td>
			</tr>
			<?php
			if (isset($filts[$fil->id]) && sizeof($filts[$fil->id]) > 0)
			{
				$sub2 = str_replace('&rarr;&nbsp;', '&nbsp;', $sub);
					$sub2 .=  '&nbsp;&nbsp;&nbsp;&rarr;&nbsp;';
				list_filters($fil->id, $filts, $sub2);
			}
			endforeach;
		}
		
		if(isset($filters[0]))
		{
			list_filters(0, $filters);
		}
		
		?>
	</tbody>
</table>