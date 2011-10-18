<?php

class Customers extends CI_Controller {

	//this is used when editing or adding a customer
	var $customer_id	= false;	

	function __construct()
	{		
		parent::__construct();
		$this->load->library('Auth');
		$this->load->model(array('Customer_model', 'Location_model'));
		$this->load->helper('formatting_helper');
		//this adds the redirect url to our flash data, incase they are not logged in
		$this->auth->is_logged_in($_SERVER['REQUEST_URI']);
	}
	
	function index($field='lastname', $by='ASC', $page=0)
	{
		//we're going to use flash data and redirect() after form submissions to stop people from refreshing and duplicating submissions
		//$this->session->set_flashdata('message', 'this is our message');
		
		$data['page_title']	= 'Customers';
		$data['customers']	= $this->Customer_model->get_customers(50,$page, $field, $by);
		
		$this->load->library('pagination');

		$config['base_url']		= base_url().'/'.$this->config->item('admin_folder').'/customers/index/'.$field.'/'.$by.'/';
		$config['total_rows']	= $this->Customer_model->count_customers();
		$config['per_page']		= 50;
		$config['uri_segment']	= 6;

		$this->pagination->initialize($config);
		
		
		$data['page']	= $page;
		$data['field']	= $field;
		$data['by']		= $by;
		
		$this->load->view($this->config->item('admin_folder').'/customers', $data);
	}
	
	function export_xml()
	{
		$this->load->helper('download_helper');
		
		$data['customers'] = (array)$this->Customer_model->get_customers();
		

		force_download('users.xml', $this->load->view($this->config->item('admin_folder').'/customers_xml', $data, true));
		
		//$this->load->view($this->config->item('admin_folder').'/customers_xml', $data);
	}

	function form($id = false)
	{
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		$data['page_title']		= 'Add Customer';
		
		//default values are empty if the customer is new
		$data['id']		= '';
		$data['group_id'] = '';
		$data['firstname']	= '';
		$data['lastname']	= '';
		$data['email']		= '';
		$data['phone']		= '';
		$data['company']	= '';
		$data['email_subscribe'] = '';
		$data['active']		= false;
				
		// get group list
		$groups = $this->Customer_model->get_groups();
		$group_list[0]='';
		foreach($groups as $group)
		{
			$group_list[$group['id']] = $group['name'];
		}
		$data['group_list'] = $group_list;
		
		
		
		if ($id)
		{	
			$this->customer_id	= $id;
			$customer		= $this->Customer_model->get_customer($id);
			//if the customer does not exist, redirect them to the customer list with an error
			if (!$customer)
			{
				$this->session->set_flashdata('message', 'The requested customer could not be found.');
				redirect($this->config->item('admin_folder').'/customers');
			}
			
			//set title to edit if we have an ID
			$data['page_title']	= 'Edit Customer';
			
			//set values to db values
			$data['id']		= $customer->id;
			$data['group_id'] = $customer->group_id;
			$data['firstname']	= $customer->firstname;
			$data['lastname']	= $customer->lastname;
			$data['email']		= $customer->email;
			$data['phone']		= $customer->phone;
			$data['company']	= $customer->company;
			$data['active']		= $customer->active;
			$data['email_subscribe'] = $customer->email_subscribe;
			
		}
		
		$this->form_validation->set_rules('firstname', 'First Name', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('lastname', 'Last Name', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[128]|callback_check_email');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('company', 'Company', 'trim|max_length[128]');
		$this->form_validation->set_rules('active', 'Active');
		$this->form_validation->set_rules('group_id', 'numeric');
		$this->form_validation->set_rules('email_subscribe', 'numeric|max_length[1]');
		
		//if this is a new account require a password, or if they have entered either a password or a password confirmation
		if ($this->input->post('password') != '' || $this->input->post('confirm') != '' || !$id)
		{
			$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|sha1');
			$this->form_validation->set_rules('confirm', 'Confirm Password', 'required|matches[password]');
		}
		
				
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view($this->config->item('admin_folder').'/customer_form', $data);
		}
		else
		{
			$save['id']		= $id;
			$save['group_id'] 	= $this->input->post('group_id');
			$save['firstname']	= $this->input->post('firstname');
			$save['lastname']	= $this->input->post('lastname');
			$save['email']		= $this->input->post('email');
			$save['phone']		= $this->input->post('phone');
			$save['company']	= $this->input->post('company');
			$save['active']		= $this->input->post('active');
			$save['email_subscribe'] = $this->input->post('email_subscribe');

			
			if ($this->input->post('password') != '' || !$id)
			{
				$save['password']	= $this->input->post('password');
			}
			
			$this->Customer_model->save($save);
			
			if (!$id)
			{
				$this->session->set_flashdata('message', $this->input->post('firstname').' '.$this->input->post('lastname').' has been added.');
			}
			else
			{
				$this->session->set_flashdata('message', $this->input->post('firstname').' '.$this->input->post('lastname').'\'s information has been updated.');
			}
			
			//go back to the customer list
			redirect($this->config->item('admin_folder').'/customers');
		}
	}
	
	function addresses($id = false)
	{
		$data['customer']		= $this->Customer_model->get_customer($id);

		//if the customer does not exist, redirect them to the customer list with an error
		if (!$data['customer'])
		{
			$this->session->set_flashdata('message', 'The requested customer could not be found.');
			redirect($this->config->item('admin_folder').'/customers');
		}
		
		$data['addresses'] = $this->Customer_model->get_address_list($id);
		
		//set title to edit if we have an ID
		$data['page_title']	= $data['customer']->lastname.', '.$data['customer']->firstname.' Addresses';
		
		$this->load->view($this->config->item('admin_folder').'/customer_addresses', $data);
	}
	
	function delete($id = false)
	{
		if ($id)
		{	
			$customer	= $this->Customer_model->get_customer($id);
			//if the customer does not exist, redirect them to the customer list with an error
			if (!$customer)
			{
				$this->session->set_flashdata('message', 'The requested customer could not be found.');
				redirect($this->config->item('admin_folder').'/customers');
			}
			else
			{
				//if the customer is legit, delete them
				$delete	= $this->Customer_model->delete($id);
				
				$this->session->set_flashdata('message', $customer->firstname.' '.$customer->lastname.' has been deleted from the system.');
				redirect($this->config->item('admin_folder').'/customers');
			}
		}
		else
		{
			//if they do not provide an id send them to the customer list page with an error
			$this->session->set_flashdata('message', 'The requested customer could not be found.');
			redirect($this->config->item('admin_folder').'/customers');
		}
	}
	
	//this is a callback to make sure that customers are not sharing an email address
	function check_email($str)
	{
		$email = $this->Customer_model->check_email($str, $this->customer_id);
        	if ($email)
        	{
			$this->form_validation->set_message('check_email', 'The requested email is already in use.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	function order_list($status = false)
	{
		//we're going to use flash data and redirect() after form submissions to stop people from refreshing and duplicating submissions
		$this->load->model('Order_model');
		
		$data['page_title']	= 'Order List';
		$data['orders']		= $this->Order_model->get_orders($status);
		
		$this->load->view($this->config->item('admin_folder').'/order_list', $data);
	}
	
	
	// download email blast list (subscribers)
	function get_subscriber_list()
	{
		$subscribers = $this->Customer_model->get_subscribers();
		
		$sub_list = '';
		foreach($subscribers as $subscriber)
		{
			$sub_list .= $subscriber['email'].",\n";
		}
		
		// start output
		header('Content-Type: "text/csv"');
		header('Content-Disposition: attachment; filename="email_subscribers_list.csv"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header("Content-Transfer-Encoding: binary");
		header('Pragma: public');
		
		echo $sub_list;
	}	
	
	////  customer groups
	
	function groups()
	{
		$data['groups'] = $this->Customer_model->get_groups();
		$data['page_title'] = 'Customer Groups';
		
		$this->load->view($this->config->item('admin_folder').'/customer_groups', $data);
	}
	
	function edit_group($id=0)
	{
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		$data['page_title']		= 'Add Customer Group';
		
		//default values are empty if the customer is new
		$data['id']				= '';
		$data['name']   		= '';
		$data['discount']		= '';
		$data['discount_type'] 	= '';
		
		if($id)
		{
			$group = $this->Customer_model->get_group($id);
			
			$data['id']				= $group->id;
			$data['name']   		= $group->name;
			$data['discount']		= $group->discount;
			$data['discount_type'] 	= $group->discount_type;
			
			$data['page_title'] = 'Edit Customer Group';
		}
		
		$this->form_validation->set_rules('name', 'Group Name', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('discount', 'Discount', 'trim|required|numeric');
		$this->form_validation->set_rules('discount_type', 'Discount Type', 'trim|required');

		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view($this->config->item('admin_folder').'/customer_group_form', $data);
		}
		else
		{
			
			if($id)
			{
				$save['id'] = $id;
			}
			
			$save['name'] 		= set_value('name');
			$save['discount'] 	= set_value('discount');
			$save['discount_type'] = set_value('discount_type');
			
			$this->Customer_model->save_group($save);
			
			if (!$id)
			{
				$this->session->set_flashdata('message', 'The group "'.set_value('name').'" has been added.');
			}
			else
			{
				$this->session->set_flashdata('message', 'The group "'.set_value('name').'" has been updated.');
			}
			
			//go back to the customer group list
			redirect($this->config->item('admin_folder').'/customers/groups');
		
		}
		
	}
	
	
	function get_group()
	{
		$id = $this->input->post('id');
		
		if(empty($id)) return;
		
		echo json_encode($this->Customer_model->get_group($id));
	}
	
	
	function delete_group($id)
	{
		
		if(empty($id)) return;
		
		$this->Customer_model->delete_group($id);
		
		//go back to the customer list
		redirect($this->config->item('admin_folder').'/customers/groups');
	}
	
	function address_list($customer_id)
	{
		$data['address_list'] = $this->Customer_model->get_address_list($customer_id);
		
		$this->load->view($this->config->item('admin_folder').'/address_list', $data);
	}
	
	function address_form($customer_id, $id = false)
	{
		$data['id']				= $id;
		$data['company']		= '';
		$data['firstname']		= '';
		$data['lastname']		= '';
		$data['email']			= '';
		$data['phone']			= '';
		$data['address1']		= '';
		$data['address2']		= '';
		$data['city']			= '';
		$data['country_id']		= '';
		$data['zone_id']		= '';
		$data['zip']			= '';
		
		$data['customer_id']	= $customer_id;
		
		$data['page_title']		= 'Add Address';
		//get the countries list for the dropdown
		$data['countries_menu']	= $this->Location_model->get_countries_menu();
		
		if($id)
		{
			$data['page_title']	= 'Edit Address';
			$address			= $this->Customer_model->get_address($id);
			
			//fully escape the address
			form_decode($address);
			
			//merge the array
			$data				= array_merge($data, $address);
			
			$data['zones_menu']	= $this->Location_model->get_zones_menu($data['country_id']);
		}
		else
		{
			//if there is no set ID, the get the zones of the first country in the countries menu
			$data['zones_menu']	= $this->Location_model->get_zones_menu(array_shift(array_keys($data['countries_menu'])));
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('company', 'Company', 'trim|max_length[128]');
		$this->form_validation->set_rules('firstname', 'First Name', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('lastname', 'Last Name', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[128]');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('address1', 'Address', 'trim|required|max_length[128]');
		$this->form_validation->set_rules('address2', 'Address', 'trim|max_length[128]');
		$this->form_validation->set_rules('city', 'City', 'trim|required');
		$this->form_validation->set_rules('country_id', 'Country', 'trim|required');
		$this->form_validation->set_rules('zone_id', 'State', 'trim|required');
		$this->form_validation->set_rules('zip', 'Zip', 'trim|required|max_length[32]');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view($this->config->item('admin_folder').'/customer_address_form', $data);
		}
		else
		{
			
			$a['customer_id']				= $customer_id; // this is needed for new records
			$a['id']						= (empty($id))?'':$id;
			$a['field_data']['company']		= $this->input->post('company');
			$a['field_data']['firstname']	= $this->input->post('firstname');
			$a['field_data']['lastname']	= $this->input->post('lastname');
			$a['field_data']['email']		= $this->input->post('email');
			$a['field_data']['phone']		= $this->input->post('phone');
			$a['field_data']['address1']	= $this->input->post('address1');
			$a['field_data']['address2']	= $this->input->post('address2');
			$a['field_data']['city']		= $this->input->post('city');
			$a['field_data']['zip']			= $this->input->post('zip');
			
			
			$a['field_data']['zone_id']		= $this->input->post('zone_id');
			$a['field_data']['country_id']	= $this->input->post('country_id');
			
			$country	= $this->Location_model->get_country($this->input->post('country_id'));
			$zone		= $this->Location_model->get_zone($this->input->post('zone_id'));
			
			$a['field_data']['zone']			= $zone->code;  // save the state for output formatted addresses
			$a['field_data']['country']			= $country->name; // some shipping libraries require country name
			$a['field_data']['country_code']	= $country->iso_code_2; // some shipping libraries require the code 
			
			$this->Customer_model->save_address($a);
			$this->session->set_flashdata('message', 'Your address has been saved!');
			
			redirect($this->config->item('admin_folder').'/customers/addresses/'.$customer_id);
		}
	}
	
	
	function delete_address($customer_id = false, $id = false)
	{
		if ($id)
		{	
			$address	= $this->Customer_model->get_address($id);
			//if the customer does not exist, redirect them to the customer list with an error
			if (!$address)
			{
				$this->session->set_flashdata('message', 'The requested address could not be found.');
				
				if($customer_id)
				{
					redirect($this->config->item('admin_folder').'/customers/addresses/'.$customer_id);
				}
				else
				{
					redirect($this->config->item('admin_folder').'/customers');
				}
				
			}
			else
			{
				//if the customer is legit, delete them
				$delete	= $this->Customer_model->delete_address($id, $customer_id);
				
				$this->session->set_flashdata('message', 'An address has been deleted from the system.');
				if($customer_id)
				{
					redirect($this->config->item('admin_folder').'/customers/addresses/'.$customer_id);
				}
				else
				{
					redirect($this->config->item('admin_folder').'/customers');
				}
			}
		}
		else
		{
			//if they do not provide an id send them to the customer list page with an error
			$this->session->set_flashdata('message', 'The requested address could not be found.');
			
			if($customer_id)
			{
				redirect($this->config->item('admin_folder').'/customers/addresses/'.$customer_id);
			}
			else
			{
				redirect($this->config->item('admin_folder').'/customers');
			}
		}
	}
	
}