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
		handle:'.handle',
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
		url:'<?php echo site_url($this->config->item('admin_folder').'/banners/organize');?>',
		type:'POST',
		data:serial
	});
}
function areyousure()
{
	return confirm('<?php echo lang('confirm_delete_banner');?>');
}
//]]>
</script>




<a style="float:right;" class="btn" href="<?php echo site_url($this->config->item('admin_folder').'/banners/form'); ?>"><i class="icon-plus-sign"></i> <?php echo lang('add_new_banner');?></a>

<strong style="float:left;"><?php echo lang('sort_banners')?></strong>

<table class="table table-striped">
	<thead>
		<tr>
			<th><?php echo lang('sort');?></th>
			<th><?php echo lang('title');?></th>
			<th><?php echo lang('enable_on');?></th>
			<th><?php echo lang('disable_on');?></th>
			<th></th>
		</tr>
	</thead>
	<?php echo (count($banners) < 1)?'<tr><td style="text-align:center;" colspan="5">'.lang('no_banners').'</td></tr>':''?>
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
			<td class="handle"><a class="btn" style="cursor:move"><span class="icon-align-justify"></span></a></td>
			<td><?php echo $disabled_icon.$banner->title;?></td>
			<td><?php echo $enable;?></td>
			<td><?php echo $disable;?></td>
			<td>
				<div class="btn-group" style="float:right">
					<a class="btn" href="<?php echo  site_url($this->config->item('admin_folder').'/banners/form/'.$banner->id);?>"><i class="icon-pencil"></i> <?php echo lang('edit');?></a>
					<a class="btn btn-danger" href="<?php echo  site_url($this->config->item('admin_folder').'/banners/delete/'.$banner->id);?>" onclick="return areyousure();"><i class="icon-trash icon-white"></i> <?php echo lang('delete');?></a>
				</div>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
	<?php endif;?>
</table>

<?php include('footer.php');