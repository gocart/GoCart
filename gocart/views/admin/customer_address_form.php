<?php include('header.php');
$f_company	= array('id'=>'f_company', 'name'=>'company','class'=>'gc_tf1', 'value'=> set_value('company',$company));
$f_address1	= array('id'=>'f_address1', 'name'=>'address1', 'class'=>'gc_tf1 address','value'=>set_value('address1',$address1));
$f_address2	= array('id'=>'f_address2', 'name'=>'address2', 'class'=>'gc_tf1 address','value'=> set_value('address2',$address2));
$f_first	= array('id'=>'f_firstname', 'name'=>'firstname', 'class'=>'gc_tf1','value'=> set_value('firstname',$firstname));
$f_last		= array('id'=>'f_lastname', 'name'=>'lastname', 'class'=>'gc_tf1','value'=> set_value('lastname',$lastname));
$f_email	= array('id'=>'f_email', 'name'=>'email', 'class'=>'gc_tf1','value'=>set_value('email',$email));
$f_phone	= array('id'=>'f_phone', 'name'=>'phone', 'class'=>'gc_tf1','value'=> set_value('phone',$phone));
$f_city		= array('id'=>'f_city', 'name'=>'city','class'=>'gc_tf1', 'value'=>set_value('city',$city));
$f_zip		= array('id'=>'f_zip', 'maxlength'=>'10', 'class'=>'bill input','class'=>'gc_tf1', 'name'=>'zip', 'value'=> set_value('zip',$zip));
?>
<?php echo form_open('/'.$this->config->item('admin_folder').'/customers/address_form/'.$customer_id.'/'.$id);?>

<div class="button_set">
	<input type="submit" value="<?php echo lang('save');?>" />
</div>

<div id="gc_tabs">
	<ul>
		<li><a href="#gc_address"><?php echo lang('address_information');?></a></li>
	</ul>
<div id="gc_address" style="overflow:auto">
	<div class="field_wrap">
		<div>
			<?php echo lang('company');?><br/>
			<?php echo form_input($f_company);?>
		</div>
		<div>
			<?php echo lang('firstname');?><br/>
			<?php echo form_input($f_first);?>
		</div>
		<div>
			<?php echo lang('lastname');?><br/>
			<?php echo form_input($f_last);?>
		</div>
	</div>

	<div class="field_wrap">
		<div>
			<?php echo lang('email');?><br/>
			<?php echo form_input($f_email);?>
		</div>
		<div>
			<?php echo lang('phone');?><br/>
			<?php echo form_input($f_phone);?>
		</div>
	</div>

	<div class="field_wrap">
		<?php echo lang('address');?><br/>
		<?php echo form_input($f_address1).'<br/>'.form_input($f_address2);?>
	</div>

	<div class="field_wrap">
		<div>
			<?php echo lang('city');?><br/>
			<?php echo form_input($f_city);?>
		</div>
		<div>
			<?php echo lang('postcode');?><br/>
			<?php echo form_input($f_zip);?>
		</div>
	</div>

	<div class="field_wrap">
		<div>
			<?php echo lang('country');?><br/>
			<?php echo form_dropdown('country_id', $countries_menu, set_value('country_id', $country_id), 'style="width:200px; display:block;" id="f_country_id" class="input"');?>
		</div>
	</div>

	<div class="field_wrap">
		<div>
			<?php echo lang('state');?><br/>
			<?php echo form_dropdown('zone_id', $zones_menu, set_value('zone_id', $zone_id), 'style="width:200px; display:block;" id="f_zone_id" class="input"');?>
		</div>
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
</div>
</form>
<?php include('footer.php');