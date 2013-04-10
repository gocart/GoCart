<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_gocart extends CI_migration {
	
	public function up()
	{
		//eliminate heard_about from orders tbale
		if($this->db->field_exists('postcode_required', 'countries'))
		{
			$fields	= array('postcode_required'=>array('name'=>'zip_required'));
			$this->dbforge->modify_column('countries', $fields);
		}
	}
	
	public function down()
	{
		if($this->db->field_exists('zip_required', 'countries'))
		{
			$fields	= array('zip_required'=>array('name'=>'postcode_required'));
			$this->dbforge->modify_column('countries', $fields);
		}
		
	}
	
}
