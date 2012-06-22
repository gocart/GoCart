<?php

class activity extends CI_Controller {	

	function __construct()
	{		
		parent::__construct();
		$this->load->model('activity_model');
	}

	function feed()
	{
		$feeds = $this->activity_model->get_activity();
		
		if(count($feeds) > 0)
		{
			echo json_encode($feeds);
		}
		else
		{
			echo json_encode(array());		
		}
	}	
	
	function notify($lastread)
	{
		$notify = $this->activity_model->get_notification($lastread);
		
		if(count($notify) > 0)
		{
			echo json_encode($notify);
		}
		else
		{
			echo json_encode(array());		
		}
	}
}