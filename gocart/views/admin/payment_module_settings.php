<?php include('header.php'); ?>

<form id="settings_form" method="post" action="<?php echo secure_base_url();?><?php echo $this->config->item('admin_folder');?>/payment/settings/<?php echo $module;?>">
	<div class="button_set">
		<input type="submit" value="submit"/> <input type="button" value="cancel" onclick="window.location='<?php echo secure_base_url();?><?php echo $this->config->item('admin_folder');?>/shipping'">
	</div>
	
	<div id="gc_tabs">
		<ul>
			<li><a href="#gc_settings">Payment Settings</a></li>
		</ul>
		
		<div id="gc_settings">
<?php
echo $form;
?>
		</div>
	</div>
</form>

<?php include('footer.php'); ?>
