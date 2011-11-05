<?php include('header.php'); ?>
<script type="text/javascript">
function areyousure()
{
	return confirm('Are you sure you want to delete this zone area?');
}
</script>

<div class="button_set" style="text-align:right;">
	<a href="<?php echo site_url($this->config->item('admin_folder').'/locations/country_form');?>" >Add New Country</a>
	<a href="<?php echo site_url($this->config->item('admin_folder').'/locations/zone_form');?>" >Add New Zone</a>
	<a href="<?php echo site_url($this->config->item('admin_folder').'/locations/zone_area_form/'.$zone->id);?>" >Add New Zone Area</a>
</div>
<br/>
<table class="gc_table" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="gc_cell_left">Code</th>
			<th>Tax Rate</th>
			<th class="gc_cell_right"></th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($areas as $location):?>
		<tr class="gc_row">
			<td class="gc_cell_left"><?php echo  $location->code; ?></td>
			<td><?php echo $location->tax+0;?>%</td>
			<td class="gc_cell_right list_buttons" >
				<a href="<?php echo  site_url($this->config->item('admin_folder').'/locations/delete_zone_area/'.$location->id); ?>" onclick="return areyousure();">Delete</a>
				<a href="<?php echo  site_url($this->config->item('admin_folder').'/locations/zone_area_form/'.$zone->id.'/'.$location->id); ?>">Edit</a>
			</td>
	  </tr>
<?php endforeach; ?>
<?php if(count($areas) == 0):?>
		<tr>
			<td colspan="2">
				There are no Zone Areas for the requested Zone. Would you like to <a href="<?php echo site_url($this->config->item('admin_folder').'/locations/zone_area_form/'.$zone->id);?>" >add  a new Zone Area</a>?
			<td>
		</tr>
<?php endif;?>
	</tbody>
</table>
<?php include('footer.php');