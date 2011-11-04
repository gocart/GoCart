<?php

class Boxes extends MY_Admin_Contriller {

    function __construct() {
        parent::__construct();
        $this->load->model('Box_model');
        $this->load->helper('date');
    }

    function index() {
        $data['boxes'] = $this->Box_model->get_boxes();
        $data['page_title'] = 'Boxes';

        $this->load->view($this->config->item('admin_folder') . '/boxes', $data);
    }

    function delete($id) {
        $this->session->set_flashdata('message', $this->Box_model->delete($id));
        redirect($this->config->item('admin_folder') . '/boxes');
    }

    /*     * ******************************************************************
      this function is called by an ajax script, it re-sorts the boxes
     * ****************************************************************** */

    function organize() {
        $boxes = $this->input->post('boxes');
        $this->Box_model->organize($boxes);
    }

    function form($id = false) {

        $config['upload_path'] = 'uploads';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = $this->config->item('size_limit');
        $config['max_width'] = '1024';
        $config['max_height'] = '768';
        $config['encrypt_name'] = true;
        $this->load->library('upload', $config);


        $this->load->helper('form');
        $this->load->library('form_validation');

        //set the default values
        $data = array('id' => $id
            , 'title' => ''
            , 'enable_on' => ''
            , 'disable_on' => ''
            , 'image' => ''
            , 'link' => ''
            , 'new_window' => false
        );

        $data['page_title'] = 'New Ad';
        if ($id) {
            $data = (array) $this->Box_model->get_box($id);

            $data['new_window'] = (bool) $data['new_window'];
            $data['page_title'] = 'Edit Ad';
        }

        $this->form_validation->set_rules('title', 'Title', 'trim|required|full_decode');
        $this->form_validation->set_rules('enable_on', 'Enable On', 'trim');
        $this->form_validation->set_rules('disable_on', 'Disable On', 'trim');
        $this->form_validation->set_rules('image', 'image', 'trim');
        $this->form_validation->set_rules('link', 'Link', 'trim');
        $this->form_validation->set_rules('new_window', 'New Window', 'trim');

        if ($this->form_validation->run() == false) {
            $data['error'] = validation_errors();
            $this->load->view($this->config->item('admin_folder') . '/box_form', $data);
        } else {

            $uploaded = $this->upload->do_upload('image');

            $save['title'] = $this->input->post('title');
            $save['enable_on'] = $this->input->post('enable_on');
            $save['disable_on'] = $this->input->post('disable_on');
            $save['link'] = $this->input->post('link');
            $save['new_window'] = $this->input->post('new_window');

            if ($id) {
                $save['id'] = $id;

                //delete the original file if another is uploaded
                if ($uploaded) {
                    if ($data['image'] != '') {
                        $file = 'uploads/' . $data['image'];

                        //delete the existing file if needed
                        if (file_exists($file)) {
                            unlink($file);
                        }
                    }
                }
            } else {
                if (!$uploaded) {
                    $data['error'] = $this->upload->display_errors();
                    $this->load->view($this->config->item('admin_folder') . '/box_form', $data);
                    return; //end script here if there is an error
                }
            }

            if ($uploaded) {
                $image = $this->upload->data();
                $save['image'] = $image['file_name'];
            }

            $this->Box_model->save($save);
            $message = 'The "' . $this->input->post('title') . '" box has been saved.';

            $this->session->set_flashdata('message', $message);

            redirect($this->config->item('admin_folder') . '/boxes');
        }
    }

}