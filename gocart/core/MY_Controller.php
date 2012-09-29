<?php
class Front_Controller extends CI_Controller {
	
	//we collect the categories automatically with each load rather than for each function
	//this just cuts the codebase down a bit
	var $categories	= '';
	
	//load all the pages into this variable so we can call it from all the methods
	var $pages = '';
	
	// determine whether to display gift card link on all cart pages
	//  This is Not the place to enable gift cards. It is a setting that is loaded during instantiation.
	var $gift_cards_enabled;
	
	function __construct(){
		
		parent::__construct();

		//load GoCart library
		$this->load->library('Go_cart');
		
		// load the template config file and library
		$this->load->config('template');
		$this->load->library('Template');

		//load needed models
		$this->load->model(array('Page_model', 'Product_model', 'Digital_Product_model', 'Gift_card_model', 'Option_model', 'Order_model', 'Settings_model'));
		
		//load helpers
		$this->load->helper(array('form_helper', 'formatting_helper'));
		
		//fill in our variables
		$this->categories	= $this->Category_model->get_categories_tierd(0);
		$this->pages		= $this->Page_model->get_pages();
		
		// check if giftcards are enabled
		$gc_setting = $this->Settings_model->get_settings('gift_cards');
		if(!empty($gc_setting['enabled']) && $gc_setting['enabled']==1)
		{
			$this->gift_cards_enabled = true;
		}			
		else
		{
			$this->gift_cards_enabled = false;
		}
		
		// create partials for the header and footer
		$this->template->set_partial('header', 'header');
		$this->template->set_partial('footer', 'footer');

		//load the theme package
//		$this->load->add_package_path(APPPATH.'themes/'.$this->config->item('theme').'/');
	}
	
	
}

class Admin_Controller extends CI_Controller 
{
	function __construct()
	{
		
		parent::__construct();
		
		$this->load->library('auth');
		$this->auth->is_logged_in(uri_string());
		
		//load the base language file
		$this->lang->load('admin_common');
		$this->lang->load('goedit');
	}
}