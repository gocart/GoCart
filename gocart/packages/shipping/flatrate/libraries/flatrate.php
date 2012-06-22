<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class flatrate
{
	var $CI;

	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->lang->load('flatrate');
	}
	
	function rates()
	{
		//rates function should return an array of rates/prices
		//this is so a UPS function could perhaps return multiple shipping rates
		//setting up some sort of database setting for this is ok
		$settings	= $this->CI->Settings_model->get_settings('flatrate');
		
		if($settings['enabled'] && $settings['enabled'] > 0)
		{
			return array('Flat Rate'=> $settings['rate']);
		}
		else
		{
			return array();
		}
		
	}
	
	function install()
	{
		//set a default blank setting for flatrate shipping
		$this->CI->Settings_model->save_settings('flatrate', array('rate'=>''));
		$this->CI->Settings_model->save_settings('flatrate', array('enabled'=>'0'));
	}
	
	function uninstall()
	{
		$this->CI->Settings_model->delete_settings('flatrate');
	}
	
	function form($post	= false)
	{
		$this->CI->load->helper('form');
		
		//this same function processes the form
		if(!$post)
		{
			$settings	= $this->CI->Settings_model->get_settings('flatrate');
			$rate		= $settings['rate'];
		}
		else
		{
			$rate		= $post['rate'];
		}
		ob_start();
		?>
		<label><?php echo lang('rate');?></label>
		<?php echo form_input(array('name'=>'rate', 'value'=>$rate, 'class'=>'span3'));?>
		
		<label><?php echo lang('enabled');?></label>
		<select name="enabled" class="span3">
			<option value="1"<?php echo((bool)$settings['enabled'])?' selected="selected"':'';?>><?php echo lang('enabled');?></option>
			<option value="0"<?php echo((bool)$settings['enabled'])?'':' selected="selected"';?>><?php echo lang('disabled');?></option>
		</select>
		<?php
		$form =ob_get_contents();
		ob_end_clean();

		return $form;
	}
	
	function check()
	{	
		$error	= false;
		
		if(!is_numeric($_POST['rate']))
		{
			$error	.= '<div>'.lang('val_err').'</div>';
		}		
		
		//count the errors
		if($error)
		{
			return $error;
		}
		else
		{
			//we save the settings if it gets here
			$this->CI->Settings_model->save_settings('flatrate', array('rate'=>$_POST['rate'], 'enabled'=>$_POST['enabled']));
			
			return false;
		}
	}
}
