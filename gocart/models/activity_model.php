<?php
Class activity_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function save_activity($type,$activity)
	{
		$save['type'] = $type;
		$save['activity'] = $activity;
		$this->db->insert('activityfeed',$save);
	}

	function get_notification($lastread=0)	
	{
		$this->db->order_by('timestamp',"desc");
		$this->db->limit(10);
		$this->db->where('id >',$lastread);
		return $this->db->get('activityfeed')->result_array();
	}

	function get_activity()	
	{
		$this->db->order_by('timestamp',"desc");
		$this->db->limit(10);
		return $this->db->get('activityfeed')->result_array();
	}

}