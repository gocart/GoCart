<?php


Class Digital_Products extends Admin_Controller {

	function __construct()
	{
		parent::__construct();
		$this->lang->load('digital_product');
		$this->load->model('digital_product_model');
	}
	
	function index()
	{
		$data['page_title'] = lang('dgtl_pr_header');
		$data['file_list']	= $this->digital_product_model->get_list();
		
		$this->load->view($this->config->item('admin_folder').'/digital_products', $data);
	}
	
	function form($id=0)
	{
		$this->load->helper('form_helper');
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		
		if($id)
		{
			$data				= (array)$this->digital_product_model->get_file_info($id);
			$data['page_title'] = 'Edit File';
		} else {
			$data				= $this->digital_product_model->new_file();
			$data['page_title'] = 'Upload File';
		}
		
		$this->form_validation->set_rules('title', 'lang:title', 'trim|required');
		$this->form_validation->set_rules('description', 'lang:desc', 'trim');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view($this->config->item('admin_folder').'/digital_product_form', $data);
		} else {
		
			
			if($id==0)
			{
				$data['file_name'] = false;
				$data['error']	= false;
				
				$config['allowed_types'] = 'exe|zip|rar|pdf';
				$config['upload_path'] = $this->config->item('digital_products_path');
				$config['remove_spaces'] = true;
		
				$this->load->library('upload', $config);
				
				if($this->upload->do_upload())
				{
					$upload_data	= $this->upload->data();
				} else {
					$data['error']	= $this->upload->display_errors();
					$this->load->view($this->config->item('admin_folder').'/digital_product_form', $data);
					return;
				}
				
				$save['filename']	= $upload_data['file_name'];
				$save['size']		= $upload_data['file_size'];
			} else {
				$save['id']			= $id;
			}
				
			$save['title']			= set_value('title');
			$save['description']	= set_value('description');
			
			$this->digital_product_model->save($save);
			
			redirect($this->config->item('admin_folder').'/digital_products');
		}
	}
	
	
	function delete($id)
	{
		$this->digital_product_model->delete($id);
		
		$this->session->set_flashdata('message', lang('message_deleted_file'));
		redirect($this->config->item('admin_folder').'/digital_products');
	}

}