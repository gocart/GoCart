<?php include('header.php'); ?>

<style type="text/css">
    body {
        margin-top:50px;
    }
</style>

<div class="row">
    
    <div class="span4 offset4">
        <div style="text-align:center; margin-bottom:15px;">
            <img src="<?php echo base_url('assets/img/logo.svg');?>"/>
        </div>
    
    <?php echo form_open($this->config->item('admin_folder').'/login') ?>
    <fieldset>
        <label for="username"><?php echo lang('username');?></label>
        <?php echo form_input(array('name'=>'username', 'class'=>'span4')); ?>
        
        <label for="password"><?php echo lang('password');?></label>
        <?php echo form_password(array('name'=>'password', 'class'=>'span4')); ?>
        
        <label class="checkbox">
            <?php echo form_checkbox(array('name'=>'remember', 'value'=>'true'))?>
            <?php echo lang('stay_logged_in');?>
        </label>
        
            <input class="btn btn-primary" type="submit" value="<?php echo lang('login');?>"/>
        
        
        <input type="hidden" value="<?php echo $redirect; ?>" name="redirect"/>
        <input type="hidden" value="submitted" name="submitted"/>
        
    </fieldset>
    <?php echo  form_close(); ?>
    </div>
</div>

<?php include('footer.php'); ?>