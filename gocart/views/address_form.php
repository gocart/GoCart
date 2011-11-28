<?php

$f_id		= array('id'=>'f_id', 'style'=>'display:none;', 'name'=>'id', 'value'=> set_value('id',$id));
$f_company	= array('id'=>'f_company', 'class'=>'input', 'name'=>'company', 'value'=> set_value('company',$company));
$f_address1	= array('id'=>'f_address1', 'class'=>'input', 'name'=>'address1', 'value'=>set_value('address1',$address1));
$f_address2	= array('id'=>'f_address2', 'class'=>'input', 'name'=>'address2', 'value'=> set_value('address2',$address2));
$f_first	= array('id'=>'f_firstname', 'class'=>'input', 'name'=>'firstname', 'value'=> set_value('firstname',$firstname));
$f_last		= array('id'=>'f_lastname', 'class'=>'input', 'name'=>'lastname', 'value'=> set_value('lastname',$lastname));
$f_email	= array('id'=>'f_email', 'class'=>'input', 'name'=>'email', 'value'=>set_value('email',$email));
$f_phone	= array('id'=>'f_phone', 'class'=>'input', 'name'=>'phone', 'value'=> set_value('phone',$phone));
$f_city		= array('id'=>'f_city', 'class'=>'input', 'name'=>'city', 'value'=>set_value('city',$city));
$f_zip		= array('id'=>'f_zip', 'maxlength'=>'10', 'class'=>'bill input', 'name'=>'zip', 'value'=> set_value('zip',$zip));

echo form_input($f_id);

?>
<div id="form_error" class="error" style="display:none;"></div>

	<div class="form_wrap">
		<div>
			<?php echo lang('address_company');?><br/>
			<?php echo form_input($f_company);?>
		</div>
		<div>
			<?php echo lang('address_firstname');?><b class="r"> *</b><br/>
			<?php echo form_input($f_first);?>
		</div>
		<div>
			<?php echo lang('address_lastname');?><b class="r"> *</b><br/>
			<?php echo form_input($f_last);?>
		</div>
	</div>

	<div class="form_wrap">
		<div>
			<?php echo lang('address_email');?><b class="r"> *</b><br/>
			<?php echo form_input($f_email);?>
		</div>
		<div>
			<?php echo lang('address_phone');?><b class="r"> *</b><br/>
			<?php echo form_input($f_phone);?>
		</div>
	</div>

	<div class="form_wrap">
		<div>
			<?php echo lang('address');?><b class="r"> *</b><br/>
			<?php echo form_input($f_address1).'<br/>'.form_input($f_address2);?>
		</div>
	</div>

	<div class="form_wrap">
		<div>
			<?php echo lang('address_city');?><b class="r"> *</b><br/>
			<?php echo form_input($f_city);?>
		</div>
		<div>
			<?php echo lang('address_postcode');?><b class="r"> *</b><br/>
			<?php echo form_input($f_zip);?>
		</div>
	</div>
		
	<div class="form_wrap">
		<div>
			<?php echo lang('address_country');?><br/>
			<?php echo form_dropdown('country_id', $countries_menu, set_value('country_id', $country_id), 'style="width:200px; display:block;" id="f_country_id" class="input"');?>
		</div>
		<div>
			<?php echo lang('address_state');?><br/>
			<?php echo form_dropdown('zone_id', $zones_menu, set_value('zone_id', $zone_id), 'style="width:200px; display:block;" id="f_zone_id" class="input"');?>
		</div>
	</div>
	<div class="clear"></div>
	<div class="center">
		<input type="button" value="Submit" onclick="save_address(); return false;"/>
	</div>
	
<script type="text/javascript">
$(function(){
	$('#f_country_id').change(function(){
			$.post('<?php echo site_url('locations/get_zone_menu');?>',{id:$('#f_country_id').val()}, function(data) {
			  $('#f_zone_id').html(data);
			});
		});
});

function save_address()
{
	$.post("<?php echo site_url('secure/address_form');?>/"+$('#f_id').val(), {	company: $('#f_company').val(),
																				firstname: $('#f_firstname').val(),
																				lastname: $('#f_lastname').val(),
																				email: $('#f_email').val(),
																				phone: $('#f_phone').val(),
																				address1: $('#f_address1').val(),
																				address2: $('#f_address2').val(),
																				city: $('#f_city').val(),
																				country_id: $('#f_country_id').val(),
																				zone_id: $('#f_zone_id').val(),
																				zip: $('#f_zip').val()
																				},
		function(data){
			if(data == 1)
			{
				window.location = "<?php echo site_url('secure/my_account');?>";
			}
			else
			{
				$('#form_error').show().html(data);
				//call resize twice to fix a wierd bug where the height is overcompensated
				$.fn.colorbox.resize();
			}
		});
}
</script>