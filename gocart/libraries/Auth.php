<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth
{
    var $CI;

    function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->helper('url');
    }
    
    function check_access($access, $default_redirect=false, $redirect = false)
    {
        /*
        we could store this in the session, but by accessing it this way
        if an admin's access level gets changed while they're logged in
        the system will act accordingly.
        */
        
        $admin = $this->CI->session->userdata('admin');
        
        $this->CI->db->select('access');
        $this->CI->db->where('id', $admin['id']);
        $this->CI->db->limit(1);
        $result = $this->CI->db->get('admin');
        $result = $result->row();
        
        //result should be an object I was getting odd errors in relation to the object.
        //if $result is an array then the problem is present.
        if(!$result || is_array($result))
        {
            $this->logout();
            return false;
        }
    //  echo $result->access;
        if ($access)
        {
            if ($access == $result->access)
            {
                return true;
            }
            else
            {
                if ($redirect)
                {
                    redirect($redirect);
                }
                elseif($default_redirect)
                {
                    redirect(config_item('admin_folder').'/dashboard/');
                }
                else
                {
                    return false;
                }
            }
            
        }
    }
    
    /*
    this checks to see if the admin is logged in
    we can provide a link to redirect to, and for the login page, we have $default_redirect,
    this way we can check if they are already logged in, but we won't get stuck in an infinite loop if it returns false.
    */
    function is_logged_in($redirect = false, $default_redirect = true)
    {
    
        //var_dump($this->CI->session->userdata('session_id'));

        //$redirect allows us to choose where a customer will get redirected to after they login
        //$default_redirect points is to the login page, if you do not want this, you can set it to false and then redirect wherever you wish.

        $admin = $this->CI->session->userdata('admin');
        
        if (!$admin)
        {
            //check the cookie
            if(isset($_COOKIE['GoCartAdmin']))
            {
                //the cookie is there, lets log the customer back in.
                $info = $this->aes256Decrypt(base64_decode($_COOKIE['GoCartAdmin']));
                $cred = json_decode($info, true);

                if(is_array($cred))
                {
                    if( $this->login_admin($cred['username'], $cred['password']) )
                    {
                        return $this->is_logged_in($redirect, $default_redirect);
                    }
                }
            }

            if ($redirect)
            {
                $this->CI->session->set_flashdata('redirect', $redirect);
            }
                
            if ($default_redirect)
            {   
                redirect(config_item('admin_folder').'/login');
            }
            
            return false;
        }
        else
        {
            return true;
        }
    }
    /*
    this function does the logging in.
    */
    function login_admin($username, $password, $remember=false)
    {
        // make sure the username doesn't go into the query as false or 0
        if(!$username)
        {
            return false;
        }

        $this->CI->db->select('*');
        $this->CI->db->where('username', $username);
        $this->CI->db->where('password',  sha1($password));
        $this->CI->db->limit(1);
        $result = $this->CI->db->get('admin');
        $result = $result->row_array();
        
        if (sizeof($result) > 0)
        {
            $admin = array();
            $admin['admin'] = array();
            $admin['admin']['id'] = $result['id'];
            $admin['admin']['access'] = $result['access'];
            $admin['admin']['firstname'] = $result['firstname'];
            $admin['admin']['lastname'] = $result['lastname'];
            $admin['admin']['email'] = $result['email'];
            $admin['admin']['username'] = $result['username'];
            
            if($remember)
            {
                $loginCred = json_encode(array('username'=>$username, 'password'=>$password));
                $loginCred = base64_encode($this->aes256Encrypt($loginCred));
                //remember the user for 6 months
                $this->generateCookie($loginCred, strtotime('+6 months'));
            }

            $this->CI->session->set_userdata($admin);
            return true;
        }
        else
        {
            return false;
        }
    }
    
    private function generateCookie($data, $expire)
    {
        setcookie('GoCartAdmin', $data, $expire, '/', $_SERVER['HTTP_HOST']);
    }

    private function aes256Encrypt($data)
    {
        $key = config_item('encryption_key');
        if(32 !== strlen($key))
        {
            $key = hash('SHA256', $key, true);
        }
        $padding = 16 - (strlen($data) % 16);
        $data .= str_repeat(chr($padding), $padding);
        return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, str_repeat("\0", 16));
    }

    private function aes256Decrypt($data) {
        $key = config_item('encryption_key');
        if(32 !== strlen($key))
        {
            $key = hash('SHA256', $key, true);
        }
        $data = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, str_repeat("\0", 16));
        $padding = ord($data[strlen($data) - 1]); 
        return substr($data, 0, -$padding); 
    }

    /*
    this function does the logging out
    */
    function logout()
    {
        $this->CI->session->unset_userdata('admin');
        //force expire the cookie
        $this->generateCookie('[]', time()-3600);
    }

    /*
    This function resets the admins password and usernames them a copy
    */
    function reset_password($username)
    {
        $admin = $this->get_admin_by_username($username);
        if ($admin)
        {
            $this->CI->load->helper('string');
            $this->CI->load->library('email');
            
            $new_password       = random_string('alnum', 8);
            $admin['password']  = sha1($new_password);
            $this->save_admin($admin);
            
            $this->CI->email->from(config_item('email'), config_item('site_name'));
            $this->CI->email->to($admin['email']);
            $this->CI->email->subject(config_item('site_name').': Admin Password Reset');
            $this->CI->email->message('Your password has been reset to '. $new_password .'.');
            $this->CI->email->send();
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /*
    This function gets the admin by their username address and returns the values in an array
    it is not intended to be called outside this class
    */
    private function get_admin_by_username($username)
    {
        $this->CI->db->select('*');
        $this->CI->db->where('username', $username);
        $this->CI->db->limit(1);
        $result = $this->CI->db->get('admin');
        $result = $result->row_array();

        if (sizeof($result) > 0)
        {
            return $result; 
        }
        else
        {
            return false;
        }
    }
    
    /*
    This function takes admin array and inserts/updates it to the database
    */
    function save($admin)
    {
        if ($admin['id'])
        {
            $this->CI->db->where('id', $admin['id']);
            $this->CI->db->update('admin', $admin);
        }
        else
        {
            $this->CI->db->insert('admin', $admin);
        }
    }
    
    
    /*
    This function gets a complete list of all admin
    */
    function get_admin_list()
    {
        $this->CI->db->select('*');
        $this->CI->db->order_by('lastname', 'ASC');
        $this->CI->db->order_by('firstname', 'ASC');
        $this->CI->db->order_by('email', 'ASC');
        $this->CI->db->order_by('username', 'ASC');
        $result = $this->CI->db->get('admin');
        $result = $result->result();
        
        return $result;
    }

    /*
    This function gets an individual admin
    */
    function get_admin($id)
    {
        $this->CI->db->select('*');
        $this->CI->db->where('id', $id);
        $result = $this->CI->db->get('admin');
        $result = $result->row();

        return $result;
    }       
    
    function check_id($str)
    {
        $this->CI->db->select('id');
        $this->CI->db->from('admin');
        $this->CI->db->where('id', $str);
        $count = $this->CI->db->count_all_results();
        
        if ($count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }   
    }
    
    function check_username($str, $id=false)
    {
        $this->CI->db->select('username');
        $this->CI->db->from('admin');
        $this->CI->db->where('username', $str);
        if ($id)
        {
            $this->CI->db->where('id !=', $id);
        }
        $count = $this->CI->db->count_all_results();
        
        if ($count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function delete($id)
    {
        if ($this->check_id($id))
        {
            $admin  = $this->get_admin($id);
            $this->CI->db->where('id', $id);
            $this->CI->db->limit(1);
            $this->CI->db->delete('admin');

            return $admin->firstname.' '.$admin->lastname.' has been removed.';
        }
        else
        {
            return 'The admin could not be found.';
        }
    }
}