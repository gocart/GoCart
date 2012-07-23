<?php

class Admin_Controller extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->library('auth');
		$this->auth->is_logged_in(uri_string());
		
		//load the base language file
		$this->lang->load('admin_common');

		//@TODO: Find Admin Permission and Add that to the mix.
		if (ENVIRONMENT === 'development')
		{
			$this->load->helper('chromephp');
		}


	}//end __construct()

}

