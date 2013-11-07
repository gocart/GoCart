<?php

//if the gocart config file doesn't exist, the cart hasn't been installed.
if(!file_exists(dirname($_SERVER['SCRIPT_FILENAME']).'/gocart/config/gocart.php'))
{
	$folder = dirname($_SERVER['SCRIPT_NAME']);
	$path	= rtrim($_SERVER['HTTP_HOST'].$folder, '/\\').'/';	
	header('Location: http://'.$path.'install');
	die;
}

session_start();

//set the session userdata if non-existant
if(!isset($_SESSION['userdata']))
{
	$_SESSION['userdata'] = array();
}
//set newFlashdata if non-existent
if(!isset($_SESSION['newFlashdata']))
{
	$_SESSION['newFlashdata'] = array();
}

//empty out the "oldFlashdata" field
$_SESSION['oldFlashdata'] = array();

//shift newFlashdata over to oldFlashdata
$_SESSION['oldFlashdata'] = $_SESSION['newFlashdata'];
$_SESSION['newFlashdata'] = array();

require_once('CI.php');

exit;