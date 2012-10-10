<?php 

/**
 * Return a formatted address
 * 
 * @param mixed   $fields       Array or Object of address fields
 * @param string  $field_prefix Optional prefix to use on the fields
 * @param boolean $br           Convert the new line chars Yes/No
 * 
 * @return string
 */
function format_address($fields, $field_prefix = '', $br=FALSE)
{
	if(empty($fields))
	{
		return ;
	}
	
	// convert from an object to an array
	$fields = (array)$fields;
	
	// Default format
	$default = "{firstname} {lastname}\n{company}\n{address_1}\n{address_2}\n{city}, {zone} {postcode}\n{country}";
	
	// Fetch country record to determine which format to use
	$CI = &get_instance();
	$CI->load->model('location_model');
	$c_data = $CI->location_model->get_country($fields[$field_prefix.'country_id']);
	
	if(empty($c_data->address_format))
	{
		$formatted	= $default;
	}
	else
	{
		$formatted	= $c_data->address_format;
	}

	$formatted = str_replace('{firstname}', $fields[$field_prefix.'firstname'], $formatted);
	$formatted = str_replace('{lastname}', $fields[$field_prefix.'lastname'], $formatted);
	$formatted = str_replace('{company}', $fields[$field_prefix.'company'], $formatted);
	
	$formatted = str_replace('{address_1}', $fields[$field_prefix.'address1'], $formatted);
	$formatted = str_replace('{address_2}', $fields[$field_prefix.'address2'], $formatted);
	$formatted = str_replace('{city}', $fields[$field_prefix.'city'], $formatted);
	$formatted = str_replace('{zone}', $fields[$field_prefix.'zone'], $formatted);
	$formatted = str_replace('{postcode}', $fields[$field_prefix.'zip'], $formatted);
	$formatted = str_replace('{country}', $fields[$field_prefix.'country'], $formatted);
	
	// remove any extra new lines resulting from blank company or address line
	$formatted = preg_replace('`[\r\n]+`',"\n",$formatted);
	
	if($br === TRUE)
	{
		$formatted	= nl2br($formatted);
	}
	
	return $formatted;
	
}

function format_currency($value, $symbol=true)
{

	if(!is_numeric($value))
	{
		return;
	}
	
	$CI = &get_instance();
	
	if($value < 0 )
	{
		$neg = '- ';
	} else {
		$neg = '';
	}
	
	if($symbol)
	{
		$formatted	= number_format(abs($value), 2, $CI->config->item('currency_decimal'), $CI->config->item('currency_thousands_separator'));
		
		if($CI->config->item('currency_symbol_side') == 'left')
		{
			$formatted	= $neg.$CI->config->item('currency_symbol').$formatted;
		}
		else
		{
			$formatted	= $neg.$formatted.$CI->config->item('currency_symbol');
		}
	}
	else
	{
		//traditional number formatting
		$formatted	= number_format(abs($value), 2, '.', ',');
	}
	
	return $formatted;
}