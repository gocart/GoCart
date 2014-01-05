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
		$this->CI->lang->load('fedex');


		$this->path_to_wsdl = APPPATH."packages/shipping/fedex/libraries/RateService_v14.wsdl";

		// Drop Off Types
		$this->dropoff_types['BUSINESS_SERVICE_CENTER'] = lang('BUSINESS_SERVICE_CENTER');
		$this->dropoff_types['DROP_BOX'] = lang('DROP_BOX');
		$this->dropoff_types['REGULAR_PICKUP'] = lang('REGULAR_PICKUP');
		$this->dropoff_types['REQUEST_COURIER'] = lang('REQUEST_COURIER');
		$this->dropoff_types['STATION'] = lang('STATION');

		// Packaging types
		$this->package_types['FEDEX_10KG_BOX'] = lang('FEDEX_10KG_BOX');
		$this->package_types['FEDEX_25KG_BOX'] = lang('FEDEX_25KG_BOX'); 
		$this->package_types['FEDEX_BOX'] = lang('FEDEX_BOX');
		$this->package_types['FEDEX_ENVELOPE'] = lang('FEDEX_ENVELOPE'); 
		$this->package_types['FEDEX_PAK'] = lang('FEDEX_PAK');
		$this->package_types['FEDEX_TUBE'] = lang('FEDEX_TUBE');
		$this->package_types['YOUR_PACKAGING'] = lang('YOUR_PACKAGING');


		// Available Services
		$this->service_list['EUROPE_FIRST_INTERNATIONAL_PRIORITY']	= lang('EUROPE_FIRST_INTERNATIONAL_PRIORITY');
		$this->service_list['FEDEX_1_DAY_FREIGHT']	= lang('FEDEX_1_DAY_FREIGHT'); 
		$this->service_list['FEDEX_2_DAY']	= lang('FEDEX_2_DAY');
		$this->service_list['FEDEX_2_DAY_FREIGHT']	= lang('FEDEX_2_DAY_FREIGHT'); 
		$this->service_list['FEDEX_3_DAY_FREIGHT']	= lang('FEDEX_3_DAY_FREIGHT'); 
		$this->service_list['FEDEX_EXPRESS_SAVER']	= lang('FEDEX_EXPRESS_SAVER');
		$this->service_list['FEDEX_GROUND']	= lang('FEDEX_GROUND'); 
		$this->service_list['FIRST_OVERNIGHT']	= lang('FIRST_OVERNIGHT');
		$this->service_list['GROUND_HOME_DELIVERY']	= lang('GROUND_HOME_DELIVERY'); 
		$this->service_list['INTERNATIONAL_ECONOMY']	= lang('INTERNATIONAL_ECONOMY'); 
		$this->service_list['INTERNATIONAL_ECONOMY_FREIGHT']	= lang('INTERNATIONAL_ECONOMY_FREIGHT'); 
		$this->service_list['INTERNATIONAL_FIRST']	= lang('INTERNATIONAL_FIRST'); 
		$this->service_list['INTERNATIONAL_PRIORITY']	= lang('INTERNATIONAL_PRIORITY'); 
		$this->service_list['INTERNATIONAL_PRIORITY_FREIGHT']	= lang('INTERNATIONAL_PRIORITY_FREIGHT'); 
		$this->service_list['PRIORITY_OVERNIGHT']	= lang('PRIORITY_OVERNIGHT');
		$this->service_list['SMART_POST']	= lang('SMART_POST'); 
		$this->service_list['STANDARD_OVERNIGHT']	= lang('STANDARD_OVERNIGHT'); 
		$this->service_list['FEDEX_FREIGHT']	= lang('FEDEX_FREIGHT'); 
		$this->service_list['FEDEX_NATIONAL_FREIGHT']	= lang('FEDEX_NATIONAL_FREIGHT');
		$this->service_list['INTERNATIONAL_GROUND']	= lang('INTERNATIONAL_GROUND');

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
		$dropofftype 		= $settings['dropofftype'];
		$service			= explode(',',$settings['service']);
		$package 			= $settings['package'];
		$handling_method	= $settings['handling_method'];
		$handling_amount	= $settings['handling_amount'];
		$pkg_width	 		= $settings['width'];
		$pkg_height			= $settings['height'];
		$pkg_length 		= $settings['length'];
		$insurance 			= $settings['insurance'];
		$billAccount 		= $shipAccount;



		// Build Request

		ini_set("soap.wsdl_cache_enabled", "0");
		 
		$client = new SoapClient($this->path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

		$request['WebAuthenticationDetail'] = array(
			'UserCredential' =>array(
				'Key' => $key,
				'Password' => $password
			)
		); 
		$request['ClientDetail'] = array(
			'AccountNumber' => $shipAccount,
			'MeterNumber' => $meter
		);
		$request['TransactionDetail'] = array('CustomerTransactionId' => '*** Rate Request v14 using PHP ***');

		$request['Version'] = array(
			'ServiceId' => 'crs', 
			'Major' => '14', 
			'Intermediate' => '0', 
			'Minor' => '0'
		);

		$request['ReturnTransitAndCommit'] = false;
		$request['RequestedShipment']['RequestedCurrency'] = $this->CI->config->item('currency');
		$request['RequestedShipment']['DropoffType'] = $dropofftype;
		$request['RequestedShipment']['ShipTimestamp'] = date('c');
		$request['RequestedShipment']['PackagingType'] = $package; 

		if($insurance=='yes')
		{
			$request['RequestedShipment']['TotalInsuredValue']=array(
				'Ammount'=> $this->CI->go_cart->order_insurable_value(),
				'Currency'=> $this->CI->config->item('currency')
			);
		}

		$request['RequestedShipment']['Shipper'] = array(
				'Contact' => array(
					'CompanyName' => $this->CI->config->item('company_name'),
					'EMailAddress' => $this->CI->config->item('email')
				),
				'Address' => array(
					'StreetLines' => array($this->CI->config->item('address1'), $this->CI->config->item('address2')),
					'City' => $this->CI->config->item('city'),
					'StateOrProvinceCode' => $this->CI->config->item('state'),
					'PostalCode' => $this->CI->config->item('zip'),
					'CountryCode' => $this->CI->config->item('country')
				)
			);


		$request['RequestedShipment']['Recipient'] =  array(
				'Contact' => array(
					'PersonName' => "{$customer_address['firstname']} {$customer_address['lastname']}",
					'CompanyName' => $customer_address['company'],
					'PhoneNumber' => $customer_address['phone']
				),
				'Address' => array(
					'StreetLines' => array($customer_address['address1'], $customer_address['address2']),
					'City' => $customer_address['city'],
					'StateOrProvinceCode' => $customer_address['zone'],
					'PostalCode' => $customer_address['zip'],
					'CountryCode' => $customer_address['country_code'],
					//'Residential' => false   // no way to determine this
				)
			);

		$request['RequestedShipment']['RateRequestTypes'] = 'LIST'; 
		$request['RequestedShipment']['PackageCount'] = '1';
		$request['RequestedShipment']['RequestedPackageLineItems'] =  array(
				'SequenceNumber'=>1,
				'GroupPackageCount'=>1,
				'Weight' => array(
					'Value' => $this->CI->go_cart->order_weight(),
					'Units' => $this->CI->config->item('weight_unit')
				),
				'Dimensions' => array(
					'Length' => $pkg_length,
					'Width' => $pkg_width,
					'Height' => $pkg_height,
					'Units' => $this->CI->config->item('dimension_unit')
				)
			);

		// Send the request to FedEx
		$response = $client->getRates($request);

		// Handle response
		if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR' )
		{

			if(!is_array(@$response->RateReplyDetails))
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
						if($handling_method=='price')
						{
							$amount += $handling_amount;
						}
						elseif($handling_method=='percent')
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

	}

	function install()
	{
		$default_settings	= array(
			'enabled'=>0,
			'key'=>'',
			'password'=>'',
			'meter'=>'',
			'shipaccount'=>'',
			'handling_method'=>'Price',
			'handling_amount'=>5,
			'length' => 10,
			'width' => 10,
			'height' => 3,
			'dropofftype'=>'',
			'insurance'=>'',
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

		$data['service_list'] = $this->service_list;
		$data['package_types'] = $this->package_types;
		$data['dropoff_types']	= $this->dropoff_types;

		if(!$post)
		{
			$settings		= $this->CI->Settings_model->get_settings('fedex');

			$data['package']			= $settings['package'];
			$data['service']			= explode(',', $settings['service']);
			$data['dropofftype'] 		= $settings['dropofftype'];
			$data['key']				= $settings['key'];
			$data['shipaccount']		= $settings['shipaccount'];
			$data['meter']				= $settings['meter'];
			$data['password']			= $settings['password'];
			$data['handling_method']	= $settings['handling_method'];
			$data['handling_amount']	= $settings['handling_amount'];
			$data['height'] 			= $settings['height'];
			$data['width'] 				= $settings['width'];
			$data['length']				= $settings['length'];
			$data['insurance'] 			= $settings['insurance'];
			$data['enabled']			= $settings['enabled'];
		}
		else
		{
			$data['package']			= $post['package'];
			$data['service'] 			= $post['service'];
			$data['dropofftype'] 		= $post['dropofftype'];
			$data['key']				= $post['key'];
			$data['password']			= $post['password'];
			$data['shipaccount']		= $post['shipaccount'];
			$data['meter']				= $post['meter'];
			$data['handling_method']	= $post['handling_method'];
			$data['handling_amount']	= $post['handling_amount'];
			$data['height'] 			= $post['height'];
			$data['width']				= $post['width'];
			$data['length']				= $post['length'];
			$data['insurance'] 			= $post['insurance'];
			$data['enabled']			= $post['enabled'];

		}

	return $this->CI->load->view('admin_form', $data, true);

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
