<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class United_parcel_service
{

	var $CI;
	var $ups_services;

	function United_parcel_service()
	{
		$this->CI =& get_instance();
		$this->CI->lang->load('ups');

		// standard services
		$this->ups_services = array(
			'01' => 'UPS Next Day Air',
			'02' => 'UPS Second Day Air',
			'03' => 'UPS Ground',
			'07' => 'UPS Worldwide Express',
			'08' => 'UPS Worldwide Expedited',
			'11' => 'UPS Standard',
			'12' => 'UPS Three-Day Select',
			'13' => 'UPS Next Day Air Saver',
			'14' => 'UPS Next Day Air Early AM',
			'54' => 'UPS Worldwide Express Plus',
			'59' => 'UPS Second Day Air AM',
			'65' => 'UPS Saver'
		);

	}


	function rates( )
	{

		// load settings
		if($settings = $this->CI->Settings_model->get_settings('united_parcel_service')) {
			$access_key						= $settings['access_key'];
			$ups_account_username			= $settings['ups_account_username'];
			$ups_account_password			= $settings['ups_account_password'];
			$enabled						= $settings['enabled'];
			$services 	    				= explode(',', $settings['services']);	
			$handling_method 				= $settings['handling_method'];
			$handling_amount 				= $settings['handling_amount'];	
		} else {
			return array(); // no settings, no compute
		}

		$zip_code			= $this->CI->config->item('zip');
		$customer			= $this->CI->go_cart->customer();
		$weight 			= $this->CI->go_cart->order_weight();
		$currency 			= $this->CI->config->item('currency');
		$insured_value 		= $this->CI->go_cart->order_insurable_value();

		// shipping address will always be there
		$destination_zip 	= $customer['ship_address']['zip'];


		$data ="<?xml version='1.0'?>
		<AccessRequest xml:lang='en-US'>
			<AccessLicenseNumber>$access_key</AccessLicenseNumber>
		<UserId>$ups_account_username</UserId>
		<Password>$ups_account_password</Password>
		</AccessRequest>
		<?xml version='1.0'?>
		<RatingServiceSelectionRequest xml:lang='en-US'>
			<Request>
			<TransactionReference>
			<CustomerContext>Rating and Service</CustomerContext>
		<XpciVersion>1.0001</XpciVersion>
		</TransactionReference>
		<RequestAction>Rate</RequestAction>
		<RequestOption>shop</RequestOption>
		</Request>
		<PickupType>
			<Code>01</Code>
		</PickupType>
		<Shipment>
			<Shipper>
			<Address>
			<PostalCode>$zip_code</PostalCode>
		</Address>
		</Shipper>
		<ShipTo>
			<Address>
			<PostalCode>$destination_zip</PostalCode>
		</Address>
		</ShipTo>
		<Package>
			<PackagingType>
			<Code>02</Code>
		<Description>Package</Description>
		</PackagingType>
		<Description>Rate Shopping</Description>
		<PackageWeight>
			<Weight>$weight</Weight>
		</PackageWeight>
		<InsuredValue>
			<CurrencyCode>$currency</CurrencyCode>
		<MonetaryValue>$insured_value</MonetaryValue>
		</InsuredValue>
		</Package>
		<ShipmentServiceOptions/>
		</Shipment>
		</RatingServiceSelectionRequest>";


		$ch = curl_init("https://www.ups.com/ups.app/xml/Rate");
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_TIMEOUT, 60);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
		$result=curl_exec ($ch);

		//die(print_r($result)); // uncomment to debug

		$data = strstr($result, '<?');

		$xml = new SimpleXMLElement($data);
		if($xml->Response->ResponseStatusCode == '1')
		{
			foreach($xml->RatedShipment as $shipping_choice)
			{
				// only display the ones the admin has chosen to show
				if(in_array($shipping_choice->Service->Code, $services))
				{
					$k = $this->ups_services[(string)$shipping_choice->Service->Code];
					$amount = (string)$shipping_choice->TotalCharges->MonetaryValue;

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

					$shipping_choices[$k] = $amount;
				}
			}
			return $shipping_choices;
		}
		else
		{	
			//send back empty array on fail
			return array();
		}
	}


	function install()
	{
		$default_settings	= array(
			'access_key'			=> '',        
			'ups_account_username'	=> '',                
			'ups_account_password'	=> '',            
			'services' 				=> '11',
			'handling_method'		=> '$',
			'handling_amount'		=> '5',
			'enabled'				=> '0'
		);
		//set a default blank setting for flatrate shipping
		$this->CI->Settings_model->save_settings('united_parcel_service', $default_settings);
	}

	function uninstall()
	{
		$this->CI->Settings_model->delete_settings('united_parcel_service');
	}

	function form($post	= false)
	{

		$this->CI->load->helper('form');

		//this same function processes the form
		if(!$post)
		{
			$settings	= $this->CI->Settings_model->get_settings('united_parcel_service');
			$access_key	= $settings['access_key'];
			$username	= $settings['ups_account_username'];
			$password	= $settings['ups_account_password'];
			$services 	= explode(',', $settings['services']);
			$handling_method = $settings['handling_method'];
			$handling_amount = $settings['handling_amount'];
			$enabled	= $settings['enabled'];
		}
		else
		{
			$access_key	= $post['access_key'];
			$username	= $post['ups_account_username'];
			$password	= $post['ups_account_password'];
			$services 	= $post['services'];
			$handling_method = $post['handling_method'];
			$handling_amount = $post['handling_amount'];
			$enabled	= $post['enabled'];
		}

		ob_start();
		?>

		<label><?php echo lang('account');?></label>
		<?php echo form_input('ups_account_username', $username, 'class="span3"');?>

		<label><?php echo lang('password');?></label>
		<?php echo form_input('ups_account_password', $password, 'class="span3"');?>

		<label><?php echo lang('key');?></label>
		<?php echo form_input('access_key', $access_key, 'class="span3"');?>


		<label><?php echo lang('services');?></label>

		<?php  foreach($this->ups_services as $id=>$opt):?>
			<label class="checkbox">
				<input type="checkbox" name="services[]" value="<?php echo $id;?>" <?php echo (in_array($id, $services))?'checked="checked"':'';?> />
				<?php echo $opt;?>
			</label>
		<?php endforeach;?>

		<label><?php echo lang('fee');?></label>
		<?php echo form_dropdown('handling_method', array('$'=>'$', '%'=>'%'), $handling_method, 'class="span1"');?>
		<?php echo form_input('handling_amount', $handling_amount, 'class="span3"');?>

		<label><?php echo lang('enabled');?></label>
		<?php echo form_dropdown('enabled', array(lang('disabled'), lang('enabled')), $enabled, 'class="span3"');?>

		<?php
		$form =ob_get_contents();
		ob_end_clean();

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
			$settings = $this->CI->input->post();
			$settings['services'] = implode(',', $settings['services']);

			//we save the settings if it gets here
			$this->CI->Settings_model->save_settings('united_parcel_service', $settings);

			return false;
		}
	}

} 