<?php
function format_date($date){	
	if ($date != '' && $date != '0000-00-00')
	{
		$d	= explode('-', $date);
	
		$m	= Array(
		'January'
		,'February'
		,'March'
		,'April'
		,'May'
		,'June'
		,'July'
		,'August'
		,'September'
		,'October'
		,'November'
		,'December'
		);
	
		return $m[$d[1]-1].' '.$d[2].', '.$d[0]; 
	}
	else
	{
		return false;
	}
}

function reverse_format($date)
{
	if(empty($date)) 
	{
		return;
	}
	
	$d = explode('-', $date);
	
	return "{$d[1]}-{$d[2]}-{$d[0]}";
}

function format_ymd($date)
{
	if(empty($date) || $date == '00-00-0000')
	{
		return '';
	}
	else
	{
		$d = explode('-', $date);
		return $d[2].'-'.$d[0].'-'.$d[1];
	}
}

function format_mdy($date)
{
	if(empty($date) || $date == '0000-00-00')
	{
		return '';
	}
	else
	{
		return date('m-d-Y', strtotime($date));
	}
	
}


/* End of file welcome.php */
/* Location: ./system/application/helpers/MY_date_helper.php */
