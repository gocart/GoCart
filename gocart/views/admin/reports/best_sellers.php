<table class="gc_table" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<?php /*<th>ID</th> uncomment this if you want it*/ ?>
			<th class="gc_cell_left">SKU</th>
			<th>Name</th>
			<th class="gc_cell_right">Quantity</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($best_sellers as $b):?>
		<tr class="gc_row">
			<?php /*<td style="width:16px;"><?php echo  $customer->id; ?></td>*/?>
			<td class="gc_cell_left"><?php echo  $b->sku; ?></td>
			<td><?php echo  $b->name; ?></td>
			<td class="gc_cell_right"><?php echo $b->quantity_sold; ?></a></td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>