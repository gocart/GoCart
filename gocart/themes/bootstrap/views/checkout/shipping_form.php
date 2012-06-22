<div class="row" id="additional_order_details">
	<div class="span12" style="border-bottom:4px solid #ddd; padding-bottom:15px;">
		<div class="row">
			<div class="span6">
				<h2><?php echo lang('shipping_method');?></h2>
				<?php if(count($shipping_methods) > 0):?>
					<div class="alert alert-error" id="shipping_error_box" style="display:none"></div>
						<?php if($this->go_cart->requires_shipping()):?>
							<table class="table">
								<?php foreach($shipping_methods as $key=>$val):?>
								<tr>
									<td style="width:16px;"><input type="radio" id="<?php echo url_title($key);?>" class="ship_option" name="shipping_input" value="<?php echo $key;?>:<?php echo $val['num'];?>" onclick="set_shipping_cost()"/></td>
									<td onclick="toggle_shipping('<?php echo url_title($key);?>');"><?php echo $key;?></td>
									<td onclick="toggle_shipping('<?php echo url_title($key);?>');"><?php echo $val['str'];?></td>
								</tr>
								<?php endforeach;?>
							</table>
						<?php else: ?>
							<?php echo lang('no_shipping_needed');?>
						<?php endif; ?>
				<?php else:?>
					<p><?php echo lang('no_shipping_needed');?></p>
				<?php endif;?>
			</div>
			
			<script type="text/javascript">
				function toggle_shipping(key)
				{
					var check = $('#'+key);
					if(check.attr('checked'))
					{
						check.attr('checked', false);
					}
					else
					{
						check.attr('checked', true);
					}
					set_shipping_cost(key);
				}
			</script>