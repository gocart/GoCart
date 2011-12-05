<?php include('header.php'); ?>

<?php echo form_open_multipart($this->config->item('admin_folder').'/categories/form/'.$id); ?>

<div class="button_set">
	<input type="submit" value="<?php echo lang('form_save');?>" />
</div>
<div id="gc_tabs">
	<ul>
		<li><a href="#gc_category_info"><?php echo lang('description');?></a></li>
		<li><a href="#gc_category_attributes"><?php echo lang('attributes');?></a></li>
		<li><a href="#gc_product_seo"><?php echo lang('seo');?></a></li>
	</ul>
	
	<div id="gc_category_info">
		<div class="gc_field">
		<?php
		$data	= array('id'=>'name', 'name'=>'name', 'value'=>set_value('name', $name), 'class'=>'gc_tf1');
		echo form_input($data);
		?>
		</div>
		
		<div class="gc_field gc_tinymce">
		<?php
		$data	= array('id'=>'description', 'name'=>'description', 'class'=>'tinyMCE', 'value'=>set_value('description', $description));
		echo form_textarea($data);
		?>
		</div>
		<div class="button_set">
			<input type="button" onclick="toggleEditor('description'); return false;" value="<?php echo lang('toggle_wysiwyg');?>" />
		</div>
	</div>
	<div id="gc_category_attributes">
		
		<div class="gc_field2">
			<label for="slug"><?php echo lang('slug');?> </label>
			<?php
			$data	= array('id'=>'slug', 'name'=>'slug', 'value'=>set_value('slug', $slug), 'class'=>'gc_tf1');
			echo form_input($data);
			?>
		</div>
		<div class="gc_field2">
			<label for="slug"><?php echo lang('sequence');?> </label>
			<?php
			$data	= array('id'=>'sequence', 'name'=>'sequence', 'value'=>set_value('sequence', $sequence), 'class'=>'gc_tf1');
			echo form_input($data);
			?>
		</div>
		
		<div class="gc_field2">
			<label for="slug"><?php echo lang('parent');?> </label>
			<?php
			$data	= array(0 => 'Top Level Category');
			foreach($categories as $parent)
			{
				if($parent->id != $id)
				{
					$data[$parent->id] = $parent->name;
				}
			}
			echo form_dropdown('parent_id', $data, $parent_id);
			?>
		</div>
		<div class="gc_field">
		<label for="excerpt"><?php echo lang('excerpt');?> </label>
			<?php
			$data	= array('id'=>'excerpt', 'name'=>'excerpt', 'value'=>set_value('excerpt', $excerpt), 'class'=>'gc_tf1');
			echo form_textarea($data);
			?>
		</div>
		<div class="gc_field">
		
		<label for="image"><?php echo lang('image');?> </label><small><?php echo lang('max_file_size');?> <?php echo  $this->config->item('size_limit')/1024; ?>kb</small>
			<?php echo form_upload(array('name'=>'image', 'id'=>'image'));?> <br/>
		<?php if($id && $image != ''):?>
		<div style="text-align:center; padding:5px; border:1px solid #ccc;"><img src="<?php echo base_url('uploads/images/small/'.$image);?>" alt="current"/><br/><?php echo lang('current_file');?></div>
		<?php endif;?>
		
		</div>
	</div>
	
	<div id="gc_product_seo">
		<div class="gc_field2">
		<label for="code"><?php echo lang('seo_title');?> </label>
		<?php
		$data	= array('id'=>'seo_title', 'name'=>'seo_title', 'value'=>set_value('seo_title', $seo_title), 'class'=>'gc_tf1');
		echo form_input($data);
		?>
		</div>
		
		<div class="gc_field">
		<label><?php echo lang('meta');?></label> <small><?php echo lang('meta_data_description');?></small>
		<?php
		$data	= array('id'=>'meta', 'name'=>'meta', 'value'=>set_value('meta', html_entity_decode($meta)), 'class'=>'gc_tf1');
		echo form_textarea($data);
		?>
		</div>
	</div>
</div>
</form>
<?php include('footer.php');