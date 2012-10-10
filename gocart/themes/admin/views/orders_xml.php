<?php header ("Content-Type:text/xml"); 
echo '<?xml version="1.0" ?>';?>

<export>
<?php foreach($orders as $order):?>
<order>
<id><?php echo $order->id;?></id>
<number><?php echo $order->order_number;?></number>
<customer<?php echo (!empty($order->customer_id))?' id="'.$order->customer_id.'"':'';?>>

<id><?php echo $order->customer_id ?></id>
<firstname><?php echo $order->firstname; ?></firstname>
<lastname><?php echo $order->lastname; ?></lastname>
<email><?php echo $order->email; ?></email>
<phone><?php echo $order->phone; ?></phone>
<company><?php echo $order->company; ?></company>
<group><?php $group = $this->customer_model->get_group($order->id); echo ($group) ? $group->name : ''  ?></group>
<ship_address>
	<company><?php echo $order->ship_company;?></company>
	<firstname><?php echo $order->ship_firstname;?></firstname>
	<lastname><?php echo $order->ship_lastname;?></lastname>
	<address1><?php echo $order->ship_address1;?></address1>
	<address2><?php echo $order->ship_address2;?></address2>
	<city><?php echo $order->ship_city;?></city>
	<state><?php echo $order->ship_zone;?></state>
	<zip><?php echo $order->ship_zip;?></zip>
	<country><?php echo $order->ship_country;?></country>
	<email><?php echo $order->ship_email;?></email>
	<phone><?php echo $order->ship_phone;?></phone>
</ship_address>
<bill_address>
	<company><?php echo $order->bill_company;?></company>
	<firstname><?php echo $order->bill_firstname;?></firstname>
	<lastname><?php echo $order->bill_lastname;?></lastname>
	<address1><?php echo $order->bill_address1;?></address1>
	<address2><?php echo $order->bill_address2;?></address2>
	<city><?php echo $order->bill_city;?></city>
	<state><?php echo $order->bill_zone;?></state>
	<zip><?php echo $order->bill_zip;?></zip>
	<country><?php echo $order->bill_country;?></country>
	<email><?php echo $order->bill_email;?></email>
	<phone><?php echo $order->bill_phone;?></phone>
</bill_address>
</customer>
<status><?php echo $order->status;?></status>
<ordered_on><?php echo $order->ordered_on;?></ordered_on>
<shipped_on><?php echo $order->shipped_on;?></shipped_on>
<tax><?php echo $order->tax;?></tax>
<total><?php echo $order->total;?></total>
<subtotal><?php echo $order->subtotal;?></subtotal>
<gift_card_discount><?php echo $order->gift_card_discount;?></gift_card_discount>
<coupon_discount><?php echo $order->coupon_discount;?></coupon_discount>
<shipping><?php echo $order->shipping;?></shipping>
<shipping_notes><?php echo $order->shipping_notes;?></shipping_notes>
<shipping_method><?php echo strip_tags(htmlentities($order->shipping_method));?></shipping_method>
<heard_about><?php echo $order->heard_about;?></heard_about>
<notes><?php echo $order->notes;?></notes>
<payment_info><?php echo $order->payment_info;?></payment_info>
<referral><?php echo $order->referral;?></referral>
<content>
<?php foreach($order->items as $item):?>
<item <?php echo (!empty($item->id))?' id="'.$item->id.'"':''?>>
<?php foreach($item as $k => $v):?>
<<?php 		echo $k; ?>>
<?php 		if($k=='options'): ?>
<?php     		foreach($v as $key=>$value):?>		
<option>
<name>
<?php echo $key; ?>
</name>
<answer>
<?php echo $value; ?>
</answer>
</option>
<?php 			endforeach; ?>
<?php 		else:
echo $v; 
endif;?>
</<?php 	echo $k; ?>>
<?php 
 endforeach;?></item>
<?php endforeach;?>
</content>
</order>
<?php endforeach;?>
</export>