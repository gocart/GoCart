<div class="page-header">
	<h2><?php echo lang('form_checkout');?></h2>
</div>

<?php if (validation_errors()):?>
	<div class="alert alert-error">
		<a class="close" data-dismiss="alert">×</a>
		<?php echo validation_errors();?>
	</div>
<?php endif;?>

<?php include('order_details.php');?>

<?php echo form_open('checkout/step_2');?>
	<div class="row">
		<div class="span6">
				<h2><?php echo lang('shipping_method');?></h2>
				<div class="alert alert-error" id="shipping_error_box" style="display:none"></div>
				<table class="table">
					<?php
					foreach($shipping_methods as $key=>$val):
						$ship_encoded	= md5(json_encode(array($key, $val)));
					
						if($ship_encoded == $shipping_code)
						{
							$checked = true;
						}
						else
						{
							$checked = false;
						}
					?>
					<tr style="cursor:pointer">
						<td style="width:16px;">
							<label class="radio"><?php echo form_radio('shipping_method', $ship_encoded, set_radio('shipping_method', $ship_encoded, $checked), 'id="s'.$ship_encoded.'"');?></label>
						</td>
						<td onclick="toggle_shipping('s<?php echo $ship_encoded;?>');"><?php echo $key;?></td>
						<td onclick="toggle_shipping('s<?php echo $ship_encoded;?>');"><strong><?php echo $val['str'];?></strong></td>
					</tr>
					<?php endforeach;?>
				</table>
		</div>
		<div class="span6">
			<h2><?php echo lang('shipping_instructions')?></h2>
			<?php echo form_textarea(array('name'=>'shipping_notes', 'value'=>set_value('shipping_notes', $this->go_cart->get_additional_detail('shipping_notes')), 'class'=>'span6', 'style'=>'height:75px;'));?>
		</div>
	</div>
	<input class="btn btn-block btn-large btn-primary" type="submit" value="<?php echo lang('form_continue');?>"/>
</form>
<script type="text/javascript">
	function toggle_shipping(key)
	{
		var check = $('#'+key);
		if(!check.attr('checked'))
		{
			check.attr('checked', true);
		}
	}
</script>