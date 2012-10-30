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

	<div class="row">
		<div class="span12">
			<h2><?php echo lang('payment_method');?></h2>
			<div class="tabbable tabs-left">
				<ul class="nav nav-tabs">
				<?php
				if(empty($payment_method))
				{
					$selected	= key($payment_methods);
				}
				else
				{
					$selected	= $payment_method['module'];
				}
				foreach($payment_methods as $method=>$info):?>
					<li <?php echo ($selected == $method)?'class="active"':'';?>><a href="#payment-<?php echo $method;?>" data-toggle="tab"><?php echo $info['name'];?></a></li>
				<?php endforeach;?>
				</ul>
				<div class="tab-content">
					<?php foreach ($payment_methods as $method=>$info):?>
						<div id="payment-<?php echo $method;?>" class="tab-pane<?php echo ($selected == $method)?' active':'';?>">
							<?php echo form_open('checkout/step_3', 'id="form-'.$method.'"');?>
								<input type="hidden" name="module" value="<?php echo $method;?>" />
								<?php echo $info['form'];?>
								<input class="btn btn-block btn-large btn-primary" type="submit" value="<?php echo lang('form_continue');?>"/>
							</form>
						</div>
					<?php endforeach;?>
				</div>
			</div>
		</div>
	</div>