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
		<div class="gc_field2">
			<label for="file"><?php echo lang('file_label');?> </label>
			<?php 	$data = array('id'=>'file', 'name'=>'userfile');
					echo form_upload($data);
			?>
		</div>
		<?php else : ?>
		<div class="gc_field2">
			<label for="file"><?php echo lang('file_label');?> </label>
			<?php echo $filename ?>
		</div>
		<?php endif; ?>
		
		<div class="gc_field2">
		<label for="title"><?php echo lang('title');?> </label>
		<?php
		$data	= array('id'=>'title', 'name'=>'title', 'value'=>set_value('title', $title), 'class'=>'gc_tf1');
		echo form_input($data);
		?>
		</div>
		
		<div class="gc_field2">
		<label for="title"><?php echo lang('max_downloads');?> </label>
		<?php
		$data	= array('id'=>'max_downloads', 'name'=>'max_downloads', 'value'=>set_value('max_downloads', $max_downloads), 'class'=>'gc_tf1');
		echo form_input($data);
		?>
		<small><?php echo lang('max_downloads_note');?></small>
		</div>
			
		</div>
</div>