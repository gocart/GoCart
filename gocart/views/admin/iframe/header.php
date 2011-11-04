s<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Go Cart</title>

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

        <?php display_css(array('../js/jquery/colorbox/colorbox.css', 'admin.css')); ?>
        <?php display_js(array('jquery/colorbox/jquery.colorbox-min.js', 'jquery/tiny_mce/tiny_mce.js', 'jquery/tiny_mce/tiny_mce_init.js')); ?>

        <script type="text/javascript">
            $(document).ready(function(){
                buttons();
            });
            function buttons()
            {
                $('.list_buttons').buttonset();
                $('.button_set').buttonset();
                $('.button').button();
            }
        </script>
        <style type="text/css">
            body {
                background-color:#fff;
                background-image:none;
            }
        </style>
        <body>