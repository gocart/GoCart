<?php include('header.php'); ?>
<script type="text/javascript">
function areyousure()
{
	return confirm('<?php echo lang('confirm_delete_zone_area');?>');
}
</script>

<div class="btn-group" style="float:right;">
	<a class="btn" href="<?php echo site_url($this->config->item('admin_folder').'/locations/country_form'); ?>"><i class="icon-plus-sign"></i> <?php echo lang('add_new_country');?></a>
	<a class="btn" href="<?php echo site_url($this->config->item('admin_folder').'/locations/zone_form'); ?>"><i class="icon-plus-sign"></i> <?php echo lang('add_new_zone');?></a>
	<a class="btn" href="<?php echo site_url($this->config->item('admin_folder').'/locations/zone_area_form/'.$zone->id);?>"><i class="icon-plus-sign"></i> <?php echo lang('add_new_zone_area');?></a>
</div>

<table class="table table-striped" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th><?php echo lang('code');?></th>
			<th><?php echo lang('tax');?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($areas as $location):?>
		<tr>
			<td><?php echo  $location->code; ?></td>
			<td><?php echo $location->tax+0;?>%</td>
			<td>
				<div class="btn-group" style="float:right;">
					<a class="btn" href="<?php echo  site_url($this->config->item('admin_folder').'/locations/zone_area_form/'.$zone->id.'/'.$location->id); ?>"><i class="icon-pencil"></i> <?php echo lang('edit');?></a>
					<a class="btn btn-danger" href="<?php echo  site_url($this->config->item('admin_folder').'/locations/delete_zone_area/'.$location->id); ?>" onclick="return areyousure();"><i class="icon-trash icon-white"></i> <?php echo lang('delete');?></a>
				</div>
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