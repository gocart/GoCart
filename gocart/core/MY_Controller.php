<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * @author    : Mhd Zaher Ghaibeh
 * @company   : Creative Web Group Syria
 * @link      : http://www.cretaivewebgroup-sy.com
 * @email     : info@creativewebgroup-sy.com
 * @date      : Nov 04, 2011
 * @copyright :	Copyright (c) 2011 , Creative Web Group Syria, Inc.
 * @version   :	Version 1.0
 * @filename  : MY_Controller.php
 */


class MY_Controller extends CI_Controller{
    function __construct() {
        parent::__construct();
        $this->load->spark('assets/0.6.3');  
    }
}