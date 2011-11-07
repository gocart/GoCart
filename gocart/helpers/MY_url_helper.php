<?php

function ssl_support()
{
	$CI =& get_instance();
    return $CI->config->item('ssl_support');
}

if ( ! function_exists('force_ssl'))
{
	function force_ssl()
	{
		$CI =& get_instance();
		$CI->config->config['base_url'] = str_replace('http://', 'https://', $CI->config->config['base_url']);
		if (ssl_support() &&  (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off' || $_SERVER['SERVER_PORT'] != 80))
		{
			redirect($CI->uri->uri_string());
		}
	}
}

//thanks C4iO [PyroDEV]
if ( ! function_exists('remove_ssl'))
{
	function remove_ssl()
	{	
		if ($_SERVER['SERVER_PORT'] != 80 || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'))
		{
			$CI =& get_instance();
			$CI->config->config['base_url'] = str_replace('https://', 'http://', $CI->config->config['base_url']);
			
			redirect($CI->uri->uri_string());
		}
	}
}