<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_gocart2_3 extends CI_migration {
    
    public function up()
    {
        //We replaced the sessions library in CI and no longer need this table.
        if($this->db->table_exists('sessions'))
        {
            $this->dbforge->drop_table('sessions');
        }

        //rename postcode_required from orders tbale
        if($this->db->field_exists('postcode_required', 'countries'))
        {
            $fields = array('postcode_required'=>array('name'=>'zip_required', 'type'=>'int','constraint'=>1));
            $this->dbforge->modify_column('countries', $fields);
        }
        
        //if the banner_collections table does not exist, run the migration
        if (!$this->db->table_exists('banner_collections'))
        {
            //create banner collections
            $this->dbforge->add_field(array(
                'banner_collection_id' => array(
                    'type' => 'INT',
                    'constraint' => 4,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ),
                'name' => array(
                    'type' => 'varchar',
                    'constraint' => 32
                )
            ));
                
            $this->dbforge->add_key('banner_collection_id', TRUE);
            $this->dbforge->create_table('banner_collections', TRUE);
        
            //create 2 collections to replace the current Banners & Boxes
            $records = array(array('name'=>'Homepage Banners'), array('name'=>'Homepage Boxes'));
            $this->db->insert_batch('banner_collections', $records);
        }
        
        if(!$this->db->table_exists('banners'))
        {
            $this->dbforge->add_field(array(
                'banner_id' => array(
                            'type' => 'int',
                            'constraint' => 9,
                            'unsigned' => true,
                            'auto_increment' => true
                            ),
                'banner_collection_id' => array(
                            'type' => 'int',
                            'constraint' => 9,
                            'unsigned' => true,
                            'null' => false
                            ),
                'name' => array(
                            'type' => 'varchar',
                            'constraint' => 128,
                            'null' => false
                            ),
                'enable_date' => array(
                            'type' => 'date',
                            'null' => false
                            ),
                'disable_date' => array(
                            'type' => 'date',
                            'null' => false
                            ),
                'image' => array(
                            'type' => 'varchar',
                            'constraint' => 64,
                            'null' => false
                            ),
                'link' => array(
                            'type' => 'varchar',
                            'constraint' => 128,
                            'null' => true
                            ),
                'new_window' => array(
                            'type' => 'tinyint',
                            'constraint' => 1,
                            'null' => false,
                            'default' => 0
                            ),
                'sequence' => array(
                            'type' => 'int',
                            'constraint' => 11,
                            'null' => false,
                            'default' => 0
                            )
            ));

            $this->dbforge->add_key('banner_id', true);
            $this->dbforge->create_table('banners', true);
        }
        
        if ($this->db->field_exists('id', 'banners'))
        {
            //update banner table
            //individual banners
            $fields = array(
                'id' => array(
                    'name' => 'banner_id',
                    'type' => 'INT',
                    'constraint' => 9,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ),
                'title' => array(
                    'name' => 'name',
                    'type' => 'varchar',
                    'constraint' => 128
                ),
                'enable_on' => array(
                    'name' => 'enable_date',
                    'type' => 'date',
                    'null' => TRUE
                ),
                'disable_on' => array(
                    'name' => 'disable_date',
                    'type' => 'date',
                    'null' => TRUE
                ),
                'link' => array(
                    'type' => 'varchar',
                    'constraint' => 255
                )
            );
            $this->dbforge->modify_column('banners', $fields);
            
            //add the new column
            $fields = array(
            'banner_collection_id' => array(
                'type' => 'INT',
                'constraint' => 4,
                'unsigned' => TRUE
                )
            );
            $this->dbforge->add_column('banners', $fields);
        
            //put them all in the homepage banners collection
            $this->db->where('banner_id !=', 0)->update('banners', array('banner_collection_id'=>1));
        }

        if ($this->db->table_exists('boxes'))
        {
            //move boxes over and delete the field.
            $boxes = $this->db->get('boxes')->result();
            if($boxes)
            {
                foreach($boxes as $b)
                {
                    $new_box = array(
                        'name' => $b->title,
                        'enable_date' => $b->enable_on,
                        'disable_date' => $b->disable_on,
                        'banner_collection_id' => 2,
                        'link' => $b->link,
                        'image' => $b->image,
                        'sequence' => $b->sequence,
                        'new_window' => $b->new_window
                    );
                
                    //put the old boxes into the updated banners table with a foreign key pointing at the homepage box collection
                    $this->db->insert('banners', $new_box);
                }
            }
            //drop the boxes table
            $this->dbforge->drop_table('boxes');
        }
        
        if (!$this->db->field_exists('enabled', 'categories'))
        {
            // Add the enabled field to categories
            $fields  = array(
                'enabled' => array(
                'type' => 'tinyint',
                'constraint' => 1,
                'default' => 1
              )
            );
            $this->dbforge->add_column('categories', $fields);
        }

        //add username field to admin table
        if (!$this->db->field_exists('username', 'admin'))
        {
            // Add the enabled field to categories
            $fields  = array(
                'username' => array(
                'type' => 'varchar',
                'constraint' => 32,
                'default' => '',
                'null' => false
              )
            );
            $this->dbforge->add_column('admin', $fields);

            //set the username to be the email by default so people can continue to login
            $admins = $this->db->get('admin')->result();
            foreach($admins as $admin)
            {
                $admin->username = $admin->email;
                $this->db->where('id', $admin->id)->update('admin', $admin);
            }
        }


        //move config to the database if it exists, otherwise enter default information
        
        //load in the settings model
        $this->load->model('settings_model');
        $settings = $this->settings_model->get_settings('gocart');

        if(empty($settings))
        {
            if(file_exists(FCPATH.'gocart/config/gocart.php'))
            {
                include(FCPATH.'gocart/config/gocart.php');
                $config['order_statuses'] = json_encode($config['order_statuses']);

                //set locale to default
                $config['locale'] = locale_get_default();
                $config['currency_iso'] = $config['currency'];

                unset($config['currency']);
                unset($config['currency_symbol']);
                unset($config['currency_symbol_side']);
                unset($config['currency_decimal']);
                unset($config['currency_thousands_separator']);
                
                
            }
            else
            {
                $config['theme'] = 'default';
                $config['ssl_support'] = false;
                $config['company_name'] = '';
                $config['address1'] = '';
                $config['address2'] = '';
                $config['country'] = '';
                $config['country_id'] = '';
                $config['city'] = '';
                $config['zone_id'] = '';
                $config['state'] = '';
                $config['zip'] = '';
                $config['email'] = '';
                $config['locale'] = locale_get_default();
                $config['currency_iso'] = 'USD';
                $config['weight_unit'] = 'LB';
                $config['dimension_unit'] = 'IN';
                $config['require_shipping'] = true;
                $config['site_logo'] = '/images/logo.png';
                $config['admin_folder'] = 'admin';
                $config['new_customer_status'] = true;
                $config['require_login'] = false;
                $config['order_status'] = 'Order Placed';
                $config['order_statuses'] = json_encode(array(
                                                'Order Placed' => 'Order Placed',
                                                'Pending' => 'Pending',
                                                'Processing' => 'Processing',
                                                'Shipped' => 'Shipped',
                                                'On Hold' => 'On Hold',
                                                'Cancelled' => 'Cancelled',
                                                'Delivered' => 'Delivered'
                                            ));
                $config['inventory_enabled'] = false;
                $config['allow_os_purchase'] = true;
                $config['tax_address'] = 'ship';
                $config['tax_shipping'] = false;
            }

            //submit the settings
            $this->settings_model->save_settings('gocart', $config);

            //kill the config var
            unset($config);
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
            $fields = array('zip_required'=>array('name'=>'postcode_required', 'type'=>'int', 'constraint'=>1));
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
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ),
                'sequence' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                ),
                'title' => array(
                    'type' => 'varchar',
                    'constraint' => 128
                ),
                'enable_on' => array(
                    'type' => 'date',
                    'null' => TRUE
                ),
                'disable_on' => array(
                    'type' => 'date',
                    'null' => TRUE
                ),
                'image' => array(
                    'type' => 'varchar',
                    'constraint' => 64
                ),
                'link' => array(
                    'type' => 'varchar',
                    'constraint' => 255,
                ),
                'new_window' => array(
                    'type' => 'tinyint',
                    'constraint' => 1,
                    'default' => 0
                )
            ));
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('banners', TRUE);
        }
        
        if (!$this->db->table_exists('boxes'))
        {   
            //create table fox boxes
            $this->dbforge->add_field(array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ),
                'sequence' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                ),
                'title' => array(
                    'type' => 'varchar',
                    'constraint' => 128
                ),
                'enable_on' => array(
                    'type' => 'date',
                    'null' => TRUE
                ),
                'disable_on' => array(
                    'type' => 'date',
                    'null' => TRUE
                ),
                'image' => array(
                    'type' => 'varchar',
                    'constraint' => 64
                ),
                'link' => array(
                    'type' => 'varchar',
                    'constraint' => 255,
                ),
                'new_window' => array(
                    'type' => 'tinyint',
                    'constraint' => 1,
                    'default' => 0
                )
            ));
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('boxes', TRUE);
        }
      
        // drop enabled field on categories
        if($this->db->field_exists('enabled', 'categories'))
        {
            $this->dbforge->drop_column('categories', 'enabled');
        }

        //drop the admin username field
        if($this->db->field_exists('username', 'admin'))
        {
            $this->dbforge->drop_column('admin', 'username');
        }

        //kill the settings from the DB
        $this->load->model('settings_model');
        $this->settings_model->delete_settings('gocart');
    }   
}