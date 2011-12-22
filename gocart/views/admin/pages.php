<?php include('header.php'); ?>

<script type="text/javascript">
function areyousure()
{
	return confirm('<?php echo lang('confirm_delete');?>');
}
</script>
<div class="button_set">
	<a href="<?php echo site_url($this->config->item('admin_folder').'/pages/form'); ?>"><?php echo lang('add_new_page');?></a>
	<a href="<?php echo site_url($this->config->item('admin_folder').'/pages/link_form'); ?>"><?php echo lang('add_new_link');?></a>
</div>

<table class="gc_table" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			
			<th class="gc_cell_left"><?php echo lang('title');?></th>
			<th class="gc_cell_right"></th>
		</tr>
	</thead>
	
	<?php echo (count($pages) < 1)?'<tr><td style="text-align:center;" colspan="2">'.lang('no_pages_or_links').'</td></tr>':''?>
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
					<a href="<?php echo site_url($GLOBALS['admin_folder'].'/pages/delete/'.$page->id); ?>" onclick="return areyousure();"><?php echo lang('delete');?></a>
					
					<?php if(empty($page->content)): ?>
						<a href="<?php echo site_url($GLOBALS['admin_folder'].'/pages/link_form/'.$page->id); ?>"><?php echo lang('edit');?></a>
						<a href="<?php echo $page->url;?>" target="_blank"><?php echo lang('follow_link');?></a>
					<?php else: ?>
						<a href="<?php echo site_url($GLOBALS['admin_folder'].'/pages/form/'.$page->id); ?>"><?php echo lang('edit');?></a>
						<a href="<?php echo site_url($page->slug); ?>" target="_blank"><?php echo lang('go_to_page');?></a>
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
<?php include('footer.php');