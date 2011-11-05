<?php include('header.php'); ?>
<script type="text/javascript">
function areyousure()
{
	return confirm('Are you sure you want to delete this Zone?');
}
</script>

<div class="button_set" style="text-align:right;">
	<a href="<?php echo site_url($this->config->item('admin_folder').'/locations/country_form');?>" >Add New Country</a>
	<a href="<?php echo site_url($this->config->item('admin_folder').'/locations/zone_form'); ?>" >Add New Zone</a>
</div>
<br/>
<table class="gc_table" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="gc_cell_left">Name</th>
			<th>Code</th>
			<th>Tax Rate</th>
			<th>Status</th>
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
				<a href="<?php echo site_url($this->config->item('admin_folder').'/locations/delete_zone/'.$location->id); ?>" onclick="return areyousure();">Delete</a>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/locations/zone_form/'.$location->id); ?>">Edit</a>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/locations/zone_areas/'.$location->id); ?>">Zone Areas</a>
			</td>
	  </tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php include('footer.php'); ?>
