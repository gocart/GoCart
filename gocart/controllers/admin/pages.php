<?php
class Pages extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		
		remove_ssl();
		$this->load->library('Auth');

		$this->auth->is_logged_in(uri_string());
		$this->auth->check_access('Admin', true);
		
		$this->load->model('Page_model');
	}
		
	function index()
	{
		$data['page_title']	= 'Page Administration';
		$data['pages']		= $this->Page_model->get_pages();
		
		
		$this->load->view($this->config->item('admin_folder').'/pages', $data);
	}
	
	/********************************************************************
	edit page
	********************************************************************/
	function form($id = false)
	{
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		//set the default values
		$data['id']			= '';
		$data['title']		= '';
		$data['menu_title']	= '';
		$data['slug']		= '';
		$data['sequence']	= 0;
		$data['parent_id']	= 0;
		$data['content']	= '';
		$data['seo_title']	= '';
		$data['meta']		= '';
		
		$data['page_title']	= 'Add Page';
		$data['pages']		= $this->Page_model->get_pages();
		
		if($id)
		{
			//set the page title to edit page
			$data['page_title']	= 'Edit Page';
			
			$page			= $this->Page_model->get_page($id);

			if(!$page)
			{
				//page does not exist
				$this->session->set_flashdata('message', 'The requested page could not be found.');
				redirect($this->config->item('admin_folder').'/pages');
			}
			
			
			//set values to db values
			$data['id']				= $page->id;
			$data['parent_id']		= $page->parent_id;
			$data['title']			= $page->title;
			$data['menu_title']		= $page->menu_title;
			$data['sequence']		= $page->sequence;
			$data['content']		= $page->content;
			$data['seo_title']		= $page->seo_title;
			$data['meta']			= $page->meta;
			$data['slug']			= $page->slug;
		}
		
		$this->form_validation->set_rules('title', 'Title', 'trim|required');
		$this->form_validation->set_rules('menu_title', 'Menu Title', 'trim');
		$this->form_validation->set_rules('slug', 'slug', 'trim');
		$this->form_validation->set_rules('seo_title', 'SEO Title', 'trim');
		$this->form_validation->set_rules('meta', 'Meta', 'trim');
		$this->form_validation->set_rules('sequence', 'sequence', 'trim|integer');
		$this->form_validation->set_rules('parent_id', 'parent_id', 'trim|integer');
		$this->form_validation->set_rules('content', 'Content', 'trim');
		
		// Validate the form
		if($this->form_validation->run() == false)
		{
			$this->load->view($this->config->item('admin_folder').'/page_form', $data);
		}
		else
		{
			//first check the slug field
			$slug = $this->input->post('slug');
			
			//if it's empty assign the name field
			if(empty($slug) || $slug=='')
			{
				$slug = $this->input->post('title');
			}
			
			$slug	= url_title($slug, 'dash', TRUE);
			
			//validate the slug
			$this->load->model('Routes_model');
			if($id)
			{
				$slug		= $this->Routes_model->validate_slug($slug, $page->route_id);
				$route_id	= $page->route_id;
			}
			else
			{
				$slug			= $this->Routes_model->validate_slug($slug);
				$route['slug']	= $slug;	
				$route_id		= $this->Routes_model->save($route);
			}
			
			
			$save = array();
			$save['id']			= $id;
			$save['parent_id']	= $this->input->post('parent_id');
			$save['title']		= $this->input->post('title');
			$save['menu_title']	= $this->input->post('menu_title'); 
			$save['sequence']	= $this->input->post('sequence');
			$save['content']	= $this->input->post('content');
			$save['seo_title']	= $this->input->post('seo_title');
			$save['meta']		= $this->input->post('meta');
			$save['route_id']	= $route_id;
			$save['slug']		= $slug;
			
			//set the menu title to the page title if if is empty
			if ($save['menu_title'] == '')
			{
				$save['menu_title']	= $this->input->post('title');
			}
			
			//save the page
			$page_id	= $this->Page_model->save($save);
			
			//save the route
			$route['id']	= $route_id;
			$route['slug']	= $slug;
			$route['route']	= 'cart/page/'.$page_id;
			
			$this->Routes_model->save($route);
			
			//set the message accordingly
			if (!$id)
			{
				$this->session->set_flashdata('message', 'The "'.$this->input->post('title').'" page has been added.');
			}
			else
			{
				$this->session->set_flashdata('message', 'Information for the "'.$this->input->post('title').'" page has been updated.');
			}
			
			//go back to the page list
			redirect($this->config->item('admin_folder').'/pages');
		}
	}
	
	function link_form($id = false)
	{
	
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		//set the default values
		$data['id']			= '';
		$data['title']		= '';
		$data['url']		= '';
		$data['new_window']	= false;
		$data['sequence']	= 0;
		$data['parent_id']	= 0;

		
		$data['page_title']	= 'Add Link';
		$data['pages']		= $this->Page_model->get_pages();
		if($id)
		{
			//set the page title to edit page
			$data['page_title']	= 'Edit Link';
			
			$page			= $this->Page_model->get_page($id);

			if(!$page)
			{
				//page does not exist
				$this->session->set_flashdata('message', 'The requested link could not be found.');
				redirect($this->config->item('admin_folder').'/pages');
			}
			
			
			//set values to db values
			$data['id']			= $page->id;
			$data['parent_id']	= $page->parent_id;
			$data['title']		= $page->title;
			$data['url']		= $page->url;
			$data['new_window']	= (bool)$page->new_window;
			$data['sequence']	= $page->sequence;
		}
		
		$this->form_validation->set_rules('title', 'Title', 'trim|required');
		$this->form_validation->set_rules('url', 'URL', 'trim|required');
		$this->form_validation->set_rules('sequence', 'sequence', 'trim|integer');
		$this->form_validation->set_rules('new_window', 'New Window', 'trim|integer');
		$this->form_validation->set_rules('parent_id', 'Parent', 'trim|integer');
		
		// Validate the form
		if($this->form_validation->run() == false)
		{
			$this->load->view($this->config->item('admin_folder').'/link_form', $data);
		}
		else
		{	
			$save = array();
			$save['id']			= $id;
			$save['parent_id']	= $this->input->post('parent_id');
			$save['title']		= $this->input->post('title');
			$save['menu_title']	= $this->input->post('title'); 
			$save['url']		= $this->input->post('url');
			$save['sequence']	= $this->input->post('sequence');
			$save['new_window']	= $this->input->post('new_window');
			
			//save the page
			$this->Page_model->save($save);
			
			//set the message accordingly
			if (!$id)
			{
				$this->session->set_flashdata('message', 'The "'.$this->input->post('title').'" link has been added.');
			}
			else
			{
				$this->session->set_flashdata('message', 'Information for the "'.$this->input->post('title').'" link has been updated.');
			}
			
			//go back to the page list
			redirect($this->config->item('admin_folder').'/pages');
		}
	}
	
	/********************************************************************
	delete page
	********************************************************************/
	function delete($id)
	{
		
		$page	= $this->Page_model->get_page($id);
		
		if($page)
		{
			$this->load->model('Routes_model');
			
			$this->Routes_model->delete($page->route_id);
			$this->Page_model->delete_page($id);
			$this->session->set_flashdata('message', 'The page has been deleted.');
		}
		else
		{
			$this->session->set_flashdata('error', 'The requested page could not be found.');
		}
		
		redirect($this->config->item('admin_folder').'/pages');
	}
}	