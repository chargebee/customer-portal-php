<?php
$invoiceEstimate = null;
if ($subscription->status != 'non_renewing' &&  $subscription->status != 'cancelled') {
    $invoiceEstimate = $servicePortal->changeSubscriptionEstimate(null, null, null, false, false)->nextInvoiceEstimate;
}
if ($subscription->status == "future") {
	$subscriptionInfoMsg = InfoNAlerts::subscriptionInfoInFutureState($servicePortal);
} else if ($subscription->status == "in_trial") {
	$subscriptionInfoMsg = InfoNAlerts::subscriptionInfoInTrialState($servicePortal);
} else if ($subscription->status == "active") {
	$subscriptionInfoMsg = InfoNAlerts::subscriptionInfoInActiveState($servicePortal);
} else if ($subscription->status == "non_renewing") {
	$subscriptionInfoMsg = InfoNAlerts::subscriptionInfoInNonRenewingState($servicePortal); 
} else if ($subscription->status == "cancelled") {
	$subscriptionInfoMsg = InfoNAlerts::subscriptionInfoInCancelState($servicePortal);
}
?>

<?php if(!is_null($subscriptionInfoMsg) && !empty($subscriptionInfoMsg)) {?>
<div class="text-center">    
        <div class="alert alert-info">
            <div class="media text-left">
                <span class="glyphicon glyphicon-info-sign pull-left"></span>
                <div class="media-body">
					<?php echo $subscriptionInfoMsg ?>
                </div>
            </div>
        </div>
</div>
<?php } ?>

<ul class="list-unstyled cb-subscribed-items">
    <?php
    if (isset($invoiceEstimate) && isset($invoiceEstimate->lineItems)) {
        foreach ($invoiceEstimate->lineItems as $li) {
            ?>
            <li>
                <div class="row">
                    <?php if($li->quantity == 1){ ?>
                        <div class="col-xs-8"><?php echo esc($li->description) ?> </div>
                    <?php }  else { ?>
                        <div class="col-xs-8">
							<?php echo esc($li->description) ?> 
							(<?php echo $configData['currency_value'] .' '. number_format($li->unitAmount / 100, 2, '.', '') ?> x <?php echo esc($li->quantity) ?>)
						</div>
                    <?php } ?>
                    <div class="col-xs-4 text-right">
						<?php echo $configData['currency_value'] .' '.number_format($li->amount / 100, 2, '.', '') ?>
					</div>
                </div>
            </li>
        <?php
        }
    }
	
    if (isset($invoiceEstimate) && isset($invoiceEstimate->taxes)) {
        foreach ($invoiceEstimate->taxes as $t) {
            ?>
            <li>
                <div class="row">
                    <div class="col-xs-8"><?php echo esc($t->description) ?></div>
                    <div class="col-xs-4 text-right">
						<?php echo $configData['currency_value'] .' '.number_format($t->amount / 100, 2, '.', '') ?></div>
                </div>
            </li>
        <?php
        }
    }
    if (isset($invoiceEstimate) && isset($invoiceEstimate->discounts)) {
        foreach ($invoiceEstimate->discounts as $dis) {
            ?>
        <li>
            <div class="row">
                <div class="col-xs-8"><?php echo esc($dis->description) ?></div>
                <div class="col-xs-4 text-right"> &#45; <?php echo $configData['currency_value'] .' '.number_format($dis->amount / 100, 2, '.', '') ?></div>
            </div>
        </li>
    <?php
    }
}
?>
</ul>


<div class="text-right">
    <span class="text-muted">
			<?php echo InfoNAlerts::timeLineSubscriptionRecurringInfoMsg($servicePortal) ?>  
	</span>
    <span class="cb-subscribed-total">
		<?php if(isset($invoiceEstimate)) { ?>
			<?php echo $configData['currency_value'] .' '.number_format( $invoiceEstimate->total / 100, 2, '.', '') ?>
		<?php } else {
			echo $configData['currency_value'] .' '. number_format( 0 / 100, 2, '.', '');
		} ?>
	</span>
</div>