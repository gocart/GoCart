<?php include('header.php'); ?>

<?php echo form_open($this->config->item('admin_folder').'/pages/form/'.$id); ?>

<div class="button_set">
	<input type="submit" value="<?php echo lang('save');?>"/>
</div>

<div id="gc_tabs">
	<ul>
		<li><a href="#gc_page_content"><?php echo lang('content');?></a></li>
		<li><a href="#gc_page_attributes"><?php echo lang('attributes');?></a></li>
		<li><a href="#gc_product_seo"><?php echo lang('seo');?></a></li>
	</ul>
	<div id="gc_page_content">
			<div class="gc_field">
			<?php
			$data	= array('id'=>'title', 'name'=>'title', 'value'=>set_value('title', $title), 'class'=>'gc_tf1');
			echo form_input($data);
			?>
			</div>

			<div class="gc_field gc_tinymce">
			<?php
			$data	= array('id'=>'description', 'name'=>'content', 'class'=>'tinyMCE', 'value'=>set_value('content', $content));
			echo form_textarea($data);
			?>
			</div>
			
			<div class="button_set">
				<input type="button" onclick="toggleEditor('description'); return false;" value="Toggle WYSIWYG" />
			</div>
	</div>
	<div id="gc_page_attributes">
		<div class="gc_field2">
		<label for="menu_title"><?php echo lang('menu_title');?></label>
		<?php
		$data	= array('id'=>'menu_title', 'name'=>'menu_title', 'value'=>set_value('menu_title', $menu_title), 'class'=>'gc_tf1');
		echo form_input($data);
		?>
		</div>
		<div class="gc_field2">
		<label for="slug"><?php echo lang('slug');?></label>
		<?php
		$data	= array('id'=>'slug', 'name'=>'slug', 'value'=>set_value('slug', $slug), 'class'=>'gc_tf1');
		echo form_input($data);
		?>
		</div>
		<div class="gc_field2">
	
		<label for="sequence"><?php echo lang('parent');?></label>
		<?php
		$options	= array();
		$options[0]	= 'Top Level';
		function page_loop($pages, $dash = '', $id=0)
		{
			$options	= array();
			foreach($pages as $page)
			{
				//this is to stop the whole tree of a particular link from showing up while editing it
				if($id != $page->id)
				{
					$options[$page->id]	= $dash.' '.$page->title;
					$options			= $options + page_loop($page->children, $dash.'-', $id);
				}
			}
			return $options;
		}
		$options	= $options + page_loop($pages, '', $id);
		echo form_dropdown('parent_id', $options,  set_value('parent_id', $parent_id));
		?>
		</div>
		<div class="gc_field2">
		<label for="sequence"><?php echo lang('sequence');?></label>
		<?php
		$data	= array('id'=>'sequence', 'name'=>'sequence', 'value'=>set_value('sequence', $sequence), 'class'=>'gc_tf1');
		echo form_input($data);
		?>
		</div>
	</div>
	<div id="gc_product_seo">
		<div class="gc_field2">
		<label for="code"><?php echo lang('seo_title');?></label>
		<?php
		$data	= array('id'=>'seo_title', 'name'=>'seo_title', 'value'=>set_value('seo_title', $seo_title), 'class'=>'gc_tf1');
		echo form_input($data);
		?>
		</div>
		
		<div class="gc_field">
		<label><?php echo lang('meta');?></label>
		<?php
		$data	= array('id'=>'meta', 'name'=>'meta', 'value'=>set_value('meta', html_entity_decode($meta)), 'class'=>'gc_tf1');
		echo form_textarea($data);
		?>
		</div>
	</div>
</div>	

<script type="text/javascript">
$("#gc_tabs").tabs();
</script>
<?php include('footer.php'); ?>