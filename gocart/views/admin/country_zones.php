<?php include('header.php'); ?>
<script type="text/javascript">
function areyousure()
{
	return confirm('<?php echo lang('confirm_delete_zone');?>');
}
</script>

<div class="button_set" style="text-align:right;">
	<a href="<?php echo site_url($this->config->item('admin_folder').'/locations/country_form'); ?>"><?php echo lang('add_new_country');?></a>
	<a href="<?php echo site_url($this->config->item('admin_folder').'/locations/zone_form'); ?>"><?php echo lang('add_new_zone');?></a>
</div>
<br/>
<table class="gc_table" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="gc_cell_left"><?php echo lang('name');?></th>
			<th><?php echo lang('code');?></th>
			<th><?php echo lang('tax');?></th>
			<th><?php echo lang('status');?></th>
			<th class="gc_cell_right"></th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($zones as $location):?>
		<tr class="gc_row">
			<td class="gc_cell_left"><?php echo  $location->name; ?></td>
			<td><?php echo $location->code;?></td>
			<td><?php echo $location->tax+0;?>%</td>
			<td><?php echo ((bool)$location->status)?'enabled':'disabled';?></td>
			<td class="gc_cell_right list_buttons" >
				<a href="<?php echo site_url($this->config->item('admin_folder').'/locations/delete_zone/'.$location->id); ?>" onclick="return areyousure();"><?php echo lang('delete');?></a>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/locations/zone_form/'.$location->id); ?>"><?php echo lang('edit');?></a>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/locations/zone_areas/'.$location->id); ?>"><?php echo lang('zone_areas');?></a>
			</td>
	  </tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php include('footer.php'); ?>
