<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Session extends CI_Session{

	/**
	 * Session Constructor
	 *
	 * The constructor runs the session routines automatically
	 * whenever the class is instantiated.
	 */		
	function __construct()
	{
		parent::__construct();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Fetch the current session data if it exists
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_read()
	{	
		// Fetch the cookie
		$session = $this->CI->input->cookie($this->sess_cookie_name);
		
		// No cookie?  Goodbye cruel world!...
		if ($session === FALSE)
		{
			log_message('debug', 'A session cookie was not found.');
			return FALSE;
		}
		
		// Decrypt the cookie data
		if ($this->sess_encrypt_cookie == TRUE)
		{
			$session = $this->CI->encrypt->decode($session);
		}
		else
		{	
			// encryption was not used, so we need to check the md5 hash
			$hash	 = substr($session, strlen($session)-32); // get last 32 chars
			$session = substr($session, 0, strlen($session)-32);

			// Does the md5 hash match?  This is to prevent manipulation of session data in userspace
			if ($hash !==  md5($session.$this->encryption_key))
			{
				log_message('error', 'The session cookie data did not match what was expected. This could be a possible hacking attempt.');
				$this->sess_destroy();
				return FALSE;
			}
		}
		
		// Unserialize the session array
		$session = $this->_unserialize($session);
		
		// Is the session data we unserialized an array with the correct format?
		if ( ! is_array($session) OR ! isset($session['session_id']) OR ! isset($session['ip_address']) OR ! isset($session['user_agent']) OR ! isset($session['last_activity']))
		{
			$this->sess_destroy();
			return FALSE;
		}
		
		// Is the session current?
		if (($session['last_activity'] + $this->sess_expiration) < $this->now)
		{
			$this->sess_destroy();
			return FALSE;
		}

		// Does the IP Match?
		if ($this->sess_match_ip == TRUE AND $session['ip_address'] != $this->CI->input->ip_address())
		{
			$this->sess_destroy();
			return FALSE;
		}
		
		// Does the User Agent Match?
		if ($this->sess_match_useragent == TRUE AND trim($session['user_agent']) != trim(substr($this->CI->input->user_agent(), 0, 50)))
		{
			$this->sess_destroy();
			return FALSE;
		}
		
		// Is there a corresponding session in the DB?
		if ($this->sess_use_database === TRUE)
		{
			$this->CI->db->where('session_id', $session['session_id']);
					
			if ($this->sess_match_ip == TRUE)
			{
				$this->CI->db->where('ip_address', $session['ip_address']);
			}

			if ($this->sess_match_useragent == TRUE)
			{
				$this->CI->db->where('user_agent', $session['user_agent']);
			}
			
			$query = $this->CI->db->get($this->sess_table_name);

			// No result?  Kill it!
			if ($query->num_rows() == 0)
			{
				$this->sess_destroy();
				return FALSE;
			}

			// Is there custom data?  If so, add it to the main session array
			$row = $query->row();
			if (isset($row->user_data) AND $row->user_data != '')
			{
				if ($this->sess_encrypt_cookie == TRUE)
				{
					$custom_data = $this->_unserialize($this->CI->encrypt->decode($row->user_data));
				}
				else
				{
						$custom_data = $this->_unserialize($row->user_data);
				}
				if (is_array($custom_data))
				{
					foreach ($custom_data as $key => $val)
					{
						$session[$key] = $val;
					}
				}
			}				
		}
	
		// Session is valid!
		$this->userdata = $session;
		unset($session);
		
		return TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Write the session data
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_write()
	{
		// Are we saving custom data to the DB?  If not, all we do is update the cookie
		if ($this->sess_use_database === FALSE)
		{
			$this->_set_cookie();
			return;
		}

		// set the custom userdata, the session data we will set in a second
		$custom_userdata = $this->userdata;
		$cookie_userdata = array();
		
		// Before continuing, we need to determine if there is any custom data to deal with.
		// Let's determine this by removing the default indexes to see if there's anything left in the array
		// and set the session data while we're at it
		foreach (array('session_id','ip_address','user_agent','last_activity') as $val)
		{
			unset($custom_userdata[$val]);
			$cookie_userdata[$val] = $this->userdata[$val];
		}
		
		// Did we find any custom data?  If not, we turn the empty array into a string
		// since there's no reason to serialize and store an empty array in the DB
		if (count($custom_userdata) === 0)
		{
			$custom_userdata = '';
		}
		else
		{	
			// Serialize the custom data array so we can store it
			//09/10/09 encrypt the userdata after its serialized
			
			
			if ($this->sess_encrypt_cookie == TRUE)
			{
				$custom_userdata = $this->CI->encrypt->encode($this->_serialize($custom_userdata));
			}
			else
			{
				$custom_userdata = $this->_serialize($custom_userdata);
			}
		}
		
		// Run the update query
		$this->CI->db->where('session_id', $this->userdata['session_id']);
		$this->CI->db->update($this->sess_table_name, array('last_activity' => $this->userdata['last_activity'], 'user_data' => $custom_userdata));

		// Write the cookie.  Notice that we manually pass the cookie data array to the
		// _set_cookie() function. Normally that function will store $this->userdata, but 
		// in this case that array contains custom data, which we do not want in the cookie.
		$this->_set_cookie($cookie_userdata);
	}
	
}
// END Session Class

/* End of file My_Session.php */
/* Location: ./system/application/libraries/Session.php */