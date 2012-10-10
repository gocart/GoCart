<?php

class Reports extends Admin_Controller {

	//this is used when editing or adding a customer
	var $customer_id	= false;	

	function __construct()
	{		
		parent::__construct();
		remove_ssl();

		$this->auth->check_access('Admin', true);
		
		$this->load->model('Order_model');
		$this->load->model('Search_model');
		$this->load->helper(array('formatting'));
		
		$this->lang->load('report');
	}
	
	function index()
	{
		$data['page_title']	= lang('reports');
		$data['years']		= $this->Order_model->get_sales_years();
		$this->template->title($data['page_title'], config_item('company_name'));
		$this->template->build('reports', $data);
	}
	
	function best_sellers()
	{
		$start	= $this->input->post('start');
		$end	= $this->input->post('end');
		$data['best_sellers']	= $this->Order_model->get_best_sellers($start, $end);
		
		$this->template->set_layout('ajax');
		$this->template->build('reports/best_sellers', $data);
	}
	
	function sales()
	{
		$year			= $this->input->post('year');
		$data['orders']	= $this->Order_model->get_gross_monthly_sales($year);
		$this->template->set_layout('ajax');
		$this->template->build('reports/sales', $data);
	}

}