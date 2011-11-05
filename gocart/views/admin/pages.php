<?php include('header.php'); ?>

<script type="text/javascript">
function areyousure()
{
	return confirm('Are you sure you want to delete this page?');
}
</script>
<div class="button_set">
	<a href="<?php echo site_url($this->config->item('admin_folder').'/pages/form'); ?>">Add New Page</a>
	<a href="<?php echo site_url($this->config->item('admin_folder').'/pages/link_form'); ?>">Add New Link</a>
</div>

<table class="gc_table" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			
			<th class="gc_cell_left">Title</th>
			<th class="gc_cell_right"></th>
		</tr>
	</thead>
	
	<?php echo (count($pages) < 1)?'<tr><td style="text-align:center;" colspan="2">There are currently no pages.</td></tr>':''?>
	<?php if($pages):?>
	<tbody>
		
		<?php
		$GLOBALS['admin_folder'] = $this->config->item('admin_folder');
		function page_loop($pages, $dash = '')
		{
			foreach($pages as $page)
			{?>
			<tr class="gc_row">
				<td class="gc_cell_left">
					<?php echo $dash.' '.$page->title; ?>
				</td>
				<td class="gc_cell_right list_buttons">
					<a href="<?php echo  base_url(); ?><?php echo $GLOBALS['admin_folder'];?>/pages/delete/<?php echo  $page->id; ?>" onclick="return areyousure();">Delete</a>

					
					
					<?php if(empty($page->content)): ?>
						<a href="<?php echo site_url($GLOBALS['admin_folder'].'/pages/link_form/'.$page->id); ?>">Edit</a>
						<a href="<?php echo $page->url;?>" target="_blank">Follow Link</a>
					<?php else: ?>
						<a href="<?php echo site_url($GLOBALS['admin_folder'].'/pages/form/'.$page->id); ?>">Edit</a>
						<a href="<?php echo site_url($page->slug); ?>" target="_blank">Go to Page</a>
					<?php endif; ?>
						
				</td>
			</tr>
			<?php
			page_loop($page->children, $dash.'-');
			}
		}
		page_loop($pages);
		?>
	</tbody>
	<?php endif;?>
</table>
<?php include('footer.php'); ?>