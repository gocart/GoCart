<?php

class Categories extends MY_Controller {	
	
	function __construct()
	{		
		parent::__construct();
		$this->load->library('Auth');
		$this->auth->check_access('Admin', true);
		
		$this->load->model('Category_model');
		//this adds the redirect url to our flash data, incase they are not logged in
		$this->auth->is_logged_in($_SERVER['REQUEST_URI']);
	}
	
	function index()
	{
		//we're going to use flash data and redirect() after form submissions to stop people from refreshing and duplicating submissions
		//$this->session->set_flashdata('message', 'this is our message');
		
		$data['page_title']	= 'Categories';
		$data['categories']	= $this->Category_model->get_categories_tierd();
		
		$this->load->view($this->config->item('admin_folder').'/categories', $data);
	}
	
	function organize($id = false)
	{
		$this->load->helper('form');
		
		if (!$id)
		{
			$this->session->set_flashdata('message', 'You must select a category to organize.');
			redirect($this->config->item('admin_folder').'/categories');
		}
		
		$data['category']		= $this->Category_model->get_category($id);
		//if the category does not exist, redirect them to the category list with an error
		if (!$data['category'])
		{
			$this->session->set_flashdata('message', 'The requested category could not be found.');
			redirect($this->config->item('admin_folder').'/categories');
		}
			
		$data['page_title']		= 'Oranize "'.$data['category']->name.'" Category';
		
		$data['category_products']	= $this->Category_model->get_category_products_admin($id);
		
		$this->load->view($this->config->item('admin_folder').'/organize_category', $data);
	}
	
	function process_organization($id)
	{
		$products	= $this->input->post('product');
		$this->Category_model->organize_contents($id, $products);
	}
	
	function form($id = false)
	{
		
		$config['upload_path']		= 'uploads/images/full';
		$config['allowed_types']	= 'gif|jpg|png';
		$config['max_size']			= $this->config->item('size_limit');
		$config['max_width']		= '1024';
		$config['max_height']		= '768';
		$config['encrypt_name']		= true;
		$this->load->library('upload', $config);
		
		
		$this->category_id	= $id;
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		
		$data['categories']		= $this->Category_model->get_categories();
		$data['page_title']		= 'Add Category';
		
		//default values are empty if the customer is new
		$data['id']				= '';
		$data['name']			= '';
		$data['slug']			= '';
		$data['description']	= '';
		$data['excerpt']		= '';
		$data['sequence']		= '';
		$data['image']			= '';
		$data['seo_title']		= '';
		$data['meta']			= '';
		$data['parent_id']		= 0;
		
		//create the photos array for later use
		$data['photos']		= array();
		
		if ($id)
		{	
			$category		= $this->Category_model->get_category($id);

			//if the category does not exist, redirect them to the category list with an error
			if (!$category)
			{
				$this->session->set_flashdata('message', 'The requested category could not be found.');
				redirect($this->config->item('admin_folder').'/categories');
			}
			
			//helps us with the slug generation
			$this->category_name	= $this->input->post('slug', $category->slug);
			
			//set title to edit if we have an ID
			$data['page_title']	= 'Edit Category';
			
			//set values to db values
			$data['id']				= $category->id;
			$data['name']			= $category->name;
			$data['slug']			= $category->slug;
			$data['description']	= $category->description;
			$data['excerpt']		= $category->excerpt;
			$data['sequence']		= $category->sequence;
			$data['parent_id']		= $category->parent_id;
			$data['image']			= $category->image;
			$data['seo_title']		= $category->seo_title;
			$data['meta']			= $category->meta;
			
		}
		
		$this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[64]');
		$this->form_validation->set_rules('slug', 'slug', 'trim');
		$this->form_validation->set_rules('description', 'Description', 'trim');
		$this->form_validation->set_rules('excerpt', 'Excerpt', 'trim');
		$this->form_validation->set_rules('sequence', 'Sequence', 'trim|integer');
		$this->form_validation->set_rules('parent_id', 'parent_id', 'trim');
		$this->form_validation->set_rules('image', 'image', 'trim');
		$this->form_validation->set_rules('seo_title', 'SEO Title', 'trim');
		$this->form_validation->set_rules('meta', 'Meta', 'trim');
		
		
		// validate the form
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view($this->config->item('admin_folder').'/category_form', $data);
		}
		else
		{
			
			
			$uploaded	= $this->upload->do_upload('image');
			
			if ($id)
			{
				//delete the original file if another is uploaded
				if($uploaded)
				{
					
					if($data['image'] != '')
					{
						$file = array();
						$file[] = 'uploads/images/full/'.$data['image'];
						$file[] = 'uploads/images/medium/'.$data['image'];
						$file[] = 'uploads/images/small/'.$data['image'];
						$file[] = 'uploads/images/thumbnails/'.$data['image'];
						
						foreach($file as $f)
						{
							//delete the existing file if needed
							if(file_exists($f))
							{
								unlink($f);
							}
						}
					}
				}
				
			}
			else
			{
				if(!$uploaded)
				{
					$error	= $this->upload->display_errors();
					if($error != '<p>You did not select a file to upload.</p>')
					{
						$data['error']	.= $this->upload->display_errors();
						$this->load->view($this->config->item('admin_folder').'/category_form', $data);
						return; //end script here if there is an error
					}
				}
			}
			
			if($uploaded)
			{
				$image			= $this->upload->data();
				$save['image']	= $image['file_name'];
				
				$this->load->library('image_lib');
				
				//this is the larger image
				$config['image_library'] = 'gd2';
				$config['source_image'] = 'uploads/images/full/'.$save['image'];
				$config['new_image']	= 'uploads/images/medium/'.$save['image'];
				$config['maintain_ratio'] = TRUE;
				$config['width'] = 600;
				$config['height'] = 500;
				$this->image_lib->initialize($config);
				$this->image_lib->resize();
				$this->image_lib->clear();

				//small image
				$config['image_library'] = 'gd2';
				$config['source_image'] = 'uploads/images/medium/'.$save['image'];
				$config['new_image']	= 'uploads/images/small/'.$save['image'];
				$config['maintain_ratio'] = TRUE;
				$config['width'] = 300;
				$config['height'] = 300;
				$this->image_lib->initialize($config); 
				$this->image_lib->resize();
				$this->image_lib->clear();

				//cropped thumbnail
				$config['image_library'] = 'gd2';
				$config['source_image'] = 'uploads/images/small/'.$save['image'];
				$config['new_image']	= 'uploads/images/thumbnails/'.$save['image'];
				$config['maintain_ratio'] = TRUE;
				$config['width'] = 150;
				$config['height'] = 150;
				$this->image_lib->initialize($config); 	
				$this->image_lib->resize();	
				$this->image_lib->clear();
			}
			
			//first check the slug field
			$slug = $this->input->post('slug');
			
			//if it's empty assign the name field
			if(empty($slug) || $slug=='')
			{
				$slug = $this->input->post('name');
			}
			
			$slug	= url_title($slug, 'dash', TRUE);
			
			//validate the slug
			$this->load->model('Routes_model');
			if($id)
			{
				$slug	= $this->Routes_model->validate_slug($slug, $category->route_id);
				$route_id	= $category->route_id;
			}
			else
			{
				$slug	= $this->Routes_model->validate_slug($slug);
				
				$route['slug']	= $slug;	
				$route_id	= $this->Routes_model->save($route);
			}
			
			$save['id']				= $id;
			$save['name']			= $this->input->post('name');
			$save['description']	= $this->input->post('description');
			$save['excerpt']		= $this->input->post('excerpt');
			$save['parent_id']		= $this->input->post('parent_id');
			$save['sequence']		= $this->input->post('sequence');
			$save['seo_title']		= $this->input->post('seo_title');
			$save['meta']			= $this->input->post('meta');

			$save['route_id']		= $route_id;
			$save['slug']			= $slug;
			
			$category_id	= $this->Category_model->save($save);
			
			//save the route
			$route['id']	= $route_id;
			$route['slug']	= $slug;
			$route['route']	= 'cart/category/'.$category_id.'';
			
			$this->Routes_model->save($route);
			
			$this->session->set_flashdata('message', 'The "'.$this->input->post('name').'" category has been updated.');
			
			//go back to the category list
			redirect($this->config->item('admin_folder').'/categories');
		}
	}

	function delete($id)
	{
		
		$category	= $this->Category_model->get_category($id);
		//if the category does not exist, redirect them to the customer list with an error
		if ($category)
		{
			$this->load->model('Routes_model');
			
			$this->Routes_model->delete($category->route_id);
			$this->Category_model->delete($id);
			
			$this->session->set_flashdata('message', 'The "'.$category->name.'" category has been deleted from the system.');
			redirect($this->config->item('admin_folder').'/categories');
		}
		else
		{
			$this->session->set_flashdata('error', 'The requested category could not be found.');
		}
	}
}