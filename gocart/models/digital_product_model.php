<?php

Class Digital_Product_Model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
	}
	
	// Return blank record array
	function new_file()
	{
		return array(
					'id'=>'',
					'filename'=>'',
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
		return is_writable($this->config->item('digital_products_path'));
	}
	
	// Verify file content
	function verify_content($filename)
	{
		return file_exists($this->config->item('digital_products_path').'/'.$filename);
	}
	
	// Save/Update file record
	function save($data)
	{
		if(isset($data['id']))
		{
			$this->db->where('id', $data['id'])->update('digital_products');
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
			unlink($this->config->item('digital_products_path').'/'.$info->filename);
		}
		
		// Disable products that are associated with this file
		//  to prevent users purchasing a product with deleted media
		$product_associations  = $this->get_associations_by_file($id);
		foreach($product_associations as $p)
		{
			$save['id'] = $p->id;
			$save['enabled'] = 0;
			$this->product_model->save($save);
		}
		
		// Remove db associations
		$this->db->where('id', $id)->delete('digital_products');
		$this->disassociate($id);
	}
}