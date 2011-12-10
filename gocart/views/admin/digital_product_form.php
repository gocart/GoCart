<?php include('header.php');

echo form_open_multipart($this->config->item('admin_folder').'/digital_products/form/'.$id, 'id="product_form"'); ?>
<div class="button_set">
	<input name="submit" type="submit" value="Save Product" />
</div>

<div id="gc_tabs">
	<ul>
		<li><a href="#gc_product_info"><?php echo lang('attributes');?></a></li>
	</ul>
	<div id="gc_product_info">
		
		<?php if($id==0) : ?>
		<div class="gc_field">
			<label for="file"><?php echo lang('file_label');?> </label>
			<?php 	$data = array('id'=>'file', 'name'=>'userfile');
					echo form_upload($data);
			?>
		</div>
		<?php else : ?>
		<div class="gc_field">
			<label for="file"><?php echo lang('file_label');?> </label>
			<?php echo $filename ?>
		</div>
		<?php endif; ?>
		
		<div class="gc_field">
		<label for="title"><?php echo lang('title');?> </label>
		<?php
		$data	= array('id'=>'title', 'name'=>'title', 'value'=>set_value('title', $title), 'class'=>'gc_tf1');
		echo form_input($data);
		?>
		</div>
			
		<label for="description"><?php echo lang('desc');?> </label>
		<div class="gc_field gc_tinymce">
		<?php
		$data	= array('id'=>'description', 'name'=>'description', 'class'=>'tinyMCE', 'value'=>set_value('description', $description));
		echo form_textarea($data);
		?>
		</div>
		<div class="button_set">
			<input type="button" onclick="toggleEditor('description'); return false;" value="Toggle WYSIWYG" />
		</div>
	</div>
</div>