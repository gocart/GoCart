<?php

class Giftcards extends Admin_Controller {
	
	function __construct()
	{
		parent::__construct();
		
		force_ssl();
		
		$this->load->model('Settings_model');
		$this->load->model('Gift_card_model');
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		$this->lang->load('giftcard');
	}
	
	function index()
	{
		
		$data['page_title'] = lang('giftcards');
		$data['cards'] = $this->Gift_card_model->get_all_new();
		
		$gc_settings = $this->Settings_model->get_settings('gift_cards');
		if(isset($gc_settings['enabled']))
		{
			$data['gift_cards']['enabled'] = $gc_settings['enabled'];
		}
		else
		{
			$data['gift_cards']['enabled'] = false;
		}
		
		$this->load->view($this->config->item('admin_folder').'/giftcards', $data);
		
	}
	
	function form()
	{
		
		$this->form_validation->set_rules('to_email', 'lang:recipient_email', 'trim|required');
		$this->form_validation->set_rules('to_name', 'lang:recipient_name', 'trim|required');
		$this->form_validation->set_rules('from', 'lang:sender_name', 'trim|required');
		$this->form_validation->set_rules('personal_message', 'lang:personal_message', 'trim');
		$this->form_validation->set_rules('beginning_amount', 'lang:amount', 'trim|required|numeric');
		
		$data['page_title'] = lang('add_giftcard');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view($this->config->item('admin_folder').'/giftcard_form', $data);
		}
		else
		{
			$save['code'] = generate_code(); // from the string helper
			$save['to_email'] = $this->input->post('to_email');
			$save['to_name'] = $this->input->post('to_name');
			$save['from'] = $this->input->post('from');
			$save['personal_message'] = $this->input->post('personal_message');
			$save['beginning_amount'] = $this->input->post('beginning_amount');
			$save['activated'] = 1;
			
			$this->Gift_card_model->save_card($save);
			
			if($this->input->post('send_notification'))
			{
				//get the canned message for gift cards
				$row = $this->db->where('id', '1')->get('canned_messages')->row_array();

				// set replacement values for subject & body
				$row['subject']	= str_replace('{from}', $save['from'], $row['subject']);
				$row['subject']	= str_replace('{site_name}', $this->config->item('company_name'), $row['subject']);
				
				$row['content']	= str_replace('{code}', $save['code'], $row['content']);
				$row['content']	= str_replace('{amount}', $save['beginning_amount'], $row['content']);
				$row['content']	= str_replace('{from}', $save['from'], $row['content']);
				$row['content']	= str_replace('{personal_message}', nl2br($save['personal_message']), $row['content']);
				$row['content']	= str_replace('{url}', $this->config->item('base_url'), $row['content']);
				$row['content']	= str_replace('{site_name}', $this->config->item('company_name'), $row['content']);

				$this->load->library('email');

				$config['mailtype'] = 'html';
				$this->email->initialize($config);

				$this->email->from($this->config->item('email'));
				$this->email->to($save['to_email']);

				$this->email->subject($row['subject']);
				$this->email->message($row['content']);

				$this->email->send();
			}
			
			$this->session->set_flashdata('message', lang('message_saved_giftcard'));
			
			redirect($this->config->item('admin_folder').'/giftcards');
		}
		
	}
	
	function activate($code)
	{
		$this->Gift_card_model->activate($code);
		$this->Gift_card_model->send_notification($code);
		$this->session->set_flashdata('message', lang('message_activated_giftcard'));
		redirect($this->config->item('admin_folder').'/giftcards');
	}
	
	function delete($id)
	{
		$this->Gift_card_model->delete($id);
		
		$this->session->set_flashdata('message', lang('message_deleted_giftcard'));
		redirect($this->config->item('admin_folder').'/giftcards');
	}
	
	// Gift card functionality 
	function enable()
	{
		
		$config['predefined_card_amounts'] = "10,20,25,50,100";
		$config['allow_custom_amount'] = "1";
		$config['enabled'] = '1';
		$this->Settings_model->save_settings('gift_cards', $config);
		redirect($this->config->item('admin_folder').'/giftcards');
	}
	
	function disable() 
	{
		$config['enabled'] = '0';
		$this->Settings_model->save_settings('gift_cards', $config);
		redirect($this->config->item('admin_folder').'/giftcards');
	}
	
	function settings()
	{		
		$gc_settings = $this->Settings_model->get_settings('gift_cards');
				
		$data['predefined_card_amounts']	= $gc_settings['predefined_card_amounts'];
		$data['allow_custom_amount'] 		= $gc_settings['allow_custom_amount'];
		
		$this->form_validation->set_rules('predefined_card_amounts', 'lang:predefined_card_amounts', 'trim');
		$this->form_validation->set_rules('allow_custom_amount', 'lang:allow_custom_amounts', 'trim');
		
		$data['page_title']	= lang('giftcard_settings');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view($this->config->item('admin_folder').'/giftcards_settings', $data);
		}
		else
		{
		
			$save['predefined_card_amounts']	= $this->input->post('predefined_card_amounts');
			$save['allow_custom_amount']		= $this->input->post('allow_custom_amount');
			
			$this->Settings_model->save_settings('gift_cards', $save);
			
			$this->session->set_flashdata('message', lang('message_saved_settings'));
			
			redirect($this->config->item('admin_folder').'/giftcards');
			
		}
	}
}
