<?php
Class Banner_model extends CI_Model
{
	function get_banners($limit = false)
	{
		if($limit)
		{
			$this->db->limit($limit);
		}
		return $this->db->order_by('sequence ASC')->get('banners')->result();
	}
	
	function get_homepage_banners($limit = false)
	{
		$banners	= $this->db->order_by('sequence ASC')->get('banners')->result();
		$count	= 1;
		foreach ($banners as &$banner)
		{
			if ($banner->enable_on == '0000-00-00')
			{
				$enable_test	= false;
				$enable			= '';
			}
			else
			{
				$eo			 	= explode('-', $banner->enable_on);
				$enable_test	= $eo[0].$eo[1].$eo[2];
				$enable			= $eo[1].'-'.$eo[2].'-'.$eo[0];
			}

			if ($banner->disable_on == '0000-00-00')
			{
				$disable_test	= false;
				$disable		= '';
			}
			else
			{
				$do			 	= explode('-', $banner->disable_on);
				$disable_test	= $do[0].$do[1].$do[2];
				$disable		= $do[1].'-'.$do[2].'-'.$do[0];
			}

			$curDate		= date('Ymd');

			if (($enable_test && $enable_test > $curDate) || ($disable_test && $disable_test <= $curDate))
			{
				unset($banner);
			}
			else
			{
				$count++;
			}
			
			if($limit)
			{
				if($count > $limit)
				{
					continue;
				}				
			}
		}
		return $banners;
	}
	
	function get_banner($id)
	{
		$this->db->where('id', $id);
		$result = $this->db->get('banners');
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
		
		$banner	= $this->get_banner($id);
		if ($banner)
		{
			$this->db->where('id', $id);
			$this->db->delete('banners');
			
			return 'The "'.$banner->title.'" banner has been removed.';
		}
		else
		{
			return 'The banner could not be found.';
		}
	}
	
	function get_next_sequence()
	{
		$this->db->select('sequence');
		$this->db->order_by('sequence DESC');
		$this->db->limit(1);
		$result = $this->db->get('banners');
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
	
	function save_banner($data)
	{
		if(isset($data['id']))
		{
			$this->db->where('id', $data['id']);
			$this->db->update('banners', $data);
		}
		else
		{
			$data['sequence'] = $this->get_next_sequence();
			$this->db->insert('banners', $data);
		}
	}
	
	function organize($banners)
	{
		foreach ($banners as $sequence => $id)
		{
			$data = array('sequence' => $sequence);
			$this->db->where('id', $id);
			$this->db->update('banners', $data);
		}
	}
}