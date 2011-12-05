<?php include('header.php'); ?>

<?php echo form_open($this->config->item('admin_folder').'/giftcards/form/'); ?>
<div class="button_set">
	<input type="submit" value="<?php echo lang('save');?>"/>
</div>
	
<div id="gc_tabs">
	<ul>
		<li><a href="#gc_coupon_attributes"><?php echo lang('attributes');?></a></li>
	</ul>
	
	<div id="gc_coupon_attributes">
		<div class="gc_field2">
		<label for="to_name"><?php echo lang('recipient_name');?> </label>
			<?php
			$data	= array('id'=>'to_name', 'name'=>'to_name', 'value'=>set_value('code'), 'class'=>'gc_tf1');
			echo form_input($data);
			?>
		</div>
		<div class="gc_field2">
			<label for="to_email"><?php echo lang('recipient_email');?></label>
			<?php
			$data	= array('id'=>'to_email', 'name'=>'to_email', 'value'=>set_value('to_email'), 'class'=>'gc_tf1');
			echo form_input($data);
			?>
		</div>
        <div class="gc_field2">
			<label for="send_notification"><?php echo lang('send_notification');?></label>
			<?php
			$data	= array('name'=>'send_notification', 'value'=>'true', 'class'=>'gc_tf1');
			echo form_checkbox($data);
			?>
		</div>
        <div class="gc_field2">
			<label for="sender_name"><?php echo lang('sender_name');?></label>
			<?php
			$data	= array('id'=>'from', 'name'=>'from', 'value'=>set_value('from'), 'class'=>'gc_tf1');
			echo form_input($data);
			?>
		</div>
		<div class="gc_field2">
			<label for="personal_message"><?php echo lang('personal_message');?></label>
			<?php
				$data	= array('name'=>'personal_message', 'value'=>set_value('personal_message'), 'class'=>'gc_tf1');
				echo form_textarea($data);
			?>
		</div>

		<div class="gc_field2" id="gc_coupon_price_fields">
			<label for="beginning_amount"><?php echo lang('amount');?></label>
			<?php
				$data	= array('id'=>'beginning_amount', 'name'=>'beginning_amount', 'value'=>set_value('beginning_amount'), 'class'=>'gc_tf1');
				echo form_input($data);
			?>
		</div>
	</div>
</div>

</form>

<?php include('footer.php'); ?>
