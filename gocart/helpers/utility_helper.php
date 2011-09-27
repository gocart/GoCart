<?php

function sort2d ($array, $index, $order='asc', $natsort=FALSE, $case_sensitive=FALSE) 
{
    if(is_array($array) && count($array)>0) 
    {
       foreach(array_keys($array) as $key) 
           $temp[$key]=$array[$key][$index];
           if(!$natsort) 
               ($order=='asc')? asort($temp) : arsort($temp);
          else 
          {
             ($case_sensitive)? natsort($temp) : natcasesort($temp);
             if($order!='asc') 
                 $temp=array_reverse($temp,TRUE);
       }
       foreach(array_keys($temp) as $key) 
           (is_numeric($key))? $sorted[]=$array[$key] : $sorted[$key]=$array[$key];
       return $sorted;
  }
  return $array;
}

//this tests for IE < 9 since IE9 supports the Canvas tag properly
function is_ie($version = 9)
{	
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
	{
		$browser	= explode(';', $_SERVER['HTTP_USER_AGENT']);
		foreach ($browser as $b)
		{
			if (strpos($b, 'MSIE') !== false)
			{
				$test	= explode(' ', $b);
			}
		}

		if($test[2] < $version)
		{
			return true;
		}
		else
		{
			return false;
		}
		
	}
	else
	{
		return false;
	}
}