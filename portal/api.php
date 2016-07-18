<?php
include_once('init.php');

// Gets the action name from the form parameter 
$action = trim($_POST['action']);

/* Calls the action specified in the form submit */
$cnt = call_user_func_array(array($servicePortal, $action), array());

print_r($cnt);
exit;
?>
