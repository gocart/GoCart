<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Upload extends CI_Upload{
    
    function __construct($props = array())
    {
        parent::__construct($props);
    }
    
    function is_allowed_filetype()
    {
        return true;
    }
} 
