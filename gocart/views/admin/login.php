<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo lang('gocart_login');?></title>

<?php
//test for http / https for non hosted files
$http = 'http';
if(isset($_SERVER['HTTPS']))
{
	$http .= 's';
}
?>
<link href="<?php echo $http;?>://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/smoothness/jquery-ui.css" type="text/css" rel="stylesheet"/> 
<script type="text/javascript" src="<?php echo $http;?>://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script> 
<script type="text/javascript" src="<?php echo $http;?>://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/jquery-ui.min.js"></script>

<script type="text/javascript">
$(document).ready(function(){
	$('input:submit').button();
});
</script>
<style type="text/css">
body {
	background-image:url('<?php echo base_url('images/admin/bg_dots.gif');?>');
	margin:0px;
	padding:0px;
}
#logo {
	margin:150px auto 15px;
	display:block;
}
#login_container {
	margin:auto;
	font-family:'Lucida Grande', Arial, Verdana, sans-serif;
	font-size:14px;
	color:#555;
	padding:10px;
	width:310px;
	background-color:#fff;
}
.form_input
{
	display:block;
	border:1px solid #ccc;
	padding:5px;
	width:300px;
	border-radius: 3px;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	font-size:16px;
	outline:none;
	font-family:Arial, Verdana, sans-serif;
	color:#555555;
	margin:0px 0px 10px;
}

label {
	display:block;
	padding:3px;
}

#error {
	background-color:#d7330d;
	border:1px solid #be2907;
	width:958px;
	margin:auto;
	color:#ffffff;
	font-size:12px;
	font-family: "Lucida Grande", Arial, Verdana, sans-serif;
	font-weight:bold;
	text-align:center;
	padding:10px 0px;
}

</style>
</head>
<body>
	<img src="<?php echo base_url('images/admin/login_logo.png');?>" id="logo"/>
	<?php
	if ($this->session->flashdata('error'))
	{
		echo '<div id="error">'.$this->session->flashdata('error').'</div>';
	}
	?>
	
	<?php echo form_open($this->config->item('admin_folder').'/login') ?>
	<div id="login_container">
			<label><?php echo lang('email');?></label>
			<?php echo  form_input(array('id'=>'email', 'name'=>'email', 'class'=>'form_input')); ?>
			
			
			<label><?php echo lang('password');?></label>
			<?php echo  form_password(array('id'=>'password', 'name'=>'password', 'class'=>'form_input')); ?>
			<?php echo lang('stay_logged_in');?>
			<input type="checkbox" value="true" name="remember" />
			
			<input type="submit" value="<?php echo lang('login');?>" name="submit" style=" margin:0px; padding:5px 10px; float:right;"/>
			
			<br style="clear:both;"/>
	</div>
	<input type="hidden" value="<?php echo $redirect; ?>" name="redirect"/>
	<input type="hidden" value="submitted" name="submitted"/>
	<?php echo  form_close(); ?>
</body>
</html>