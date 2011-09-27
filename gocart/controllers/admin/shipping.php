<?php

class Shipping extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('Auth');
		$this->auth->check_access('Admin', true);
		//$this->load->helper('Shipping');
		$this->load->model('Settings_model');
		//this adds the redirect url to our flash data, incase they are not logged in
		$this->auth->is_logged_in($_SERVER['REQUEST_URI']);
	}
	
	function index()
	{
		redirect($this->config->item('admin_folder').'/settings');
	}
	
	function install($module)
	{
		$enabled_modules	= $this->Settings_model->get_settings('shipping_modules');
		
		$this->load->library('shipping/'.$module.'/'.$module);
		
		if(!array_key_exists($module, $enabled_modules))
		{
			$this->Settings_model->save_settings('shipping_modules', array($module=>false));
			
			//run install script
			$this->$module->install();
		}
		else
		{
			$this->Settings_model->delete_setting('shipping_modules', $module);
			$this->$module->uninstall();
		}
		redirect($this->config->item('admin_folder').'/shipping');
	}
	
	//this is an alias of install
	function uninstall($module)
	{
		$this->install($module);
	}
	
	function settings($module)
	{
		$this->load->library('shipping/'.$module.'/'.$module);
		//ok, in order for the most flexibility, and in case someone wants to use javascript or something
		//the form gets pulled directly from the library.
	
		if(count($_POST) >0)
		{
			$check	= $this->$module->check();
			if(!$check)
			{
				$this->session->set_flashdata('message', $module.' settings have been updated');
				redirect($this->config->item('admin_folder').'/shipping');
			}
			else
			{
				//set the error data and form data in the flashdata
				$this->session->set_flashdata('message', $check);
				$this->session->set_flashdata('post', $_POST);
				redirect($this->config->item('admin_folder').'/shipping/settings/'.$module);
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
		$data['page_title']	= '"'.$module.'" Shipping Settings';
		$this->load->view($this->config->item('admin_folder').'/shipping_module_settings', $data);
	}
}
