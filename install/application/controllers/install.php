<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Install extends CI_Controller {


	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->library('form_validation');
		$this->load->helper(array('form', 'file'));
		
		$this->form_validation->set_rules('hostname', 'Hostname', 'required');
		//$this->form_validation->set_rules('database', 'Database Name', 'required');
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_rules('prefix', 'Database Prefix', 'required');
		
		$this->form_validation->set_rules('admin_email', 'Admin Email', 'required|valid_email');
		$this->form_validation->set_rules('admin_password', 'Admin Password', 'required|min_length[5]');
		
		$this->form_validation->set_rules('company_name', 'Company Name', 'required');
		$this->form_validation->set_rules('website_email', 'Website Email', 'required|valid_email');
		$this->form_validation->set_rules('ssl_support');
		
		$this->form_validation->set_rules('address1');
		$this->form_validation->set_rules('address2');
		$this->form_validation->set_rules('city');
		$this->form_validation->set_rules('state');
		$this->form_validation->set_rules('zip');
		$this->form_validation->set_rules('country');
		
		
		if ($this->form_validation->run() == FALSE)
		{
			$data['errors']	= validation_errors();
			$this->load->view('install', $data);
		}
		else
		{
			//make sure we can connect to the database
			$config['hostname'] = $this->input->post('hostname');
			$config['username'] = $this->input->post('username');
			$config['password'] = $this->input->post('password');
			$config['database'] = $this->input->post('database');
			$config['dbdriver'] = "mysql";
			$config['dbprefix'] = "";
			$config['pconnect'] = FALSE;
			$config['db_debug'] = TRUE;
			$config['cache_on'] = FALSE;
			$config['cachedir'] = "";
			$config['char_set'] = "utf8";
			$config['dbcollat'] = "utf8_general_ci";
			$config['active_r'] = TRUE; 
			
			$db = $this->load->database($config, true);
			
			if($db)
			{	
				$this->load->model('Install_model');
				
				//open the database and run the install sql script.
				$query = $this->Install_model->get_query($this->input->post('prefix'));
				
				foreach($query as $q)
				{
					$db->query($q);
				}

				//set up the admin user
				$db->insert($this->input->post('prefix').'admin', array('access'=>'Admin', 'email'=>$this->input->post('admin_email'), 'password'=>sha1($this->input->post('admin_password') ) ) );

				//setup the database config file
				$settings					= array();
				$settings['hostname']		= $this->input->post('hostname');
				$settings['username']		= $this->input->post('username');
				$settings['password']		= $this->input->post('password');
				$settings['database']		= $this->input->post('database');
				$settings['prefix']			= $this->input->post('prefix');				
				$file_contents				= $this->load->view('templates/database', $settings, true);
				write_file($_SERVER['DOCUMENT_ROOT'].'/gocart/config/database.php', $file_contents);

				//setup the gocart config file
				$settings					= array();
				$settings['company_name']	= $this->input->post('company_name');
				$settings['address1']		= $this->input->post('address1');
				$settings['address2']		= $this->input->post('address2');
				$settings['city']			= $this->input->post('city');
				$settings['state']			= $this->input->post('state');
				$settings['country']		= $this->input->post('country');
				$settings['zip']			= $this->input->post('zip');
				$settings['email']			= $this->input->post('website_email');
				$settings['ssl_support']	= (bool)$this->input->post('ssl_support');
				$file_contents				= $this->load->view('templates/gocart', $settings, true);
				write_file($_SERVER['DOCUMENT_ROOT'].'/gocart/config/gocart.php', $file_contents);

				//setup the Code Igniter default config file
				$file_contents				= $this->load->view('templates/config', array(), true);
				write_file($_SERVER['DOCUMENT_ROOT'].'/gocart/config/config.php', $file_contents);

				//redirect to the admin login
				if($this->input->post('ssl_support'))
				{
					header( 'Location: https://'.$_SERVER['HTTP_HOST'].'/admin/login' ) ;
				}
				else
				{
					header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/admin/login' ) ;
				}
			}
			else
			{
				$data['errors']	= '<p>A connection to the database could not be established.</p>';
				
				$this->load->view('install', $data);
			}
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/install.php */