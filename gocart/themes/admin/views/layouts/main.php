<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Go Cart<?php echo (isset($page_title))?' :: '.$page_title:''; ?></title>

<link href="<?php echo base_url('assets/css/bootstrap.min.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url('assets/css/bootstrap-responsive.min.css');?>" rel="stylesheet" type="text/css" />
<link type="text/css" href="<?php echo base_url('assets/css/jquery-ui.css');?>" rel="stylesheet" />
<link type="text/css" href="<?php echo base_url('assets/css/goedit.css');?>" rel="stylesheet" />

<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/jquery-ui.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/bootstrap.min.js');?>"></script>

<script type="text/javascript">
/* GoEdit Media URLs*/
var goedit_media_manager_url		= '<?php echo site_url(config_item('admin_folder').'/media');?>';
var goedit_media_attributes_url		= '<?php echo site_url(config_item('admin_folder').'/media/edit_image');?>';
var goedit_language_toggle_editor	= '<?php echo lang('goedit_toggle_editor');?>';
</script>
<script type="text/javascript" src="<?php echo base_url('assets/js/goedit.js');?>"></script>
<?php if($this->auth->is_logged_in(false, false)):?>
	
<style type="text/css">
	body {
		margin-top:50px;
	}
	
	@media (max-width: 979px){ 
		body {
			margin-top:0px;
		}
	}
	@media (min-width: 980px) {
		.nav-collapse.collapse {
			height: auto !important;
			overflow: visible !important;
		}
	 }
	
	.nav-tabs li a {
		text-transform:uppercase;
		background-color:#f2f2f2;
		border-bottom:1px solid #ddd;
		text-shadow: 0px 1px 0px #fff;
		filter: dropshadow(color=#fff, offx=0, offy=1);
		font-size:12px;
		padding:5px 8px;
	}
	
	.nav-tabs li a:hover {
		border:1px solid #ddd;
		text-shadow: 0px 1px 0px #fff;
		filter: dropshadow(color=#fff, offx=0, offy=1);
	}

</style>
<script type="text/javascript">
$(document).ready(function(){
	$('.datepicker').datepicker({dateFormat: 'yy-mm-dd'});
});
</script>
<?php endif;?>
</head>
<body>

<?php echo $template['partials']['header']; ?>
<?php echo $template['body']; ?>
<?php echo $template['partials']['footer']; ?>

</body>
</html>
