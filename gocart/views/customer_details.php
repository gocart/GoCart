<?php include('header.php'); ?>
<script type="text/javascript">
$(document).ready(function() {
	
	// automatically copy the billing address as the shipping address, to meet cart internal needs
	$('.bill').change(function(){
		// if we are not going to use an alternate shipping address, copy the values
		if($('#ship_to_bill_address').is(':checked'))
		{
			copy_billing_address();
		}
	});
	
});

function toggle_shipping_address(checked)
{	
	if(!checked)
	{
		clear_shipping_address();
		$('#shipping_method_list').html('Enter your shipping address to see a list of available options and prices');
	}
	else
	{
		copy_billing_address();
	}
}

function clear_shipping_address()
{
	$('#ship_company').val('');
	$('#ship_firstname').val('');
	$('#ship_lastname').val('');
	$('#ship_email').val('');
	$('#ship_phone').val('');
	$('#ship_address1').val('');
	$('#ship_address2').val('');
	$('#ship_city').val('');
	$('#ship_state').val('');
	$('#ship_zip').val('');
}

function copy_billing_address()
{
	$('#ship_company').val($('#bill_company').val());
	$('#ship_firstname').val($('#bill_firstname').val());
	$('#ship_lastname').val($('#bill_lastname').val());
	$('#ship_email').val($('#bill_email').val());
	$('#ship_phone').val($('#bill_phone').val());
	$('#ship_address1').val($('#bill_address1').val());
	$('#ship_address2').val($('#bill_address2').val());
	$('#ship_city').val($('#bill_city').val());
	$('#ship_state').val($('#bill_state').val());
	$('#ship_zip').val($('#bill_zip').val());
}

function pick_address(context, cbox_title)
{
	addr_context = context;
	if(cbox_title===undefined) 
	{
		cbox_title = 'Choose an Address';
	}
	$.fn.colorbox({href: '<?php echo base_url() ?>secure/checkout_address_manager', 
				   width:"850", 
				   height:"450", 
				   title:cbox_title,
				   onComplete:function(){ $('#address_mgr_title').html(cbox_title); } // double display the title
				   });
}

function toggle_static_shipping_address(checked)
{	
	// update static form data
	if(!checked)
	{	
		// when they uncheck the option to use billing address, we will need them to choose their shipping address from the manager
		pick_address('ship', 'Choose Your Shipping Address');
		$('#checkout_shipping_address').show();
	}
	else
	{
		$('#checkout_shipping_address').hide();
		save_address_choice('ship', 'bill');
	}
}

function save_address_choice(context, address_id)
{
	$.post('<?php echo base_url() ?>secure/set_customer_address_choice', {context:context, address_id:address_id}, function(response)
	{
		if(response.company!==undefined) // test to see if we have field contents
		{	
			// close the manager
			$.fn.colorbox.close();
			
			
			// build a new address display string
			address_display = '';
			if(response.company!='')
			{
				address_display = address_display + response.company + '<br />';
			}
			address_display = address_display + response.firstname + ' ' + response.lastname;
			address_display = address_display + ' &lt;'+ response.email +'&gt;<br />';
			address_display = address_display + response.phone +'<br />';
			address_display = address_display + response.address1 + '<br />';
			if(response.address2!='')
			{
				address_display = address_display + response.address2 + '<br />';
			}
			address_display = address_display + response.city +', '+ response.state +' '+ response.zip;
			
			// update the address display box
			$('#'+context+'_address_display').html(address_display);
		}
	}, 'json');
}

</script>
<div>
<?php echo secure_form_open('checkout/customer_details'); ?>
	<input type="hidden" name="submitted" value="submitted" />
	
	<?php if(validation_errors()): ?>
	<div class="error"><?php echo validation_errors(); ?></div>
	<?php endif; ?>
	
	<?php include('customer_details_form.php');?>
	
	<?php if(!$this->Customer_model->is_logged_in(false, false)):?>
		<div style="margin-top: 35px"><input type="submit" value="Continue" class="gc_reg_button" /></div>
	<?php else:?>
		<div style="margin-top: 35px"><input type="button" onclick="window.location='<?php echo base_url() ?>checkout/continue_from_cust_info'" value="Continue" class="gc_reg_button" /></div>
	<?php endif; ?>
	</form>
</div>
<?php include('footer.php'); ?>
