<?php include('header.php'); ?>

<script type="text/javascript">
function areyousure()
{
	return confirm('<?php echo lang('confirm_delete');?>');
}
</script>
<div class="btn-group pull-right">
	<a class="btn" href="<?php echo site_url($this->config->item('admin_folder').'/pages/form'); ?>"><i class="icon-plus-sign"></i> <?php echo lang('add_new_page');?></a>
	<a class="btn" href="<?php echo site_url($this->config->item('admin_folder').'/pages/link_form'); ?>"><i class="icon-plus-sign"></i> <?php echo lang('add_new_link');?></a>
</div>

<table class="table table-striped">
	<thead>
		<tr>
			<th><?php echo lang('title');?></th>
			<th></th>
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
				<td>
					<?php echo $dash.' '.$page->title; ?>
				</td>
				<td>
					<div class="btn-group pull-right">
						<?php if(!empty($page->url)): ?>
							<a class="btn" href="<?php echo site_url($GLOBALS['admin_folder'].'/pages/link_form/'.$page->id); ?>"><i class="icon-pencil"></i> <?php echo lang('edit');?></a>
							<a class="btn" href="<?php echo $page->url;?>" target="_blank"><i class="icon-play-circle"></i> <?php echo lang('follow_link');?></a>
						<?php else: ?>
							<a class="btn" href="<?php echo site_url($GLOBALS['admin_folder'].'/pages/form/'.$page->id); ?>"><i class="icon-pencil"></i> <?php echo lang('edit');?></a>
							<a class="btn" href="<?php echo site_url($page->slug); ?>" target="_blank"><i class="icon-play-circle"></i> <?php echo lang('go_to_page');?></a>
						<?php endif; ?>
						<a class="btn btn-danger" href="<?php echo site_url($GLOBALS['admin_folder'].'/pages/delete/'.$page->id); ?>" onclick="return areyousure();"><i class="icon-trash icon-white"></i> <?php echo lang('delete');?></a>
					</div>
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