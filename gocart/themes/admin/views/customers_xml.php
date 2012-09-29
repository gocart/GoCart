<?php header ("Content-Type:text/xml"); 
echo '<?xml version="1.0" ?>';?>
<export>
<?php foreach($customers as $customer) : 
		$customer = (array)$customer;
?>
<customer>
<id><?php echo $customer['id'] ?></id>
<firstname><?php echo $customer['firstname'] ?></firstname>
<lastname><?php echo $customer['lastname'] ?></lastname>
<email><?php echo $customer['email'] ?></email>
<confirmed><?php echo $customer['confirmed'] ?></confirmed>
<email_subscribe><?php echo $customer['email_subscribe'] ?></email_subscribe>
<phone><?php echo $customer['phone'] ?></phone>
<company><?php echo $customer['company'] ?></company>
<group><?php $group = $this->Customer_model->get_group($customer['id']); echo ($group) ? $group->name : ''  ?></group>
<billing_address>
	<?php 
		$bill_address = $this->Customer_model->get_address($customer['default_billing_address']);
		if($bill_address)
		{
			foreach($bill_address['field_data'] as $k=>$v)
			  {
				echo "<$k>$v</$k>";
			  } 
		}
	?>
</billing_address>
<shipping_address>
	<?php 
		$ship_address = $this->Customer_model->get_address($customer['default_shipping_address']);
		if($ship_address)
		{
			foreach($ship_address['field_data'] as $k=>$v)
			  {
				echo "<$k>$v</$k>";
			  }  
		}
	?>
</shipping_address>

</customer>
<?php endforeach; ?>
</export>