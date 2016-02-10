<?php
include_once('init.php');
$offset = $_POST['offset'];
$lastInvoiceNo = $_POST['lastInvoiceNo'];
$customerInvoice = $servicePortal->retrieveInvoice($offset);
$nextOffset = $customerInvoice->nextOffset();

include("invoiceTable.php"); 
?>
