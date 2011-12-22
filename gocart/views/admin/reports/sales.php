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

foreach($orders as $year=>$months):?>

<table class="gc_table" cellspacing="0" cellpadding="0" style="margin-bottom:10px;">
	<thead>
		<tr>
			<?php /*<th>ID</th> uncomment this if you want it*/ ?>
			<th class="gc_cell_left"><?php echo sprintf(lang('months_of'), $year);?></th>
			<th><?php echo lang('coupon_discounts');?></th>
			<th><?php echo lang('giftcard_discounts');?></th>
			<th><?php echo lang('products');?></th>
			<th><?php echo lang('shipping');?></th>
			<th><?php echo lang('tax');?></th>
			<th class="gc_cell_right"><?php echo lang('grand_total');?></th>
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