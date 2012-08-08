<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class usps_international
{
	var $CI;
	
	var $liveserver	= 'http://production.shippingapis.com/ShippingAPI.dll';
    var $service_list;
    
	function usps_international()
	{
		//we're going to have this information in the back end for editing eventually
		//username password, origin zip code etc.
		$this->CI =& get_instance();
		$this->CI->load->model('Settings_model');
		$this->CI->lang->load('usps_international');
		
		$this->service_list = array(
			
			"USPS GXG&lt;sup&gt;&amp;trade;&lt;/sup&gt; Envelopes**",
			"Express Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International",
			"Express Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International Flat Rate Envelope",
			"Express Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International Legal Flat Rate Envelope",
			"Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International",
			"Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International Large Flat Rate Box",
			"Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International Medium Flat Rate Box",
			"Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International Small Flat Rate Box**",
			"Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International DVD Flat Rate Box**",
			"Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International Large Video Flat Rate Box**",
			"Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International Flat Rate Envelope**",
			"Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International Legal Flat Rate Envelope**",
			"Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International Padded Flat Rate Envelope**",
			"Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International Gift Card Flat Rate Envelope**",
			"Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International Small Flat Rate Envelope**",
			"Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International Window Flat Rate Envelope**",
			"First-Class Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International Package**",
			"First-Class Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International Large Envelope**"
						
		);

	}
	
	function rates()
	{
		$rates = array();
		
		$this->CI->load->library('session');
		
		// get customer info
		$customer = $this->CI->go_cart->customer();
		$dest_zip 	= $customer['ship_address']['zip'];
		$dest_country = $customer['ship_address']['country'];
		
		//grab this information from the config file
		$country	= $this->CI->config->item('country');
		$orig_zip	= $this->CI->config->item('zip');
		
		// retrieve settings
		$settings	= $this->CI->Settings_model->get_settings('usps_international');
		
		//check if we're enabled
		if(!$settings['enabled'] || $settings['enabled'] < 1)
		{
			return array();
		}
		
		$user	 			= $settings['username'];
		$pass 				= $settings['password'];
		$service			= explode(',',$settings['service']);
		$mailtype 			= $settings['mailtype'];
		$container 			= $settings['container'];
		$size 				= $settings['size'];
		$machinable 		= $settings['machinable'];
		$handling_method	= $settings['handling_method'];
		$handling_amount	= $settings['handling_amount'];
		
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
			
		if($dest_country=='United States')
		{
			return array();
		}
		
		//$str = '<IntlRateV2Request USERID="'. $user . '" PASSWORD="' . $pass . '">';
		$str = '<IntlRateV2Request USERID="'. $user . '">';
		$str .= '<Package ID="1">';
        $str .= '<Pounds>'.$lbs.'</Pounds><Ounces>'.$oz.'</Ounces>';
        $str .= '<MailType>ALL</MailType>';
        $str .= '<ValueOfContents>'.$total.'</ValueOfContents>';
        $str .= '<Country>'.$dest_country.'</Country>';
        $str .= '<Container>' . $container .'</Container><Size>'.$size.'</Size>';
        $str .= '<Width>10</Width><Length>5</Length><Height>5</Height><Girth>5</Girth>';
        $str .= '</Package></IntlRateV2Request>';
	
		$str = $this->liveserver .'?API=IntlRateV2&XML='. urlencode($str);
	  
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
       	
 
       	
       	if(isset($array['INTLRATEV2RESPONSE'][0]['PACKAGE'][0]['ERROR'])) 
       	{
       	//	var_dump($array);
       		
       		return array(); // if the request failed, just send back an empty set
       	}
       	
   		foreach ($array['INTLRATEV2RESPONSE'][0]['PACKAGE'][0]['SERVICE'] as $value)
       	{	
	         if(in_array($value['SVCDESCRIPTION'][0]['VALUE'],$service_list))
             {	
	         	$amount = $value['POSTAGE'][0]['VALUE'];
	         	
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
	
	            $rates[html_entity_decode($value['SVCDESCRIPTION'][0]['VALUE'])] = $amount;
		      }	
        }

  			
  			      
  
       return $rates;
	}
	
	function install()
	{
		$default_settings		= array(
			'username'			=> '',
			'password'			=> '',
			'mailtype'			=> 'ALL',
			'container'			=> 'RECTANGULAR',
			'service'			=> implode(',', array_keys($this->service_list)),
			'size'				=> 'LARGE',
			'length'			=> '',
			'width'				=> '',
			'height'			=> '',
			'girth'				=> '',
			'machinable'		=> 'true',
			'handling_method'	=> '$',
			'handling_amount'	=> 5,
			'enabled'			=> '0'
		);
		//set a default blank setting for flatrate shipping
		$this->CI->Settings_model->save_settings('usps_international', $default_settings);
	}
	
	function uninstall()
	{
		$this->CI->Settings_model->delete_settings('usps_international');
	}
	
	function form($post	= false)
	{
		$this->CI->load->helper('form');
		
		//this same function processes the form
		if(!$post)
		{
			$settings			= $this->CI->Settings_model->get_settings('usps_international');
			$mailtype			= $settings['mailtype'];
			$container			= $settings['container'];
			$service   			= explode(',', $settings['service']);
			$username			= $settings['username'];
			$password			= $settings['password'];
			$enabled			= $settings['enabled'];
			$size				= $settings['size'];
			$length				= $settings['length'];
			$width				= $settings['width'];
			$height				= $settings['height'];
			$girth				= $settings['girth'];
			$machinable			= $settings['machinable'];
			$handling_method	= $settings['handling_method'];
			$handling_amount	= $settings['handling_amount'];
		}
		else
		{
			$mailtype			= $post['mailtype'];
			$container			= $post['container'];
			$service 			= $post['service'];
			$username			= $post['username'];
			$password			= $post['password'];
			$enabled			= $post['enabled'];
			$size				= $post['size'];
			$length				= $post['length'];
			$width				= $post['width'];
			$height				= $post['height'];
			$girth				= $post['girth'];
			$machinable			= $post['machinable'];
			$handling_method	= $post['handling_method'];
			$handling_amount	= $post['handling_amount'];
		}
		ob_start();
		?>

		<label><?php echo lang('username');?></label>
		<?php echo form_input('username', $username, 'class="span3"');?>
				
		<?php //form_input('password', $password);?>
		
		<label><?php echo lang('method')?></label>
		<div class="controls">
		 <?php foreach($this->service_list as $id=>$opt):?>
			<label class="checkbox">
				<input type='checkbox' name='service[]' value='<?php echo $id;?>' <?php echo(in_array($id, $service))?"checked='checked'":'';?> /> <?php echo htmlspecialchars_decode(html_entity_decode(stripslashes($opt)));?>
			</label>
         <?php endforeach;?>
		</div>
		
		<label><?php echo lang('mail_type');?></label>
		<?php
		$opts	= array('ALL'		=>lang('all'),
						'PACKAGE'	=>lang('package'),
						'ENVELOPE'	=>lang('envelope')
						);
		
		echo form_dropdown('mailtype', $opts, $mailtype, 'class="span3"');?>
		
		<label><?php echo lang('container');?></label>
		<?php
		$opts = array(
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
			return 'You must choose at least one service';
		}
		else if(empty($save['username']))
		{
			return 'You must provide a username';
		}
		else
		{
			
			$save['service'] = implode(',', $save['service']);
			
			//we save the settings if it gets here
			$this->CI->Settings_model->save_settings('usps_international', $save);
			
			return false;
		}
	}
}
