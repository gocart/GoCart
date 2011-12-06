<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

    /**
     * GoCart Sage Pay Customer payment block
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



?>

<script>
//validate our form
payment_method.sage_pay = function(){
    var errors = false;
        
    // validate fields
    $('.pmt_required').each(function(){
        if($(this).val().length==0)
        {
            $(this).addClass('require_fail');
            errors = true;
            display_error('payment', '<?php echo lang('required_fields') ?>');
        }
    });
    
    //errors is inverted here. if errors == true, then validation is false
    if(errors)
    {
        return false;
    }
    else
    {
        return true;
    }
}

</script>

<?php
    //add accepted cards to list
    $available_card_types = explode(',', SAGE_PAY_CARD_TYPES);
    for ($i=0; $i < count($available_card_types); $i+=2) 
        if(isset($settings['card_types'][$available_card_types[$i]]))
            $accepted_cards[$available_card_types[$i]]=$available_card_types[$i+1]; 
    
    // months        
    $months = array('01'=>'01','02'=>'02','03'=>'03','04'=>'04','05'=>'05','06'=>'06','07'=>'07','08'=>'08','09'=>'09','10'=>'10','11'=>'11','12'=>'12');            
    
    // valid from years
    $year_now = date('y');
    $valid_from_years = array();
    for($y = $year_now; $y > $year_now - 5; $y--)
        $valid_from_years[$y] = $y;
    // expires on years
    $expires_on_years = array();
    for($y = $year_now; $y < $year_now + 7; $y++)
        $expires_on_years[$y] = $y;

    
?>


	<div class="form_wrap">
		<div>
			<?php echo lang('full_name') ?><b class="r"> *</b><br/>
            <?php echo form_input(array('id' => 'CardHolder', 'name' => 'CardHolder', 'value' => @$sp_data["CardHolder"],  'class' => 'pmt_required textfield input', 'size' => '30')) ?>
		</div>
        <div>
        <?php echo lang('card_type') ?><b class="r"> *</b><br/>
            <?php echo form_dropdown('CardType', $accepted_cards, @$sp_data["CardType"],'class="input"') ?>
        </div>
        <div>
        <?php echo lang('card_number') ?><b class="r"> *</b><br/>
            <?php echo form_input(array('id' => 'CardNumber', 'name' => 'CardNumber', 'value' => @$sp_data["CardNumber"],  'class' => 'pmt_required textfield input', 'size' => '30')) ?>
        </div>
	</div>
	<div class="form_wrap">
        <div>
            <?php echo lang('valid_from') ?><br/>
            <?php echo form_dropdown('StartDate_mm', $months, @$sp_data["StartDate_mm"], 'class="input"').'/'.form_dropdown('StartDate_yy', $valid_from_years, @$sp_data["StartDate_yy"], 'class="input"');            
            ?>
        </div>
		<div>
			<?php echo lang('expires_on') ?><b class="r"> *</b><br/>
			<?php echo form_dropdown('ExpiryDate_mm', $months, @$sp_data["ExpiryDate_mm"], 'class="input"').'/'.form_dropdown('ExpiryDate_yy', $expires_on_years, @$sp_data["ExpiryDate_yy"], 'class="input"');			
			?>
		</div>
		<div>
			<?php echo lang('cvv_code') ?><b class="r"> *</b><br/>
            <?php echo form_input(array('id' => 'CV2', 'name' => 'CV2', 'max_length' => '3',  'value' => @$sp_data["CV2"],  'class' => 'pmt_required textfield input', 'size' => '5')) ?>
		</div>
	</div>
	<br style="clear:both;">