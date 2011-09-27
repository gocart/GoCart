<?php 

$additional_header_info = '<style type="text/css">#page_title {text-align:center;}</style>';
include('header.php'); ?>
	<div id="login_container_wrap">
		<div id="login_container">
		
			<?php echo secure_form_open('secure/login') ?>
				<table>
					<tr>
						<td>Email: </td>
						<td><input type="text" name="email" class="gc_login_input"/></td>
					</tr>
					<tr>
						<td>Password: </td>
						<td><input type="password" name="password" class="gc_login_input"/></td>
					</tr>
				</table>
				<div class="center">
						<input name="remember" value="true" type="checkbox" /> Keep me logged in!<br/>
						<input type="hidden" value="<?= $redirect; ?>" name="redirect"/>
						<input type="hidden" value="submitted" name="submitted"/>
						<input type="submit" value="Login" name="submit" class="gc_login_button"/>
				</div>
			</form>
		
			<div id="login_form_links">
				<a href="<?php echo secure_base_url(); ?>secure/forgot_password">Lost Password?</a> | <a href="<?php echo secure_base_url(); ?>secure/register">Register</a>
			</div>
		</div>
	</div>
<?php include('footer.php'); ?>