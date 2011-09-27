<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>GoCart Login</title>

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
	$('#login_container').css('opacity', .9)
	
});
if (navigator.userAgent.toLowerCase().indexOf("chrome") >= 0) {
    $(window).load(function(){
        $('input:-webkit-autofill').each(function(){
            var text = $(this).val();
            var name = $(this).attr('name');
            $(this).after(this.outerHTML).remove();
            $('input[name=' + name + ']').val(text);
        });
    });
}
</script>
<style type="text/css">
body {
	background-image:url('/images/admin/gc_login_bg.gif');
	margin:0px;
	padding:0px;
}
#login_container {
	margin:auto;
	position:absolute;
	font-family:'Lucida Grande', Arial, Verdana, sans-serif;
	font-size:20px;
	z-index:2;
	left:150px;
	top:100px;
	color:#555;
	background-color:#fff;
	-moz-box-shadow: 0px 0px 5px #000;
	-webkit-box-shadow: 0px 0px 5px #000;
	box-shadow: 0px 0px 5px #000;
	border-radius: 5px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	padding:10px;
}
h3 {
	font-family: "Lucida Grande", Arial, Verdana, sans-serif;
	color:#555;
	font-weight:normal;
	font-size:28px;
	padding:0px;
	margin:0px;
}
.gc_tf1
{
	border:1px solid #ccc;
	padding:7px;
	width:300px;
	border-radius: 5px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	font-size:20px;
	outline:none;
	font-family: "Lucida Grande", Arial, Verdana, sans-serif;
	color:#525252;
}

#error {
	background-color:#900000;
	border-bottom:1px solid #a50101;
	color:#ffffff;
	font-size:12px;
	font-family: "Lucida Grande", Arial, Verdana, sans-serif;
	font-weight:bold;
	text-align:center;
	padding:10px 0px;
	-moz-box-shadow: 0px 0px 5px #000;
	-webkit-box-shadow: 0px 0px 5px #000;
	box-shadow: 0px 0px 5px #000;
}
</style>
</head>
<body>
	<img style="position:absolute;bottom:0px;right:0px;z-index:1;" src="/images/admin/gc_login_logo.png" alt="logo"/>
	<?php
	if ($this->session->flashdata('message'))
	{
		echo '<div id="error">'.$this->session->flashdata('message').'</div>';
	}
	?>
	
	<?php echo secure_form_open($this->config->item('admin_folder').'/login') ?>
	<table id="login_container">
		<tr>
			<td colspan="2"><h3>Login</h3></td>
		</tr>
		<tr>
			<td>Email: </td>
			<td><?php echo  form_input(array('id'=>'email', 'name'=>'email', 'class'=>'gc_tf1')); ?></td>
		</tr>
		<tr>
			<td>Password:</td>
			<td><?php echo  form_password(array('id'=>'password', 'name'=>'password', 'class'=>'gc_tf1')); ?></td>
		</tr>
		<tr>
			<td>Keep Me Logged In:</td>
			<td><input type="checkbox" value="true" name="remember" /></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Login" name="submit" style="font-size:25px;padding:5px 10px; float:right;"/></td>
		</tr>
	</table>
	<input type="hidden" value="<?php echo $redirect; ?>" name="redirect"/>
	<input type="hidden" value="submitted" name="submitted"/>
	<?php echo  form_close(); ?>
</body>
</html>