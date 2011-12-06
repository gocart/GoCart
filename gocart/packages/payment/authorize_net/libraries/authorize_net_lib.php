<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Authorize_net_lib {
    
	var $CI;
    var $field_string;
    var $fields = array();    
    var $response_string;
    var $response = array();
	var $settings;
    var $debuginfo;
    var $gateway_url = "https://secure.authorize.net/gateway/transact.dll";

    function __construct() {
        $this->CI =& get_instance(); 
		
		// Retrieve gocart admin settings
		if( $this->settings = $this->CI->Settings_model->get_settings('Authorize_net') )
		{      
		// If we have settings, the module is installed. If not, don't bother loading them
			if($this->settings['authorize_net_test_mode'] == 'TRUE') {            
				$this->gateway_url = $this->settings['authorize_net_test_api_host'];
				$this->add_x_field('x_test_request', $this->settings['authorize_net_test_mode']);
				$this->add_x_field('x_login', $this->settings['authorize_net_test_x_login']);
				$this->add_x_field('x_tran_key', $this->settings['authorize_net_test_x_tran_key']);
			}else{
				$this->gateway_url = $this->settings['authorize_net_live_api_host'];
				$this->add_x_field('x_test_request', $this->settings['authorize_net_test_mode']);
				$this->add_x_field('x_login', $this->settings['authorize_net_live_x_login']);
				$this->add_x_field('x_tran_key', $this->settings['authorize_net_live_x_tran_key']);
			}
			$this->add_x_field('x_version', $this->settings['authorize_net_x_version']);
			$this->add_x_field('x_delim_data', $this->settings['authorize_net_x_delim_data']);
			$this->add_x_field('x_delim_char', $this->settings['authorize_net_x_delim_char']);  
			$this->add_x_field('x_encap_char', $this->settings['authorize_net_x_encap_char']);
			$this->add_x_field('x_url', $this->settings['authorize_net_x_url']);
			$this->add_x_field('x_type', $this->settings['authorize_net_x_type']);
			$this->add_x_field('x_method', $this->settings['authorize_net_x_method']);
			$this->add_x_field('x_relay_response', $this->settings['authorize_net_x_relay_response']);  
		}  
    }

    function add_x_field($field, $value) {
      $this->fields[$field] = $value;   
    }


   function process_payment() {
        foreach( $this->fields as $key => $value ) {
            $this->field_string .= "$key=" . urlencode( $value ) . "&";
        }
        $ch = curl_init($this->gateway_url);
        
        // turn off peer verification for test mode
        if($this->settings['authorize_net_test_mode'] == 'TRUE')
        {
        	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        }
        
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim( $this->field_string, "& " ));
        $this->response_string = urldecode(curl_exec($ch));
        
        if (curl_errno($ch)) {
            $this->response['Response_Reason_Text'] = curl_error($ch);
            return 3;
        }else{
            curl_close ($ch);
        }
        $temp_values = explode($this->settings['authorize_net_x_delim_char'], $this->response_string);
        $temp_keys= array (
            "Response_Code", "Response_Subcode", "Response_Reason_Code", "Response_Reason_Text",
            "Approval_Code", "AVS_Result_Code", "Transaction_ID", "Invoice_Number", "Description",
            "Amount", "Method", "Transaction_Type", "Customer_ID", "Cardholder_First_Name",
            "Cardholder Last_Name", "Company", "Billing_Address", "City", "State",
            "Zip", "Country", "Phone", "Fax", "Email", "Ship_to_First_Name", "Ship_to_Last_Name",
            "Ship_to_Company", "Ship_to_Address", "Ship_to_City", "Ship_to_State",
            "Ship_to_Zip", "Ship_to_Country", "Tax_Amount", "Duty_Amount", "Freight_Amount",
            "Tax_Exempt_Flag", "PO_Number", "MD5_Hash", "Card_Code_CVV_Response Code",
            "Cardholder_Authentication_Verification_Value_CAVV_Response_Code"
        );
        for ($i=0; $i<=27; $i++) {
            array_push($temp_keys, 'Reserved_Field '.$i);
        }
        $i=0;
        while (sizeof($temp_keys) < sizeof($temp_values)) {
            array_push($temp_keys, 'Merchant_Defined_Field '.$i);
            $i++;
        }
        for ($i=0; $i<sizeof($temp_values);$i++) {
            $this->response["$temp_keys[$i]"] = $temp_values[$i];
        }
        return $this->response['Response_Code'];
   }
   
   function get_response_reason_text() {
        return $this->response['Response_Reason_Text'];
   }

    function get_all_response_codes() {
        return $this->response;
    }


   function dump_fields() {                
        echo "<h3>authorizenet_class->dump_fields() Output:</h3>";
        echo "<table width=\"95%\" border=\"1\" cellpadding=\"2\" cellspacing=\"0\">
            <tr>
               <td bgcolor=\"black\"><b><font color=\"white\">Field Name</font></b></td>
               <td bgcolor=\"black\"><b><font color=\"white\">Value</font></b></td>
            </tr>";
            
        foreach ($this->fields as $key => $value) {
         echo "<tr><td>$key</td><td>".urldecode($value)."&nbsp;</td></tr>";
        }
        
        echo "</table><br>";
   }

   function dump_response() {             
      $i = 0;
      foreach ($this->response as $key => $value) {
         $this->debuginfo .= "$key: $value\n";
         $i++;
      }
      return $this->debuginfo;
   }
}