<?php
Class Page_model extends CI_Model
{

	/********************************************************************
	Page functions
	********************************************************************/
	function get_pages($parent = 0)
	{
		$this->db->order_by('sequence', 'ASC');
		$this->db->where('parent_id', $parent);
		$result = $this->db->get('pages')->result();
		
		$return	= array();
		foreach($result as $page)
		{

			// Set a class to active, so we can highlight our current page
			if($this->uri->segment(1) == $page->slug) {
				$page->active = true;
			} else {
				$page->active = false;
			}

			$return[$page->id]				= $page;
			$return[$page->id]->children	= $this->get_pages($page->id);
		}
		
		return $return;
	}

	function get_pages_tiered()
    {
		$this->db->order_by('sequence', 'ASC');
		$this->db->order_by('title', 'ASC');
		$pages = $this->db->get('pages')->result();
		
		$results	= array();
		foreach($pages as $page)
		{
			$results[$page->parent_id][$page->id] = $page;
		}
		
		return $results;
	}

	function get_page($id)
	{
		$this->db->where('id', $id);
		$result = $this->db->get('pages')->row();
		
		return $result;
	}
	
	function get_slug($id)
	{
		$page = $this->get_page($id);
		if($page) 
		{
			return $page->slug;
		}
	}
	
	function save($data)
	{
		if($data['id'])
		{
			$this->db->where('id', $data['id']);
			$this->db->update('pages', $data);
			return $data['id'];
		}
		else
		{
			$this->db->insert('pages', $data);
			return $this->db->insert_id();
		}
	}
	
	function delete_page($id)
	{
		//delete the page
		$this->db->where('id', $id);
		$this->db->delete('pages');
	
	}
	
	function get_page_by_slug($slug)
	{
		$this->db->where('slug', $slug);
		$result = $this->db->get('pages')->row();
		
		return $result;
	}
}