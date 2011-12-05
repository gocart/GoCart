<?php include('header.php'); ?>

<?php echo form_open($this->config->item('admin_folder').'/settings/canned_message_form/'.$id); ?>

<div class="button_set">
	<input type="submit" value="<?php echo lang('save_canned_message');?>"/>
</div>

<div id="gc_tabs">
	<ul>
		<li><a href="#gc_message_info"><?php echo lang('tab_canned_info');?></a></li>
	</ul>
	<div id="gc_message_info">
		<div class="gc_field2">
			<label for="name"><?php echo lang('label_canned_name');?> </label>
			<input class="gc_tf1" type="text" name="name" value="<?php echo  set_value('name', $name); ?>" size="40" <?php if(!$deletable) { ?> style=" background-color:#f2f2f2;" readonly="readonly" <?php } ?>/>
		</div>

		<div class="gc_field2">
			<label for="subject"><?php echo lang('label_canned_subject');?> </label>
			<input class="gc_tf1" type="text" name="subject" value="<?php echo  set_value('subject', $subject); ?>" size="40" />
		</div>
		<div class="gc_field gc_tinymce">
			<?php
			$data	= array('id'=>'description', 'name'=>'content', 'class'=>'tinyMCE', 'value'=>set_value('content', $content));
			echo form_textarea($data);
			?>
		</div>
	</div>
</div>

</form>
<script type="text/javascript">
	$(document).ready(function() {
		$("#gc_tabs").tabs();
	});
</script>
<?php include('footer.php');