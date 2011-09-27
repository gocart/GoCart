<?php include('header.php'); ?>
<script type="text/javascript">
function areyousure()
{
	return confirm('Are you sure you want to delete this gift card?');
}
</script>

<div class="button_set">
<?php if ($gift_cards['enabled']):?>

	<a href="<?php echo base_url(); ?><?php echo $this->config->item('admin_folder');?>/giftcards/form">Add New Gift Card</a>
	
	<a href="<?php echo  base_url(); ?><?php echo $this->config->item('admin_folder');?>/giftcards/settings">Settings</a>
	
	<a href="<?php echo  base_url(); ?><?php echo $this->config->item('admin_folder');?>/giftcards/disable">Disable Giftcards</a>
	
<?php else: ?>
	<a href="<?php echo  base_url(); ?><?php echo $this->config->item('admin_folder');?>/giftcards/enable">Enable Giftcards</a>
<?php endif; ?>
</div>
<table class="gc_table" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="gc_cell_left">Code</th>
			<th>To</th>
			<th>From</th>
			<th>Total</th>
			<th>Used</th>
			<th>Remaining</th>
			<th class="gc_cell_right"></th>
		</tr>
	</thead>
	<tbody>
		<?php echo (count($cards) < 1)?'<tr><td style="text-align:center;" colspan="3">There are currently no giftcards.</td></tr>':''?>
<?php foreach ($cards as $card):?>
		<tr>
			<td><?php echo  $card['code']; ?></td>
			<td><?php echo  $card['to_name']; ?></td>
			<td><?php echo  $card['from']; ?></td>
			<td><?php echo (float) $card['beginning_amount'];?></td>
			<td><?php echo (float) $card['amount_used']; ?></td>
			<td><?php echo (float) $card['beginning_amount'] - (float) $card['amount_used']; ?></td>
			<td class="list_buttons">
				<a href="<?php echo  base_url(); ?><?php echo $this->config->item('admin_folder'); ?>/giftcards/delete/<?php echo  $card['id']; ?>" onclick="return areyousure();">Delete</a>
	  </tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php include('footer.php'); ?>
