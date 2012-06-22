<?php include('header.php'); ?>
<script type="text/javascript">
function areyousure()
{
	return confirm('<?php echo lang('confirm_delete_zone');?>');
}
</script>

<div class="btn-group" style="float:right;">
	<a class="btn" href="<?php echo site_url($this->config->item('admin_folder').'/locations/country_form'); ?>"><i class="icon-plus-sign"></i> <?php echo lang('add_new_country');?></a>
	<a class="btn" href="<?php echo site_url($this->config->item('admin_folder').'/locations/zone_form'); ?>"><i class="icon-plus-sign"></i> <?php echo lang('add_new_zone');?></a>
</div>

<table class="table table-striped">
	<thead>
		<tr>
			<th><?php echo lang('name');?></th>
			<th><?php echo lang('code');?></th>
			<th><?php echo lang('tax');?></th>
			<th><?php echo lang('status');?></th>
			<th class="gc_cell_right"></th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($zones as $location):?>
		<tr>
			<td class="gc_cell_left"><?php echo  $location->name; ?></td>
			<td><?php echo $location->code;?></td>
			<td><?php echo $location->tax+0;?>%</td>
			<td><?php echo ((bool)$location->status)?'enabled':'disabled';?></td>
			<td>
				<div class="btn-group" style="float:right;">
					<a class="btn" href="<?php echo site_url($this->config->item('admin_folder').'/locations/zone_form/'.$location->id); ?>"><i class="icon-pencil"></i> <?php echo lang('edit');?></a>
					<a class="btn" href="<?php echo site_url($this->config->item('admin_folder').'/locations/zone_areas/'.$location->id); ?>"><i class="icon-map-marker"></i> <?php echo lang('zone_areas');?></a>
					<a class="btn btn-danger" href="<?php echo site_url($this->config->item('admin_folder').'/locations/delete_zone/'.$location->id); ?>" onclick="return areyousure();"><i class="icon-trash icon-white"></i> <?php echo lang('delete');?></a>
				</div>
			</td>
	  </tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php include('footer.php'); ?>
