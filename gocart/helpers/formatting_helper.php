<?php 
function format_address($fields)
{
	if(empty($fields))
	{
		return ;
	}
	
	// Default format
	$default = "{firstname} {lastname}\n{company}\n{address_1}\n{address_2}\n{city}, {zone} {postcode}\n{country}";
	
	// Fetch country record to determine which format to use
	$CI = &get_instance();
	$CI->load->model('location_model');
	$c_data = $CI->location_model->get_country($fields['country_id']);
	
	if(empty($c_data->address_format))
	{
		$formatted = $default;
	} else {
		$formatted = $c_data->address_format;
	}

	$formatted = str_replace('{firstname}', $fields['firstname'], $formatted);
	$formatted = str_replace('{lastname}',  $fields['lastname'], $formatted);
	$formatted = str_replace('{company}',  $fields['company'], $formatted);
	
	$formatted = str_replace('{address_1}', $fields['address1'], $formatted);
	$formatted = str_replace('{address_2}', $fields['address2'], $formatted);
	$formatted = str_replace('{city}', $fields['city'], $formatted);
	$formatted = str_replace('{zone}', $fields['zone'], $formatted);
	$formatted = str_replace('{postcode}', $fields['zip'], $formatted);
	$formatted = str_replace('{country}', $fields['country'], $formatted);
	
	// remove any extra new lines resulting from blank company or address line
	$formatted = preg_replace('`[\r\n]+`',"\n",$formatted);
	
	return $formatted;
	
}


// Same as above, except it includes characters used by the jquery selectmenu address selector,
//  also adds email address and phone number to the formatted return string
function format_address_for_selector($fields)
{	
	if(empty($fields))
	{
		return ;
	}
	
	// Default format
	$default = "{firstname} {lastname}\n{company}\n{address_1}\n{address_2}\n{city}, {zone} {postcode}\n{country}";
	
	// Fetch country record to determine which format to use
	$CI = &get_instance();
	$CI->load->model('location_model');
	$c_data = $CI->location_model->get_country($fields['country_id']);
	
	if(empty($c_data->address_format))
	{
		$formatted = $default;
	} else {
		$formatted = $c_data->address_format;
	}
	
	$formatted = str_replace('{firstname}', $fields['firstname'], $formatted);
	$formatted = str_replace("{lastname}\n",  $fields['lastname'] . ' - ', $formatted);
	$formatted = str_replace('{company}',  $fields['company'], $formatted);
	
	$formatted = str_replace('{address_1}', $fields['address1'], $formatted);
	$formatted = str_replace('{address_2}', $fields['address2'], $formatted);
	$formatted = str_replace('{city}', $fields['city'], $formatted);
	$formatted = str_replace('{zone}', $fields['zone'], $formatted);
	$formatted = str_replace('{postcode}', $fields['zip'], $formatted);
	$formatted = str_replace('{country}', $fields['country'], $formatted);
	// tack on the phone number
	$formatted .= "\n".'('. $fields['phone'] .')';
	
	// remove any extra new lines resulting from blank company or address line
	$formatted = preg_replace('`[\r\n]+`',"\n",$formatted);
	
	// take out the last newline character, but only the last one
	// (I don't like all this reversing, unreversing, but if you can find a better way to replace right-to-left, let me know)
	$formatted = strrev($formatted);
	$formatted = str_replace_once("\n", ' ', $formatted);
	$formatted = strrev($formatted);
	
	// convert new lines to pipe char
	$formatted = str_replace("\n", ' | ', $formatted);
	
	return $formatted;	
}

function str_replace_once($search, $replace, $subject) {
    $firstChar = strpos($subject, $search);
    if($firstChar !== false) {
        $beforeStr = substr($subject,0,$firstChar);
        $afterStr = substr($subject, $firstChar + strlen($search));
        return $beforeStr.$replace.$afterStr;
    } else {
        return $subject;
    }
}


function format_currency($value, $symbol=true)
{
	// locale information must be set up for this to return a proper value
	// return money_format(!%i, $value);
	
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
	
	$formatted = number_format(abs($value), 2, '.', ',');
	
	if($symbol)
	{
		$formatted = $neg.$CI->config->item('currency_symbol').$formatted;
	}
	
	return $formatted;

}

