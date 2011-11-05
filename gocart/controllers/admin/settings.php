<?php

class Settings extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
		remove_ssl();
		$this->load->library('Auth');
		$this->auth->check_access('Admin', true);
		$this->load->model('Settings_model');
		$this->load->model('Messages_model');
		
		//this adds the redirect url to our flash data, incase they are not logged in
		$this->auth->is_logged_in(uri_string());
	}
	
	function index()
	{
		//we're going to handle the shipping and payment model landing page with this, basically
		
		//Payment Information
		$payment_order		= $this->Settings_model->get_settings('payment_order');
		$enabled_modules	= $this->Settings_model->get_settings('payment_modules');
		
		$data['payment_modules']	= array();
		//create a list of available payment modules
		if ($handle = opendir(APPPATH.'/libraries/payment/')) {
			while (false !== ($file = readdir($handle)))
			{
				//now we eliminate the periods from the list.
				if (!strstr($file, '.'))
				{
					//also, set whether or not they are installed according to our payment settings
					if(array_key_exists($file, $enabled_modules))
					{
						$data['payment_modules'][$file]	= true;
					}
					else
					{
						$data['payment_modules'][$file]	= false;
					}
				}
			}
			closedir($handle);
		}
		//now time to do it again with shipping
		$shipping_order		= $this->Settings_model->get_settings('shipping_order');
		$enabled_modules	= $this->Settings_model->get_settings('shipping_modules');
		
		$data['shipping_modules']	= array();
		//create a list of available shipping modules
		if ($handle = opendir(APPPATH.'/libraries/shipping/')) {
			while (false !== ($file = readdir($handle)))
			{
				//now we eliminate anything with periods
				if (!strstr($file, '.'))
				{
					//also, set whether or not they are installed according to our shipping settings
					if(array_key_exists($file, $enabled_modules))
					{
						$data['shipping_modules'][$file]	= true;
					}
					else
					{
						$data['shipping_modules'][$file]	= false;
					}
				}
			}
			closedir($handle);
		}
		
		$data['canned_messages'] = $this->Messages_model->get_list();
		
		$data['page_title']	= 'Settings';
		$this->load->view($this->config->item('admin_folder').'/settings', $data);
	}
	
	function canned_message_form($id=false)
	{
		$data['page_title'] = 'Add Canned Message';

		$data['id']			= $id;
		$data['name']		= '';
		$data['subject']	= '';
		$data['content']	= '';
		$data['deletable']	= 1;
		
		if($id)
		{
			$data['page_title'] = 'Edit Canned Message';
			$message = $this->Messages_model->get_message($id);
						
			$data['name']		= $message['name'];
			$data['subject']	= $message['subject'];
			$data['content']	= $message['content'];
			$data['deletable']	= $message['deletable'];
		}
		
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('name', 'Message name', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('subject', 'Subject', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('content', 'Message Content', 'trim|required');
		
		if ($this->form_validation->run() == FALSE)
		{
			$data['errors'] = validation_errors();
			
			$this->load->view($this->config->item('admin_folder').'/canned_message_form', $data);
		}
		else
		{
			
			$save['id']			= $id;
			$save['name']		= $this->input->post('name');
			$save['subject']	= $this->input->post('subject');
			$save['content']	= $this->input->post('content');
			
			$this->Messages_model->save_message($save);
			
			$this->session->set_flashdata('message', 'Your message has been saved.');
			redirect($this->config->item('admin_folder').'/settings');
		}
	}
	
	function delete_message($id)
	{
		$this->Messages_model->delete_message($id);
		redirect($this->config->item('admin_folder').'/settings');
	}
}