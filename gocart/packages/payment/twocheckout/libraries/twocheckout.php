<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Twocheckout
{
	var $CI;
	
	//this can be used in several places
	var	$method_name;
	
	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->library('session');
		$this->CI->lang->load('twocheckout');
		
		$this->method_name	= lang('twocheckout');
	}
	
	/*
	checkout_form()
	this function returns an array, the first part being the name of the payment type
	that will show up beside the radio button the next value will be the actual form if there is no form, then it should equal false
	there is also the posibility that this payment method is not approved for this purchase. in that case, it should return a blank array 
	*/
	
	//these are the front end form and check functions
	function checkout_form($post = false)
	{
		$settings	= $this->CI->Settings_model->get_settings('twocheckout');
		$enabled	= $settings['enabled'];
		
		$form			= array();
		if($enabled)
		{
			$form['name'] = $this->method_name;
			
			$form['form'] = $this->CI->load->view('twocheckout_checkout', array(), true);
			
			return $form;
			
		} else return array();
		
	}
	
	
	function checkout_check()
	{
		// Nothing to check in this module
		return false;
	}
	
	function description()
	{
		return lang('twocheckout');
	}
	
	//back end installation functions
	function install()
	{
		
		$config['sid'] = '';
		$config['secret'] = '';;
		$config['currency'] = 'USD'; // default
		
		$config['enabled'] = "0";
		
		//not normally user configurable
		$config['return_url'] = "twocheckout_gate/twocheckout_return/";
		$config['cancel_url'] = "twocheckout_gate/twocheckout_cancel/";

		$this->CI->Settings_model->save_settings('twocheckout', $config);
	}
	
	function uninstall()
	{
		$this->CI->Settings_model->delete_settings('twocheckout');
	}
	
	//payment processor
	function process_payment()
	{
		$customer = $this->CI->go_cart->customer();

		if ( $settings = $this->CI->Settings_model->get_settings('twocheckout') ) 
		{
			$args = array();
			$args['sid'] = $settings['sid'];
			$args['cart_order_id'] = $this->CI->config->item('company_name').' order';
			$args['purchase_step'] = 'payment-method';
			$args['currency_code'] = $settings['currency'];
			$args['total'] = $this->CI->go_cart->total();
			$args['return_url'] = site_url($settings['cancel_url']);
			$args['x_receipt_link_url'] = site_url($settings['return_url']);
			$args['card_holder_name'] = $customer['bill_address']["firstname"] . ' ' . $customer['bill_address']["lastname"];
			$args['street_address'] = $customer['bill_address']["address1"];
			$args['street_address2'] = $customer['bill_address']["address2"];
			$args['city'] = $customer['bill_address']["city"];

			// Pass state for US and Canada
			if( $customer['bill_address']["country_id"] == "223" || $customer['bill_address']["country_id"] == "38" )
			{
				$args['state'] = $customer['bill_address']["zone"];
			}
			else
			{
				$args['state'] = 'XX';
			}
			
			$args['zip'] = $customer['bill_address']["zip"];
			$args['country'] = $customer['bill_address']["country_code"];
			$args['phone'] = $customer['bill_address']["phone"];
			$args['email'] = $customer['bill_address']["email"]; 
			
			$url = 'https://www.2checkout.com/checkout/purchase?'.http_build_query($args, '', '&amp;');
			redirect($url);
		}
		else
		{
			return lang('twocheckout_error');
		}	
	}
	
	//admin end form and check functions
	function form($post	= false)
	{
		//this same function processes the form
		if(!$post)
		{
			$settings	= $this->CI->Settings_model->get_settings('twocheckout');
		}
		else
		{
			$settings = $post;
		}
		//retrieve form contents
		return $this->CI->load->view('twocheckout_form', array('settings'=>$settings), true);
	}
	
	function check()
	{	
		$error	= false;

		if($error)
		{
			return $error;
		}
		else
		{
			//we save the settings if it gets here
			$this->CI->Settings_model->save_settings('twocheckout', $_POST);
			
			return false;
		}
	}
}
