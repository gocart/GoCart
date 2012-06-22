<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* GoCart Sage Pay Class
*
* This class is part of the sage pay payment module.
* 
*
* @package       GoCart Sage Pay payment module
* @subpackage    
* @category      Packages/Payment
* @author        swicks@devicesoftware.com
* @version       0.2
* @todo          integrate backend to manage payments directly through GoCart, support form, server & 3D secure, 
*/


//Sage pay currently supported credit/debit cards
define('SAGE_PAY_CARD_TYPES', 'MC,MasterCard,VISA,VISA Credit,DELTA,VISA Debit,UKE,VISA Electron,MAESTRO,Maestro (Switch),AMEX,American Express,DC,Diner\'s Club,JCB,JCB Card,LASER,Laser');

//list of currencies this can easily be modified by adding/removing items    
define('SAGE_PAY_CURRENCY', 'USD,US Dollar (USD),EURO,Euro,GBP,GB Pound (GBP)');

class Sage_pay
{


	//codeigniter instance
	private $CI;

	// store sagepay response data
	private $_sagepay_response = array();

	//title of the payment method
	private $method_name;


	/**
	* constructor
	* 
*/
	function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->library('sage_pay_lib');
		$this->CI->lang->load('sage_pay');

		$this->method_name = lang('package_name');
	}

	/**
	* customer front end checkout form
	* 
	* @param array $post
	* @return string
*/
	public function checkout_form($post = false){
		//load form helper
		$this->CI->load->helper('form');

		$settings    = $this->CI->Settings_model->get_settings('sage_pay');
		$enabled    = $settings['enabled'];

		//explode selected card types
		if(isset($settings['card_types'])){
			$selected_cards = explode(',', $settings['card_types']);
			$settings['card_types'] = array();
			//store values in keys
			foreach($selected_cards as $selected_card){
				$settings['card_types'][$selected_card] = 1;
			}                
		}

		$form = array();

		// Retrieve any previously stored card data to redisplay in case of errors
		$sp_data = $this->CI->session->userdata('sp_data');


		if($enabled)
		{
			$form['name'] = $this->method_name;

			$form['form'] = $this->CI->load->view('customer_card', array('settings'=>$settings, 'sp_data'=>$sp_data), true);

			return $form;

			} else return array();

			return $form;
		}

		public function checkout_check(){

			//load credit card helper
			$this->CI->load->helper('credit_card_helper');

			$error_msg = lang('fix_errors')."<br/><ul>";
			$error_list = "";

			//Verify name field
			if( empty($_POST["CardHolder"])) 
				$error_list .= "<li>".lang('enter_name')."</li>";

			//Verify date
			if( !card_expiry_valid($_POST["ExpiryDate_mm"], $_POST["ExpiryDate_yy"]) )
				$error_list .= "<li>".lang('fix_exp_date')."</li>";

			//Verify card number
			if( empty($_POST["CardNumber"]) || !card_number_valid($_POST["CardNumber"]) )
				$error_list .= "<li>".lang('fix_card_num')."</li>";

			//Verify security code
			if( empty($_POST["CV2"]))
			{
				$error_list .= "<li>".lang('enter_cvv')."</li>";
			}

			// We need to store the credit card information temporarily
			$sp_tmp_data["sp_data"] = $_POST;
			$this->CI->session->set_userdata($sp_tmp_data);

			if( $error_list ) 
				return $error_msg . $error_list . "</ul>";
			else 
			{
				return false;
			}
		}

	/**
	* payment module description
	* 
*/
	public function description(){
		//create a description from the session which we can store in the database
		//this will be added to the database upon order confirmation

/*
	access the payment information with the  $_POST variable since this is called
	from the same place as the checkout_check above.
*/

	return 'Sage Pay';

	}

	/**
	* back end installation functions
	* 
*/
	public function install(){
		//default settings
		$config['service'] = 'DIRECT'; //FORM, SERVER & DIRECT - direct only available in this version

		$config['mode'] = 'Simulator'; //Simulator, Test & Live

		$config['direct_simulator_url'] = 'https://test.sagepay.com/Simulator/VSPDirectGateway.asp';
		$config['direct_test_url'] = 'https://test.sagepay.com/gateway/service/vspdirect-register.vsp';
		$config['direct_live_url'] = 'https://live.sagepay.com/gateway/service/vspdirect-register.vsp';

		$config['vps_protocol'] = 2.23;

		$config['tx_type'] = 'PAYMENT';  //Can be PAYMENT, DEFERRED or AUTHENTICATE

		$config['vendor'] = ''; //Sage pay vendor name

		$config['account_type'] = 'E';  //E for e-commerce

		$config['currency'] = 'USD';  //default USD

		$config['enabled'] = '0';

		$this->CI->Settings_model->save_settings('sage_pay', $config);

		//create sql table(s)
		$queries = $this->create_table($this->CI->db->dbprefix);
		foreach($queries as $query){
			$this->CI->db->query($query);
		}

	}

	/**
	* table for storing transaction data
	* 
	* @param string $prefix
	* @return array
*/
	private function create_table($prefix){
		$query = array();

		// currently disabled
		//$query[] = "DROP TABLE IF EXISTS `".$prefix."sage_pay`;";

		// currently set not to overwrite if install has already been run
		$query[] = "CREATE TABLE IF NOT EXISTS `".$prefix."sage_pay` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`order_id` int(11) DEFAULT NULL,
			`VPSProtocol` varchar(4) COLLATE utf8_general_ci DEFAULT NULL,
			`Status` varchar(15) COLLATE utf8_general_ci DEFAULT NULL,
			`StatusDetail` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
			`VendorTxCode` varchar(64) COLLATE utf8_general_ci DEFAULT NULL,
			`VPSTxId` varchar(38) COLLATE utf8_general_ci DEFAULT NULL,
			`SecurityKey` varchar(10) COLLATE utf8_general_ci DEFAULT NULL,
			`TxAuthNo` int(11) DEFAULT NULL,
			`AVSCV2` varchar(50) COLLATE utf8_general_ci DEFAULT NULL,
			`AddressResult` varchar(20) COLLATE utf8_general_ci DEFAULT NULL,
			`PostCodeResult` varchar(20) COLLATE utf8_general_ci DEFAULT NULL,
			`CV2Result` varchar(20) COLLATE utf8_general_ci DEFAULT NULL,
			`3DSecureStatus` varchar(20) COLLATE utf8_general_ci DEFAULT NULL,
			`CAVV` varchar(32) COLLATE utf8_general_ci DEFAULT NULL,
			`MD` varchar(35) COLLATE utf8_general_ci DEFAULT NULL,
			`ACSURL` text COLLATE utf8_general_ci,
			`PAReq` text COLLATE utf8_general_ci,
			`PayPalRedirectURL` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
			`Created` datetime default NULL,
			PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;";

		return $query;
	}
	/**
	* remove payment module settings
	* 
	*/
	public function uninstall(){
		$this->CI->Settings_model->delete_settings('sage_pay');
		$queries = $this->drop_table($this->CI->db->dbprefix);
		foreach($queries as $query){
			$this->CI->db->query($query);
		}        
	}
	/**
	* drop any associated tables
	* 
	* @param string $prefix
	* @return array
	*/
	private function drop_table($prefix){
		$query = array();       

		$query[] = "DROP TABLE IF EXISTS `".$prefix."sage_pay`;";

		return $query;
	}


	/**
	* payment processor
	* 
	*/
	public function process_payment(){

		// Get previously entered customer info
		$sp_data = $this->CI->session->userdata('sp_data');
		$customer = $this->CI->go_cart->customer();

		// Set our sagepay fields       

		$this->CI->sage_pay_lib->add_field('Amount', $this->CI->go_cart->total());        

		$this->CI->sage_pay_lib->add_field('CardHolder', $sp_data["CardHolder"]);
		$this->CI->sage_pay_lib->add_field('CardNumber', $sp_data["CardNumber"]);
		$this->CI->sage_pay_lib->add_field('StartDate', $sp_data["StartDate_mm"] . $sp_data["StartDate_yy"]);
		$this->CI->sage_pay_lib->add_field('ExpiryDate', $sp_data["ExpiryDate_mm"] . $sp_data["ExpiryDate_yy"]);
		$this->CI->sage_pay_lib->add_field('CV2', $sp_data["CV2"]);
		$this->CI->sage_pay_lib->add_field('CardType', $sp_data["CardType"]);

		$this->CI->sage_pay_lib->add_field('BillingSurname', $customer['bill_address']["lastname"]);
		$this->CI->sage_pay_lib->add_field('BillingFirstnames', $customer['bill_address']["firstname"]);        
		$this->CI->sage_pay_lib->add_field('BillingAddress1', $customer['bill_address']["address1"]);
		$this->CI->sage_pay_lib->add_field('BillingAddress2',$customer['bill_address']["address2"]);
		$this->CI->sage_pay_lib->add_field('BillingCity', $customer['bill_address']["city"]);
		// State is only supported in US, for all other countries leave blank
		if($customer['bill_address']["country_id"] == "223")
		{
			$this->CI->sage_pay_lib->add_field('BillingState', $customer['bill_address']["zone"]);
		}
		else
		{
			$this->CI->sage_pay_lib->add_field('BillingState', "");
		}
		
		$this->CI->sage_pay_lib->add_field('BillingPostCode', $customer['bill_address']["zip"]);
		$this->CI->sage_pay_lib->add_field('BillingCountry', $customer['bill_address']["country_code"]);
		$this->CI->sage_pay_lib->add_field('BillingPhone', $customer['bill_address']["phone"]);        

		$this->CI->sage_pay_lib->add_field('DeliverySurname', $customer['ship_address']["lastname"]);
		$this->CI->sage_pay_lib->add_field('DeliveryFirstnames', $customer['ship_address']["firstname"]);        
		$this->CI->sage_pay_lib->add_field('DeliveryAddress1', $customer['ship_address']["address1"]);
		$this->CI->sage_pay_lib->add_field('DeliveryAddress2',$customer['ship_address']["address2"]);
		$this->CI->sage_pay_lib->add_field('DeliveryCity', $customer['ship_address']["city"]);
		// State is only supported in US, for all other countries leave blank
		if($customer['ship_address']["country_id"] == "223")
		{
			$this->CI->sage_pay_lib->add_field('DeliveryState', $customer['ship_address']["zone"]);
		}
		else
		{
			$this->CI->sage_pay_lib->add_field('DeliveryState', "");
		}

		$this->CI->sage_pay_lib->add_field('DeliveryPostCode', $customer['ship_address']["zip"]);
		$this->CI->sage_pay_lib->add_field('DeliveryCountry', $customer['ship_address']["country_code"]);
		$this->CI->sage_pay_lib->add_field('DeliveryPhone', $customer['ship_address']["phone"]);        


		// Send info to sagepay and receive a response
		$this->CI->sage_pay_lib->process_payment();
		$this->_sagepay_response = $this->CI->sage_pay_lib->get_all_responses();

		// handle response status
		switch($this->_sagepay_response['Status']){
			case 'OK':
			case 'REGISTERED':
			$this->CI->session->unset_userdata('sp_data');
			return false;   // false == no error
			break;

			case 'MALFORMED':
			case 'INVALID':
			case 'ERROR':
			case 'NOTAUTHED':
			case 'REJECTED':
			case '3DAUTH':
			log_message('debug', 'Sage-pay module - Protocol:'. $this->_sagepay_response['VPSProtocol'] .' - Status:' . $this->_sagepay_response['Status']);
			log_message('debug', 'Sage-pay module - Status Detail:'. $this->_sagepay_response['StatusDetail']);
			return lang('transaction_declined');
			break;
		}
	}
	/**
	* final method to run after the order has been saved.
	* allows you to save order_id etc. back to the payment module
	*
	* @param array $data
	*/
	public function complete_payment($data){

		// add order id for admin to process payments
		$this->_sagepay_response['order_id'] = $data['order_id'];

		// record results in say_pay table for future processing
		$this->CI->db->insert($this->CI->db->dbprefix . 'sage_pay', $this->_sagepay_response);
	}

	/**
	* Admin form settings
	* 
	* @param array $post
	* @return string
	*/
	public function form($post	= false){

		//load form helper
		$this->CI->load->helper('form');

		//this same function processes the form
		if(!$post)
		{
			$settings	= $this->CI->Settings_model->get_settings('sage_pay');

			//explode selected card types
			if(isset($settings['card_types'])){
				$selected_cards = explode(',', $settings['card_types']);
				$settings['card_types'] = array();
				//store values in keys
				foreach($selected_cards as $selected_card){
					$settings['card_types'][$selected_card] = 1;
				}                
			}



		}
		else
		{
			$settings = $post;
		}

		return $this->CI->load->view('sage_pay_form', array('settings'=>$settings), true);
	}

	/**
	* Admin form validation
	* 
	*/
	public function check(){	
		$error	= false;

		// TODO 4 -o swicks -c Category: fix check options

		if(empty($_POST['vendor']))
			$error = "<DIV>".lang('enter_vendor')."</DIV>";

		if($error)
		{
			return $error;
		}
		else
		{
			//we save the settings if it gets here

			//place selected card types in a string
			$_POST['card_types'] = isset($_POST['card_types'])? implode(',', $_POST['card_types']) : "";

			$this->CI->Settings_model->save_settings('sage_pay', $_POST);

			return false;
		}
	}
}
