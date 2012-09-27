<?php

class My_Router extends CI_Router 
{
	function __construct()
	{
		parent::__construct();
	}
	
	// this is here to add an additional layer to the routing system.
	//If a route isn't found in the routes config file. then it will scan the database for a matching route.
	function _parse_routes()
	{
		$segments	= $this->uri->segments;

		// Turn the segment array into a URI string
		$uri = implode('/', $segments);
		
		// Is there a literal match?  If so we're done
		if (isset($this->routes[$uri]))
		{
			return $this->_set_request(explode('/', $this->routes[$uri]));
		}

		// Loop through the route array looking for wild-cards
		foreach ($this->routes as $key => $val)
		{
			// Convert wild-cards to RegEx
			$key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));

			// Does the RegEx match?
			if (preg_match('#^'.$key.'$#', $uri))
			{
				// Do we have a back-reference?
				if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE)
				{
					$val = preg_replace('#^'.$key.'$#', $val, $uri);
				}

				return $this->_set_request(explode('/', $val));
			}
		}
		
		// now try the GoCart specific routing
		$segments = array_splice($segments, -2, 2);

		// Turn the segment array into a URI string
		$uri = implode('/', $segments);

		//look through the database for a route that matches and apply the same logic as above :-)
		//load the database connection information
		require_once BASEPATH.'database/DB'.EXT;
		
		if(count($segments) == 1)
		{
			$row	= $this->_get_db_route($segments[0]);
			
			if(!empty($row))
			{
				return $this->_set_request(explode('/', $row['route']));
			}
		}
		else
		{	
			$segments	= array_reverse($segments);
			//start with the end just to make sure we're not a multi-tiered category or category/product combo before moving to the second segment
			//we could stop people from naming products or categories after numbers, but that would be limiting their use.
			$row	= $this->_get_db_route($segments[0]);
			//set a pagination flag. If this is set true in the next if statement we'll know that the first row is segment is possibly a page number
			$page_flag	= false;
			if($row)
			{
				return $this->_set_request(explode('/', $row['route']));
			}
			else
			{
				//this is the second go
				$row	= $this->_get_db_route($segments[1]);
				$page_flag	= true;
			}
			
			//we have a hit, continue down the path!
			if($row)
			{
				if(!$page_flag)
				{
					return $this->_set_request(explode('/', $row['route']));
				}
				else
				{
					$key = $row['slug'].'/([0-9]+)';
					
					//pages can only be numerical. This could end in a mighty big error!!!!
					if (preg_match('#^'.$key.'$#', $uri))
					{
						$row['route'] = preg_replace('#^'.$key.'$#', $row['route'],$uri);
						return $this->_set_request(explode('/', $row['route']));
					}
				}
			}
		}
		
		// If we got this far it means we didn't encounter a
		// matching route so we'll set the site default route
		$this->_set_request($this->uri->segments);
	}
	
	function _get_db_route($slug)
	{
		return DB()->where('slug',$slug)->get('routes')->row_array();
	}
}