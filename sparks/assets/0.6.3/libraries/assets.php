<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');	
	
/**
 * Assets Library
 *
 * @author 		Boris Strahija <boris@creolab.hr>
 * @copyright 	Copyright (c) 2010, Boris Strahija, Creo
 * @version 	0.6.3
 */

define('ASSETS_VERSION', '0.6.3');


class Assets {
	
	protected $ci;
	protected $less;
	
	
	// Paths and folders
	public $assets_dir;
	public $base_path;
	public $base_url;
	
	public $js_dir;
	public $js_path;
	public $js_url;
	
	public $css_dir;
	public $css_path;
	public $css_url;
	
	public $cache_dir;
	public $cache_path;
	public $cache_url;
	
	
	// Files that should be processed
	private $_js;
	private $_css;
	
	
	// Config
	public $combine              = true;  // Combine files
	public $minify               = false; // Minify all
	public $minify_js            = true;
	public $minify_css           = true;
	public $auto_clear_cache     = false; // Automaticly clear all cache before creating new cache files
	public $auto_clear_css_cache = false; // Or clear just cached CSS files
	public $auto_clear_js_cache  = false; // Or just cached JS files
	public $html5                = true;  // Use HTML5 tags
	public $env                  = 'production';
	
	
	/* ------------------------------------------------------------------------------------------ */
	
	/**
	 *
	 */
	public function __construct($cfg = null)
	{
		$this->ci =& get_instance();
		
		// Load the resources and config
		$this->ci->load->library(array('lessc'));
		
		// Load JSMin
		include(reduce_double_slashes(SPARKPATH.'assets/'.ASSETS_VERSION.'/libraries/jsmin.php'));
		
		// Load CSSMin
		include(reduce_double_slashes(SPARKPATH.'assets/'.ASSETS_VERSION.'/libraries/cssmin.php'));
		
		// Initialize LessPHP
		$this->less = new lessc();
		
		// Add config to library
		if ($cfg)
		{
			$this->configure(array_merge($cfg), config_item('assets'));
		}
		else
		{
			$this->configure(config_item('assets'));
		}
		
	} // __contruct()
	
	
	/* ------------------------------------------------------------------------------------------ */
	
	/**
	 * Add new CSS file for processing
	 *
	 */
	public function css($file = null)
	{
		if ($file)
		{
			// Multiple files as array are supported
			if (is_array($file))
			{
				foreach ($file as $f)
				{
					$this->css($f);
				}
			}
			
			// Single file
			else
			{
				$this->_css[] = $file;
			}
		}
		
	} // css()
	
	
	/* ------------------------------------------------------------------------------------------ */
	
	/**
	 * Add new JS file for processing
	 *
	 */
	public function js($file = null)
	{
		if ($file)
		{
			// Multiple files as array are supported
			if (is_array($file))
			{
				foreach ($file as $f)
				{
					$this->js($f);
				}
			}
			
			// Single file
			else
			{
				$this->_js[] = $file;
			}
		}
				
	} // js()
	
	
	
	/* ------------------------------------------------------------------------------------------ */
	/* !/===> Processing files, generating HTML tags */
	/* ------------------------------------------------------------------------------------------ */
	
	
	/**
	 *
	 */
	public function get($type = 'all')
	{
		$html = '';
		
		if ($type == 'all')
		{
			$html .= $this->_get_css();
			$html .= $this->_get_js();
		}
		elseif ($type == 'css')
		{
			$html .= $this->_get_css();
		}
		elseif ($type == 'js')
		{
			$html .= $this->_get_js();
		}
		
		return $html;
		
	} // get()
	
	
	/* ------------------------------------------------------------------------------------------ */
	
	/**
	 *
	 */
	function _get_css()
	{
		$html = '';
		
		if ($this->_css)
		{
			// Simply return a list of all css tags
			if ($this->env == 'dev' or ( ! $this->combine and ( ! $this->minify and ! $this->minify_css) and ! $this->less_css))
			{
				foreach ($this->_css as $css)
				{
					$html .= $this->_tag($this->css_url.'/'.$css);
				}
			
			}
			else
			{
				// Try to cache assets and get html tag
				$files = $this->_cache_assets($this->_css, 'css');
				
				// Add to html
				foreach ($files as $file)
				{
					$html .= $this->_tag($file);
				}
			}
		}
		
		return $html;
		
	} // _get_css()
	
	
	/* ------------------------------------------------------------------------------------------ */
	
	/**
	 *
	 */
	function _get_js()
	{
		$html = '';
		
		if ($this->_js)
		{
			// Simply return a list of all css tags
			if ($this->env == 'dev' or ( ! $this->combine and ( ! $this->minify and ! $this->minify_js)))
			{
				foreach ($this->_js as $js)
				{
					$html .= $this->_tag($this->js_url.'/'.$js);
				}
			}
			else
			{
				// Try to cache assets and get html tag
				$files = $this->_cache_assets($this->_js, 'js');
				
				// Add to html
				foreach ($files as $file)
				{
					$html .= $this->_tag($file);
				}
			}
		}
		
		return $html;
		
	} // _get_js()
	
	
	/* ------------------------------------------------------------------------------------------ */
	
	/**
	 * Caches the assets if needed and returns a list files/paths
	 */
	private function _cache_assets($assets = null, $type = null)
	{
		$files = array(); // Will contain all the processed files
		
		if ($assets and $type)
		{
			$last_modified = 0;
			$path          = ($type == 'css') ? $this->css_path : $this->js_path ;
			
			if ($this->combine)
			{
				// Find last modified file
				foreach ($assets as $asset)
				{
					$last_modified 	= max($last_modified, filemtime(realpath($path.'/'.$asset)));
				}
				
				// Now check if the file exists in the cache directory
				$file_name = date('YmdHis', $last_modified).'.'.$type;
				$file_path = reduce_double_slashes($this->cache_path.'/'.$file_name);
				
				if ( ! file_exists($file_path))
				{
					$data = '';
					
					// Get file contents
					foreach ($assets as $asset)
					{
						// Get file contents
						$contents = read_file(reduce_double_slashes($path.'/'.$asset));
						$pathinfo = pathinfo($asset);
						if ($pathinfo['dirname'] != '.') 	$base_url = $this->css_url.'/'.$pathinfo['dirname'];
						else 								$base_url = $this->css_url;
						
						// Process asset
						$data .= $this->_process($contents, $type, 'minify', $base_url);
					}
					
					// Process with less and minify
					if ($type == 'css')
					{
						$data = $this->_process($data, $type, 'less');
					}
					
					// Auto clear cache directory?
					if ($type == 'css' and ($this->auto_clear_cache or $this->auto_clear_css_cache))
					{
						$this->clear_css_cache();
					}
					
					if ($type == 'js' and ($this->auto_clear_cache or $this->auto_clear_js_cache))
					{
						$this->clear_js_cache();
					}
					
					// And save the file
					write_file($file_path, $data);
				}
				
				// Add to files
				$files[] = reduce_double_slashes($this->cache_url.'/'.$file_name);
			}
			
			// No combining
			else
			{
				foreach ($assets as $asset)
				{
					$last_modified 	= filemtime(realpath($path.'/'.$asset));
					
					// Now check if the file exists in the cache directory
					$file 		= pathinfo($asset);
					$file_name 	= date('YmdHis', $last_modified).'.'.$file['filename'].'.'.$type;
					$file_path 	= reduce_double_slashes($this->cache_path.'/'.$file_name);
					
					if ( ! file_exists($file_path))
					{
						// Get file contents
						$data = read_file(reduce_double_slashes($path.'/'.$asset));
						
						// Process
						$data = $this->_process($data, $type, 'all', site_url($this->css_url));
						
						// Auto clear cache directory?
						if ($type == 'css' and ($this->auto_clear_cache or $this->auto_clear_css_cache))
						{
							$this->clear_css_cache($asset);
						}
						
						if ($type == 'js' and ($this->auto_clear_cache or $this->auto_clear_js_cache))
						{
							$this->clear_js_cache($asset);
						}
						
						// And save the file
						write_file($file_path, $data);
					}
					
					// Add to files
					$files[] = reduce_double_slashes($this->cache_url.'/'.$file_name);
				}
			}
		}
		
		return $files;
		
	} // _cache_assets()
	
	
	/* ------------------------------------------------------------------------------------------ */
	
	/**
	 * Minify, less
	 *
	 */
	function _process($data = null, $type = null, $do = 'all', $base_url = null)
	{
		if ( ! $base_url) $base_url = $this->base_url;
		
		if ($type == 'css')
		{
			if ($this->less_css and ($do == 'all' or $do == 'less'))
			{
				$data = $this->less->parse($data);
			}
			
			if (($this->minify or $this->minify_css) and ($do == 'all' or $do == 'minify'))
			{
				$data = CSSMin::minify($data, array(
					'currentDir'          => str_replace(site_url(), '', $base_url).'/',
				));
			}
		}
		else
		{
			if (($this->minify or $this->minify_js) and ($do == 'all' or $do == 'minify'))
			{
				$data = JSMin::minify($data);
			}
		}
		
		return $data;
		
	} // _process()
	
	
	/* ------------------------------------------------------------------------------------------ */
	
	/**
	 *
	 */
	private function _tag($file = null, $type = null)
	{
		// Try to figure out a type if none passed
		if ( ! $type)
		{
			$type = substr(strrchr($file,'.'),1);
		}
		
		// Now return CSS html tag
		if ($file and $type == 'css')
		{
			if ($this->html5) {
				return '<link rel="stylesheet" href="'.$file.'">'.PHP_EOL;
			}
			else
			{
				return '<link rel="stylesheet" type="text/css" href="'.$file.'" />'.PHP_EOL;
			}
		}
		
		// And the JS html tag
		elseif ($file and $type == 'js')
		{
			if ($this->html5)
			{
				return '<script src="'.$file.'"></script>'.PHP_EOL;
			}
			else
			{
				return '<script src="'.$file.'" type="text/javascript" charset="utf-8"></script>'.PHP_EOL;
			}
		}
		
		return null;
		
	} // _tag()
	
	
	
	/* ------------------------------------------------------------------------------------------ */
	/* !/===> Displaying assets */
	/* ------------------------------------------------------------------------------------------ */
	
	
	/**
	 *
	 */
	public function display($type = 'all', $css = null, $js = null, $cfg = null)
	{
		// Configuration
		if ($cfg) $this->configure($cfg);
		
		// Overwrite CSS files
		if ($css)
		{
			$this->_css = array();
			$this->css($css);
		}
		
		// Overwrite JS files
		if ($js)
		{
			$this->_js = array();
			$this->js($js);
		}
		
		// Display all the tags
		echo $this->get($type);
		
	} // display()
	
	
	/* ------------------------------------------------------------------------------------------ */
	
	/**
	 *
	 */
	public function display_css($assets = null, $cfg = null)
	{
		$this->display('css', $assets, null, $cfg);
		
	} // display_css()
	
	
	/* ------------------------------------------------------------------------------------------ */
	
	/**
	 *
	 */
	public function display_js($assets = null, $cfg = null)
	{
		$this->display('js', null, $assets, $cfg);
		
	} // display_js()
	
	
	
	/* ------------------------------------------------------------------------------------------ */
	/* !/===> Deleting files */
	/* ------------------------------------------------------------------------------------------ */
	
	/**
	 *
	 */
	public function clear_cache($type = null, $asset_file = null)
	{
		$files = directory_map($this->cache_path, 1);
		
		if ($files)
		{
			foreach ($files as $file)
			{
				if ( ! is_array($file))
				{
					$file_path = reduce_double_slashes($this->cache_path.'/'.$file);
					$file_info = pathinfo($file_path);
					
					// Clear single file cache
					if ($asset_file)
					{
						$dev_file_name = substr($file, 15); // Get the real filename, without the timestamp prefix
						
						// Compare file name and remove if necesary
						if ($dev_file_name == $asset_file)
						{
							unlink($file_path);
							//echo 'Deleted asset: '.$file."<br>\n";
						}
					}
					
					// Or all files
					else
					{
						if (is_file($file_path) and $file_info)
						{
							// Delete the CSS files
							if ($file_info['extension'] == 'css' and ( ! $type or $type == 'css'))
							{
								unlink($file_path);
								//echo 'Deleted CSS: '.$file."<br>\n";
							}
							
							// Delete the JS files
							if ($file_info['extension'] == 'js' and ( ! $type or $type == 'js'))
							{
								unlink($file_path);
								//echo 'Deleted JS: '.$file."<br>\n";
							}
						}
					}
				}
			}
		}
		
	} // clear_cache()
	
	
	/* ------------------------------------------------------------------------------------------ */
	
	/**
	 *
	 */
	public function clear_css_cache($asset_file = null)
	{
		return $this->clear_cache('css', $asset_file);
		
	} // empty_css_cache()
	
	
	/* ------------------------------------------------------------------------------------------ */
	
	/**
	 *
	 */
	public function clear_js_cache($asset_file = null)
	{
		return $this->clear_cache('js', $asset_file);
		
	} // empty_js_cache()
	
	
	
	/* ------------------------------------------------------------------------------------------ */
	/* !/===> Configuration */
	/* ------------------------------------------------------------------------------------------ */
	
	
	/**
	 * Configure the library
	 *
	 */
	public function configure($cfg = null)
	{
		$cfg = array_merge($cfg, config_item('assets'));
		
		if ($cfg and is_array($cfg))
		{
			foreach ($cfg as $key=>$val)
			{
				$this->$key = $val;
				//echo 'CONFIG: ', $key, ' :: ', $val, '<br>';
			}
		}
		
		// Prepare all the paths and URI's
		$this->_paths();
		
	} // configure()
	
	
	/* ------------------------------------------------------------------------------------------ */
	
	/**
	 *
	 */
	private function _paths()
	{
		// Set the assets base path
		$this->base_path = reduce_double_slashes(realpath($this->assets_dir));
		
		// Now set the assets base URL
		$this->base_url = reduce_double_slashes(config_item('base_url').'/'.$this->assets_dir);
		
		// And finally the paths and URL's to the css and js assets
		$this->js_path 		= reduce_double_slashes($this->base_path .'/'.$this->js_dir);
		$this->js_url 		= reduce_double_slashes($this->base_url  .'/'.$this->js_dir);
		$this->css_path 	= reduce_double_slashes($this->base_path .'/'.$this->css_dir);
		$this->css_url 		= reduce_double_slashes($this->base_url  .'/'.$this->css_dir);
		$this->cache_path 	= reduce_double_slashes($this->base_path .'/'.$this->cache_dir);
		$this->cache_url 	= reduce_double_slashes($this->base_url  .'/'.$this->cache_dir);
		
		// Check if all directories exist
		if ( ! is_dir($this->js_path))
		{
			if ( ! @mkdir($this->js_path, 0755))    exit('Error with JS directory.');
		}
		
		if ( ! is_dir($this->css_path))
		{
			if ( ! @mkdir($this->css_path, 0755))   exit('Error with CSS directory.');
		}
		
		if ( ! is_dir($this->cache_path))
		{
			if ( ! @mkdir($this->cache_path, 0777)) exit('Error with CACHE directory.');
		}
		
		// Try to make the cache direcory writable
		if (is_dir($this->cache_path) and ! is_really_writable($this->cache_path))
		{
			@chmod($this->cache_path, 0777);
		}
		
		// If it's still not writable throw error
		if ( ! is_dir($this->cache_path) or ! is_really_writable($this->cache_path))
		{
			exit('Error with CACHE directory.');
		}
		
	} // _paths()
	
	
	/* ------------------------------------------------------------------------------------------ */
	
} //end Assets


/* End of file assets.php */