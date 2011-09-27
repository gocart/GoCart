<?php

Class Shipping_model extends Cart_model
{
	function __construct()
	{
		Cart_model::__construct();	
	}
	
	function get_flatrate()
	{
		return 10.00;
	}
	
	function get_tablerate()
	{
		
		//$method either equals weight or price
		$method	= 'price';
		$table	= array(
		 '80'	=> '85.00'
		,'70'	=> '65.00'
		,'60'	=> '55.00'
		,'50'	=> '55.00'
		,'40'	=> '45.00'
		,'30'	=> '35.00'
		,'20'	=> '25.00'
		,'10'	=> '15.00'
		,'0'	=> '5.00'
		);
		
		
		if ($method == 'weight')
		{
			$order_weight	= $this->get_order_weight();
			$shipping_price	= '';
			foreach ($table as $weight => $rate)
			{
				$shipping_price = $rate;
				if($weight <= $order_weight)
				{
					break;
				}
			}
			echo $order_weight;
			return $shipping_price;
		}
		elseif ($method == 'price')
		{
			$order_price = $this->get_order_price();
			$shipping_price	= '';
			foreach ($table as $price => $rate)
			{
				$shipping_price = $rate;
				if($price <= $order_price)
				{
					break;
				}
			}
			return $shipping_price;
		}
	}
}
?>
