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
		$this->load->helper(array('formatting', 'utility'));
		
		$this->lang->load('report');
	}
	
	function index()
	{
		
		$orders		= $this->Order_model->get_orders();
		foreach($orders as &$o)
		{
			$data['orders'][] = $this->Order_model->get_items($o->id);
		}
		$data['page_title']	= lang('reports');
		$this->load->view($this->config->item('admin_folder').'/reports', $data);
	}
	
	function best_sellers()
	{
		$start	= $this->input->post('start');
		$end	= $this->input->post('end');
		$data['best_sellers']	= $this->Order_model->get_best_sellers($start, $end);
		
		$this->load->view($this->config->item('admin_folder').'/reports/best_sellers', $data);	
	}
	
	function sales()
	{
		$bulk_orders	= $this->Order_model->get_orders();
		
		$orders			= array();
		
		foreach($bulk_orders as $o)
		{
			// omit orders with a blank date
			if($o->ordered_on=='0000-00-00 00:00:00')
			{
				continue;
			}
			
			
			$date	= explode('-', $o->ordered_on);
			$y		= $date[0];
			$m		= $date[1];
			if(!isset($orders[$y]))
			{
				$orders[$y]	= array();
			}
			
			if(!isset($orders[$y][$m]))
			{
				$orders[$y][$m]	= array();
			}
			
			//coupon discounts
			if(!isset($orders[$y][$m]['coupon_discounts']))
			{
				$orders[$y][$m]['coupon_discounts'] = 0;
			}
			$orders[$y][$m]['coupon_discounts'] += $o->coupon_discount;
			
			//gift card discounts
			if(!isset($orders[$y][$m]['gift_card_discounts']))
			{
				$orders[$y][$m]['gift_card_discounts'] = 0;
			}
			$orders[$y][$m]['gift_card_discounts'] += $o->gift_card_discount;
			
			//total of product sales
			if(!isset($orders[$y][$m]['product_totals']))
			{
				$orders[$y][$m]['product_totals'] = 0;
			}
			$orders[$y][$m]['product_totals'] += $o->subtotal;
			
			//total of Shipping
			if(!isset($orders[$y][$m]['shipping']))
			{
				$orders[$y][$m]['shipping'] = 0;
			}
			$orders[$y][$m]['shipping'] += $o->shipping;
			
			//total taxes
			if(!isset($orders[$y][$m]['tax']))
			{
				$orders[$y][$m]['tax'] = 0;
			}
			$orders[$y][$m]['tax'] += $o->tax;
			
			//Grand Total less discounts
			if(!isset($orders[$y][$m]['total']))
			{
				$orders[$y][$m]['total'] = 0;
			}
			$orders[$y][$m]['total'] += $o->total;	
		}
		
		krsort($orders);
		foreach($orders as &$order)
		{
			krsort($order);
		}
		
		$data['orders'] = $orders;
		$this->load->view($this->config->item('admin_folder').'/reports/sales', $data);	
	}

}