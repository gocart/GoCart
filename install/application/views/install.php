<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Thanks for using GoCart!</title>
	
	<link href="<?php echo $subfolder;?>assets/css/bootstrap.min.css" type="text/css" rel="stylesheet"/>

</head>
<body>
<div style="text-align:center; padding:20px;"><img src="<?php echo $subfolder;?>assets/img/login-logo.png" alt="GoCart"/></div>
<div class="container">
	<div class="row">
		<div class="span8 offset2">
		
		<?php if(!$config_writable):?>
			<div class="alert alert-error">
				<p>The <?php echo $relative_path?> folder is not writable! This is required to generate the config files.</p>
			</div>
		<?php endif;?>
		<?php if(!$root_writable):?>
			<div class="alert alert-error">
				<p>The root folder is not writable! This is required if you want to eliminate "index.php" from the URL by generating an .htaccess file.</p>
			</div>
		<?php endif;?>
		<?php if($errors):?>
			<div class="alert alert-error">
				<?php echo $errors;?>
			</div>
		<?php endif;?>
			
		<?php echo form_open('/');?>

			<fieldset>
				<legend>Database Information</legend>
				
				<div class="alert alert-info">
					<h4>Note&hellip;</h4>
					<p>Installing GoCart will not create a database. It will simply fill your existing database with the appropriate tables and records required to run.</p>
				</div>
				
				<label for="hostname">Hostname</label> <?php echo form_input(array('class'=>'span8', 'name'=>'hostname', 'value'=>set_value('hostname', 'localhost') ));?>
				<label for="database">Database Name</label> <?php echo form_input(array('class'=>'span8', 'name'=>'database', 'value'=>set_value('database') ));?>
				<label for="username">Username</label> <?php echo form_input(array('class'=>'span8', 'name'=>'username', 'value'=>set_value('username') ));?>
				<label for="password">Password</label> <?php echo form_input(array('class'=>'span8', 'name'=>'password', 'value'=>set_value('password') ));?>
				<label for="password">Database Prefix</label> <?php echo form_input(array('class'=>'span8', 'name'=>'prefix', 'value'=>set_value('prefix', 'gc_') ));?>
				
			</fieldset>
			
			<fieldset>
				<legend>Admin Information</legend>
				
				<label for="login">Admin Email</label> <?php echo form_input(array('class'=>'span8', 'name'=>'admin_email', 'value'=>set_value('admin_email') ));?>
				<label for="password">Admin Password</label> <?php echo form_input(array('class'=>'span8', 'name'=>'admin_password', 'value'=>set_value('admin_password') ));?>
				
			</fieldset>
			
			<fieldset>
				<legend>Cart Information</legend>
				
				<label for="company_name">Company Name</label> <?php echo form_input(array('class'=>'span8', 'name'=>'company_name', 'value'=>set_value('company_name') ));?>
				<label for="website_email">Website Email</label> <?php echo form_input(array('class'=>'span8', 'name'=>'website_email', 'value'=>set_value('website_email') ));?>
				<label class="checkbox">
					<?php echo form_checkbox('ssl_support', '1', (bool)set_value('ssl_support') );?> SSL Support
				</label>
				
				<label class="checkbox">
					<?php echo form_checkbox('mod_rewrite', '1', (bool)set_value('mod_rewrite') );?> Remove "index.php" from the url <small>(requires Apache with mod_rewrite)</small>
				</label>
				
			</fieldset>
			
			<fieldset>
				<legend>Location Information</legend>
				
				<div class="alert alert-info alert-block">
					<h4>Note&hellip;</h4>
					<p>Address information is only required for carts using live rate shipping modules such as FedEx, UPS, or USPS.</p>
				</div>
				
				<label for="address1">Address</label> <?php echo form_input(array('class'=>'span8', 'name'=>'address1', 'value'=>set_value('address1') ));?>
				<label for="address2">Address 2</label> <?php echo form_input(array('class'=>'span8', 'name'=>'address2', 'value'=>set_value('address2') ));?>
				<label for="city">City</label> <?php echo form_input(array('class'=>'span8', 'name'=>'city', 'value'=>set_value('city') ));?>
				<label for="state">State <small>(ex. LA)</small></label> <?php echo form_input(array('class'=>'span8', 'name'=>'state', 'value'=>set_value('state') ));?>
				<label for="zip">Zip</label> <?php echo form_input(array('class'=>'span8', 'name'=>'zip', 'value'=>set_value('zip') ));?>
				<label for="country">Country <small>(ex. US)</small></label> <?php echo form_input(array('class'=>'span8', 'name'=>'country', 'value'=>set_value('country') ));?>
				
			</fieldset>
			
			<p>
				<button type="submit" class="btn btn-large btn-primary">Install GoCart!</button>
			</p>
		</form>
	</div>
</div>
<hr>
<div class="footer">
	<div style="text-align:center;"><a href="http://gocartdv.com" target="_blank"><img src="<?php echo $subfolder;?>assets/img/driven-by-gocart.png" alt="Driven By GoCart"></a></div>
</div>
</body>
</html>