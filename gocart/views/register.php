<?php
$additional_header_info = '<style type="text/css">#gc_page_title {text-align:center;}</style>';
include('header.php'); ?>
<?php
$company	= array('id'=>'bill_company', 'class'=>'bill input', 'name'=>'company', 'value'=> set_value('company'));
$first		= array('id'=>'bill_firstname', 'class'=>'bill input bill_req', 'name'=>'firstname', 'value'=> set_value('firstname'));
$last		= array('id'=>'bill_lastname', 'class'=>'bill input bill_req', 'name'=>'lastname', 'value'=> set_value('lastname'));
$email		= array('id'=>'bill_email', 'class'=>'bill input bill_req', 'name'=>'email', 'value'=>set_value('email'));
$phone		= array('id'=>'bill_phone', 'class'=>'bill input bill_req', 'name'=>'phone', 'value'=> set_value('phone'));
?>
<div id="login_container_wrap">
	<div id="login_container">
	<?php echo form_open('secure/register'); ?>
		<input type="hidden" name="submitted" value="submitted" />
		<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />

		<div class="form_wrap">
			<div>
				<?php echo lang('address_company');?><br/>
				<?php echo form_input($company);?>
			</div>
			<div>
				<?php echo lang('address_firstname');?><b class="r"> *</b><br/>
				<?php echo form_input($first);?>
			</div>
			<div>
				<?php echo lang('address_lastname');?><b class="r"> *</b><br/>
				<?php echo form_input($last);?>
			</div>
		</div>
	
		<div class="form_wrap">
			<div>
				<?php echo lang('address_email');?><b class="r"> *</b><br/>
				<?php echo form_input($email);?>
			</div>
			<div>
				<?php echo lang('address_phone');?><b class="r"> *</b><br/>
				<?php echo form_input($phone);?>
			</div>
		</div>
		<div class="form_wrap">
			<input type="checkbox" name="email_subscribe" value="1" <?php echo set_radio('email_subscribe', '1', TRUE); ?>/> <?php echo lang('account_newsletter_subscribe');?>
		</div>
		<div class="form_wrap">
			<div>
				<?php echo lang('account_password');?><b class="r"> *</b><br/>
				<input type="password" name="password" value="" />
			</div>
			<div>
				<?php echo lang('account_confirm');?><b class="r"> *</b><br/>
				<input type="password" name="confirm" value="" />
			</div>
		</div>
	
		<div class="form_wrap">
			<input type="submit" value="<?php echo lang('form_register');?>" />
		</div>
	</form>
	
	<div id="login_form_links">
		<a href="<?php echo site_url('secure/login'); ?>"><?php echo lang('go_to_login');?></a>
	</div>
	
	</div>
</div>
<?php include('footer.php');