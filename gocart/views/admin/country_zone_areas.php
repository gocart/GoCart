<?php include('header.php'); ?>
<script type="text/javascript">
function areyousure()
{
	return confirm('<?php echo lang('confirm_delete_zone_area');?>');
}
</script>

<div class="button_set" style="text-align:right;">
	<a href="<?php echo site_url($this->config->item('admin_folder').'/locations/country_form'); ?>"><?php echo lang('add_new_country');?></a>
	<a href="<?php echo site_url($this->config->item('admin_folder').'/locations/zone_form'); ?>"><?php echo lang('add_new_zone');?></a>
	<a href="<?php echo site_url($this->config->item('admin_folder').'/locations/zone_area_form/'.$zone->id);?>"><?php echo lang('add_new_zone_area');?></a>
</div>
<br/>
<table class="gc_table" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="gc_cell_left"><?php echo lang('code');?></th>
			<th><?php echo lang('tax');?></th>
			<th class="gc_cell_right"></th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($areas as $location):?>
		<tr class="gc_row">
			<td class="gc_cell_left"><?php echo  $location->code; ?></td>
			<td><?php echo $location->tax+0;?>%</td>
			<td class="gc_cell_right list_buttons" >
				<a href="<?php echo  site_url($this->config->item('admin_folder').'/locations/delete_zone_area/'.$location->id); ?>" onclick="return areyousure();"><?php echo lang('delete');?></a>
				<a href="<?php echo  site_url($this->config->item('admin_folder').'/locations/zone_area_form/'.$zone->id.'/'.$location->id); ?>"><?php echo lang('edit');?></a>
			</td>
	  </tr>
<?php endforeach; ?>
<?php if(count($areas) == 0):?>
		<tr>
			<td colspan="2">
				<?php echo lang('no_zone_areas');?>
			<td>
		</tr>
<?php endif;?>
	</tbody>
</table>
<?php include('footer.php');