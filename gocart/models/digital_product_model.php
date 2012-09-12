<?php

Class Digital_Product_Model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->lang->load('digital_product');
	}
	
	// Return blank record array
	function new_file()
	{
		return array(
					'id'=>'',
					'filename'=>'',
					'max_downloads'=>'',
					'title'=>'',
					'description'=>'',
					'size'=>''
					);
	}
	
	// Get files list
	function get_list()
	{
		
		$list = $this->db->get('digital_products')->result();
		
		foreach($list as &$file)
		{
			// identify if the record is missing it's file content
			$file->verified = $this->verify_content($file->filename);
		}
		
		return $list;
	}
	
	// Get file record
	function get_file_info($id)
	{
		return $this->db->where('id', $id)->get('digital_products')->row();
	}
	
	// Verify upload path
	function verify_file_path()
	{
		return is_writable('uploads/digital_products');
	}
	
	// Verify file content
	function verify_content($filename)
	{
		return file_exists('uploads/digital_products'.'/'.$filename);
	}
	
	// Save/Update file record
	function save($data)
	{
		if(isset($data['id']))
		{
			$this->db->where('id', $data['id'])->update('digital_products', $data);
			return $data['id'];
		} else {
			$this->db->insert('digital_products', $data);
			return $this->db->insert_id();
		}
	}
	
	// Add product association
	function associate($file_id, $product_id)
	{
		$this->db->insert('products_files', array('product_id'=>$product_id, 'file_id'=>$file_id));
	}
	
	// Remove product association (all or by product)
	function disassociate($file_id, $product_id=false)
	{
		
		if($product_id)
		{
			$data['product_id'] = $product_id;
		}
		if($file_id)
		{
			$data['file_id']	= $file_id;
		}
		$this->db->where($data)->delete('products_files');
	}
	
	function get_associations_by_file($id)
	{
		return $this->db->where('file_id', $id)->get('products_files')->result();
	}
	
	function get_associations_by_product($product_id)
	{
		return $this->db->where('product_id', $product_id)->get('products_files')->result();
	}
	
	// Delete file record & content
	function delete($id)
	{
		$this->load->model('product_model');
		
		$info = $this->get_file_info($id);
		
		if(!$info)
		{
			return false;
		}
		
		// remove file
		if($this->verify_content($info->filename))
		{
			unlink('uploads/digital_products/'.$info->filename);
		}
		
		// Disable products that are associated with this file
		//  to prevent users purchasing a product with deleted media
		$product_associations  = $this->get_associations_by_file($id);
		foreach($product_associations as $p)
		{
			$save['id']			= $p->product_id;
			$save['enabled']	= 0;
			$this->product_model->save($save);
		}
		
		// Remove db associations
		$this->db->where('id', $id)->delete('digital_products');
		$this->disassociate($id);
	}
	
	// Accepts an array of file lists for products purchased
	//  and sets up the list of available downloads for the customer
	//  uses customer id if available, also creates a package code
	//  that can be sent to non registered customers
	function add_download_package($package, $order_id)
	{
		// get customer stuff
		$customer = $this->go_cart->customer();
		if(!empty($customer['id']))
		{
			$new_package['customer_id'] = $customer['id'];
		} else {
			$new_package['customer_id'] = 0;
		}
		
		$new_package['order_id'] = $order_id;
		$new_package['code']	= generate_code();
		
		// save master package record
		$this->db->insert('download_packages',$new_package);
		
		$package_id = $this->db->insert_id();
		
		// save the db data here
		$files_list = array();
		
		// use this to prevent inserting duplicates
		// in case a file is shared across products
		$ids = array();
		
		// build files records list
		foreach($package as $product_list)
		{
			foreach($product_list as $f)
			{
				if(!isset($ids[$f->file_id]))
				{
					$file['package_id'] = $package_id;
					$file['file_id'] = $f->file_id;
					$file['link'] = md5($f->file_id . time() . $new_package['customer_id']); // create a unique download key for each file
					
					$files_list[] = $file;
				}
			}
		}
		
		$this->db->insert_batch('download_package_files', $files_list);
		
		// save the master record to include links in the order email
		$this->go_cart->save_order_downloads($new_package);
	}
	
	// Retrieve user's download packages
	//  send back an array indexed by order number
	function get_user_downloads($customer_id)
	{
		$result = $this->db->where('customer_id', $customer_id)->get('download_packages')->result();
		
		$downloads = array();
		foreach($result as $r)
		{
			$downloads[$r->order_id] = $this->get_package_files($r->id); 
		}
		
		return $downloads;
	}
	
	// Retrieve non-member download by code
	//   format array exactly as by user
	function get_downloads_by_code($code)
	{
		$row =  $this->db->where('code', $code)->get('download_packages')->row();
		
		if($row)
		{
			return array(	
							$row->order_id => $this->get_package_files($row->id)
						);
		}
	}
	
	// get the files in a package
	function get_package_files($package_id)
	{
		
		return $this->db->select('*')
						->from('download_package_files as a')
						->join('digital_products as b', 'a.file_id=b.id')
						->where('package_id', $package_id)
						->get()
						->result();
	}
	
	// get file info for download by the link code
	//  increment the download counter
	function get_file_info_by_link($link)
	{
		
		$record = $this->db->from('digital_products as a')
						->join('download_package_files as b', 'a.id=b.file_id')
						->where('link', $link)
						->get()
						->row();
						
		return $record;
		
	}
	
	
	function touch_download($link)
	{
		$this->db->where('link', $link)->set('downloads','downloads+1', false)->update('download_package_files');
	}
}