<?php include('header.php'); ?>

<script type="text/javascript">
	$(document).ready(function(){
		$('.customer_info_box').equalHeights();
	});
</script>
<h3><?php echo lang('order_number');?>: <?php echo $order_id;?></h3>

<?php
		// content defined in canned messages
	 echo $download_section ?>

<div class="customer_info_box">
	<h3><?php echo lang('account_information');?></h3>
	<strong><?php echo (!empty($customer['company']))?$customer['company'].'<br>':'';?>
	<?php echo $customer['firstname'];?> <?php echo $customer['lastname'];?></strong> <br/>
	<?php echo $customer['email'];?> <br/>
	<?php echo $customer['phone'];?>
</div>

<?php
$ship = $customer['ship_address'];
$bill = $customer['bill_address'];
?>
<div class="customer_info_box">
	<h3><?php echo ($ship != $bill)?'Shipping Information':'Billing &amp; Shipping Information';?></h3>
	<strong><?php echo (!empty($ship['company']))?$ship['company'].'<br/>':'';?>
	<?php echo $ship['firstname'].' '.$ship['lastname'];?> <br/>
	<?php echo $ship['address1'];?><br>
	<?php echo (!empty($ship['address2']))?$ship['address2'].'<br/>':'';?>
	<?php echo $ship['city'].', '.$ship['zone'].' '.$ship['zip'];?></strong><br/>
	
	<?php echo $ship['email'];?><br/>
	<?php echo $ship['phone'];?><br/>
</div>
<?php if($ship != $bill):?>
<div class="customer_info_box">
	<h3><?php echo lang('billing_information');?></h3>
	<strong><?php echo (!empty($bill['company']))?$bill['company'].'<br/>':'';?>
	<?php echo $bill['firstname'].' '.$bill['lastname'];?> <br/>
	<?php echo $bill['address1'];?><br>
	<?php echo (!empty($bill['address2']))?$bill['address2'].'<br/>':'';?>
	<?php echo $bill['city'].', '.$bill['zone'].' '.$bill['zip'];?></strong><br/>
	
	<?php echo $bill['email'];?><br/>
	<?php echo $bill['phone'];?>
</div>
<?php endif;?>
<div class="customer_info_box">
	<h3><?php echo lang('payment_information');?></h3>
	<?php echo $payment['description']; ?><br/>
	<h3 style="padding-top:10px;"><?php echo lang('shipping_method');?></h3>
	<?php echo $shipping['method']; ?>
</div>
<div class="customer_info_box">
	<h3><?php echo lang('additional_details');?></h3>
	<?php
	extract($additional_details);
	
	
	if(!empty($referral)):?><div style="margin-top:10px;"><strong><?php echo lang('heard_about');?></strong> <?php echo $referral;?></div><?php endif;?>
	<?php if(!empty($shipping_notes)):?><div style="margin-top:10px;"><strong><?php echo lang('shipping_instructions');?></strong> <?php echo $shipping_notes;?></div><?php endif;?>
		
</div>
<table class="cart_table" cellpadding="0" cellspacing="0" border="0">
	<thead>
		<tr>
			<th style="width:10%;"><?php echo lang('sku');?></th>
			<th style="width:20%;"><?php echo lang('name');?></th>
			<th style="width:10%;"><?php echo lang('price');?></th>
			<th><?php echo lang('description');?></th>
			<th style="text-align:center; width:10%;"><?php echo lang('quantity');?></th>
			<th style="width:8%;"><?php echo lang('totals');?></th>
		</tr>
	</thead>
	<tfoot>
		<tr class="tfoot_top"><td colspan="7"></td></tr>
		 <?php if($this->go_cart->group_discount() > 0)  : ?> 
       	<tr>
			<td colspan="5"><?php echo lang('group_discount');?></td>
			<td><?php echo format_currency(0-$this->go_cart->group_discount()); ?>                </td>
		</tr>
		<?php endif; ?>
        <tr>
			<td colspan="5"><?php echo lang('subtotal');?></td>
			<td>
            <?php echo format_currency($this->go_cart->subtotal()); ?>                </td>
		</tr>
       <?php if($this->go_cart->coupon_discount() > 0)  : ?> 
    	<tr>
			<td colspan="5"><?php echo lang('coupon_discount');?></td>
			<td><?php echo format_currency(0-$this->go_cart->coupon_discount()); ?>                </td>
		</tr>
			 <?php if($this->go_cart->order_tax() != 0) : // Only show a discount subtotal if we still have taxes to add (to show what the tax is calculated from) ?> 
		<tr>
			<td colspan="5"><?php echo lang('discounted_subtotal');?></td>
			<td><?php echo format_currency($this->go_cart->discounted_subtotal()); ?>                </td>
		</tr>

       <?php endif;

       endif; ?>
       <?php // Show shipping cost if added before taxes
		if($this->config->item('tax_shipping') && $this->go_cart->shipping_cost()>0) : ?>
		<tr>
			<td><?php echo lang('shipping');?></td>
			<td colspan="4"><?php echo $shipping['method']; ?></td>
			<td><?php echo format_currency($this->go_cart->shipping_cost()); ?>                </td>
		</tr>
		<?php endif ?>
       <?php if($this->go_cart->order_tax() != 0) : ?> 
     	<tr>
			<td colspan="5"><?php echo lang('taxes');?></td>

			<td><?php echo format_currency($this->go_cart->order_tax()); ?>                </td>
		</tr>
      <?php endif;   ?>
       <?php // Show shipping cost if added after taxes
		if(!$this->config->item('tax_shipping') && $this->go_cart->shipping_cost()>0) : ?>
		<tr>
			<td><?php echo lang('shipping');?></td>
			<td colspan="4" style="font-weight:normal;"><?php echo $shipping['method']; ?></td>
			<td><?php echo format_currency($this->go_cart->shipping_cost()); ?>                </td>
		</tr>
		<?php endif ?>
       <?php if($this->go_cart->gift_card_discount() != 0) : ?> 
     	<tr>
			<td colspan="5"><?php echo lang('gift_card');?></td>

			<td><?php echo format_currency(0-$this->go_cart->gift_card_discount()); ?>                </td>
		</tr>
      <?php endif;   ?>
        <tr class="cart_total"> 
			<td colspan="5"><?php echo lang('grand_total');?></td>
			<td><?php echo format_currency($this->go_cart->total()); ?>                </td>
		</tr>
		<tr class="tfoot_bottom"><td colspan="7"></td></tr>
	</tfoot>

	<tbody id="cart_items">
	<?php
	$subtotal = 0;
	foreach ($this->go_cart->contents() as $cartkey=>$product):?>
		<tr class="cart_spacer"><td colspan="7"></td></tr>
		<tr class="cart_item">
			<td><?php echo $product['sku'];?></td>
			<td><?php echo $product['name']; ?></td>
			<td><?php echo format_currency($product['base_price']);   ?></td>
			<td><?php echo $product['excerpt'];
				if(isset($product['options'])) {
					foreach ($product['options'] as $name=>$value)
					{
						if(is_array($value))
						{
							echo '<div><span class="gc_option_name">'.$name.':</span><br/>';
							foreach($value as $item)
								echo '- '.$item.'<br/>';
							echo '</div>';
						} 
						else 
						{
							echo '<div><span class="gc_option_name">'.$name.':</span> '.$value.'</div>';
						}
					}
				}
				?></td>
			<td style="text-align:center;"><?php echo $product['quantity'];?></td>
			<td><?php echo format_currency($product['price']*$product['quantity']); ?>				</td>
		</tr>
			
	<?php endforeach; ?>
	<tr class="cart_spacer"><td colspan="7"></td></tr>
	</tbody>
	</table>
<?php include('footer.php');