<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class table_rate
{
	var $CI;
	var $cart;
	
	function table_rate()
	{
		$this->CI =& get_instance();
		$this->CI->lang->load('table_rate');
	}
	
	function rates()
	{
		//$method either equals weight or price	
		//this can be set either from some sort of admin panel, or directly here.
		$this->CI->load->library('session');
		
		// get customer info
		$customer = $this->CI->go_cart->customer();
		
		//if there is no address set then return blank
		if(empty($customer['ship_address']))
		{
			return array();
		}
		
		$settings	= $this->CI->Settings_model->get_settings('table_rate');
		
		if(!(bool)$settings['enabled'])
		{
			return array();
		}
		
		$rates			= unserialize($settings['rates']);
		
		$order_weight	= $this->get_order_weight();
		$order_price	= $this->get_order_price();
		
		$countries = $this->CI->Location_model->get_countries();
		
		$return = array();
		foreach($rates as $rate)
		{
			if((bool)$rate['country'] && ($rate['country'] != $customer['ship_address']['country_id']))
			{
				// if the customer is not in the country specified by this table, then skip it
				continue;
			}

			//sort rates highest "From" to lowest
			krsort($rate['rates'], SORT_NUMERIC);

			if ($rate['method'] == 'weight')
			{
				foreach ($rate['rates'] as $key => $val)
				{
					if($key <= $order_weight)
					{
						$return[$rate['name']] = $val;
						break;
					}
				}
			}
			elseif ($rate['method'] == 'price')
			{
				foreach ($rate['rates'] as $key => $val)
				{
					if($key <= $order_price)
					{
						$return[$rate['name']] = $val;
						break;
					}
				}
			}
		}
		
		return $return;
	}
	
	function install()
	{
		
		$install			= array();
		$install['enabled']	= false;
		
		$rate				= array();
		$rate[0]			= array();
		$rate[0]['name']	= 'Example';
		$rate[0]['method']	= 'price';
		$rate[0]['country']	= 0;
		
		// install some example data
		$rate[0]['rates'] =  array(
					 '80'	=> '85.00'
					,'70'	=> '65.00'
					,'60'	=> '55.00'
					,'50'	=> '55.00'
					,'40'	=> '45.00'
					,'30'	=> '35.00'
					,'20'	=> '25.00'
					,'10'	=> '15.00'
					,'0'	=> '5.00');
		
		$install['rates']	= serialize($rate);
		
		$this->CI->Settings_model->save_settings('table_rate', $install);
	}
	
	function uninstall()
	{
		$this->CI->Settings_model->delete_settings('table_rate');
	}
	
	function form($_POST	= false)
	{ 
		$this->CI->load->helper('form');
		//this same function processes the form
		if(!$_POST)
		{
			$settings				= $this->CI->Settings_model->get_settings('table_rate');
			$settings['rates']		= unserialize($settings['rates']);
		}
		else
		{
			$settings['enabled']	= $_POST['enabled'];
			$settings['rates']		= $_POST['rates'];
		}
		
		$countries					= $this->CI->Location_model->get_countries_menu();
		
		$data						= $settings;
		$data['countries']			= array(0=>lang('all_countries')) + $countries;
		
		return $this->CI->load->view('table_rate_form', $data, true);
		
	}
	
	function check()
	{	
		if(empty($_POST))
		{
			return '<div>'.lang('empty_post').'</div>';
		}
		
		$save['enabled']	= $_POST['enabled'];
		
		$rates				= array();
		foreach($_POST['rate'] as $rate)
		{
			if(isset($rate['rates']))
			{
				$rate['rates']	= $this->organize_rates($rate['rates']);
			}
			else
			{
				$rate['rates']	= array();
			}
			
			$rates[]		= $rate;
		}
		$save['rates']	= serialize($rates);
		
		//we save the settings if it gets here
		$this->CI->Settings_model->save_settings('table_rate', $save);
		
		return false;
		
	}
	
	function organize_rates($rates)
	{
		$new_rates	= array();
		foreach($rates as $r)
		{
			if(is_numeric($r['from']) && is_numeric($r['rate']))
			{
				$new_rates[$r['from']]	= $r['rate'];
			}
			ksort($new_rates, SORT_NUMERIC);
		}
		return $new_rates;
	}
	
	
	function organize_post_rates($_POST)
	{
		$rates	= array();
		
		foreach($_POST['from'] as $table=>$list)
		{
			foreach($list as $key=>$value)
			{
				$rates[$table][$value] = $_POST['rate'][$table][$key];
			}
			// sort the list
			krsort($rates[$table]);
		}

		
		return $rates;
	}
	
	function get_order_weight()
	{
		return $this->CI->go_cart->order_weight();
	}
	
	function get_order_price()
	{
		return $this->CI->go_cart->subtotal();
	}
}