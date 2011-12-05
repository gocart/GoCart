<?php

class Locations extends Admin_Controller {	
	
	function __construct()
	{		
		parent::__construct();
		remove_ssl();
		
		$this->auth->check_access('Admin', true);
		$this->load->model('Location_model');
		$this->lang->load('location');
	}
	
	function index()
	{
		$data['page_title']	= lang('countries');
		$data['locations']	= $this->Location_model->get_countries();
		
		$this->load->view($this->config->item('admin_folder').'/countries', $data);
	}
	
	function organize_countries()
	{
		$countries	= $this->input->post('country');
		$this->Location_model->organize_countries($countries);
	}
	
	function country_form($id = false)
	{
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
	
		
		$data['page_title']		= lang('country_form');
		
		//default values are empty if the product is new
		$data['id']					= '';
		$data['name']				= '';
		$data['iso_code_2']			= '';
		$data['iso_code_3']			= '';
		$data['status']				= false;
		$data['postcode_required']	= false;
		$data['address_format']		= '';
		$data['tax']				= 0;

		if ($id)
		{	
			$country		= (array)$this->Location_model->get_country($id);
			//if the country does not exist, redirect them to the country list with an error
			if (!$country)
			{
				$this->session->set_flashdata('error', lang('error_country_not_found'));
				redirect($this->config->item('admin_folder').'/locations');
			}
			
			$data	= array_merge($data, $country);
		}
		
		$this->form_validation->set_rules('name', 'lang:name', 'trim|required');
		$this->form_validation->set_rules('iso_code_2', 'lang:iso_code_2', 'trim|required');
		$this->form_validation->set_rules('iso_code_3', 'lang:iso_code_3', 'trim|required');
		$this->form_validation->set_rules('address_format', 'lang:address_format', 'trim');
		$this->form_validation->set_rules('postcode_required', 'lang:require_postcode', 'trim');
		$this->form_validation->set_rules('tax', 'lang:tax', 'trim|numeric');
		$this->form_validation->set_rules('status', 'lang:status', 'trim');		
	
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view($this->config->item('admin_folder').'/country_form', $data);
		}
		else
		{
			$save['id']					= $id;
			$save['name']				= $this->input->post('name');
			$save['iso_code_2']			= $this->input->post('iso_code_2');
			$save['iso_code_3']			= $this->input->post('iso_code_3');
			$save['address_format']		= $this->input->post('address_format');
			$save['postcode_required']	= $this->input->post('postcode_required');
			$save['status'] 			= $this->input->post('status');
			$save['tax'] 				= $this->input->post('tax');

			$promo_id = $this->Location_model->save_country($save);
			
			$this->session->set_flashdata('message', lang('message_saved_country'));
			
			//go back to the product list
			redirect($this->config->item('admin_folder').'/locations');
		}
	}

	
	function delete_country($id = false)
	{
		if ($id)
		{	
			$location	= $this->Location_model->get_country($id);
			//if the promo does not exist, redirect them to the customer list with an error
			if (!$location)
			{
				$this->session->set_flashdata('error', lang('error_country_not_found'));
				redirect($this->config->item('admin_folder').'/locations');
			}
			else
			{
				$this->Location_model->delete_country($id);
				
				$this->session->set_flashdata('message', lang('message_deleted_country'));
				redirect($this->config->item('admin_folder').'/locations');
			}
		}
		else
		{
			//if they do not provide an id send them to the promo list page with an error
			$this->session->set_flashdata('error', lang('error_country_not_found'));
			redirect($this->config->item('admin_folder').'/locations');
		}
	}
	
	function delete_zone($id = false)
	{
		if ($id)
		{	
			$location	= $this->Location_model->get_zone($id);
			//if the promo does not exist, redirect them to the customer list with an error
			if (!$location)
			{
				$this->session->set_flashdata('error', lang('error_zone_not_found'));
				redirect($this->config->item('admin_folder').'/locations');
			}
			else
			{
				$this->Location_model->delete_zone($id);
				
				$this->session->set_flashdata('message', lang('message_deleted_zone'));
				redirect($this->config->item('admin_folder').'/locations/zones/'.$location->country_id);
			}
		}
		else
		{
			//if they do not provide an id send them to the promo list page with an error
			$this->session->set_flashdata('error', lang('error_zone_not_found'));
			redirect($this->config->item('admin_folder').'/locations');
		}
	}
	
	function zones($country_id)
	{
		$data['countries']	= $this->Location_model->get_countries();
		$data['country']	= $this->Location_model->get_country($country_id);
		if(!$data['country'])
		{
			$this->session->set_flashdata('error', lang('error_zone_not_found'));
			redirect($this->config->item('admin_folder').'/locations');
		}
		$data['zones']	= $this->Location_model->get_zones($country_id);
		
		$data['page_title']	= sprintf(lang('country_zones'), $data['country']->name);

		$this->load->view($this->config->item('admin_folder').'/country_zones', $data);
	}
	
	function zone_form($id = false)
	{
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
	
		$data['countries']		= $this->Location_model->get_countries();
		$data['page_title']		= lang('zone_form');
		
		//default values are empty if the product is new
		$data['id']			= '';
		$data['name']		= '';
		$data['country_id']	= '';
		$data['code']		= '';
		$data['tax']		= 0;
		$data['status']		= false;
		
		if ($id)
		{	
			$zone		= (array)$this->Location_model->get_zone($id);

			//if the country does not exist, redirect them to the country list with an error
			if (!$zone)
			{
				$this->session->set_flashdata('error', lang('error_zone_not_found'));
				redirect($this->config->item('admin_folder').'/locations');
			}
			
			$data	= array_merge($data, $zone);
		}
		
		$this->form_validation->set_rules('country_id', 'Country ID', 'trim|required');
		$this->form_validation->set_rules('name', 'lang:name', 'trim|required');
		$this->form_validation->set_rules('code', 'lang:code', 'trim|required');
		$this->form_validation->set_rules('tax', 'lang:tax', 'trim|numeric');
		$this->form_validation->set_rules('status', 'lang:status', 'trim');		
	
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view($this->config->item('admin_folder').'/country_zone_form', $data);
		}
		else
		{
			$save['id']			= $id;
			$save['country_id']	= $this->input->post('country_id');
			$save['name']		= $this->input->post('name');
			$save['code']		= $this->input->post('code');
			$save['status'] 	= $this->input->post('status');
			$save['tax'] 		= $this->input->post('tax');

			$this->Location_model->save_zone($save);
			
			$this->session->set_flashdata('message', lang('message_zone_saved'));
			//go back to the product list
			redirect($this->config->item('admin_folder').'/locations/zones/'.$save['country_id']);
		}
	}
	
	function get_zone_menu()
	{
		$id	= $this->input->post('id');
		$zones	= $this->Location_model->get_zones_menu($id);
		
		foreach($zones as $id=>$z):?>
		
		<option value="<?php echo $id;?>"><?php echo $z;?></option>
		
		<?php endforeach;
	}
	
	function zone_areas($id)
	{
		$data['zone']			= $this->Location_model->get_zone($id);
		$data['areas']			= $this->Location_model->get_zone_areas($id);
		
		$data['page_title']		= sprintf(lang('zone_areas_for'), $data['zone']->name);
		
		$this->load->view($this->config->item('admin_folder').'/country_zone_areas', $data);
	}

	function delete_zone_area($id = false)
	{
		if ($id)
		{	
			$location	= $this->Location_model->get_zone_area($id);
			//if the promo does not exist, redirect them to the customer list with an error
			if (!$location)
			{
				$this->session->set_flashdata('error', lang('error_zone_area_not_found'));
				redirect($this->config->item('admin_folder').'/locations');
			}
			else
			{
				$this->Location_model->delete_zone_area($id);
				
				$this->session->set_flashdata('message', lang('message_deleted_zone_area'));
				redirect($this->config->item('admin_folder').'/locations/zone_areas/'.$location->zone_id);
			}
		}
		else
		{
			//if they do not provide an id send them to the promo list page with an error
			$this->session->set_flashdata('error', lang('error_zone_area_not_found'));
			redirect($this->config->item('admin_folder').'/locations/');
		}
	}
		
	function zone_area_form($zone_id, $area_id =false)
	{
		$this->load->helper('form');
		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		
		$zone					= $this->Location_model->get_zone($zone_id);
		$data['page_title']		= sprintf(lang('zone_area_form'), $zone->name);

		//default values are empty if the product is new
		$data['id']			= '';
		$data['code']		= '';
		$data['zone_id']	= $zone_id;
		$data['tax']		= 0;

		if ($area_id)
		{	
			$area		= (array)$this->Location_model->get_zone_area($area_id);

			//if the country does not exist, redirect them to the country list with an error
			if (!$area)
			{
				$this->session->set_flashdata('error', lang('error_zone_area_not_found'));
				redirect($this->config->item('admin_folder').'/locations/zone_areas/'.$zone_id);
			}

			$data	= array_merge($data, $area);
		}

		$this->form_validation->set_rules('code', 'lang:code', 'trim|required');
		$this->form_validation->set_rules('tax', 'lang:tax', 'trim|numeric');

		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view($this->config->item('admin_folder').'/country_zone_area_form', $data);
		}
		else
		{
			$save['id']			= $area_id;
			$save['zone_id']	= $zone_id;
			$save['code']		= $this->input->post('code');
			$save['tax'] 		= $this->input->post('tax');

			$this->Location_model->save_zone_area($save);

			$this->session->set_flashdata('message', lang('message_saved_zone_area'));

			//go back to the product list
			redirect($this->config->item('admin_folder').'/locations/zone_areas/'.$save['zone_id']);
		}
	}
}