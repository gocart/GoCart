<?php
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