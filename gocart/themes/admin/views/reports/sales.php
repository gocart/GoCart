<?php

$m	= Array(
lang('january')
,lang('february')
,lang('march')
,lang('april')
,lang('may')
,lang('june')
,lang('july')
,lang('august')
,lang('september')
,lang('october')
,lang('november')
,lang('december')
);
?>

<table class="table table-striped">
	<thead>
		<tr>
			<?php /*<th>ID</th> uncomment this if you want it*/ ?>
			<th><?php echo lang('date');?></th>
			<th><?php echo lang('coupon_discounts');?></th>
			<th><?php echo lang('gift_card_discounts');?></th>
			<th><?php echo lang('products');?></th>
			<th><?php echo lang('shipping');?></th>
			<th><?php echo lang('tax');?></th>
			<th><?php echo lang('grand_total');?></th>
		</tr>
	</thead>
	<tbody>
<?php foreach($orders as $month):?>
		<tr>
			<td><?php echo $m[intval($month->month)-1].' '.$month->year;?></td>
			<td><?php echo format_currency($month->coupon_discounts);?></td>
			<td><?php echo format_currency($month->gift_card_discounts);?></td>
			<td><?php echo format_currency($month->product_totals);?></td>
			<td><?php echo format_currency($month->shipping);?></td>
			<td><?php echo format_currency($month->tax);?></td>
			<td><?php echo format_currency($month->total);?></td>
		</tr>
<?php endforeach;?>
	</tbody>
</table>

