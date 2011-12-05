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
		
		$form	= '<table><tr><td>'.lang('rate').':</td><td><input type="text" name="rate" value="'.$rate.'"  class="gc_tf1"/></td></tr>
		<tr><td>'.lang('enabled').': </td><td><select name="enabled">';
		if($settings['enabled'] == 1)
		{
			$enable		= ' selected="selected"';
			$disable	= '';
		}
		else
		{
			$enable		= '';
			$disable	= ' selected="selected"';
		}
		$form	.= '<option value="1"'.$enable.'>'.lang('enabled').'</option>
					<option value="0"'.$disable.'>'.lang('disabled').'</option>';
		$form	.= '</select></td></tr>
		</table>';
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
