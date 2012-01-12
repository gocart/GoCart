<script>
$(function() {
	// keep the cart total up to date for other JS functionality
	cart_total = <?php echo $this->go_cart->total(); ?>;
	
	if(cart_total==0)
	{
		$('#payment_section_container').hide();
		$('#no_payment_necessary').show();
	} else {
		$('#payment_section_container').show();
		$('#no_payment_necessary').hide();
	}
});
</script>

	<table class="cart_table" cellpadding="0" cellspacing="0" border="0">
		<thead>
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
		</thead>
		
		<tfoot>
			<tr class="tfoot_top"><td colspan="6"></td></tr>
			<?php
			/**************************************************************
			Subtotal Calculations
			**************************************************************/
			?>
			<?php if($this->go_cart->group_discount() > 0)  : ?> 
        	<tr>
				<td colspan="5"><?php echo lang('group_discount');?></td>
				<td><?php echo format_currency(0-$this->go_cart->group_discount()); ?>                </td>
			</tr>
			<?php endif; ?>
			<tr>
		    	<td colspan="5"><?php echo lang('subtotal');?></td>
				<td id="gc_subtotal_price"><?php echo format_currency($this->go_cart->subtotal()); ?></td>
			</tr>
				
				
			<?php if($this->go_cart->coupon_discount() > 0) {?>
		    <tr>
		    	<td colspan="5"><?php echo lang('coupon_discount');?></td>
				<td id="gc_coupon_discount">-<?php echo format_currency($this->go_cart->coupon_discount());?></td>
			</tr>
				<?php if($this->go_cart->order_tax() != 0) { // Only show a discount subtotal if we still have taxes to add (to show what the tax is calculated from)?> 
				<tr>
		    		<td colspan="5"><?php echo lang('discounted_subtotal');?></td>
					<td id="gc_coupon_discount"><?php echo format_currency($this->go_cart->discounted_subtotal());?></td>
				</tr>
				<?php
				}
			} 
			/**************************************************************
			 Custom charges
			**************************************************************/
			$charges = $this->go_cart->get_custom_charges();
			if(!empty($charges))
			{
				foreach($charges as $name=>$price) : ?>
					
			<tr>
				<td colspan="5"><?php echo $name?></td>
				<td><?php echo format_currency($price); ?></td>
			</tr>	
					
			<?php endforeach;
			}	
			
			/**************************************************************
			Order Taxes
			**************************************************************/
			 // Show shipping cost if added before taxes
			if($this->config->item('tax_shipping') && $this->go_cart->shipping_cost()>0) : ?>
				<tr>
				<td colspan="5"><?php echo lang('shipping');?></td>
				<td id="gc_tax_price"><?php echo format_currency($this->go_cart->shipping_cost()); ?></td>
			</tr>
			<?php endif;
			if($this->go_cart->order_tax() > 0) :  ?>
		    <tr>
		    	<td colspan="5" colspan="3"><?php echo lang('tax');?></td>
				<td id="gc_tax_price"><?php echo format_currency($this->go_cart->order_tax());?></td>
			</tr>
			<?php endif; 
			// Show shipping cost if added after taxes
			if(!$this->config->item('tax_shipping') && $this->go_cart->shipping_cost()>0) : ?>
				<tr>
				<td colspan="5"><?php echo lang('shipping');?></td>
				<td id="gc_tax_price"><?php echo format_currency($this->go_cart->shipping_cost()); ?></td>
			</tr>
			<?php endif ?>
			
			<?php
			/**************************************************************
			Gift Cards
			**************************************************************/
			if($this->go_cart->gift_card_discount() > 0) : ?>
			<tr>
				<td colspan="5"><?php echo lang('gift_card_discount');?></td>
				<td id="gc_gift_discount">-<?php echo format_currency($this->go_cart->gift_card_discount()); ?></td>
			</tr>
			<?php endif; ?>
			
			<?php
			/**************************************************************
			Grand Total
			**************************************************************/
			?>
			<tr class="cart_total">
				<td colspan="5" class="gc_view_cart_totals"><div class="cart_total_line_left"></div><?php echo lang('grand_total');?></td>
				<td id="gc_total_price" class="gc_total"><div class="cart_total_line_right"></div><?php echo format_currency($this->go_cart->total()); ?></td>
			</tr>
			
			<tr class="tfoot_bottom"><td colspan="6"></td></tr>
		</tfoot>
		
		<tbody class="cart_items">
			<?php
			$subtotal = 0;

			foreach ($this->go_cart->contents() as $cartkey=>$product):?>
				<tr class="cart_spacer"><td colspan="7"></td></tr>
				<tr class="cart_item">
					<td><?php echo $product['sku']; ?></td>
					<td><?php echo $product['name']; ?></td>
					<td><?php echo format_currency($product['price']);?></td>
					<td>
						<?php echo $product['excerpt'];
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
							?>
					</td>
					
					<td style="text-align:center;">
						<?php echo $product['quantity'];?>
					</td>
					<td class="total"><?php echo format_currency($product['price']*$product['quantity']); ?></td>
				</tr>
			<?php endforeach;?>
			<tr class="cart_spacer"><td colspan="7"></td></tr>
		</tbody>
	</table>