<?php include('header.php');
$f_company	= array('name'=>'company','class'=>'span3', 'value'=> set_value('company',$company));
$f_address1	= array('name'=>'address1', 'class'=>'span6','value'=>set_value('address1',$address1));
$f_address2	= array('name'=>'address2', 'class'=>'span6','value'=> set_value('address2',$address2));
$f_first	= array('name'=>'firstname', 'class'=>'span3','value'=> set_value('firstname',$firstname));
$f_last		= array('name'=>'lastname', 'class'=>'span3','value'=> set_value('lastname',$lastname));
$f_email	= array('name'=>'email', 'class'=>'span3','value'=>set_value('email',$email));
$f_phone	= array('name'=>'phone', 'class'=>'span3','value'=> set_value('phone',$phone));
$f_city		= array('name'=>'city','class'=>'span2', 'value'=>set_value('city',$city));
$f_zip		= array('maxlength'=>'10', 'class'=>'span1', 'name'=>'zip', 'value'=> set_value('zip',$zip));
?>
<?php echo form_open($this->config->item('admin_folder').'/customers/address_form/'.$customer_id.'/'.$id);?>

	<div class="row">
		<div class="span3">
			<label><?php echo lang('company');?></label>
			<?php echo form_input($f_company);?>
		</div>
	</div>

	<div class="row">
		<div class="span3">
			<label><?php echo lang('firstname');?></label>
			<?php echo form_input($f_first);?>
		</div>
		<div class="span3">
			<label><?php echo lang('lastname');?></label>
			<?php echo form_input($f_last);?>
		</div>
	</div>

	<div class="row">
		<div class="span3">
			<label><?php echo lang('email');?></label>
			<?php echo form_input($f_email);?>
		</div>
		<div class="span3">
			<label><?php echo lang('phone');?></label>
			<?php echo form_input($f_phone);?>
		</div>
	</div>

	<div class="row">
		<div class="span6">
			<label><?php echo lang('country');?></label>
			<?php echo form_dropdown('country_id', $countries_menu, set_value('country_id', $country_id), 'id="f_country_id" class="span6"');?>
		</div>
	</div>

	<div class="row">
		<div class="span6">
			<label><?php echo lang('address');?></label>
			<?php echo form_input($f_address1);?>
		</div>
	</div>

	<div class="row">
		<div class="span6">
			<?php echo form_input($f_address2);?>
		</div>
	</div>

	<div class="row">
		<div class="span2">
			<label><?php echo lang('city');?></label>
			<?php echo form_input($f_city);?>
		</div>
		<div class="span3">
			<label><?php echo lang('state');?></label>
			<?php echo form_dropdown('zone_id', $zones_menu, set_value('zone_id', $zone_id), 'id="f_zone_id" class="span3"');?>
		</div>
		<div class="span1">
			<label><?php echo lang('postcode');?></label>
			<?php echo form_input($f_zip);?>
		</div>
	</div>

	<div class="form-actions">
		<input class="btn btn-primary" type="submit" value="<?php echo lang('save');?>"/>
	</div>
	<script type="text/javascript">
	$(document).ready(function(){
		$('.button').button();
	
		$('#f_country_id').change(function(){
			$.post('<?php echo site_url($this->config->item('admin_folder').'/locations/get_zone_menu');?>',{id:$('#f_country_id').val()}, function(data) {
			  $('#f_zone_id').html(data);
			});
	
		});
	});
	</script>
</form>
<?php include('footer.php');