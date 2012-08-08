<table class="table table-striped" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<?php /*<th>ID</th> uncomment this if you want it*/ ?>
			<th><?php echo lang('sku');?></th>
			<th><?php echo lang('name');?></th>
			<th><?php echo lang('quantity');?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($best_sellers as $b):?>
		<tr>
			<?php /*<td style="width:16px;"><?php echo  $customer->id; ?></td>*/?>
			<td><?php echo  $b->sku; ?></td>
			<td><?php echo  $b->name; ?></td>
			<td><?php echo $b->quantity_sold; ?></a></td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>