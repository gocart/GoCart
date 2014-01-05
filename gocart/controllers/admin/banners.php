<?php
	
/*

Banner Admin controller

*/

class Banners extends Admin_Controller {
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('banner_model');
		$this->load->language('banner');
	}
	
	function get_details()
	{
		return $this->details;
	}
	
	function index()
	{
		$data['page_title']			= lang('banner_collections');
		
		$data['banner_collections']	= $this->banner_model->banner_collections();
		$this->view(config_item('admin_folder').'/banner_collections', $data);
	}
	
	function banner_collection_form($banner_collection_id = false)
	{
		$data['page_title']			= lang('banner_collection_form');
		
		$this->load->library('form_validation');
		
		$data['banner_collection_id']	= $banner_collection_id;
		$data['name']					= '';
		
		if($banner_collection_id)
		{
			$banner_collection	= $this->banner_model->banner_collection($banner_collection_id);
			
			if(!$banner_collection)
			{
				$this->session->set_flashdara('error', lang('banner_collection_not_found'));
				redirect(config_item('admin_folder').'/banners');
			}
			else
			{
				$data	= array_merge($data, (array)$banner_collection);
			}
		}
		
		$this->form_validation->set_rules('name', 'lang:name', 'trim|required');
		
		if ($this->form_validation->run() == false)
		{
			$this->view(config_item('admin_folder').'/banner_collection_form', $data);
		}
		else
		{
			$save['banner_collection_id']	= $banner_collection_id;
			$save['name']					= $this->input->post('name');
			
			$this->banner_model->save_banner_collection($save);
			
			$this->session->set_flashdata('message', lang('message_banner_collection_saved'));
			
			redirect(config_item('admin_folder').'/banners');
		}
	}
	
	function delete_banner_collection($banner_collection_id)
	{
		$banner_collection	= $this->banner_model->banner_collection($banner_collection_id);
		if(!$banner_collection)
		{
			$this->session->set_flashdata('error', lang('banner_collection_not_found'));
		}
		else
		{
			$this->banner_model->delete_banner_collection($banner_collection_id);
			$this->session->set_flashdata('message', lang('message_delete_banner_collection'));
		}
		
		redirect(config_item('admin_folder').'/banners');
	}
	
	function banner_collection($banner_collection_id)
	{
		$data['banner_collection']	= $this->banner_model->banner_collection($banner_collection_id);
		if(!$data['banner_collection'])
		{
			$this->session->set_flashdata('error', lang('banner_collection_not_found'));
			redirect(config_item('admin_folder').'/banners');
		}
		
		$data['banner_collection_id']	= $banner_collection_id;
		$data['page_title']				= lang('banners').' : '.$data['banner_collection']->name;
		$data['banners']				= $this->banner_model->banner_collection_banners($banner_collection_id);
		
		$this->view(config_item('admin_folder').'/banner_collection', $data);
	}

	function banner_form($banner_collection_id, $id = false)
	{
		
		$config['upload_path']		= 'uploads';
		$config['allowed_types']	= 'gif|jpg|png';
		$config['max_size']			= $this->config->item('size_limit');
		$config['encrypt_name']		= true;
		$this->load->library('upload', $config);
		
		
		$this->load->helper(array('form', 'date'));
		$this->load->library('form_validation');
		
		//set the default values
		$data	= array(	 'banner_id'			=> $id
							,'banner_collection_id'	=> $banner_collection_id
							,'name'					=> ''
							,'enable_date'			=> ''
							,'disable_date'			=> ''
							,'image'				=> ''
							,'link'					=> ''
							,'new_window'			=> false
						);
		
		if($id)
		{
			$data					= array_merge($data, (array)$this->banner_model->banner($id));
			$data['enable_date']	= format_mdy($data['enable_date']);
			$data['disable_date']	= format_mdy($data['disable_date']);
			$data['new_window']		= (bool) $data['new_window'];
		}
		
		$data['page_title']	= lang('banner_form');
		
		$this->form_validation->set_rules('name', 'lang:name', 'trim|required|full_decode');
		$this->form_validation->set_rules('enable_date', 'lang:enable_date', 'trim');
		$this->form_validation->set_rules('disable_date', 'lang:disable_date', 'trim|callback_date_check');
		$this->form_validation->set_rules('image', 'lang:image', 'trim');
		$this->form_validation->set_rules('link', 'lang:link', 'trim');
		$this->form_validation->set_rules('new_window', 'lang:new_window', 'trim');
		
		if ($this->form_validation->run() == false)
		{
			$data['error'] = validation_errors();
			$this->view(config_item('admin_folder').'/banner_form', $data);
		}
		else
		{	
			
			$uploaded	= $this->upload->do_upload('image');
			
			$save['banner_collection_id']	= $banner_collection_id;
			$save['name']					= $this->input->post('name');
			$save['enable_date']			= format_ymd($this->input->post('enable_date'));
			$save['disable_date']			= format_ymd($this->input->post('disable_date'));
			$save['link']					= $this->input->post('link');
			$save['new_window']				= $this->input->post('new_window');
			
			if ($id)
			{
				$save['banner_id']	= $id;
				
				//delete the original file if another is uploaded
				if($uploaded)
				{
					if($data['image'] != '')
					{
						$file = 'uploads/'.$data['image'];
						
						//delete the existing file if needed
						if(file_exists($file))
						{
							unlink($file);
						}
					}
				}
				
			}
			else
			{
				if(!$uploaded)
				{
					$data['error']	= $this->upload->display_errors();
					$this->view(config_item('admin_folder').'/banner_form', $data);
					return; //end script here if there is an error
				}
			}
			
			if($uploaded)
			{
				$image			= $this->upload->data();
				$save['image']	= $image['file_name'];
			}
			
			$this->banner_model->save_banner($save);
			
			$this->session->set_flashdata('message', lang('message_banner_saved'));
			
			redirect(config_item('admin_folder').'/banners/banner_collection/'.$banner_collection_id);
		}	
	}
	
	function delete_banner($banner_id)
	{
		$banner	= $this->banner_model->banner($banner_id);
		if(!$banner)
		{
			$this->session->set_flashdata('error', lang('banner_not_found'));
		}
		else
		{
			$this->banner_model->delete_banner($banner_id);
			$this->session->set_flashdata('message', lang('message_delete_banner'));
		}
		
		redirect(config_item('admin_folder').'/banners/banner_collection/'.$banner->banner_collection_id);
	}
	
	function organize()
	{
		$banners	= $this->input->post('banners');
		$this->banner_model->organize($banners);
	}
}