<?php include('header.php'); ?>

<?php echo form_open_multipart($this->config->item('admin_folder').'/categories/form/'.$id); ?>

<div class="button_set">
	<input type="submit" value="Save Category" />
</div>
<div id="gc_tabs">
	<ul>
		<li><a href="#gc_category_info">Description</a></li>
		<li><a href="#gc_category_attributes">Attributes</a></li>
		<li><a href="#gc_product_seo">SEO</a></li>
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
			<input type="button" onclick="toggleEditor('description'); return false;" value="Toggle WYSIWYG" />
		</div>
	</div>
	<div id="gc_category_attributes">
		
		<div class="gc_field2">
			<label for="slug">Slug: </label>
			<?php
			$data	= array('id'=>'slug', 'name'=>'slug', 'value'=>set_value('slug', $slug), 'class'=>'gc_tf1');
			echo form_input($data);
			?>
		</div>
		<div class="gc_field2">
			<label for="slug">Sequence: </label>
			<?php
			$data	= array('id'=>'sequence', 'name'=>'sequence', 'value'=>set_value('sequence', $sequence), 'class'=>'gc_tf1');
			echo form_input($data);
			?>
		</div>
		
		<div class="gc_field2">
			<label for="slug">Parent: </label>
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
		<label for="excerpt">Excerpt: </label>
			<?php
			$data	= array('id'=>'excerpt', 'name'=>'excerpt', 'value'=>set_value('excerpt', $excerpt), 'class'=>'gc_tf1');
			echo form_textarea($data);
			?>
		</div>
		<div class="gc_field">
		
		<label for="image">Image: </label><small>Max Size <?php echo  $this->config->item('size_limit')/1024; ?>kb</small>
			<?php echo form_upload(array('name'=>'image', 'id'=>'image'));?> <br/>
		<?php if($id && $image != ''):?>
		<div style="text-align:center; padding:5px; border:1px solid #ccc;"><img src="<?php echo base_url('uploads/images/small/'.$image);?>" alt="current"/><br/>Current File</div>
		<?php endif;?>
		
		</div>
	</div>
	
	<div id="gc_product_seo">
		<div class="gc_field2">
		<label for="code">SEO title: </label>
		<?php
		$data	= array('id'=>'seo_title', 'name'=>'seo_title', 'value'=>set_value('seo_title', $seo_title), 'class'=>'gc_tf1');
		echo form_input($data);
		?>
		</div>
		
		<div class="gc_field">
		<label>Meta Data:</label> <small>ex. &lt;meta name="description" content="We sell products that help you" /&gt;</small>
		<?php
		$data	= array('id'=>'meta', 'name'=>'meta', 'value'=>set_value('meta', html_entity_decode($meta)), 'class'=>'gc_tf1');
		echo form_textarea($data);
		?>
		</div>
	</div>
</div>
</form>
<?php include('footer.php');