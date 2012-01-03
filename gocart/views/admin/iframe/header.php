<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Go Cart</title>

<?php
//test for http / https for non hosted files
$http = 'http';
if(isset($_SERVER['HTTPS']))
{
	$http .= 's';
}
?>
<link type="text/css" href="<?php echo base_url('js/jquery/theme/smoothness/jquery-ui-1.8.16.custom.css');?>" rel="stylesheet" />
<script type="text/javascript" src="<?php echo base_url('js/jquery/jquery-1.7.1.min.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/jquery/jquery-ui-1.8.16.custom.min.js');?>"></script>

<link type="text/css" href="<?php echo base_url('js/jquery/colorbox/colorbox.css');?>" rel="stylesheet" />
<script type="text/javascript" src="<?php echo base_url('js/jquery/colorbox/jquery.colorbox-min.js');?>"></script>

<script type="text/javascript" src="<?php echo base_url('js/jquery/tiny_mce/tiny_mce.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/jquery/tiny_mce/jquery.tinymce.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/jquery/tiny_mce/tiny_mce_init.php');?>"></script>
<link href="<?php echo base_url('css/admin.css');?>" rel="stylesheet" type="text/css" />

<script type="text/javascript">
$(document).ready(function(){
	buttons();
});
function buttons()
{
	$('.list_buttons').buttonset();
	$('.button_set').buttonset();
	$('.button').button();
}
</script>
<style type="text/css">
body {
	background-color:#fff;
	background-image:none;
}
</style>
<body>