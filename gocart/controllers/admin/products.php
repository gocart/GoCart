<?php

class Products extends MY_Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Product_model');
        $this->load->helper('form');
    }

    function index() {
        $data['page_title'] = 'Products';
        $data['products'] = $this->Product_model->get_products();

        $this->load->view($this->config->item('admin_folder') . '/products', $data);
    }

    function bulk_save() {
        $products = $this->input->post('product');

        foreach ($products as $id => $product) {
            $product['id'] = $id;
            $this->Product_model->save($product);
        }

        $this->session->set_flashdata('message', 'Your products have been updated');
        redirect($this->config->item('admin_folder') . '/products');
    }

    function form($id = false, $duplicate = false) {
        $this->product_id = $id;
        $this->load->library('form_validation');
        $this->load->model(array('Option_model', 'Category_model'));
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        $data['categories'] = $this->Category_model->get_categories_tierd();
        $data['product_list'] = $this->Product_model->get_products();

        $data['page_title'] = 'Add Product';

        //default values are empty if the product is new
        $data['id'] = '';
        $data['sku'] = '';
        $data['name'] = '';
        $data['slug'] = '';
        $data['description'] = '';
        $data['excerpt'] = '';
        $data['price'] = '';
        $data['saleprice'] = '';
        $data['weight'] = '';
        $data['in_stock'] = '';
        $data['seo_title'] = '';
        $data['meta'] = '';
        $data['related_products'] = array();
        $data['product_categories'] = array();
        $data['images'] = array();

        //create the photos array for later use
        $data['photos'] = array();

        if ($id) {
            $data['product_options'] = $this->Option_model->get_product_options($id);
            $product = $this->Product_model->get_product($id);

            //if the product does not exist, redirect them to the product list with an error
            if (!$product) {
                $this->session->set_flashdata('error', 'The requested product could not be found.');
                redirect($this->config->item('admin_folder') . '/products');
            }

            //helps us with the slug generation
            $this->product_name = $this->input->post('slug', $product->slug);


            //if we're duplicating the product, then this should not be set
            if (!$duplicate) {
                $data['page_title'] = 'Edit Product';
            }

            //set values to db values
            $data['id'] = $id;
            $data['sku'] = $product->sku;
            $data['name'] = $product->name;
            $data['seo_title'] = $product->seo_title;
            $data['meta'] = $product->meta;
            $data['slug'] = $product->slug;
            $data['description'] = $product->description;
            $data['excerpt'] = $product->excerpt;
            $data['price'] = $product->price;
            $data['saleprice'] = $product->saleprice;
            $data['weight'] = $product->weight;
            $data['in_stock'] = $product->in_stock;

            //make sure we haven't submitted the form yet before we pull in the images/related products from the database
            if (!$this->input->post('submit')) {
                $data['product_categories'] = $product->categories;
                $data['related_products'] = json_decode($product->related_products);
                $data['images'] = (array) json_decode($product->images);
            }
        }

        //if $data['related_products'] is not an array, make it one.
        if (!is_array($data['related_products'])) {
            $data['related_products'] = array();
        }
        if (!is_array($data['product_categories'])) {
            $data['product_categories'] = array();
        }

        //no error checking on these
        $this->form_validation->set_rules('caption', 'Caption');
        $this->form_validation->set_rules('primary_photo', 'Primary');

        $this->form_validation->set_rules('sku', 'SKU', 'trim');
        $this->form_validation->set_rules('seo_title', 'SEO Title', 'trim');
        $this->form_validation->set_rules('meta', 'Meta Data', 'trim');
        $this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[64]');
        $this->form_validation->set_rules('slug', 'slug', 'trim');
        $this->form_validation->set_rules('description', 'Description', 'trim');
        $this->form_validation->set_rules('excerpt', 'Excerpt', 'trim');
        $this->form_validation->set_rules('price', 'Price', 'trim|numeric');
        $this->form_validation->set_rules('saleprice', 'Sale Price', 'trim|numeric');
        $this->form_validation->set_rules('weight', 'Weight', 'trim|numeric');
        $this->form_validation->set_rules('in_stock', 'In Stock', 'trim|numeric');

        /*
          if we've posted already, get the photo stuff and organize it
          if validation comes back negative, we feed this info back into the system
          if it comes back good, then we send it with the save item

          submit button has a value, so we can see when it's posted
         */

        if ($duplicate) {
            $data['id'] = false;
        }
        if ($this->input->post('submit')) {
            //reset the product options that were submitted in the post
            $data['product_options'] = $this->input->post('option');
            $data['related_products'] = $this->input->post('related_products');
            $data['product_categories'] = $this->input->post('categories');
            $data['images'] = $this->input->post('images');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->load->view($this->config->item('admin_folder') . '/product_form', $data);
        } else {
            //first check the slug field
            $slug = $this->input->post('slug');

            //if it's empty assign the name field
            if (empty($slug) || $slug == '') {
                $slug = $this->input->post('name');
            }

            $slug = url_title($slug, 'dash', TRUE);

            //validate the slug
            $this->load->model('Routes_model');

            if ($id) {
                $slug = $this->Routes_model->validate_slug($slug, $product->route_id);
                $route_id = $product->route_id;
            } else {
                $slug = $this->Routes_model->validate_slug($slug);

                $route['slug'] = $slug;
                $route_id = $this->Routes_model->save($route);
            }

            $save['id'] = $id;
            $save['sku'] = $this->input->post('sku');
            $save['name'] = $this->input->post('name');
            $save['seo_title'] = $this->input->post('seo_title');
            $save['meta'] = $this->input->post('meta');
            $save['description'] = $this->input->post('description');
            $save['excerpt'] = $this->input->post('excerpt');
            $save['price'] = $this->input->post('price');
            $save['saleprice'] = $this->input->post('saleprice');
            $save['weight'] = $this->input->post('weight');
            $save['in_stock'] = $this->input->post('in_stock');
            $post_images = $this->input->post('images');

            $save['slug'] = $slug;
            $save['route_id'] = $route_id;

            if ($primary = $this->input->post('primary_image')) {
                if ($post_images) {
                    foreach ($post_images as $key => &$pi) {
                        if ($primary == $key) {
                            $pi['primary'] = true;
                            continue;
                        }
                    }
                }
            }

            $save['images'] = json_encode($post_images);


            if ($this->input->post('related_products')) {
                $save['related_products'] = json_encode($this->input->post('related_products'));
            } else {
                $save['related_products'] = '';
            }

            //save categories
            $categories = $this->input->post('categories');


            $options = array();
            if ($this->input->post('option')) {
                foreach ($this->input->post('option') as $option) {
                    $options[] = $option;
                }
            }

            $product_id = $this->Product_model->save($save, $options, $categories);

            //save the route
            $route['id'] = $route_id;
            $route['slug'] = $slug;
            $route['route'] = 'cart/product/' . $product_id;

            $this->Routes_model->save($route);

            if (!$id) {
                $this->session->set_flashdata('message', 'The "' . $this->input->post('name') . '" product has been added.');
            } else {
                $this->session->set_flashdata('message', 'Information for the "' . $this->input->post('name') . '" product has been updated.');
            }

            //go back to the product list
            redirect($this->config->item('admin_folder') . '/products');
        }
    }

    function product_image_form() {
        $data['file_name'] = false;
        $data['error'] = false;
        $this->load->view($this->config->item('admin_folder') . '/iframe/product_image_uploader', $data);
    }

    function product_image_upload() {
        $data['file_name'] = false;
        $data['error'] = false;

        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = $this->config->item('size_limit');
        $config['upload_path'] = 'uploads/images/full';
        $config['encrypt_name'] = true;
        $config['remove_spaces'] = true;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload()) {
            $upload_data = $this->upload->data();

            $this->load->library('image_lib');
            /*

              I find that ImageMagick is more efficient that GD2 but not everyone has it
              if your server has ImageMagick then change out the line

              $config['image_library'] = 'gd2';

              with

              $config['library_path']		= '/usr/bin/convert';
              $config['image_library']	= 'ImageMagick';
             */

            //this is the larger image
            $config['image_library'] = 'gd2';
            $config['source_image'] = 'uploads/images/full/' . $upload_data['file_name'];
            $config['new_image'] = 'uploads/images/medium/' . $upload_data['file_name'];
            $config['maintain_ratio'] = TRUE;
            $config['width'] = 600;
            $config['height'] = 500;
            $this->image_lib->initialize($config);
            $this->image_lib->resize();
            $this->image_lib->clear();

            //small image
            $config['image_library'] = 'gd2';
            $config['source_image'] = 'uploads/images/medium/' . $upload_data['file_name'];
            $config['new_image'] = 'uploads/images/small/' . $upload_data['file_name'];
            $config['maintain_ratio'] = TRUE;
            $config['width'] = 235;
            $config['height'] = 235;
            $this->image_lib->initialize($config);
            $this->image_lib->resize();
            $this->image_lib->clear();

            //cropped thumbnail
            $config['image_library'] = 'gd2';
            $config['source_image'] = 'uploads/images/small/' . $upload_data['file_name'];
            $config['new_image'] = 'uploads/images/thumbnails/' . $upload_data['file_name'];
            $config['maintain_ratio'] = TRUE;
            $config['width'] = 150;
            $config['height'] = 150;
            $this->image_lib->initialize($config);
            $this->image_lib->resize();
            $this->image_lib->clear();

            $data['file_name'] = $upload_data['file_name'];
        }

        if ($this->upload->display_errors() != '') {
            $data['error'] = $this->upload->display_errors();
        }
        $this->load->view($this->config->item('admin_folder') . '/iframe/product_image_uploader', $data);
    }

    function delete($id = false) {
        if ($id) {
            $product = $this->Product_model->get_product($id);
            //if the product does not exist, redirect them to the customer list with an error
            if (!$product) {
                $this->session->set_flashdata('message', 'The requested product could not be found.');
                redirect($this->config->item('admin_folder') . '/products');
            } else {

                // remove the slug
                $this->load->model('Routes_model');
                $this->Routes_model->remove('(' . $product->slug . ')');

                //if the product is legit, delete them
                $delete = $this->Product_model->delete_product($id);

                $this->session->set_flashdata('message', 'The "' . $product->name . '" product has been deleted from the system.');
                redirect($this->config->item('admin_folder') . '/products');
            }
        } else {
            //if they do not provide an id send them to the product list page with an error
            $this->session->set_flashdata('message', 'The requested product could not be found.');
            redirect($this->config->item('admin_folder') . '/products');
        }
    }

}