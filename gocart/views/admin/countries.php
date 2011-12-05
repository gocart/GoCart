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

<div class="button_set" style="text-align:right;">
	<strong style="float:left; font-size:12px;"><?php echo lang('sort_countries')?></strong>
	<a href="<?php echo site_url($this->config->item('admin_folder').'/locations/country_form'); ?>"><?php echo lang('add_new_country');?></a>
	<a href="<?php echo site_url($this->config->item('admin_folder').'/locations/zone_form'); ?>"><?php echo lang('add_new_zone');?></a>
</div>
<br/>
<table class="gc_table" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="gc_cell_left"><?php echo lang('name');?></th>
			<th><?php echo lang('iso_code_2');?></th>
			<th><?php echo lang('iso_code_3');?></th>
			<th><?php echo lang('tax');?></th>
			<th><?php echo lang('status');?></th>
			<th class="gc_cell_right"></th>
		</tr>
	</thead>
	<tbody id="countries">
<?php foreach ($locations as $location):?>
		<tr id="country-<?php echo $location->id;?>">
			<td><?php echo  $location->name; ?></td>
			<td><?php echo $location->iso_code_2;?></td>
			<td><?php echo $location->iso_code_3;?></td>
			<td><?php echo $location->tax+0;?>%</td>
			<td><?php echo ((bool)$location->status)?'enabled':'disabled';?></td>
			<td class="gc_cell_right list_buttons" >
				<a href="<?php echo site_url($this->config->item('admin_folder').'/locations/delete_country/'.$location->id); ?>" onclick="return areyousure();"><?php echo lang('delete');?></a>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/locations/country_form/'.$location->id); ?>"><?php echo lang('edit');?></a>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/locations/zones/'.$location->id); ?>"><?php echo lang('zones');?></a>
			</td>
	  </tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php include('footer.php'); ?>
