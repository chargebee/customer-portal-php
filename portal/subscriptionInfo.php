<?php
$currentPlanDetails = $servicePortal->retrievePlan($subscription->planId);
if ($subscription->status == 'non_renewing' && $subscription->currentTermEnd < time()) {
    $estimate = array();
} else {
    $estimate = $servicePortal->retrieveEstimate();
}
if ($subscription->status == "future") {
    if(isset($subscription->trialStart)){
        $trialstartdetails = str_replace('$subscription.trial_start', date('d-M-y', $subscription->trialStart), 											$infoconfigData['Future_Subscriptions']['Future_subscription_info_trial']);
        $subscriptionInfoMsg = str_replace('$subscription.trial_end', date('d-M-y', $subscription->trialEnd), $trialstartdetails);
    } else{
        $subscriptionInfoMsg = str_replace('$subscription.start_date', date('d-M-y', $subscription->startDate), 										$infoconfigData['Future_Subscriptions']['Future_subscription_info_active']);
    } 
} else if ($subscription->status == "in_trial") {
	$subscriptionInfoMsg =str_replace('$subscription.trial_end', date('d-M-y', $subscription->trialEnd), 									$infoconfigData['Trial_Subscriptions']['Trial_end_date']);
} else if ($subscription->status == "active") {
	$subscriptionInfoMsg = str_replace('$subscription.current_term_end', date('d-M-y', $subscription->currentTermEnd), 									$infoconfigData['Active_Subscriptions']['Subscription_renewal_info']);
} else if ($subscription->status == "non_renewing") {
	$subscriptionInfoMsg = str_replace('$subscription.cancelled_at', date('d-M-y', $subscription->cancelledAt), 									$infoconfigData['Non_Renewing_Subscriptions']['Will_be_canceled']); 
} else if ($subscription->status == "cancelled") {
    if($subscription->cancelReason == 'not_paid'){
        $subscriptionInfoMsg =  $infoconfigData['Canceled_Subscriptions']['Canceled_due_to_invoice_not_paid'];
    } elseif($subscription->cancelReason == 'no_card'){
        $subscriptionInfoMsg = $infoconfigData['Canceled_Subscriptions']['Canceled_due_to_no_card'];
    } else{
        $subscriptionInfoMsg = $infoconfigData['Canceled_Subscriptions']['Canceled'];
	}
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
    if (isset($estimate->lineItems)) {
        foreach ($estimate->lineItems as $li) {
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
	
    if (isset($estimate->taxes)) {
        foreach ($estimate->taxes as $t) {
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
    if (isset($estimate->discounts)) {
        foreach ($estimate->discounts as $dis) {
            ?>
        <li>
            <div class="row">
                <div class="col-xs-8"><?php echo esc($dis->description) ?></div>
                <div class="col-xs-4 text-right"><?php echo $configData['currency_value'] .' '.number_format($dis->amount / 100, 2, '.', '') ?></div>
            </div>
        </li>
    <?php
    }
}
?>
</ul>


<div class="text-right">
    <span class="text-muted">
		<?php 
        $phrase = $infoconfigData['Timeline']['Recurring_charge'];
        $default = array('$planperiod', '$planunit');
        $assign   = array($currentPlanDetails->period, $currentPlanDetails->periodUnit);
        echo str_replace($default,  $assign, $phrase); ?> </span>
    <span class="cb-subscribed-total">
		<?php echo $configData['currency_value'] .' '.number_format( $estimate->amount / 100, 2, '.', '')  ?>
	</span>
</div>