<?php include('header.php');?>

<?php if ($this->go_cart->total_items()==0):?>
	<div class="alert alert-info">
		<a class="close" data-dismiss="alert">×</a>
		<?php echo lang('empty_view_cart');?>
	</div>
<?php else: ?>
	
	<div class="page-header">
		<h2><?php echo lang('your_cart');?></h2>
	</div>
	<?php echo form_open('cart/update_cart', array('id'=>'update_cart_form'));?>
	
	<table class="table table-bordered table-striped">
		
		<thead>
			<tr>
				<th style="width:10%;"><?php echo lang('sku');?></th>
				<th style="width:20%;"><?php echo lang('name');?></th>
				<th style="width:10%;"><?php echo lang('price');?></th>
				<th><?php echo lang('description');?></th>
				<th style="width:15%;"><?php echo lang('quantity');?></th>
				<th style="width:8%;"><?php echo lang('totals');?></th>
			</tr>
		</thead>
		
		<tfoot>
			<?php if($this->go_cart->group_discount() > 0)  : ?> 
			<tr>
				<td colspan="5"><strong><?php echo lang('group_discount');?></strong></td>
				<td><?php echo format_currency(0-$this->go_cart->group_discount()); ?></td>
			</tr>
			<?php endif; ?>

			<tr>
				<td colspan="5"><strong><?php echo lang('subtotal');?></strong></td>
				<td><?php echo format_currency($this->go_cart->subtotal()); ?></td>
			</tr>
			
			<?php if($this->go_cart->coupon_discount() > 0)  : ?> 
			<tr>
				<td colspan="5"><strong><?php echo lang('coupon_discount');?></strong></td>
				<td><?php echo format_currency(0-$this->go_cart->coupon_discount()); ?></td>
			</tr>
			
			<?php if($this->go_cart->order_tax() != 0) : // Only show a discount subtotal if we still have taxes to add (to show what the tax is calculated from) ?> 
			<tr>
				<td colspan="5"><strong><?php echo lang('discounted_subtotal');?></strong></td>
				<td><?php echo format_currency($this->go_cart->discounted_subtotal()); ?></td>
			</tr>

			<?php endif;
			endif; 
			// Custom Charges
			$charges = $this->go_cart->get_custom_charges();
			if(!empty($charges)):
				foreach($charges as $name=>$price) : ?>
					<tr>
						<td colspan="5"><strong><?php echo $name?></strong></td>
						<td><?php echo format_currency($price); ?></td>
					</tr>	
					
			<?php endforeach;
			endif;
           
            // Show shipping cost if added before taxes
			if($this->config->item('tax_shipping') && $this->go_cart->shipping_cost()>0) : ?>
				<tr>
			<td colspan="5"><strong><?php echo lang('shipping');?></strong></td>
				<td><?php echo format_currency($this->go_cart->shipping_cost()); ?></td>
			</tr>
			<?php endif ?>
			<?php if($this->go_cart->order_tax() != 0) : ?> 
			<tr>
				<td colspan="5"><strong><?php echo lang('taxes');?></strong></td>
				<td><?php echo format_currency($this->go_cart->order_tax()); ?></td>
			</tr>
			<?php endif;   ?>
			
			<?php // Show shipping cost if added after taxes
			if(!$this->config->item('tax_shipping') && $this->go_cart->shipping_cost()>0) : ?>
				<tr>
				<td colspan="5"><strong><?php echo lang('shipping');?></strong></td>
				<td><?php echo format_currency($this->go_cart->shipping_cost()); ?></td>
			</tr>
			<?php endif ?>
			
			<?php if($this->go_cart->gift_card_discount() != 0) : ?> 
			<tr>
				<td colspan="5"><strong><?php echo lang('gift_card_discount');?></strong></td>
				<td><?php echo format_currency(0-$this->go_cart->gift_card_discount()); ?></td>
			</tr>
			<?php endif;?>

			<tr class="cart_total"> 
				<td colspan="5"><strong><?php echo lang('cart_total');?></strong></td>
				<td><?php echo format_currency($this->go_cart->total()); ?></td>
			</tr>
		</tfoot>

		<tbody>
		<?php
		$subtotal = 0;
		foreach ($this->go_cart->contents() as $cartkey=>$product):?>
			<tr>
				<td><?php echo $product['sku'];?></td>
				<td><?php echo $product['name']; ?></td>
				<td><?php echo format_currency($product['base_price']);?></td>
				<td><?php echo $product['excerpt'];
					if(isset($product['options'])) {
						foreach ($product['options'] as $name=>$value)
						{
							if(is_array($value))
							{
								echo '<div>'.$name.':<br/>';
								foreach($value as $item)
									echo '- '.$item.'<br/>';
								echo '</div>';
							} 
							else 
							{
								echo '<div>'.$name.':'.$value.'</div>';
							}
						}
					}
					?></td>
				<td>
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
				</td>
				<td><?php echo format_currency($product['price']*$product['quantity']); ?></td>
			</tr>
				
		<?php endforeach; ?>
		</tbody>
	</table>
	
	
	<div class="row">
		<div class="span5">
			<label><?php echo lang('coupon_label');?></label>
			<input type="text" name="coupon_code" class="span3" style="margin:0px;">
			<input class="span2 btn" type="submit" value="<?php echo lang('apply_coupon');?>"/>
			
			<?php if($gift_cards_enabled):?>
				<label style="margin-top:15px;"><?php echo lang('gift_card_label');?></label>
				<input type="text" name="gc_code" class="span3" style="margin:0px;">
				<input class="span2 btn"  type="submit" value="<?php echo lang('apply_gift_card');?>"/>
			<?php endif;?>
		</div>
		
		<div class="span7" style="text-align:right;">
				<input id="redirect_path" type="hidden" name="redirect" value=""/>
	
				<?php if(!$this->Customer_model->is_logged_in(false,false)): ?>
					<input class="btn" type="submit" onclick="$('#redirect_path').val('checkout/login');" value="<?php echo lang('login');?>"/>
					<input class="btn" type="submit" onclick="$('#redirect_path').val('checkout/register');" value="<?php echo lang('register_now');?>"/>
				<?php endif; ?>
					<input class="btn" type="submit" value="<?php echo lang('form_update_cart');?>"/>
					
			<?php if ($this->Customer_model->is_logged_in(false,false) || !$this->config->item('require_login')): ?>
				<input class="btn btn-large btn-primary" type="submit" onclick="$('#redirect_path').val('checkout');" value="<?php echo lang('form_checkout');?>"/>
			<?php endif; ?>
			
		</div>
	</div>

</form>
<?php endif; ?>

<?php include('footer.php'); ?>