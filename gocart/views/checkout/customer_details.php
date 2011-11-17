<script type="text/javascript">
$(document).ready(function() {	
	//disable the form elements under billing if the checkbox is checked
	if($('#different_address').is(':checked'))
	{
		toggle_billing_address_form(true);
	}
	
	//add the disabled class to to disabled fields
	$('*:disabled').addClass('disabled');

	// automatically copy values when the checkbox is checked
	$('.ship').change(function(){
		if($('#different_address').is(':checked'))
		{
			copy_shipping_address();
		}
	});	
	
	// populate zone menu with country selection
	$('#ship_country_id').change(function(){
			populate_zone_menu('ship');
		});

	$('#bill_country_id').change(function(){
			populate_zone_menu('bill');
		});	

});
// context is ship or bill
function populate_zone_menu(context, value)
{
	$.post('<?php echo site_url('locations/get_zone_menu');?>',{id:$('#'+context+'_country_id').val()}, function(data) {
		$('#'+context+'_zone_id').html(data);
		
		//if the ship country is changed, and copy shipping address is checked, then reset the billing address to blank
		if(context == 'ship' && $('#different_address').is(':checked'))
		{
			$('#bill_zone_id').html(data).val($('#bill_zone_id option:first').val());
		}
	});
}
function toggle_billing_address_form(checked)
{
	if(!checked)
	{
		clear_billing_address();
		$('.bill').attr('disabled', false);
		$('.bill').removeClass('disabled');
	}
	else
	{
		copy_shipping_address();
		$('.bill').attr('disabled', true);
		$('.bill').addClass('disabled');
	}
}

function clear_billing_address()
{
	$('.bill').val('');
}

function copy_shipping_address()
{
	$('#bill_company').val($('#ship_company').val());
	$('#bill_firstname').val($('#ship_firstname').val());
	$('#bill_lastname').val($('#ship_lastname').val());
	$('#bill_address1').val($('#ship_address1').val());
	$('#bill_address2').val($('#ship_address2').val());
	$('#bill_city').val($('#ship_city').val());
	$('#bill_zip').val($('#ship_zip').val());
	$('#bill_phone').val($('#ship_phone').val());
	$('#bill_email').val($('#ship_email').val());
	$('#bill_country_id').val($('#ship_country_id').val());

	// repopulate and set zone field
	$('#bill_zone_id').html($('#ship_zone_id').html());
	$('#bill_zone_id').val($('#ship_zone_id').val());
}

function save_customer()
{
	$('#save_customer_loader').show();
	// temporarily enable the billing fields (if disabled)
	if($('#different_address').is(':checked'))
	{
		$('.bill').attr('disabled', false);
		$('.bill').removeClass('disabled');
	}
	//send data to server
	form_data  = $('#customer_info_form').serialize();
	
	$.post('<?php echo site_url('checkout/save_customer') ?>', form_data, function(response)
	{
		if(typeof response != "object") // error
		{
			display_error('customer', '<?php echo lang('communication_error');?>');
			return;
		}
		
		if(response.status=='success')
		{
			//populate the information from ajax, so someone cannot use developer tools to edit the form after it's saved
			$('#customer_info_fields').html(response.view);
			
			$('input:button, button').button();			
			 // and update the summary to show proper tax information / discounts
			 update_summary();
		}
		else if(response.status=='error')
		{
			display_error('customer', response.error);
			$('#save_customer_loader').hide();
		}
	}, 'json');
}
</script>
<?php /* Only show this javascript if the user is logged in */ ?>
<?php if($this->Customer_model->is_logged_in(false, false)) : ?>
<script type="text/javascript">
	
	var address_type = 'ship';
	$(document).ready(function(){
		$('.address_picker').click(function(){
			$.colorbox({href:'#address_manager', inline:true, height:'400px'});
			address_type = $(this).attr('rel');
		});
	});

	<?php
	$add_list = array();
	foreach($customer_addresses as $row) {
		// build a new array
		$add_list[$row['id']] = $row['field_data'];
	}
	$add_list = json_encode($add_list);
	echo "eval(addresses=$add_list);";
	?>
		
	function populate_address(address_id)
	{
		if(address_id=='') return;

		// update the visuals

		// - this is redundant, but it updates the visuals before the operation begins
		if(shipping_required && address_type=='ship')
		{
			$('#shipping_loading').show();
			$('#shipping_method_list').hide();
		}

		// - populate the fields
		$.each(addresses[address_id], function(key, value){
			$('#'+address_type+'_'+key).val(value);

			// repopulate the zone menu and set the right value if we change the country
			if(key=='zone_id')
			{
				zone_id = value;
			}
		});	
		
		// - save the address id
		$('#'+address_type+'_address_id').val(address_id);

		// repopulate the zone list, set the right value, then copy all to billing
		$.post('<?php echo site_url('locations/get_zone_menu');?>',{id:$('#'+address_type+'_country_id').val()}, function(data) {
			// - uncheck the option box if they choose a billing address
			if(address_type=='bill')
			{
				$('#different_address').attr('checked', false);
				$('.bill').attr('disabled', false);
				$('.bill').removeClass('disabled');
				$('#bill_zone_id').html(data);
				$('#bill_zone_id').val(zone_id);
			} 
			else 
			{
				// set the right zone values
				$('#ship_zone_id').html(data);
				$('#ship_zone_id').val(zone_id);

				if($('#different_address').is(':checked'))
				{
					// copy the rest of the fields
					copy_shipping_address();
				}	
			}
		});		
	}
	
</script>
<?php endif;?>

<?php
$countries = $this->Location_model->get_countries_menu();

if(!empty($customer['ship_address']['country_id']))
{
	$ship_zone_menu	= $this->Location_model->get_zones_menu($customer['ship_address']['country_id']);
}
else
{
	// if this is set, it means we've got a blank address. Set the state field to an empty initial value
	$ship_zone_menu = array(''=>'')+$this->Location_model->get_zones_menu(array_shift(array_keys($countries)));
}

if(!empty($customer['bill_address']['country_id']))
{
	$bill_zone_menu	= $this->Location_model->get_zones_menu($customer['bill_address']['country_id']);
}
else
{
	$bill_zone_menu = array(''=>'')+$this->Location_model->get_zones_menu(array_shift(array_keys($countries)));
}

//form elements

$b_company	= array('id'=>'bill_company', 'class'=>'bill input', 'name'=>'bill_company', 'value'=> @$customer['bill_address']['company']);
$b_address1	= array('id'=>'bill_address1', 'class'=>'bill input bill_req', 'name'=>'bill_address1', 'value'=>@$customer['bill_address']['address1']);
$b_address2	= array('id'=>'bill_address2', 'class'=>'bill input', 'name'=>'bill_address2', 'value'=> @$customer['bill_address']['address2']);
$b_first	= array('id'=>'bill_firstname', 'class'=>'bill input bill_req', 'name'=>'bill_firstname', 'value'=> @$customer['bill_address']['firstname']);
$b_last		= array('id'=>'bill_lastname', 'class'=>'bill input bill_req', 'name'=>'bill_lastname', 'value'=> @$customer['bill_address']['lastname']);
$b_email	= array('id'=>'bill_email', 'class'=>'bill input bill_req', 'name'=>'bill_email', 'value'=>@$customer['bill_address']['email']);
$b_phone	= array('id'=>'bill_phone', 'class'=>'bill input bill_req', 'name'=>'bill_phone', 'value'=> @$customer['bill_address']['phone']);
$b_city		= array('id'=>'bill_city', 'class'=>'bill input bill_req', 'name'=>'bill_city', 'value'=>@$customer['bill_address']['city']);
$b_zip		= array('id'=>'bill_zip', 'maxlength'=>'10', 'class'=>'bill input bill_req', 'name'=>'bill_zip', 'value'=> @$customer['bill_address']['zip']);


$s_company	= array('id'=>'ship_company', 'class'=>'ship input', 'name'=>'ship_company', 'value'=> @$customer['ship_address']['company']);
$s_address1	= array('id'=>'ship_address1', 'class'=>'ship input ship_req', 'name'=>'ship_address1', 'value'=>@$customer['ship_address']['address1']);
$s_address2	= array('id'=>'ship_address2', 'class'=>'ship input', 'name'=>'ship_address2', 'value'=> @$customer['ship_address']['address2']);
$s_first	= array('id'=>'ship_firstname', 'class'=>'ship input ship_req', 'name'=>'ship_firstname', 'value'=> @$customer['ship_address']['firstname']);
$s_last		= array('id'=>'ship_lastname', 'class'=>'ship input ship_req', 'name'=>'ship_lastname', 'value'=> @$customer['ship_address']['lastname']);
$s_email	= array('id'=>'ship_email', 'class'=>'ship input ship_req', 'name'=>'ship_email', 'value'=>@$customer['ship_address']['email']);
$s_phone	= array('id'=>'ship_phone', 'class'=>'ship input ship_req', 'name'=>'ship_phone', 'value'=> @$customer['ship_address']['phone']);
$s_city		= array('id'=>'ship_city', 'class'=>'ship input ship_req', 'name'=>'ship_city', 'value'=>@$customer['ship_address']['city']);
$s_zip		= array('id'=>'ship_zip', 'maxlength'=>'10', 'class'=>'ship input ship_req', 'name'=>'ship_zip', 'value'=> @$customer['ship_address']['zip']);

?>

	<div id="customer_error_box" class="error" style="display:none"></div>
	<form id="customer_info_form">
		<h3><?php echo lang('customer_information');?></h3>
		<div id="shipping_address">
			<div class="form_wrap">
				<div style="font-weight:bold"><?php echo lang('shipping_address');?></div>
			</div>
			<div class="form_wrap">
				<?php if($this->Customer_model->is_logged_in(false, false)) : ?>
				<div>
					<input class="address_picker" type="button" value="<?php echo lang('choose_address');?>" rel="ship" />
				</div>
				<?php endif; ?>
			</div>
			
			<div class="form_wrap">
				<input type="hidden" name="ship_address_id" id="ship_address_id" />
				<div class="shorter">
					<?php echo lang('address_company');?><br/>
					<?php echo form_input($s_company);?>
				</div>
				<div class="shorter">
					<?php echo lang('address_firstname');?><b class="r"> *</b><br/>
					<?php echo form_input($s_first);?>
				</div>
				<div class="shorter">
					<?php echo lang('address_lastname');?><b class="r"> *</b><br/>
					<?php echo form_input($s_last);?>
				</div>
			</div>
			
			<div class="form_wrap">
				<div class="shorter">
					<?php echo lang('address_email');?><b class="r"> *</b><br/>
					<?php echo form_input($s_email);?>
				</div>
				<div class="shorter">
					<?php echo lang('address_phone');?><b class="r"> *</b><br/>
					<?php echo form_input($s_phone);?>
				</div>
			</div>
			
			<div class="form_wrap">
				<div class="longer">
					<?php echo lang('address');?><b class="r"> *</b><br/>
					<?php echo form_input($s_address1).'<br/>'.form_input($s_address2);?>
				</div>
			</div>
			
			<div class="form_wrap">
				<div>
					<?php echo lang('address_city');?><b class="r"> *</b><br/>
					<?php echo form_input($s_city);?>
				</div>
				<div>
					<?php echo lang('address_postcode');?><b class="r"> *</b><br/>
					<?php echo form_input($s_zip);?>
				</div>
			</div>
			
			<div class="form_wrap">
				<div>
					<?php echo lang('address_country');?><b class="r"> *</b><br/>
					<?php echo form_dropdown('ship_country_id',$countries, @$customer['ship_address']['country_id'], 'id="ship_country_id" class="ship input ship_req"');?>
				</div>
				<div>
					<?php echo lang('address_state');?><b class="r"> *</b><br/>
					<?php echo form_dropdown('ship_zone_id',$ship_zone_menu, @$customer['ship_address']['zone_id'], 'id="ship_zone_id" class="ship input ship_req"');?>
				</div>
			</div>
			<div class="clear"></div>
			<div class="form_wrap">
				<input type="checkbox" id="different_address" name="ship_to_bill_address" value="yes" <?php echo set_checkbox('ship_to_bill_address', 'yes', @$customer['ship_to_bill_address']);?> onclick="toggle_billing_address_form(this.checked)"> <?php echo lang('use_address_for_billing');?>
			</div>
		</div>
		
		<div id="billing_address">
			<div class="form_wrap">
				<div style="font-weight:bold"><?php echo lang('billing_address')?></div>
			</div>
			<div class="form_wrap">
				<?php if($this->Customer_model->is_logged_in(false, false)) : ?>
				<div>
					<input class="address_picker" type="button" value="<?php echo lang('choose_address');?>" rel="bill" />
				</div>
				<?php endif; ?>
			</div>
			<div class="form_wrap">
				<input type="hidden" name="bill_address_id" id="bill_address_id" />
				<div class="shorter">
					<?php echo lang('address_company');?><br/>
					<?php echo form_input($b_company);?>
				</div>
				<div class="shorter">
					<?php echo lang('address_firstname');?><b class="r"> *</b><br/>
					<?php echo form_input($b_first);?>
				</div>
				<div class="shorter">
					<?php echo lang('address_lastname');?><b class="r"> *</b><br/>
					<?php echo form_input($b_last);?>
				</div>
			</div>
				
			<div class="form_wrap">
				<div class="shorter">
					<?php echo lang('address_email');?><b class="r"> *</b><br/>
					<?php echo form_input($b_email);?>
				</div>
				<div class="shorter">
					<?php echo lang('address_phone');?><b class="r"> *</b><br/>
					<?php echo form_input($b_phone);?>
				</div>
			</div>
			
			<div class="form_wrap">
				<div class="longer">
					<?php echo lang('address');?><b class="r"> *</b><br/>
					<?php echo form_input($b_address1).'<br/>'.form_input($b_address2);?>
				</div>
			</div>
				
			<div class="form_wrap">
				<div>
					<?php echo lang('address_city');?><b class="r"> *</b><br/>
					<?php echo form_input($b_city);?>
				</div>
	
				<div>
					<?php echo lang('address_postcode');?><b class="r"> *</b><br/>
					<?php echo form_input($b_zip);?>
				</div>
			</div>
			<div class="form_wrap">
				<div>
					<?php echo lang('address_country');?><b class="r"> *</b><br/>
					<?php echo form_dropdown('bill_country_id',$countries, @$customer['bill_address']['country_id'], 'id="bill_country_id" class="bill input bill_req"');?>
				</div>
				<div>
					<?php echo lang('address_state');?><b class="r"> *</b><br/>
					<?php echo form_dropdown('bill_zone_id',$bill_zone_menu, @$customer['bill_address']['zone_id'], 'id="bill_zone_id" class="bill input bill_req"');?>
				</div>
			</div>
			<div class="clear"></div>
		</div>
		
	</form>
	
	<br style="clear:both;"/>
	<table style="margin-top:10px;">
		<tr>
			<td><input type="button" value="<?php echo lang('form_continue');?>" onclick="save_customer()"/></td>
			<td><img id="save_customer_loader" alt="loading" src="<?php echo base_url('images/ajax-loader.gif');?>" style="display:none;"/></td>
		</tr>
	</table>
	


<?php if($this->Customer_model->is_logged_in(false, false)) : ?>
<div id="stored_addresses" style="display:none;">
	<div id="address_manager">
		<h3 style="text-align:center;"><?php echo lang('your_addresses');?></h3>
		<script type="text/javascript">
		$(document).ready(function(){
			$('#address_list .my_account_address:even').addClass('address_bg');
		});
		</script>
		<div id="address_list">
			
		<?php
		$c = 1;
		foreach($customer_addresses as $a):?>
			<div class="my_account_address" id="address_<?php echo $a['id'];?>">
				<div class="address_toolbar">
					<input type="button" class="choose_address" onclick="populate_address(<?php echo $a['id'];?>); $.colorbox.close()" value="<?php echo lang('form_choose');?>" />
				</div>
				<?php
				$b	= $a['field_data'];
				echo nl2br(format_address($b));
				?>
			</div>
		<?php endforeach;?>
		</div>
	</div>
</div>
<?php endif;?>