<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shipping
{
	var $CI;
	var $cart;
	
	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->library('session');
		$this->CI->load->model('Cart_model');
		$this->cart = $this->CI->session->userdata('cart');
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
			$order_weight	= $this->CI->Cart_model->get_order_weight();
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
			$order_price = $this->CI->Cart_model->get_order_price();
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
	
	function _get_order_weight()
	{
		$order_weight	= 0;
		foreach ($this->cart['content'] as $item)
		{
			$item_weight	= $item['weight']*$item['quantity'];
			$order_weight		= $order_weight + $item_weight;
		}
		
		return $order_weight;
	}
	
	function _get_order_price()
	{
		$order_price	= 0;
		foreach ($this->cart['content'] as $item)
		{
			$item_price	= $item['price']*$item['quantity'];
			$order_price	= $order_price + $item_price;
		}
		
		return $order_price;
	}
}
?>
