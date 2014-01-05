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
            $migration = $this->migration->version($version);
            if(!$migration)
            {
                echo $this->migration->error_string();
            }
            else
            {
                echo 'Migration(s) done'.PHP_EOL;
            }
     }
 }