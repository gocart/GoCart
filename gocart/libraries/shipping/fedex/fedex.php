<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class fedex
{
	var $CI;
	var $server;
	var $package_types;
	var $service_list;
	var $path_to_wsdl;
	
	function __construct()
	{
		//we're going to have this information in the back end for editing eventually
		//username password, origin zip code etc.
		$this->CI =& get_instance();
		$this->CI->load->model('Settings_model');
		
		//$this->server = 'https://gatewaybeta.fedex.com/GatewayDC';
		
		//The WSDL is not included with the sample code.
		//Please include and reference in $path_to_wsdl variable.
		$this->path_to_wsdl = APPPATH."libraries/shipping/fedex/lib/RateService_v8.wsdl";
		
		// Packaging types
		$this->package_types['FEDEX_10KG_BOX']	= '10KG Box';
		$this->package_types['FEDEX_25KG_BOX'] 	= '25KG Box'; 
		$this->package_types['FEDEX_BOX']		= 'Fedex Box';
		$this->package_types['FEDEX_ENVELOPE']	= 'Fedex Envelope'; 
		$this->package_types['FEDEX_PAK']		= 'Fedex Pak';
		$this->package_types['FEDEX_TUBE'] 		= 'Fedex Tube';
		$this->package_types['YOUR_PACKAGING'] 	= 'Your Packaging';
		
	
		// Available Services
		$this->service_list['EUROPE_FIRST_INTERNATIONAL_PRIORITY']	= 'Fedex Europe 1st Intn\'l Priority';
		$this->service_list['FEDEX_1_DAY_FREIGHT']	= 'Fedex 1 Day Freight'; 
		$this->service_list['FEDEX_2_DAY']			  = 'Fedex 2 Day';
		$this->service_list['FEDEX_2_DAY_FREIGHT']	= 'Fedex 2 Day Freight'; 
		$this->service_list['FEDEX_3_DAY_FREIGHT']	= 'Fedex 3 Day Freight'; 
		$this->service_list['FEDEX_EXPRESS_SAVER']	= 'Fedex Express Saver';
		$this->service_list['FEDEX_GROUND']		  = 'Fedex Ground'; 
		$this->service_list['FIRST_OVERNIGHT']		  = 'Fedex First Overnight';
		$this->service_list['GROUND_HOME_DELIVERY']	= 'Fedex Ground Home Delivery'; 
		$this->service_list['INTERNATIONAL_ECONOMY']	= 'Fedex International Economy'; 
		$this->service_list['INTERNATIONAL_ECONOMY_FREIGHT']	= 'Fedex International Economy Freight'; 
		$this->service_list['INTERNATIONAL_FIRST']	= 'Fedex International First'; 
		$this->service_list['INTERNATIONAL_PRIORITY']	= 'Fedex International Priority'; 
		$this->service_list['INTERNATIONAL_PRIORITY_FREIGHT']	= 'Fedex International Priority Freight'; 
		$this->service_list['PRIORITY_OVERNIGHT']	= 'Fedex Priority Overnight';
		$this->service_list['SMART_POST']	= 'Fedex Smart Post'; 
		$this->service_list['STANDARD_OVERNIGHT']	= 'Fedex Standard Overnight'; 
		$this->service_list['FEDEX_FREIGHT']	= 'Fedex Freight'; 
		$this->service_list['FEDEX_NATIONAL_FREIGHT']	= 'Fedex National Freight';
		$this->service_list['INTERNATIONAL_GROUND']	= 'Fedex International Ground';
		
	}
	
	function rates()
	{
		
		$this->CI->load->library('session');
		
		// get customer info
		$customer = $this->CI->go_cart->customer();
		
		$customer_address = $customer['ship_address'];
		
		
		// Weight of order
		$weight	= $this->CI->go_cart->order_weight();

		// retrieve settings
		$settings	= $this->CI->Settings_model->get_settings('fedex');
		
		//check if we're enabled
		if(!$settings['enabled'] || $settings['enabled'] < 1)
		{
			return array();
		}
		
		$key 				= $settings['key'];
		$password			= $settings['password'];
		$shipAccount 		= $settings['shipaccount'];
		$meter				= $settings['meter'];
		$service			= explode(',',$settings['service']);
		$package 			= $settings['package'];
		$handling_method	= $settings['handling_method'];
		$handling_amount	= $settings['handling_amount'];
		$pkg_width	 		= $settings['width'];
		$pkg_height			= $settings['height'];
		$pkg_length 		= $settings['length'];
		$billAccount 		= $shipAccount;
		
		
	
		//====== Fedex code start
	
		require_once('lib/fedex-common.php5');
		
		ini_set("soap.wsdl_cache_enabled", "0");
		
		$client = new SoapClient($this->path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information
		
		$request['WebAuthenticationDetail'] = array('UserCredential' =>
											 array('Key' => $key, 'Password' => $password)); 
		$request['ClientDetail'] = array('AccountNumber' => $shipAccount, 'MeterNumber' => $meter);
		$request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Rate Available Services Request v8 using PHP ***');
		$request['Version'] = array('ServiceId' => 'crs', 'Major' => '8', 'Intermediate' => '0', 'Minor' => '0');
		$request['ReturnTransitAndCommit'] = true;
		$request['RequestedShipment']['DropoffType'] = 'REGULAR_PICKUP'; // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
		$request['RequestedShipment']['ShipTimestamp'] = date('c');
		
		$request['RequestedShipment']['Shipper'] = array('Address' => array(
												 'StreetLines' => array($this->CI->config->item('address1'), $this->CI->config->item('address2')), // Origin details
												 'City' => $this->CI->config->item('city'),
												 'StateOrProvinceCode' => $this->CI->config->item('state'),
												 'PostalCode' => $this->CI->config->item('zip'),
												 'CountryCode' => $this->CI->config->item('country')));
		$request['RequestedShipment']['Recipient'] = array('Address' => array (
													  'StreetLines' => array($customer_address['address1'],$customer_address['address2']), // Destination details
													  'City' => $customer_address['city'],
													  'StateOrProvinceCode' => $customer_address['state'],
													  'PostalCode' => $customer_address['zip'],
													  'CountryCode' => $customer_address['country_code']));
		$request['RequestedShipment']['ShippingChargesPayment'] = array('PaymentType' => 'SENDER',
																'Payor' => array('AccountNumber' => $billAccount, 
																			'CountryCode' => $customer_address['country_code']));
		$request['RequestedShipment']['RateRequestTypes'] = 'ACCOUNT'; 
		$request['RequestedShipment']['RateRequestTypes'] = 'LIST'; 
		$request['RequestedShipment']['PackageCount'] = '1';
		$request['RequestedShipment']['PackageDetail'] = 'INDIVIDUAL_PACKAGES';
		$request['RequestedShipment']['PackagingType'] = $package;
		$request['RequestedShipment']['RequestedPackageLineItems'] = array('0' => array('Weight' => array('Value' => $weight,
																							'Units' => 'LB'),
																							'Dimensions' => array('Length' => $pkg_length,
																								'Width' => $pkg_width,
																								'Height' => $pkg_height,
																								'Units' => 'IN')));
		
		// send request
		$response = $client ->getRates($request);

			
		if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR' )
		{
			
			if(!isset($response->RateReplyDetails) || ! is_array($response->RateReplyDetails))
			{
				return array(); // No Results
			}
			
			foreach ($response->RateReplyDetails as $rateReply)
			{		  
				if(in_array($rateReply->ServiceType, $service))
				{
					
					$amount = $rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount;
					
					if(is_numeric($handling_amount)) // valid entry?
					{
						if($handling_method=='$')
						{
							$amount += $handling_amount;
						}
						elseif($handling_method=='%')
						{
							$amount += $amount * ($handling_amount/100);
						}
					}
					
					$rates[$this->service_list[$rateReply->ServiceType]] = number_format($amount,2,".",",");
				}
			}
			
			return $rates;
		}
		else
		{
			return array(); // fail
		}
	
	
	//========  Fedex Code End
  
   
	}
	
	function install()
	{
		$default_settings	= array(
			'enabled'=>0,
			'key'=>'',
			'password'=>'',
			'meter'=>'',
			'shipaccount'=>'',
			'handling_method'=>'$',
			'handling_amount'=>5,
			'length' => 10,
			'width' => 10,
			'height' => 3,
			'package'=>'YOUR_PACKAGING',
			'service' => implode(',', array_keys($this->service_list))
		);
		//set a default blank setting for flatrate shipping
		$this->CI->Settings_model->save_settings('fedex', $default_settings);
	}
	
	function uninstall()
	{
		$this->CI->Settings_model->delete_settings('fedex');
	}
	
	function form($post	= false)
	{
		$this->CI->load->helper('form');
		
		//this same function processes the form
		if(!$post)
		{
			$settings			= $this->CI->Settings_model->get_settings('fedex');
			$package			= $settings['package'];
			$service			= explode(',', $settings['service']);
			$key				= $settings['key'];
			$shipaccount		= $settings['shipaccount'];
			$meter				= $settings['meter'];
			$password			= $settings['password'];
			$handling_method	= $settings['handling_method'];
			$handling_amount	= $settings['handling_amount'];
			$height 			= $settings['height'];
			$width 				= $settings['width'];
			$length				= $settings['length'];
			$enabled			= $settings['enabled'];
		}
		else
		{
			$package			= $post['package'];
			$service 			= $post['service'];
			$key				= $post['key'];
			$password			= $post['password'];
			$shipaccount		= $post['shipaccount'];
			$meter				= $post['meter'];
			$handling_method	= $post['handling_method'];
			$handling_amount	= $post['handling_amount'];
			$height 			= $settings['height'];
			$width				= $settings['width'];
			$length				= $settings['length'];
			$enabled			= $post['enabled'];

		}
		
		$form	= '<table><tr><td>Key: </td><td>'.form_input('key', $key, 'class="gc_tf1"') .'</td></tr>
					<tr><td>Ship Account: </td><td>'.form_input('shipaccount', $shipaccount, 'class="gc_tf1"') .'</td></tr>
					<tr><td>Meter: </td><td>'.form_input('meter', $meter, 'class="gc_tf1"') .'</td></tr>
					<tr><td>Password: </td><td>'.form_input('password', $password, 'class="gc_tf1"') .'</td></tr>';
		
		$form	.= '<tr><td valign="top">Services To Offer: </td><td>';
		
		foreach($this->service_list as $id=>$opt)
		{
			$form .= "<input type='checkbox' name='service[]' value='$id' ";
			if(in_array($id, $service)) $form .= "checked='checked'";
			$form .= "> $opt <br />";
		}
	
		$form .= '</td></tr><tr><td>Container: </td><td>';
		
		$opts = $this->package_types;
		
		$form .= form_dropdown('package', $opts, $package);
		
		$form .= '</td></tr><tr><td colspan="2">Container Dimensions (inches): </td>';
		
		$form .= '</td></tr><tr><td>Height: </td><td>';
		
		$form .= ' '. form_input('height', $height, 'class="gc_tf1"');
		
		$form .= '</td></tr><tr><td>Width: </td><td>';
		
		$form .= ' '. form_input('width', $width, 'class="gc_tf1"');
		
		$form .= '</td></tr><tr><td>Length: </td><td>';
		
		$form .= ' '. form_input('length', $length, 'class="gc_tf1"');
		
		$form .= '</td></tr><tr><td>Handling Fee: </td><td>';
		
		$form .= form_dropdown('handling_method', array('$'=>'$', '%'=>'%'), $handling_method);
		
		$form .= ' '. form_input('handling_amount', $handling_amount, 'class="gc_tf1"');
		
		$form .= '</td></tr><tr><td>Module Status: </td><td>';
		
		$opts = array('Disabled', 'Enabled');
		
		$form .= form_dropdown('enabled', $opts, $enabled);
		
		$form .= '</td></tr></table>';
		
		return $form;
	}
	
	function check()
	{	
		$error	= false;
		
		
		//count the errors
		if($error)
		{
			return $error;
		}
		else
		{
		
			$save = $_POST;
			$save['service'] = implode(',', $save['service']);
			
			//we save the settings if it gets here
			$this->CI->Settings_model->save_settings('fedex', $save);
			
			return false;
		}
	}
}
