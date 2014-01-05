<?php

class Payment extends Admin_Controller {
	
	function __construct()
	{
		parent::__construct();

		$this->auth->check_access('Admin', true);
		$this->load->model('Settings_model');

		$this->lang->load('settings');
		$this->load->helper('inflector');
	}
	
	function index()
	{
		//Payment Information
        $payment_order = $this->Settings_model->get_settings('payment_order');
        $enabled_modules = $this->Settings_model->get_settings('payment_modules');

        $data['payment_modules']    = array();
        //create a list of available payment modules
        if ($handle = opendir(APPPATH.'packages/payment/')) {
            while (false !== ($file = readdir($handle)))
            {
                //now we eliminate the periods from the list.
                if (!strstr($file, '.'))
                {
                    //also, set whether or not they are installed according to our payment settings
                    if(array_key_exists($file, $enabled_modules))
                    {
                        $data['payment_modules'][$file] = true;
                    }
                    else
                    {
                        $data['payment_modules'][$file] = false;
                    }
                }
            }
            closedir($handle);
        }

        $data['page_title'] = lang('common_payment_modules');
        $this->view($this->config->item('admin_folder').'/payment_modules', $data);
	}
	
	function install($module)
	{
		$this->load->add_package_path(APPPATH.'packages/payment/'.$module.'/');
		
		$enabled_modules	= $this->Settings_model->get_settings('payment_modules');
		
		$this->load->library($module);
		
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
		redirect($this->config->item('admin_folder').'/payment');
	}
	
	//this is an alias of install
	function uninstall($module)
	{
		$this->install($module);
	}
	
	function settings($module)
	{
		$this->load->add_package_path(APPPATH.'packages/payment/'.$module.'/');
		$this->load->library($module);
		$this->load->helper('form');
		
		//ok, in order for the most flexibility, and in case someone wants to use javascript or something
		//the form gets pulled directly from the library.
	
		if(count($_POST) >0)
		{
			$check	= $this->$module->check();
			if(!$check)
			{
				$this->session->set_flashdata('message', sprintf(lang('settings_updated'), $module));
				redirect($this->config->item('admin_folder').'/payment');
			}
			else
			{
				//set the error data and form data in the flashdata
				$this->session->set_flashdata('message', $check);
				$this->session->set_flashdata('post', $_POST);
				redirect($this->config->item('admin_folder').'/payment/settings/'.$module);
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
		$data['page_title']	= sprintf(lang('payment_settings'), humanize($module));
		$this->view($this->config->item('admin_folder').'/payment_module_settings', $data);
	}
}
