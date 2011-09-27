<?php

$m	= Array(
'January'
,'February'
,'March'
,'April'
,'May'
,'June'
,'July'
,'August'
,'September'
,'October'
,'November'
,'December'
);

foreach($orders as $year=>$months):?>

<table class="gc_table" cellspacing="0" cellpadding="0" style="margin-bottom:10px;">
	<thead>
		<tr>
			<?php /*<th>ID</th> uncomment this if you want it*/ ?>
			<th class="gc_cell_left">Months of <?php echo $year?></th>
			<th>Coupon Discounts</th>
			<th>Gift Card Discounts</th>
			<th>Products</th>
			<th>Shipping</th>
			<th>Tax</th>
			<th class="gc_cell_right">Grand Total</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($months as $month=>$totals):?>
		<tr class="gc_row">
			<td class="gc_cell_left"><?php echo $m[$month-1];?></td>
			<td><?php echo format_currency($totals['coupon_discounts']);?></td>
			<td><?php echo format_currency($totals['gift_card_discounts']);?></td>
			<td><?php echo format_currency($totals['product_totals']);?></td>
			<td><?php echo format_currency($totals['shipping']);?></td>
			<td><?php echo format_currency($totals['tax']);?></td>
			<td class="gc_cell_right"><?php echo format_currency($totals['total']);?></td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>

<?php endforeach;?>