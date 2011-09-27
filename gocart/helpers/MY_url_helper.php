<?php

/* 
Extends functionality for url helper
Thanks to Mohammad Sajjad Hossain
http://sajjadhossain.com/2008/10/27/ssl-https-urls-and-codeigniter/
*/

if( ! function_exists('secure_site_url') )
{
    function secure_site_url($uri = '')
    {
        $CI =& get_instance();
        return $CI->config->secure_site_url($uri);
    }
}
 
 
if( ! function_exists('secure_base_url') )
{
    function secure_base_url()
    {
        $CI =& get_instance();
        return $CI->config->slash_item('secure_base_url');
    }
}
 
if ( ! function_exists('secure_anchor'))
{
    function secure_anchor($uri = '', $title = '', $attributes = '')
    {
        $title = (string) $title;
 
        if ( ! is_array($uri))
        {
            $secure_site_url = ( ! preg_match('!^\w+://! i', $uri)) ? secure_site_url($uri) : $uri;
        }
        else
        {
            $secure_site_url = secure_site_url($uri);
        }
 
        if ($title == '')
        {
            $title = $secure_site_url;
        }
 
        if ($attributes != '')
        {
            $attributes = _parse_attributes($attributes);
        }
 
        return '<a href="'.$secure_site_url.'"'.$attributes.'>'.$title.'</a>';
    }
}
 
if ( ! function_exists('secure_redirect'))
{
    function secure_redirect($uri = '', $method = 'location', $http_response_code = 302)
    {
        switch($method)
        {
            case 'refresh'    : header("Refresh:0;url=".secure_site_url($uri));
                break;
            default            : header("Location: ".secure_site_url($uri), TRUE, $http_response_code);
                break;
        }
        exit;
    }
}

//write over the "current_url" function with this one, it tells us the url based on whether its secure or not rather than just spitting out base_url();
function current_url()
{
	return rtrim(current_base_url(), '/').uri_string();
}

//return the current base url
function current_base_url()
{
	//from what I understand $_SERVER['HTTPS'] may not work under IIS, but then again I don't care too much about IIS - GO APACHE!!!!
	$http	= 'http://';
	if (isset($_SERVER['HTTPS']))
	{
		$http	= 'https://';
	}
	
	//just in case someone entered capital letters in their url
	return $http.strtolower($_SERVER['HTTP_HOST']).'/';
}

//test the current page to see if it's using the secure base url
function secure_page()
{
	if (current_base_url() == strtolower(secure_base_url()))
	{
		return true;	
	}
	else
	{
		return false;
	}
}

?>
