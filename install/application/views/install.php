<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Thanks for using GoCart!</title>
	<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/smoothness/jquery-ui.css" type="text/css" rel="stylesheet"/> 
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script> 
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/jquery-ui.min.js"></script>
	
	<script type="text/javascript">
	$(document).ready(function(){
		
		$(':text').addClass('input');
		$('button').button();
	});
	</script>
	
	<style type="text/css">

	::selection{ background-color: #E13300; color: white; }
	::moz-selection{ background-color: #E13300; color: white; }
	::webkit-selection{ background-color: #E13300; color: white; }

	body {
		background-color: #fff;
		margin: 40px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
		background-image:url('/images/admin/bg_dots.gif');
	}

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
	}

	h1 {
		color: #444;
		background-color: transparent;
		border-bottom: 1px solid #D0D0D0;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		border: 1px solid #D0D0D0;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}
	
	#errors {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #fde7f2;
		border: 1px solid #de94a0;
		color: #c23636;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	#body{
		margin: 0 15px 0 15px;
	}
	
	p.footer{
		text-align: right;
		font-size: 11px;
		border-top: 1px solid #eeeeee;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
	}
	
	#container{
		margin: 10px auto;
		padding:10px;
		background-color:#fff;
		width:700px;
	}
	
	fieldset {
		border:1px solid #ccc;
		padding:10px;
		margin-top:15px;
	}
	
	legend {
		font-weight:bold;
	}
	
	label {
		float:left;
		width:130px;
	}
	
	.input {
		padding:5px 7px;
		border:1px solid #ccc;
		border-radius:3px;
		outline:0px;
	}
	
	input {
		float:left;
	}
	
	br {
		clear:both;
	}
	
	small {
		font-style:oblique;
	}
	</style>
</head>
<body>
<div style="text-align:center; padding:20px;"><img src="/images/admin/login_logo.png" alt="GoCart"/></div>
<div id="container">
	<div id="body">
		
		<?php if($errors):?>
		
		<div id="errors">
			<h2>Error!</h2>
			<?php echo $errors;?>
		</div>
		
		<?php endif;?>
		<?php echo form_open('/');?>
			
			<fieldset>
				<legend>Database Information</legend>
				
				<label for="hostname">Hostname</label> <?php echo form_input(array('name'=>'hostname', 'value'=>set_value('hostname', 'localhost') ));?><br/>
				<label for="database">Database Name</label> <?php echo form_input(array('name'=>'database', 'value'=>set_value('database') ));?><br/>
				<label for="username">Username</label> <?php echo form_input(array('name'=>'username', 'value'=>set_value('username') ));?><br/>
				<label for="password">Password</label> <?php echo form_input(array('name'=>'password', 'value'=>set_value('password') ));?><br/>
				<label for="password">Database Prefix</label> <?php echo form_input(array('name'=>'prefix', 'value'=>set_value('prefix', 'gc_') ));?>
				
			</fieldset>
			
			<fieldset>
				<legend>Admin Information</legend>
				
				<label for="login">Admin Email</label> <?php echo form_input(array('name'=>'admin_email', 'value'=>set_value('admin_email') ));?><br/>
				<label for="password">Admin Password</label> <?php echo form_input(array('name'=>'admin_password', 'value'=>set_value('admin_password') ));?>
				
			</fieldset>
			
			<fieldset>
				<legend>Cart Information</legend>
				
				<label for="company_name">Company Name</label> <?php echo form_input(array('name'=>'company_name', 'value'=>set_value('company_name') ));?><br/>
				<label for="website_email">Website Email</label> <?php echo form_input(array('name'=>'website_email', 'value'=>set_value('website_email') ));?><br/>
				<label for="ssl">SSL Support</label> <?php echo form_checkbox('ssl_support', '1', (bool)set_value('ssl_support') );?> 
				
			</fieldset>
			
			<fieldset>
				<legend>Location Information</legend>
				
				<p>
					Address information is only required for carts using live rate shipping modules such as FedEx, UPS, or USPS.
				</p>
				
				<label for="address1">Address</label> <?php echo form_input(array('name'=>'address1', 'value'=>set_value('address1') ));?><br/>
				<label for="address2">&nbsp;</label> <?php echo form_input(array('name'=>'address2', 'value'=>set_value('address2') ));?><br/>
				<label for="city">City</label> <?php echo form_input(array('name'=>'city', 'value'=>set_value('city') ));?><br/>
				<label for="state">State <small>(ex. LA)</small></label> <?php echo form_input(array('name'=>'state', 'value'=>set_value('state') ));?><br/>
				<label for="zip">Zip</label> <?php echo form_input(array('name'=>'zip', 'value'=>set_value('zip') ));?><br/>
				<label for="country">Country <small>(ex. US)</small></label> <?php echo form_input(array('name'=>'country', 'value'=>set_value('country') ));?>
				
			</fieldset>
			
			<p>
				<button type="submit">Install GoCart!</button>
			</p>
		</form>
	</div>

	<p class="footer"><a href="http://clearskydesigns.com">Clear Sky Designs</a></p>
</div>

</body>
</html>