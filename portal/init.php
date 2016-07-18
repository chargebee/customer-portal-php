<?php 

$config = __DIR__ . "/config/config.ini";
$configData = parse_ini_file($config);

$settingsconfig = __DIR__ . "/config/portalsettings.ini";
$settingconfigData = parse_ini_file($settingsconfig);

error_reporting(E_ALL);
ini_set("display_errors", $configData['display_errors']);

/* Default Time zone */
date_default_timezone_set("GMT");

include_once('utils/functions.php');

/* Importing Chargebee's PHP library */
require_once("lib/ChargeBee.php");

/* Environment Configuration of your Chargebee site */
ChargeBee_Environment::configure($configData['SITE_NAME'], $configData['SITE_API_KEY']);

include_once("InfoNAlerts.php");
/* Including servicePortal class */
include_once("ServicePortal.php");

/* If you have your own authentication module, add comments to the below code and use your own authentication.  */

/*
 * ***** Authentication code starts here *****
 */
include_once('Auth.php');
$authObj = new Auth();

$successMessage = isset($_GET['success']) ? filter_input(INPUT_GET, 'success') : null;
$doAction = isset($_GET['do']) ? filter_input(INPUT_GET, 'do') : null;
$cbStatus = isset($_GET['status']) ? filter_input(INPUT_GET, 'status') : null;
if( $doAction == "logout" || $cbStatus == "logged_out") { 
    $authObj->logout($configData);
}   

if(isset($_GET['auth_session_id']) && isset($_GET['auth_session_token'])){
    $params = array(
        'cb_auth_session_id' => filter_input(INPUT_GET,'auth_session_id'),  
        'cb_auth_session_token' => filter_input(INPUT_GET, 'auth_session_token')
    );
    $authObj->authenticateSession($params, $configData);
} 


if (!$authObj->isLoggedIn()) {
    header('Location: ' . getPortalLoginUrl($configData));
    exit;
}
/* 
 * **** Authentication code ends here **** 
 */

/*
 * If you're using your own authentication module, then the particular subscription ID should be passed 
 * in the constructor of ServicePortal object. 
 * This subscription ID will be used throughout the portal.
 */
$servicePortal = new ServicePortal($authObj->getSessionSubscriptionId());

?>
