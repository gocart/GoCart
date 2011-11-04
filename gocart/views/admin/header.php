<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Go Cart :: <?php echo $page_title; ?></title>

        <link href="/assets/css/admin.css" rel="stylesheet" type="text/css" />

        <?php
//test for http / https for non hosted files
        $http = 'http';
        if (isset($_SERVER['HTTPS'])) {
            $http .= 's';
        }
        ?>
        <link href="<?php echo $http; ?>://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/smoothness/jquery-ui.css" type="text/css" rel="stylesheet"/> 
        <script type="text/javascript" src="<?php echo $http; ?>://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script> 
        <script type="text/javascript" src="<?php echo $http; ?>://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/jquery-ui.min.js"></script>
        <link href='<?php echo $http; ?>://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css' />

        <link href="/assets/js/jquery/colorbox/colorbox.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="/assets/js/jquery/colorbox/jquery.colorbox-min.js"></script>

        <script type="text/javascript" src="/assets/js/jquery/tiny_mce/tiny_mce.js"></script>
        <script type="text/javascript" src="/assets/js/jquery/tiny_mce/tiny_mce_init.php"></script>

        <script type="text/javascript">
            $(document).ready(function(){
                buttons();
                $("#gc_tabs").tabs();
                $('.datepicker').datepicker({dateFormat: 'yy-mm-dd'});
            });
            function buttons()
            {
                $('.list_buttons').buttonset();
                $('.button_set').buttonset();
                $('.button').button();
            }
        </script>

    </head>
    <body>
        <div id="wrapper">
            <div id="header">
                <div class="shine"></div>
                <a href="/<?php echo $this->config->item('admin_folder'); ?>/dashboard"><img src="/assets/images/admin/logo.png" alt="dashboard"/></a>
                <h2><?php echo $page_title; ?></h2>

                <span>
                    <a style="float:right;" href="/<?php echo $this->config->item('admin_folder'); ?>/login/logout">Logout</a>
                    <a style="float:right;" href="/">Front End</a>
                    <a style="float:right;" href="/<?php echo $this->config->item('admin_folder'); ?>/dashboard">Dashboard</a>
                </span>
            </div>
            <div id="container">
                <div id="menu_wrapper">
                    <?php
                    //just to shorten this up some.
                    $admin_url = base_url() . $this->config->item('admin_folder') . '/';
                    ?>

                    <div class="menu shadow">
                        <div class="menu_title">Sales</div>
                        <a href="<?php echo $admin_url; ?>orders">Orders</a>
                        <?php if ($this->auth->check_access('Admin')) : ?>
                            <a href="<?php echo $admin_url; ?>customers">Customers</a>
                            <a href="<?php echo $admin_url; ?>customers/groups">Groups</a>
                            <a href="<?php echo $admin_url; ?>reports">Reports</a>
                            <a href="<?php echo $admin_url; ?>coupons">Coupons</a>
                            <a href="<?php echo $admin_url; ?>giftcards">Giftcards</a>
                        <?php endif; ?>
                    </div>
                    <?php
                    // Restrict access to Admins only
                    if ($this->auth->check_access('Admin')) :
                        ?>
                        <div class="menu shadow">
                            <div class="menu_title">Catalog</div>
                            <a href="<?php echo $admin_url; ?>categories">Categories</a>
                            <a href="<?php echo $admin_url; ?>products">Products</a>
                        </div>

                        <div class="menu shadow">
                            <div class="menu_title">Content</div>
                            <a href="<?php echo $admin_url; ?>banners">Banners</a>
                            <a href="<?php echo $admin_url; ?>boxes">Boxes</a>
                            <a href="<?php echo $admin_url; ?>pages">Pages</a>
                        </div>

                        <div class="menu shadow">
                            <div class="menu_title">Administrative</div>
                            <a href="<?php echo $admin_url; ?>settings">Settings</a>
                            <a href="<?php echo $admin_url; ?>locations">Locations</a>
                            <a href="<?php echo $admin_url; ?>admin">Administrators</a>
                        </div>
                    <?php endif; ?>

                </div>
                <div id="page_content">
                    <?php
                    //lets have the flashdata overright "$message" if it exists
                    if ($this->session->flashdata('message')) {
                        $message = $this->session->flashdata('message');
                    }

                    if ($this->session->flashdata('error')) {
                        $error = $this->session->flashdata('error');
                    }

                    if (function_exists('validation_errors') && validation_errors() != '') {
                        $error = validation_errors();
                    }
                    ?>

                    <?php if (!empty($message)): ?>
                        <div class="ui-state-highlight ui-corner-all" style="padding:10px; margin-bottom:10px;"> 
                            <p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
                                <strong>Note:</strong> <?php echo $message; ?></p>
                        </div>
<?php endif; ?>

                    <?php if (!empty($error)): ?>
                        <div class="ui-state-error ui-corner-all" style="padding:10px; margin-bottom:10px;"> 
                            <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
                                <strong>Alert:</strong> <?php echo $error; ?></p>
                        </div>
<?php endif; ?>

                    <div id="js_error_container" class="ui-state-error ui-corner-all" style="display:none; padding:10px; margin-bottom:10px;"> 
                        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
                            <strong>Alert:</strong> <span id="js_error"></span> </p>
                    </div>