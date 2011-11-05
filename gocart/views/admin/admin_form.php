<?php include('header.php'); ?>
<?php echo form_open($this->config->item('admin_folder').'/admin/form/'.$id); ?>

<div class="button_set">
	<input type="submit" value="Save Admin"/>
</div>

<div id="gc_tabs">
	
	<ul>
		<li><a href="#gc_admin_info">Information</a></li>
	</ul>
	
	<div id="gc_admin_info">
		<div class="gc_field2">
		<label>First Name</label>
		<?php
		$data	= array('id'=>'firstname', 'name'=>'firstname', 'value'=>set_value('firstname', $firstname), 'class'=>'gc_tf1');
		echo form_input($data);
		?>
		</div>
		
		<div class="gc_field2">
		<label>Last Name</label>
		<?php
		$data	= array('id'=>'lastname', 'name'=>'lastname', 'value'=>set_value('lastname', $lastname), 'class'=>'gc_tf1');
		echo form_input($data);
		?>
		</div>

		<div class="gc_field2">
		<label>Email</label>
		<?php
		$data	= array('id'=>'email', 'name'=>'email', 'value'=>set_value('email', $email), 'class'=>'gc_tf1');
		echo form_input($data);
		?>
		</div>

		<div class="gc_field2">
		<label>Access</label>
		<?php
		$options = array(	'Admin'		=> 'Admin',
							'Orders'	=> 'Orders'
		                );
		echo form_dropdown('access', $options, set_value('phone', $access));
		?>
		</div>

		<div class="gc_field2">
		<label>Password</label>
		<?php
		$data	= array('id'=>'password', 'name'=>'password', 'class'=>'gc_tf1');
		echo form_password($data);
		?>
		</div>

		<div class="gc_field2">
		<label>Confirm Password</label>
		<?php
		$data	= array('id'=>'confirm', 'name'=>'confirm', 'class'=>'gc_tf1');
		echo form_password($data);
		?>
		</div>
	</div>
</div>
</form>
<script type="text/javascript">
$("#gc_tabs").tabs();
</script>
<?php include('footer.php');