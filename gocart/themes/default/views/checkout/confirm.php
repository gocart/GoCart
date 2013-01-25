<div class="page-header">
	<h2><?php echo lang('form_checkout');?></small></h2>
</div>
	
<?php include('order_details.php');?>
<?php include('summary.php');?>

<div class="row">
	<div class="span12">
		<a class="btn btn-primary btn-large btn-block" href="<?php echo site_url('checkout/place_order');?>"><?php echo lang('submit_order');?></a>
	</div>
</div>