<?php include('header.php'); ?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	create_sortable();	
});
// Return a helper with preserved width of cells
var fixHelper = function(e, ui) {
	ui.children().each(function() {
		$(this).width($(this).width());
	});
	return ui;
};
function create_sortable()
{
	$('#countries').sortable({
		scroll: true,
		helper: fixHelper,
		axis: 'y',
		handle:'.handle',
		update: function(){
			save_sortable();
		}
	});	
	$('#countries').sortable('enable');
}

function save_sortable()
{
	serial=$('#countries').sortable('serialize');
			
	$.ajax({
		url:'<?php echo site_url($this->config->item('admin_folder').'/locations/organize_countries');?>',
		type:'POST',
		data:serial
	});
}
function areyousure()
{
	return confirm('<?php echo lang('confirm_delete_country');?>');
}
//]]>
</script>

<div class="btn-group" style="float:right;">
	<a class="btn" href="<?php echo site_url($this->config->item('admin_folder').'/locations/country_form'); ?>"><i class="icon-plus-sign"></i> <?php echo lang('add_new_country');?></a>
	<a class="btn" href="<?php echo site_url($this->config->item('admin_folder').'/locations/zone_form'); ?>"><i class="icon-plus-sign"></i> <?php echo lang('add_new_zone');?></a>
</div>


<strong style="float:left;"><?php echo lang('sort_countries')?></strong>

<table class="table table-striped" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th><?php echo lang('sort');?></th>
			<th><?php echo lang('name');?></th>
			<th><?php echo lang('iso_code_2');?></th>
			<th><?php echo lang('iso_code_3');?></th>
			<th><?php echo lang('tax');?></th>
			<th><?php echo lang('status');?></th>
			<th></th>
		</tr>
	</thead>
	<tbody id="countries">
<?php foreach ($locations as $location):?>
		<tr id="country-<?php echo $location->id;?>">
			<td class="handle"><a class="btn" style="cursor:move"><span class="icon-align-justify"></span></a></td>
			<td><?php echo  $location->name; ?></td>
			<td><?php echo $location->iso_code_2;?></td>
			<td><?php echo $location->iso_code_3;?></td>
			<td><?php echo $location->tax+0;?>%</td>
			<td><?php echo ((bool)$location->status)?'enabled':'disabled';?></td>
			<td>
				<div class="btn-group" style="float:right;">
					<a class="btn" href="<?php echo site_url($this->config->item('admin_folder').'/locations/country_form/'.$location->id); ?>"><i class="icon-pencil"></i> <?php echo lang('edit');?></a>
					<a class="btn" href="<?php echo site_url($this->config->item('admin_folder').'/locations/zones/'.$location->id); ?>"><i class="icon-map-marker"></i> <?php echo lang('zones');?></a>
					<a class="btn btn-danger" href="<?php echo site_url($this->config->item('admin_folder').'/locations/delete_country/'.$location->id); ?>" onclick="return areyousure<();"><i class="icon-trash icon-white"></i> <?php echo lang('delete');?></a>
				</div>
			</td>
	  </tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php include('footer.php'); ?>
