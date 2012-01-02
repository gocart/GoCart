<div class="checkout_block">
	<div id="additional_order_details">
		<?php if($this->session->flashdata('additional_details_message'))
		{
			echo '<div class="message">'.$this->session->flashdata('additional_details_message').'</div>';
		}
		?>
		<h3><?php echo lang('additional_order_details');?></h3>
		<?php //additional order details ?>
		<form id="additional_details_form" method="post" action="<?php echo site_url('checkout/save_additional_details');?>">
			<div class="form_wrap">
				<div>
					<?php echo lang('heard_about');?><br/>
					<?php echo form_input(array('name'=>'referral', 'class'=>'input', 'value'=>$referral));?>
				</div>
			</div>
			<div class="form_wrap">
				<div>
					<?php echo lang('shipping_instructions');?><br/>
					<?php echo form_textarea(array('name'=>'shipping_notes', 'class'=>'checkout_textarea', 'value'=>$shipping_notes))?>
				</div>
			</div>
		</form>
	</div>
	<div class="clear"></div>
</div>