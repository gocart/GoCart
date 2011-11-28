<?php

class Dashboard extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		remove_ssl();
		
		$this->load->library('Auth');
		$this->load->model('Order_model');
		$this->load->model('Customer_model');
		$this->load->helper('date');
				
		//this adds the redirect url to our flash data, incase they are not logged in
		$this->auth->is_logged_in(uri_string());
		
		if($this->auth->check_access('Orders'))
		{
			redirect($this->config->item('admin_folder').'/orders');
		}
	}
	
	function index()
	{
		$data['page_title']	=  lang('dashboard');
		
		// get 5 latest orders
		$data['orders']	= $this->Order_model->get_orders(false, '' , 'DESC', 5);

		// get 5 latest customers
		$data['customers'] = $this->Customer_model->get_customers(5);
				
		
		$this->load->view($this->config->item('admin_folder').'/dashboard', $data);
	}

}