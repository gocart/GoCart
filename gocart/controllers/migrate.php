<?php

class Migrate extends CI_Controller {

	function index()
	{
		$this->load->library('migration');
	
		if ( ! $this->migration->current())
		{
			$this->load->view('migrate_failure');
		}
		else
		{
			$this->load->view('migrate_success');
		}
	}
}