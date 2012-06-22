<?php
Class Option_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('formatting_helper');
	}
	
 	/********************************************************************
		Options Management
	********************************************************************/
 
	
	//get product options
	function get_all_options($product_id)
	{
		$this->db->where('product_id', $product_id);
		$this->db->order_by('id', 'DESC');
		$result	= $this->db->get('options');
		
		$return = array();
		foreach($result->result() as $option)
		{
			$option->values	= $this->get_option_values($option->id);
			$return[]	= $option;
		}
		return $return;
	}
	
	function get_option($id, $as_array = false)
	{
		$result	= $this->db->get_where('options', array('id'=>$id));
		
		$data	= $result->row();
		
		if($as_array)
		{
			$data->values = $this->get_option_values($id, true);
		}
		else
		{
			$data->values = $this->get_option_values($id);
		}
		
		return $data;
	}
	
	function save_option($option, $values)
	{
		if(isset($option['id']))
		{
			$this->db->where('id', $option['id']);
			$this->db->update('options', $option);
			$id	= $option['id'];
			
			//eliminate existing options
			$this->delete_option_values($id);
		}
		else
		{
			$this->db->insert('options', $option);
			$id	= $this->db->insert_id();
		}
		
		//add options to the database
		$sequence	= 0;
		foreach($values as $value)
		{
			$value['option_id'] = $id;
			$value['sequence']	= $sequence;
			$value['weight']	= floatval($value['weight']);
			$value['price']		= floatval($value['price']);
			$sequence++;
			
			$this->db->insert('option_values', $value);
		}
		return $id;
	}
	
	// for product level options 
	function clear_options($product_id)
	{
		// get the list of options for this product
		$list = $this->db->where('product_id', $product_id)->get('options')->result();
		
		foreach($list as $opt)
		{
			$this->delete_option($opt->id);
		}
	}
	
	// also deletes child records in option_values and product_option
	function delete_option($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('options');
		
		$this->delete_option_values($id);
	}
	


	/********************************************************************
		Option values Management
	********************************************************************/
	
	function get_option_values($option_id)
	{
		$this->db->where('option_id',$option_id); 
		$this->db->order_by('sequence', 'ASC');
		return $this->db->get('option_values')->result();
	}
	
	function get_value($value_id) 
	{
		$this->db->where('id', $value_id);
		return $this->db->get('option_values')->row();
	}
	
	function delete_option_values($id)
	{
		$this->db->where('option_id', $id);
		$this->db->delete('option_values');
	}
	

	/********************************************************************
		Product options Management
	********************************************************************/

	function get_product_options($product_id)
	{
		$this->db->where('product_id',$product_id); 
		$this->db->order_by('sequence', 'ASC');
		
		$result	= $this->db->get('options');
		
		$return = array();
		foreach($result->result() as $option)
		{
			$option->values	= $this->get_option_values($option->id);
			$return[]	= $option;
		}
		return $return;
	}

	
	/***************************************************
		Options Live Use Functionality
	****************************************************/
	
	function validate_product_options(&$product, $post_options)
	{
		
		if( ! isset($product['id'])) return false;
		
		// set up to catch option errors
		$error = false;
		$msg = 'The following options were not selected and are required:<br/>';
		
		// Get the list of options for the product 
		//  We will check the submitted options against this to make sure required options were selected	
		$db_options = $this->get_product_options($product['id']);
		
		// Loop through the options from the database
		foreach($db_options as $option)
		{
			// Use the product option to see if we have matching data from the product form
			$option_value = @$post_options[$option->id];
						
			// are we missing any required values?
			if((int) $option->required && empty($option_value)) 
			{
				// Set our error flag and add to the user message
				//  then continue processing the other options to built a full list of missing requirements
				$error = true;
				$msg .= "- ". $option->name .'<br/>';	
				continue; // don't bother processing this particular option any further
			}
			
			// process checklist items specially
			   // multi-valued
			if($option->type == 'checklist')
			{

				$opts = array();				
				// tally our adjustments
				
				//check to make sure this is an array before looping
				if(is_array($option_value))
				{
					
					foreach($option_value as $check_value) 
					{
						//$val = $this->get_value($check_value);
						
						foreach($option->values as $check_match)
						{
							if($check_match->id == $check_value)
							{
								$val	= $check_match;
							}
						}
						
						$price = '';
						if($val->price > 0)
						{
							$price = ' ('.format_currency($val->price).')';
						}
						$product['price'] 	= $product['price'] + $val->price;
						$product['weight'] 	= $product['weight'] + $val->weight;

						array_push($opts, $val->value.$price);
					}
				}
				
				// If only one option was checked, add it as a single value
				if(count($opts)==1) 
				{
					$product['options'][$option->name] = $opts[0];
				}
				// otherwise, add it as an array of values
				else if(!empty($opts)) 
				{ 
					$product['options'][$option->name] = $opts;
				}
				
			}
			
			 // handle text fields
			else if($option->type == 'textfield' || $option->type == 'textarea') 
			{
				//get the value and weight of the textfield/textarea and add it!
				
				if($option_value)
				{
					//get the potential price and weight of this field
					$val	= $option->values[0];
										
					//add the weight and price to the product
					$product['price'] 	= $product['price'] + $val->price;
					$product['weight'] 	= $product['weight'] + $val->weight;
					
					//if there is additional cost, add it to the item description
					$price = '';
					if($val->price > 0)
					{
						$price = ' ('.format_currency($val->price).')';
					}
					
					$product['options'][$option->name] = $option_value.$price;
				}
			}
			 // handle radios and droplists
			else
			{
				//make sure that blank options aren't used
				if ($option_value)
				{
					// we only need the one selected
					//$val = $this->get_value($option_value);
					
					foreach($option->values as $check_match)
					{
						if($check_match->id == $option_value)
						{
							$val	= $check_match;
						}
					}
					
					//adjust product price and weight
					$product['price'] 	= $product['price'] + $val->price;
					$product['weight'] 	= $product['weight'] + $val->weight;
					
					$price = '';
					if($val->price > 0)
					{
						$price = ' ('.format_currency($val->price).')';
					}
					//add the option to the options
					//$product['options'][$option->name] = $val->name.$price.$weight;
					$product['options'][$option->name] = $val->name.$price;
				}
			}
		}
		
		if($error)
		{
			return( array( 'validated' => false,
						   'message' => $msg
						  ));
		}
		else
		{
			return( array( 'validated' => true ));
		}
		
	}
}