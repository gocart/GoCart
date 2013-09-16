<?php

class Login extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		
		force_ssl();
		
		$this->load->library('Auth');
		$this->lang->load('login');
	}

	function index()
	{
		//we check if they are logged in, generally this would be done in the constructor, but we want to allow customers to log out still
		//or still be able to either retrieve their password or anything else this controller may be extended to do
		$redirect	= $this->auth->is_logged_in(false, false);
		//if they are logged in, we send them back to the dashboard by default, if they are not logging in
		if ($redirect)
		{
			redirect($this->config->item('admin_folder').'/dashboard');
		}
		
		
		$this->load->helper('form');
		$data['redirect']	= $this->session->flashdata('redirect');
		$submitted 			= $this->input->post('submitted');
		if ($submitted)
		{
			$email		= $this->input->post('email');
			$password	= $this->input->post('password');
			$remember   = $this->input->post('remember');
			$redirect	= $this->input->post('redirect');
			$login		= $this->auth->login_admin($email, $password, $remember);
			if ($login)
			{
				if ($redirect == '')
				{
					$redirect = $this->config->item('admin_folder').'/dashboard';
				}
				redirect($redirect);
			}
			else
			{
				//this adds the redirect back to flash data if they provide an incorrect credentials
				$this->session->set_flashdata('redirect', $redirect);
				$this->session->set_flashdata('error', lang('error_authentication_failed'));
				redirect($this->config->item('admin_folder').'/login');
			}
		}
		$this->load->view($this->config->item('admin_folder').'/login', $data);
	}
	
	function logout()
	{
		$this->auth->logout();
		
		//when someone logs out, automatically redirect them to the login page.
		$this->session->set_flashdata('message', lang('message_logged_out'));
		redirect($this->config->item('admin_folder').'/login');
	}

}
