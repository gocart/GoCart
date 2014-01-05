<?php
	
class Banners {
	
	var $CI;
	
	function __construct()
	{
		$this->CI =& get_instance();
		
		$this->CI->load->model('banner_model');
	}
	
	function show_collection($banner_collection_id, $quantity=5, $theme='default')
	{
		$data['id']			= $banner_collection_id;
		$data['banners']	= $this->CI->banner_model->banner_collection_banners($banner_collection_id, true, $quantity);
		$this->CI->load->view('banners/'.$theme, $data);
	}
	
}