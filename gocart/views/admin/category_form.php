<?php include('header.php'); ?>

<?php echo form_open_multipart($this->config->item('admin_folder').'/categories/form/'.$id); ?>

<div class="tabbable">

	<ul class="nav nav-tabs">
		<li class="active"><a href="#description_tab" data-toggle="tab"><?php echo lang('description');?></a></li>
		<li><a href="#attributes_tab" data-toggle="tab"><?php echo lang('attributes');?></a></li>
		<li><a href="#seo_tab" data-toggle="tab"><?php echo lang('seo');?></a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane active" id="description_tab">
			
			<fieldset>
				<label for="name"><?php echo lang('name');?></label>
				<?php
				$data	= array('name'=>'name', 'value'=>set_value('name', $name), 'class'=>'span12');
				echo form_input($data);
				?>
				
				<label for="description"><?php echo lang('description');?></label>
				<?php
				$data	= array('name'=>'description', 'class'=>'redactor', 'value'=>set_value('description', $description));
				echo form_textarea($data);
				?>
			</fieldset>
		</div>

		<div class="tab-pane" id="attributes_tab">
			
			<fieldset>
				<label for="slug"><?php echo lang('slug');?> </label>
				<?php
				$data	= array('name'=>'slug', 'value'=>set_value('slug', $slug));
				echo form_input($data);
				?>
				
				<label for="sequence"><?php echo lang('sequence');?> </label>
				<?php
				$data	= array('name'=>'sequence', 'value'=>set_value('sequence', $sequence));
				echo form_input($data);
				?>
				
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
				
				<label for="excerpt"><?php echo lang('excerpt');?> </label>
				<?php
				$data	= array('name'=>'excerpt', 'value'=>set_value('excerpt', $excerpt), 'class'=>'span12', 'rows'=>3);
				echo form_textarea($data);
				?>

				<label for="image"><?php echo lang('image');?> </label>
				<div class="input-append">
					<?php echo form_upload(array('name'=>'image'));?><span class="add-on"><?php echo lang('max_file_size');?> <?php echo  $this->config->item('size_limit')/1024; ?>kb</span>
				</div>
					
				<?php if($id && $image != ''):?>
				
				<div style="text-align:center; padding:5px; border:1px solid #ddd;"><img src="<?php echo base_url('uploads/images/small/'.$image);?>" alt="current"/><br/><?php echo lang('current_file');?></div>
				
				<?php endif;?>
				
			</fieldset>
			
		</div>
		
		<div class="tab-pane" id="seo_tab">
			<fieldset>
				<label for="seo_title"><?php echo lang('seo_title');?> </label>
				<?php
				$data	= array('name'=>'seo_title', 'value'=>set_value('seo_title', $seo_title), 'class'=>'span12');
				echo form_input($data);
				?>
				
				<label><?php echo lang('meta');?></label> 
				<?php
				$data	= array('rows'=>3, 'name'=>'meta', 'value'=>set_value('meta', html_entity_decode($meta)), 'class'=>'span12');
				echo form_textarea($data);
				?>
				<p class="help-block"><?php echo lang('meta_data_description');?></p>
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
<?php include('footer.php');