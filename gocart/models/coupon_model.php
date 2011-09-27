<?php
/*

 - DB STRUCTURE
coupons
	id  (int)
	name (varchar)
	code (varchar)
	description (text)
	start_date (date)
	end_date (date)
	max_uses (int)
	num_uses (int)
	reduction_type (varchar) (percent or fixed)
	reduction_amount (float)
	

coupons_products
	coupon_id (int)
	product_id (int) (zero applies to all products?)
	sequence (int) ( for coupon product listings )

*/

class Coupon_model extends CI_Model 
{
	function __construct()
	{
		parent::__construct();
	}
	
	function save($coupon)
	{
		if(!$coupon['id']) 
		{
			return $this->add_coupon($coupon);
		} 
		else 
		{
			$this->update_coupon($coupon['id'], $coupon);
			return $coupon['id'];
		}
	}

	// add coupon, returns id
	function add_coupon($data) 
	{
		$this->db->insert('coupons', $data);
		return $this->db->insert_id();
	}
	
	// update coupon
	function update_coupon($id, $data)
	{
		$this->db->where('id', $id);
		$this->db->update('coupons', $data);
	}
	
	// delete coupon
	function delete_coupon($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('coupons');
	
		// delete children
		$this->remove_product($id);
	}
	
	// checks coupon dates and usage numbers
	function is_valid($coupon)
	{
		//$coupon = $this->get_coupon($id);
				
		if($coupon['max_uses']!=0 && $coupon['num_uses'] >= $coupon['max_uses'] ) return false;
		
		if($coupon['start_date'] != "0000-00-00")
		{
			$s_date = split("-", $coupon['start_date']);
			$start = mktime(0,0,0, $s_date[1], $s_date[2], $s_date[0]);
		
			$current = time();
		
			if($current < $start) return false;
		}
		
		if($coupon['end_date'] != "0000-00-00")
		{
			$e_date = split("-", $coupon['end_date']);
			$end = mktime(0,0,0, $e_date[1], (int) $e_date[2] +1 , $e_date[0]); // add a day to account for the end date as the last viable day
		
			$current = time();
		
			if($current > $end) return false;
		}
		
		return true;
	}
	
	// increment coupon uses
	function touch_coupon($code)
	{
		$this->db->where('code', $code);
		$this->db->set('num_uses','num_uses+1', false);
		$this->db->update('coupons');
	}
	
	// get coupons list, sorted by start_date (default), end_date
	function get_coupons($sort=NULL) 
	{
		if($sort=='end_date') {
			$this->db->order_by('end_date');
		} else {
			$this->db->order_by('start_date');
		}
		$res = $this->db->get('coupons');
		return $res->result();
	}
	
	// get coupon details, by id
	function get_coupon($id)
	{
		$this->db->where('id', $id);
		$res = $this->db->get('coupons');
		return $res->row();
	}
	
	// get coupon details, by code
	function get_coupon_by_code($code)
	{
		$this->db->where('code', $code);
		$res = $this->db->get('coupons');
		$return = $res->row_array();
		if(!$return) return false;
		$return['product_list'] = $this->get_product_ids($return['id']);
		return $return;
	}
	
	// get the next sequence number for a coupon products list 
	function get_next_sequence($coupon_id)
	{
		$this->db->select_max('sequence');
		$this->db->where('coupon_id',$coupon_id);
		$res = $this->db->get('coupons_products');
		$res = $res->row();
		return $res->sequence + 1;
	}
	
	function check_code($str, $id=false)
	{
		$this->db->select('code');
		$this->db->from('coupons');
		$this->db->where('code', $str);
		if ($id)
		{
			$this->db->where('id !=', $id);
		}
		$count = $this->db->count_all_results();
		
		if ($count > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	// add product to coupon
	function add_product($coupon_id, $prod_id, $seq=NULL)
	{
		// get the next seq
		if(is_null($seq))
			$seq = $this->get_next_sequence($coupon_id);
			
		$this->db->insert('coupons_products', array('coupon_id'=>$coupon_id, 'product_id'=>$prod_id, 'sequence'=>$seq));	
	}
	
	// remove product from coupon. Product id as null for removing all products
	function remove_product($coupon_id, $prod_id=NULL)
	{
		$where = array('coupon_id'=>$coupon_id);
		
		if(!is_null($prod_id))
			$where['product_id'] = $prod_id;
			
		$this->db->where($where);
		$this->db->delete('coupons_products');
	}
	
	// get list of products in coupon with full info
	function get_products($coupon_id) 
	{
		$this->db->from('coupons_products');
		$this->db->join("products", "product_id=products.id");
		$this->db->where('coupon_id', $coupon_id);
		$res = $this->db->get();
		return $res->result();
	}
	
	// Get list of product id's only - utility function
	function get_product_ids($coupon_id)
	{
		$this->db->select('product_id');
		$this->db->where('coupon_id', $coupon_id);
		$res = $this->db->get('coupons_products');
		$res = $res->result_array();
		$list = array();
		foreach($res as $item) {
			array_push($list, $item["product_id"]);	
		}
		return $list;
	}
	
	// set sequence number of product in coupon, for re-sorting
	function set_product_sequence($coupon_id, $prod_id, $seq)
	{
		$this->db->where(array('coupon_id'=>$coupon_id, 'product_id'=>$prod_id));
		$this->db->update('coupons_products', array('sequence'=>$seq));
	}
	
	
}	