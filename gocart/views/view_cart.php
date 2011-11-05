<?php include('header.php');?>
<script type="text/javascript">
if (top.location != location) {
	top.location.href = document.location.href;
}	
</script>


<?php if ($this->go_cart->total_items()==0):?>
	<div class="message">There are no products in your cart!</div>
<?php else: ?>
	<?php echo form_open('cart/update_cart');?>
	
	<table class="cart_table" cellpadding="0" cellspacing="0" border="0">
		<thead>
			<tr>
				<th style="width:10%;">SKU</th>
				<th style="width:20%;">Name</th>
				<th style="width:10%;">Price</th>
				<th>Description</th>
				<th style="text-align:center; width:10%;">Quantity</th>
				<th style="width:8%;">Totals</th>
			</tr>
		</thead>
		<tfoot>
			<tr class="tfoot_top"><td colspan="6"></td></tr>
			 <?php if($this->go_cart->group_discount() > 0)  : ?> 
	       	<tr>
				<td colspan="5" >Group Discount</td>
				<td><?php echo format_currency(0-$this->go_cart->group_discount()); ?>                </td>
			</tr>
			<?php endif; ?>
            <tr>
				<td colspan="5" >Subtotal</td>
				<td>
                <?php echo format_currency($this->go_cart->subtotal()); ?>                </td>
			</tr>
           <?php if($this->go_cart->coupon_discount() > 0)  : ?> 
        	<tr>
				<td colspan="5" >Coupon Discount</td>
				<td><?php echo format_currency(0-$this->go_cart->coupon_discount()); ?>                </td>
			</tr>
				 <?php if($this->go_cart->order_tax() != 0) : // Only show a discount subtotal if we still have taxes to add (to show what the tax is calculated from) ?> 
			<tr>
				<td colspan="5" >Discounted Subtotal</td>
				<td><?php echo format_currency($this->go_cart->discounted_subtotal()); ?>                </td>
			</tr>

           <?php endif;
           
           endif; 
          
           // Custom Charges
           $charges = $this->go_cart->get_custom_charges();
			if(!empty($charges))
			{
				foreach($charges as $name=>$price) : ?>
					
			<tr>
				<td colspan="6"><?php echo $name?></td>
				<td><?php echo format_currency($price); ?></td>
			</tr>	
					
			<?php endforeach;
			}
           
            // Show shipping cost if added before taxes
			if($this->config->item('tax_shipping') && $this->go_cart->shipping_cost()>0) : ?>
				<tr>
			<td colspan="5">Shipping</td>
				<td><?php echo format_currency($this->go_cart->shipping_cost()); ?>                </td>
			</tr>
			<?php endif ?>
           <?php if($this->go_cart->order_tax() != 0) : ?> 
         	<tr>
				<td colspan="5">Taxes</td>

				<td><?php echo format_currency($this->go_cart->order_tax()); ?>                </td>
			</tr>
          <?php endif;   ?>
           <?php // Show shipping cost if added after taxes
			if(!$this->config->item('tax_shipping') && $this->go_cart->shipping_cost()>0) : ?>
				<tr>
				<td colspan="5">Shipping</td>
				<td><?php echo format_currency($this->go_cart->shipping_cost()); ?>                </td>
			</tr>
			<?php endif ?>
           <?php if($this->go_cart->gift_card_discount() != 0) : ?> 
         	<tr>
				<td colspan="5">Gift Card</td>

				<td><?php echo format_currency(0-$this->go_cart->gift_card_discount()); ?>                </td>
			</tr>
          <?php endif;   ?>
            <tr class="cart_total"> 
				<td colspan="5">Cart Total</td>
				<td><?php echo format_currency($this->go_cart->total()); ?>                </td>
			</tr>
			<tr class="tfoot_bottom"><td colspan="6"></td></tr>
		</tfoot>

		<tbody id="cart_items">
		<?php
		$subtotal = 0;
		foreach ($this->go_cart->contents() as $cartkey=>$product):?>
			<tr class="cart_spacer"><td colspan="6"></td></tr>
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
				<td style="text-align:center;">
					<input type="text" style="width:30px;"name="cartkey[<?php echo $cartkey;?>]" value="<?php echo $product['quantity'];?>" size="3"/>		 
                    </td>
				<td><?php echo format_currency($product['price']*$product['quantity']); ?>				</td>
			</tr>
				
		<?php endforeach; ?>
		<tr class="cart_spacer"><td colspan="6"></td></tr>
		</tbody>
	</table>
<div class="view_cart_additions">
	<table>
		<tr>
			<td>
				If you have a coupon, enter the code here:<br/><input type="text" name="coupon_code">
				<input type="submit" value="Apply Coupon"/>
			</td>
		
	    <tr>
	      <td>&nbsp;</td>
	     </tr>
        <tr>
          <td>If you have a Gift Certificate, enter the code here:<br/><input type="text" name="gc_code">
				<input type="submit" value="Apply Gift Card"/>
		   </td>
        </tr>        
  </table>
</div>	
<div id="gc_view_cart_buttons">
	<span class="buttonset">
	<?php if(!$this->Customer_model->is_logged_in(false,false)): ?>
		<input type="button" onclick="window.location='<?php echo site_url('checkout/login');?>'" value="Login"/>
		<input type="button" onclick="window.location='<?php echo site_url('checkout/register');?>'" value="Register"/>
	<?php endif; ?>
		<input type="submit" value="Update Cart"/>
	</span>
	<?php if ($this->Customer_model->is_logged_in(false,false) || !$this->config->item('require_login')): ?>
			<input style="padding:10px 15px; font-size:16px;" type="button" onclick="window.location='<?php echo site_url('checkout');?>'" value="Checkout &raquo;"/>
	<?php endif; ?>
	
	
</div>
</form>
<?php endif; ?>

<script type="text/javascript">
	$('.buttonset').buttonset();
</script>
<?php include('footer.php'); ?>