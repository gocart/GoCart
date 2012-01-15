<div class="checkout_block">

	<div id="no_payment_necessary" <?php if($this->go_cart->total()!=0) { ?> style="display:none" <?php } ?>>

		<?php echo lang('no_payment_needed');?>
	
	</div>


	<?php
	if(count($payment_methods) > 1):
	?>
	<div id="payment_section_container" <?php if($this->go_cart->total()==0) { ?> style="display:none" <?php } ?>>
		<div class="error" id="payment_error_box" style="display:none"></div>
		<table style="width:100%;">
			<tr>
				<td style="width:30%;">
					<div id="payments">
						<h3><?php echo lang('choose_payment_method');?></h3>
						<?php foreach ($payment_methods as $method=>$info):?>
							<input type="radio" id="payment_<?php echo $method;?>" name="payment_method" value="<?php echo $method;?>" onchange="set_payment(this.value)" /><label for="payment_<?php echo $method;?>"><?php echo $info['name'];?></label><br/>
						<?php endforeach;?>
					</div>
				</td>
				<td>
				
						<?php foreach ($payment_methods as $method=>$info):?>
							<form id="pmnt_form_<?php echo $method;?>">
								<input type="hidden" name="module" value="<?php echo $method;?>" />
							<div class="gc_payment_form" id="pmnt_<?php echo $method;?>">
								<h3><?php echo $info['name']; ?></h3>
								<?php echo $info['form'];?>
								<div class="gc_clr"></div>
							</div>
							</form>
						<?php endforeach;?>
				
				</td>
			</tr>
		</table>
	

	
	<script type="text/javascript">
	
		function set_payment(value) {
		
			chosen_method = value;
		
			$('.gc_payment_form').hide();
			$('#pmnt_'+value).show();
		}
		</script>
	</div>

	<?php else: ?>
	<?php // overwrite the css from the stylesheet here ?>
	<style type="text/css">
	.gc_payment_form {
		display:block;
	}
	</style>
	<div id="payment_section_container" <?php if($this->go_cart->total()==0) { ?> style="display:none" <?php } ?>>
		<div class="error" id="payment_error_box" style="display:none"></div>
	
			<?php foreach ($payment_methods as $method=>$info):?>
				<input type="hidden" id="payment_<?php echo $method;?>" name="payment_method" value="<?php echo $method;?>"/>
			<?php endforeach;?>
		
			<?php foreach ($payment_methods as $method=>$info):?>
				<form id="pmnt_form_<?php echo $method;?>">
					<input type="hidden" name="module" value="<?php echo $method;?>" />
				<div class="gc_payment_form" id="pmnt_<?php echo $method;?>">
					<h3><?php echo $info['name']; ?></h3>
					<?php echo $info['form'];?>
					<div class="gc_clr"></div>
				</div>
				</form>
			
				<script type="text/javascript">
					chosen_method = '<?php echo $method;?>';
				</script>
			
			<?php endforeach;?>
	</div>
	<?php endif; ?>

<div class="clear"></div>
</div>