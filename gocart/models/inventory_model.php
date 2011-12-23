<?php
class Inventory_model extends CI_Model {

	function product_inventory($pid) 
	{
		$q = $this->db
				->select('*')
				->from('inventory')
				->where('pid', $pid)
				->order_by('date', 'desc')
				->get();
	
		if($q->num_rows()) 
		{
			return $q->result();
		}
		else
		{
			return FALSE;
		}
	}
	
	function add_inventory($pid, $qty, $cost) 
	{
		if( $qty && $cost ) 
		{
			$this->db->set('date','NOW()', FALSE);
			$this->db->insert('inventory', array('qty' => $qty, 'cost' => $cost, 'pid' => $pid ) );
			// Reset this flag, it's not used when inventory is enabled
			$this->db->where('id', $pid)->update('products', array('in_stock'=> '1'));
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	function product_cost($pid)
	{
		$cost = 0;
		$q = $this->db
				->select('SUM(cost) / COUNT(cost) AS avg', FALSE)
				->from('inventory')
				->where('pid', $pid)
				->get();
		
		if($q->num_rows()) 
		{
			$row = $q->row();
			$cost = $row->avg;
		}
		return $cost;
	}
	
	function sum_sold($pid) 
	{
		$q = $this->db
				->select('SUM(quantity) sum', FALSE)
				->from('order_items')
				->where('product_id', $pid)
				->get();
		
		if($q->num_rows()) 
		{
			$result = $q->result();
			$sold = $result[0]->sum;
			return $sold ? $sold : 0;
		}
		else
		{
			return FALSE;
		}
	}
	
	function sum_inventory($pid) 
	{
		$q = $this->db
				->select('SUM(qty) sum', FALSE)
				->from('inventory')
				->where('pid', $pid)
				->get();
	
		if( $q->num_rows() ) 
		{
			$result = $q->result();
			$sum = $result[0]->sum;
			return $sum ? $sum : 0;
		}
		else
		{
			return FALSE;
		}
	}
	
	function available_qty($pid) 
	{
		$inventory_level = $this->sum_inventory($pid);
		$sales_level = $this->sum_sold($pid);
	
		return ($inventory_level - $sales_level);
	}
}