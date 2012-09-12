<script type="text/javascript">
//if this page is loaded, it means that we can load payment and shipping info too.
$('#shipping_payment_container').show();
$('#submit_button_container').show();

$.post('<?php echo site_url('checkout/shipping_payment_methods');?>', function(data){
	$('#shipping_payment_container').html(data);
});
</script>
<?php
$bill	= $customer['bill_address'];
$ship	= $customer['ship_address'];
?>

<div class="row">
	<div style="float:right; text-align:right;">
		<img id="save_customer_loader" alt="loading" src="<?php echo theme_img('ajax-loader.gif');?>" style="display:none;"/> <button class="btn btn-inverse" type="button" onclick="get_customer_form();"><?php echo lang('edit_customer_information');?></button>
	</div>
	<div class="span6">
		<h2 style="margin-left:0px;"><?php echo ($ship != $bill)? lang('shipping_address'):lang('shipping_and_billing');?></h2>
		
		<p>
			<?php echo format_address($ship, true);?>
		</p>
		
		<p>
			<?php echo $ship['email'];?><br/>
			<?php echo $ship['phone'];?>
		</p>
	</div>
	
	
	<?php if($ship != $bill):?>
		<div class="span3">
			<h2 style="margin-left:0px;"><?php echo lang('billing_address');?></h2>

			<p>
				<?php echo format_address($bill, true);?>
			</p>

			<p>
				<?php echo $bill['email'];?><br/>
				<?php echo $bill['phone'];?>
			</p>
		</div>
	<?php endif;?>
</div>