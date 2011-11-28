<?php 

$additional_header_info = '<style type="text/css">#page_title {text-align:center;}</style>';
include('header.php'); ?>
	<div id="login_container_wrap">
		<div id="login_container">
		
			<?php echo form_open('secure/login') ?>
				<table>
					<tr>
						<td><?php echo lang('email');?></td>
						<td><input type="text" name="email" class="gc_login_input"/></td>
					</tr>
					<tr>
						<td><?php echo lang('password');?></td>
						<td><input type="password" name="password" class="gc_login_input"/></td>
					</tr>
				</table>
				<div class="center">
						<input name="remember" value="true" type="checkbox" /> <?php echo lang('keep_me_logged_in');?><br/>
						<input type="hidden" value="<?php echo $redirect; ?>" name="redirect"/>
						<input type="hidden" value="submitted" name="submitted"/>
						<input type="submit" value="<?php echo lang('form_login');?>" name="submit" class="gc_login_button"/>
				</div>
			</form>
		
			<div id="login_form_links">
				<a href="<?php echo site_url('secure/forgot_password'); ?>"><?php echo lang('forgot_password')?></a> | <a href="<?php echo site_url('secure/register'); ?>"><?php echo lang('register');?></a>
			</div>
		</div>
	</div>
<?php include('footer.php'); ?>