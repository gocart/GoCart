<?php include('header.php');?>

<script type="text/javascript">

$(window).ready(function(){
	$('#iframe_uploader', window.parent.document).height($('body').height());	
});


<?php if($file_name):?>
	parent.add_product_image('<?php echo $file_name;?>');
<?php endif;?>

</script>

<?php if (!empty($error)): ?>
	<div class="alert alert-error">
		<a class="close" data-dismiss="alert">×</a>
		<?php echo $error; ?>
	</div>
<?php endif; ?>

<div class="row-fluid">
	<div class="span12">
		<?php echo form_open_multipart(ADMIN_AREA.'/products/product_image_upload', 'class="form-inline"');?>
			<?php echo form_upload(array('name'=>'userfile', 'id'=>'userfile', 'class'=>'input-file'));?> <input class="btn" type="submit" value="<?php echo lang('upload');?>" />
		</form>
	</div>
</div>

<?php include('footer.php');