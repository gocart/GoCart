<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class moneris
{
	var $CI;
	
	//this can be used in several places
	var	$method_name;
	
	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->lang->load('moneris');
		
		$this->method_name	= lang('moneris_solutions');
	}
	
	
	//these are the front end form and check functions
	function checkout_form($post = false)
	{
		$settings	= $this->CI->Settings_model->get_settings('moneris');
		$enabled	= $settings['enabled'];

		$cc_data = $this->CI->session->userdata('cc_data');
		
		$form			= array();
		if($enabled == 1)
		{
			$form['name']	= $this->method_name;
			$form['form']	= $this->CI->load->view('customer_card', array('cc_data'=>$cc_data), true);
		}
		
		return $form;
	}
	function checkout_check()
	{
		$cc_tmp_data["cc_data"] = $_POST;
        $this->CI->session->set_userdata($cc_tmp_data);
		
		//if all is well, return false, otherwise, return an error message
		return false;
	}
	
	function description()
	{
		$cc_data = $this->CI->session->userdata('cc_data');

		return 'Moneris Solutions Credit Card Payment<br/>
		Name on Card: '. $cc_data['first_name'].' '.$cc_data['last_name'] .'<br/>
		Card Number: XXXX-XXXX-XXXX-'.  substr($cc_data['card_num'], -4) .'<br/>
		Expires: '. $cc_data['exp_date_mm'] .'/'. $cc_data['exp_date_yy'];
	}
	
	//back end installation functions
	function install()
	{
		$settings['enabled'] = '0';
		$settings['mode'] = 'test';
		$settings['site_id'] = '';
		$settings['api_key'] = '';
		$settings['descriptor'] = '';

		//set a default blank setting for flatrate shipping
		$this->CI->Settings_model->save_settings('moneris', $settings);
	}
	
	function uninstall()
	{
		$this->CI->Settings_model->delete_settings('moneris');
	}
	
	//payment processor
	function process_payment()
	{
			$settings = $this->CI->Settings_model->get_settings('moneris');
			$customer = $this->CI->go_cart->customer();

			// Load the global classes
			require APPPATH."packages/payment/moneris/libraries/mpgClasses.php";

			/**************************** Request Variables *******************************/

			if($settings['mode']=='test')
			{
				$store_id	='store5';
				$api_token	='yesguy';
			} else {
				$store_id 	= $settings['site_id'];
				$api_token	= $settings['api_key'];
			}

			$cc_data = $this->CI->session->userdata('cc_data');

			/*********************** Transactional Associative Array **********************/

			$txnArray = array('type'			=> 'purchase',
			     		    'order_id'			=> 'trans-'.date("dmy-G:i:s"),
			     		    'cust_id'			=>  @$customer['id'],
			    		    'amount'			=>	(string)number_format((float)$this->CI->go_cart->total(),2),
			   			    'pan'				=>	$cc_data['card_num'],
			   			    'expdate'			=>	$cc_data['exp_date_yy'].$cc_data['exp_date_mm'],
			   			    'cvd_value' 		=>  $cc_data['card_code'],
			   			    'cvd_indicator' 	=> 	1,
			   			    'crypt_type' 		=>	'7', // Code for SSL Enabled Website
			   			    'dynamic_descriptor'=> 	$settings['descriptor']
			   		       );

			/**************************** Transaction Object *****************************/

			$mpgTxn = new mpgTransaction($txnArray);

			/****************************** Request Object *******************************/

			$mpgRequest = new mpgRequest($mpgTxn);

			/***************************** HTTPS Post Object *****************************/

			/* Status Check Example
			$mpgHttpPost  =new mpgHttpsPostStatus($store_id,$api_token,$status_check,$mpgRequest);
			*/

			$mpgHttpPost  =new mpgHttpsPost($store_id,$api_token,$mpgRequest);

			/******************************* Response ************************************/

			$mpgResponse=$mpgHttpPost->getMpgResponse();

		
			/* for debugging 
			print("\nCardType = " . $mpgResponse->getCardType());
			print("\nTransAmount = " . $mpgResponse->getTransAmount());
			print("\nTxnNumber = " . $mpgResponse->getTxnNumber());
			print("\nReceiptId = " . $mpgResponse->getReceiptId());
			print("\nTransType = " . $mpgResponse->getTransType());
			print("\nReferenceNum = " . $mpgResponse->getReferenceNum());
			print("\nResponseCode = " . $mpgResponse->getResponseCode());
			print("\nISO = " . $mpgResponse->getISO());
			print("\nMessage = " . $mpgResponse->getMessage());
			print("\nIsVisaDebit = " . $mpgResponse->getIsVisaDebit());
			print("\nAuthCode = " . $mpgResponse->getAuthCode());
			print("\nComplete = " . $mpgResponse->getComplete());
			print("\nTransDate = " . $mpgResponse->getTransDate());
			print("\nTransTime = " . $mpgResponse->getTransTime());
			print("\nTicket = " . $mpgResponse->getTicket());
			print("\nTimedOut = " . $mpgResponse->getTimedOut());
			print("\nStatusCode = " . $mpgResponse->getStatusCode());
			print("\nStatusMessage = " . $mpgResponse->getStatusMessage());
			*/

			if($mpgResponse->getResponseCode()=='null')
			{
				// Incomplete Transaction
				return lang('processing_error');
			}

			$responseCode = (int)$mpgResponse->getResponseCode();
			if($responseCode >= 0 && $responseCode < 50)
			{
				// Transaction is good
				$this->CI->session->unset_userdata('cc_data');
				return false; // no errors
			} else {
				// Transaction Declined
				return lang('transaction_declined');
			}

	}
	
	//admin end form and check functions
	function form($post	= false)
	{
		//this same function processes the form
		if(!$post)
		{
			$settings	= $this->CI->Settings_model->get_settings('moneris');
			$data['settings']['enabled']	= $settings['enabled'];
			$data['settings']['mode']		= $settings['mode'];
			$data['settings']['site_id']	= $settings['site_id'];
			$data['settings']['api_key']	= $settings['api_key'];
			$data['settings']['descriptor']	= $settings['descriptor'];
		}
		else
		{
			$data['settings']['enabled']	= $post['enabled'];
			$data['settings']['mode']		= $post['mode'];
			$data['settings']['site_id']	= $post['site_id'];
			$data['settings']['api_key']	= $post['api_key'];
			$data['settings']['descriptor']	= $post['descriptor'];
		}
		
		return $this->CI->load->view('admin_form', $data, true);
	}
	
	function check()
	{	
		
		$settings['enabled'] 	= $this->CI->input->post('enabled');
		$settings['mode'] 		= $this->CI->input->post('mode');
		$settings['site_id'] 	= $this->CI->input->post('site_id');
		$settings['api_key'] 	= $this->CI->input->post('api_key');
		$settings['descriptor'] = $this->CI->input->post('descriptor');

		//we save the settings if it gets here
		$this->CI->Settings_model->save_settings('moneris', $settings);
		
		return false;
	}
}
