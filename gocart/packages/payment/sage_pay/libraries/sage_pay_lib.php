<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * GoCart Sage Pay Class
 *
 * This class is part of the sage pay payment module.
 * 
 *
 * @package       GoCart Sage Pay payment module
 * @subpackage    
 * @category      Packages/Payment
 * @author        swicks@devicesoftware.com
 * @version       0.2
 */


class Sage_pay_lib {
    
	private $CI;
    private $field_string;
    private $fields = array();    
    private $response_string;
    private $response = array();
    private $vendor_tx_code = '';
	private $settings;
    private $debuginfo;
    private $gateway_url = "";

    /**
    * constructor
    * 
    */
    function __construct() {
        $this->CI =& get_instance(); 
		
		// Retrieve gocart admin settings
		if( $this->settings = $this->CI->Settings_model->get_settings('sage_pay') )
		{      
		// If we have settings, the module is installed. If not, don't bother loading them
            switch($this->settings['mode']){
                case 'test':
                    $this->gateway_url = $this->settings['direct_test_url'];
                    break;
                case 'live':
                    $this->gateway_url = $this->settings['direct_live_url'];
                    break;                
                case 'simulator':
                default:
                    $this->gateway_url = $this->settings['direct_simulator_url'];
                    break;
            }
 
		}  
    }

    /**
    * Add field to instance array
    * 
    * @param string $field
    * @param string $value
    */
    function add_field($field, $value) {
      $this->fields[$field] = $value;   
    }

    
    /**
    * process say pay payments
    * 
    * @return string status
    */
    public function process_payment(){
        set_time_limit(60);

        // add some preset items
        $this->add_field('VPSProtocol', $this->settings['vps_protocol']);
        $this->add_field('Vendor', $this->settings['vendor']);
        
        // need to improve description - site description or part of cart
        $this->add_field('Description', $this->settings['vendor']);
        
        $this->add_field('Currency', $this->settings['currency']);
        $this->add_field('TxType', $this->settings['tx_type']);
        
        //generate a unique vendorTxCode
        $time_stamp = date("ymdHis");
        $rand_num = rand(0,32000)*rand(0,32000);
        $this->vendor_tx_code= $this->settings['vendor'] . "-" . $time_stamp . "-" . $rand_num;
        $this->add_field('VendorTxCode', $this->vendor_tx_code);

        foreach( $this->fields as $key => $value ) {
            $this->field_string .= "$key=" . urlencode( $value ) . "&";
        }
        $this->field_string = rtrim( $this->field_string, "& " );
        
        //set up URL & options
        $curl = curl_init($this->gateway_url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this->field_string);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT,30);        
        //remove these last 2 lines if using an earlier version of CURL
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);        
        
        // execute curl
        $this->response_string = curl_exec($curl);
        $this->response = array();
        // split response into lines
        $lines = preg_split( '/\r\n|\r|\n/', $this->response_string );
        foreach($lines as $line){            
            $key_value = preg_split( '/=/', $line );
            if(count($key_value) == 2)
                $this->response[trim($key_value[0])] = trim($key_value[1]);
        }
        
        // additional information returned for debugging
        $this->response['VendorTxCode'] = $this->vendor_tx_code;
        
        $this->response['Created'] = date('Y-m-d H:i:s');
        
        return $this->response['Status'];
    }
   
   /**
   * response status detail
   * 
   */
   function get_response_status_text() {
        return $this->response['StatusDetail'];
   }

   /**
   * returns array of all response results from sagepay
   * 
   */
   function get_all_responses() {
        return $this->response;
   }


   
   
}