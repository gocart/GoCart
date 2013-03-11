<?php include('header.php');?>
<?php if($this->admin_session->userdata('admin')): ?>
						<a class="btn" title="Edit Page" href="<?php echo  site_url($this->config->item('admin_folder').'/pages/form/'.$page->id); ?>"><i class="icon-pencil"></i></a>
						<?php endif; ?>
<?php echo html_entity_decode($page->content); ?>
<?php include('footer.php');?>