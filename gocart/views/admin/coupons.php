<?php include('header.php'); ?>
<script type="text/javascript">
function areyousure()
{
	return confirm('Are you sure you want to delete this coupon?');
}
</script>
<div class="button_set">
	<a href="<?php echo site_url($this->config->item('admin_folder').'/coupons/form'); ?>">Add New Coupon</a>
</div>

<table class="gc_table" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
		  <th class="gc_cell_left">Code</th>
		  <th>Usage</th>
		  <th class="gc_cell_right"></th>
		</tr>
	</thead>
	<tbody>
	<?php echo (count($coupons) < 1)?'<tr><td style="text-align:center;" colspan="3">There are currently no coupons.</td></tr>':''?>
<?php foreach ($coupons as $coupon):?>
		<tr>
			<td><?php echo  $coupon->code; ?></td>
			<td>
			  <?php echo  $coupon->num_uses ." / ". $coupon->max_uses; ?>
			</td>
			<td class="list_buttons" >
				<a href="<?php echo site_url($this->config->item('admin_folder').'/coupons/delete/'.$coupon->id); ?>" onclick="return areyousure();">Delete</a>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/coupons/form/'.$coupon->id); ?>">Edit</a>
			</td>
	  </tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php include('footer.php'); ?>
