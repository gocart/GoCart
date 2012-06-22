<?php include('header.php'); ?>

<div class="row" style="margin-top:50px;">
	<div class="span6 offset3">
		<div class="page-header">
			<h1><?php echo lang('forgot_password');?></h1>
		</div>
		<?php echo form_open('secure/forgot_password', 'class="form-horizontal"') ?>
				<fieldset>
				
					<div class="control-group">
						<label class="control-label" for="email"><?php echo lang('email');?></label>
						<div class="controls">
							<input type="text" name="email" class="span3"/>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label"></label>
						<div class="controls">
							<input type="hidden" value="submitted" name="submitted"/>
							<input type="submit" value="<?php echo lang('reset_password');?>" name="submit" class="btn btn-primary"/>
						</div>
					</div>
				</fieldset>
		</form>
		<div style="text-align:center;">
			<a href="<?php echo site_url('secure/login'); ?>"><?php echo lang('return_to_login');?></a>
		</div>
	</div>
</div>

<?php include('footer.php'); ?>
