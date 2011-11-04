<?php

class Admin extends MY_Admin_Contriller {

    //these are used when editing, adding or deleting an admin
    var $admin_id = false;
    var $current_admin = false;

    function __construct() {
        parent::__construct();
        $this->current_admin = $this->session->userdata('admin');
    }

    function index() {
        $data['page_title'] = 'Admins';
        $data['admins'] = $this->auth->get_admin_list();

        $this->load->view($this->config->item('admin_folder') . '/admins', $data);
    }

    function delete($id) {
        //even though the link isn't displayed for an admin to delete themselves, if they try, this should stop them.
        if ($this->current_admin['id'] == $id) {
            $this->session->set_flashdata('message', 'You cannot delete yourself!');
            redirect($this->config->item('admin_folder') . '/admin');
        }
        $this->session->set_flashdata('message', $this->auth->delete($id));
        redirect($this->config->item('admin_folder') . '/admin');
    }

    function form($id = false) {
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        $data['page_title'] = 'Add Admin';

        //default values are empty if the customer is new
        $data['id'] = '';
        $data['firstname'] = '';
        $data['lastname'] = '';
        $data['email'] = '';
        $data['access'] = '';

        if ($id) {
            $this->admin_id = $id;
            $admin = $this->auth->get_admin($id);
            //if the administrator does not exist, redirect them to the admin list with an error
            if (!$admin) {
                $this->session->set_flashdata('message', 'The requested admin could not be found.');
                redirect($this->config->item('admin_folder') . '/admin');
            }

            //set title to edit if we have an ID
            $data['page_title'] = 'Edit Administrator';

            //set values to db values
            $data['id'] = $admin->id;
            $data['firstname'] = $admin->firstname;
            $data['lastname'] = $admin->lastname;
            $data['email'] = $admin->email;
            $data['access'] = $admin->access;
        }

        $this->form_validation->set_rules('firstname', 'First Name', 'trim|max_length[32]');
        $this->form_validation->set_rules('lastname', 'Last Name', 'trim|max_length[32]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[128]|callback_check_email');
        $this->form_validation->set_rules('access', 'Access', 'trim|required');

        //if this is a new account require a password, or if they have entered either a password or a password confirmation
        if ($this->input->post('password') != '' || $this->input->post('confirm') != '' || !$id) {
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|sha1');
            $this->form_validation->set_rules('confirm', 'Confirm Password', 'required|matches[password]');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->load->view($this->config->item('admin_folder') . '/admin_form', $data);
        } else {
            $save['id'] = $id;
            $save['firstname'] = $this->input->post('firstname');
            $save['lastname'] = $this->input->post('lastname');
            $save['email'] = $this->input->post('email');
            $save['access'] = $this->input->post('access');

            if ($this->input->post('password') != '' || !$id) {
                $save['password'] = $this->input->post('password');
            }

            $this->auth->save($save);

            if (!$id) {
                $this->session->set_flashdata('message', 'The user has been added');
            } else {
                $this->session->set_flashdata('message', ' The user has been updated.');
            }

            //go back to the customer list
            redirect($this->config->item('admin_folder') . '/admin');
        }
    }

    function check_email($str) {
        $email = $this->auth->check_email($str, $this->admin_id);
        if ($email) {
            $this->form_validation->set_message('check_email', 'The Email is already in use.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

}