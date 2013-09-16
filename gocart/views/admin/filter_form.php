<?php echo form_open_multipart($this->config->item('admin_folder').'/filters/form/'.$id); ?>

<div class="tabbable">

	<ul class="nav nav-tabs">
		<li  class="active"><a href="#attributes_tab" data-toggle="tab"><?php echo lang('attributes');?></a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane active" id="attributes_tab">
			
			<fieldset>
				<label for="name"><?php echo lang('name');?></label>
				<?php
				$data	= array('name'=>'name', 'value'=>set_value('name', $name), 'class'=>'span6');
				echo form_input($data);
				?>
				
				<label for="slug"><?php echo lang('slug');?> </label>
				<?php
				$data	= array('name'=>'slug', 'value'=>set_value('slug', $slug));
				echo form_input($data);
				?>
				
				<label for="slug"><?php echo lang('parent');?> </label>
				<?php
				$data	= array(0 => 'Top Level filter');
				foreach($filters as $parent)
				{
					if($parent->id != $id)
					{
						$data[$parent->id] = $parent->name;
					}
				}
				echo form_dropdown('parent_id', $data, $parent_id);
				?>
				
			</fieldset>
		</div>
	</div>

</div>

<div class="form-actions">
	<button type="submit" class="btn btn-primary"><?php echo lang('form_save');?></button>
</div>
</form>

<script type="text/javascript">
$('form').submit(function() {
	$('.btn').attr('disabled', true).addClass('disabled');
});
</script>