<?php include('header.php'); ?>

<?php echo form_open($this->config->item('admin_folder').'/giftcards/settings'); ?>


			<label for="predefined_card_amounts"><?php echo lang('predefined_card_amounts');?></label>
			<?php 
			$data	= array('name'=>'predefined_card_amounts', 'value'=>set_value('predefined_card_amounts', $predefined_card_amounts), 'class'=>'gc_tf1');
			echo form_input($data);
			 ?>
			<span class="help-inline"><?php echo lang('predefined_examples');?></span>
			
			<label class="checkbox">
			<?php
			$data	= array('name'=>'allow_custom_amount', 'value'=>'1', 'checked'=>(bool)$allow_custom_amount);
			echo form_checkbox($data);
			?>
			<?php echo lang('allow_custom_amounts');?></label>
			
	<div class="form-actions">
		<input class="btn btn-primary" type="submit" value="<?php echo lang('save');?>"/>
	</div>
</form>

<?php include('footer.php');