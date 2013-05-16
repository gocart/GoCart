<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_gocart extends CI_migration {

	public function up() {

		// Add our 'enabled' field
		$fields	= array(
			'enabled'	=> array(
				'type'			=> 'tinyint',
				'constraint'	=> 1,
				'default'		=> 1
			)
		);
		$this->dbforge->add_column('categories', $fields);

	}

	public function down() {

		$this->dbforge->drop_column('categories', 'enabled');

	}

}
