<?php include('header.php'); ?>

<div class="row" style="margin-top:50px;">
	<div class="span6 offset3">
		<div class="page-header">
			<h1><?php echo lang('login');?></h1>
		</div>
			<?php echo form_open('secure/login', 'class="form-horizontal"'); ?>
				<fieldset>
				
					<div class="control-group">
						<label class="control-label" for="email"><?php echo lang('email');?></label>
						<div class="controls">
							<input type="text" name="email" class="span3"/>
						</div>
					</div>
				
					<div class="control-group">
						<label class="control-label" for="password"><?php echo lang('password');?></label>
						<div class="controls">
							<input type="password" name="password" class="span3"/>
						</div>
					</div>
				
					<div class="control-group">
						<label class="control-label"></label>
						<div class="controls">
							<label class="checkbox">
								<input name="remember" value="true" type="checkbox" />
								 <?php echo lang('keep_me_logged_in');?>
							</label>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="password"></label>
						<div class="controls">
							<input type="submit" value="<?php echo lang('form_login');?>" name="submit" class="btn btn-primary"/>
						</div>
					</div>
				</fieldset>
				
				<input type="hidden" value="<?php echo $redirect; ?>" name="redirect"/>
				<input type="hidden" value="submitted" name="submitted"/>
				
			</form>
		
			<div style="text-align:center;">
				<a href="<?php echo site_url('secure/forgot_password'); ?>"><?php echo lang('forgot_password')?></a> | <a href="<?php echo site_url('secure/register'); ?>"><?php echo lang('register');?></a>
			</div>
	</div>
</div>
<?php include('footer.php'); ?>