id, firstname, lastname, email, phone, company, shipping company, shipping firstname, shipping lastname, shipping email, shipping phone, shipping address1, shipping address2, shipping city, shipping state, shipping post code, shipping country, billing company, billing firstname, billing lastname, billing email, billing phone, billing address1, billing address2, billing city, billing state, billing post code, billing country
<?php foreach($customers as $c) {

	$bill_address = $this->Customer_model->get_address($c->default_address);
	if(!$c->ship_to_bill_address)
	{
		$ship_address = $this->Customer_model->get_address($c->default_shipping_address);
	} else {
		$ship_address = $bill_address;
	}

	echo $c->id .', ';
	echo $c->firstname .', ';
	echo $c->lastname .', ';
	echo $c->email .', ';
	echo $c->phone .', ';
	echo $c->company .', ';
	echo @$ship_address['field_data']['company'] .', ';
	echo @$ship_address['field_data']['firstname'] .', ';
	echo @$ship_address['field_data']['lastname'] .', ';
	echo @$ship_address['field_data']['email'] .', ';
	echo @$ship_address['field_data']['phone'] .', ';
	echo @$ship_address['field_data']['address1'] .', ';
	echo @$ship_address['field_data']['address2'] .', ';
	echo @$ship_address['field_data']['city'] .', ';
	echo @$ship_address['field_data']['zone'] .', ';
	echo @$ship_address['field_data']['zip'] .', ';
	echo @$ship_address['field_data']['country'] .', ';
	
	echo @$bill_address['field_data']['company'] .', ';
	echo @$bill_address['field_data']['firstname'] .', ';
	echo @$bill_address['field_data']['lastname'] .', ';
	echo @$bill_address['field_data']['email'] .', ';
	echo @$bill_address['field_data']['phone'] .', ';
	echo @$bill_address['field_data']['address1'] .', ';
	echo @$bill_address['field_data']['address2'] .', ';
	echo @$bill_address['field_data']['city'] .', ';
	echo @$bill_address['field_data']['zone'] .', ';
	echo @$bill_address['field_data']['zip'] .', ';
	echo @$bill_address['field_data']['country'] ."\n";


} ?>