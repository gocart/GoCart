<?php
Class Cart_model extends CI_Model
{	
	var $cart; 
	function __construct()
	{
		parent::__construct();
		$this->cart = $this->session->userdata('cart');	
	}
	
	/********************************************************************

	********************************************************************/
	
	function get_order($by='id', $string)
	{
		/*
		get_order() will only return the higher end order information
		it does not return order items or item options
		
		$by can be id, customer_id, or session_id
		*/
		$this->db->where($id, $string);
		$result	= $this->db->get('order');
		return $result->row();
	}
	
	function get_order_items($order_id)
	{
		$this->db_where('order_id', $id);
		$result	= $this->db->get('order_items');
		return $result->results();
	}
	
	function get_order_item_options($item_id)
	{
		$this->db_where('item_id', $id);
		$result	= $this->db->get('order_item_options');
		return $result->results();
	}
	
	function get_order_weight()
	{
		$order_weight	= 0;
		foreach ($this->cart['content'] as $item)
		{
			$item_weight	= $item['weight']*$item['quantity'];
			$order_weight		= $order_weight + $item_weight;
		}
		
		return $order_weight;
	}
	
	function get_order_price()
	{
		$order_price	= 0;
		foreach ($this->cart['content'] as $item)
		{
			$item_price	= $item['price']*$item['quantity'];
			$order_price	= $order_price + $item_price;
		}
		
		return $order_price;
	}
	
	function get_total()
	{
		$order_price	= $this->get_order_price();
		$tax			= $this->cart['taxes'];
		$shipping		= $this->cart['shipping'];
		$discount		= isset($this->cart['coupon_discount']) ? $this->cart['coupon_discount'] : 0;
		
		$total			= ($order_price + $tax + $shipping) - $discount;
		
		return number_format($total, '2');
	}
}
?>
