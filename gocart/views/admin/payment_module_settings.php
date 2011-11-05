<?php include('header.php'); ?>

<form id="settings_form" method="post" action="<?php echo site_url($this->config->item('admin_folder').'/payment/settings/'. $module);?>">
	<div class="button_set">
		<input type="submit" value="submit"/>
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
