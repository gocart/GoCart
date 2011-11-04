<?php

class Locations extends MY_Admin_Contriller {

    function __construct() {
        parent::__construct();
        $this->load->model('Location_model');
    }

    function index() {
        $data['page_title'] = 'Countries';
        $data['locations'] = $this->Location_model->get_countries();

        $this->load->view($this->config->item('admin_folder') . '/countries', $data);
    }

    function organize_countries() {
        $countries = $this->input->post('country');
        $this->Location_model->organize_countries($countries);
    }

    function country_form($id = false) {
        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');


        $data['page_title'] = 'Add Country';

        //default values are empty if the product is new
        $data['id'] = '';
        $data['name'] = '';
        $data['iso_code_2'] = '';
        $data['iso_code_3'] = '';
        $data['status'] = false;
        $data['postcode_required'] = false;
        $data['address_format'] = '';
        $data['tax'] = 0;

        if ($id) {
            $country = (array) $this->Location_model->get_country($id);
            //if the country does not exist, redirect them to the country list with an error
            if (!$country) {
                $this->session->set_flashdata('message', 'The requested country could not be found.');
                redirect($this->config->item('admin_folder') . '/locations');
            }

            //set title to edit if we have an ID
            $data['page_title'] = 'Edit Country';

            $data = array_merge($data, $country);
        }

        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('iso_code_2', 'ISO Code 2', 'trim|required');
        $this->form_validation->set_rules('iso_code_3', 'ISO Code 3', 'trim|required');
        $this->form_validation->set_rules('address_format', 'Address Format', 'trim');
        $this->form_validation->set_rules('postcode_required', 'Post Code Required', 'trim');
        $this->form_validation->set_rules('tax', 'Tax', 'trim|numeric');
        $this->form_validation->set_rules('status', 'Status', 'trim');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view($this->config->item('admin_folder') . '/country_form', $data);
        } else {
            $save['id'] = $id;
            $save['name'] = $this->input->post('name');
            $save['iso_code_2'] = $this->input->post('iso_code_2');
            $save['iso_code_3'] = $this->input->post('iso_code_3');
            $save['address_format'] = $this->input->post('address_format');
            $save['postcode_required'] = $this->input->post('postcode_required');
            $save['status'] = $this->input->post('status');
            $save['tax'] = $this->input->post('tax');

            $promo_id = $this->Location_model->save_country($save);

            // We're done
            if (!$id) {
                $this->session->set_flashdata('message', 'The "' . $this->input->post('name') . '" Country has been added.');
            } else {
                $this->session->set_flashdata('message', 'Information for the "' . $this->input->post('name') . '" Country has been updated.');
            }

            //go back to the product list
            redirect($this->config->item('admin_folder') . '/locations');
        }
    }

    function delete_country($id = false) {
        if ($id) {
            $location = $this->Location_model->get_country($id);
            //if the promo does not exist, redirect them to the customer list with an error
            if (!$location) {
                $this->session->set_flashdata('message', 'The requested Coutnry could not be found.');
                redirect($this->config->item('admin_folder') . '/locations');
            } else {
                $this->Location_model->delete_country($id);

                $this->session->set_flashdata('message', 'The "' . $location->name . '" Country has been deleted from the system.');
                redirect($this->config->item('admin_folder') . '/locations');
            }
        } else {
            //if they do not provide an id send them to the promo list page with an error
            $this->session->set_flashdata('message', 'The requested Country could not be found.');
            redirect($this->config->item('admin_folder') . '/locations');
        }
    }

    function zones($country_id) {
        $data['countries'] = $this->Location_model->get_countries();
        $data['country'] = $this->Location_model->get_country($country_id);
        if (!$data['country']) {
            $this->session->set_flashdata('error', 'The requested Country could not be found.');
            redirect($this->config->item('admin_folder') . '/locations');
        }
        $data['zones'] = $this->Location_model->get_zones($country_id);

        $data['page_title'] = $data['country']->name . ' Zones';

        $this->load->view($this->config->item('admin_folder') . '/country_zones', $data);
    }

    function zone_form($id = false) {
        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        $data['countries'] = $this->Location_model->get_countries();
        $data['page_title'] = 'Add Zone';

        //default values are empty if the product is new
        $data['id'] = '';
        $data['name'] = '';
        $data['country_id'] = '';
        $data['code'] = '';
        $data['tax'] = 0;
        $data['status'] = false;

        if ($id) {
            $zone = (array) $this->Location_model->get_zone($id);

            //if the country does not exist, redirect them to the country list with an error
            if (!$zone) {
                $this->session->set_flashdata('message', 'The requested zone could not be found.');
                redirect($this->config->item('admin_folder') . '/locations');
            }
            //set title to edit if we have an ID
            $data['page_title'] = 'Edit Zone';

            $data = array_merge($data, $zone);
        }

        $this->form_validation->set_rules('country_id', 'Country ID', 'trim|required');
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('code', 'Code', 'trim|required');
        $this->form_validation->set_rules('tax', 'Tax', 'trim|numeric');
        $this->form_validation->set_rules('status', 'Status', 'trim');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view($this->config->item('admin_folder') . '/country_zone_form', $data);
        } else {
            $save['id'] = $id;
            $save['country_id'] = $this->input->post('country_id');
            $save['name'] = $this->input->post('name');
            $save['code'] = $this->input->post('code');
            $save['status'] = $this->input->post('status');
            $save['tax'] = $this->input->post('tax');

            $this->Location_model->save_zone($save);

            // We're done
            if (!$id) {
                $this->session->set_flashdata('message', 'The "' . $this->input->post('name') . '" zone has been added.');
            } else {
                $this->session->set_flashdata('message', 'Information for the "' . $this->input->post('name') . '" zone has been updated.');
            }

            //go back to the product list
            redirect($this->config->item('admin_folder') . '/locations/zones/' . $save['country_id']);
        }
    }

    function get_zone_menu() {
        $id = $this->input->post('id');
        $zones = $this->Location_model->get_zones_menu($id);

        foreach ($zones as $id => $z):
            ?>

            <option value="<?php echo $id; ?>"><?php echo $z; ?></option>

        <?php
        endforeach;
    }

    function zone_areas($id) {
        $data['zone'] = $this->Location_model->get_zone($id);
        $data['areas'] = $this->Location_model->get_zone_areas($id);

        $data['page_title'] = 'Zone Areas for ' . $data['zone']->name;

        $this->load->view($this->config->item('admin_folder') . '/country_zone_areas', $data);
    }

    function zone_area_form($zone_id, $area_id =false) {
        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        $zone = $this->Location_model->get_zone($zone_id);
        $data['page_title'] = 'Add Zone Area to ' . $zone->name;

        //default values are empty if the product is new
        $data['id'] = '';
        $data['code'] = '';
        $data['zone_id'] = $zone_id;
        $data['tax'] = 0;

        if ($area_id) {
            $area = (array) $this->Location_model->get_zone_area($area_id);

            //if the country does not exist, redirect them to the country list with an error
            if (!$area) {
                $this->session->set_flashdata('message', 'The requested zone area could not be found.');
                redirect($this->config->item('admin_folder') . '/locations/zone_areas/' . $zone_id);
            }
            //set title to edit if we have an ID
            $data['page_title'] = 'Edit Zone Area';

            $data = array_merge($data, $area);
        }

        $this->form_validation->set_rules('code', 'Code', 'trim|required');
        $this->form_validation->set_rules('tax', 'Tax', 'trim|numeric');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view($this->config->item('admin_folder') . '/country_zone_area_form', $data);
        } else {
            $save['id'] = $area_id;
            $save['zone_id'] = $zone_id;
            $save['code'] = $this->input->post('code');
            $save['tax'] = $this->input->post('tax');

            $this->Location_model->save_zone_area($save);

            // We're done
            if (!$area_id) {
                $this->session->set_flashdata('message', 'The "' . $this->input->post('code') . '" zone area has been added.');
            } else {
                $this->session->set_flashdata('message', 'Information for the "' . $this->input->post('code') . '" zone area has been updated.');
            }

            //go back to the product list
            redirect($this->config->item('admin_folder') . '/locations/zone_areas/' . $save['zone_id']);
        }
    }

}