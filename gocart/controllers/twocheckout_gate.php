<?php

class twocheckout_gate extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->add_package_path(APPPATH.'packages/payment/twocheckout/');
		$this->load->library(array('go_cart','Twocheckout'));
		$this->load->helper('form_helper');
	}
	

	function index()
	{
		//we don't have a default landing page
		redirect('');
		
	}
		
	/* 
	   Receive postback confirmation from twocheckout
	   to complete the customer's order.
	*/
	function twocheckout_return()
	{
		$settings = $this->go_cart->CI->Settings_model->get_settings('twocheckout');

		if ($this->go_cart->CI->input->post('order_number'))
		{
			foreach ($this->go_cart->CI->input->post() as $k => $v)
			{
				$response[$k] = $v;
			}
		}
		elseif ($this->go_cart->CI->input->get('order_number'))
		{
			foreach ($this->go_cart->CI->input->get() as $k => $v)
			{
				$response[$k] = $v;
			}
		}

		//2Checkout breaks the hash on demo sales, we must do the same here so the hashes match.
		if (isset($response['demo']))
		{
			if ( $response['demo'] == 'Y' )
			{
				$response['order_number'] = 1;
			}
		}

		//Create hash
		$our_hash = strtoupper(md5($settings['secret'] . $settings['sid'] . $response['order_number'] . $response['total']));

		//Compare hashes to check the validity of the sale and print the response
		if ($our_hash == $response['key'])
		{            
			// The transaction is good. Finish order
			
			// set a confirmed flag in the gocart payment property
			$this->go_cart->set_payment_confirmed();
			
			// send them back to the cart payment page to finish the order
			// the confirm flag will bypass payment processing and save up
			redirect('checkout/place_order/');
		}
		else
		{
			// Possible fake request; was not verified by 2Checkout. Could be due to a double page-get, should never happen under normal circumstances
			$this->session->set_flashdata('message', "<div>2Checkout did not validate your order. Either it has been processed already, or something else went wrong. If you believe there has been a mistake, please contact us.</div>");
			redirect('checkout');
		}
	}
	
	/* 
		Customer cancelled 2Checkout payment
		
	*/
	function twocheckout_cancel()
	{
		//make sure they're logged in if the config file requires it
		if($this->config->item('require_login'))
		{
			$this->Customer_model->is_logged_in();
		}
	
		// User canceled using 2Checkout, send them back to the payment page
		$cart  = $this->session->userdata('cart');	
		$this->session->set_flashdata('message', "<div>2Checkout transaction canceled.</div>");
		redirect('checkout');
	}

}