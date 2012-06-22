<?php include('header.php'); ?>

<?php echo form_open($this->config->item('admin_folder').'/pages/link_form/'.$id); ?>

	<label for="menu_title"><?php echo lang('title');?> </label>
	<?php
	$data	= array('id'=>'title', 'name'=>'title', 'value'=>set_value('title', $title), 'class'=>'span3');
	echo form_input($data);
	?>

	<label for="url"><?php echo lang('url');?></label>
	<?php
	$data	= array('id'=>'url', 'name'=>'url', 'value'=>set_value('url', $url), 'class'=>'span3'); 
	echo form_input($data);
	?>
	<span class="help-inline"><?php echo lang('url_example');?></span>

	<label for="sequence"><?php echo lang('parent_id');?></label>
	<?php
	$options	= array();
	$options[0]	= lang('top_level');
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
	echo form_dropdown('parent_id', $options,  set_value('parent_id', $parent_id, 'class="span3"'));
	?>

	<label for="sequence"><?php echo lang('sequence');?></label>
	<?php
	$data	= array('id'=>'sequence', 'name'=>'sequence', 'value'=>set_value('sequence', $sequence), 'class'=>'span3');
	echo form_input($data);
	?>

	<label class="checkbox">
	<?php
	$data	= array('name'=>'new_window', 'value'=>'1', 'checked'=>(bool)$new_window);
	echo form_checkbox($data);
	?>
	<?php echo lang('open_in_new_window');?></label>

	<div class="form-actions">
		<input class="btn btn-primary" type="submit" value="<?php echo lang('save');?>"/>
	</div>
</form>

<?php include('footer.php'); ?>