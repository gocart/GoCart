<?php

class Locations extends CI_Controller {	
	
	function __construct()
	{		
		parent::__construct();
		$this->load->model('Location_model');
		
	}
	
	function get_zone_menu()
	{
		$id	= $this->input->post('id');
		$zones	= $this->Location_model->get_zones_menu($id);
		
		foreach($zones as $id=>$z):?>
		
		<option value="<?php echo $id;?>"><?php echo $z;?></option>
		
		<?php endforeach;
	}
}