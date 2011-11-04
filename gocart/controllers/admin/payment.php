<?php

class Payment extends MY_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('Auth');
		$this->auth->check_access('Admin', true);
		$this->load->model('Settings_model');
		//this adds the redirect url to our flash data, incase they are not logged in
		$this->auth->is_logged_in($_SERVER['REQUEST_URI']);
	}
	
	function index()
	{
		secure_redirect($this->config->item('admin_folder').'/settings');
	}
	
	function install($module)
	{
		$enabled_modules	= $this->Settings_model->get_settings('payment_modules');
		
		$this->load->library('payment/'.$module.'/'.$module);
		
		if(!array_key_exists($module, $enabled_modules))
		{
			$this->Settings_model->save_settings('payment_modules', array($module=>false));
			
			//run install script
			$this->$module->install();
		}
		else
		{
			$this->Settings_model->delete_setting('payment_modules', $module);
			$this->$module->uninstall();
		}
		secure_redirect($this->config->item('admin_folder').'/payment');
	}
	
	//this is an alias of install
	function uninstall($module)
	{
		$this->install($module);
	}
	
	function settings($module)
	{
		$this->load->library('payment/'.$module.'/'.$module);
		//ok, in order for the most flexibility, and in case someone wants to use javascript or something
		//the form gets pulled directly from the library.
	
		if(count($_POST) >0)
		{
			$check	= $this->$module->check();
			if(!$check)
			{
				$this->session->set_flashdata('message', $module.' settings have been updated');
				secure_redirect($this->config->item('admin_folder').'/payment');
			}
			else
			{
				//set the error data and form data in the flashdata
				$this->session->set_flashdata('message', $check);
				$this->session->set_flashdata('post', $_POST);
				secure_redirect($this->config->item('admin_folder').'/payment/settings/'.$module);
			}
		}
		elseif($this->session->flashdata('post'))
		{
			$data['form']		= $this->$module->form($this->session->flashdata('post'));
		}
		else
		{
			$data['form']		= $this->$module->form();
		}
		$data['module']		= $module;
		$data['page_title']	= '"'.$module.'" Payment Settings';
		$this->load->view($this->config->item('admin_folder').'/payment_module_settings', $data);
	}
}
