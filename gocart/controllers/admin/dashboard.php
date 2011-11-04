<?php

class Dashboard extends MY_Admin_Contriller {

    function __construct() {
        parent::__construct();
        $this->load->model('Order_model');
        $this->load->model('Customer_model');
        $this->load->helper('date');


        if ($this->auth->check_access('Orders')) {
            redirect($this->config->item('admin_folder') . '/orders');
        }
    }

    function index() {
        $data['page_title'] = 'Dashboard';

        // get 5 latest orders
        $data['orders'] = $this->Order_model->get_orders(false, '', 'DESC', 5);

        // get 5 latest customers
        $data['customers'] = $this->Customer_model->get_customers(5);


        $this->load->view($this->config->item('admin_folder') . '/dashboard', $data);
    }

}