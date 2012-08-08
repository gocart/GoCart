<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*

	USPS Domestic.
	
	For use in the USA 
	
*/

class usps_domestic
{
	var $CI;
	
	
	var $testserver = 'http://testing.shippingapis.com/ShippingAPITest.dll';
	var $liveserver	= 'http://production.shippingapis.com/ShippingAPI.dll';
    var $service_list;
    
	function usps_domestic()
	{
		//we're going to have this information in the back end for editing eventually
		//username password, origin zip code etc.
		$this->CI =& get_instance();
		$this->CI->load->model('Settings_model');
		$this->CI->lang->load('usps_domestic');
		
		$this->service_list = array(
			// Domestic Services
			"Express Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt;",
			"Express Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; Hold For Pickup",
			"Express Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; Sunday\/Holiday Delivery",
			"Express Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; Flat Rate Envelope",
			"Express Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; Flat Rate Envelope Hold For Pickup",
			"Express Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; Sunday\/Holiday Delivery Flat Rate Envelope",
			"Express Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; Legal Flat Rate Envelope",
			"Express Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; Legal Flat Rate Envelope Hold For Pickup",
			"Express Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; Sunday\/Holiday Delivery Legal Flat Rate Envelope",
			"Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt;",
			"Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; Large Flat Rate Box",
			"Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; Medium Flat Rate Box",
			"Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; Small Flat Rate Box",
			"Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; Flat Rate Envelope",
			"Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; Legal Flat Rate Envelope",
			"Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; Padded Flat Rate Envelope",
			"Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; Gift Card Flat Rate Envelope",
			"Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; Small Flat Rate Envelope",
			"Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; Window Flat Rate Envelope",
			"Parcel Post&lt;sup&gt;&amp;reg;&lt;/sup&gt;",
			"Media Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt;",
			"Library Mail"
		);
		
		
		
		/*
		$this->service_list = array(
				'Express Mail to PO Addressee'=>'Express Mail to PO Addressee',
      		  	'First Class Mail'=>'First-Class Mail',
     			'Priority Mail'=>'Priority Mail',
      			'Parcel Post'=>'Parcel Post',
      			'Bound Printed Matter'=>'Bound Printed Matter',
      			'Media Mail'=>'Media Mail',
      			'Library Mail'=>'Library Mail' 
      			);
      	*/

	}
	
	function rates()
	{

		$this->CI->load->library('session');

		// get customer info
		$customer = $this->CI->go_cart->customer();
		$dest_zip 	= $customer['ship_address']['zip'];
		$dest_country = $customer['ship_address']['country'];

		//grab this information from the config file
		$country	= $this->CI->config->item('country');
		$orig_zip	= $this->CI->config->item('zip');

		// retrieve settings
		$settings	= $this->CI->Settings_model->get_settings('usps_domestic');

		//check if we're enabled
		if(!$settings['enabled'] || $settings['enabled'] < 1)
		{
			return array();
		}

		$user	 		= $settings['username'];
		$pass 			= $settings['password'];
		$service		= explode(',',$settings['service']);
		$container 		= $settings['container'];
		$size 			= 'Regular';//$settings['size'];
		$machinable 	= $settings['machinable'];
		$handling_method = $settings['handling_method'];
		$handling_amount = $settings['handling_amount'];

		// build allowed service list
		foreach($service as $s)
		{
			$service_list[] = $this->service_list[$s];
		}

		//set the weight
		$weight	= $this->CI->go_cart->order_weight();

		// value of contents
		$total = $this->CI->go_cart->order_insurable_value();

		//strip the decimal
		$oz		= ($weight-(floor($weight)))*100;
		//set pounds
		$lbs	= floor($weight);
		//set ounces based on decimal
		$oz	= round(($oz*16)/100);

		// no foreign support
		if($country!="US")
		{
			return array(); 
		}

		// no intl shipping in this lib
		if($dest_country!='United States')
		{
			return array();
		}

		// send a standard test request
		if($settings['mode'] == 'test')
		{

			$str = '<RateV2Request USERID="';
			$str .= $user . '"><Package ID="1"><Service>';
			$str .= 'All</Service><ZipOrigination>10022</ZipOrigination>';
			$str .= '<ZipDestination>20008</ZipDestination>';
			$str .= '<Pounds>10</Pounds><Ounces>5</Ounces>';
			$str .= '<Container>Flat Rate Box</Container><Size>LARGE</Size>';
			$str .= '<Machinable>True</Machinable></Package></RateV2Request>';

			$str = $this->testserver .'?API=RateV2&XML='. urlencode($str);

		}
		else
		{

			// Domestic Rates
			$str = '<RateV4Request USERID="';
			//$str .= $user . '" PASSWORD="' . $pass . '"><Package ID="1"><Service>';
			$str .= $user . '"><Package ID="1"><Service>';
			$str .= 'ALL</Service><ZipOrigination>'.$orig_zip.'</ZipOrigination>';
			$str .= '<ZipDestination>'.$dest_zip.'</ZipDestination>';
			$str .= '<Pounds>'.$lbs.'</Pounds><Ounces>'.$oz.'</Ounces>';
			$str .= '<Container>' . $container .'</Container><Size>'.$size.'</Size>';
			$str .= '<Machinable>'.$machinable.'</Machinable></Package></RateV4Request>';

			$str = $this->liveserver .'?API=RateV4&XML='. urlencode($str);				

		}


		$ch = curl_init();
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $str);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		// grab URL and pass it to the browser
		$ats = curl_exec($ch);

		// close curl resource, and free up system resources
		curl_close($ch);
		//$xmlParser = new xmlparser();
		$this->CI->load->library('xmlparser');
		$array = $this->CI->xmlparser->GetXMLTree($ats);



		if(isset($array['ERROR'])) 
		{
			var_dump($array);

			return array(); // if the request failed, just send back an empty set
		}

		$rates = array();



		// Parse test mode response
		if($settings['mode'] == 'test')
		{
			foreach ($array['RATEV2RESPONSE'][0]['PACKAGE'][0]['POSTAGE'] as $value)
			{

				$amount = $value['RATE'][0]['VALUE'];

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

				$rates[$value['MAILSERVICE'][0]['VALUE']] = $amount;

			}

			// Parse live response
		} else {
			//var_dump($service_list);

			foreach ($array['RATEV4RESPONSE'][0]['PACKAGE'][0]['POSTAGE'] as $value)
			{		
				//echo $value['MAILSERVICE'][0]['VALUE']."\n";
				if(in_array($value['MAILSERVICE'][0]['VALUE'],$service_list))
				{	             	
					$amount = $value['RATE'][0]['VALUE'];

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

					$rates[$value['MAILSERVICE'][0]['VALUE']] = $amount;
				}	
			}

		}

		return $rates;
	}
	
	function install()
	{
		$default_settings	= array(
			'mode'=>'test',
			'username'=>'',
			'password'=>'',
			'container'=>'Flat Rate Box',
			'service' => implode(',', array_keys($this->service_list)),
			'size'=>'LARGE',
			'length'=>'',
			'width'=>'',
			'height'=>'',
			'girth'=>'',
			'machinable'=>'true',
			'handling_method'=>'$',
			'handling_amount'=>5,
			'enabled'=>'0'
		);
		//set a default blank setting for flatrate shipping
		$this->CI->Settings_model->save_settings('usps_domestic', $default_settings);
	}
	
	function uninstall()
	{
		$this->CI->Settings_model->delete_settings('usps_domestic');
	}
	
	function form($post	= false)
	{
		$this->CI->load->helper('form');
		
		//this same function processes the form
		if(!$post)
		{
			$settings	= $this->CI->Settings_model->get_settings('usps_domestic');
			$container	= $settings['container'];
			$service   	= explode(',', $settings['service']);
			$username	= $settings['username'];
			$password	= $settings['password'];
			$enabled	= $settings['enabled'];
			$mode		= $settings['mode'];
			$size		= $settings['size'];
			$length		= $settings['length'];
			$width      = $settings['width'];
			$height		= $settings['height'];
			$girth		= $settings['girth'];
			$machinable	= $settings['machinable'];
			$handling_method = $settings['handling_method'];
			$handling_amount = $settings['handling_amount'];
		}
		else
		{
			$container	= $post['container'];
			$service 	= $post['service'];
			$username	= $post['username'];
			$password	= $post['password'];
			$mode		= $post['mode'];
			$enabled	= $post['enabled'];
			$size		= $post['size'];
			$length		= $post['length'];
			$width      = $post['width'];
			$height		= $post['height'];
			$girth		= $post['girth'];
			$machinable	= $post['machinable'];
			$handling_method = $post['handling_method'];
			$handling_amount = $post['handling_amount'];
		}
		
		ob_start();
		?>
		<div class="row">
			<div class="span12">
				<label><?php echo lang('username');?></label>
				<?php echo form_input('username', $username, 'class="span3"');?>
		
				<label><?php echo lang('password');?></label>
				<?php echo form_input('password', $username, 'class="span3"');?>
		
				<label><?php echo lang('mode');?></label>
				<?php echo form_dropdown('mode', array('test'=>lang('test'), 'live'=>lang('live')), $mode);?>

				<label><?php echo lang('method')?></label>
				<div class="controls">
				 <?php foreach($this->service_list as $id=>$opt):?>
					<label class="checkbox">
						<input type='checkbox' name='service[]' value='<?php echo $id;?>' <?php echo(in_array($id, $service))?"checked='checked'":'';?> /> <?php echo htmlspecialchars_decode(html_entity_decode(stripslashes($opt)));?>
					</label>
		         <?php endforeach;?>
				</div>
				<label><?php echo lang('container')?></label>
				<?php
				$opts	= array('VARIABLE'=>lang('variable'),
								'FLAT RATE BOX'=>lang('flat_rate_box'),
								'MD FLAT RATE BOX'=>lang('medium_flat_rate_box'),
								'LG FLAT RATE BOX'=>lang('large_flat_rate_box'),
								'FLAT RATE ENVELOPE'=>lang('flat_rate_envelope'),
								'RECTANGULAR'=>lang('rectangular'),
								'NONRECTANGULAR'=>lang('non_rectangular')
								);
		
				echo form_dropdown('container', $opts, $container, 'class="span3"');?>
		
				<label><?php echo lang('size');?></label>
				<?php
				$opts	= array('REGULAR'=>lang('regular'),
								'LARGE'=>lang('large'),
								'OVERSIZE'=>lang('oversize')
								);
				echo form_dropdown('size', $opts, $size, 'class="span3"');?>
		
				<h3><?php echo lang('size_message');?></h3>
		
				<label><?php echo lang('package_length');?></label>
				<?php echo form_input('length', $length, 'class="span3"');?>
		
				<label><?php echo lang('package_width');?></label>
				<?php echo form_input('width', $width, 'class="span3"');?>
		
				<label><?php echo lang('package_height');?></label>
				<?php echo form_input('height', $height, 'class="span3"');?>

				<label><?php echo lang('package_girth');?></label>
				<?php echo form_input('girth', $girth, 'class="span3"');?>
		
				<label><?php echo lang('machinable');?></label>
				<?php echo form_dropdown('machinable', array('TRUE'=>lang('yes'), 'FALSE'=>lang('no')), $machinable, 'class="span3"');?>
				
				<label><?php echo lang('handling_fee');?></label>
			</div>
		</div>
		<div class="row">
			<div class="span1">
				<?php echo form_dropdown('handling_method', array('$'=>'$', '%'=>'%'), $handling_method, 'class="span1"');?>
			</div>
			<div class="span2">
				<?php echo form_input('handling_amount', $handling_amount, 'class="span2"');?>
			</div>
		</div>
		<div class="row">
			<div class="span12">
				<label><?php echo lang('enabled');?></label>
				<?php echo form_dropdown('enabled', array(lang('disabled'), lang('enabled')), $enabled, 'class="span3"');?>
			</div>
		</div>
		<?php
		$form =ob_get_contents();
		ob_end_clean();
		
		return $form;
	}
	
	function check()
	{	
		$save = $_POST;
		
		
		//count the errors
		if(empty($save['service']))
		{
			return lang('service_error');
		}
		else if(empty($save['username']))
		{
			return lang('username_error');
		}
		else
		{
			
			$save['service'] = implode(',', $save['service']);
			
			//die(var_dump($save));
			
			//we save the settings if it gets here
			$this->CI->Settings_model->save_settings('usps_domestic', $save);
			
			return false;
		}
	}
}
