<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_gocart2_3 extends CI_migration {
	
	public function up()
	{
		//We replaced the sessions library in CI and no longer need this table.
		if($this->db->table_exists('sessions'))
		{
			$this->dbforge->drop_table('sessions');
		}
		//eliminate heard_about from orders tbale
		if($this->db->field_exists('postcode_required', 'countries'))
		{
			$fields	= array('postcode_required'=>array('name'=>'zip_required', 'type'=>'int','constraint'=>1));
			$this->dbforge->modify_column('countries', $fields);
		}
		
		//if the banner_collections table does not exist, run the migration
		if (!$this->db->table_exists('banner_collections'))
		{
			//create banner collections
			$this->dbforge->add_field(array(
				'banner_collection_id'	=> array(
					'type'				=> 'INT',
					'constraint'		=> 4,
					'unsigned'			=> TRUE,
					'auto_increment'	=> TRUE
				),
				'name'					=> array(
					'type'				=> 'varchar',
					'constraint'		=> 32
				)
			));
				
			$this->dbforge->add_key('banner_collection_id', TRUE);
			$this->dbforge->create_table('banner_collections', TRUE);
		
			//create 2 collections to replace the current Banners & Boxes
			$this->db->insert('banner_collections', array('banner_collection_id'=>1, 'name'=>'Homepage Banners'));
			$this->db->insert('banner_collections', array('banner_collection_id'=>2, 'name'=>'Homepage Boxes'));
		}
		
		if ($this->db->field_exists('id', 'banners'))
		{
			//update banner table
			//individual banners
			$fields	= array(
				'id'					=> array(
					'name'				=> 'banner_id',
					'type'				=> 'INT',
					'constraint'		=> 9,
					'unsigned'			=> TRUE,
					'auto_increment'	=> TRUE
				),
				'title'					=> array(
					'name'				=> 'name',
					'type'				=> 'varchar',
					'constraint'		=> 128
				),
				'enable_on'				=> array(
					'name'				=> 'enable_date',
					'type'				=> 'date',
					'null'				=> TRUE
				),
				'disable_on'			=> array(
					'name'				=> 'disable_date',
					'type'				=> 'date',
					'null'				=> TRUE
				),
				'link'					=> array(
					'type'				=> 'varchar',
					'constraint'		=> 255
				)
			);
			$this->dbforge->modify_column('banners', $fields);
			
			//add the new column
			$fields	= array(
			'banner_collection_id'	=> array(
				'type'				=> 'INT',
				'constraint'		=> 4,
				'unsigned'			=> TRUE
				)
			);
			$this->dbforge->add_column('banners', $fields);
		
			//put them all in the homepage banners collection
			$this->db->where('id !=', 0)->update('banners', array('banner_collection_id'=>1));
		}

		if ($this->db->table_exists('boxes'))
		{
			//move boxes over and delete the field.
			$boxes = $this->db->get('boxes')->result();
			if($boxes)
			{
				foreach($boxes as $b)
				{
					$new_box = array();
				
					$new_box['name']					= $b->title;
					$new_box['enable_date']				= $b->enable_on;
					$new_box['disable_date']			= $b->disable_on;
					$new_box['banner_collection_id']	= 2;
					$new_box['link']					= $b->link;
					$new_box['image']					= $b->image;
					$new_box['sequence']				= $b->sequence;
					$new_box['new_window']				= $b->new_window;
				
					//put the old boxes into the updated banners table with a foreign key pointing at the homepage box collection
					$this->db->insert('banners', $new_box);
				}
			}
			//drop the boxes table
			$this->dbforge->drop_table('boxes');
		}

		// Create the filters and filter products tables
		if(!$this->db->table_exists('filters'))
		{
			$this->dbforge->add_field(array(
				'id' => array(
							'type' => 'int',
							'constraint' => 11,
							'auto_increment' => true
						),
				'parent_id' => array(
							'type' => 'int',
							'constraint' => 10,
							'unsigned' => true,
							'null' => false
						),
				'name' => array(
							'type' => 'varchar',
							'constraint' => 64, 
							'null' => false
						),
				'slug' => array(
							'type' => 'varchar',
							'constraint' => 64,
							'null' => false
						),
				'route_id' => array(
							'type' => 'int',
							'constraint' => 11,
							'null' => false
						),
				'sequence' => array(
							'type' => 'int',
							'constraint' => 10,
							'unsigned' => true,
							'null' => false
						),
				'seo_title' => array(
							'type' => 'text',
							'null' => true
						),
  				'meta' => array(
  							'type' => 'text',
  							'null' => true,
						)
  			));

  			$this->dbforge->add_key('id', true);
			$this->dbforge->create_table('filters');


			$this->dbforge->add_field(array(
				'product_id' => array(
							'type' => 'int',
							'constraint' => 10,
							'unsigned' => true, 
							'null' => false
						),
  				'filter_id' => array(
  							'type' => 'int',
  							'constraint' => 10, 
  							'unsigned' => true,
  							'null' => false
  						),
  				'sequence' => array( 
  							'type' => 'int',
  							'constraint' => 10,
  							'unsigned' => true, 
  							'null' => false
  						)
			));
			$this->dbforge->create_table('filter_products', true);
		}

		if (!$this->db->field_exists('enabled', 'categories'))
		{
			// Add the enabled field to categories
	    	$fields  = array(
	    	  'enabled'  => array(
	    	    'type'      => 'tinyint',
	    	    'constraint'  => 1,
	    	    'default'    => 1
	    	  )
	    	);
	    	$this->dbforge->add_column('categories', $fields);
	    }
	}
	
	public function down()
	{

		//put the session table back if rolling back
		if(!$this->db->table_exists('sessions'))
		{
			$this->dbforge->add_field(array(
				'session_id' => array(
					'type' => 'varchar',
					'constraint' => 40, 
					'null' => false,
					'default' => '0'
					),
				'ip_address' => array(
					'type' => 'varchar',
					'constraint' => 45, 
					'null' => false,
					'default' => '0'
					),
				'user_agent' => array(
					'type' => 'varchar',
					'constraint' => 120, 
					'null' => true
					),
				'last_activity' => array(
					'type' => 'int',
					'constraint' => 10, 
					'unsigned' => true,
					'null' => false,
					'default' => '0'
					),
				'user_data' => array(
					'type' => 'text',
					'null' => false
					)
			));

			$this->dbforge->add_key('session_id', true);
			$this->dbforge->add_key('last_activity');
			$this->dbforge->create_table('sessions', true);
		}

		if($this->db->field_exists('zip_required', 'countries'))
		{
			$fields	= array('zip_required'=>array('name'=>'postcode_required', 'type'=>'int', 'constraint'=>1));
			$this->dbforge->modify_column('countries', $fields);
		}
		
		//moving down to the old banner and box system is destructive.
		if ($this->db->table_exists('banner_collections'))
		{
			//drop the boxes table
			$this->dbforge->drop_table('banner_collections');
		}

		if ($this->db->table_exists('banners'))
		{
			$this->dbforge->drop_table('banners');
			
			
			//create the old banners table
			//individual banners
			$this->dbforge->add_field(array(
				'id'					=> array(
					'type'				=> 'INT',
					'constraint'		=> 11,
					'unsigned'			=> TRUE,
					'auto_increment'	=> TRUE
				),
				'sequence'				=> array(
					'type'				=> 'INT',
					'constraint'		=> 11,
					'unsigned'			=> TRUE,
				),
				'title'					=> array(
					'type'				=> 'varchar',
					'constraint'		=> 128
				),
				'enable_on'			=> array(
					'type'				=> 'date',
					'null'				=> TRUE
				),
				'disable_on'			=> array(
					'type'				=> 'date',
					'null'				=> TRUE
				),
				'image'					=> array(
					'type'				=> 'varchar',
					'constraint'		=> 64
				),
				'link'					=> array(
					'type'				=> 'varchar',
					'constraint'		=> 255,
				),
				'new_window'			=> array(
					'type'				=> 'tinyint',
					'constraint'		=> 1,
					'default'			=> 0
				)
			));
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table('banners', TRUE);
		}
		
		if (!$this->db->table_exists('boxes'))
		{	
			//create table fox boxes
			$this->dbforge->add_field(array(
				'id'					=> array(
					'type'				=> 'INT',
					'constraint'		=> 11,
					'unsigned'			=> TRUE,
					'auto_increment'	=> TRUE
				),
				'sequence'				=> array(
					'type'				=> 'INT',
					'constraint'		=> 11,
					'unsigned'			=> TRUE,
				),
				'title'					=> array(
					'type'				=> 'varchar',
					'constraint'		=> 128
				),
				'enable_on'			=> array(
					'type'				=> 'date',
					'null'				=> TRUE
				),
				'disable_on'			=> array(
					'type'				=> 'date',
					'null'				=> TRUE
				),
				'image'					=> array(
					'type'				=> 'varchar',
					'constraint'		=> 64
				),
				'link'					=> array(
					'type'				=> 'varchar',
					'constraint'		=> 255,
				),
				'new_window'			=> array(
					'type'				=> 'tinyint',
					'constraint'		=> 1,
					'default'			=> 0
				)
			));
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table('boxes', TRUE);
		}
		
		// Drop filters tables
		if (!$this->db->table_exists('filters'))
		{	
			$this->dbforge->drop_table('filters');
			$this->dbforge->drop_table('filter_products');
		}

		// drop enabled field on categories
		if($this->db->field_exists('enabled', 'categories'))
		{
			$this->dbforge->drop_column('categories', 'enabled');
		}
	}
	
}