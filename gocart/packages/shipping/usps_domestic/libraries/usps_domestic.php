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
		
		$form	= '<table><tr><td>Username: </td><td>'.form_input('username', $username, 'class="gc_tf1"') .'</td></tr>
					<tr><td>Password: </td><td>'.form_input('password', $password, 'class="gc_tf1"') .'</td></tr>
					<tr><td>Mode: </td><td>';
		
		$opts = array('test'=>'Test', 'live'=>'Live');
		
		$form .= form_dropdown('mode', $opts, $mode);
		
		$form	.= '</td></tr><tr><td valign="top">Services To Offer: </td><td>';
		
		 foreach($this->service_list as $id=>$opt)
         {
         	$form .= "<input type='checkbox' name='service[]' value='$id' ";
         	if(in_array($id, $service)) $form .= "checked='checked'";
         	$form .= "> ".  htmlspecialchars_decode(html_entity_decode(stripslashes($opt))) ." <br />";
         }
         
        
	
		
		$form .= '</td></tr><tr><td>Container: </td><td>';
		
		
		$opts = array('VARIABLE'=>'Variable',
					  'FLAT RATE BOX'=>'Flat Rate Box',
					  'MD FLAT RATE BOX'=>'Medium Flat Rate Box',
					  'LG FLAT RATE BOX'=>'Large Flat Rate Box',
					  'FLAT RATE ENVELOPE'=>'Flat Rate Envelope',
					  'RECTANGULAR'=>'Rectangular',
					  'NONRECTANGULAR'=>'Non Rectangular'
					  );
		
		$form .= form_dropdown('container', $opts, $container);

		$form	.= '</td></tr><tr><td>Size: </td><td>';
		
		
		$opts = array('REGULAR'=>'Regular',
					  'LARGE'=>'Large',
					  'OVERSIZE'=>'Oversize'	
					 );
		
		$form .= form_dropdown('size', $opts, $size);
		
		$form .= '</td></tr><tr><td colspan=2>(Dimensions required for size LARGE)</td>';
		
		$form .= '</tr><tr><td>Pkg Length: </td><td>';
		$form .= form_input('length', $length, 'class="gc_tf1"');
		
		$form .= '</td></tr><tr><td>Pkg Width: </td><td>';
		$form .= form_input('width', $width, 'class="gc_tf1"');
		
		$form .= '</td></tr><tr><td>Pkg Height: </td><td>';
		$form .= form_input('height', $height, 'class="gc_tf1"');
		
		$form .= '</td></tr><tr><td>Pkg Girth: </td><td>';
		$form .= form_input('girth', $girth, 'class="gc_tf1"');
		
		$form .= '</td></tr><tr><td>Machinable: </td><td>';
		
		$opts = array('TRUE'=>'True', 'FALSE'=>'False');
		
		$form .= form_dropdown('machinable', $opts, $machinable);
		
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
		$save = $_POST;
		
		
		//count the errors
		if(empty($save['service']))
		{
			return 'You must choose at least one service';
		}
		else if(empty($save['username']))
		{
			return 'You must provide a username';
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
