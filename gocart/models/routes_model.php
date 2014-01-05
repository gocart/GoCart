<?php

class Routes_model extends CI_Model {

	function __construct()
	{
		parent::__construct();		
	}
	

	// save or update a route and return the id
	function save($route)
	{
		if(!empty($route['id']))
		{
			$this->db->where('id', $route['id']);
			$this->db->update('routes', $route);
			
			return $route['id'];
		}
		else
		{
			$this->db->insert('routes', $route);
			return $this->db->insert_id();
		}
	}
	
	function check_slug($slug, $id=false)
	{
		if($id)
		{
			$this->db->where('id !=', $id);
		}
		$this->db->where('slug', $slug);
		
		return (bool) $this->db->count_all_results('routes');
	}
	
	function validate_slug($slug, $id=false, $count=false)
	{
		if($this->check_slug($slug.$count, $id))
		{
			if(!$count)
			{
				$count	= 1;
			}
			else
			{
				$count++;
			}
			return $this->validate_slug($slug, $id, $count);
		}
		else
		{
			return $slug.$count;
		}
	}
	
	function delete($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('routes');
	}
}