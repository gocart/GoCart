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

function format_datetime($datetime)
{
	$d	= explode(' ', format_date($datetime));
	
	$t	= $d[2];
	
	$t	= explode(':', $t);
	
	$ap	= 'am';
	if($t[0] > 12)
	{
		$t[0] = $t[0]-12;
		$ap	= 'pm';
	}
	elseif($t[0] == 0)
	{
		$t[0] = 12;
	}
	elseif ($t[0] == 12)
	{
		$ap = 'pm';
	}
	
	return $d[0].' '.$d[1].', '.$d[3].' at '.$t[0].':'.$t[1].$ap;
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


/* End of file welcome.php */
/* Location: ./system/application/helpers/MY_date_helper.php */
