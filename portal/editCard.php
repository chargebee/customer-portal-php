<?php
include("init.php");
$result = ChargeBee_HostedPage::updatePaymentMethod(array(
    "embed" => false,
    "redirectUrl" => getReturnURL(),
    "cancelUrl" => getReturnURL(),
    array("customer" => array("id" => $servicePortal->getCustomer()->id))
));

header('Location: ' . $result->hostedPage()->url);
?>