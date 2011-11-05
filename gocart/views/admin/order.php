<?php include('header.php'); ?>

<div class="button_set">
	 <a title="Send Email Notification" id="notify" href="<?php echo site_url($this->config->item('admin_folder').'/orders/send_notification/'.$order->id); ?>">Send Notification Email</a> <a href="<?php echo site_url('admin/orders/packing_slip/'.$order->id);?>" target="_blank">Packing Slip</a>
</div>
<?php echo form_open($this->config->item('admin_folder').'/orders/view/'.$order->id);?>
<table class="order_table" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="gc_cell_left">Account Info</th>
			<th>Billing Address</th>
			<th class="gc_cell_right">Shipping Address</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<?php echo (!empty($order->company))?$order->company.'<br>':'';?>
				<?php echo $order->firstname;?> <?php echo $order->lastname;?> <br/>
				<?php echo $order->email;?> <br/>
				<?php echo $order->phone;?>
			</td>
			<td>
				<?php echo (!empty($order->bill_company))?$order->bill_company.'<br/>':'';?>
				<?php echo $order->bill_firstname.' '.$order->bill_lastname;?> <br/>
				<?php echo $order->bill_address1;?><br>
				<?php echo (!empty($order->bill_address2))?$order->bill_address2.'<br/>':'';?>
				<?php echo $order->bill_city.', '.$order->bill_zone.' '.$order->bill_zip;?><br/>
				<?php echo $order->bill_country;?><br/>
				
				<?php echo $order->bill_email;?><br/>
				<?php echo $order->bill_phone;?>
			</td>
			<td>
				<?php echo (!empty($order->ship_company))?$order->ship_company.'<br/>':'';?>
				<?php echo $order->ship_firstname.' '.$order->ship_lastname;?> <br/>
				<?php echo $order->ship_address1;?><br>
				<?php echo (!empty($order->ship_address2))?$order->ship_address2.'<br/>':'';?>
				<?php echo $order->ship_city.', '.$order->ship_zone.' '.$order->ship_zip;?><br/>
				<?php echo $order->ship_country;?><br/>
				
				<?php echo $order->ship_email;?><br/>
				<?php echo $order->ship_phone;?>
			</td>
		</tr>
		
		
		<tr>
			<td class="title">Order Details</td>
			<td class="title">Payment Method</td>
			<td class="title">Shipping Method/Instructions</td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="0">
					<?php if(!empty($order->referral)):?>
					<tr>
						<td><strong>Referral: </strong></td>
						<td><?php echo $order->referral;?></td>
					</tr>
					<?php endif;?>
					<?php if(!empty($order->is_gift)):?>
					<tr>
						<td colspan="2"><strong>This is a gift.</strong></td>
					</tr>
					<?php endif;?>
					<?php if(!empty($order->gift_message)):?>
					<tr>
						<td><strong>Gift Note: </strong></td>
						<td><?php echo $order->gift_message;?></td>
					</tr>
					<?php endif;?>
				</table>
				
			</td>
			<td>
				<?php echo $order->payment_info; ?>
			</td>
			<td>
				<div><?php echo $order->shipping_method; ?></div>
				<?php if(!empty($order->shipping_notes)):?><div style="margin-top:10px;"><?php echo $order->shipping_notes;?></div><?php endif;?>
			</td>
		</tr>
		
		
		<tr>
			<td class="title">Admin Notes</td>
			<td class="title"></td>
			<td class="title">Status</td>
		</tr>
		<tr>
			<td colspan="2">
				<textarea name="notes" style="width:100%;"><?php echo $order->notes;?></textarea>
			</td>
			<td>
				<?php
				echo form_dropdown('status', $this->config->item('order_statuses'), $order->status);
				?><br/>
				<input type="submit" class="button" value="Update Order"/>
			</td>
		</tr>
	</tbody>
		
</table>
</form>


<table class="gc_table" cellspacing="0" cellpadding="0" style="margin-top:10px;">
	<thead>
		<tr>
			<th class="gc_cell_left">Name</th>
			<th>Description</th>
			<th>Price</th>
			<th style="width:70px;">Quantity</th>
			<th class="gc_cell_right" style="width:130px;">Total</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($order->contents as $orderkey=>$product):?>
		<tr>
			<td>
				<?php echo $product['name'];?>
				<?php echo (trim($product['sku']) != '')?'<br/><small>sku: '.$product['sku'].'</small>':'';?>
				
			</td>
			<td>
				<?php //echo $product['excerpt'];?>
				<?php
				
				// Print options
				if(isset($product['options']))
				{
					foreach($product['options'] as $name=>$value)
					{
						$name = explode('-', $name);
						$name = trim($name[0]);
						if(is_array($value))
						{
							echo '<div>'.$name.':<br/>';
							foreach($value as $item)
							{
								echo '- '.$item.'<br/>';
							}	
							echo "</div>";
						}
						else
						{
							echo '<div>'.$name.': '.$value.'</div>';
						}
					}
				}
				
				if(isset($product['gc_status'])) echo $product['gc_status'];
				?>
			</td>
			<td><?php echo format_currency($product['price']);?></td>
			<td style="text-align:center;"><?php echo $product['quantity'];?></td>
			<td><?php echo format_currency($product['price']*$product['quantity']);?></td>
		</tr>
		<?php endforeach;?>
		<tr><td colspan="5" style="height:3px;overflow:hidden; background-color:#aaaaaa;"></td></tr>
		
		<?php if($order->coupon_discount > 0):?>
		<tr>
			<td><strong>Coupon Discount</strong></td>
			<td colspan="3"></td>
			<td><?php echo format_currency(0-$order->coupon_discount); ?></td>
		</tr>
		<?php endif;?>
		
		<tr>
			<td><strong>Subtotal</strong></td>
			<td colspan="3"></td>
			<td><?php echo format_currency($order->subtotal); ?></td>
		</tr>
		
		<?php 
		$charges = @$order->custom_charges;
		if(!empty($charges))
		{
			foreach($charges as $name=>$price) : ?>
				
		<tr>
			<td><strong><?php echo $name?></strong></td>
			<td colspan="3"></td>
			<td><?php echo format_currency($price); ?></td>
		</tr>	
				
		<?php endforeach;
		}
		?>

		
		<tr>
			<td><strong>Shipping</strong></td>
			<td colspan="3"><?php echo $order->shipping_method; ?></td>
			<td><?php echo format_currency($order->shipping); ?></td>
		</tr>
		
		<tr>
			<td><strong>Tax</strong></td>
			<td colspan="3"></td>
			<td><?php echo format_currency($order->tax); ?></td>
		</tr>
		<?php if($order->gift_card_discount > 0):?>
		<tr>
			<td><strong>Gift Card Discount</strong></td>
			<td colspan="3"></td>
			<td><?php echo format_currency(0-$order->gift_card_discount); ?></td>
		</tr>
		<?php endif;?>
		<tr>
			<td><strong>Total</strong></td>
			<td colspan="3"></td>
			<td><strong><?php echo format_currency($order->total); ?></strong></td>
		</tr>
	</tbody>
</table>

<script>
$('#notify').colorbox({
					width: '550px',
					height: '600px',
					iframe: true
			});
</script>
<?php include('footer.php'); ?>
