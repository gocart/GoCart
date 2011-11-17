<?php 
$additional_header_info = '<style type="text/css">#page_title {text-align:center;}</style>';
include('header.php'); ?>

<div id="login_container_wrap">
		<div id="login_container">
		<?php echo form_open('secure/forgot_password') ?>
			<table>
				<tr>
					<td><?php echo lang('email');?></td>
					<td><input type="text" name="email" class="gc_login_input"/></td>
				</tr>
			</table>
			<div class="center">
					<input type="hidden" value="submitted" name="submitted"/>
					<input type="submit" value="Reset Password" name="<?php echo lang('form_submit');?>"/>
			</div>
		</form>
		<div id="login_form_links">
			<a href="<?php echo site_url('secure/login'); ?>"><?php echo lang('return_to_login');?></a>
		</div>
	</div>
</div>

<?php include('footer.php'); ?>
