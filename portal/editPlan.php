<?php
$allPlansIndexed = array();
foreach ($allPlans as $plan) {
    if ($plan->plan()->id == $curPlan) {
		// Storing the current plan result.
        $planResult = $plan->plan();
    }
	if($plan->plan()->status == "archived" && $plan->plan()->id != $curPlan){
		// if the plan is archived and if it is not the currently subscribed plan then skip showing it.
		continue;
	}
    $allPlansIndexed[$plan->plan()->id] = $plan->plan();
    if ($settingconfigData["changesubscription"]["groupplan"] == 'false') {
        $nonGroupPlans[$plan->plan()->id] = $plan->plan()->period;
    } elseif ($plan->plan()->periodUnit == "week") {
        $weeklyPlans[$plan->plan()->id] = $plan->plan()->period;
        asort($weeklyPlans);
    } elseif ($plan->plan()->periodUnit == "month") {
        if ($plan->plan()->period == 6) {
            $halfYearlyPlans[$plan->plan()->id] = $plan->plan()->period;
        } elseif ($plan->plan()->period == 3) {
            $quarterYearlyPlans[$plan->plan()->id] = $plan->plan()->period;
        } else {
            $monthlyPlans[$plan->plan()->id] = $plan->plan()->period;
            asort($monthlyPlans);
        }
    } elseif ($plan->plan()->periodUnit == "year") {
        $yearlyPlans[$plan->plan()->id] = $plan->plan()->period;
        asort($yearlyPlans);
    }
}
?>

  <div class="cb-product-box">
    <div class="cb-product-title">
      Your Current Plan
    </div>
    <hr class="clearfix">
    <div class="cb-product-list">
      <div class="row cb-product-item">
        <div class="col-xs-8">
			<?php 
			echo isset($planResult->invoiceName) ? esc($planResult->invoiceName) : esc($planResult->name) . 
						' / ' . esc($planResult->periodUnit);  
			if($planResult->chargeModel == "per_unit" && $planResult->price != 0) {
                echo ' (' . $configData['currency_value'] . 
					esc(number_format($planResult->price / 100, 2, '.', '') . ' x '. $planQuantity) . ')';
		   	} 
			?>
		</div>
        <div class="col-xs-4 text-right">
			<strong><?php echo $configData['currency_value'] . 
							number_format($planResult->price * $curPlanQty/100, 2, '.', '');?></strong>
		</div>
      </div>
    </div>
  </div>
  <?php if($addonChange && sizeof($curAddons) != 0) { ?>
  <div class="cb-product-box">
    <div class="cb-product-title">
      Your Current Addon(s)
    </div>
    <hr class="clearfix">
    <div class="cb-product-list">
		<?php
		foreach ($curAddons as $curAddon) {  
		    foreach ($allAddons as $addon) {
			  if ($curAddon->id == $addon->addon()->id && 
			  			$addon->addon()->chargeType == 'recurring') { ?>
      			<div class="row cb-product-item">
        			<div class="col-xs-8">
						<?php echo esc(isset($addon->addon()->invoiceName) ? 
							$addon->addon()->invoiceName: $addon->addon()->name) . ' (' . $configData['currency_value'] .
							 esc(number_format($addon->addon()->price)/100, 2, '.', '') . ' x ' .
								 	 esc($curAddon->quantity) . ')' ?>
					</div>
        			<div class="col-xs-4 text-right">
						<strong>
							<?php echo $configData['currency_value'] . 
										esc(number_format($addon->addon()->price * $curAddon->quantity/100, 2, '.', '')) ?>
						</strong>
					</div>
      		  	</div>
	  <?php }
         }
        } ?>
    </div>
  </div>
  <?php } ?>
  <div class="page-header clearfix">
    <span class="h3">Change your plan</span>
  </div>
  <p>You can choose from one of the available plans below.</p> 
  
  <?php
  if (isset($nonGroupPlans)) {   
      $planList = $nonGroupPlans;
      include("renderPlans.php");
  }
  
  if (isset($weeklyPlans)) {
      $planList = $weeklyPlans;
      include "renderPlans.php";
  }
  
  if (isset($monthlyPlans)) {
      $planList = $monthlyPlans;
      include "renderPlans.php";
  }
  
  if (isset($quarterYearlyPlans)) {
      $planList = $quarterYearlyPlans;
      include "renderPlans.php";
  }
  
  if (isset($halfYearlyPlans)) {
      $planList = $halfYearlyPlans;
      include "renderPlans.php";
  }
  
  if (isset($yearlyPlans)) {
      $planList = $yearlyPlans;
      include "renderPlans.php";
  }
  ?>
                    
  <span id="plan_id.err" class="text-danger">&nbsp;</span>
  <span id="plan_quantity.err" class="text-danger">&nbsp;</span>
  <hr class="clearfix">
  <p class="cb-step-nav clearfix">
    <a data-current-section="section-plan" 
			data-next-section="<?php echo $addonChange ? "section-addons" : "section-review" ?>"
			data-url="<?php echo $addonChange ? "editAddon.php?call-from=section-plan" : "reviewChangeSub.php?call-from=section-plan"?>" 
			class="cb-nav-next skip-plan">Next
		</a>
  </p>
  <div class="clearfix">
    <input type='button' data-cb="save-plan" data-current-section="section-plan" 
		data-next-section="<?php echo $addonChange ? "section-addons" : "section-review" ?>"
		data-url="<?php echo $addonChange ? "editAddon.php?call-from=section-plan" : "reviewChangeSub.php?call-from=section-plan"?>" 
		class='btn btn-default' value="Save and Continue">
    <a href="<?php echo getCancelURL($configData) ?>" class="btn btn-link">cancel</a>
  </div>