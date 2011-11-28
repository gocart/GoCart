<?php include('header.php');?>

<?php
if(validation_errors())
{
	echo '<div class="error">'.validation_errors().'</div>';
}
?>

<script>
$(document).ready(function(){
	$('.delete_address').click(function(){
		if($('.delete_address').length > 1)
		{
			if(confirm('<?php echo lang('delete_address_confirmation');?>'))
			{
				$.post("<?php echo site_url('secure/delete_address');?>", { id: $(this).attr('rel') },
					function(data){
						$('#address_'+data).remove();
						$('#address_list .my_account_address').removeClass('address_bg');
						$('#address_list .my_account_address:even').addClass('address_bg');
					});
			}
		}
		else
		{
			alert('<?php echo lang('error_must_have_address');?>');
		}	
	});
	
	$('.edit_address').click(function(){
		$.fn.colorbox({	href: '<?php echo site_url('secure/address_form'); ?>/'+$(this).attr('rel'), width:'600px', height:'500px'}, function(){
			$('input:submit, input:button, button').button();
		});
	});
	
	if ($.browser.webkit) {
	    $('input:password').attr('autocomplete', 'off');
	}
});


function set_default(address_id, type)
{
	$.post('<?php echo site_url('secure/set_default_address') ?>/',{id:address_id, type:type});
}


</script>

<div id="my_account_container">

<?php
$company	= array('id'=>'company', 'class'=>'input', 'name'=>'company', 'value'=> set_value('company', $customer['company']));
$first		= array('id'=>'firstname', 'class'=>'input', 'name'=>'firstname', 'value'=> set_value('firstname', $customer['firstname']));
$last		= array('id'=>'lastname', 'class'=>'input', 'name'=>'lastname', 'value'=> set_value('lastname', $customer['lastname']));
$email		= array('id'=>'email', 'class'=>'input', 'name'=>'email', 'value'=> set_value('email', $customer['email']));
$phone		= array('id'=>'phone', 'class'=>'input', 'name'=>'phone', 'value'=> set_value('phone', $customer['phone']));

$password	= array('id'=>'password', 'class'=>'input', 'name'=>'password', 'value'=>'');
$confirm	= array('id'=>'confirm', 'class'=>'input', 'name'=>'confirm', 'value'=>'');
?>	
	<div id="my_account_info">
		<div id="my_information">
			<?php echo form_open('secure/my_account'); ?>
				<h2>Account Information</h2>
				<div class="form_wrap">
					<div>
						<?php echo lang('account_company');?><br/>
						<?php echo form_input($company);?>
					</div>
				</div>
				<div class="form_wrap">
					<div>
						<?php echo lang('account_firstname');?><b class="r"> *</b><br/>
						<?php echo form_input($first);?>
					</div>
					<div >
						<?php echo lang('account_lastname');?><b class="r"> *</b><br/>
						<?php echo form_input($last);?>
					</div>
				</div>
				
				<div class="form_wrap">
					<div>
						<?php echo lang('account_email');?><b class="r"> *</b><br/>
						<?php echo form_input($email);?>
					</div>
					<div >
						<?php echo lang('account_phone');?><b class="r"> *</b><br/>
						<?php echo form_input($phone);?>
					</div>
				</div>
				
				<div class="form_wrap">
					<div>
						<input type="checkbox" name="email_subscribe" value="1" <?php if((bool)$customer['email_subscribe']) { ?> checked="checked" <?php } ?>/> <?php echo lang('account_newsletter_subscribe');?>
					</div>
					
				</div>
				
				<div class="form_wrap">
					<div style="margin-top:20px; margin-bottom:0px; padding:0px; float:none; text-align:center;"><small><?php echo lang('account_password_instructions');?></small></div>
					<div>
						<?php echo lang('account_password');?><br/>
						<?php echo form_password($password);?>
					</div>
					<div >
						<?php echo lang('account_confirm');?><br/>
						<?php echo form_password($confirm);?>
					</div>
				</div>
				
				<div class="form_wrap" style="text-align:center;">
					<input type="submit" value="Save Information"  />
				</div>
			</form>
		</div>
		<div id="address_manager">
			<input type="button" class="edit_address right" rel="0" value="<?php echo lang('add_address');?>"/>
			<h2><?php echo lang('address_manager');?></h2>
			<script type="text/javascript">
			$(document).ready(function(){
				$('#address_list .my_account_address:even').addClass('address_bg');	
			});
			</script>
			<div id="address_list">
				
			<?php
			$c = 1;
			foreach($addresses as $a):?>
				<div class="my_account_address" id="address_<?php echo $a['id'];?>">
					<div class="address_toolbar">
						<input type="button"class="delete_address" rel="<?php echo $a['id'];?>" value="<?php echo lang('form_delete');?>" />
						<input type="button"class="edit_address" rel="<?php echo $a['id'];?>" value="<?php echo lang('form_edit');?>" />
						<br>
						<input type="radio" name="bill_chk" onclick="set_default(<?php echo $a['id'] ?>, 'bill')" <?php if($customer['default_billing_address']==$a['id']) echo 'checked="checked"'?> /> <?php echo lang('default_billing');?> <input type="radio" name="ship_chk" onclick="set_default(<?php echo $a['id'] ?>,'ship')" <?php if($customer['default_shipping_address']==$a['id']) echo 'checked="checked"'?>/> <?php echo lang('default_shipping');?>
					</div>
					<?php
					$b	= $a['field_data'];
					echo nl2br(format_address($b));
					?>
				</div>
			<?php endforeach;?>
			</div>
		</div>
		<br class="gc_clr"/>
	</div>
	<br class="gc_clr"/>
</div>
<br class="gc_clr"/>
<div style="text-align:center;">
<h2 style="padding:20px 0px 10px 0px; border-bottom:1px dashed #ccc; margin-bottom:10px;"><?php echo lang('order_history');?></h2>
<?php if($orders):
	echo $orders_pagination;
?>
<table class="cart_table" cellpadding="0" cellspacing="0" border="0">
	<thead>
		<tr>
			<th class="product_info"><?php echo lang('order_date');?></th>
			<th><?php echo lang('order_number');?></th>
			<th><?php echo lang('order_status');?></th>
		</tr>
	</thead>

	<tbody class="cart_items" style="text-align:left;">
	<?php
	foreach($orders as $order): ?>
		<tr class="cart_spacer"><td colspan="7"></td></tr>
		<tr class="cart_item">
			<td>
				<?php $d = format_date($order->ordered_on); 
				
				$d = explode(' ', $d);
				echo $d[0].' '.$d[1].', '.$d[3];
				
				?>
			</td>
			<td><?php echo $order->order_number; ?></td>
			<td><?php echo $order->status;?></td>
		</tr>
		
	<?php endforeach;?>
	</tbody>
</table>
<?php else: ?>
	<?php echo lang('no_order_history');?>
<?php endif;?>

<?php include('footer.php');?>
