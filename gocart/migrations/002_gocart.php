<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_gocart extends CI_migration {
	
	public function up()
	{
		//eliminate heard_about from orders tbale
		if($this->db->field_exists('heard_about', 'orders'))
		{
			$this->dbforge->drop_column('orders', 'heard_about');
		}
		
		
		//update the notes field to be NULL by default
		$fields	= array('notes'=>array(  'type'			=> 'text'
										,'null'			=> TRUE
										)
						);
		$this->dbforge->modify_column('orders', $fields);
		
		//update session ip_address to support ipv6 length
		$fields	= array('ip_address'=>array( 'type'			=> 'VARCHAR'
											,'constraint'	=> '45'
											)
						);
		$this->dbforge->modify_column('sessions', $fields);
		
	}
	
	public function down()
	{
		//none of the changes should effect the product
	}
	
}
