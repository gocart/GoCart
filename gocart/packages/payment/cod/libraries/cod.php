<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class cod
{
	var $CI;
	
	//this can be used in several places
	var	$method_name;
	
	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->lang->load('cod');
		
		$this->method_name	= lang('charge_on_delivery');
	}
	
	/*
	checkout_form()
	this function returns an array, the first part being the name of the payment type
	that will show up beside the radio button the next value will be the actual form if there is no form, then it should equal false
	there is also the posibility that this payment method is not approved for this purchase. in that case, it should return a blank array 
	*/
	
	//these are the front end form and check functions
	function checkout_form($post = false)
	{
		$settings	= $this->CI->Settings_model->get_settings('cod');
		$enabled	= $settings['enabled'];
		
		$form			= array();
		if($enabled == 1)
		{
			$form['name']	= $this->method_name;
			$form['form']	= false;
		}
		
		return $form;
	}
	function checkout_check()
	{
		//this is where we would normally check the $_POST info
		
		//if all is well, return false, otherwise, return an error message
		return false;
	}
	
	function description()
	{
		//create a description from the session which we can store in the database
		//this will be added to the database upon order confirmation
		
		/*
		access the payment information with the  $_POST variable since this is called
		from the same place as the checkout_check above.
		*/
		
		return lang('charge_on_delivery');
		
		/*
		for a credit card, this may look something like
		
		$cart['payment']['description']	= 'Card Type: Visa
		Name on Card: John Doe<br/>
		Card Number: XXXX-XXXX-XXXX-9976<br/>
		Expires: 10/12<br/>';
		*/	
	}
	
	//back end installation functions
	function install()
	{
		//set a default blank setting for flatrate shipping
		$this->CI->Settings_model->save_settings('cod', array('enabled'=>'0'));
	}
	
	function uninstall()
	{
		$this->CI->Settings_model->delete_settings('cod');
	}
	
	//payment processor
	function process_payment()
	{
		$process	= false;
		//process the payment here, if it goes through return false if it breaks down, return an error message
		if($process)
		{
			return lang('processing_error');
		}
		else
		{
			return false;
		}
	}
	
	//admin end form and check functions
	function form($post	= false)
	{
		//this same function processes the form
		if(!$post)
		{
			$settings	= $this->CI->Settings_model->get_settings('cod');
			$enabled	= $settings['enabled'];
		}
		else
		{
			$enabled	= $post['enabled'];
		}
		
		ob_start();
		?>

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
		
		//there is no need for error checking on this form, but this is how it will generally be done.
		//test against $_POST 
		
		//count the errors
		if($error)
		{
			return $error;
		}
		else
		{
			//we save the settings if it gets here
			$this->CI->Settings_model->save_settings('cod', array('enabled'=>$_POST['enabled']));
			
			return false;
		}
	}
}
