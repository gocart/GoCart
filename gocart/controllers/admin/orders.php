<?php

class Orders extends Admin_Controller {	

	function __construct()
	{		
		parent::__construct();

		remove_ssl();
		$this->load->model('Order_model');
		$this->load->model('Search_model');
		$this->load->model('location_model');
		$this->load->helper(array('formatting', 'utility'));
		$this->lang->load('order');
	}
	
	function index($sort_by='order_number',$sortorder='desc', $code=0, $page=0, $rows=15)
	{
		$this->load->helper('form');
		$this->load->helper('date');
		$data['message']	= $this->session->flashdata('message');
		$data['page_title']	= lang('orders');
		$data['code']		= $code;
		$term				= false;
		
		$post	= $this->input->post(null, false);
		if($post)
		{
			//if the term is in post, save it to the db and give me a reference
			$term	= json_encode($post);
			$data['code']	= $this->Search_model->record_term($term);
			
			//reset the term to an object for use
			$term	= (object)$post;
		}
		elseif ($code)
		{
			$term	= $this->Search_model->get_term($code);
			$term	= json_decode($term);
		} 
 		
 		$data['term']	= $term;
 		$data['orders']	= $this->Order_model->get_orders($term, $sort_by, $sortorder, $rows, $page);
		$data['total']	= $this->Order_model->get_orders_count($term);
		
		$this->load->library('pagination');
		
		$config['base_url'] = $this->config->item('admin_folder').'/orders/index/'.$sort_by.'/'.$sortorder.'/'.$code.'/';
		$config['total_rows'] = $data['total'];
		$config['per_page'] = $rows;
		
		$this->pagination->initialize($config);
	
		$data['sort_by']	= $sort_by;
		$data['sortorder']	= $sortorder;
		$data['pages']		= $this->pagination->create_links();
				
		$this->load->view($this->config->item('admin_folder').'/orders', $data);
	}
	
	function export()
	{
		$this->load->model('customer_model');
		$this->load->helper('download_helper');
		$post	= $this->input->post(null, false);
		$term	= (object)$post;
		
		$data['orders']	= $this->Order_model->get_orders($term);		

		foreach($data['orders'] as &$o)
		{
			$o->items	= $this->Order_model->get_items($o->id);
		}

		force_download_content('orders.xml', $this->load->view($this->config->item('admin_folder').'/orders_xml', $data, true));
		
	}
	
	function view($id)
	{
		$this->load->helper(array('form', 'date'));
		$this->load->library('form_validation');
		$this->load->model('Gift_card_model');
			
		$this->form_validation->set_rules('notes', 'lang:notes');
		$this->form_validation->set_rules('status', 'lang:status', 'required');
		if ($this->form_validation->run() == TRUE)
		{
			$save			= array();
			$save['id']		= $id;
			$save['notes']	= $this->input->post('notes');
			$save['status']	= $this->input->post('status');
			
			$data['message']	= lang('message_order_updated');
			
			$this->Order_model->save_order($save);
		}
		//get the order information, this way if something was posted before the new one gets queried here
		$data['page_title']	= lang('view_order');
		$data['order']		= $this->Order_model->get_order($id);
		
		// we need to see if any items are gift cards, so we can generate an activation link
		foreach($data['order']->contents as $orderkey=>$product)
		{
			if(isset($product['is_gc']))
			{
				if($this->Gift_card_model->is_active($product['code']))
				{
					$data['order']->contents[$orderkey]['gc_status'] = '[ '.lang('giftcard_is_active').' ]';
				} else {
					$data['order']->contents[$orderkey]['gc_status'] = ' [ <a href="'. base_url() . $this->config->item('admin_folder').'/giftcards/activate/'. $product['code'].'">'.lang('activate').'</a> ]';
				}
			}
		}
		
		$this->load->view($this->config->item('admin_folder').'/order', $data);
		
	}
	
	function packing_slip($order_id)
	{
		$this->load->helper('date');
		$data['order']		= $this->Order_model->get_order($order_id);
		
		$this->load->view($this->config->item('admin_folder').'/packing_slip.php', $data);
	}
	
	function edit_status()
    {
    	$this->auth->is_logged_in();
    	$order['id']		= $this->input->post('id');
    	$order['status']	= $this->input->post('status');
    	
    	$this->Order_model->save_order($order);
    	
    	echo url_title($order['status']);
    }
    
    function send_notification($order_id='')
    {
    	
    	$send = $this->input->post('send');
    	if(!empty($send))
    	{
    		// send the message
    		$this->load->library('email');
			
			$config['mailtype'] = 'html';
			
			$this->email->initialize($config);
	
			$this->email->from($this->config->item('email'), $this->config->item('company_name'));
			$this->email->to($this->input->post('recipient'));
			
			$this->email->subject($this->input->post('subject'));
			$this->email->message(html_entity_decode($this->input->post('content')));
			
			$this->email->send();
			
			$this->load->view($this->config->item('admin_folder').'/iframe/order_notification.php', array('finished'=>1));
			
			return;
    	}
    	
    	
    	$this->load->model('Messages_model');
    	
    	// get the order details
    	$data['order'] = $this->Order_model->get_order($order_id);
    	
    	// get the list of canned messages (order)
    	$data['msg_templates'] = $this->Messages_model->get_list('order');
    	
    	// replace template variables
    	foreach($data['msg_templates'] as &$msg)
    	{
 			// fix html
 			$msg['content'] = str_replace("\n", '', html_entity_decode($msg['content']));
 			
 			// {order_number}
 			$msg['subject'] = str_replace('{order_number}', $data['order']->order_number, $msg['subject']);
			$msg['content'] = str_replace('{order_number}', $data['order']->order_number, $msg['content']);
    		
    		// {url}
			$msg['subject'] = str_replace('{url}', $this->config->item('base_url'), $msg['subject']);
			$msg['content'] = str_replace('{url}', $this->config->item('base_url'), $msg['content']);
			
			// {site_name}
			$msg['subject'] = str_replace('{site_name}', $this->config->item('company_name'), $msg['subject']);
			$msg['content'] = str_replace('{site_name}', $this->config->item('company_name'), $msg['content']);
			
    	}
    	
    	$this->load->view($this->config->item('admin_folder').'/iframe/order_notification.php', $data);
 
    }
	
	function bulk_delete()
    {
    	$orders	= $this->input->post('order');
    	
		if($orders)
		{
			foreach($orders as $order)
	   		{
	   			$this->Order_model->delete($order);
	   		}
			$this->session->set_flashdata('message', lang('message_orders_deleted'));
		}
		else
		{
			$this->session->set_flashdata('error', lang('error_no_orders_selected'));
		}
   		//redirect as to change the url
		redirect($this->config->item('admin_folder').'/orders');	
    }
}