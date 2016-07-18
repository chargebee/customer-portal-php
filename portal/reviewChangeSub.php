<?php
include_once('init.php');

$planId = isset($_GET["plan_id"]) ? $_GET["plan_id"] : $servicePortal->getSubscription()->planId;
$planQuantity = isset($_GET["plan_quantity"]) ? $_GET["plan_quantity"] : $servicePortal->getSubscription()->planQuantity;
$addons = isset($_GET["addons"]) ? $_GET["addons"] : array();
$invoiceEstimate = null;
$replaceAddonList = isset($_GET["replace_addon_list"]) ? true : false;
$endOfTerm = $settingconfigData["subscription"]["immediately"] == "false";
$estimate = $servicePortal->changeSubscriptionEstimate($planId, $planQuantity, $addons, $replaceAddonList, $endOfTerm);
if(isset($estimate->nextInvoiceEstimate)) {
	$invoiceEstimate = $estimate->nextInvoiceEstimate;
} else {
	$invoiceEstimate = $estimate->invoiceEstimate;
}
$planLineItems = $servicePortal->getLineItems($invoiceEstimate, "plan");
$addonLineItems = $servicePortal->getLineItems($invoiceEstimate, "addon");
?>
<div class="page-header clearfix">
    <span class="h3">Review and Confirm</span>
</div>
<p>
	Review your order and make sure you've selected the right products before confirming it. You can go back and make changes if needed.
</p>
<div class="cb-product-box">
	<?php if(sizeof($planLineItems) > 0) { ?>
    <div class="cb-product-title">
        Selected Plan
    </div>
    <div class="cb-product-list">
		
	<?php
	   foreach ($planLineItems as $li) {
		if($li->entityType == "plan") {
		?>		
        <div class="row cb-product-item">
            <div class="col-xs-8" id="selectedPlanReview">
				<?php echo $li->description . " (" . $configData['currency_value'] . number_format($li->unitAmount/100, 2, ".", "") .
					 		"x" . $li->quantity . ")"?> 
			</div>
            <div class="col-xs-4 text-right" id="selectedPlanAmount">
				<strong>
					<?php echo $configData['currency_value'] . number_format($li->amount/100, 2, ".","") ?>
				</strong>
			</div>
        </div>
	<?php }
      } ?>	
    </div>
	<?php } ?>
	<?php if(sizeof($addonLineItems) > 0) { ?>
    <div class="cb-product-title" id="ifAddonSelected">
        Selected Addon(s)
    </div>
	<div class="cb-product-list">
	<?php  
		foreach ($addonLineItems as $li) {
			if($li->entityType == "addon") {
		?>
      	<div class="row cb-product-item">
         	<div class="col-xs-8"> <?php echo $li->description?> 
				<?php if($li->quantity > 1) {
					echo " (" . $configData['currency_value'] . 
						number_format($li->unitAmount/100, 2, ".", "") . "x" . $li->quantity . ") ";
				 } ?>
			</div>
        	<div class="col-xs-4 text-right">
				<strong><?php echo $configData['currency_value'] . number_format($li->amount/100, 2, ".","") ?></strong>
			</div>
      	</div>
	<?php }
      } ?>
	</div>
	<?php } ?>
	<?php if(isset($invoiceEstimate) && $invoiceEstimate->creditsApplied != 0) {?>
		<hr class="clearfix">
		<div class="cb-product-list">
		 	<div class="row cb-product-item">
		   	 <div class="col-xs-8">Credits Applied</div>
		   		<div class="col-xs-4 text-right">
					<strong>- <?php echo $configData['currency_value'] . 
									number_format($invoiceEstimate->creditsApplied/100, 2, ".","") ?></strong>
				</div>
		 	  </div>
		</div>
	<?php } ?>				   
    <div class="cb-product-total">
        <hr class="clearfix">
        <div class="cb-product-list">
            <div class="row cb-product-item cb-product-grand-total">
                <div class="col-xs-8 col-sm-9">Total
                </div>
                <div class="col-xs-4 col-sm-3 text-right" data-cb="grand-total" id="grand-total" >
					<strong>
						<?php $amountDue = 0;
						      if(isset($invoiceEstimate) && isset($invoiceEstimate->total)){
								  $amountDue = $invoiceEstimate->amountDue; 
							  }
							  echo $configData['currency_value'] . number_format($amountDue/100, 2, ".","");
					    ?> 
					</strong>
				</div>
            </div>
        </div>
    </div>
</div>

<div class="text-center">
    <div class="alert alert-info">
        <div class="media text-left">
            <span class="glyphicon glyphicon-info-sign pull-left"></span>
            <div class="media-body" id="subscriptionMessage"> 
                <?php
				$currentTermEnd = $servicePortal->getSubscription()->currentTermEnd;
                if ($settingconfigData["subscription"]["immediately"] == 'true') {
					echo InfoNAlerts::subscriptionChangeImmediatelyInfoMsg($servicePortal, $estimate, $configData['currency_value']);
                } else {
                    echo InfoNAlerts::subscriptionChangeEndOfTermInfoMsg($servicePortal, $estimate, $configData['currency_value']);
                    ?>
				<?php } ?>
            </div>
        </div>
    </div>
</div>
<?php if($settingconfigData["changesubscription"]["addon"] == 'false') {?>
	<input type="hidden" name="plan-id" value="<?php echo $planId?>">
	<input type="hidden" name="plan-qty" value="<?php echo $planQuantity ?>">
<?php } ?>
<?php if($replaceAddonList) {?>
	<input type="hidden" name="replace_addon_list" value="true">
<?php } ?>
<hr class="clearfix">
<p class="cb-step-nav clearfix">
    <a data-prev-section="<?php echo $_GET["call-from"] ?>" data-current-section="section-review" 
			data-action="prev" class="cb-nav-prev">Prev</a>
</p>
<div class="clearfix">
    <input type="button" data-cb="review" class="btn btn-primary" value="Change Subscription">
    <a class="btn btn-link" href='<?php echo getCancelURL($configData) ?>'>Cancel</a>
</div>
