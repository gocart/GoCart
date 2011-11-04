<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Assets Helper
 *
 * @author 		Boris Strahija <boris@creolab.hr>
 * @copyright 	Copyright (c) 2010, Boris Strahija
 * @version 	0.6.0
 * 
 */



/* ------------------------------------------------------------------------------------------ */

function display_css($assets = null)
{
	$ci =& get_instance();
	$ci->assets->display_css($assets);
	
} // end display_css()


/* ------------------------------------------------------------------------------------------ */

/**
 *
 */
function display_js($assets = null)
{
	$ci =& get_instance();
	$ci->assets->display_js($assets);
	
} // end display_js()


/* ------------------------------------------------------------------------------------------ */

/**
 *
 */
function clear_cache($type = null)
{
	$ci =& get_instance();
	$ci->assets->clear_cache($type);
	
} // end clear_cache()


/* ------------------------------------------------------------------------------------------ */



/* End of file assets_helper.php */