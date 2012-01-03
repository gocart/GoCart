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

<div id="shipping_address">
	<div class="form_wrap">
		<h3><?php echo ($ship != $bill)? lang('shipping_address'):lang('shipping_and_billing');?></h3>
		<strong><?php echo (!empty($ship['company']))?$ship['company'].'<br/>':'';?>
		<?php echo $ship['firstname'].' '.$ship['lastname'];?> <br/>
		<?php echo $ship['address1'];?><br>
		<?php echo (!empty($ship['address2']))?$ship['address2'].'<br/>':'';?>
		<?php echo $ship['city'].', '.$ship['zone'].' '.$ship['zip'];?><br/>
		<?php echo $ship['country'];?></strong><br/>
		
		<?php echo $ship['email'];?><br/>
		<?php echo $ship['phone'];?>
	</div>
</div>

<?php if($ship != $bill):?>
<div id="billing_address">
	<div class="form_wrap">
		<h3><?php echo lang('billing_address');?></h3>
		<strong><?php echo (!empty($bill['company']))?$bill['company'].'<br/>':'';?>
		<?php echo $bill['firstname'].' '.$bill['lastname'];?> <br/>
		<?php echo $bill['address1'];?><br>
		<?php echo (!empty($bill['address2']))?$bill['address2'].'<br/>':'';?>
		<?php echo $bill['city'].', '.$bill['zone'].' '.$bill['zip'];?><br/>
		<?php echo $bill['country'];?></strong><br/>
		
		<?php echo $bill['email'];?><br/>
		<?php echo $bill['phone'];?>
	</div>
</div>
<?php endif;?>
<br style="clear:both;"/>	

<table style="margin-top:10px;">
	<tr>
		<td><input type="button" value="<?php echo lang('edit_customer_information');?>" onclick="get_customer_form();"/></td>
		<td><img id="save_customer_loader" alt="loading" src="<?php echo base_url('images/ajax-loader.gif');?>" style="display:none;"/></td>
	</tr>
</table>

	
