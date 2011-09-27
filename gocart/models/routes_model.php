<?php

class Routes_model extends CI_Model {

	var $file_name; 
	function __construct()
	{
		parent::__construct();
		
		$this->file_name = APPPATH.'config/routes'.EXT;
		
		
	}

	// Check for existing indexes or reserved words
	function verify($route)
	{
		$route = strtolower($route);
		
		// Can't be the same as the name of a controller
		if ($handle = opendir(APPPATH.'controllers')) {
		    while (false !== ($file = readdir($handle))) {
		        if ($file != "." && $file != "..") {
		        	$file = str_replace('.php', '', strtolower($file));
		            if($route == $file) return false;
		        }
		    }
		    closedir($handle);
		}
		
		// otherwise, we're good
		return true;
	
	}
	
	// does the route exist ?
	function exists($route)
	{
		if(array_key_exists($route, $this->router->routes)) return true;
	}
	
	// Update an entry
	function update($old_slug, $new_slug, $path)
	{
		
		// Does the old exist ? if not, just append it
		if( ! $this->exists($old_slug)) return $this->append($new_slug, $path);
		
		// Is the new valid ?
		if( ! $this->verify($new_slug)) return false;
		
		// does the new conflict?
		if( $this->exists($new_slug)) return false;
		
		// read in the whole file 
		$contents = file($this->file_name);
		
		// Open the file to rewrite 
		$fp = fopen($this->file_name, 'w');
		if(!$fp) return false; // can't open the file
		
		/// re-write the file as we search it, skipping over the line we want to remove 
		//  This isn't the most efficient way to do this, but since we have to loop and rewrite the file,
		//   we might as well do it in one swoop
		$found_line = false;
		$search_string = "\$route['$old_slug']";
		foreach($contents as &$line)
		{
			if( ! stristr($line, $search_string))
			{
				fwrite($fp, $line);
			} else {  
				fwrite($fp, "\$route['$new_slug'] = '$path';\n"); // write replacement
				$found_line = true;
			}
		}
		fclose($fp);
		
		if(!$found_line) return false;
		
		return true;
		
	}
	
	// Remove an entry
	// this should happen less often, when a product, etc, is deleted.
	function remove($slug)
	{
		// Does this exist ? if not don't bother reading the file
		if( ! $this->exists($slug)) return false;
		
		// read in the whole file 
		$contents = file($this->file_name);
		
		// Open the file to rewrite 
		$fp = fopen($this->file_name, 'w');
		if(!$fp) return false; // can't open the file
		
		/// re-write the file as we search it, skipping over the line we want to remove 
		//  This isn't the most efficient way to do this, but since we have to loop and rewrite the file,
		//   we might as well do it in one swoop
		$found_line = false;
		$search_string = "\$route['$slug']";
		foreach($contents as &$line)
		{
			if( ! stristr($line, $search_string))
			{
				fwrite($fp, $line);
			} else $found_line = true;
		}
		fclose($fp);
		
		if(!$found_line) return false;
		
		return true;
	}
	
	// Add an entry
	function append($slug, $path)
	{
		// Reserved path?
		if( ! $this->verify($slug)) return false;
		
		// Are we overwriting something?
		if( $this->exists($slug)) return false;
		
		// if it doesn't exist, we can append it to our file
		$fp = fopen($this->file_name, 'a');
		if(!$fp) return false; // can't open the file
		fwrite($fp, "\$route['$slug'] = '$path';\n");
		fclose($fp);
		
		return true;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_all()
	{
		$all	= $this->db->get('routes')->result();
		$routes	= array();
		foreach($all as $route)
		{
			$routes[$route['slug']]	= $route['route'];
		}
		
		return $routes;
	}
	
	function get($id)
	{
		return $this->db->get_where('routes', array('id'=>$id))->row();
	}
	
	// save or update a route and return the id
	function save($route)
	{
		if(!empty($route['id']))
		{
			$this->db->where('id', $route['id']);
			$this->db->update('routes', $route);
			
			return $route['id'];
		}
		else
		{
			$this->db->insert('routes', $route);
			return $this->db->insert_id();
		}
	}
	
	function check_slug($slug, $id=false)
	{
		if($id)
		{
			$this->db->where('id !=', $id);
		}
		$this->db->where('slug', $slug);
		
		return (bool) $this->db->count_all_results('routes');
	}
	
	function validate_slug($slug, $id=false, $count=false)
	{
		if($this->check_slug($slug.$count, $id))
		{
			if(!$count)
			{
				$count	= 1;
			}
			else
			{
				$count++;
			}
			return $this->validate_slug($slug, $id, $count);
		}
		else
		{
			return $slug.$count;
		}
	}
	
	function delete($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('routes');
	}
}