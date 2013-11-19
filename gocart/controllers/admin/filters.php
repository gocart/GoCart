<?php

class filters extends Admin_Controller {	
	
	// For slug validation
	// slugs are not going in as routes for filters
	var $tmp_id;
	
	function __construct()
	{		
		parent::__construct();

		$this->auth->check_access('Admin', true);
		$this->lang->load('filter');
		$this->load->model('filter_model');
	}
	
	function index()
	{
		//we're going to use flash data and redirect() after form submissions to stop people from refreshing and duplicating submissions
		//$this->session->set_flashdata('message', 'this is our message');
		
		$data['page_title']	= lang('filters');
		$data['filters']	= $this->filter_model->get_filters_tierd();
		
		$this->view($this->config->item('admin_folder').'/filters', $data);
	}
	
	//basic filter search
	function filter_autocomplete()
	{
		$name	= trim($this->input->post('name'));
		$limit	= $this->input->post('limit');
		
		if(empty($name))
		{
			echo json_encode(array());
		}
		else
		{
			$results	= $this->filter_model->filter_autocomplete($name, $limit);
			
			$return		= array();
			foreach($results as $r)
			{
				$return[$r->id]	= $r->name;
			}
			echo json_encode($return);
		}
		
	}
	
	function organize($id = false)
	{
		$this->load->helper('form');
		$this->load->helper('formatting');
		
		if (!$id)
		{
			$this->session->set_flashdata('error', lang('error_must_select'));
			redirect($this->config->item('admin_folder').'/filters');
		}
		
		$data['filter']		= $this->filter_model->get_filter($id);
		//if the filter does not exist, redirect them to the filter list with an error
		if (!$data['filter'])
		{
			$this->session->set_flashdata('error', lang('error_not_found'));
			redirect($this->config->item('admin_folder').'/filters');
		}
			
		$data['page_title']		= sprintf(lang('organize_filter'), $data['filter']->name);
		
		$data['filter_products']	= $this->filter_model->get_filter_products_admin($id);
		
		$this->view($this->config->item('admin_folder').'/organize_filter', $data);
	}
	
	function process_organization($id)
	{
		$products	= $this->input->post('product');
		$this->filter_model->organize_contents($id, $products);
	}
	
	function form($id = false)
	{
		$this->tmp_id = $id;

		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		
		$data['filters']		= $this->filter_model->get_filters();
		$data['page_title']		= lang('filter_form');
		
		//default values are empty if the customer is new
		$data['id']				= '';
		$data['name']			= '';
		$data['slug']			= '';
		$data['seo_title']		= '';
		$data['meta']			= '';
		$data['parent_id']		= 0;
		$data['error']			= '';
		
		if ($id)
		{	
			$filter		= $this->filter_model->get_filter($id);

			//if the filter does not exist, redirect them to the filter list with an error
			if (!$filter)
			{
				$this->session->set_flashdata('error', lang('error_not_found'));
				redirect($this->config->item('admin_folder').'/filters');
			}
			
			//helps us with the slug generation
			$this->filter_name	= $this->input->post('slug', $filter->slug);
			
			//set values to db values
			$data['id']				= $filter->id;
			$data['name']			= $filter->name;
			$data['slug']			= $filter->slug;
			$data['parent_id']		= $filter->parent_id;

			//$data['seo_title']		= $filter->seo_title;
			//$data['meta']			= $filter->meta;
			
		}
		
		$this->form_validation->set_rules('name', 'lang:name', 'trim|required|max_length[64]');
		$this->form_validation->set_rules('slug', 'lang:slug', 'trim|callback_check_slug');
		$this->form_validation->set_rules('parent_id', 'parent_id', 'trim');
		//$this->form_validation->set_rules('seo_title', 'lang:seo_title', 'trim');
		//$this->form_validation->set_rules('meta', 'lang:meta', 'trim');
		
		
		// validate the form
		if ($this->form_validation->run() == FALSE)
		{
			$this->view($this->config->item('admin_folder').'/filter_form', $data);
		}
		else
		{
				
			$this->load->helper('text');
			
			//first check the slug field
			$slug = $this->input->post('slug');
			
			//if it's empty assign the name field
			if(empty($slug) || $slug=='')
			{
				$slug = $this->input->post('name');
			}
			
			$slug	= url_title(convert_accented_characters($slug), 'dash', TRUE);
			
			//validate the slug
			/*
			$this->load->model('Routes_model');
			if($id)
			{
				$slug	= $this->Routes_model->validate_slug($slug, $filter->route_id);
				$route_id	= $filter->route_id;
			}
			else
			{
				$slug	= $this->Routes_model->validate_slug($slug);
		
				$route['slug']	= $slug;	
				$route_id	= $this->Routes_model->save($route);
			}
			*/
			
			$save['id']				= $id;
			$save['name']			= $this->input->post('name');

			$save['parent_id']		= intval($this->input->post('parent_id'));

			//$save['seo_title']		= $this->input->post('seo_title');
			//$save['meta']			= $this->input->post('meta');

			//$save['route_id']		= intval($route_id);
			$save['slug']			= $slug;
			
			$filter_id	= $this->filter_model->save($save);
			
			//save the route
			//$route['id']	= $route_id;
			//$route['slug']	= $slug;
			//$route['route']	= 'cart/filter/'.$filter_id.'';
			
		//	$this->Routes_model->save($route);
			
			$this->session->set_flashdata('message', lang('message_filter_saved'));
			
			//go back to the filter list
			redirect($this->config->item('admin_folder').'/filters');
		}
	}

	function delete($id)
	{
		
		$filter	= $this->filter_model->get_filter($id);
		//if the filter does not exist, redirect them to the customer list with an error
		if ($filter)
		{
			$this->load->model('Routes_model');
			
			$this->Routes_model->delete($filter->route_id);
			$this->filter_model->delete($id);
			
			$this->session->set_flashdata('message', lang('message_delete_filter'));
			redirect($this->config->item('admin_folder').'/filters');
		}
		else
		{
			$this->session->set_flashdata('error', lang('error_not_found'));
		}
	}
	
	function check_slug($slug)
	{
		if($this->tmp_id)
		{
			$this->db->where('id !=',$this->tmp_id);
		}
		
		$rec = $this->db->where('slug',$slug)->get('filters')->result();
		if(count($rec)>0)
		{
			$this->form_validation->set_message('check_slug', lang('slug_exists'));
			return false;
		} else {
			return true;
		}
	}
}