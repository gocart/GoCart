<?php include('header.php'); ?>
<div class="row">
	<div class="span12">
		<div class="btn-group pull-right">
			<a class="btn" title="<?php echo lang('send_email_notification');?>" onclick="$('#notification_form').slideToggle();"><i class="icon-envelope"></i> <?php echo lang('send_email_notification');?></a>
			<a class="btn" href="<?php echo site_url('admin/orders/packing_slip/'.$order->id);?>" target="_blank"><i class="icon-file"></i><?php echo lang('packing_slip');?></a>
		</div>
	</div>
</div>

<script type="text/javascript">
// store message content in JS to eliminate the need to do an ajax call with every selection
var messages = <?php
	$messages	= array();
	foreach($msg_templates as $msg)
	{
		$messages[$msg['id']]= array('subject'=>$msg['subject'], 'content'=>$msg['content']);
	}
	echo json_encode($messages);
	?>;
//alert(messages[3].subject);
// store customer name information, so names are indexed by email
var customer_names = <?php 
	echo json_encode(array(
		$order->email=>$order->firstname.' '.$order->lastname,
		$order->ship_email=>$order->ship_firstname.' '.$order->ship_lastname,
		$order->bill_email=>$order->bill_firstname.' '.$order->bill_lastname
	));
?>;
// use our customer names var to update the customer name in the template
function update_name()
{
	if($('#canned_messages').val().length>0)
	{
		set_canned_message($('#canned_messages').val());
	}
}

function set_canned_message(id)
{
	// update the customer name variable before setting content	
	$('#msg_subject').val(messages[id]['subject'].replace(/{customer_name}/g, customer_names[$('#recipient_name').val()]));
}	
</script>

<div id="notification_form" class="row" style="display:none;">
	<div class="span12">
		<?php echo form_open($this->config->item('admin_folder').'/orders/send_notification/'.$order->id);?>
			<fieldset>
				<label><?php echo lang('message_templates');?></label>
				<select id="canned_messages" onchange="set_canned_message(this.value)" class="span12">
					<option><?php echo lang('select_canned_message');?></option>
					<?php foreach($msg_templates as $msg)
					{
						echo '<option value="'.$msg['id'].'">'.$msg['name'].'</option>';
					}
					?>
				</select>

				<label><?php echo lang('recipient');?></label>
				<select name="recipient" onchange="update_name()" id="recipient_name" class='span12'>
					<?php 
						if(!empty($order->email))
						{
							echo '<option value="'.$order->email.'">'.lang('account_main_email').' ('.$order->email.')';
						}
						if(!empty($order->ship_email))
						{
							echo '<option value="'.$order->ship_email.'">'.lang('shipping_email').' ('.$order->ship_email.')';
						}
						if($order->bill_email != $order->ship_email)
						{
							echo '<option value="'.$order->bill_email.'">'.lang('billing_email').' ('.$order->bill_email.')';
						}
					?>
				</select>

				<label><?php echo lang('subject');?></label>
				<input type="text" name="subject" size="40" id="msg_subject" class="span12"/>

				<label><?php echo lang('message');?></label>
				<textarea id="content_editor" name="content" class="redactor"></textarea>

				<div class="form-actions">
					<input type="submit" class="btn btn-primary" value="<?php echo lang('send_message');?>" />
				</div>
			</fieldset>
		</form>
	</div>
</div>

<div class="row" style="margin-top:10px;">
	<div class="span4">
		<h3><?php echo lang('account_info');?></h3>
		<p>
		<?php echo (!empty($order->company))?$order->company.'<br>':'';?>
		<?php echo $order->firstname;?> <?php echo $order->lastname;?> <br/>
		<?php echo $order->email;?> <br/>
		<?php echo $order->phone;?>
		</p>
	</div>
	<div class="span4">
		<h3><?php echo lang('billing_address');?></h3>
		<?php echo (!empty($order->bill_company))?$order->bill_company.'<br/>':'';?>
		<?php echo $order->bill_firstname.' '.$order->bill_lastname;?> <br/>
		<?php echo $order->bill_address1;?><br>
		<?php echo (!empty($order->bill_address2))?$order->bill_address2.'<br/>':'';?>
		<?php echo $order->bill_city.', '.$order->bill_zone.' '.$order->bill_zip;?><br/>
		<?php echo $order->bill_country;?><br/>
		
		<?php echo $order->bill_email;?><br/>
		<?php echo $order->bill_phone;?>
	</div>
	<div class="span4">
		<h3><?php echo lang('shipping_address');?></h3>
		<?php echo (!empty($order->ship_company))?$order->ship_company.'<br/>':'';?>
		<?php echo $order->ship_firstname.' '.$order->ship_lastname;?> <br/>
		<?php echo $order->ship_address1;?><br>
		<?php echo (!empty($order->ship_address2))?$order->ship_address2.'<br/>':'';?>
		<?php echo $order->ship_city.', '.$order->ship_zone.' '.$order->ship_zip;?><br/>
		<?php echo $order->ship_country;?><br/>
		
		<?php echo $order->ship_email;?><br/>
		<?php echo $order->ship_phone;?>
	</div>
</div>

<div class="row" style="margin-top:20px;">
	<div class="span4">
		<h3><?php echo lang('order_details');?></h3>
		<p>
		<?php if(!empty($order->referral)):?>
			<strong><?php echo lang('referral');?>: </strong><?php echo $order->referral;?><br/>
		<?php endif;?>
		<?php if(!empty($order->is_gift)):?>
			<strong><?php echo lang('is_gift');?></strong>
		<?php endif;?>
		
		<?php if(!empty($order->gift_message)):?>
			<strong><?php echo lang('gift_note');?></strong><br/>
			<?php echo $order->gift_message;?>
		<?php endif;?>
		</p>
	</div>
	<div class="span4">
		<h3><?php echo lang('payment_method');?></h3>
		<p><?php echo $order->payment_info; ?></p>
	</div>
	<div class="span4">
		<h3><?php echo lang('shipping_details');?></h3>
		<?php echo $order->shipping_method; ?>
		<?php if(!empty($order->shipping_notes)):?><div style="margin-top:10px;"><?php echo $order->shipping_notes;?></div><?php endif;?>
	</div>
</div>

<?php echo form_open($this->config->item('admin_folder').'/orders/view/'.$order->id, 'class="form-inline"');?>
<fieldset>
	<div class="row" style="margin-top:20px;">
		<div class="span8">
			<h3><?php echo lang('admin_notes');?></h3>
			<textarea name="notes" class="span8"><?php echo $order->notes;?></textarea>
		</div>

	
		<div class="span4">
			<h3><?php echo lang('status');?></h3>
			<?php
			echo form_dropdown('status', $this->config->item('order_statuses'), $order->status, 'class="span4"');
			?>
			
		</div>
	</div>
	
	<div class="form-actions">
		<input type="submit" class="btn btn-primary" value="<?php echo lang('update_order');?>"/>
	</div>
</fieldset>
</form>


<table class="table table-striped">
	<thead>
		<tr>
			<th><?php echo lang('name');?></th>
			<th><?php echo lang('description');?></th>
			<th><?php echo lang('price');?></th>
			<th><?php echo lang('quantity');?></th>
			<th><?php echo lang('total');?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($order->contents as $orderkey=>$product):?>
		<tr>
			<td>
				<?php echo $product['name'];?>
				<?php echo (trim($product['sku']) != '')?'<br/><small>'.lang('sku').': '.$product['sku'].'</small>':'';?>
				
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
			<td><?php echo $product['quantity'];?></td>
			<td><?php echo format_currency($product['price']*$product['quantity']);?></td>
		</tr>
		<?php endforeach;?>
		</tbody>
		<tfoot>
		<?php if($order->coupon_discount > 0):?>
		<tr>
			<td><strong><?php echo lang('coupon_discount');?></strong></td>
			<td colspan="3"></td>
			<td><?php echo format_currency(0-$order->coupon_discount); ?></td>
		</tr>
		<?php endif;?>
		
		<tr>
			<td><strong><?php echo lang('subtotal');?></strong></td>
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
			<td><strong><?php echo lang('shipping');?></strong></td>
			<td colspan="3"><?php echo $order->shipping_method; ?></td>
			<td><?php echo format_currency($order->shipping); ?></td>
		</tr>
		
		<tr>
			<td><strong><?php echo lang('tax');?></strong></td>
			<td colspan="3"></td>
			<td><?php echo format_currency($order->tax); ?></td>
		</tr>
		<?php if($order->gift_card_discount > 0):?>
		<tr>
			<td><strong><?php echo lang('giftcard_discount');?></strong></td>
			<td colspan="3"></td>
			<td><?php echo format_currency(0-$order->gift_card_discount); ?></td>
		</tr>
		<?php endif;?>
		<tr>
			<td><h3><?php echo lang('total');?></h3></td>
			<td colspan="3"></td>
			<td><strong><?php echo format_currency($order->total); ?></strong></td>
		</tr>
	</tfoot>
</table>

<?php include('footer.php');