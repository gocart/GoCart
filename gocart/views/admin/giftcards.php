<?php include('header.php'); ?>
<script type="text/javascript">
function areyousure()
{
	return confirm('<?php echo lang('confirm_delete_giftcard');?>');
}
</script>

<div class="button_set">
<?php if ($gift_cards['enabled']):?>

	<a href="<?php echo site_url($this->config->item('admin_folder').'/giftcards/form'); ?>"><?php echo lang('add_giftcard')?></a>
	
	<a href="<?php echo site_url($this->config->item('admin_folder').'/giftcards/settings'); ?>"><?php echo lang('settings');?></a>
	
	<a href="<?php echo site_url($this->config->item('admin_folder').'/giftcards/disable'); ?>"><?php echo lang('disable_giftcards');?></a>
	
<?php else: ?>
	<a href="<?php echo site_url($this->config->item('admin_folder').'/giftcards/enable'); ?>"><?php echo lang('enable_giftcards');?></a>
<?php endif; ?>
</div>
<table class="gc_table" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="gc_cell_left"><?php echo lang('code');?></th>
			<th><?php echo lang('to');?></th>
			<th><?php echo lang('from');?></th>
			<th><?php echo lang('total');?></th>
			<th><?php echo lang('used');?></th>
			<th><?php echo lang('remaining');?></th>
			<th class="gc_cell_right"></th>
		</tr>
	</thead>
	<tbody>
		<?php echo (count($cards) < 1)?'<tr><td style="text-align:center;" colspan="7">'.lang('no_giftcards').'</td></tr>':''?>
<?php foreach ($cards as $card):?>
		<tr>
			<td><?php echo  $card['code']; ?></td>
			<td><?php echo  $card['to_name']; ?></td>
			<td><?php echo  $card['from']; ?></td>
			<td><?php echo (float) $card['beginning_amount'];?></td>
			<td><?php echo (float) $card['amount_used']; ?></td>
			<td><?php echo (float) $card['beginning_amount'] - (float) $card['amount_used']; ?></td>
			<td class="list_buttons">
				<a href="<?php echo site_url($this->config->item('admin_folder').'/giftcards/delete/'.$card['id']); ?>" onclick="return areyousure();"><?php echo lang('delete');?></a>
	  </tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php include('footer.php'); ?>
