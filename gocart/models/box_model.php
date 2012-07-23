<?php
Class Box_model extends CI_Model
{
	function get_boxes($limit = false)
	{
		if($limit)
		{
			$this->db->limit($limit);
		}
		return $this->db->order_by('sequence ASC')->get('boxes')->result();
	}
	
	function get_homepage_boxes($limit = false)
	{
		$boxes	= $this->db->order_by('sequence ASC')->get('boxes')->result();
		
		$return	= array();
		foreach ($boxes as $box)
		{
			if ($box->enable_on == '0000-00-00')
			{
				$enable_test	= false;
				$enable			= '';
			}
			else
			{
				$eo			 	= explode('-', $box->enable_on);
				$enable_test	= $eo[0].$eo[1].$eo[2];
				$enable			= $eo[1].'-'.$eo[2].'-'.$eo[0];
			}

			if ($box->disable_on == '0000-00-00')
			{
				$disable_test	= false;
				$disable		= '';
			}
			else
			{
				$do			 	= explode('-', $box->disable_on);
				$disable_test	= $do[0].$do[1].$do[2];
				$disable		= $do[1].'-'.$do[2].'-'.$do[0];
			}

			$curDate		= date('Ymd');

			if (($enable_test && $enable_test > $curDate) || ($disable_test && $disable_test <= $curDate))
			{
				//fails to make it. rewrite this if statement one day to work opposite of how it does.
			}
			else
			{
				$return[]	= $box;
			}
			
			if($limit && $limit <= count($return))
			{
				break;
			}
		}
		return $return;
	}
	
	function get_box($id)
	{
		$this->db->where('id', $id);
		$result = $this->db->get('boxes');
		$result = $result->row();
		
		if ($result)
		{
			if ($result->enable_on == '0000-00-00')
			{
				$result->enable_on = '';
			}
			
			if ($result->disable_on == '0000-00-00')
			{
				$result->disable_on = '';
			}
		
			return $result;
		}
		else
		{ 
			return array();
		}
	}
	
	function delete($id)
	{
		
		$box	= $this->get_box($id);
		if ($box)
		{
			$this->db->where('id', $id);
			$this->db->delete('boxes');
			
			return 'The "'.$box->title.'" box has been removed.';
		}
		else
		{
			return 'The box could not be found.';
		}
	}
	
	function get_next_sequence()
	{
		$this->db->select('sequence');
		$this->db->order_by('sequence DESC');
		$this->db->limit(1);
		$result = $this->db->get('boxes');
		$result = $result->row();
		if ($result)
		{
			return $result->sequence + 1;
		}
		else
		{
			return 0;
		}
	}
	
	function save($data)
	{
		if(isset($data['id']))
		{
			$this->db->where('id', $data['id']);
			$this->db->update('boxes', $data);
		}
		else
		{
			$data['sequence'] = $this->get_next_sequence();
			$this->db->insert('boxes', $data);
		}
	}
	
	function organize($boxes)
	{
		foreach ($boxes as $sequence => $id)
		{
			$data = array('sequence' => $sequence);
			$this->db->where('id', $id);
			$this->db->update('boxes', $data);
		}
	}
}