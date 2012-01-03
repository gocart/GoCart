<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Go Cart :: <?php echo  $page_title; ?></title>

<link href="<?php echo base_url('css/admin.css');?>" rel="stylesheet" type="text/css" />

<?php
//test for http / https for non hosted files
$http = 'http';
if(isset($_SERVER['HTTPS']))
{
	$http .= 's';
}
?>
<link href='<?php echo $http;?>://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css' />

<link type="text/css" href="<?php echo base_url('js/jquery/theme/smoothness/jquery-ui-1.8.16.custom.css');?>" rel="stylesheet" />
<script type="text/javascript" src="<?php echo base_url('js/jquery/jquery-1.7.1.min.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/jquery/jquery-ui-1.8.16.custom.min.js');?>"></script>

<link href="<?php echo base_url('js/jquery/colorbox/colorbox.css');?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo base_url('js/jquery/colorbox/jquery.colorbox-min.js');?>"></script>

<script type="text/javascript" src="<?php echo base_url('js/jquery/tiny_mce/tiny_mce.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/jquery/tiny_mce/tiny_mce_init.php');?>"></script>

<script type="text/javascript">
$(document).ready(function(){
	buttons();
	$("#gc_tabs").tabs();
	$('.datepicker').datepicker({dateFormat: 'yy-mm-dd'});
});
function buttons()
{
	$('.list_buttons').buttonset();
	$('.button_set').buttonset();
	$('.button').button();
}
</script>

</head>
<body>
<div id="wrapper">
	<div id="header">
		<div class="shine"></div>
		<a href="<?php echo site_url($this->config->item('admin_folder').'/dashboard');?>"><img src="<?php echo base_url('images/admin/logo.png');?>" alt="dashboard"/></a>
		<h2><?php echo  $page_title; ?></h2>
		
		<span>
			<a style="float:right;" href="<?php echo site_url($this->config->item('admin_folder').'/login/logout');?>"><?php echo lang('common_log_out') ?></a>
			<a style="float:right;" href="<?php echo site_url();?>"><?php echo lang('common_front_end') ?></a>
			<a style="float:right;" href="<?php echo site_url($this->config->item('admin_folder').'/dashboard');?>"><?php echo lang('common_dashboard') ?></a>
		</span>
	</div>
	<div id="container">
		<div id="menu_wrapper">
			<?php
			//just to shorten this up some.
			$admin_url = site_url($this->config->item('admin_folder')).'/';?>
			
			<div class="menu shadow">
				<div class="menu_title"><?php echo lang('common_sales') ?></div>
				<a href="<?php echo $admin_url;?>orders"><?php echo lang('common_orders') ?></a>
				<?php if($this->auth->check_access('Admin')) : ?>
				<a href="<?php echo $admin_url;?>customers"><?php echo lang('common_customers') ?></a>
				<a href="<?php echo $admin_url;?>customers/groups"><?php echo lang('common_groups') ?></a>
				<a href="<?php echo $admin_url;?>reports"><?php echo lang('common_reports') ?></a>
				<a href="<?php echo $admin_url;?>coupons"><?php echo lang('common_coupons') ?></a>
				<a href="<?php echo $admin_url;?>giftcards"><?php echo lang('common_giftcards') ?></a>
				<?php endif; ?>
			</div>
			<?php
			// Restrict access to Admins only
			if($this->auth->check_access('Admin')) : 
			?>
			<div class="menu shadow">
				<div class="menu_title"><?php echo lang('common_catalog') ?></div>
				<a href="<?php echo $admin_url;?>categories"><?php echo lang('common_categories') ?></a>
				<a href="<?php echo $admin_url;?>products"><?php echo lang('common_products') ?></a>
				<a href="<?php echo $admin_url;?>digital_products"><?php echo lang('common_digital_products') ?></a>
			</div>
			
			<div class="menu shadow">
				<div class="menu_title"><?php echo lang('common_content') ?></div>
				<a href="<?php echo $admin_url;?>banners"><?php echo lang('common_banners') ?></a>
				<a href="<?php echo $admin_url;?>boxes"><?php echo lang('common_boxes') ?></a>
				<a href="<?php echo $admin_url;?>pages"><?php echo lang('common_pages') ?></a>
			</div>
	
			<div class="menu shadow">
				<div class="menu_title"><?php echo lang('common_administrative') ?></div>
				<a href="<?php echo $admin_url;?>settings"><?php echo lang('common_settings') ?></a>
				<a href="<?php echo $admin_url;?>locations"><?php echo lang('common_locations') ?></a>
				<a href="<?php echo $admin_url;?>admin"><?php echo lang('common_administrators') ?></a>
			</div>
			<?php endif; ?>
			
		</div>
		<div id="page_content">
			<?php
			//lets have the flashdata overright "$message" if it exists
			if($this->session->flashdata('message'))
			{
				$message	= $this->session->flashdata('message');
			}
			
			if($this->session->flashdata('error'))
			{
				$error	= $this->session->flashdata('error');
			}
			
			if(function_exists('validation_errors') && validation_errors() != '')
			{
				$error	= validation_errors();
			}
			?>
		
			<?php if (!empty($message)): ?>
			<div class="ui-state-highlight ui-corner-all" style="padding:10px; margin-bottom:10px;"> 
				<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
				<strong><?php echo lang('common_note') ?>:</strong> <?php echo $message; ?></p>
			</div>
			<?php endif; ?>
		
			<?php if (!empty($error)): ?>
			<div class="ui-state-error ui-corner-all" style="padding:10px; margin-bottom:10px;"> 
				<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
				<strong><?php echo lang('common_alert') ?>:</strong> <?php echo $error; ?></p>
			</div>
			<?php endif; ?>
			
			<div id="js_error_container" class="ui-state-error ui-corner-all" style="display:none; padding:10px; margin-bottom:10px;"> 
				<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
				<strong><?php echo lang('common_alert') ?>:</strong> <span id="js_error"></span> </p>
			</div>