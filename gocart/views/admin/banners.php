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
	$('#banners_sortable').sortable({
		scroll: true,
		helper: fixHelper,
		axis: 'y',
		update: function(){
			save_sortable();
		}
	});	
	$('#banners_sortable').sortable('enable');
}

function save_sortable()
{
	serial=$('#banners_sortable').sortable('serialize');
			
	$.ajax({
		url:'/<?php echo $this->config->item('admin_folder');?>/banners/organize',
		type:'POST',
		data:serial
	});
}
function areyousure()
{
	return confirm('Are you sure you want to delete this banner?');
}
//]]>
</script>



<div class="button_set">
	<a href="<?php echo  base_url(); ?><?php echo $this->config->item('admin_folder');?>/banners/form" >Add New Banner</a>
</div>


<table class="gc_table" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="gc_cell_left">Title</th>
			<th>Enable On</th>
			<th>Disable On</th>
			<th class="gc_cell_right"></th>
		</tr>
	</thead>
	<?php echo (count($banners) < 1)?'<tr><td style="text-align:center;" colspan="3">There are currently no banners.</td></tr>':''?>
	<?php if ($banners): ?>
	<tbody id="banners_sortable">
	<?php

	foreach ($banners as $banner):

		//clear the dates out if they're all zeros
		if ($banner->enable_on == '0000-00-00')
		{
			$enable_test	= false;
			$enable			= '';
		}
		else
		{
			$eo			 	= explode('-', $banner->enable_on);
			$enable_test	= $eo[0].$eo[1].$eo[2];
			$enable			= $eo[1].'-'.$eo[2].'-'.$eo[0];
		}

		if ($banner->disable_on == '0000-00-00')
		{
			$disable_test	= false;
			$disable		= '';
		}
		else
		{
			$do			 	= explode('-', $banner->disable_on);
			$disable_test	= $do[0].$do[1].$do[2];
			$disable		= $do[1].'-'.$do[2].'-'.$do[0];
		}


		$disabled_icon	= '';
		$curDate		= date('Ymd');

		if (($enable_test && $enable_test > $curDate) || ($disable_test && $disable_test <= $curDate))
		{
			$disabled_icon	= '<span style="color:#ff0000;">&bull;</span> ';
		}
		?>
		<tr id="banners-<?php echo $banner->id;?>">
			<td><?php echo $disabled_icon.$banner->title;?></td>
			<td><?php echo $enable;?></td>
			<td><?php echo $disable;?></td>
			<td class="gc_cell_right list_buttons">
				<a href="<?php echo  base_url(); ?><?php echo $this->config->item('admin_folder');?>/banners/delete/<?php echo  $banner->id; ?>" onclick="return areyousure();" >Delete</a>
				<a href="<?php echo  base_url(); ?><?php echo $this->config->item('admin_folder');?>/banners/form/<?php echo  $banner->id; ?>">Edit</a>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
	<?php endif;?>
</table>

<?php include('footer.php'); ?>
