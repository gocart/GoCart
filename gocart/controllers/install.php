<?php

Class Install extends CI_Controller {

    function __construct()
    {
        parent::__construct();

        //load in some helpers
        $this->load->helper(array('form', 'file', 'url'));

        //if this system is already installed redirect to the homepage
        if(file_exists(FCPATH.'gocart/config/database.php'))
        {
            redirect('/');
        }

        $this->load->library('form_validation');
    }

    function index()
    {
        //build our checks
        $data = array();

        //check for writable folders
        $data['is_writeable']['root'] = is_writeable(FCPATH);
        $data['is_writeable']['config'] = is_writeable(FCPATH.'gocart/config/');
        $data['is_writeable']['uploads'] = is_writeable(FCPATH.'uploads/');

        $this->form_validation->set_rules('hostname', 'Hostname', 'required');
        $this->form_validation->set_rules('database', 'Database Name', 'required');
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'trim');
        $this->form_validation->set_rules('prefix', 'Database Prefix', 'trim');

        $this->form_validation->set_rules('ssl_support');
        $this->form_validation->set_rules('mod_rewrite');

        if ($this->form_validation->run() == FALSE)
        {
            $data['errors'] = validation_errors();
            $this->load->view('install', $data);
        }
        else
        {
            // Unset any existing DB information
            unset($this->db);

            //generate a dsn string
            $dsn = 'mysqli://'.$this->input->post('username').':'.$this->input->post('password').'@'.$this->input->post('hostname').'/'.$this->input->post('database');

            //connect!
            $this->load->database($dsn);

            if (is_resource($this->db->conn_id) OR is_object($this->db->conn_id))
            {
                //setup the database config file
                $settings                   = array();
                $settings['hostname']       = $this->input->post('hostname');
                $settings['username']       = $this->input->post('username');
                $settings['password']       = $this->input->post('password');
                $settings['database']       = $this->input->post('database');
                $settings['prefix']         = $this->input->post('prefix');             
                $file_contents              = $this->load->view('templates/database', $settings, true);
                write_file(FCPATH.'gocart/config/database.php', $file_contents);

                //setup the CodeIgniter default config file
                $config_index               = array('index'=>'index.php');
                if($this->input->post('mod_rewrite'))
                {
                    $config_index           = array('index'=>'');
                }
                $file_contents              = $this->load->view('templates/config', $config_index, true);
                write_file(FCPATH.'gocart/config/config.php', $file_contents);
                
                //setup the .htaccess file
                if($this->input->post('mod_rewrite'))
                {
                    $subfolder = trim(str_replace($_SERVER['DOCUMENT_ROOT'], '', FCPATH), '/').'/';
                    $file_contents              = $this->load->view('templates/htaccess', array('subfolder'=>$subfolder), true);
                    write_file(FCPATH.'.htaccess', $file_contents);
                }

                //redirect to the admin login
                redirect('admin');
            }
            else
            {
                $data['errors'] = '<p>A connection to the database could not be established.</p>';
                $this->load->view('install', $data);
            }
        }
    }

}