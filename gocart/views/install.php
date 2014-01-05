<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Thanks for using GoCart!</title>
	
	<link href="<?php echo base_url('assets/css/bootstrap.min.css');?>" type="text/css" rel="stylesheet"/>

</head>
<body>
<div style="text-align:center; padding:20px;"><img src="<?php echo base_url('assets/img/login-logo.png');?>" alt="GoCart"/></div>
<div class="container">
	<div class="row">
		<div class="span8 offset2">
		
		<?php if(!$is_writeable['config'] || !$is_writeable['root'] || !$is_writeable['uploads']):?>
			<div class="alert alert-error">
				
				<?php if(!$is_writeable['config']):?>
					<p><strong>Alert!</strong><br> The gocart/config directory is not writable! This is required to generate the config files.</p>
				<?php endif;?>
				<?php if(!$is_writeable['root']):?>
					<p><strong>Alert!</strong><br> The root directory is not writable! This is required if you want to eliminate "index.php" from the URL by generating an .htaccess file.</p>
				<?php endif;?>
				<?php if(!$is_writeable['uploads']):?>
					<p><strong>Alert!</strong><br> The uploads directory is not writable! This is required for uploading files.</p>
				<?php endif;?>

			</div>
		<?php endif;?>
		<?php if($errors):?>
			<div class="alert alert-error">
				<?php echo $errors;?>
			</div>
		<?php endif;?>
			
		<?php echo form_open('/');?>

			<fieldset>
				
				<div class="alert alert-info">
					<h4>Note&hellip;</h4>
					<p>Installing GoCart will not create a database. It will simply fill your existing database with the appropriate tables and records required to run.</p>
				</div>
				
				<label for="hostname">Hostname</label> <?php echo form_input(array('class'=>'span8', 'name'=>'hostname', 'value'=>set_value('hostname', 'localhost') ));?>
				<label for="database">Database Name</label> <?php echo form_input(array('class'=>'span8', 'name'=>'database', 'value'=>set_value('database') ));?>
				<label for="username">Username</label> <?php echo form_input(array('class'=>'span8', 'name'=>'username', 'value'=>set_value('username') ));?>
				<label for="password">Password</label> <?php echo form_input(array('class'=>'span8', 'name'=>'password', 'value'=>set_value('password') ));?>
				<label for="password">Database Prefix</label> <?php echo form_input(array('class'=>'span8', 'name'=>'prefix', 'value'=>set_value('prefix', 'gc_') ));?>

				<label class="checkbox">
					<?php echo form_checkbox('mod_rewrite', '1', (bool)set_value('mod_rewrite') );?> Remove "index.php" from the url <small>(requires Apache with mod_rewrite)</small>
				</label>
				
			</fieldset>

			<p>
				<button type="submit" class="btn btn-large btn-primary">Install GoCart!</button>
			</p>
		</form>
	</div>
</div>
<hr>
<div class="footer">
	<div style="text-align:center;"><a href="http://gocartdv.com" target="_blank"><img src="<?php echo base_url('assets/img/driven-by-gocart.png');?>" alt="Driven By GoCart"></a></div>
</div>
</body>
</html>