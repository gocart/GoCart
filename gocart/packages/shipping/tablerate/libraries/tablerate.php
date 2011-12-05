<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class tablerate
{
	var $CI;
	var $cart;
	
	function tablerate()
	{
		$this->CI =& get_instance();
		$this->CI->lang->load('tablerate');
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
		
		$settings	= $this->CI->Settings_model->get_settings('tablerate');
		$methods = unserialize($settings['method']);
		$locations = unserialize($settings['location']);
		
		if(!$settings['enabled'] && $settings['enabled'] == 0)
		{
			return array();
		}
		
		$rate_lists		= $this->CI->Settings_model->get_settings('tablerates');
		$rate_lists = unserialize($rate_lists['rates']);
		
		$order_weight	= $this->get_order_weight();
		$order_price = $this->get_order_price();
		
		$countries = $this->CI->Location_model->get_countries();
		
		$rates = array();
		
		foreach($rate_lists as $table=>&$list)
		{
			

			// check location by country ( if applicable )
			if(!empty($locations[$table]) && $locations[$table]!=$customer['ship_address']['country_id'])
			{	
				// if the customer is not in the country specified by this table, then skip it
				continue;
			}
				
			//sort rates highest "From" to lowest
			krsort($list);
		
			if ($methods[$table] == 'weight')
			{
				foreach ($list as $weight => $rate)
				{
					
					$rates[str_replace('_', ' ', $table)] = $rate;
					if($weight <= $order_weight)
					{
						break;
					}
				}
			}
			elseif ($methods[$table] == 'price')
			{
				foreach ($list as $price => $rate)
				{
					
					$rates[str_replace('_', ' ', $table)] = $rate;
					if($price <= $order_price)
					{
						break;
					}
				}
			}			

		}
			
		return $rates;
	}
	
	function install()
	{
		// install some example data
		$rates =  array(
					 '80'	=> '85.00'
					,'70'	=> '65.00'
					,'60'	=> '55.00'
					,'50'	=> '55.00'
					,'40'	=> '45.00'
					,'30'	=> '35.00'
					,'20'	=> '25.00'
					,'10'	=> '15.00'
					,'0'	=> '5.00');
		
		$table	= array('Example' => $rates);
		
		//note that the code here is plural, these are the rates
		$this->CI->Settings_model->save_settings('tablerates', array('rates' => serialize($table)));
		$this->CI->Settings_model->save_settings('tablerate', array('enabled'=>'0', 
																	'method'=>serialize(array('Example'=>'price')),
																	'location'=>serialize(array('Example'=>'')) //location is by country ID
																	 ));
	}
	
	function uninstall()
	{
		$this->CI->Settings_model->delete_settings('tablerate');
		$this->CI->Settings_model->delete_settings('tablerates');
	}
	
	function form($post	= false)
	{
		//this same function processes the form
		if(!$post)
		{
			$settings	= $this->CI->Settings_model->get_settings('tablerate');
			$settings['method'] = unserialize($settings['method']);
			$settings['location'] = unserialize($settings['location']);
			
			$rates		= $this->CI->Settings_model->get_settings('tablerates');
			
			$rates = unserialize($rates['rates']);
			
		}
		else
		{
			$settings['location'] = $post['location'];
			$settings['method'] = $post['method'];
			$settings['enabled'] = $post['enabled'];
			$rates	= $this->organize_post_rates($post['rates']);
		}
		
		$countries		= $this->CI->Location_model->get_countries_menu();
		
		// fetch form contents
		$data = array('settings'=>$settings,
					  'rates'	=>$rates,
					  'countries' =>$countries );
		
		return $this->CI->load->view('admin_form', $data, true);
		
	}
	
	function check()
	{	
		if(empty($_POST))
		{
			return '<div>'.lang('empty_post').'</div>';
		}
		
		foreach($_POST['from'] as $table=>$list)
		{
			if(empty($list))
			{
				return '<div>'.lang('post_err').'</div>';
			}
		}
		
		// build the rates list from input
		$rates	= $this->organize_post_rates($_POST);
		
		// implode the rates
		$rates = serialize($rates);
		
		// implode the methods
		$methods = serialize($_POST['method']);
		
		// implode location data
		$locations = serialize($_POST['location']);
	
		//we save the settings if it gets here
		$this->CI->Settings_model->save_settings('tablerate', array('method'=>$methods, 'location'=>$locations, 'enabled'=>$_POST['enabled']));
		$this->CI->Settings_model->save_settings('tablerates', array('rates'=>$rates));
		
		return false;
		
	}
	
	function organize_post_rates($post)
	{
		$rates	= array();
		
		foreach($post['from'] as $table=>$list)
		{
			foreach($list as $key=>$value)
			{
				$rates[$table][$value] = $post['rate'][$table][$key];
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
