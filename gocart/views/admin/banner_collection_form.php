
<?php
$name			= array('name'=>'name', 'value' => set_value('name', $name));
?>

<?php echo form_open_multipart(config_item('admin_folder').'/banners/banner_collection_form/'.$banner_collection_id); ?>
	<label for="title"><?php echo lang('name');?> </label>
	<?php echo form_input($name); ?>

	<div class="form-actions">
		<input class="btn btn-primary" type="submit" value="<?php echo lang('save');?>"/>
	</div>
</form>