<?php
$additional_header_info = '<style type="text/css">#page_title {text-align:center;}</style>';
include('header.php');
?>

<div id="login_container_wrap">
    <div id="login_container">
        <?php echo secure_form_open('secure/login') ?>

<?php echo secure_form_open('secure/forgot_password') ?>
        <table>
            <tr>
                <td>Email: </td>
                <td><input type="text" name="email" class="gc_login_input"/></td>
            </tr>
        </table>
        <div class="center">
            <input type="hidden" value="submitted" name="submitted"/>
            <input type="submit" value="Reset Password" name="submit"/>
        </div>
        </form>
        <div id="login_form_links">
            <a href="<?php echo secure_base_url(); ?>secure/login">Return to Login</a>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
