<?php
Class Banner_model extends CI_Model
{
	
	function banner_collections()
	{
		return $this->db->order_by('name', 'ASC')->get('banner_collections')->result();
	}
	
	function banner_collection($banner_collection_id)
	{
		return $this->db->where('banner_collection_id', $banner_collection_id)->get('banner_collections')->row();
	}
	
	function banner_collection_banners($banner_collection_id, $only_active=false, $limit=5)
	{
		$this->db->where('banner_collection_id', $banner_collection_id);
		$banners	= $this->db->order_by('sequence', 'ASC')->get('banners')->result();
		
		if($only_active)
		{
			$return	= array();
			foreach ($banners as $banner)
			{
				if ($banner->enable_date == '0000-00-00')
				{
					$enable_test	= false;
					$enable			= '';
				}
				else
				{
					$eo			 	= explode('-', $banner->enable_date);
					$enable_test	= $eo[0].$eo[1].$eo[2];
					$enable			= $eo[1].'-'.$eo[2].'-'.$eo[0];
				}

				if ($banner->disable_date == '0000-00-00')
				{
					$disable_test	= false;
					$disable		= '';
				}
				else
				{
					$do			 	= explode('-', $banner->disable_date);
					$disable_test	= $do[0].$do[1].$do[2];
					$disable		= $do[1].'-'.$do[2].'-'.$do[0];
				}

				$curDate		= date('Ymd');

				if ( (!$enable_test || $curDate >= $enable_test) && (!$disable_test || $curDate < $disable_test))
				{
					$return[]	= $banner;
				}

				if(count($return) == $limit)
				{
					break;
				}
			}
			
			return $return;
		}
		else
		{
			return $banners;
		}
	}
	
	function banner($banner_id)
	{
		$this->db->where('banner_id', $banner_id);
		$result = $this->db->get('banners');
		$result = $result->row();
		
		if ($result)
		{
			if ($result->enable_date == '0000-00-00')
			{
				$result->enable_date = '';
			}
			
			if ($result->disable_date == '0000-00-00')
			{
				$result->disable_date = '';
			}
		
			return $result;
		}
		else
		{ 
			return array();
		}
	}
	
	function save_banner($data)
	{
		if(isset($data['banner_id']))
		{
			$this->db->where('banner_id', $data['banner_id']);
			$this->db->update('banners', $data);
		}
		else
		{
			$data['sequence'] = $this->get_next_sequence($data['banner_collection_id']);
			$this->db->insert('banners', $data);
		}
	}
	
	function save_banner_collection($data)
	{
		if(isset($data['banner_collection_id']) && (bool)$data['banner_collection_id'])
		{
			$this->db->where('banner_collection_id', $data['banner_collection_id']);
			$this->db->update('banner_collections', $data);
		}
		else
		{
			$this->db->insert('banner_collections', $data);
		}
	}
	
	function get_homepage_banners($limit = false)
	{
		$banners	= $this->db->order_by('sequence ASC')->get('banners')->result();
		$count	= 1;
		foreach ($banners as &$banner)
		{
			if ($banner->enable_date == '0000-00-00')
			{
				$enable_test	= false;
				$enable			= '';
			}
			else
			{
				$eo			 	= explode('-', $banner->enable_date);
				$enable_test	= $eo[0].$eo[1].$eo[2];
				$enable			= $eo[1].'-'.$eo[2].'-'.$eo[0];
			}

			if ($banner->disable_date == '0000-00-00')
			{
				$disable_test	= false;
				$disable		= '';
			}
			else
			{
				$do			 	= explode('-', $banner->disable_date);
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
	
	function delete_banner($banner_id)
	{
		$this->db->where('banner_id', $banner_id);
		$this->db->delete('banners');
	}
	
	function delete_banner_collection($banner_collection_id)
	{
		$this->db->where('banner_collection_id', $banner_collection_id);
		$this->db->delete('banners');
		
		$this->db->where('banner_collection_id', $banner_collection_id);
		$this->db->delete('banner_collections');
	}
	
	function get_next_sequence($banner_collection_id)
	{
		$this->db->where('banner_collection_id', $banner_collection_id);
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

	function organize($banners)
	{
		foreach ($banners as $sequence => $id)
		{
			$data = array('sequence' => $sequence);
			$this->db->where('banner_id', $id);
			$this->db->update('banners', $data);
		}
	}
}