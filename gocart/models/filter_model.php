<?php
Class filter_model extends CI_Model
{

	function get_filters($parent = false)
	{
		if ($parent !== false)
		{
			$this->db->where('parent_id', $parent);
		}
		$this->db->select('id');
		$this->db->order_by('filters.sequence', 'ASC');
		
		//this will alphabetize them if there is no sequence
		$this->db->order_by('name', 'ASC');
		$result	= $this->db->get('filters');
		
		$filters	= array();
		foreach($result->result() as $cat)
		{
			$filters[]	= $this->get_filter($cat->id);
		}
		
		return $filters;
	}
	
	function get_filters_by_names($filter_list)
	{
		if(count($filter_list)==0)
		{
			return array();
		}
		
		$querystr = '';
		foreach($filter_list as $f)
		{
			$querystr .= ' slug=\''.$f.'\' OR';
		}
		$querystr = substr($querystr, 0, -2);
		
		return $this->db->select('name, slug')->from('filters')->where($querystr, null, false)->get()->result();
	}
	
	// Retrieves the list of Ids for products that match a list of filters
	function get_filter_product_ids($filter_list=array(), $category_id=false)
	{
		$filter_ids = array();
		$product_ids = array();
		if(count($filter_list)>0) {
			foreach($filter_list as $f)
			{
				$filter_ids = array_merge($filter_ids, $this->get_filter_ids($f));
			}
			
			if(count($filter_ids)==0)
			{
				return array();
			}
		
			$this->db->select('filter_products.product_id, category_products.category_id')->from('filter_products');
			
			if($category_id!==false)
			{
				$this->db->join('category_products', 'filter_products.product_id=category_products.product_id');
			}
				
			if(count($filter_ids)>1)
			{
				$querystr = '';
				foreach($filter_ids as $filter_id)
				{
					$querystr .= ' filter_id="'.$filter_id.'" OR';
				}
		
				$querystr = substr($querystr, 0, -2);
			
				$this->db->where($querystr, null, false);
			} else {
				$this->db->where('filter_id', $filter_ids[0]);
			}
			
			if($category_id!==false)
			{
				$this->db->where('category_id', $category_id);
			}
			
			$productrecs = $this->db->get()->result();
			
			//die($this->db->last_query());
	
			foreach($productrecs as $p)
			{
				$product_ids[] = $p->product_id;
			}
		}
		
		return $product_ids;
		
	}
	
	// Get the id's of all filters based on the parent slug
	function get_filter_ids($parent_slug)
	{	
		$return = array();
		$parentrec = $this->db->select('id')->from('filters')->where('slug', $parent_slug)->get()->result();
		
		if(count($parentrec)==1)
		{
			$return[] = $parentrec[0]->id;
		} else {
			return $return;
		}
		
		$return = array_merge($return, $this->get_filter_tree($parentrec[0]->id));
		
		return $return;
	}
	
	// get children of a filter parent by ID
	function get_filter_tree($parent_id)
	{
		$return = array();
		$children = $this->db->select('id')->from('filters')->where('parent_id', $parent_id)->get()->result();
		foreach($children as $c)
		{
			$return[] = $c->id;
			// look for further children
			$return = array_merge($return, $this->get_filter_tree($c->id));
		}
		return $return;
	}
	
	
	function get_filters_tiered()
	{
		$this->db->order_by('sequence');
		$this->db->order_by('name', 'ASC');
		$filters = $this->db->get('filters')->result();
		
		$results	= array();
		foreach($filters as $filter) {

			// Set a class to active, so we can highlight our current category
			if($this->uri->segment(1) == $filter->slug) {
				$filter->active = true;
			} else {
				$filter->active = false;
			}

			$results[$filter->parent_id][$filter->id] = $filter;
		}
		
		return $results;
	}
	
	
	function filter_autocomplete($name, $limit)
	{
		return	$this->db->like('name', $name)->get('filters', $limit)->result();
	}
	
	function get_filter($id)
	{
		return $this->db->get_where('filters', array('id'=>$id))->row();
	}
	
	function get_filter_products_admin($id)
	{
		$this->db->order_by('sequence', 'ASC');
		$result	= $this->db->get_where('filter_products', array('filter_id'=>$id));
		$result	= $result->result();
		
		$contents	= array();
		foreach ($result as $product)
		{
			$result2	= $this->db->get_where('products', array('id'=>$product->product_id));
			$result2	= $result2->row();
			
			$contents[]	= $result2;	
		}
		
		return $contents;
	}
	
	function get_filter_products($id, $limit, $offset)
	{
		$this->db->order_by('sequence', 'ASC');
		$result	= $this->db->get_where('filter_products', array('filter_id'=>$id), $limit, $offset);
		$result	= $result->result();
		
		$contents	= array();
		$count		= 1;
		foreach ($result as $product)
		{
			$result2	= $this->db->get_where('products', array('id'=>$product->product_id));
			$result2	= $result2->row();
			
			$contents[$count]	= $result2;
			$count++;
		}
		
		return $contents;
	}
	
	function organize_contents($id, $products)
	{
		//first clear out the contents of the filter
		$this->db->where('filter_id', $id);
		$this->db->delete('filter_products');
		
		//now loop through the products we have and add them in
		$sequence = 0;
		foreach ($products as $product)
		{
			$this->db->insert('filter_products', array('filter_id'=>$id, 'product_id'=>$product, 'sequence'=>$sequence));
			$sequence++;
		}
	}
	
	function save($filter)
	{
		if ($filter['id'])
		{
			$this->db->where('id', $filter['id']);
			$this->db->update('filters', $filter);
			
			return $filter['id'];
		}
		else
		{
			$this->db->insert('filters', $filter);
			return $this->db->insert_id();
		}
	}
	
	function delete($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('filters');
		
		//delete references to this filter in the product to filter table
		$this->db->where('filter_id', $id);
		$this->db->delete('filter_products');
	}
}