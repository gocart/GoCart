<?php include('header.php');?>

<?php if(validation_errors()):?>
<div class="alert allert-error">
	<a class="close" data-dismiss="alert">×</a>
	<?php echo validation_errors();?>
</div>
<?php endif;?>
<script type="text/javascript">
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
		$.post('<?php echo site_url('secure/address_form'); ?>/'+$(this).attr('rel'),
			function(data){
				$('#address-form-container').html(data).modal('show');
			}
		);
//		$.fn.colorbox({	href: '<?php echo site_url('secure/address_form'); ?>/'+$(this).attr('rel')});
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


<?php
$company	= array('id'=>'company', 'class'=>'span4', 'name'=>'company', 'value'=> set_value('company', $customer['company']));
$first		= array('id'=>'firstname', 'class'=>'span2', 'name'=>'firstname', 'value'=> set_value('firstname', $customer['firstname']));
$last		= array('id'=>'lastname', 'class'=>'span2', 'name'=>'lastname', 'value'=> set_value('lastname', $customer['lastname']));
$email		= array('id'=>'email', 'class'=>'span2', 'name'=>'email', 'value'=> set_value('email', $customer['email']));
$phone		= array('id'=>'phone', 'class'=>'span2', 'name'=>'phone', 'value'=> set_value('phone', $customer['phone']));

$password	= array('id'=>'password', 'class'=>'span2', 'name'=>'password', 'value'=>'');
$confirm	= array('id'=>'confirm', 'class'=>'span2', 'name'=>'confirm', 'value'=>'');
?>	
	<div class="span4">
		<div class="my-account-box">
		<?php echo form_open('secure/my_account'); ?>
			<fieldset>
				<h2><?php echo lang('account_information');?></h2>
				
				<div class="row">
					<div class="span4">
						<label for="company"><?php echo lang('account_company');?></label>
						<?php echo form_input($company);?>
					</div>
				</div>
				<div class="row">	
					<div class="span2">
						<label for="account_firstname"><?php echo lang('account_firstname');?></label>
						<?php echo form_input($first);?>
					</div>
				
					<div class="span2">
						<label for="account_lastname"><?php echo lang('account_lastname');?></label>
						<?php echo form_input($last);?>
					</div>
				</div>
			
				<div class="row">
					<div class="span2">
						<label for="account_email"><?php echo lang('account_email');?></label>
						<?php echo form_input($email);?>
					</div>
				
					<div class="span2">
						<label for="account_phone"><?php echo lang('account_phone');?></label>
						<?php echo form_input($phone);?>
					</div>
				</div>
			
				<div class="row">
					<div class="span7">
						<label class="checkbox">
							<input type="checkbox" name="email_subscribe" value="1" <?php if((bool)$customer['email_subscribe']) { ?> checked="checked" <?php } ?>/> <?php echo lang('account_newsletter_subscribe');?>
						</label>
					</div>
				</div>
			
				<div class="row">
					<div class="span4">
						<div style="margin:30px 0px 10px; text-align:center;">
							<strong><?php echo lang('account_password_instructions');?></strong>
						</div>
					</div>
				</div>
			
				<div class="row">	
					<div class="span2">
						<label for="account_password"><?php echo lang('account_password');?></label>
						<?php echo form_password($password);?>
					</div>

					<div class="span2">
						<label for="account_confirm"><?php echo lang('account_confirm');?></label>
						<?php echo form_password($confirm);?>
					</div>
				</div>
			
				<input type="submit" value="<?php echo lang('form_submit');?>" class="btn btn-primary" />

			</fieldset>
		</form>
		</div>
	</div>
	
	<div class="span7 pull-right">
		<div class="row" style="padding-top:10px;">
			<div class="span4">
				<h2><?php echo lang('address_manager');?></h2>
			</div>
			<div class="span3" style="text-align:right;">
				<input type="button" class="btn edit_address" rel="0" value="<?php echo lang('add_address');?>"/>
			</div>
		</div>
		<div class="row">
			<div class="span7" id='address_list'>
			<?php if(count($addresses) > 0):?>
				<table class="table table-bordered table-striped">
			<?php
			$c = 1;
				foreach($addresses as $a):?>
					<tr id="address_<?php echo $a['id'];?>">
						<td>
							<?php
							$b	= $a['field_data'];
							echo format_address($b, true);
							?>
						</td>
						<td>
							<div class="row-fluid">
								<div class="span12">
									<div class="btn-group pull-right">
										<input type="button" class="btn edit_address" rel="<?php echo $a['id'];?>" value="<?php echo lang('form_edit');?>" />
										<input type="button" class="btn btn-danger delete_address" rel="<?php echo $a['id'];?>" value="<?php echo lang('form_delete');?>" />
									</div>
								</div>
							</div>
							<div class="row-fluid">
								<div class="span12">
									<div class="pull-right" style="padding-top:10px;">
										<input type="radio" name="bill_chk" onclick="set_default(<?php echo $a['id'] ?>, 'bill')" <?php if($customer['default_billing_address']==$a['id']) echo 'checked="checked"'?> /> <?php echo lang('default_billing');?>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="ship_chk" onclick="set_default(<?php echo $a['id'] ?>,'ship')" <?php if($customer['default_shipping_address']==$a['id']) echo 'checked="checked"'?>/> <?php echo lang('default_shipping');?>
									</div>
								</div>
							</div>
						</td>
					</tr>
				<?php endforeach;?>
				</table>
			<?php endif;?>
			</div>
		</div>
	</div>

<div class="row">
	<div class="span12">
		<div class="page-header">
			<h2><?php echo lang('order_history');?></h2>
		</div>
		<?php if($orders):
			echo $orders_pagination;
		?>
		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<th><?php echo lang('order_date');?></th>
					<th><?php echo lang('order_number');?></th>
					<th><?php echo lang('order_status');?></th>
				</tr>
			</thead>

			<tbody>
			<?php
			foreach($orders as $order): ?>
				<tr>
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
	</div>
</div>

<div id="address-form-container" class="hide">
</div>
<?php include('footer.php');