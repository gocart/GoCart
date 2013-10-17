<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *  Migration Controller (direct access only)
 *
 *  Use to choose migration version /admin/migrate/version/#
 */

class Migrate extends Admin_Controller{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('migration');
    }

     public function version($version)
     {
        // CLI is not working very well with our routing setup + bug in CI
        // if($this->input->is_cli_request())
         //{
            $migration = $this->migration->version($version);
            if(!$migration)
            {
                echo $this->migration->error_string();
            }
            else
            {
                echo 'Migration(s) done'.PHP_EOL;
            }
       /* 
       }
        else
        {
            show_error('You don\'t have permission for this action');;
        }
        */
     }
 }