<?php include('header.php'); ?>

<?php echo form_open($this->config->item('admin_folder').'/customers/form/'.$id); ?>

	<div class="row">
		<div class="span3">
			<label><?php echo lang('company');?></label>
			<?php
			$data	= array('name'=>'company', 'value'=>set_value('company', $company), 'class'=>'span3');
			echo form_input($data); ?>
		</div>
	</div>

	<div class="row">
		<div class="span3">
			<label><?php echo lang('firstname');?></label>
			<?php
			$data	= array('name'=>'firstname', 'value'=>set_value('firstname', $firstname), 'class'=>'span3');
			echo form_input($data); ?>
		</div>
		<div class="span3">
			<label><?php echo lang('lastname');?></label>
			<?php
			$data	= array('name'=>'lastname', 'value'=>set_value('lastname', $lastname), 'class'=>'span3');
			echo form_input($data); ?>
		</div>
	</div>

	<div class="row">
		<div class="span3">
			<label><?php echo lang('email');?></label>
			<?php
			$data	= array('name'=>'email', 'value'=>set_value('email', $email), 'class'=>'span3');
			echo form_input($data); ?>
		</div>
		<div class="span3">
			<label><?php echo lang('phone');?></label>
			<?php
			$data	= array('name'=>'phone', 'value'=>set_value('phone', $phone), 'class'=>'span3');
			echo form_input($data); ?>
		</div>
	</div>

	<div class="row">
		<div class="span3">
			<label><?php echo lang('password');?></label>
			<?php
			$data	= array('name'=>'password', 'class'=>'span3');
			echo form_password($data); ?>
		</div>
		<div class="span3">
			<label><?php echo lang('confirm');?></label>
			<?php
			$data	= array('name'=>'confirm', 'class'=>'span3');
			echo form_password($data); ?>
		</div>
	</div>

	<div class="row">
		<div class="span3">
			<label class="checkbox">
			<?php $data	= array('name'=>'email_subscribe', 'value'=>1, 'checked'=>(bool)$email_subscribe);
			echo form_checkbox($data).' '.lang('email_subscribed'); ?>
			</label>
		</div>
	</div>

	<div class="row">
		<div class="span3">
			<label class="checkbox">
				<?php
				$data	= array('name'=>'active', 'value'=>1, 'checked'=>$active);
				echo form_checkbox($data).' '.lang('active'); ?>
			</label>
		</div>
	</div>

	<div class="row">
		<div class="span3">
			<label><?php echo lang('group');?></label>
			<?php echo form_dropdown('group_id', $group_list, set_value('group_id',$group_id), 'class="span3"'); ?>
		</div>
	</div>

	<div class="form-actions">
		<input class="btn btn-primary" type="submit" value="<?php echo lang('save');?>"/>
	</div>
</form>

<?php include('footer.php');