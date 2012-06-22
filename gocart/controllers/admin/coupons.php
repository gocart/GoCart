<?php

class Coupons extends Admin_Controller {	
	
	var $coupon_id;
	
	function __construct()
	{		
		parent::__construct();
		
		force_ssl();
		$this->auth->check_access('Admin', true);
		$this->load->model('Coupon_model');
		$this->load->model('Product_model');
		$this->lang->load('coupon');
	}
	
	function index()
	{
		$data['page_title']	= lang('coupons');
		$data['coupons']	= $this->Coupon_model->get_coupons();
		
		$this->load->view($this->config->item('admin_folder').'/coupons', $data);
	}
	
	
	function form($id = false)
	{
		$this->load->helper(array('form', 'date'));
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		
		$this->coupon_id	= $id;
		
		$data['page_title']		= lang('coupon_form');
		
		//default values are empty if the product is new
		$data['id']						= '';
		$data['code']					= '';
		$data['start_date']				= '';
		$data['whole_order_coupon']		= 0;
		$data['max_product_instances'] 	= '';
		$data['end_date']				= '';
		$data['max_uses']				= '';
		$data['reduction_target'] 		= '';
		$data['reduction_type']			= '';
		$data['reduction_amount']		= '';
		
		$added = array();
		
		if ($id)
		{	
			$coupon		= $this->Coupon_model->get_coupon($id);

			//if the product does not exist, redirect them to the product list with an error
			if (!$coupon)
			{
				$this->session->set_flashdata('message', lang('error_not_found'));
				redirect($this->config->item('admin_folder').'/product');
			}
			
			//set values to db values
			$data['id']						= $coupon->id;
			$data['code']					= $coupon->code;
			$data['start_date']				= $coupon->start_date;
			$data['end_date']				= $coupon->end_date;
			$data['whole_order_coupon']		= $coupon->whole_order_coupon;
			$data['max_product_instances'] 	= $coupon->max_product_instances;
			$data['num_uses']     			= $coupon->num_uses;
			$data['max_uses']				= $coupon->max_uses;
			$data['reduction_target']		= $coupon->reduction_target;
			$data['reduction_type']			= $coupon->reduction_type;
			$data['reduction_amount']		= $coupon->reduction_amount;
			
			$added = $this->Coupon_model->get_product_ids($id);
		}
		
		$this->form_validation->set_rules('code', 'lang:code', 'trim|required|callback_check_code');
		$this->form_validation->set_rules('max_uses', 'lang:max_uses', 'trim|numeric');
		$this->form_validation->set_rules('max_product_instances', 'lang:limit_per_order', 'trim|numeric');
		$this->form_validation->set_rules('whole_order_coupon', 'lang:whole_order_discount');
		$this->form_validation->set_rules('reduction_target', 'lang:reduction_target', 'trim|required');
		$this->form_validation->set_rules('reduction_type', 'lang:reduction_type', 'trim');
		$this->form_validation->set_rules('reduction_amount', 'lang:reduction_amount', 'trim|numeric');
		$this->form_validation->set_rules('start_date', 'lang:start_date');
		$this->form_validation->set_rules('end_date', 'lang:end_date');
		
		// create product list
		$products = $this->Product_model->get_products();
		
		// set up a 2x2 row list for now
		$data['product_rows'] = "";
		$x=0;
		while(TRUE) { // Yes, forever, until we find the end of our list
			if ( !isset($products[$x] )) break; // stop if we get to the end of our list
			$checked = "";
			if(in_array($products[$x]->id, $added))
			{
				$checked = "checked='checked'";
			}
			$data['product_rows']  .=  "<tr><td><input type='checkbox' name='product[]' value='". $products[$x]->id ."' $checked></td><td> ". $products[$x]->name ."</td>";
			
			$x++;
			
			//reset the checked value to nothing
			$checked = "";
			if ( isset($products[$x] )) { // if we've gotten to the end on this row
				if(in_array($products[$x]->id, $added))
				{
					$checked = "checked='checked'";
				}
				$data['product_rows']  .= 	"<td><input type='checkbox' name='product[]' value='". $products[$x]->id ."' $checked><td><td> ". $products[$x]->name ."</td></tr>";
			} else {
				$data['product_rows']  .= 	"<td> </td></tr>";
			}
			
			$x++;
		} 
		
	
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view($this->config->item('admin_folder').'/coupon_form', $data);
		}
		else
		{
			$save['id']						= $id;
			$save['code']					= $this->input->post('code');
			$save['start_date']				= $this->input->post('start_date');
			$save['end_date']				= $this->input->post('end_date');
			$save['max_uses']				= $this->input->post('max_uses');
			$save['whole_order_coupon'] 	= $this->input->post('whole_order_coupon');
			$save['max_product_instances'] 	= $this->input->post('max_product_instances');
			$save['reduction_target']		= $this->input->post('reduction_target');
			$save['reduction_type']			= $this->input->post('reduction_type');
			$save['reduction_amount']		= $this->input->post('reduction_amount');
			
			$product = $this->input->post('product');
			
			// save coupon
			$promo_id = $this->Coupon_model->save($save);
			
			// save products if not a whole order coupon
			//   clear products first, then save again (the lazy way, but sequence is not utilized at the moment)
			$this->Coupon_model->remove_product($id);
			
			if(!$save['whole_order_coupon'] && $product) 
			{
				while(list(, $product_id) = each($product))
				{
					$this->Coupon_model->add_product($promo_id, $product_id);
				}
			}
			
			// We're done
			$this->session->set_flashdata('message', lang('message_saved_coupon'));
			
			//go back to the product list
			redirect($this->config->item('admin_folder').'/coupons');
		}
	}

	//this is a callback to make sure that 2 coupons don't have the same code
	function check_code($str)
	{
		$code = $this->Coupon_model->check_code($str, $this->coupon_id);
        if ($code)
       	{
			$this->form_validation->set_message('check_code', lang('error_already_used'));
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	function delete($id = false)
	{
		if ($id)
		{	
			$coupon	= $this->Coupon_model->get_coupon($id);
			//if the promo does not exist, redirect them to the customer list with an error
			if (!$coupon)
			{
				$this->session->set_flashdata('error', lang('error_not_found'));
				redirect($this->config->item('admin_folder').'/coupons');
			}
			else
			{
				$this->Coupon_model->delete_coupon($id);
				
				$this->session->set_flashdata('message', lang('message_coupon_deleted'));
				redirect($this->config->item('admin_folder').'/coupons');
			}
		}
		else
		{
			//if they do not provide an id send them to the promo list page with an error
			$this->session->set_flashdata('message', lang('error_not_found'));
			redirect($this->config->item('admin_folder').'/coupons');
		}
	}
}