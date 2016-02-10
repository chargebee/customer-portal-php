<?php $count = 0; ?>
<table class="table table-hover" id="cb-portal-invoice-table">
    <tbody>
        <tr class="hidden-xs">
            <td class="control-label">Status</td>
            <td class="control-label">Date</td>
            <td class="control-label">Invoice Number</td>
            <td class="control-label text-right">Amount</td>
            <td></td>
            <td></td>
        </tr>
        <?php
        foreach ($customerInvoice as $entry) {
            
            $invoice = $entry->invoice();
            $amount = $invoice->amount;
            if ($invoice->status == "pending") {
                continue;
            }
			$count++;
            ?>
            <tr>
                <td data-cb-invoice="Status">
                    <?php if ($invoice->status == "paid") { ?> 
                        <span class="glyphicon glyphicon-ok text-success"></span>                            
                        <span class="hidden-lg hidden-sm hidden-md">&nbsp;<?php echo $invoice->status ?></span>
                    <?php } else if ($invoice->status == "payment_due") { ?>
                        <span class="glyphicon glyphicon-exclamation-sign text-warning"></span>
                        <span class="hidden-lg hidden-sm hidden-md">&nbsp;<?php echo $invoice->status ?></span>
                    <?php } else if ($invoice->status == "not_paid") { ?>
                        <span class="glyphicon glyphicon-remove text-danger"></span>
                        <span class="hidden-lg hidden-sm hidden-md">&nbsp;<?php echo $invoice->status ?></span>
                    <?php } ?>
                </td>
                <td data-cb-invoice="Date">
                    <?php echo date('d-M-y', $invoice->paidOn) ?>
                </td>
                <td data-cb-invoice="Invoice Number">
                    <?php echo $invoice->id ?>
                </td>
                <td data-cb-invoice="Amount" class="text-right">
                    <?php echo $configData['currency_value'] . number_format($amount / 100, 2, '.', '') ?>
                </td>
                <td class="text-muted">
                    <span class="cb-portal-invoice-desc">
						Paid on <?php echo date('d-M-y', $invoice->endDate) ?>
					</span>
                </td>
                <td class="text-right">
                     <?php if ($settingconfigData["invoice"]["download"] == 'true') { 
                    	 $invoiceAsPdf = getEditUrl("downloadInvoice.php", $configData) . "?invoice_id=" . $invoice->id;
                     ?>
                     <a href="<?php echo $invoiceAsPdf ?>">
                        Download as PDF<span class="glyphicon glyphicon-cloud-download" title="Download"></span>
                     </a>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<div class="clearfix cb-portal-invoice-details">
    <p class="pull-left hidden-xs">
        <span class="glyphicon glyphicon-ok text-success"></span>&nbsp;<small>Paid </small>&emsp;
        <span class="glyphicon glyphicon-exclamation-sign text-warning"></span>&nbsp;<small>Payment Due</small>&emsp;
        <span class="glyphicon glyphicon-remove text-danger"></span>&nbsp;<small>Not Paid</small>
    </p>
    <p class="pull-right center-xs">
        Showing <span class="inv-start-no"><?php echo $lastInvoiceNo + 1 ?></span> - 
				<span class="inv-end-no"><?php echo $lastInvoiceNo + $count ?></span>&emsp;
        <?php if(isset($nextOffset)){ ?>
            <a href="" data-cb-nav="next" id="next">Next</a>
        <?php } else{ ?>
            <span class="text-muted" >Next</span>
        <?php } ?>
		<span class="inv-next-offset" style="display :none"><?php echo $customerInvoice->nextOffset() ?> </span>
    </p>
</div>