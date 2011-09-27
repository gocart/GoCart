<?php include('header.php'); ?>

<?php echo form_open($this->config->item('admin_folder').'/pages/link_form/'.$id); ?>

<div class="button_set">
	<input type="submit" value="Save Link"/>
</div>

<div id="gc_tabs">
	<ul>
		<li><a href="#gc_link_info">Link Information</a></li>
	</ul>

	<div id="gc_link_info">
		<div class="gc_field2">
		<label for="menu_title">Title: </label>
		<?php
		$data	= array('id'=>'title', 'name'=>'title', 'value'=>set_value('title', $title), 'class'=>'gc_tf1');
		echo form_input($data);
		?>
		</div>
		<div class="gc_field2">
		<label for="url">URL: </label>
		<?php
		$data	= array('id'=>'url', 'name'=>'url', 'value'=>set_value('url', $url), 'class'=>'gc_tf1'); 
		echo form_input($data);
		?>
		 <small><em>ex. HTTP://www.google.com <strong>(http:// is required!)</strong></em></small>
		</div>
		<div class="gc_field2">
	
		<label for="sequence">Parent Page: </label>
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
		<label for="sequence">Sequence: </label>
		<?php
		$data	= array('id'=>'sequence', 'name'=>'sequence', 'value'=>set_value('sequence', $sequence), 'class'=>'gc_tf1');
		echo form_input($data);
		?>
		</div>
		<div class="gc_field2">
			<p><input type="checkbox" value="1" name="new_window" <?php echo set_checkbox('new_window', '1', $new_window); ?> /> Open this link in a new window</p>
		</div>
	</div>
</div>	

<?php include('footer.php'); ?>