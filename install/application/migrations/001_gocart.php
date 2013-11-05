<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Gocart extends CI_Migration {
	
	public function up()
	{

		$this->dbforge->add_field(array(
				'id' => array(  
							'type' => 'int',
							'constraint' => 9,
							'unsigned' => true,
							'auto_increment' => true
							),
				'firstname' => array(
							'type' => 'varchar',
							'constraint' => 32,
							'null' => true
							),
				'lastname' => array(
							'type' => 'varchar',
							'constraint' => 32,
							'null' => true
							),
				'email' => array(
							'type' => 'varchar',
							'constraint' => 128,
							'null' => false
							),
				'access' => array(
							'type' => 'varchar',
							'constraint' => 11,
							'null' => false
							),
				'password' => array(
							'type' => 'varchar',
							'constraint' => 40,
							'null' => false
							)
		));

		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('admin', true);


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
				'title' => array(
							'type' => 'varchar',
							'constraint' => 128,
							'null' => false
							),
				'enable_on' => array(
							'type' => 'date',
							'null' => false
							),
				'disable_on' => array(
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


		$this->dbforge->add_field(array(
				'banner_collection_id' => array(
							'type' => 'int',
							'constraint' => 4,
							'unsigned' => true,
							'auto_increment' => true
							),
				'name' => array(
							'type' => 'varchar',
							'constraint' => 32
							)
		));

		$this->dbforge->add_key('banner_collection_id', true);
		$this->dbforge->create_table('banner_collections', true);


		$records = array(array('name'=>'Homepage Banners'), array('name'=>'Homepage Boxes'));
		$this->db->insert_batch('banner_collections', $records);


		if (!$this->db->table_exists('canned_messages'))
		{

				$this->dbforge->add_field(array(
						'id' => array(
									'type' => 'int',
									'constraint' => 9,
									'unsigned' => true,
									'auto_increment' => true
									),
						'deletable' => array(
									'type' => 'tinyint',
									'constraint' => 1,
									'null' => false,
									'default' => 1
									),
						'type' => array(
									'type' => 'enum',
									'constraint' => array('internal', 'order'),
									'null' => true
									),
						'name' => array(
									'type' => 'varchar',
									'constraint' => 50,
									'null' => true
									),
						'subject' => array(
									'type' => 'varchar',
									'constraint' => 100,
									'null' => true
									),
						'content' => array(
									'type' => 'text'
									)
				));

				$this->dbforge->add_key('id', true);
				$this->dbforge->create_table('canned_messages', true);

				$records = array( 
						array('id'=>'1', 
						'deletable'=>'0',
						'type'=>'internal', 
						'name'=>'Gift Card Email Notification',
						'subject'=>'You have received a gift card from {from} to {site_name}!',
						'content'=>'<p>Congrats on your new {site_name} gift card!<br><br>Card Code: {code}<br>Gift Amount: {amount}<br>From: {from}<br>Redeemable at: {site_name} {url}</p><p>Personal Message from Sender:<br>{personal_message}</p><p>Be sure to save this email in a safe place. Your gift card code is as good as cash.</p>'), 
						array('id'=>'3', 
						'deletable'=>'0', 
						'type'=>'order', 
						'name'=>'Order Shipped Notification', 
						'subject'=>'Your order has shipped (order: {order_number})!', 
						'content'=>'<p>Dear {customer_name},</p>\n<p>Thank you for your purchase at {site_name}!</p>\n<p>This message is to inform you that your order ({order_number}) has been shipped.</p>\n<p>Enjoy your purchase!</p>'), 
						array('id'=>'6', 
						'deletable'=>'0', 
						'type'=>'internal', 
						'name'=>'Registration Confirmation', 
						'subject'=>'Thank you for registring at {site_name}!', 
						'content'=>'<p>Dear {customer_name},</p>\n<p>Thanks for registering at {site_name}. Your participation is appreciated. After registering, you should have been logged in automatically. You may access your account by using the email address this notice was sent to, and the password you signed up with. If you forget your password, on the login page, click the \"forgot password\" link and you can get a new password generated and sent to you.<br /><br />Thanks,<br />{site_name}</p>'), 
						array('id'=>'7', 
						'deletable'=>'0', 
						'type'=>'internal', 
						'name'=>'Order Submitted Confirmation', 
						'subject'=>'Thank you for your order with {site_name}!', 
						'content'=>'<p>Dear {customer_name},</p>\n<p>Thank you for your order with {site_name}!</p>\n<p>{order_summary}</p>'), 
						array('id'=>'8', 
						'deletable'=>'0', 
						'type'=>'order', 
						'name'=>'Digital Download Notification', 
						'subject'=>'Digital Download Notification', 
						'content'=>'<p style=\"text-align: center;\"><strong>{download_link}</strong></p>')
				);

				$this->db->insert_batch('canned_messages', $records);
		}


		$this->dbforge->add_field(array(
						'id' => array(
								'type' => 'int',
								'constraint' => 9,
								'unsigned' => true,
								'auto_increment' => true
									),
						'parent_id' => array(
								'type' => 'int',
								'constraint' => 9,
								'unsigned' => true,
								'null' => false,
								'default' => 0
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
								'constraint' => 9,
								'null' => false
									),
						'description' => array(
								'type' => 'text',
								'null' => false
									),
						'excerpt' => array(
								'type' => 'text',
								'null' => false
									),
						'sequence' => array(
								'type' => 'int',
								'constraint' => 5,
								'unsigned' => true,
								'default' => 0,
								'null' => false
									),
						'image' => array(
								'type' => 'varchar',
								'constraint' => 255,
								'null' => true
									),
						'seo_title' => array(
								'type' => 'text',
								'null' => false
									),
						'meta' => array(
								'type' => 'text',
								'null' => false
									)
		));

		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('categories', true);

		$this->dbforge->add_field(array(
						'product_id' => array(
								'type' => 'int',
								'constraint' => 9,
								'unsigned' => true
								),
						'category_id' => array(
								'type' => 'int',
								'constraint' => 9,
								'unsigned' => true
								),
						'sequence' => array(
								'type' => 'int',
								'constraint' => 5,
								'unsigned' => true,
								'default' => 0
								)
		));

		$this->dbforge->add_key(array('product_id', 'category_id'), true);
		$this->dbforge->create_table('category_products', true);


		if(!$this->db->table_exists('countries'))
		{
			$this->dbforge->add_field(array(
					'id' => array(
						'type' => 'int',
						'constraint' => 9,
						'unsigned' => true,
						'auto_increment' => true
						),
					'sequence' => array(
						'type' => 'int',
						'constraint' => 11,
						'default' => 0
						),
					'name' => array(
						'type' => 'varchar',
						'constraint' => 128,
						'null' => false
						),
					'iso_code_2' => array(
						'type' => 'varchar',
						'constraint' => 2 ,
						'null' => false
						),
					'iso_code_3' => array(
						'type' => 'varchar',
						'constraint' => 3 ,
						'null' => false
						),
					'address_format' => array(
						'type' => 'text'
						),
					'zip_required' => array(
						'type' => 'int',
						'constraint' => 1 ,
						'null' => false,
						'default' => 0
						),
					'status' => array(
						'type' => 'int',
						'constraint' => 1 ,
						'null' => false, 
						'default' => 1
						),
					'tax' => array(
						'type' => 'float',
						'constraint' => array(10,2),
						'null' => false,
						'default' => 0
						)
					));

			$this->dbforge->add_key('id', true);
			$this->dbforge->create_table('countries');

			// Seed

			$records = $this->load->view('templates/countries.txt', array(), true);
			$records = explode("\n", $records);

			$batch = array();
			foreach($records as $r)
			{
				$r = explode('|', $r);

				$batch[] = array('id'=>$r[0], 
								 'sequence'=>$r[1], 
								 'name'=>$r[2], 
								 'iso_code_2'=>$r[3],
								 'iso_code_3'=>$r[4], 
								 'address_format'=> str_replace('\n', "\n", $r[5]), // convert string newline to literal
								 'zip_required'=>$r[6],
								 'status'=>$r[7],
								 'tax'=>$r[8]
								 );
			}

			$this->db->insert_batch('countries', $batch);

		}


		$this->dbforge->add_field(array(
			'id' =>array(
				'type'=>'int',
				'constraint' => 9,
				'unsigned' => true,
				'auto_increment' => true
				),
			'zone_id' =>array(
				'type'=>'int',
				'constraint' => 9,
				'unsigned' => true,
				'null' => false
				),
			'code' =>array(
				'type'=>'varchar',
				'constraint' => 15,
				'null' => false
				),
			'tax' =>array(
				'type'=>'float',
				'constraint' => array(10,2),
				'null' => false,
				'default' => 0
				)
		));

		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('country_zone_areas', true);


		if(!$this->db->table_exists('country_zones'))
		{	

			$this->dbforge->add_field(array(
				'id' => array( 
					'type' => 'int', 
					'constraint' =>	11, 
					'unsigned' => true,
					'auto_increment' => true
					),
				'country_id' => array( 
					'type' => 'int', 
					'constraint' =>	9,
					'unsigned' => true, 
					'null' => false
					),
				'code' => array( 
					'type' => 'varchar', 
					'constraint' =>	32, 
					'null' => true
					),
				'name' => array( 
					'type' => 'varchar', 
					'constraint' =>	128, 
					'null' => false
					),
				'status' => array( 
					'type' => 'int', 
					'constraint' =>	1, 
					'null' => false,
					'default' => 1
					),
				'tax' => array( 
					'type' => 'float', 
					'constraint' => array(10,2),
					'null' => false
					)
			));

			$this->dbforge->add_key('id', true);
			$this->dbforge->create_table('country_zones');

			// Seed

			$records = $this->load->view('templates/country_zones.txt', array(), true);
			$records = explode("\n", $records);

			foreach($records as $r)
			{
				$r = explode('|', $r);

				$insert = array('id'=>$r[0], 
								'country_id' => $r[1],
								'code' => $r[2],
								'name' => $r[3],
								'status' => $r[4],
								'tax' => $r[5]
								 );

				// Run this one one at a time, since the list is probably too large for a batch
				$this->db->insert('country_zones', $insert);
			}

		}


		$this->dbforge->add_field(array(
				'id' => array(
					'type' => 'int',
					'constraint' => 10,
					'unsigned' => true,
					'auto_increment' => true
					),
				'code' => array(
					'type' => 'varchar',
					'constraint' => 50,
					'null' => false
					),
				'start_date' => array(
					'type' => 'date',
					'null' => false
					),
				'end_date' => array(
					'type' => 'date',
					'null' => false
					),
				'whole_order_coupon' => array(
					'type' => 'tinyint',
					'constraint' => 1,
					'null' => false
					),
				'max_product_instances' => array(
					'type' => 'mediumint',
					'constraint' => 8,
					'unsigned' => true,
					'null' => false,
					'default' => 0
					),
				'max_uses' => array(
					'type' => 'mediumint',
					'constraint' => 8,
					'unsigned' => true,
					'default' => 0
					),
				'num_uses' => array(
					'type' => 'mediumint',
					'constraint' => 8,
					'unsigned' => true,
					'null' => false
					),
				'reduction_target' => array(
					'type' => 'varchar',
					'constraint' => 8,
					'null' => false
					),
				'reduction_type' => array(
					'type' => 'varchar',
					'constraint' => 10,
					'null' => false
					),
				'reduction_amount' => array(
					'type' => 'float',
					'constraint' => array(10,2),
					'null' => false
					)
			));

		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('coupons', true);


		$this->dbforge->add_field(array(
				'coupon_id' => array(
						'type' =>  'int',
						'constraint' => 9,
						'unsigned' => true,
						'null' => false
						),
				'product_id' => array(
						'type' =>  'int',
						'constraint' => 9,
						'unsigned' => true,
						'null' => false
						),
				'sequence' => array(
						'type' =>  'int',
						'constraint' => 9,
						'unsigned' => true,
						'null' => false
						)
		));


		$this->dbforge->add_key(array('coupon_id', 'product_id'));
		$this->dbforge->create_table('coupons_products', true);



		if(!$this->db->table_exists('customer_groups'))
		{

			$this->dbforge->add_field(array(
					'id' => array(
						'type' => 'int',
						'constraint' => 9,
						'unsigned' => true,
						'auto_increment' => true
						),
					'discount' => array(
						'type' => 'float',
						'constraint' => array(10,2),
						'null' => true
						),
					'name' => array(
						'type' => 'varchar',
						'constraint' => 50,
						'null' => true
						),
					'discount_type' => array(
						'type' => 'enum',
						'constraint' =>  array('fixed','percent'),
						'null' => false,
						'default' =>'percent'
						)
			));


			$this->dbforge->add_key('id', true);
			$this->dbforge->create_table('customer_groups');

			$this->db->insert('customer_groups', array('discount'=>0, 'name'=>'Shoppers', 'discount_type'=>'fixed'));

		}


		$this->dbforge->add_field(array(
				'id' => array( 
					'type'=> 'int',
					'constraint' => 9,
					'unsigned' => true,
					'auto_increment' => true
					 ),
				'firstname' => array( 
					'type'=> 'varchar',
					'constraint' => 32,
					'null' => false
					 ),
				'lastname' => array( 
					'type'=> 'varchar',
					'constraint' => 32,
					'null' => false
					 ),
				'email' => array( 
					'type'=> 'varchar',
					'constraint' => 128,
					'null' => false
					 ),
				'email_subscribe' => array( 
					'type'=> 'tinyint',
					'constraint' => 1,
					 'default' => '0'
					 ),
				'phone' => array( 
					'type'=> 'varchar',
					'constraint' => 32,
					'null' => false
					 ),
				'company' => array( 
					'type'=> 'varchar',
					'constraint' => 128,
					'null' => false
					 ),
				'default_billing_address' => array( 
					'type'=> 'int',
					'constraint' => 9
					 ),
				'default_shipping_address' => array( 
					'type'=> 'int',
					'constraint' => 9
					 ),
				'ship_to_bill_address' => array( 
					'type'=> 'enum',
					'constraint' => array('false','true'),
					'null' => false,
					'default' => 'true'
				),
				'password' => array( 
					'type'=> 'varchar',
					'constraint' => 40,
					'null' => false
					 ),
				'active' => array( 
					'type'=> 'tinyint',
					'constraint' => 1,
					'null' => false
					 ),
				'group_id' => array( 
					'type'=> 'int',
					'constraint' => 11,
					'null' => false,
					'default' => '1'
					 ),
				'confirmed' => array( 
					'type'=> 'tinyint',
					'constraint' => 1,
					'null' => false,
					'default' => '0'
					 )
		));

		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('customers', true);


		$this->dbforge->add_field(array(
				'id' => array(
					'type' => 'int',
					'constraint' => 9,
					'unsigned' => true,
					'auto_increment' => true
					),
				'customer_id' => array(
					'type' => 'int',
					'constraint' => 9,
					'unsigned' => true,
					),
				'entry_name' => array(
					'type' => 'varchar',
					'constraint' => 20,
					),
				'field_data' => array(
					'type' => 'text'
					)
		));

		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('customers_address_bank', true);


		$this->dbforge->add_field(array(
				'id' => array(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'auto_increment' => true
					),
				'filename' => array(
					'type' => 'varchar',
					'constraint' => 100,
					'null' => false
					),
				'max_downloads' => array(
					'type' => 'int',
					'constraint' => 11,
					'null' => false
					),
				'title' => array(
					'type' => 'varchar',
					'constraint' => 100,
					'null' => false
					),
				'version' => array(
					'type' => 'varchar',
					'constraint' => 150,
					'null' => false
					),
				'size' => array(
					'type' => 'varchar',
					'constraint' => 20,
					'null' => false
					)
				));

		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('digital_products', true);


		$this->dbforge->add_field(array(
				'package_id' => array(
					'type' => 'int',
					'constraint' => 9,
					'unsigned' => true,
					'null' => false
					),
				'file_id' => array(
					'type' => 'int',
					'constraint' => 9,
					'unsigned' => true,
					'null' => false
					),
				'downloads' => array(
					'type' => 'int',
					'constraint' => 5,
					'null' => false
					),
				'link' => array(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
					)
		));

		
		$this->dbforge->add_key('package_id');
		$this->dbforge->create_table('download_package_files', true);


		$this->dbforge->add_field(array(
					'id' => array(
						'type' => 'int',
						'constraint' => 9,
						'unsigned' => true,
						'auto_increment' => true
						),
					'order_id' => array(
						'type' => 'varchar',
						'constraint' => 60,
						'null' => false
						),
					'customer_id' => array(
						'type' => 'int',
						'constraint' => 9,
						'unsigned' => true,
						'null' => false
						),
					'code' => array(
						'type' => 'varchar',
						'constraint' => 16,
						'null' => false
						)
		));

		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('download_packages', true);


		$this->dbforge->add_field(array(
					'id' => array( 
						'type' => 'int',
						'constraint' => 9,
						'unsigned' => true,
						'auto_increment' => true
						),
					'order_number' => array( 
						'type' => 'varchar',
						'constraint' => 60,
						'null' => false
						),
					'code' => array( 
						'type' => 'varchar',
						'constraint' => 16,
						'null' => false
						),
					'expiry_date' => array( 
						'type' => 'date',
						'null' => false
						),
					'beginning_amount' => array( 
						'type' => 'float',
						'constraint' =>  array(10,2),
						'null' => false
						),
					'amount_used' => array( 
						'type' => 'float',
						'constraint' =>  array(10,2),
						'null' => false
						),
					'to_name' => array( 
						'type' => 'varchar',
						'constraint' => 70,
						'null' => true
						),
					'to_email' => array( 
						'type' => 'varchar',
						'constraint' => 75,
						'null' => false
						),
					'from' => array( 
						'type' => 'varchar',
						'constraint' => 70,
						'null' => true
						),
					'personal_message' => array( 
						'type' => 'mediumtext',
						'null' => true
						),
					'activated' => array( 
						'type' => 'tinyint',
						'constraint' => 1,
						'null' => false
						)
		));



		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('gift_cards', true);


		$this->dbforge->add_field(array(
				'id' => array(
					'type' => 'int',
					'constraint' => 9,
					'unsigned' => true,
					'auto_increment' => true
					),
				'option_id' => array(
					'type' => 'int',
					'constraint' => 9,
					'unsigned' => true,
					'null' => false
					),
				'name' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
					),
				'value' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
					),
				'price' => array(
					'type' => 'float',
					'constraint' => array(10,2),
					'null' => false
					),
				'weight' => array(
					'type' => 'float',
					'constraint' => array(10,2),
					'null' => false
					),
				'sequence' => array(
					'type' => 'int',
					'constraint' => 11,
					'null' => false
					),
				'limit' => array(
					'type' => 'int',
					'constraint' => 9,
					'null' => true
					)
		));
		
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('option_values', true);


		$this->dbforge->add_field(array(
				'id' => array(
					'type' => 'int',
					'constraint' => 9,
					'unsigned' => true,
					'auto_increment' => true,
					 ),
				'product_id' => array(
					'type' => 'int',
					'constraint' => 9,
					'unsigned' => true,
					'null' => false
					 ),
				'sequence' => array(
					'type' => 'int',
					'constraint' => 5,
					'unsigned' => true,
					'default' => 0,
					'null' => false
					 ),
				'name' => array(
					'type' => 'varchar',
					'constraint' => 64,
					'null' => false
					 ),
				'type' => array(
					'type' => 'varchar',
					'constraint' => 24,
					'null' => false
					 ),
				'required' => array(
					'type' => 'tinyint',
					'constraint' => 1,
					'null' => false
					)
		));

		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('options', true);


		$this->dbforge->add_field(array(
				'id' => array(
					'type' => 'int',
					'constraint' => 9,
					'unsigned' => true,
					'auto_increment' => true
					),
				'order_id' => array(
					'type' => 'int',
					'constraint' => 9,
					'unsigned' => true,
					'null' => false
					),
				'product_id' => array(
					'type' => 'int',
					'constraint' => 9,
					'unsigned' => true,
					'null' => false
					),
				'quantity' => array(
					'type' => 'int',
					'constraint' => 11,
					'null' => false
					),
				'contents' => array(
					'type' => 'longtext',
					)
		));
		
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('order_items', true);


		$this->dbforge->add_field(array(
				'id' => array(
					'type' => 'int',
					'constraint' => 10,
					'unsigned' => true, 
					'auto_increment' => true
					),
				'order_number' => array(
					'type' => 'varchar',
					'constraint' => 60,
					'null' => false
					),
				'customer_id' => array(
					'type' => 'int',
					'constraint' => 9,
					'null' => true,
					'unsigned' => true
					),
				'status' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
					),
				'ordered_on' => array(
					'type' => 'datetime',
					'null' => false
					 ),
				'shipped_on' => array(
					'type' => 'datetime'
					 ),
				'tax' => array(
					'type' => 'float',
					'constraint' => array(10,2),
					'null' => false
					),
				'total' => array(
					'type' => 'float',
					'constraint' => array(10,2),
					'null' => false
					),
				'subtotal' => array(
					'type' => 'float',
					'constraint' => array(10,2),
					'null' => false
					),
				'gift_card_discount' => array(
					'type' => 'float',
					'constraint' => array(10,2),
					'null' => false
					),
				'coupon_discount' => array(
					'type' => 'float',
					'constraint' => array(10,2),
					'null' => false
					),
				'shipping' => array(
					'type' => 'float',
					'constraint' => array(10,2),
					'null' => false
					),
				'shipping_notes' => array(
					'type' => 'text',
					'null' => false
					 ),
				'shipping_method' => array(
					'type' => 'tinytext',
					'null' => false
					 ),
				'notes' => array(
					'type' => 'text',
					'null' => true
					 ),
				'payment_info' => array(
					'type' => 'text',
					'null' => false
					 ),
				'referral' => array(
					'type' => 'text',
					'null' => false
					 ),
				'company' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => true
					),
				'firstname' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => true
					),
				'lastname' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => true
					),
				'phone' => array(
					'type' => 'varchar',
					'constraint' => 40,
					'null' => true
					),
				'email' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => true
					),
				'ship_company' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => true
					),
				'ship_firstname' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => true
					),
				'ship_lastname' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => true
					),
				'ship_email' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => true
					),
				'ship_phone' => array(
					'type' => 'varchar',
					'constraint' => 40,
					'null' => true
					),
				'ship_address1' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => true
					),
				'ship_address2' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => true
					),
				'ship_city' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => true
					),
				'ship_zip' => array(
					'type' => 'varchar',
					'constraint' => 11,
					'null' => true
					),
				'ship_zone' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => true
					),
				'ship_zone_id' => array(
					'type' => 'int',
					'constraint' => 11,
					'null' => true
					),
				'ship_country' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => true
					),
				'ship_country_code' => array(
					'type' => 'varchar',
					'constraint' => 10,
					'null' => true
					),
				'ship_country_id' => array(
					'type' => 'int',
					'constraint' => 9,
					'unsigned' => true,
					'null' => true
					),
				'bill_company' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => true
					),
				'bill_firstname' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => true
					),
				'bill_lastname' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => true
					),
				'bill_email' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => true
					),
				'bill_phone' => array(
					'type' => 'varchar',
					'constraint' => 40,
					'null' => true
					),
				'bill_address1' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => true
					),
				'bill_address2' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => true
					),
				'bill_city' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => true
					),
				'bill_zip' => array(
					'type' => 'varchar',
					'constraint' => 11,
					'null' => true
					),
				'bill_zone' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => true
					),
				'bill_zone_id' => array(
					'type' => 'int',
					'constraint' => 9,
					'unsigned' => true,
					'null' => true
					),
				'bill_country' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => true
					),
				'bill_country_code' => array(
					'type' => 'varchar',
					'constraint' => 10,
					'null' => true
					),
				'bill_country_id' => array(
					'type' => 'int',
					'constraint' => 9,
					'unsigned' => true,
					'null' => true
					)
		));


		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('orders', true);



		$this->dbforge->add_field(array(
				'id' => array(
					'type' => 'int',
					'constraint' => 9,
					'unsigned' => true,
					'auto_increment' => true
					),
				'parent_id' => array(
					'type' => 'int',
					'constraint' => 9,
					'unsigned' => true,
					'null' => false
					),
				'title' => array(
					'type' => 'varchar',
					'constraint' => 128,
					'null' => false
					),
				'menu_title' => array(
					'type' => 'varchar',
					'constraint' => 128,
					'null' => false
					),
				'slug' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
					),
				'route_id' => array(
					'type' => 'int',
					'constraint' => 128,
					'null' => false
					),
				'content' => array(
					'type' => 'longtext',
					'null' => false
					 ),
				'sequence' => array(
					'type' => 'int',
					'constraint' => 11,
					'null' => false,
					'default' => '0'
					),
				'seo_title' => array(
					'type' => 'text',
					'null' => false
					 ),
				'meta' => array(
					'type' => 'text',
					'null' => false
					 ),
				'url' => array(
					'type' => 'varchar',
					'constraint' => 255
					),
				'new_window' => array(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0'
					)
		));

		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('pages', true);


		$this->dbforge->add_field(array(
				'id' => array( 
					'type' => 'int', 
					'constraint' => 9,
					'unsigned' => true,
					'auto_increment' => true
					),
				'sku' => array( 
					'type' => 'varchar', 
					'constraint' => 30
					),
				'name' => array( 
					'type' => 'varchar', 
					'constraint' => 128
					),
				'slug' => array( 
					'type' => 'varchar', 
					'constraint' => 128
					),
				'route_id' => array( 
					'type' => 'int', 
					'constraint' => 9,
					'unsigned' => true,
					'null' => false
					),
				'description' => array( 
					'type' => 'text'
					),
				'excerpt' => array( 
					'type' => 'text'
					),
				'price' => array( 
					'type' => 'float', 
					'constraint' => array(10,2), 
					'null' => false, 
					'default' => '0.00'
					),
				'saleprice' => array( 
					'type' => 'float', 
					'constraint' => array(10,2), 
					'null' => false, 
					'default' => '0.00'
					),
				'free_shipping' => array( 
					'type' => 'tinyint', 
					'constraint' => 1,
					'null' => false,
					 'default' => '0'
					),
				'shippable' => array( 
					'type' => 'tinyint', 
					'constraint' => 1,
					'null' => false,
					 'default' => '1'
					),
				'taxable' => array( 
					'type' => 'tinyint', 
					'constraint' => 1,
					'null' => false,
					 'default' => '1'
					),
				'fixed_quantity' => array( 
					'type' => 'tinyint', 
					'constraint' => 1,
					'null' => false,
					 'default' => '0'
					),
				'weight' => array( 
					'type' => 'varchar', 
					'constraint' => 10,
					'null' => false,
					 'default' => '0'
					),
				'track_stock' => array( 
					'type' => 'tinyint', 
					'constraint' => 1,
					'null' => false,
					 'default' => '0'
					),
				'quantity' => array( 
					'type' => 'int', 
					'constraint' => 11,
					'null' => false
					),
				'related_products' => array( 
					'type' => 'text',
					'null' => true
					),
				'images' => array( 
					'type' => 'text',
					'null' => true
					),
				'seo_title' => array( 
					'type' => 'text',
					'null' => true
					),
				'meta' => array( 
					'type' => 'text',
					'null' => true
					),
				'enabled' => array( 
					'type' => 'tinyint', 
					'constraint' => 1,
					'null' => false, 
					'default' => '1',
					)
		));
		
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('products', true);


		$this->dbforge->add_field(array(
				'product_id' => array(
					'type' => 'int',
					'constraint' => 9,
					'unsigned' => true,
					'null' => false
					),
				'file_id' => array(
					'type' => 'int',
					'constraint' => 9,
					'unsigned' => true,
					'null' => false
					)
		));

		$this->dbforge->add_key(array('product_id', 'file_id'));
		$this->dbforge->create_table('products_files', true);


		$this->dbforge->add_field(array(
				'id' => array(
					'type' => 'int',
					'constraint' => 9,
					'unsigned' => true,
					'auto_increment' => true
					),
				'slug' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
					),
				'route' => array(
					'type' => 'varchar',
					'constraint' => 32
					)
		));

		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('routes', true);


		$this->dbforge->add_field(array(
				'code' => array(
					'type' => 'varchar',
					'constraint' => 40,
					'null' => false
					),
				'term' => array(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
					)
		));

		$this->dbforge->add_key('code', true);
		$this->dbforge->create_table('search', true);

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

		$this->dbforge->add_field(array(
				'id' => array( 
					'type' => 'int',
					'constraint' => 9,
					'unsigned' => true,
					'auto_increment' => true
					),
				'code' => array( 
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
					),
				'setting_key' => array( 
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
					),
				'setting' => array( 
					'type' => 'longtext',
					'null' => false
					)
		));


		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('settings', true);

	}
	
	public function down()
	{

	}
	
}
