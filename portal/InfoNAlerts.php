<?php

class InfoNAlerts {

static function paymentModeMsg($servicePortal) {
	$paymentMessage = null;
	if($servicePortal->getCustomer()->autoCollection == "on" && $servicePortal->getCustomer()->cardStatus == "no_card") {
	     $paymentMessage = "If a valid card is not present when a new invoice is generated, the subscription will be canceled.";
	} elseif ($servicePortal->getCustomer()->autoCollection == "on") {
		$paymentMessage = "Your card will be automatically charged when a new invoice is generated.";
	} elseif ($servicePortal->getCustomer()->autoCollection == "off") {
		$paymentMessage = "You have chosen an alternate payment method to pay for your subscriptions.";
	}
	return $paymentMessage;
}

static function paymentInfoMsg($servicePortal) {
	$paymentInfoMsg = null;
	$currentYear = date('Y');
	$currentMonth = date('n');
	
	if (!isset($servicePortal->getCustomer()->paymentMethod)) { 
		$paymentInfoMsg = "You have not added your card details. Use the add option to update your card details.";
	} else if( $currentYear > $servicePortal->getCard()->expiryYear || ($servicePortal->getCard()->expiryYear == $currentYear && 																$currentMonth  > $servicePortal->getCard()->expiryMonth ) ) {
		$paymentInfoMsg = "Your card is expired. Please update your card details to ensure uninterrupted service.";
	} else if( $servicePortal->getCard()->expiryYear == $currentYear && $servicePortal->getCard()->expiryMonth == $currentMonth ) {
		$paymentInfoMsg = "Your card is expiring. For uninterrupted services, please update your card details before your next billing.";
	}
	return $paymentInfoMsg;
}

static function billingAddressInfoMsg($servicePortal) {
	$billingAddressInfoMsg = "";
	if (!isset($servicePortal->getCustomer()->billingAddress)) { 
		$billingAddressInfoMsg = "You do not have any billing information. Use the add option to update your billing details.";
	}
	return $billingAddressInfoMsg;
}

static function invoiceNotPresentInfoMsg($servicePortal) {
	return "You do not have any invoices.";
}

static function shippingAddressNotPresetInfoMsg($servicePortal) {
	$shippingAddressInfoMsg = "";
	if(!isset($servicePortal->getSubscription()->shippingAddress)) {
		$shippingAddressInfoMsg = "You do not have any shipping information. Use the add option to update your shipping details.";
	}
	return $shippingAddressInfoMsg;
}

static function subscriptionInfoInTrialState($servicePortal){
   	return "Your trial period expires on ". date('d-M-y',$servicePortal->getSubscription()->trialEnd);
}

static function subscriptionInfoInFutureState($servicePortal) {
    if(isset($servicePortal->getSubscription()->trialStart)){
       return "Your trial period will start on" . date('d-M-y', $servicePortal->getSubscription()->trialStart) . " and will end on " . 
		   				date('d-M-y',$servicePortal->getSubscription()->trialEnd);
    } else {
		return "Your subscription will become active on ". date('d-M-y', $servicePortal->getSubscription()->startDate);
    } 
}
static function subscriptionInfoInActiveState($servicePortal) {
	return "Your next billing is on " . date('d-M-y', $servicePortal->getSubscription()->currentTermEnd);
}

static function subscriptionInfoInNonRenewingState($servicePortal){
	return "Your subscription will be canceled on " . date('d-M-y',$servicePortal->getSubscription()->currentTermEnd). " and no further recurring charges will be applied again.";
}

static function subscriptionInfoInCancelState($servicePortal) {

    if($servicePortal->getSubscription()->cancelReason == 'not_paid'){
        return "Your subscription has been canceled due to non payment of invoice. Please contact us to reactivate your subscription.";
    } elseif($servicePortal->getSubscription()->cancelReason == 'no_card'){
		return "Your subscription has been canceled as there was no card present to charge. Please contact us to reactivate your subscription.";
    } else {
        return "Your subscription has been canceled. Please contact us to reactivate your subscription.";
	}
}

static function timeLineSubscriptionRecurringInfoMsg($servicePortal){
	return 	"Your recurring charges every " . $servicePortal->getPlan()->period . " " . $servicePortal->getPlan()->periodUnit;
}
	
static function timeLineSubscriptionCreatedAtInfoMsg($servicePortal) {
	return "Signed up on " . date('d-M-y',$servicePortal->getSubscription()->createdAt);
}

static function timeLineSubscriptionCurrentTermEndInfoMsg($servicePortal){
	return "Your current billing term is ". date('d-M-y',$servicePortal->getSubscription()->currentTermStart) . " to " . date('d-M-y',$servicePortal->getSubscription()->currentTermEnd);
}

static function timeLineSubscriptionTrialStartMsg($servicePortal){
	return "Your trial period started on ". date('d-M-y',$servicePortal->getSubscription()->trialStart);
}

static function timeLineSubscriptionTrialEndInfoMsg($servicePortal){
	return "Your trial expired on " . date('d-M-y',$servicePortal->getSubscription()->trialEnd);
}
	
static function timeLineSubscriptionActivatedAtInfoMsg($servicePortal){
	return "Your subscription was activated on " . date('d-M-y',$servicePortal->getSubscription()->activatedAt);
}

static function timeLineSubscriptionNextBillingInfoMsg($servicePortal) {
	return "Your next billing will be on ". date('d-M-y', $servicePortal->getSubscription()->currentTermEnd);
}

static function timeLineSubscriptionNonRenewingInfoMsg($servicePortal) {
	return "Your subscription will be canceled on " . date('d-M-y',$servicePortal->getSubscription()->currentTermEnd). " and no further recurring charges will be applied again.";
}

static function timeLineSubscriptionCancelledInfoMsg($servicePortal){
	return "Your subscription was canceled on ". date('d-M-y',$servicePortal->getSubscription()->cancelledAt);
}

static function cancelSubscriptionImmediatelyInfoMsg($servicePortal) {
	return "Your subscription will be canceled immediately.";
}

static function cancelSubscriptionEndOfTermInfoMsg($servicePortal){
	if($servicePortal->getSubscription()->status == "in_trial") {
		return "Your subscription will be canceled when your current billing term ends on " . 
			date('d-M-y',$servicePortal->getSubscription()->trialEnd);
	} else {
		return "Your subscription will be canceled when your current billing term ends on " . 
			date('d-M-y',$servicePortal->getSubscription()->currentTermEnd);
	}
}

static function subscriptionChangeImmediatelyInfoMsg($servicePortal, $estimate, $currency){
	return "Your subscription changes will be applied immediately and your next renewal will be on " . 
		date('d-M-y',$servicePortal->getSubscription()->currentTermEnd) . ". You will be charged " .  $currency. 
		number_format((isset($estimate->invoiceEstimate) ? $estimate->invoiceEstimate->amountDue : $estimate->nextInvoiceEstimate->amountDue)/100, 2, ".", "");
}

static function subscriptionChangeEndOfTermInfoMsg($servicePortal, $estimate, $currency) {
	return "Your subscription changes will be applied when your renewal happens on " . 
		date('d-M-y',$servicePortal->getSubscription()->currentTermEnd) . ". Your new recurring charge will be " .
			$currency .number_format($estimate->nextInvoiceEstimate->amountDue/100, 2, ".", "") . ".";
}

}	

?>
