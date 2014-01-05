<?php

class Settings extends Admin_Controller {
    
    function __construct()
    {
        parent::__construct();

        $this->auth->check_access('Admin', true);
        $this->load->model('Settings_model');
        $this->load->model('Messages_model');
        $this->lang->load('settings');
        $this->load->helper('inflector');
    }
    
    function index()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');


        $this->form_validation->set_rules('company_name', 'lang:company_name', 'required');
        $this->form_validation->set_rules('theme', 'lang:theme', 'required');
        $this->form_validation->set_rules('email', 'lang:cart_email', 'required|valid_email');

        $this->form_validation->set_rules('country_id', 'lang:country');
        $this->form_validation->set_rules('address1', 'lang:address');
        $this->form_validation->set_rules('address2', 'lang:address');
        $this->form_validation->set_rules('zone_id', 'lang:state');
        $this->form_validation->set_rules('zip', 'lang:zip');

        $this->form_validation->set_rules('locale', 'lang:locale', 'required');
        $this->form_validation->set_rules('currency_iso', 'lang:currency', 'required');

      

        $data = $this->Settings_model->get_settings('gocart');
        
        $data['config'] = $data;
        //break out order statuses to an array

        //get installed themes
        $data['themes'] = array();
        if ($handle = opendir(FCPATH.APPPATH.'/themes')) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." && is_dir(FCPATH.APPPATH.'/themes/'.$entry)) {
                    $data['themes'][$entry] = $entry;
                }
            }
            closedir($handle);
        }
        asort($data['themes']);

        //get locales
        $locales = ResourceBundle::getLocales('');
        $data['locales'] = array();
        foreach($locales as $locale)
        {
            $data['locales'][$locale] = locale_get_display_name($locale);
        }
        asort($data['locales']);
        //get ISO 4217 codes
        $data['iso_4217'] = array();
        $iso_4217 =json_decode(json_encode(simplexml_load_file(FCPATH.'ISO_4217.xml')));
        $iso_4217 = $iso_4217->CcyTbl->CcyNtry;
        foreach($iso_4217 as $iso_code)
        {
            if(isset($iso_code->Ccy))
            {
                $data['iso_4217'][$iso_code->Ccy] = $iso_code->Ccy;    
            }
        }
        asort($data['iso_4217']);


        $data['countries_menu'] = $this->Location_model->get_countries_menu();
        if(!empty($data['country_id']))
        {
            $data['zones_menu'] = $this->Location_model->get_zones_menu($data['country_id']);
        }
        else
        {
            $data['zones_menu'] = $this->Location_model->get_zones_menu(array_shift(array_keys($data['countries_menu'])));
        }

        $data['page_title'] = lang('common_gocart_configuration');

        if ($this->form_validation->run() == FALSE)
        {
            $data['error'] = validation_errors();
            $this->view($this->config->item('admin_folder').'/settings', $data);
        }
        else
        {
            $this->session->set_flashdata('message', lang('config_updated_message'));

            $save = $this->input->post();
            //fix boolean values
            $save['ssl_support'] = $this->input->post('ssl_support');
            $save['require_login'] = $this->input->post('require_login');
            $save['new_customer_status'] = $this->input->post('new_customer_status');
            $save['allow_os_purchase'] = $this->input->post('allow_os_purchase');
            $save['tax_shipping'] = $this->input->post('tax_shipping');

            $this->Settings_model->save_settings('gocart', $save);

            redirect(config_item('admin_folder').'/settings');
        }
        
    }

    function canned_messages()
    {
        $data['canned_messages'] = $this->Messages_model->get_list();
        $data['page_title'] = lang('common_canned_messages');
        $this->view($this->config->item('admin_folder').'/canned_messages', $data);
    }

  
    function canned_message_form($id=false)
    {
        $data['page_title'] = lang('canned_message_form');

        $data['id']         = $id;
        $data['name']       = '';
        $data['subject']    = '';
        $data['content']    = '';
        $data['deletable']  = 1;
        
        if($id)
        {
            $message = $this->Messages_model->get_message($id);
                        
            $data['name']       = $message['name'];
            $data['subject']    = $message['subject'];
            $data['content']    = $message['content'];
            $data['deletable']  = $message['deletable'];
        }
        
        $this->load->helper('form');
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('name', 'lang:message_name', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('subject', 'lang:subject', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('content', 'lang:message_content', 'trim|required');
        
        if ($this->form_validation->run() == FALSE)
        {
            $data['errors'] = validation_errors();
            
            $this->view($this->config->item('admin_folder').'/canned_message_form', $data);
        }
        else
        {
            
            $save['id']         = $id;
            $save['name']       = $this->input->post('name');
            $save['subject']    = $this->input->post('subject');
            $save['content']    = $this->input->post('content');
            
            //all created messages are typed to order so admins can send them from the view order page.
            if($data['deletable'])
            {
                $save['type'] = 'order';
            }
            $this->Messages_model->save_message($save);
            
            $this->session->set_flashdata('message', lang('message_saved_message'));
            redirect($this->config->item('admin_folder').'/settings/canned_messages');
        }
    }
    
    function delete_message($id)
    {
        $this->Messages_model->delete_message($id);
        
        $this->session->set_flashdata('message', lang('message_deleted_message'));
        redirect($this->config->item('admin_folder').'/settings/canned_messages');
    }
}