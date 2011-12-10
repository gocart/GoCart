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
		return $this->db->where('id', $id)->get('digital_products')->result()->row();
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
	
	// Delete file record
	function delete($id)
	{
		$info = $this->get_file_info($id);
		
		// remove file
		unlink($this->config->item('digital_products_path').'/'.$info->filename);
		
		$this->db->where('id', $id)->delete('digital_products');
		$this->db->where('file_id', $id)->delete('products_files');
	}
}