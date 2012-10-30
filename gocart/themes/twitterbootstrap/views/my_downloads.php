<?php include('header.php') ?>

<?php
$counter	= 0;
if(!empty($downloads)) : ?>
	<?php foreach($downloads as $key=>$val) : ?>
	<h2><?php echo lang('order_number').': '.$key ?></h2>
	<table class="table table-bordered table-striped">
		<thead>
			<tr>
				<th><?php echo lang('filename');?></th>
				<th><?php echo lang('title');?></th>
				<th><?php echo lang('size');?></th>
				<th><?php echo lang('download_limit') ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($val as $file): ?>
		
			<tr>
				<td><?php echo $file->filename; ?></td>
				<td><?php echo $file->title; ?></td>
				<td><?php echo $file->size; ?></td>
				<?php 
						$max_exc = false;
						if($file->max_downloads==0) 
						{
							echo '<td width="10%">'.lang('no_max').'</td>';
						} else if($file->max_downloads <= $file->downloads) {
							$max_exc = true;
							echo '<td colspan="2">'.lang('max_exceeded').'</td>';
						} else {
							echo '<td width="10%" rel="';
							echo $file->max_downloads - $file->downloads;
							echo '" id="count_down_'.$counter.'">';
							echo $file->max_downloads - $file->downloads;
							echo '</td>';
						}
				?>
				<?php  if(!$max_exc) : ?>
				<td id="download_button_<?php echo $counter;?>">
					<a class="btn" onclick="count_down(<?php echo $counter;?>);" href="<?php echo site_url('secure/download/'.$file->link) ?>"><?php echo lang('download_btn') ?></a>
				</td>
				<?php endif; ?>
			</tr>
		<?php $counter++;
		 endforeach; // end foreach val ?>
		</tbody>
	</table>
	<?php endforeach; // end foreach downloads ?>
<?php else: ?>
	<div class="alert">
	  <?php echo lang('no_downloads');?>
	</div>
<?php endif;?>

<script type="text/javascript">

function count_down(key){
	if($('#count_down_'+key).length > 0)
	{
		var count	= $('#count_down_'+key).attr('rel');
		count		= parseInt(count)-1;
		
		if(count <= 0)
		{
			$('#count_down_'+key).html('<?php echo lang('max_exceeded');?>').attr('colspan', 2);
			$('#download_button_'+key).remove();
			
		}
		else
		{
			$('#count_down_'+key).attr('rel', count);
			$('#count_down_'+key).html(count);
		}
	}
}

</script>

<?php include('footer.php'); ?>
