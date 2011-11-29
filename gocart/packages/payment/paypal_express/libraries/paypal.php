<?php
/**
 * Class  PayPal
 *
 * @version 1.0
 * @author Martin Maly - http://www.php-suit.com
 * @copyright (C) 2008 martin maly
 * @see  http://www.php-suit.com/paypal
 * 2.10.2008 20:30:40
 
 ** Mofified for compatibility with GoCart, by Clear Sky Designs
 
 */
 
/*
* Copyright (c) 2008 Martin Maly - http://www.php-suit.com
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions are met:
*     * Redistributions of source code must retain the above copyright
*       notice, this list of conditions and the following disclaimer.
*     * Redistributions in binary form must reproduce the above copyright
*       notice, this list of conditions and the following disclaimer in the
*       documentation and/or other materials provided with the distribution.
*     * Neither the name of the <organization> nor the
*       names of its contributors may be used to endorse or promote products
*       derived from this software without specific prior written permission.
*
* THIS SOFTWARE IS PROVIDED BY MARTIN MALY ''AS IS'' AND ANY
* EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
* WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
* DISCLAIMED. IN NO EVENT SHALL MARTIN MALY BE LIABLE FOR ANY
* DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
* (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
* ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
* (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
* SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

class PayPal {

	private $API_USERNAME;
	private $API_PASSWORD;
	private $API_SIGNATURE;
	
	private $RETURN_URL;
	private $CANCEL_URL;

	public $endpoint;
	public $host;
	private $gate;
	private $currency;
	private $CI;

	function __construct() {
		
		$this->endpoint = '/nvp';
		$this->CI =& get_instance();
		
		// retrieve settings
		if ( $settings = $this->CI->Settings_model->get_settings('paypal_express') ) 
		{
			$this->API_USERNAME = $settings['username'];
			$this->API_PASSWORD = $settings['password'];
			$this->API_SIGNATURE = $settings['signature'];
			
			$this->RETURN_URL = $settings["return_url"];
			$this->CANCEL_URL = $settings["cancel_url"];
			
			$this->currency = $settings['currency'];
			
			// Test mode?
			if (!$settings['SANDBOX']) {
				$this->host = "api-3t.paypal.com";
				$this->gate = 'https://www.paypal.com/cgi-bin/webscr?';
			} else {
				//sandbox
				$this->host = "api-3t.sandbox.paypal.com";
				$this->gate = 'https://www.sandbox.paypal.com/cgi-bin/webscr?';
			}
		}
	}

	/**
	 * @return string URL of the "success" page
	 */
	private function getReturnTo() {
		//return sprintf("%s://%s/".$this->RETURN_URL,
		//$this->getScheme(), $_SERVER['SERVER_NAME']);
		return site_url($this->RETURN_URL);
	}

	/**
	 * @return string URL of the "cancel" page
	 */
	private function getReturnToCancel() {
		//return sprintf("%s://%s/".$this->CANCEL_URL,
		//$this->getScheme(), $_SERVER['SERVER_NAME']);
		return site_url($this->CANCEL_URL);
	}

	/**
	 * @return HTTPRequest
	 */
	private function response($data){
		//$r = new HTTPRequest($this->host, $this->endpoint, 'POST', true);
		//$result = $r->connect($data);
		$result = $this->CI->httprequest->connect($data);
		if ($result<400) return $this->CI->httprequest;
		return false;
	}

	private function buildQuery($data = array()){
		$data['USER'] = $this->API_USERNAME;
		$data['PWD'] = $this->API_PASSWORD;
		$data['SIGNATURE'] = $this->API_SIGNATURE;
		$data['VERSION'] = '56.0';
		$query = http_build_query($data);
		return $query;
	}

	/**
	 * Main payment function
	 * 
	 * If OK, the customer is redirected to PayPal gateway
	 * If error, the error info is returned
	 * 
	 * @param float $amount Amount (2 numbers after decimal point)
	 * @param string $desc Item description
	 * @param string $invoice Invoice number (can be omitted)
	 * @param string $currency 3-letter currency code (USD, GBP, CZK etc.)
	 * 
	 * @return array error info
	 */
	public function doExpressCheckout($amount, $desc, $invoice=''){
		$data = array(
		'PAYMENTACTION' =>'Sale',
		'AMT' => $amount,
		'RETURNURL' => $this->getReturnTo(),
		'CANCELURL'  => $this->getReturnToCancel(),
		'DESC'=> $desc,
		'NOSHIPPING'=> "1",
		'ALLOWNOTE'=> "1",
		'CURRENCYCODE'=> $this->currency,
		'METHOD' => 'SetExpressCheckout');

		$data['CUSTOM'] = $amount.'|'.$this->currency.'|'.$invoice;
		if ($invoice) $data['INVNUM'] = $invoice;

		$query = $this->buildQuery($data);

		$result = $this->response($query);

		if (!$result) return false;
		$response = $result->getContent();
		$return = $this->responseParse($response);

		if ($return['ACK'] == 'Success') {
			header('Location: '.$this->gate.'cmd=_express-checkout&useraction=commit&token='.$return['TOKEN'].'');
			die();
		}
		return($return);
	}

	public function getCheckoutDetails($token){
		$data = array(
		'TOKEN' => $token,
		'METHOD' =>'GetExpressCheckoutDetails');
		$query = $this->buildQuery($data);

		$result = $this->response($query);

		if (!$result) return false;
		$response = $result->getContent();
		$return = $this->responseParse($response);
		return($return);
	}
	
	
	public function doPayment()
	{
		$token = $_GET['token'];
		$payer = $_GET['PayerID'];
		
		$details = $this->getCheckoutDetails($token);
		if (!$details) return false;
		list($amount,$currency,$invoice) = explode('|',$details['CUSTOM']);
		$data = array(
		'PAYMENTACTION' => 'Sale',
		'PAYERID' => $payer,
		'TOKEN' =>$token,
		'AMT' => $amount,
		'CURRENCYCODE'=>$currency,
		'METHOD' =>'DoExpressCheckoutPayment');
		$query = $this->buildQuery($data);

		$result = $this->response($query);

		if (!$result) return false;
		$response = $result->getContent();
		$return = $this->responseParse($response);

		/*
		 * [AMT] => 10.00
		 * [CURRENCYCODE] => USD
		 * [PAYMENTSTATUS] => Completed
		 * [PENDINGREASON] => None
		 * [REASONCODE] => None
		 */

		return($return);
	}

	private function getScheme() {
		$scheme = 'http';
		if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {
			$scheme .= 's';
		}
		return $scheme;
	}

	private function responseParse($resp){
		$a=explode("&", $resp);
		$out = array();
		foreach ($a as $v){
			$k = strpos($v, '=');
			if ($k) {
				$key = trim(substr($v,0,$k));
				$value = trim(substr($v,$k+1));
				if (!$key) continue;
				$out[$key] = urldecode($value);
			} else {
				$out[] = $v;
			}
		}
		return $out;
	}
}