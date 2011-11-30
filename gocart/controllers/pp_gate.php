<?php

class pp_gate extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->add_package_path(APPPATH.'packages/payment/paypal_express/');
		$this->load->library(array('paypal', 'httprequest', 'go_cart'));
		$this->load->helper('form_helper');
	}
	

	function index()
	{
		//we don't have a default landing page
		redirect('');
		
	}
		
	/* 
	   Receive postback confirmation from paypal
	   to complete the customer's order.
	*/
	function pp_return()
	{
				
		// Verify the transaction with paypal
		$final = $this->paypal->doPayment();

		// Process the results
		if ($final['ACK'] == 'Success') {
			// The transaction is good. Finish order
			
			// set a confirmed flag in the gocart payment property
			$this->go_cart->set_payment_confirmed();
			
			// send them back to the cart payment page to finish the order
			// the confirm flag will bypass payment processing and save up
			redirect('checkout/place_order/');			
			
		} else {
			// Possible fake request; was not verified by paypal. Could be due to a double page-get, should never happen under normal circumstances
			$this->session->set_flashdata('message', "<div>Paypal did not validate your order. Either it has been processed already, or something else went wrong. If you believe there has been a mistake, please contact us.</div>");
			redirect('checkout');
		}
	}
	
	/* 
		Customer cancelled paypal payment
		
	*/
	function pp_cancel()
	{
		//make sure they're logged in if the config file requires it
		if($this->config->item('require_login'))
		{
			$this->Customer_model->is_logged_in();
		}
	
		// User canceled using paypal, send them back to the payment page
		$cart  = $this->session->userdata('cart');	
		$this->session->set_flashdata('message', "<div>Paypal transaction canceled, select another payment method</div>");
		redirect('checkout');
	}

}