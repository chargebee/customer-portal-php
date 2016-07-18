<?php
$planChangeNotAllowed = isset($planChange);
if(!$planChangeNotAllowed) {
	include_once('init.php');
}
	
$subAddons = $servicePortal->getAddon();
$allAddons = $servicePortal->retrieveAllAddons(); 
$planId = isset($_GET['plan_id']) ? $_GET['plan_id'] : $servicePortal->getSubscription()->planId;
$planQuantity = isset($_GET['plan_quantity']) ? $_GET['plan_quantity'] : $servicePortal->getSubscription()->planQuantity;
$plan = $servicePortal->retrievePlan($planId);
	
?>

    <div class="cb-product-body" data-cb="cb-product-body" data-cb-req-from="addon" style="display: block;">
      <div class="cb-product-box">
          <div class="cb-product-title">
              Your Selected Plan
          </div>
          <hr class="clearfix">
          <div class="cb-product-list">
              <div class="row cb-product-item">
                  <div class="col-xs-8"  id="selectedPlan">
  					<?php echo esc(isset($plan->invoiceName) ? $plan->invoiceName : $plan->name) . 
					      ' / ' . esc($plan->periodUnit) . ' ( ' . $configData['currency_value'] .
							 number_format($plan->price/100, 2, '.', '') . ' x ' . $planQuantity . ' )' ?>
  				</div>
                  <div class="col-xs-4 text-right" id="selectedPrice"> 
  					<strong>
  						<?php echo $configData['currency_value'] . number_format($plan->price * $planQuantity / 100, 2, '.', '') ?> 
  					</strong>
  				</div>
              </div>
          </div>
      </div>
      <div class="page-header clearfix">
        <span class="h3">Add/Remove Addons</span>
      </div>
      <p>You can pick one or more addons from the list below.</p>
      <div class="cb-available-list">
		<?php
		$totalAddons = 0;
        foreach ($allAddons as $addons) {
            if (($addons->addon()->status == "archived" && $servicePortal->isCurrentSubscriptionAddon($addons->addon())) || 
					($addons->addon()->status == "active" && $addons->addon()->chargeType == "recurring")) {

                if ($servicePortal->addonIsApplicableToPlan($plan, $addons->addon())) {
                    $totalAddons++;
					$currentAddon = null; 
				 	foreach($subAddons as $a) {
						  if($addons->addon()->id == $a->id){
						 	$currentAddon = $a;
							break;
						  }
				 	} 
		?>
        <div class="cb-available-item cb-avail-has-qty" data-cb="cb-available-item">
          <div class="checkbox">
            <label>
              <input type="checkbox" name="addons" value="<?php echo esc($addons->addon()->id) ?>" 
			  	data-addon-id="<?php echo esc($addons->addon()->id) ?>"  <?php echo ($currentAddon != null ? "checked" : "" ) ?> >
				   <?php echo esc(isset($addons->addon()->invoiceName) ? $addons->addon()->invoiceName : $addons->addon()->name) ?> 
				<input type="hidden" data-addon-price="<?php echo esc($addons->addon()->id) ?>" 
				         value=<?php echo number_format($addons->addon()->price/100, 2,'.','') ?> >    
            </label>
            <div class="cb-available-pick">
			<?php if ($addons->addon()->type == "quantity") { ?>
              <span>Qty</span>
              <input validate="true" type="number" class="form-control" data-addon-quantity="<?php echo esc($addons->addon()->id) ?>"
			      value="<?php  echo ($currentAddon != null ? $currentAddon->quantity : "1" )?>"  
				  data-cb="addon-quantity-select" min="1" 
				  <?php echo ($currentAddon == null ? "	disabled='true'": "") ?> >
			<?php } ?>  
			
			<?php if ($addons->addon()->price != 0) {?>
              <strong id="product_price" class="cb-available-pick-price">
				  <?php echo $configData['currency_value']  ?>
				  <span data-addon-total-price="<?php echo esc($addons->addon()->id) ?>">
				  	<?php
					    $_addonQty = 1;
					    if($currentAddon != null) {
							$_addonQty = $currentAddon->quantity;
						} 
						echo number_format($addons->addon()->price * $_addonQty / 100, 2, '.', '') 
					?> 
			  	  </span>
			  </strong>
			<?php } ?>
              <br>
            </div>
            <div class="clearfix"></div>
			<?php if(isset($addons->addon()->description)) {?>
			    <hr class="clearfix">
            	<p class="help-block">
					<?php echo $addons->addon()->description?>
				</p>
			<?php } ?>
          </div>
        </div>
		<?php }
		  }
		} ?>
      </div>
	  <?php if($totalAddons == 0) {?>
          <div class="cb-available-list ">
              <div class="text-center">
                  <div class="alert alert-warning">
                      <div class="media text-left">
                          <span class="glyphicon glyphicon-exclamation-sign pull-left"></span>
                          <div class="media-body">
                              You do not have any addons for the seleced plan.
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      <?php } ?>
	<input type="hidden" name="plan-id" value="<?php echo $planId?>">
	<input type="hidden" name="plan-qty" value="<?php echo $planQuantity ?>">
      <hr class="clearfix">
      <p class="cb-step-nav clearfix">
		<?php if(!$planChangeNotAllowed) { ?>  
        <a data-prev-section="<?php echo $_GET["call-from"] ?>" data-current-section="section-addons" data-action="prev" class="cb-nav-prev">Prev</a>
		        <span class="hidden-xs">&emsp;|&emsp;</span>
		<?php } ?>
        <a data-next-section="section-review" data-current-section="section-addons" data-url="reviewChangeSub.php?call-from=section-addons"
			class="cb-nav-next skip-addons">Next</a>
      </p>
      <div class="clearfix">
        <input type="button" data-url="reviewChangeSub.php?call-from=section-addons" 
				data-current-section="section-addons" data-next-section="section-review" 
				data-cb="save-addon" class="btn btn-default" value="Save and Continue">
        <a href="<?php echo getCancelURL($configData) ?>" class="btn btn-link">cancel</a>
      </div>
    </div>