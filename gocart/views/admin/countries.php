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
		url:'/<?php echo $this->config->item('admin_folder');?>/locations/organize_countries',
		type:'POST',
		data:serial
	});
}
function areyousure()
{
	return confirm('Are you sure you want to delete this Country?');
}
//]]>
</script>

<div class="button_set" style="text-align:right;">
	<strong style="float:left; font-size:12px;">Countries are sortable! Just drag and drop them in the order you would like for them to appear.</strong>
	<a href="<?php echo  base_url(); ?><?php echo $this->config->item('admin_folder'); ?>/locations/country_form" >Add New Country</a>
	<a href="<?php echo  base_url(); ?><?php echo $this->config->item('admin_folder'); ?>/locations/zone_form" >Add New Zone</a>
</div>
<br/>
<table class="gc_table" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="gc_cell_left">Name</th>
			<th>ISO 2</th>
			<th>ISO 3</th>
			<th>Tax Rate</th>
			<th>Status</th>
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
				<a href="<?php echo  base_url(); ?><?php echo $this->config->item('admin_folder'); ?>/locations/delete_country/<?php echo  $location->id; ?>" onclick="return areyousure();">Delete</a>
				<a href="<?php echo  base_url(); ?><?php echo $this->config->item('admin_folder'); ?>/locations/country_form/<?php echo  $location->id; ?>">Edit</a>
				<a href="<?php echo  base_url(); ?><?php echo $this->config->item('admin_folder'); ?>/locations/zones/<?php echo  $location->id; ?>">Zones</a>
			</td>
	  </tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php include('footer.php'); ?>
