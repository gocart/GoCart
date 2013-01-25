	<table class="table table-striped table-bordered">
		<thead>
			<thead>
				<tr>
					<th style="width:10%;"><?php echo lang('sku');?></th>
					<th style="width:20%;"><?php echo lang('name');?></th>
					<th style="width:10%;"><?php echo lang('price');?></th>
					<th><?php echo lang('description');?></th>
					<th style="width:10%;"><?php echo lang('quantity');?></th>
					<th style="width:8%;"><?php echo lang('totals');?></th>
				</tr>
			</thead>
		</thead>
		
		<tfoot>
			<?php
			/**************************************************************
			Subtotal Calculations
			**************************************************************/
			?>
			<?php if($this->go_cart->group_discount() > 0)  : ?> 
        	<tr>
				<td colspan="5"><strong><?php echo lang('group_discount');?></strong></td>
				<td><?php echo format_currency(0-$this->go_cart->group_discount()); ?>                </td>
			</tr>
			<?php endif; ?>
			<tr>
		    	<td colspan="5"><strong><?php echo lang('subtotal');?></strong></td>
				<td id="gc_subtotal_price"><?php echo format_currency($this->go_cart->subtotal()); ?></td>
			</tr>
				
				
			<?php if($this->go_cart->coupon_discount() > 0) {?>
		    <tr>
		    	<td colspan="5"><strong><?php echo lang('coupon_discount');?></strong></td>
				<td id="gc_coupon_discount">-<?php echo format_currency($this->go_cart->coupon_discount());?></td>
			</tr>
				<?php if($this->go_cart->order_tax() != 0) { // Only show a discount subtotal if we still have taxes to add (to show what the tax is calculated from)?> 
				<tr>
		    		<td colspan="5"><strong><?php echo lang('discounted_subtotal');?></strong></td>
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
				<td colspan="5"><strong><?php echo $name?></strong></td>
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
				<td colspan="5"><strong><?php echo lang('shipping');?></strong></td>
				<td><?php echo format_currency($this->go_cart->shipping_cost()); ?></td>
			</tr>
			<?php endif;
			if($this->go_cart->order_tax() > 0) :  ?>
		    <tr>
		    	<td colspan="5"><strong><?php echo lang('tax');?></strong></td>
				<td><?php echo format_currency($this->go_cart->order_tax());?></td>
			</tr>
			<?php endif; 
			// Show shipping cost if added after taxes
			if(!$this->config->item('tax_shipping') && $this->go_cart->shipping_cost()>0) : ?>
				<tr>
				<td colspan="5"><strong><?php echo lang('shipping');?></strong></td>
				<td><?php echo format_currency($this->go_cart->shipping_cost()); ?></td>
			</tr>
			<?php endif ?>
			
			<?php
			/**************************************************************
			Gift Cards
			**************************************************************/
			if($this->go_cart->gift_card_discount() > 0) : ?>
			<tr>
				<td colspan="5"><strong><?php echo lang('gift_card_discount');?></strong></td>
				<td>-<?php echo format_currency($this->go_cart->gift_card_discount()); ?></td>
			</tr>
			<?php endif; ?>
			
			<?php
			/**************************************************************
			Grand Total
			**************************************************************/
			?>
			<tr>
				<td colspan="5"><strong><?php echo lang('grand_total');?></strong></td>
				<td><?php echo format_currency($this->go_cart->total()); ?></td>
			</tr>
		</tfoot>
		
		<tbody>
			<?php
			$subtotal = 0;

			foreach ($this->go_cart->contents() as $cartkey=>$product):?>
				<tr>
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
					
					<td style="white-space:nowrap">
						<?php if($this->uri->segment(1) == 'cart'): ?>
							<?php if(!(bool)$product['fixed_quantity']):?>
								<div class="control-group">
									<div class="controls">
										<div class="input-append">
											<input class="span1" style="margin:0px;" name="cartkey[<?php echo $cartkey;?>]"  value="<?php echo $product['quantity'] ?>" size="3" type="text"><button class="btn btn-danger" type="button" onclick="if(confirm('<?php echo lang('remove_item');?>')){window.location='<?php echo site_url('cart/remove_item/'.$cartkey);?>';}"><i class="icon-remove icon-white"></i></button>
										</div>
									</div>
								</div>
							<?php else:?>
								<?php echo $product['quantity'] ?>
								<input type="hidden" name="cartkey[<?php echo $cartkey;?>]" value="1"/>
								<button class="btn btn-danger" type="button" onclick="if(confirm('<?php echo lang('remove_item');?>')){window.location='<?php echo site_url('cart/remove_item/'.$cartkey);?>';}"><i class="icon-remove icon-white"></i></button>
							<?php endif;?>
						<?php else: ?>
							<?php echo $product['quantity'] ?>
						<?php endif;?>
					</td>
					<td><?php echo format_currency($product['price']*$product['quantity']); ?></td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>