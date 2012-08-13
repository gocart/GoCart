			<div class="span6" id="additional_order_details">
				<h2 style="margin-left:0px;"><?php echo lang('additional_order_details');?></h2>
				<?php if($this->session->flashdata('additional_details_message')):?>
					<div class="alert alert-error">
						<?php echo $this->session->flashdata('additional_details_message');?>
					</div>
				<?php endif;?>
		
				<?php //additional order details ?>
				<form id="additional_details_form" method="post" action="<?php echo site_url('checkout/save_additional_details');?>">
					<fieldset>
						<div class="row">
							<div class="span6">
								<label><?php echo lang('heard_about');?></label>
								<?php echo form_input(array('name'=>'referral', 'class'=>'span6', 'value'=>$referral));?>
							</div>
						</div>
				
						<div class="row">
							<div class="span6">
								<label><?php echo lang('shipping_instructions');?></label>
								<?php echo form_textarea(array('name'=>'shipping_notes', 'class'=>'span6', 'rows'=>'4', 'value'=>$shipping_notes))?>
							</div>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
</div>