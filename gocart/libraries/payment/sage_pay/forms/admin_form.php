<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php
    /**
     * GoCart Sage Pay Admin Form
     *
     * This class is part of the sage pay payment module.
     * 
     *
     * @package       GoCart Sage Pay payment module
     * @subpackage    
     * @category      Libraries
     * @author        swicks@devicesoftware.com
     * @version       0.1
     * 
     */


    //enabled
    $enabled_options = array(0 => 'Disabled', 1 => 'Enabled');

    //protocol (save hidden to keep in settings)
    echo form_hidden('vps_protocol', $settings['vps_protocol']);

    //mode
    $mode_options = array('simulator' => 'Simulator', 'test' => 'Test', 'live' => 'Live');
    //type
    $type_options = array('PAYMENT' => 'Payment', 'DEFFERRED' => 'Deferred', 'AUTHENTICATE' => 'Authenticate');
    //available card types
    $available_card_types = explode(',', SAGE_PAY_CARD_TYPES);
    for ($i=0; $i < count($available_card_types); $i+=2) 
       $acts[$available_card_types[$i]]=$available_card_types[$i+1]; 
    //currency
    $available_currency_types = explode(',', SAGE_PAY_CURRENCY);
    for ($i=0; $i < count($available_currency_types); $i+=2) 
       $currency_options[$available_currency_types[$i]]=$available_currency_types[$i+1]; 

?>
<table width="100%" border="0" cellpadding="5">
  <tr>
    <td width="21%"><div align="right">Enabled:</div></td>
    <td width="79%"><?php echo form_dropdown('enabled', $enabled_options, $settings['enabled']); ?></td>
  </tr>
  <tr>
    <td><div align="right">VPS Protocol:</div></td>
    <td><?php echo $settings['vps_protocol']; ?></td>
  </tr>
  <tr>
    <td><div align="right">Vendor Name:</div></td>
    <td><?php echo form_input('vendor', $settings['vendor']) ?></td>
  </tr>
  <tr>
    <td valign="top"><div align="right" style="padding-top: 4px;">Supported Card Types:</div></td>
    <td><table border="0">
    <?php
        foreach($acts as $key=>$value){
            echo '<tr><td>' . $value . '</td><td>' . form_checkbox('card_types[]', $key, isset($settings['card_types'][$key])) . '</td></tr>';
        }
    ?>    

    </table></td>
  </tr>
  
  <tr>
    <td><div align="right">System:</div></td>
    <td><? echo form_dropdown('mode', $mode_options, $settings['mode'] ); ?></td>
  </tr>
  <tr>
    <td><div align="right">Type:</div></td>
    <td><? echo form_dropdown('tx_type', $type_options, $settings['tx_type'] ); ?></td>
  </tr>  
  <tr>
    <td><div align="right">Currency</div></td>
    <td><? echo form_dropdown('currency', $currency_options, $settings['currency'] ); ?></td>
  </tr>
</table>
