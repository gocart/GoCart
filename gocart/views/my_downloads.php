<?php include('header.php') ?>
<script>
$(function(){
	$('.button').button();
});
</script>
<div style="text-align:center;">
<?php 	
if(!empty($downloads)) : ?>
	<table class="cart_table" cellpadding="0" cellspacing="0" border="0">
		<thead>
			<tr>
				<th class="product_info"><?php echo lang('filename');?></th>
				<th><?php echo lang('title');?></th>
				<th><?php echo lang('size');?></th>
				<th><?php echo lang('download_limit') ?></th>
				<th></th>
			</tr>
		</thead>
		<?php foreach($downloads as $k=>$d) : ?>
	
			<tbody class="cart_items" style="text-align:left;">
			<tr class="cart_spacer">
					<td colspan="5"><?php echo lang('order_number').': '.$k ?></td>
				</tr>
			<?php foreach($d as $file): ?>
				
				<tr class="cart_item">
					<td><?php echo $file->filename; ?></td>
					<td><?php echo $file->title; ?></td>
					<td width="10%"><?php echo $file->size; ?></td>
					<td width="10%"><?php 
							$max_exc = false;
							if($file->max_downloads==0) 
							{
								echo lang('no_max');
							} else if($file->max_downloads <= $file->downloads) {
								$max_exc = true;
								echo lang('max_exceeded');
							} else {
								echo $file->max_downloads - $file->downloads;
							}
					  ?> </td>
					<td align="right" width="10%">
						<?php  if(!$max_exc) : ?>
						<a class="button" href="<?php echo site_url('secure/download/'.$file->link) ?>"><?php echo lang('download_btn') ?></a>
						<?php endif; ?>
					</td>
				</tr>
				
			<?php endforeach; // end foreach d ?>
			</tbody>
		<?php endforeach; // end foreach downloads ?>
	</table>
<?php else: ?>
	<?php echo lang('no_downloads');?>
<?php endif;?>
<?php include('footer.php'); ?>
