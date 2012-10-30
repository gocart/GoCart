<?php
$additional_header_info = '<style type="text/css">#gc_page_title {text-align:center;}</style>';
include('header.php'); ?>
<?php
$company	= array('id'=>'bill_company', 'class'=>'span6', 'name'=>'company', 'value'=> set_value('company'));
$first		= array('id'=>'bill_firstname', 'class'=>'span3', 'name'=>'firstname', 'value'=> set_value('firstname'));
$last		= array('id'=>'bill_lastname', 'class'=>'span3', 'name'=>'lastname', 'value'=> set_value('lastname'));
$email		= array('id'=>'bill_email', 'class'=>'span3', 'name'=>'email', 'value'=>set_value('email'));
$phone		= array('id'=>'bill_phone', 'class'=>'span3', 'name'=>'phone', 'value'=> set_value('phone'));
?>
<div class="row" style="margin-top:50px;">
	<div class="span6 offset3">
		<div class="page-header">
			<h1><?php echo lang('form_register');?></h1>
		</div>
		<?php echo form_open('secure/register'); ?>
			<input type="hidden" name="submitted" value="submitted" />
			<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />

			<fieldset>
				<div class="row">
					<div class="span6">
						<label for="company"><?php echo lang('account_company');?></label>
						<?php echo form_input($company);?>
					</div>
				</div>
				<div class="row">	
					<div class="span3">
						<label for="account_firstname"><?php echo lang('account_firstname');?></label>
						<?php echo form_input($first);?>
					</div>
				
					<div class="span3">
						<label for="account_lastname"><?php echo lang('account_lastname');?></label>
						<?php echo form_input($last);?>
					</div>
				</div>
			
				<div class="row">
					<div class="span3">
						<label for="account_email"><?php echo lang('account_email');?></label>
						<?php echo form_input($email);?>
					</div>
				
					<div class="span3">
						<label for="account_phone"><?php echo lang('account_phone');?></label>
						<?php echo form_input($phone);?>
					</div>
				</div>
			
				<div class="row">
					<div class="span7">
						<label class="checkbox">
							<input type="checkbox" name="email_subscribe" value="1" <?php echo set_radio('email_subscribe', '1', TRUE); ?>/> <?php echo lang('account_newsletter_subscribe');?>
						</label>
					</div>
				</div>
			
				<div class="row">	
					<div class="span3">
						<label for="account_password"><?php echo lang('account_password');?></label>
						<input type="password" name="password" value="" class="span3"/>
					</div>

					<div class="span3">
						<label for="account_confirm"><?php echo lang('account_confirm');?></label>
						<input type="password" name="confirm" value="" class="span3"/>
					</div>
				</div>
				
				<input type="submit" value="<?php echo lang('form_register');?>" class="btn btn-primary" />
			</fieldset>
		</form>
	
		<div style="text-align:center;">
			<a href="<?php echo site_url('secure/login'); ?>"><?php echo lang('go_to_login');?></a>
		</div>
	</div>
</div>
<?php include('footer.php');