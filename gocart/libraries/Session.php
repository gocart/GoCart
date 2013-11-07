<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Session Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/sessions.html
 */
class CI_Session {
	/**
	 * Session Constructor
	 *
	 * The constructor runs the session routines automatically
	 * whenever the class is instantiated.
	 */
	public function __construct()
	{
		log_message('debug', "Session Class Initialized");
	}

	// --------------------------------------------------------------------

	/**
	 * Destroy the current session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_destroy()
	{
		//clear the userdata var
		$_SESSION['userdata'] = array();
		$_SESSION['newFlashdata'] = array();
		$_SESSION['oldFlashdata'] = array();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch a specific item from the session array
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function userdata($item)
	{
		return ( ! isset($_SESSION['userdata'][$item])) ? FALSE : $_SESSION['userdata'][$item];
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch all session data
	 *
	 * @access	public
	 * @return	array
	 */
	function all_userdata()
	{
		return $_SESSION['userdata'];
	}

	// --------------------------------------------------------------------

	/**
	 * Add or change data in the "userdata" array
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	void
	 */
	function set_userdata($newdata = array(), $newval = '')
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => $newval);
		}

		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				$_SESSION['userdata'][$key] = $val;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Delete a session variable from the "userdata" array
	 *
	 * @access	array
	 * @return	void
	 */
	function unset_userdata($newdata = array())
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => '');
		}

		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				unset($_SESSION['userdata'][$key]);
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Add or change flashdata, only available
	 * until the next request
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	void
	 */
	function set_flashdata($newdata = array(), $newval = '')
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => $newval);
		}

		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
		 	{
				$_SESSION['newFlashdata'][$key] = $val;
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Keeps existing flashdata available to next request.
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	function keep_flashdata($key)
	{
		// 'old' flashdata gets removed.  Here we mark all
		// flashdata as 'new' to preserve it from _flashdata_sweep()
		// Note the function will return FALSE if the $key
		// provided cannot be found

		$_SESSION['newFlashdata'][$key] = $_SESSION['oldFlashdata'][$key];
	}

	// ------------------------------------------------------------------------

	/**
	 * Fetch a specific flashdata item from the session array
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function flashdata($key)
	{
		if(isset($_SESSION['oldFlashdata'][$key]))
		{
			return $_SESSION['oldFlashdata'][$key];
		}
		else
		{
			return false;
		}
		
	}
}
// END Session Class

/* End of file Session.php */
/* Location: ./system/libraries/Session.php */