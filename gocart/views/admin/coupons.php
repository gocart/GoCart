<?php include('header.php'); ?>
<script type="text/javascript">
function areyousure()
{
	return confirm('<?php echo lang('confirm_delete_coupon');?>');
}
</script>
<div class="button_set">
	<a href="<?php echo site_url($this->config->item('admin_folder').'/coupons/form'); ?>"><?php echo lang('add_new_coupon');?></a>
</div>

<table class="gc_table" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
		  <th class="gc_cell_left"><?php echo lang('code');?></th>
		  <th><?php echo lang('usage');?></th>
		  <th class="gc_cell_right"></th>
		</tr>
	</thead>
	<tbody>
	<?php echo (count($coupons) < 1)?'<tr><td style="text-align:center;" colspan="3">'.lang('no_coupons').'</td></tr>':''?>
<?php foreach ($coupons as $coupon):?>
		<tr>
			<td><?php echo  $coupon->code; ?></td>
			<td>
			  <?php echo  $coupon->num_uses ." / ". $coupon->max_uses; ?>
			</td>
			<td class="list_buttons" >
				<a href="<?php echo site_url($this->config->item('admin_folder').'/coupons/delete/'.$coupon->id); ?>" onclick="return areyousure();"><?php echo lang('delete');?></a>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/coupons/form/'.$coupon->id); ?>"><?php echo lang('edit');?></a>
			</td>
	  </tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php include('footer.php'); ?>
