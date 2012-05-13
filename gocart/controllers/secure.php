<?php

class Secure extends CI_Controller {

	//we collect the categories automatically with each load rather than for each function
	//this just cuts the codebase down a bit
	var $categories	= '';
	
	//this is so there will be a breadcrumb on every page even if it is blank
	//the breadcrumbs currently suck. on a product page if you refresh, you lose the path
	//will have to find a better way for these, but it's not a priority
	var $breadcrumb	= '';	
	
	// determine whether to display gift card link on all cart pages
	var $gift_cards_enabled = false; 
	
	//load all the pages into this variable so we can call it from all the methods
	var $pages;
	
	var $customer;
	
	function __construct()
	{
		parent::__construct();
		
		//check to see if they are on a secure URL, this will stop them from typing in the insecure url and
		//attempting to force an insecure page.... why would someone do this? I dunnno....
		force_ssl();
		
		$this->load->library('Go_cart');
		$this->load->model(array('Page_model', 'Product_model', 'Option_model','location_model'));
		$this->load->helper('form_helper');
		
		$this->customer = $this->go_cart->customer();
		
		//fill up our categories variable
		$this->categories =  $this->Category_model->get_categories_tierd(0);
		$this->pages		= $this->Page_model->get_pages();
		$gc_setting = $this->Settings_model->get_settings('gift_cards');
		
		if(!empty($gc_setting['enabled']) && $gc_setting['enabled']==1)
		{
			$this->gift_cards_enabled = true;
		}
		
		//load the theme package
		$this->load->add_package_path(APPPATH.'themes/'.$this->config->item('theme').'/');
	}
	
	function index()
	{
		//we don't have a default landing page for secure
		redirect('');
	}
	function login_facebook($ajax = false)
        {

           session_start();

            // IF the User Decided to Login using one of the Social Provider then the $social will contain the provider name
            $submitted 		= $this->input->post('submitted');
            if ($submitted)
            {
                $redirect	= $this->input->post('redirect');
                $social         = $this->input->post('social');
                $openidurl = '';
                if('facebook' == $social)
                {
                    // Connect to FaceBook For Authentication.
                    $app_id = $this->config->item('Facebook_APPID');
                    $app_secret = $this->config->item('Facebook_APPSecret');
                    
                    $my_url = site_url('secure/login_facebook');
                    $_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
                    $dialog_url = "https://www.facebook.com/dialog/oauth?client_id=" 
                       . $app_id . "&redirect_uri=" . urlencode($my_url) . "&state="
                       . $_SESSION['state']."&scope=email";
                    echo " Connecting To FaceBook for Authentication ....  Please Wait .....";
                    echo "<br/> Dont Press Back Button .....";
                    echo("<script> top.location.href='" . $dialog_url . "'</script>");
                }
                else
                {
                    // OOPS something went wrong, fallback safe.
                    redirect('secure/login');
                }
            }
            else
            {
                 
                /* We might reach here when the FaceBook Authorization Happened
                or if the user has directly launched this url with Social value */
                // Avoid Cross Site Scripting
                if($_REQUEST['state'] == $_SESSION['state']) 
                {
                    // Using the Code get the Access Token
                    if(isset($_REQUEST["code"]))
                    {        
                        $app_id = $this->config->item('Facebook_APPID');
                        $app_secret = $this->config->item('Facebook_APPSecret');
                        
                        $my_url = site_url('secure/login_facebook');
                        $code = $_REQUEST["code"];

                        $token_url = "https://graph.facebook.com/oauth/access_token?"
                           . "client_id=" . $app_id . "&redirect_uri=" . urlencode($my_url)
                           . "&client_secret=" . $app_secret . "&code=" . $code;

                        $response = file_get_contents($token_url);
                        $statuscode = explode(' ',$http_response_header[0] );
                        if('200' != $statuscode[1])
                        {
                            // If we didnt Get Proper Response then Redirect Login.
                            // In future we should display proper Error Message Also.
                            redirect('secure/login');
                        }
                        $params = null;
                        parse_str($response, $params);
                        
                        
                        $graph_url = "https://graph.facebook.com/me?access_token=" 
                           . $params['access_token'];
                        $dataresponse = file_get_contents($graph_url);
                        $statuscode = explode(' ',$http_response_header[0] );
                        if('200' != $statuscode[1])
                        {
                            // If there is Error with the Access Token then we might get Response Code 400.
                            redirect('secure/login');
                        }
                        
                        $user = json_decode($dataresponse,true);

                        // We should have a valid Email returned by the Social Provider
                        $this->load->library('form_validation');
                        if($this->form_validation->valid_email($user['email']))
                        {
                            // Check whether the User is already existing, if so then login him
                            if($this->Customer_model->check_email($user['email']))
                            {
                                $redirect = $this->session->flashdata('redirect');
                                $login		= $this->Customer_model->login_social($user['email']);
                                if ($login)
                                {
                                    if ($redirect == '')
                                    {
                                            //if there is not a redirect link, send them to the my account page
                                            $redirect = 'secure/my_account';
                                    }
                                    //to login via ajax
                                    if($ajax)
                                    {
                                            die(json_encode(array('result'=>true)));
                                    }
                                    else
                                    {
                                            redirect($redirect);
                                    }

                                }
                                else
                                {
                                    //this adds the redirect back to flash data if they provide an incorrect credentials
                                    //
                                    //to login via ajax
                                    if($ajax)
                                    {
                                            die(json_encode(array('result'=>false)));
                                    }
                                    else
                                    {
                                            $this->session->set_flashdata('redirect', $redirect);
                                            $this->session->set_flashdata('error', lang('login_failed'));

                                            redirect('secure/login');
                                    }
                                }
                            }
                            // Else Register for New User
                            else
                            {
                                $redirect = $this->session->flashdata('redirect');
                                $data['page_title']	= lang('account_registration');
                                $data['gift_cards_enabled'] = $this->gift_cards_enabled;
                                $data['redirect'] = $redirect;
                                $data['email']=$user['email'];
                                $data['firstname']=$user['first_name'];
                                $data['lastname']=$user['last_name'];
                                $this->load->view('register_social',$data);
                            }        
                        }
                        else
                        {
                            // We should receive a valid Email from the Social otherwise we cant proceed. So redirecting the User to Login Page
                            redirect('secure/login');
                        }

                    }
                    else
                    {
                            // May Be the User Denied To Authorize
                            redirect('secure/login');
                    }
                }
            }
        }

        function login_social($ajax = false)
        {
            // Load the OpenID Third Party Library
            // Credit Goes to "https://gitorious.org/lightopenid"
            // This Library is Governed by MIT License
         
            // The following code is not working in my Hosting Provider  :-(, so following a dirty method
             
             require_once APPPATH .'thirdparty/openid/openid.php';
            //require_once '../third_party/openid/openid.php';
            $openidconnection = new LightOpenID(base_url());

            // IF the User Decided to Login using one of the Social Provider then the $social will contain the provider name
            $submitted 		= $this->input->post('submitted');
            if ($submitted)
            {
                $redirect	= $this->input->post('redirect');
                $social         = $this->input->post('social');
                $openidurl = '';
                if('google' == $social)
                {
                    $openidurl = 'https://www.google.com/accounts/o8/id';
                }
                else if('yahoo' == $social)
                {
                    $openidurl = 'yahoo.com';
                }
                else
                {
                    // Invalid Social
                    $this->session->set_flashdata('redirect', $redirect);
                        redirect('secure/login');
                }
                if('' != $openidurl)
                {
                    // Store the Redirection Address for later referrel.
                    $this->session->set_flashdata('redirect', $redirect);

                    if(!$openidconnection->mode) 
                    {
                        $openidconnection->identity = $openidurl;
                        # The following two lines request email, full name, and a nickname
                        # from the provider. Remove them if you don't need that data.
                        $openidconnection->required = array('contact/email','namePerson/first', 'namePerson/last');
                        $openidconnection->optional = array('namePerson');
                        header('Location: ' . $openidconnection->authUrl());
                    }
                    else
                    {
                        // OOPS something went wrong, fallback safe.
                        redirect('secure/login');
                    }
                }
            }
            else
            {
                
                /* We might reach here when the Social Provider is providing the information back to us
                or if the user has directly launched this url with Social value */
                if($openidconnection->mode == 'cancel') 
                {
                    // If the Authetication is cancelled by the user then redirect him to the Login Screen.
                    redirect('secure/login');
                } 
                else 
                {
                    // Check whether the User is Successfully Authenticated.
                    if($openidconnection->validate())
                    {
                        $userAttributes = $openidconnection->getAttributes();
                        // We should have a valid Email returned by the Social Provider
                        $this->load->library('form_validation');
                        if($this->form_validation->valid_email($userAttributes['contact/email']))
                        {
                            // Check whether the User is already existing, if so then login him
                            if($this->Customer_model->check_email($userAttributes['contact/email']))
                            {
                                $redirect = $this->session->flashdata('redirect');
                                $email = $userAttributes['contact/email'];
                                $login		= $this->Customer_model->login_social($email);
                                if ($login)
                                {
                                    if ($redirect == '')
                                    {
                                            //if there is not a redirect link, send them to the my account page
                                            $redirect = 'secure/my_account';
                                    }
                                    //to login via ajax
                                    if($ajax)
                                    {
                                            die(json_encode(array('result'=>true)));
                                    }
                                    else
                                    {
                                            redirect($redirect);
                                    }

                                }
                                else
                                {
                                    //this adds the redirect back to flash data if they provide an incorrect credentials


                                    //to login via ajax
                                    if($ajax)
                                    {
                                            die(json_encode(array('result'=>false)));
                                    }
                                    else
                                    {
                                            $this->session->set_flashdata('redirect', $redirect);
                                            $this->session->set_flashdata('error', lang('login_failed'));

                                            redirect('secure/login');
                                    }
                                }
                            }
                            // Else Register for New User
                            else
                            {
                                $useremail = $userAttributes['contact/email'];
                                if(array_key_exists('namePerson',$userAttributes))
                                {
                                    $namePerson = explode(' ',$userAttributes['namePerson']);
                                    $firstname = $namePerson[0];
                                    $lastname = $namePerson[1];
                                }
                                
                                if(array_key_exists('namePerson/first',$userAttributes))
                                {
                                    $firstname = $userAttributes['namePerson/first'];
                                }
                                if(array_key_exists('namePerson/last',$userAttributes))
                                {
                                    $lastname = $userAttributes['namePerson/last'];
                                }

                                //redirect('secure/register_social/'.urlencode($useremail).'/'.urlencode($firstname).'/'.urlencode($lastname)); 
								//register_social($useremail,$firstname,$lastname);
                                $redirect = $this->session->flashdata('redirect');
                                $data['page_title']	= lang('account_registration');
                                $data['gift_cards_enabled'] = $this->gift_cards_enabled;
                                $data['redirect'] = $redirect;
                                $data['email']=$useremail;
                                $data['firstname']=$firstname;
                                $data['lastname']=$lastname;
                                    
                                $this->load->view('register_social',$data);

                            }        
                        }
                        else
                        {
                            // We should receive a valid Email from the Social otherwise we cant proceed. So redirecting the User to Login Page
                            redirect('secure/login');
                        }

                    }
                    else 
                    {
                        redirect('secure/login');
                    }
                }
            }
            
            

        }
	function login($ajax = false)
	{
		//find out if they're already logged in, if they are redirect them to the my account page
		$redirect	= $this->Customer_model->is_logged_in(false, false);
		//if they are logged in, we send them back to the my_account by default, if they are not logging in
		if ($redirect)
		{
			redirect('secure/my_account/');
		}
		
                $data['page_title']	= 'Login';
		$data['gift_cards_enabled'] = $this->gift_cards_enabled;
		
		$this->load->helper('form');
		$data['redirect']	= $this->session->flashdata('redirect');
		$submitted 		= $this->input->post('submitted');
		if ($submitted)
		{
			$email		= $this->input->post('email');
			$password	= $this->input->post('password');
			$remember   = $this->input->post('remember');
			$redirect	= $this->input->post('redirect');
			$login		= $this->Customer_model->login($email, $password, $remember);
			if ($login)
			{
				if ($redirect == '')
				{
					//if there is not a redirect link, send them to the my account page
					$redirect = 'secure/my_account';
				}
				//to login via ajax
				if($ajax)
				{
					die(json_encode(array('result'=>true)));
				}
				else
				{
					redirect($redirect);
				}
				
			}
			else
			{
				//this adds the redirect back to flash data if they provide an incorrect credentials
				
				
				//to login via ajax
				if($ajax)
				{
					die(json_encode(array('result'=>false)));
				}
				else
				{
					$this->session->set_flashdata('redirect', $redirect);
					$this->session->set_flashdata('error', lang('login_failed'));
					
					redirect('secure/login');
				}
			}
		}
		
		// load other page content 
		//$this->load->model('banner_model');
		$this->load->helper('directory');
	
		//if they want to limit to the top 5 banners and use the enable/disable on dates, add true to the get_banners function
		//$data['banners']	= $this->banner_model->get_banners();
		//$data['ads']		= $this->banner_model->get_banners(true);
		$data['categories']	= $this->Category_model->get_categories_tierd(0);
			
		$this->load->view('login', $data);
	}
	
	function logout()
	{
		$this->Customer_model->logout();
		redirect('secure/login');
	}
	
	function register_social()
	{
	
		$redirect	= $this->Customer_model->is_logged_in(false, false);
		//if they are logged in, we send them back to the my_account by default
		if ($redirect)
		{
			redirect('secure/my_account');
		}
		
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div>', '</div>');
		
		/*
		we're going to set this up early.
		we can set a redirect on this, if a customer is checking out, they need an account.
		this will allow them to register and then complete their checkout, or it will allow them
		to register at anytime and by default, redirect them to the homepage.
		*/
		$data['redirect']	= $this->session->flashdata('redirect');
		
		$data['page_title']	= lang('account_registration');
		$data['gift_cards_enabled'] = $this->gift_cards_enabled;
		
		//default values are empty if the customer is new

		$data['company']	= '';
		$data['firstname']	= '';
		$data['lastname']	= '';
		$data['email']		= '';
		$data['phone']		= '';
		$data['address1']	= '';
		$data['address2']	= '';
		$data['city']		= '';
		$data['state']		= '';
		$data['zip']		= '';

		$this->form_validation->set_rules('company', 'Company', 'trim|max_length[128]');
		$this->form_validation->set_rules('firstname', 'First Name', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('lastname', 'Last Name', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[128]|callback_check_email');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|required|max_length[32]');
		//$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|sha1');
		//$this->form_validation->set_rules('confirm', 'Confirm Password', 'required|matches[password]');
		$this->form_validation->set_rules('email_subscribe', 'Subscribe', 'trim|numeric|max_length[1]');
		
		if ($this->form_validation->run() == FALSE)
		{
			//if they have submitted the form already and it has returned with errors, reset the redirect
			if ($this->input->post('submitted'))
			{
				$data['redirect']	= $this->input->post('redirect');				
			}
			
			// load other page content 
			//$this->load->model('banner_model');
			$this->load->helper('directory');
		
			$data['categories']	= $this->Category_model->get_categories_tierd(0);
			
			$data['error'] = validation_errors();
			
			$this->load->view('register_social', $data);
		}
		else
		{
			
			
			$save['id']		= false;
			
			$save['firstname']			= $this->input->post('firstname');
			$save['lastname']			= $this->input->post('lastname');
			$save['email']				= $this->input->post('email');
			$save['phone']				= $this->input->post('phone');
			$save['company']			= $this->input->post('company');
			$save['active']				= $this->config->item('new_customer_status');
			$save['email_subscribe']	= intval((bool)$this->input->post('email_subscribe'));
			$socialpassword = uniqid('sp_');
			$save['password']			= sha1($socialpassword);
			
			$redirect					= $this->input->post('redirect');
			
			//if we don't have a value for redirect
			if ($redirect == '')
			{
				$redirect = 'secure/my_account';
			}
			
			// save the customer info and get their new id
			$id = $this->Customer_model->save($save);

			/* send an email */
			// get the email template
			$res = $this->db->where('id', '6')->get('canned_messages');
			$row = $res->row_array();
			
			// set replacement values for subject & body
			
			// {customer_name}
			$row['subject'] = str_replace('{customer_name}', $this->input->post('firstname').' '. $this->input->post('lastname'), $row['subject']);
			$row['content'] = str_replace('{customer_name}', $this->input->post('firstname').' '. $this->input->post('lastname'), $row['content']);
			
			// {url}
			$row['subject'] = str_replace('{url}', $this->config->item('base_url'), $row['subject']);
			$row['content'] = str_replace('{url}', $this->config->item('base_url'), $row['content']);
			
			// {site_name}
			$row['subject'] = str_replace('{site_name}', $this->config->item('company_name'), $row['subject']);
			$row['content'] = str_replace('{site_name}', $this->config->item('company_name'), $row['content']);
			
			$this->load->library('email');
			
			$config['mailtype'] = 'html';
			
			$this->email->initialize($config);
	
			$this->email->from($this->config->item('email'), $this->config->item('company_name'));
			$this->email->reply_to($this->config->item('reply_email'),$this->config->item('company_name'));
			$this->email->to($save['email']);
			$this->email->bcc($this->config->item('email'));
			$this->email->subject($row['subject']);
			$this->email->message(html_entity_decode($row['content']));
			
			$this->email->send();
			
			$this->session->set_flashdata('message', sprintf( lang('registration_thanks'), $this->input->post('firstname') ) );
			
			//lets automatically log them in
			$this->Customer_model->login($save['email'], $socialpassword);
			
			//we're just going to make this secure regardless, because we don't know if they are
			//wanting to redirect to an insecure location, if it needs to be secured then we can use the secure redirect in the controller
			//to redirect them, if there is no redirect, the it should redirect to the homepage.
			redirect($redirect);
		}
	}
	
	function register()
	{
	
		$redirect	= $this->Customer_model->is_logged_in(false, false);
		//if they are logged in, we send them back to the my_account by default
		if ($redirect)
		{
			redirect('secure/my_account');
		}
		
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div>', '</div>');
		
		/*
		we're going to set this up early.
		we can set a redirect on this, if a customer is checking out, they need an account.
		this will allow them to register and then complete their checkout, or it will allow them
		to register at anytime and by default, redirect them to the homepage.
		*/
		$data['redirect']	= $this->session->flashdata('redirect');
		
		$data['page_title']	= lang('account_registration');
		$data['gift_cards_enabled'] = $this->gift_cards_enabled;
		
		//default values are empty if the customer is new

		$data['company']	= '';
		$data['firstname']	= '';
		$data['lastname']	= '';
		$data['email']		= '';
		$data['phone']		= '';
		$data['address1']	= '';
		$data['address2']	= '';
		$data['city']		= '';
		$data['state']		= '';
		$data['zip']		= '';

		$this->form_validation->set_rules('company', 'Company', 'trim|max_length[128]');
		$this->form_validation->set_rules('firstname', 'First Name', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('lastname', 'Last Name', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[128]|callback_check_email');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|sha1');
		$this->form_validation->set_rules('confirm', 'Confirm Password', 'required|matches[password]');
		$this->form_validation->set_rules('email_subscribe', 'Subscribe', 'trim|numeric|max_length[1]');
		
		if ($this->form_validation->run() == FALSE)
		{
			//if they have submitted the form already and it has returned with errors, reset the redirect
			if ($this->input->post('submitted'))
			{
				$data['redirect']	= $this->input->post('redirect');				
			}
			
			// load other page content 
			//$this->load->model('banner_model');
			$this->load->helper('directory');
		
			$data['categories']	= $this->Category_model->get_categories_tierd(0);
			
			$data['error'] = validation_errors();
			
			$this->load->view('register', $data);
		}
		else
		{
			
			
			$save['id']		= false;
			
			$save['firstname']			= $this->input->post('firstname');
			$save['lastname']			= $this->input->post('lastname');
			$save['email']				= $this->input->post('email');
			$save['phone']				= $this->input->post('phone');
			$save['company']			= $this->input->post('company');
			$save['active']				= $this->config->item('new_customer_status');
			$save['email_subscribe']	= intval((bool)$this->input->post('email_subscribe'));
			
			$save['password']			= $this->input->post('password');
			
			$redirect					= $this->input->post('redirect');
			
			//if we don't have a value for redirect
			if ($redirect == '')
			{
				$redirect = 'secure/my_account';
			}
			
			// save the customer info and get their new id
			$id = $this->Customer_model->save($save);

			/* send an email */
			// get the email template
			$res = $this->db->where('id', '6')->get('canned_messages');
			$row = $res->row_array();
			
			// set replacement values for subject & body
			
			// {customer_name}
			$row['subject'] = str_replace('{customer_name}', $this->input->post('firstname').' '. $this->input->post('lastname'), $row['subject']);
			$row['content'] = str_replace('{customer_name}', $this->input->post('firstname').' '. $this->input->post('lastname'), $row['content']);
			
			// {url}
			$row['subject'] = str_replace('{url}', $this->config->item('base_url'), $row['subject']);
			$row['content'] = str_replace('{url}', $this->config->item('base_url'), $row['content']);
			
			// {site_name}
			$row['subject'] = str_replace('{site_name}', $this->config->item('company_name'), $row['subject']);
			$row['content'] = str_replace('{site_name}', $this->config->item('company_name'), $row['content']);
			
			$this->load->library('email');
			
			$config['mailtype'] = 'html';
			
			$this->email->initialize($config);
	
			$this->email->from($this->config->item('email'), $this->config->item('company_name'));
			$this->email->reply_to($this->config->item('reply_email'),$this->config->item('company_name'));
			$this->email->to($save['email']);
			$this->email->bcc($this->config->item('email'));
			$this->email->subject($row['subject']);
			$this->email->message(html_entity_decode($row['content']));
			
			$this->email->send();
			
			$this->session->set_flashdata('message', sprintf( lang('registration_thanks'), $this->input->post('firstname') ) );
			
			//lets automatically log them in
			$this->Customer_model->login($save['email'], $this->input->post('confirm'));

			// Update the activity log
			$this->load->model('activity_model');
			$this->activity_model->save_activity(2,"New Customer : ".$save['firstname']." ".$save['lastname']);
			
			//we're just going to make this secure regardless, because we don't know if they are
			//wanting to redirect to an insecure location, if it needs to be secured then we can use the secure redirect in the controller
			//to redirect them, if there is no redirect, the it should redirect to the homepage.
			redirect($redirect);
		}
	}

        function check_email($str)
	{
		if(!empty($this->customer['id']))
		{
			$email = $this->Customer_model->check_email($str, $this->customer['id']);
		}
		else
		{
			$email = $this->Customer_model->check_email($str);
		}
		
        if ($email)
       	{
			$this->form_validation->set_message('check_email', lang('error_email'));
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	function forgot_password()
	{
		$data['page_title']	= lang('forgot_password');
		$data['gift_cards_enabled'] = $this->gift_cards_enabled;
		$submitted = $this->input->post('submitted');
		if ($submitted)
		{
			$this->load->helper('string');
			$email = $this->input->post('email');
			
			$reset = $this->Customer_model->reset_password($email);
			
			if ($reset)
			{						
				$this->session->set_flashdata('message', lang('message_new_password'));
			}
			else
			{
				$this->session->set_flashdata('error', lang('error_no_account_record'));
			}
			redirect('secure/forgot_password');
		}
		
		// load other page content 
		//$this->load->model('banner_model');
		$this->load->helper('directory');
	
		//if they want to limit to the top 5 banners and use the enable/disable on dates, add true to the get_banners function
		//$data['banners']	= $this->banner_model->get_banners();
		//$data['ads']		= $this->banner_model->get_banners(true);
		$data['categories']	= $this->Category_model->get_categories_tierd();
		
		
		$this->load->view('forgot_password', $data);
	}
	
	function my_account($offset=0)
	{
		//make sure they're logged in
		$this->Customer_model->is_logged_in('secure/my_account/');
	
		$data['gift_cards_enabled']	= $this->gift_cards_enabled;
		
		$data['customer']			= (array)$this->Customer_model->get_customer($this->customer['id']);
			
		$data['addresses'] 			= $this->Customer_model->get_address_list($this->customer['id']);
		
		$data['page_title']			= 'Welcome '.$data['customer']['firstname'].' '.$data['customer']['lastname'];
		$data['customer_addresses']	= $this->Customer_model->get_address_list($data['customer']['id']);
		
		// load other page content 
		//$this->load->model('banner_model');
		$this->load->model('order_model');
		$this->load->helper('directory');
		$this->load->helper('date');
		
		//if they want to limit to the top 5 banners and use the enable/disable on dates, add true to the get_banners function
	//	$data['banners']	= $this->banner_model->get_banners();
	//	$data['ads']		= $this->banner_model->get_banners(true);
		$data['categories']	= $this->Category_model->get_categories_tierd(0);
		
		
		// paginate the orders
		$this->load->library('pagination');

		$config['base_url'] = site_url('secure/my_account');
		$config['total_rows'] = $this->order_model->count_customer_orders($this->customer['id']);
		$config['per_page'] = '15'; 
	
		
		$this->pagination->initialize($config); 
		
		$data['orders_pagination'] = $this->pagination->create_links();

		$data['orders']		= $this->order_model->get_customer_orders($this->customer['id'], $offset);

		
		//if they're logged in, then we have all their acct. info in the cookie.
		
		
		/*
		This is for the customers to be able to edit their account information
		*/

		$this->load->library('form_validation');	
		$this->form_validation->set_rules('company', 'Company', 'trim|max_length[128]');
		$this->form_validation->set_rules('firstname', 'First Name', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('lastname', 'Last Name', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[128]|callback_check_email');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('email_subscribe', 'Subscribe', 'trim|numeric|max_length[1]');


		if($this->input->post('password') != '' || $this->input->post('confirm') != '')
		{
			$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|sha1');
			$this->form_validation->set_rules('confirm', 'Confirm Password', 'required|matches[password]');
		}
		else
		{
			$this->form_validation->set_rules('password', 'Password');
			$this->form_validation->set_rules('confirm', 'Confirm Password');
		}


		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('my_account', $data);
		}
		else
		{
			$customer = array();
			$customer['id']						= $this->customer['id'];
			$customer['company']				= $this->input->post('company');
			$customer['firstname']				= $this->input->post('firstname');
			$customer['lastname']				= $this->input->post('lastname');
			$customer['email']					= $this->input->post('email');
			$customer['phone']					= $this->input->post('phone');
			$customer['email_subscribe']		= intval((bool)$this->input->post('email_subscribe'));
			if($this->input->post('password') != '')
			{
				$customer['password']			= $this->input->post('password');
			}
						
			$this->go_cart->save_customer($this->customer);
			$this->Customer_model->save($customer);
			
			$this->session->set_flashdata('message', lang('message_account_updated'));
			
			redirect('secure/my_account');
		}
	
	}
	
	
	function my_downloads($code=false)
	{
		
		if($code!==false)
		{
			$data['downloads'] = $this->Digital_Product_model->get_downloads_by_code($code);
		} else {
			$this->Customer_model->is_logged_in();
			
			$customer = $this->go_cart->customer();
			
			$data['downloads'] = $this->Digital_Product_model->get_user_downloads($customer['id']);
		}
		
		$data['gift_cards_enabled']	= $this->gift_cards_enabled;
		
		$data['page_title'] = lang('my_downloads');
		
		$this->load->view('my_downloads', $data);
	}
	
	
	function download($link)
	{
		$filedata = $this->Digital_Product_model->get_file_info_by_link($link);
		
		// missing file (bad link)
		if(!$filedata)
		{
			show_404();
		}
		
		// validate download counter
		if(intval($filedata->downloads) >= intval($filedata->max_downloads))
		{
			show_404();
		}
		
		// increment downloads counter
		$this->Digital_Product_model->touch_download($link);
		
		// Deliver file
		$this->load->helper('download');
		force_download('uploads/digital_uploads/', $filedata->filename);
	}
	
	
	function set_default_address()
	{
		$id = $this->input->post('id');
		$type = $this->input->post('type');
	
		$customer = $this->go_cart->customer();
		$save['id'] = $customer['id'];
		
		if($type=='bill')
		{
			$save['default_billing_address'] = $id;

			$customer['bill_address'] = $this->Customer_model->get_address($id);
			$customer['default_billing_address'] = $id;
		} else {

			$save['default_shipping_address'] = $id;

			$customer['ship_address'] = $this->Customer_model->get_address($id);
			$customer['default_shipping_address'] = $id;
		} 
		
		//update customer db record
		$this->Customer_model->save($save);
		
		//update customer session info
		$this->go_cart->save_customer($customer);
		
		echo "1";
	}
	
	
	// this is a form partial for the checkout page
	function checkout_address_manager()
	{
		$customer = $this->go_cart->customer();
		
		$data['customer_addresses'] = $this->Customer_model->get_address_list($customer['id']);
	
		$this->load->view('address_manager', $data);
	}
	
	// this is a partial partial, to refresh the address manager
	function address_manager_list_contents()
	{
		$customer = $this->go_cart->customer();
		
		$data['customer_addresses'] = $this->Customer_model->get_address_list($customer['id']);
	
		$this->load->view('address_manager_list_content', $data);
	}
	
	function address_form($id = 0)
	{
		
		$customer = $this->go_cart->customer();
		
		//grab the address if it's available
		$data['id']			= false;
		$data['company']	= $customer['company'];
		$data['firstname']	= $customer['firstname'];
		$data['lastname']	= $customer['lastname'];
		$data['email']		= $customer['email'];
		$data['phone']		= $customer['phone'];
		$data['address1']	= '';
		$data['address2']	= '';
		$data['city']		= '';
		$data['country_id'] = '';
		$data['zone_id']	= '';
		$data['zip']		= '';
		

		if($id != 0)
		{
			$a	= $this->Customer_model->get_address($id);
			if($a['customer_id'] == $this->customer['id'])
			{
				//notice that this is replacing all of the data array
				//if anything beyond this form data needs to be added to
				//the data array, do so after this portion of code
				$data		= $a['field_data'];
				$data['id']	= $id;
			} else {
				redirect('/'); // don't allow cross-customer editing
			}
			
			$data['zones_menu']	= $this->location_model->get_zones_menu($data['country_id']);
		}
		
		//get the countries list for the dropdown
		$data['countries_menu']	= $this->location_model->get_countries_menu();
		
		if($id==0)
		{
			//if there is no set ID, the get the zones of the first country in the countries menu
			$data['zones_menu']	= $this->location_model->get_zones_menu(array_shift(array_keys($data['countries_menu'])));
		} else {
			$data['zones_menu']	= $this->location_model->get_zones_menu($data['country_id']);
		}

		$this->load->library('form_validation');	
		$this->form_validation->set_rules('company', 'Company', 'trim|max_length[128]');
		$this->form_validation->set_rules('firstname', 'First Name', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('lastname', 'Last Name', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[128]');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('address1', 'Address', 'trim|required|max_length[128]');
		$this->form_validation->set_rules('address2', 'Address', 'trim|max_length[128]');
		$this->form_validation->set_rules('city', 'City', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('country_id', 'Country', 'trim|required|numeric');
		$this->form_validation->set_rules('zone_id', 'State', 'trim|required|numeric');
		$this->form_validation->set_rules('zip', 'Zip', 'trim|required|max_length[32]');
		
		
		if ($this->form_validation->run() == FALSE)
		{
			if(validation_errors() != '')
			{
				echo validation_errors();
			}
			else
			{
				$this->load->view('address_form', $data);
			}
		}
		else
		{
			$a = array();
			$a['id']						= ($id==0) ? '' : $id;
			$a['customer_id']				= $this->customer['id'];
			$a['field_data']['company']		= $this->input->post('company');
			$a['field_data']['firstname']	= $this->input->post('firstname');
			$a['field_data']['lastname']	= $this->input->post('lastname');
			$a['field_data']['email']		= $this->input->post('email');
			$a['field_data']['phone']		= $this->input->post('phone');
			$a['field_data']['address1']	= $this->input->post('address1');
			$a['field_data']['address2']	= $this->input->post('address2');
			$a['field_data']['city']		= $this->input->post('city');
			$a['field_data']['zip']			= $this->input->post('zip');
			
			// get zone / country data using the zone id submitted as state
			$country = $this->location_model->get_country(set_value('country_id'));	
			$zone    = $this->location_model->get_zone(set_value('zone_id'));		
			if(!empty($country))
			{
				$a['field_data']['zone']		= $zone->code;  // save the state for output formatted addresses
				$a['field_data']['country']		= $country->name; // some shipping libraries require country name
				$a['field_data']['country_code']   = $country->iso_code_2; // some shipping libraries require the code 
				$a['field_data']['country_id']  = $this->input->post('country_id');
				$a['field_data']['zone_id']		= $this->input->post('zone_id');  
			}
			
			$this->Customer_model->save_address($a);
			$this->session->set_flashdata('message', lang('message_address_saved'));
			echo 1;
		}
	}
        
	function delete_address()
	{
		$id = $this->input->post('id');
		// use the customer id with the addr id to prevent a random number from being sent in and deleting an address
		$customer = $this->go_cart->customer();
		$this->Customer_model->delete_address($id, $customer['id']);
		echo $id;
	}
}