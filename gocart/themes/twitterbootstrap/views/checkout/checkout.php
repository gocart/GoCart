
<style type="text/css">
	.checkout-stage {
		padding:10px;
		background-color:#f2f2f2;
		color:#bbbbbb;
		border:1px solid #eee;
		text-shadow: 0px 1px 0px #ffffff;
		font-family:'Helvetica Neue',Helvetica, Arial, sans-serif;
		font-weight:300;
		-webkit-border-radius: 4px;
		-moz-border-radius: 4px;
		border-radius: 4px;
		font-size:25px;
	}
	.checkout-loading {
		text-align:center;
		margin:30px 0px;
	}
</style>
<div class="row">
	<div class="span12" style="margin-bottom:10px;">
		<div class="btn-group pull-right">
			<?php if(!$this->Customer_model->is_logged_in(false, false)) : ?>
				<input class="btn" type="button" onclick="window.location='<?php echo site_url('checkout/login');?>'" value="<?php echo lang('form_login');?>" />
				<input class="btn" type="button" onclick="window.location='<?php echo site_url('checkout/register');?>'" value="<?php echo lang('register_now');?>"/>
			<?php endif;?>
			<input class="btn" type="button" onclick="window.location='<?php echo site_url();?>'" value="<?php echo lang('continue_shopping');?>"/>
		</div>
	</div>
</div>

<div class="checkout-stage">
	<?php echo lang('customer_information');?>
</div>
<div id="customer-info-container">
	
</div>

<script type="text/javascript">
	$(document).ready(function(){
		get_customer_info_form();
	});
	
	function get_customer_info_form()
	{
		//place holder text
		$('#customer-info-container').addClass('checkout-loading').html('<img class="loading" src="<?php echo theme_img('ajax-loader.gif');?>"/> <?php echo lang('loading');?>');
		
		$.post('<?php echo site_url('checkout/customer_form'); ?>', function(data) {
			$('#customer-info-container').removeClass('checkout-loading').html(data);
		});
	}
</script>










<script type="text/javascript">

$(document).ready(function() {
	<?php if(isset($customer['ship_address'])):?>
		$.post('<?php echo site_url('checkout/customer_details');?>', function(data){
			//populate the form with their information
			$('#customer_info_fields').html(data);
		});
	<?php else:	?>
		get_customer_form();
	<?php endif;?>
});

function get_customer_form()
{
	//the loader will only show if someone is editing their existing information
	$('#save_customer_loader').show();
	//hide the button again
	$('#submit_button_container').hide();
	
	//remove the shipping and payment forms
	$('#shipping_payment_container').html('<div class="checkout_block"><img alt="loading" src="<?php echo theme_img('ajax-loader.gif');?>"/><br style="clear:both;"/></div>').hide();
	$.post('<?php echo site_url('checkout/customer_form'); ?>', function(data){
		//populate the form with their information
		$('#customer_info_fields').html(data);
		update_summary();
		
	});
}

// some behavior controlling global variables
var logged_in_user = <?php if($this->Customer_model->is_logged_in(false, false)) echo "true"; else echo "false"; ?>;

var shipping_required = <?php echo ($this->go_cart->requires_shipping()) ? 'true' : 'false'; ?>;
var shipping = Array();
var shipping_choice = '<?php $shipping=$this->go_cart->shipping_method(); if($shipping) echo $shipping['method']; ?>';

var addr_context = '';
var ship_to_bill_address = <?php if(isset($customer['ship_to_bill_address'])) { echo $customer['ship_to_bill_address']; } else { echo 'false'; } ?>;
var addresses;

// cart total is also set in the summary view
cart_total = <?php echo $this->go_cart->total(); ?>;

// payment method
var chosen_method = ''; // holds the current chosen method
var payment_method = {}; // list of payment method validators



function submit_order()
{
				
	// if we need to save a payment method
	if(cart_total>0) {
	
		frm_data = $('#pmnt_form_'+chosen_method).serialize();
		
		$.post('<?php echo site_url('checkout/save_payment_method'); ?>', frm_data, function(response)
		{
			if(typeof response != "object")
			{
				display_error('payment', '<?php echo lang('error_save_payment');  ?>');

				return;
			}
			
			if(response.status=='success')
			{
				// send them on to place the order
				$('#order_submit_form').trigger('submit');
			}
			else if(response.status=='error')
			{
				display_error('payment', response.error);
			}
			
		}, 'json');
	} else {
		$('#order_submit_form').trigger('submit');	
	}
}

function display_error(panel, message) 
{
	$('#'+panel+'_error_box').html(message).show();
}

function clear_errors()
{
	$('.error').hide();
	
	$('.required').each(function(){ 
			$(this).removeClass('require_fail');
	});
	
	$('.pmt_required').each(function(){ 
			$(this).removeClass('require_fail');
	});
}


// shipping cost visual calculator
function set_shipping_cost()
{
	clear_errors();
	
	$.post('<?php echo site_url('checkout/save_shipping_method');?>', {shipping:$(':radio[name$="shipping_input"]:checked').val()}, function(response)
	{
		update_summary();
	});
}

function set_chosen_payment_method(value)
{
	chosen_method = value;
}

// Set payment info
function submit_payment_method()
{
	$('#submit-form-loader').show();
	clear_errors();
	
	errors = false;
		
	// verify a shipping method is chosen
	if(shipping_required && $('input:radio[name=shipping_input]:checked').val()===undefined && $('input:radio[name=shipping_input]').length > 0)
	{
		display_error('shipping', '<?php echo lang('error_choose_shipping');?>');
		errors = true;
	}
		
	// validate payment method if payment is required
	if(cart_total>0)
	{
		// verify a payment option is chosen
		if($('input[name=payment_method]').length > 1)
		{
			if($('input:radio[name=payment_method]:checked').val()===undefined)
			{
				display_error('payment', '<?php echo lang('error_choose_payment');?>');
				errors = true;
			}
		}
		
		// determine if our payment method has a built-in validator
		if(typeof payment_method[chosen_method] == 'function' )
		{
			if(!payment_method[chosen_method]())
			{
				errors = true;
			}
		}
	}
	
	// stop here if we have problems
	if(errors)
	{
		$('#submit-form-loader').hide();
		return false;
	}

	// send the customer data again and then submit the order
	save_order();
}

function save_order()
{
	//submit additional order details
	$.post('<?php echo site_url('checkout/save_additional_details');?>', $('#additional_details_form').serialize(), function(){

		//thus must be a callback, otherwise there is a risk of the form submitting without the additional details saved
		// if we need to save a payment method
		if(cart_total>0) {

			frm_data = $('#pmnt_form_'+chosen_method).serialize();

			$.post('<?php echo site_url('checkout/save_payment_method');?>', frm_data, function(response)
			{
				if(typeof response != "object")
				{
					display_error('payment', '<?php echo lang('error_save_payment') ?>');
					$('#submit-form-loader').hide();
					return;
				}

				if(response.status=='success')
				{
					// send them on to place the order
					$('#order_submit_form').trigger('submit');
				}
				else if(response.status=='error')
				{
					display_error('payment', response.error);
					$('#submit-form-loader').hide();
					return;
				}

			}, 'json');
		} else {
			$('#order_submit_form').trigger('submit');	
		}
	});
}	 			

// refresh the summary so that tax rows will be incorporated into the display
// (they'll be missing before the customer enters their address and change if they change it)
function update_summary()
{
	// refresh confirmation content
	$.post('<?php echo site_url('checkout/order_summary');?>', {}, function(response)
	{
		$('#summary_section').html(response);	
	});
}


</script>

<div class="row">
	<div class="span12" style="margin-bottom:10px;">
		<div class="btn-group pull-right">
			<?php if(!$this->Customer_model->is_logged_in(false, false)) : ?>
				<input class="btn" type="button" onclick="window.location='<?php echo site_url('checkout/login');?>'" value="<?php echo lang('form_login');?>" />
				<input class="btn" type="button" onclick="window.location='<?php echo site_url('checkout/register');?>'" value="<?php echo lang('register_now');?>"/>
			<?php endif;?>
			<input class="btn" type="button" onclick="window.location='<?php echo base_url();?>'" value="<?php echo lang('continue_shopping');?>"/>
		</div>
	</div>
</div>

<div class="row" style="margin-bottom:10px;">
	<div class="span12" id="customer_info_fields" style="border-bottom:4px solid #ddd; padding-bottom:15px;">
		<img alt="loading" src="<?php echo theme_img('ajax-loader.gif');?>"/>
	</div>
</div>

<div id="shipping_payment_container" style="display:none;">
	<img alt="loading" src="<?php echo theme_img('ajax-loader.gif');?>"/>
</div>

<div id="summary_section">
<?php  include('summary.php'); ?>
</div>

<div id="submit_button_container" style="display:none; text-align:center; padding-top:10px; clear:both;">
	<form id="order_submit_form" action="<?php echo site_url('checkout/place_order'); ?>" method="post">
		<input type="hidden" name="process_order" value="true">

		<div style="text-align:center; margin:10px; display:none;" id="submit-form-loader">
			<img alt="loading" src="<?php echo theme_img('ajax-loader.gif');?>"/>
		</div>
		<input style="padding:10px 15px; font-size:16px;" type="button" class="btn btn-primary btn-large" onclick="submit_payment_method()" value="<?php echo lang('submit_order');?>" />
	</form>
</div>