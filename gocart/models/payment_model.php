<?php

Class Payment_model extends Cart_model
{
	
	function __construct()
	{
		Cart_model::__construct();		
	}
	
	//authorize.net
	function authorize()
	{	
	
		$cart	= $this->session->userdata('cart');
		
		
		$customer	= $cart['customer'];
		
		$post_url = "https://test.authorize.net/gateway/transact.dll";
		$post_values = array(
	
			// the API Login ID and Transaction Key must be replaced with valid values
			"x_login"		=> $this->config->item('x_login'),
			"x_tran_key"		=> $this->config->item('x_tran_key'),

			"x_version"		=> "3.1",
			"x_delim_data"		=> "TRUE",
			"x_delim_char"		=> "|",
			"x_relay_response"	=> "FALSE",

			"x_type"		=> "AUTH_CAPTURE",
			"x_method"		=> "CC",
			"x_card_num"		=> "4111111111111111",
			"x_exp_date"		=> "0115",

			"x_amount"		=> "19.99",
			"x_description"		=> "Sample Transaction",

			"x_first_name"		=> $customer['firstname'],
			"x_last_name"		=> $customer['lastname'],
			"x_address"		=> $customer['bill_address1'].' '.$customer['bill_address2'],
			"x_state"		=> $customer['bill_state'],
			"x_zip"			=> $customer['bill_zip']
			// Additional fields can be added here as outlined in the AIM integration
			// guide at: http://developer.authorize.net
		);
		$post_string = "";
		foreach( $post_values as $key => $value )
			{ $post_string .= "$key=" . urlencode( $value ) . "&"; }
		$post_string = rtrim( $post_string, "& " );
		
		
		$request = curl_init($post_url); // initiate curl object
		curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
		curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
		curl_setopt($request, CURLOPT_POSTFIELDS, $post_string); // use HTTP POST to send form data
		curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response.
		$post_response = curl_exec($request); // execute curl post and store results in $post_response
		// additional options may be required depending upon your server configuration
		// you can find documentation on curl options at http://www.php.net/curl_setopt
		curl_close ($request); // close curl object

		// This line takes the response and breaks it into an array using the specified delimiting character
		$response_array = explode($post_values["x_delim_char"],$post_response);
	}
}
?>
