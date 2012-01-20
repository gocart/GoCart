<?php 
// Single page checkout controller

class Checkout extends CI_Controller {

	//we collect the categories automatically with each load rather than for each function
	//this just cuts the codebase down a bit
	var $categories	= '';
	
	//this is so there will be a breadcrumb on every page even if it is blank
	//the breadcrumbs currently suck. on a product page if you refresh, you lose the path
	//will have to find a better way for these, but it's not a priority
	var $breadcrumb	= '';	
	
	//load all the pages into this variable so we can call it from all the methods
	var $pages = '';
	
	// determine whether to display gift card link on all cart pages
	var $gift_cards_enabled = false; 
	
	// construct 
	function __construct()
	{
		parent::__construct();
		
		force_ssl();
		
		$this->load->helper(array('formatting_helper', 'form_helper'));
		$this->load->model(array('Page_model', 'Settings_model', 'Location_model'));
		$this->load->library('Go_cart');
		
		//make sure the cart isn't empty
		if($this->go_cart->total_items()==0)
		{
			redirect('cart/view_cart');
		}
		
		//fill in our variables
		$this->categories	= $this->Category_model->get_categories_tierd(0);
		$this->pages		= $this->Page_model->get_pages();	
		$gc_setting			= $this->Settings_model->get_settings('gift_cards');
		
		if(isset($gc_setting['enabled']) && (bool)$gc_setting['enabled'])
		{
			$this->gift_cards_enabled = true;
		}
		
		if ($this->config->item('require_login'))
		{
			$this->Customer_model->is_logged_in('checkout');
		}
		
		//load the theme package
		$this->load->add_package_path(APPPATH.'themes/'.$this->config->item('theme').'/');
	}

	function index()
	{
		
		//everytime they try to checkout, see if they have something in their cart
		//this will redirect them to the empty cart page if they have already confirmed their order, and had their cart wiped from the session
		if ($this->go_cart->total_items()==0){
			redirect('cart/view_cart');
		}
		
		//double check the inventory of each item before proceeding to checkout
		$inventory_check	= $this->go_cart->check_inventory();
		if($inventory_check)
		{
			//OOPS we have an error. someone else has gotten the scoop on our customer and bought products out from under them!
			//we need to redirect them to the view cart page and let them know that the inventory is no longer there.
			$this->session->set_flashdata('error', $inventory_check);
			redirect('cart/view_cart');
		}
		
		$this->load->model('Customer_model');
		
		$data['gift_cards_enabled'] = $this->gift_cards_enabled;
		$data['page_title']	= 'Check Out';
		
		
		$data['customer']	= $this->go_cart->customer();
		
		
		// load other page content 
		//$this->load->model('banner_model');
		$this->load->helper('directory');
	
		//if they want to limit to the top 5 banners and use the enable/disable on dates, add true to the get_banners function
		//$data['banners']	= $this->banner_model->get_banners();
		//$data['ads']		= $this->banner_model->get_banners(true);
		$data['categories']	= $this->Category_model->get_categories_tierd(0);		
		
		$this->load->view('checkout/checkout', $data);
	}
	
	function login()
	{
		$this->Customer_model->is_logged_in('checkout');
	}
	
	function register()
	{
		$this->Customer_model->is_logged_in('checkout', 'secure/register');
	}
	
	function shipping_payment_methods()
	{
		
		//load the shipping modules
		$shipping_methods	= array();
		foreach ($this->Settings_model->get_settings('shipping_modules') as $shipping_method=>$order)
		{
			$this->load->add_package_path(APPPATH.'packages/shipping/'.$shipping_method.'/');
			//eventually, we will sort by order, but I'm not concerned with that at the moment
			$this->load->library($shipping_method);
			
			$shipping_methods	= array_merge($shipping_methods, $this->$shipping_method->rates());
		}
	
		
		// Free shipping coupon applied ?
		if($this->go_cart->is_free_shipping()) 
		{
			// add free shipping as an option, but leave other options in case they want to upgrade
			$shipping_methods["Free Shipping (basic)"] = "0.00";
		}
		
		// format the values for currency display
		foreach($shipping_methods as &$method)
		{
			// convert numeric values into an array containing numeric & formatted values
			$method = array('num'=>$method,'str'=>format_currency($method));
		}
		
		$ship['shipping_methods']	= $shipping_methods;
		
		
		
		//load the payment modules
		$pay['payment_methods']	= array();
		
		foreach ($this->Settings_model->get_settings('payment_modules') as $payment_method=>$order)
		{
			$this->load->add_package_path(APPPATH.'packages/payment/'.$payment_method.'/');
			$this->load->library($payment_method);
			
			$payment_form = $this->$payment_method->checkout_form();
			if(!empty($payment_form))
			{
				$pay['payment_methods'][$payment_method] = array_merge($pay['payment_methods'], $payment_form);
			}
		}
		
		//Load additional details
		$details	= $this->go_cart->additional_details();
		//deadline content
		$this->load->model('Page_model');
		$details['deadline_content']	= $this->Page_model->get_page(144);
		
		$this->load->view('checkout/additional_details_form', $details);
		$this->load->view('checkout/shipping_form', $ship);
		$this->load->view('checkout/payment_form', $pay);
	}
	
	function save_additional_details()
	{
		//run everything through the XSS filter
		$this->go_cart->set_additional_details($this->input->post(null, true));
		
		
		print_r($this->input->post(null, true));
		
		//$this->session->set_flashdata('message', 'Your additional Details have been saved!');
		
		//redirect('checkout');
	}
	
	function customer_details()
	{
		$this->load->view('checkout/customer_details_static', array('customer'=>$this->go_cart->customer()));
	}
	
	function customer_form()
	{
		//clear shipping if showing the customer form
		$this->go_cart->clear_shipping();
		
		$data['customer']	= $this->go_cart->customer();
		if(isset($data['customer']['id']))
		{
			$data['customer_addresses'] = $this->Customer_model->get_address_list($data['customer']['id']);
		}
		
		if(@$data['customer']['ship_address'] == @$data['customer']['bill_address'])
		{
			$data['customer']['ship_to_bill_address'] = true;
		}
		else
		{
			$data['customer']['ship_to_bill_address'] = false;
		}
		$this->load->view('checkout/customer_details', $data);
	}
	
	// Validate & Save guest (non-logged in) customer address information
	function save_customer()
	{
		$this->load->library('form_validation');
		// only necessary if we need a separate shipping address
		if($this->input->post('ship_to_bill_address')!='yes') 
		{		
			$this->form_validation->set_rules('bill_address_id', 'Billing Address ID', 'numeric');
			$this->form_validation->set_rules('bill_firstname', 'Billing First Name', 'trim|required|max_length[32]');
			$this->form_validation->set_rules('bill_lastname', 'Billing Last Name', 'trim|required|max_length[32]');
			$this->form_validation->set_rules('bill_email', 'Billing Email', 'trim|required|valid_email|max_length[128]');
			$this->form_validation->set_rules('bill_phone', 'Billing Phone', 'trim|required|max_length[32]');
			$this->form_validation->set_rules('bill_company', 'Billing Company', 'trim|max_length[128]');
			$this->form_validation->set_rules('bill_address1', 'Billing Address 1', 'trim|required|max_length[128]');
			$this->form_validation->set_rules('bill_address2', 'Billing Address 2', 'trim|max_length[128]');
			$this->form_validation->set_rules('bill_city', 'Billing City', 'trim|required|max_length[128]');
			$this->form_validation->set_rules('bill_country_id', 'Billing Country', 'trim|required|numeric');
			$this->form_validation->set_rules('bill_zone_id', 'Billing State', 'trim|required|numeric');
			$this->form_validation->set_rules('bill_zip', 'Billing Zip', 'trim|required|max_length[10]');
		}
		
		$this->form_validation->set_rules('ship_address_id', 'Shipping Address ID', 'numeric');
		$this->form_validation->set_rules('ship_firstname', 'Shipping First Name', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('ship_lastname', 'Shipping Last Name', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('ship_email', 'Shipping Email', 'trim|required|valid_email|max_length[128]');
		$this->form_validation->set_rules('ship_phone', 'Shipping Phone', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('ship_company', 'Shipping Company', 'trim|max_length[128]');	
		$this->form_validation->set_rules('ship_address1', 'Shipping Address 1', 'trim|required|max_length[128]');
		$this->form_validation->set_rules('ship_address2', 'Shipping Address 2', 'trim|max_length[128]');
		$this->form_validation->set_rules('ship_city', 'Shipping City', 'trim|required|max_length[128]');
		$this->form_validation->set_rules('ship_country_id', 'Shipping Country', 'trim|required|numeric');
		$this->form_validation->set_rules('ship_zone_id', 'Shipping State', 'trim|required|numeric');
		$this->form_validation->set_rules('ship_zip', 'Shipping Zip', 'trim|required|max_length[10]');
		
		
		
		if ($this->form_validation->run())
		{
			//load any customer data to get their ID (if logged in)
			$customer				= $this->go_cart->customer();
				
			$customer['ship_to_bill_address'] = 'false';
			$customer['ship_address']['firstname']		= $this->input->post('ship_firstname');
			$customer['ship_address']['lastname']		= $this->input->post('ship_lastname');
			$customer['ship_address']['email']			= $this->input->post('ship_email');
			$customer['ship_address']['phone']			= $this->input->post('ship_phone');
			$customer['ship_address']['company']		= $this->input->post('ship_company');
			$customer['ship_address']['address1']		= $this->input->post('ship_address1');
			$customer['ship_address']['address2']		= $this->input->post('ship_address2');
			$customer['ship_address']['city']			= $this->input->post('ship_city');
			$customer['ship_address']['zip']			= $this->input->post('ship_zip');
			
			
			// get zone / country data using the zone id submitted as state
			$ship_country = $this->Location_model->get_country($this->input->post('ship_country_id'));
			$ship_zone = $this->Location_model->get_zone($this->input->post('ship_zone_id'));			

			$customer['ship_address']['zone']			= $ship_zone->code;  // save the state for output formatted addresses
			$customer['ship_address']['country']		= $ship_country->name; // some shipping libraries require country name
			$customer['ship_address']['country_code']   = $ship_country->iso_code_2; // some shipping libraries require the code 
			$customer['ship_address']['zone_id']		= $this->input->post('ship_zone_id');  // use the id's to populate address forms
			$customer['ship_address']['country_id']		= $this->input->post('ship_country_id');
			
			// Remember the chosen address ID for logged in customers, use as default in the future
			if(empty($customer['default_shipping_address']) && set_value('ship_address_id')!='')
			{
				$customer['default_shipping_address'] = set_value('ship_address_id');	
			}
			
			if($this->input->post('ship_to_bill_address')=='yes') 
			{
				$customer['ship_to_bill_address'] = 'true';
				$customer['bill_address'] = $customer['ship_address'];
				
				$customer['default_billing_address'] = @$customer['default_shipping_address'];
  			}
 			else 
 			{
 				
	 			$customer['bill_address']['firstname']		= $this->input->post('bill_firstname');
				$customer['bill_address']['lastname']		= $this->input->post('bill_lastname');
				$customer['bill_address']['email']			= $this->input->post('bill_email');
				$customer['bill_address']['phone']			= $this->input->post('bill_phone');
				$customer['bill_address']['company']		= $this->input->post('bill_company');
				$customer['bill_address']['address1']		= $this->input->post('bill_address1');
				$customer['bill_address']['address2']		= $this->input->post('bill_address2');
				$customer['bill_address']['city']			= $this->input->post('bill_city');
				$customer['bill_address']['zip']			= $this->input->post('bill_zip');
				
				
				// get zone / country data using the zone id submitted as state
				$bill_country	= $this->Location_model->get_country(set_value('bill_country_id'));
				$bill_zone		= $this->Location_model->get_zone(set_value('bill_zone_id'));

				$customer['bill_address']['zone']			= $bill_zone->code;  // save the state for output formatted addresses
				$customer['bill_address']['country']		= $bill_country->name; // some shipping libraries require country name
				$customer['bill_address']['country_code']   = $bill_country->iso_code_2; // some shipping libraries require the code 
				$customer['bill_address']['zone_id']		= $this->input->post('bill_zone_id');  // use the zone id to populate address state field value
				$customer['bill_address']['country_id']		= $this->input->post('bill_country_id');
				
				// Remember chosen ID
				if(empty($customer['default_billing_address']) && $this->input->post('bill_address_id')!='')
				{
					$customer['default_billing_address'] = $this->input->post('bill_address_id');	
				}
				
			}
			
			// for guest customers, load the billing address data as their base info as well
			if(empty($customer['id']))
			{
				$customer['company']	= $customer['bill_address']['company'];
				$customer['firstname']	= $customer['bill_address']['firstname'];
				$customer['lastname']	= $customer['bill_address']['lastname'];
				$customer['phone']		= $customer['bill_address']['phone'];
				$customer['email']		= $customer['bill_address']['email'];
			}
			
			if(!isset($customer['group_id']))
			{
				$customer['group_id'] = 1; // default group
			}
			
			// save customer details
			$this->go_cart->save_customer($customer);
			
			$return = array('status'=>'success');
			
			//customer details
			$return['view'] = $this->load->view('checkout/customer_details_static', array('customer'=>$customer), true);
			
			//shipping/payment information
			echo json_encode($return);
		}
		else
		{
			echo json_encode(array('status'=>'error', 'error'=>validation_errors()));
		}
	}
	
	// this is here for ajax use
	function get_formatted_currency()
	{
		$value = $this->input->post('value');
		echo format_currency($value);
	}
	
	function save_shipping_method()
	{
		$shipping = $this->input->post('shipping');
		$shipping = spliti(':', $shipping);
				
		$this->go_cart->set_shipping( $shipping[0], $shipping[1]);
		
		echo "1";
	}
	
	function order_summary()
	{
		$this->load->view('checkout/summary');
	}
	
	function save_payment_method()
	{
		$module = $this->input->post('module');
		
		if($module)
		{	
			$this->load->add_package_path(APPPATH.'packages/payment/'.$module.'/');
			$this->load->library($module);
			
			$check	= $this->$module->checkout_check();
			if(!$check)
			{
				$this->go_cart->set_payment($module, $this->$module->description() );
				
				echo json_encode(array('status'=>'success'));
			}
			else
			{
				// send back the errors
				echo json_encode(array('status'=>'error', 'error'=>$check));
			}
		}
	}
	
	function confirmation_contents()
	{
		$data['customer'] = $this->go_cart->customer();
		$data['shipping'] = $this->go_cart->shipping_method();
		$data['payment'] = $this->go_cart->payment_method();
		
		$this->load->view('checkout/sconfirm', $data);
	}
	
	function place_order()
	{		
		// retrieve the payment method
		$payment = $this->go_cart->payment_method();
		//die(var_dump($payment));
		
		// verify that we intend to place the order 
		if( ! $this->input->post('process_order') && !isset($payment['confirmed']))
		{
			redirect('/'); // otherwise, send them packing
		}
		
		//make sure they're logged in if the config file requires it
		if($this->config->item('require_login'))
		{
			$this->Customer_model->is_logged_in();
		}
		
		// are we processing an empty cart?
		$contents = $this->go_cart->contents();
		if(empty($contents))
		{
			redirect('cart/view_cart');
		} else {
			//double check the inventory of each item before processing the order
			$inventory_check	= $this->go_cart->check_inventory();
			if($inventory_check)
			{
				//OOPS we have an error. someone else has gotten the scoop on our customer and bought products out from under them!
				//we need to redirect them to the view cart page and let them know that the inventory is no longer there.
				$this->session->set_flashdata('error', $inventory_check);
				redirect('cart/view_cart');
			}
			
			//  - check to see if we have a payment method set, if we need one
			if(empty($payment) && $this->go_cart->total()>0)
			{
				redirect('checkout');
			}
		}
		
		// Is payment bypassed? (total is zero, or processed flag is set)
		if($this->go_cart->total() > 0 && ! isset($payment['confirmed'])) {
			
			//load the payment module
			$this->load->add_package_path(APPPATH.'packages/payment/'.$payment['module'].'/');
			$this->load->library($payment['module']);
			
			//run the payment
			$error_status	= $this->$payment['module']->process_payment();
			if($error_status !== false)
			{
				// send them back to the checkout page with the error
				$this->session->set_flashdata('error', $error_status);
				redirect('checkout');
			}
			
		}
		
		//// save the order
		$order_id = $this->go_cart->save_order();
		
		$data['order_id']			= $order_id;
		$data['shipping']			= $this->go_cart->shipping_method();
		$data['payment']			= $this->go_cart->payment_method();
		$data['customer']			= $this->go_cart->customer();
		$data['additional_details']	= $this->go_cart->additional_details();
		
		$order_downloads 			= $this->go_cart->get_order_downloads();
		
		$data['hide_menu']			= true;
		
		// run the complete payment module method once order has been saved
		if(!empty($payment))
		{
			if(method_exists($this->$payment['module'], 'complete_payment'))
			{
				$this->$payment['module']->complete_payment($data);
			}
		}
	
		// Send the user a confirmation email
		
		// - get the email template
		$this->load->model('messages_model');
		$row = $this->messages_model->get_message(7);
		
		$download_section = '';
		if( ! empty($order_downloads))
		{
			// get the download link segment to insert into our confirmations
			$downlod_msg_record = $this->messages_model->get_message(8);
			
			if(!empty($data['customer']['id']))
			{
				// they can access their downloads by logging in
				$download_section = str_replace('{download_link}', anchor('secure/my_downloads', lang('download_link')),$downlod_msg_record['content']);
			} else {
				// non regs will receive a code
				$download_section = str_replace('{download_link}', anchor('secure/my_downloads/'.$order_downloads['code'], lang('download_link')), $downlod_msg_record['content']);
			}
		}
		
		$row['content'] = html_entity_decode($row['content']);
		
		// set replacement values for subject & body
		// {customer_name}
		$row['subject'] = str_replace('{customer_name}', $data['customer']['firstname'].' '.$data['customer']['lastname'], $row['subject']);
		$row['content'] = str_replace('{customer_name}', $data['customer']['firstname'].' '.$data['customer']['lastname'], $row['content']);
		
		// {url}
		$row['subject'] = str_replace('{url}', $this->config->item('base_url'), $row['subject']);
		$row['content'] = str_replace('{url}', $this->config->item('base_url'), $row['content']);
		
		// {site_name}
		$row['subject'] = str_replace('{site_name}', $this->config->item('company_name'), $row['subject']);
		$row['content'] = str_replace('{site_name}', $this->config->item('company_name'), $row['content']);
			
		// {order_summary}
		$row['content'] = str_replace('{order_summary}', $this->load->view('order_email', $data, true), $row['content']);
		
		// {download_section}
		$row['content'] = str_replace('{download_section}', $download_section, $row['content']);
			
		$this->load->library('email');
		
		$config['mailtype'] = 'html';
		$this->email->initialize($config);

		$this->email->from($this->config->item('email'), $this->config->item('company_name'));
		
		if($this->Customer_model->is_logged_in(false, false))
		{
			$this->email->to($data['customer']['email']);
		}
		else
		{
			$this->email->to($data['customer']['ship_address']['email']);
		}
		
		//email the admin
		$this->email->bcc($this->config->item('email'));
		
		$this->email->subject($row['subject']);
		$this->email->message($row['content']);
		
		$this->email->send();
		
		$data['page_title'] = 'Thanks for shopping with '.$this->config->item('company_name');
		$data['gift_cards_enabled'] = $this->gift_cards_enabled;
		$data['download_section']	= $download_section;
		
		// show final confirmation page
		$this->load->view('order_placed', $data);
		
		//remove the cart from the session
		$this->go_cart->destroy();
	}
}