<?php
/* 
Extends functionality for url helper
Thanks to Mohammad Sajjad Hossain
http://sajjadhossain.com/2008/10/27/ssl-https-urls-and-codeigniter/
*/
class MY_Config extends CI_Config
{

	function __construct()
	{
		parent::__construct();
	}
	
	function secure_site_url($uri = '')
	{
		if ($uri == '')
		{
			return $this->slash_item('secure_base_url').$this->item('index_page');
		}

		if ($this->item('enable_query_strings') == FALSE)
		{
			if (is_array($uri))
			{
				$uri = implode('/', $uri);
			}

			$index = $this->item('index_page') == '' ? '' : $this->slash_item('index_page');
			$suffix = ($this->item('url_suffix') == FALSE) ? '' : $this->item('url_suffix');
			return $this->slash_item('secure_base_url').$index.trim($uri, '/').$suffix;
		}
		else
		{
			if (is_array($uri))
			{
				$i = 0;
				$str = '';
				foreach ($uri as $key => $val)
				{
					$prefix = ($i == 0) ? '' : '&';
					$str .= $prefix.$key.'='.$val;
					$i++;
				}

				$uri = $str;
			}

			return $this->slash_item('secure_base_url').$this->item('index_page').'?'.$uri;
		}
	}
}