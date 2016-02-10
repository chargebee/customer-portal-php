<?php
$page = 1;
$nextOffset = $customerInvoice->nextOffset();
$lastInvoiceNo = 0;
?>
<div id="invoiceTableShow">
    <?php include("invoiceTable.php"); ?>
</div>
