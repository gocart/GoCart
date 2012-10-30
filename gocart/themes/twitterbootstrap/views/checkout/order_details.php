<div class="row">
	<?php if(!empty($customer['bill_address'])):?>
	<div class="span3">
		<a href="<?php echo site_url('checkout/step_1');?>" class="btn btn-block">
		
			<?php if($customer['bill_address'] != @$customer['ship_address'])
			{
				echo lang('billing_address_button');
			}
			else
			{
				echo lang('address_button');
			}
			?>
		</a>

		<p>
			<?php echo format_address($customer['bill_address'], true);?>
		</p>
		<p>
			<?php echo $customer['bill_address']['phone'];?><br/>
			<?php echo $customer['bill_address']['email'];?>
		</p>
	</div>
	<?php endif;?>

<?php if(config_item('require_shipping')):?>
	<?php if($this->go_cart->requires_shipping()):?>
		<div class="span3">
			<a href="<?php echo site_url('checkout/shipping_address');?>" class="btn btn-block"><?php echo lang('shipping_address_button');?></a>
			<p>
				<?php echo format_address($customer['ship_address'], true);?>
			</p>
			<p>
				<?php echo $customer['ship_address']['phone'];?><br/>
				<?php echo $customer['ship_address']['email'];?><br/>
			</p>
		</div>

		<?php
		
		if(!empty($shipping_method) && !empty($shipping_method['method'])):?>
		<div class="span3">
			<p><a href="<?php echo site_url('checkout/step_2');?>" class="btn btn-block"><?php echo lang('shipping_method_button');?></a></p>
			<strong><?php echo lang('shipping_method');?></strong><br/>
			<?php echo $shipping_method['method'].': '.format_currency($shipping_method['price']);?>
		</div>
		<?php endif;?>
	<?php endif;?>
<?php endif;?>

<?php if(!empty($payment_method)):?>
	<div class="span3">
		<p><a href="<?php echo site_url('checkout/step_3');?>" class="btn btn-block"><?php echo lang('billing_method_button');?></a></p>
		<?php echo $payment_method['description'];?>
	</div>
<?php endif;?>
</div>