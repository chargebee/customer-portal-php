<?php
include("init.php");
$invoiceId = $_GET['invoice_id'];
$invoice = ChargeBee_Invoice::retrieve($invoiceId)->invoice();
if( $invoice->subscriptionId != $servicePortal->getSubscription()->id ) {
    header("HTTP/1.0 400 Error");
	exit;
}
$result = ChargeBee_Invoice::pdf($invoiceId);
header("Location: " . $result->download()->downloadUrl);
?>