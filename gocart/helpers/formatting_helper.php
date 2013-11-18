<?php 
function format_address($fields, $br=false)
{
	if(empty($fields))
	{
		return ;
	}
	
	// Default format
	$default = "{firstname} {lastname}\n{company}\n{address_1}\n{address_2}\n{city}, {zone} {zip}\n{country}";
	
	// Fetch country record to determine which format to use
	$CI = &get_instance();
	$CI->load->model('location_model');
	$c_data = $CI->location_model->get_country($fields['country_id']);
	
	if(empty($c_data->address_format))
	{
		$formatted	= $default;
	} else {
		$formatted	= $c_data->address_format;
	}

	$keys = preg_split("/[\s,{}]+/", $formatted);
	foreach ($keys as $id=>$key)
	{
		$formatted = array_key_exists($key, $fields) ? str_replace('{'.$key.'}', $fields[$key], $formatted) : str_replace('{'.$key.'}', '', $formatted);
	}
	
	// remove any extra new lines resulting from blank company or address line
	$formatted		= preg_replace('`[\r\n]+`',"\n",$formatted);
	if($br)
	{
		$formatted	= nl2br($formatted);
	}
	return $formatted;
	
}

function format_currency($value, $symbol=true)
{
	$fmt = numfmt_create( config_item('locale'), NumberFormatter::CURRENCY );
	return numfmt_format_currency($fmt, $value, config_item('currency_iso'));
}