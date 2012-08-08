<?php
class Banners extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();

		remove_ssl();
		$this->auth->check_access('Admin', true);
		
		$this->lang->load('banner');
		
		$this->load->model('Banner_model');
		$this->load->helper('date');

	}
		
	function index()
	{
		$data['banners']		= $this->Banner_model->get_banners();
		$data['page_title']		= lang('banners');
		
		$this->load->view($this->config->item('admin_folder').'/banners', $data);
	}
	
	function organize()
	{
		$banners	= $this->input->post('banners');
		$this->Banner_model->organize($banners);
	}
	
	function delete($id)
	{
		$this->Banner_model->delete($id);
		
		$this->session->set_flashdata('message', lang('message_delete_banner'));
		redirect($this->config->item('admin_folder').'/banners');
	}
	
	/********************************************************************
	this function is called by an ajax script, it re-sorts the banners
	********************************************************************/
	
	function form($id = false)
	{
		
		$config['upload_path']		= 'uploads';
		$config['allowed_types']	= 'gif|jpg|png';
		$config['max_size']			= $this->config->item('size_limit');
		$config['encrypt_name']		= true;
		$this->load->library('upload', $config);
		
		
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		//set the default values
		$data	= array(	 'id'=>$id
							,'title'=>''
							,'enable_on'=>''
							,'disable_on'=>''
							,'image'=>''
							,'link'=>''
							,'new_window'=>false	
						);
		
		if($id)
		{
			$data				= (array) $this->Banner_model->get_banner($id);
			$data['enable_on']	= format_mdy($data['enable_on']);
			$data['disable_on']	= format_mdy($data['disable_on']);
			$data['new_window']	= (bool) $data['new_window'];
		}
		
		$data['page_title']	= lang('banner_form');
		
		$this->form_validation->set_rules('title', 'lang:title', 'trim|required|full_decode');
		$this->form_validation->set_rules('enable_on', 'lang:enable_on', 'trim');
		$this->form_validation->set_rules('disable_on', 'lang:disable_on', 'trim|callback_date_check');
		$this->form_validation->set_rules('image', 'lang:image', 'trim');
		$this->form_validation->set_rules('link', 'lang:link', 'trim');
		$this->form_validation->set_rules('new_window', 'lang:new_window', 'trim');
		
		if ($this->form_validation->run() == false)
		{
			$data['error'] = validation_errors();
			$this->load->view($this->config->item('admin_folder').'/banner_form', $data);
		}
		else
		{	
			
			$uploaded	= $this->upload->do_upload('image');
			
			$save['title']			= $this->input->post('title');
			$save['enable_on']		= format_ymd($this->input->post('enable_on'));
			$save['disable_on']		= format_ymd($this->input->post('disable_on'));
			$save['link']			= $this->input->post('link');
			$save['new_window']		= $this->input->post('new_window');
			
			if ($id)
			{
				$save['id']	= $id;
				
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
					$this->load->view($this->config->item('admin_folder').'/banner_form', $data);
					return; //end script here if there is an error
				}
			}
			
			if($uploaded)
			{
				$image			= $this->upload->data();
				$save['image']	= $image['file_name'];
			}
			
			$this->Banner_model->save_banner($save);
			
			$this->session->set_flashdata('message', lang('message_banner_saved'));
			
			redirect($this->config->item('admin_folder').'/banners');
		}	
	}

	function date_check()
	{
		
		if ($this->input->post('disable_on') != '')
		{
			if (format_ymd($this->input->post('disable_on')) <= format_ymd($this->input->post('enable_on')))
			{
				$this->form_validation->set_message('date_check', lang('date_error'));
				return FALSE;
			}
		}
		
		return TRUE;
	}
}