<?php 
if(isset($nonGroupPlans)){ ?>
    <h4> Your Available Plan(s)</h4>
<?php }else{
    $currIndex = 0;
}?>
<div class='cb-available-list cb-has-select'>
    <?php
    foreach ($planList as $key => $value) {
        $plan = $allPlansIndexed[$key];
        if ($plan->periodUnit == "week") {
            $planHeader = ($value == 1) ? "Weekly Plans" : $value . " Weekly Plan(s)";
        } elseif ($plan->periodUnit == "month" && $plan->period == 3) {
            $planHeader = "Quarterly Plans";
        } elseif ($plan->periodUnit == "month" && $plan->period == 6) {
            $planHeader = "Half Yearly Plans ";
        } elseif ($plan->periodUnit == "month") {
            $planHeader = ($value == 1) ? "Monthly Plans" : $value . " Monthly Plan(s)";
        } elseif ($plan->periodUnit == "year") {
            $planHeader = ($value == 1) ? "Yearly Plans" : $value . " Yearly Plan(s)";
        }

        if (isset($currIndex) && $currIndex != $value) {
            $currIndex = $value;
			?>
            <h4> <?php echo $planHeader ?></h4>
            <?php
        }

        if (($plan->status == "active") || ($plan->status == "archived")) { 
			?>
            <div class='cb-available-item cb-avail-has-qty' data-cb="cb-available-item">
                <div class='radio'>
                    <label>
                        <input type='radio' name='plan_id' 
							id='plan.id.<?php echo esc($plan->id) ?>' value="<?php echo esc($plan->id) ?>" validate="true" 
							<?php echo ($curPlan == $plan->id ) ? "checked" : "" ?> > 
							<?php echo esc($plan->name) ?>                          
                    </label>

                    <div class="cb-available-pick">
                        <?php if ($settingconfigData[changesubscription][planqty] == 'true' && $settingconfigData[changesubscription][allow] == 'true' && $plan->chargeModel == 'per_unit') { ?>
                            <span>Qty</span>
                            <input type="number" validate="true" class="form-control"  id="plan_quantity_<?php echo esc($plan->id) ?>"
										name="plan_quantity" data-cb="product-quantity-elem" min="1" 
                                   	 	value="<?php echo ($planId == $plan->id) ? $planQuantity : 1; ?>" 
                                   	 	onchange="quantityChange('<?php echo esc($plan->id) ?>')" 
										<?php echo ($planId != $plan->id ) ? "disabled" : "" ?> >
                               <?php } ?>

                        <input type="hidden" id="plan_price_<?php echo esc($plan->id) ?>" 
								name="plan_price" value="<?php echo number_format($plan->price / 100, 2, '.', '') ?>"/>
                        <?php $planqty = ($planId == $plan->id) ? $planQuantity : 1; 
                        	if (number_format($plan->price / 100, 2, '.', '') != 0.00) {
                         ?>
                            <strong id="product_price_<?php echo esc($plan->id) ?>" class="cb-available-pick-price">
								<?php echo $configData['currency_value'] . ' ' . number_format($plan->price * $planqty / 100, 2, '.', '') ?>			</strong>
                        <?php } ?>
                    </div>
                    <div class="clearfix"> </div>
                    <?php if (isset($plan->description)) { ?>
                        <hr class="clearfix">
                        <p class="help-block" style="display: block;"> <?php echo esc($plan->description) ?></p>
                    <?php } ?>
                </div>
            </div>
            <?php
        }
    }
    ?>
</div>