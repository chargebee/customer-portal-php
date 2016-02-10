<?php
include("header.php");
$curAddons = $servicePortal->getAddon();
$curPlan = $servicePortal->getSubscription()->planId;
$allPlans = $servicePortal->retrieveAllPlan(); 
$allAddons = $servicePortal->retrieveAllAddon(); 
$planQuantity = $servicePortal->getSubscription()->planQuantity;
$planId = $servicePortal->getSubscription()->planId;
$currentTermEnd = $servicePortal->getSubscription()->currentTermEnd;

$allPlansIndexed = array();
foreach ($allPlans as $plan) {
    if ($plan->plan()->id == $servicePortal->getSubscription()->planId) {
        $planResult = $plan->plan();
    }
	if($plan->plan()->status == "archived" && $plan->plan()-> id != $servicePortal->getSubscription()->planId){
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
$total = ($planResult->price * $servicePortal->getSubscription()->planQuantity);
if (sizeof($allPlans) == 1 && $settingconfigData["changesubscription"]["planqty"] == 'false') {
    ?>
    <input id="onePlan" name="onePlan" type="hidden" class="form-control" value="1" > 
<?php } ?>

<div class="container" >
    <div id="cb-wrapper-ssp">
		<?php include("processing.php") ?>
        <div id="cb-user-content">
            <form id="form" method="post">
                <div class="cb-well">
                    <div class="cb-product">
                        <div class="cb-product-header">
                            <div class="cb-product-steps" data-cb="cb-product-steps">
                                <div class="cb-product-step current" data-cb-step-for="plan" data-cb-current-step='current' id="step-plan">
                                    Change your plan
                                </div>
                                <?php if ( $servicePortal->addonAccessible($allAddons, $settingconfigData )) { ?>
                                    <div class="cb-product-step future" data-cb-step-for="addon" data-cb-current-step='' id="step-addon">
                                        Add/Remove Addon(s)
                                    </div>
                                <?php } else {
                                    ?>                                    
                                    <input id="NoAddon" name="NoAddon" type="hidden" class="form-control" value="1" > 
                                <?php } ?>
                                <div class="cb-product-step future" data-cb-step-for="review" data-cb-current-step='' id="step-review">
                                    Review and Confirm
                                </div>
                            </div>
                        </div>
                        <div class="cb-product-body" data-cb="cb-product-body" data-cb-req-from="plan" id="changeYourPlan">
                            <div class="cb-product-box" id="subscriptionForm">
                                <div class="cb-product-title">
                                    Your Current Plan
                                </div>
                                <hr class="clearfix">
                                <div class="cb-product-list">
                                    <?php
                                    if (number_format($planResult->price / 100, 2, '.', '') == 0.00) {
                                        ?>
                                        <div class="row cb-product-item">
                                            <div class="col-xs-8"><?php echo esc($planResult->name) ?></div>
                                            <div class="col-xs-4 text-right"></div>
                                        </div>
                                    <?php
                                    } else {
                                        ?>
                                        <div class="row cb-product-item">
                                            <div class="col-xs-8"><?php echo esc($planResult->name) ?> ( <?php echo $configData['currency_value'] . ' ' . number_format($planResult->price / 100, 2, '.', '') ?> x <?php echo esc($planQuantity) ?> ) </div>
                                            <div class="col-xs-4 text-right">
												<strong><?php echo $configData['currency_value'] . ' ' . number_format($total / 100, 2, '.', '') ?>
												</strong></div>
                                        </div>
                                    <?php } ?>

                                </div>
                            </div>

                            <?php if (isset($curAddons)) { ?>
                                <div class="cb-product-box">
                                    <div class="cb-product-title">
                                        Your Current Addon(s)
                                    </div>
                                    <hr class="clearfix">
                                    <?php
                                    foreach ($curAddons as $curaddon) {
                                        foreach ($allAddons as $addons) {
                                            if ($curaddon->id == $addons->addon()->id) {
                                                ?>
                                                <div class="cb-product-list">
                                                    <?php
                                                    if (($planResult->period != $addons->addon()->period) && ($planResult->periodUnit != $addons->addon()->periodUnit)) {
                                                        ?>
                                                        <input id="NoAddon" name="NoAddon" type="hidden" class="form-control" value="1" > 
                                                    <?php }
                                                    ?>
                                                    <div class="row cb-product-item">
                                                        <div class="col-xs-8"><?php echo esc($addons->addon()->name) ?>   ( <?php echo $configData['currency_value'] . number_format($addons->addon()->price / 100, 2, '.', '') ?> x <?php echo $addons->addon()->period ?> )</div>
                                                        <div class="col-xs-4 text-right">
															<strong><?php echo $configData['currency_value'] . number_format($addons->addon()->price / 100, 2, '.', '') ?>
															</strong></div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
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
                                <a data-cb-next-link="cb-nav-next" class="cb-nav-next" href="#" id="next" onclick="savePlan()">Next</a>
                            </p>
                            <div class="clearfix">
                                <input type='button' data-cb="plan" class='btn btn-default' value="Save and Continue" id="continueSubscription" onclick="savePlan()">         
                                <a class="btn btn-link" id="back" href=<?php echo getCancelURL($configData) ?>>Cancel</a>   
                            </div>
                        </div>

                        <div id="addons" >
                        </div>

                        <div class="cb-product-body" data-cb="cb-product-body" data-cb-req-from="review" id="review" style="display: none;" >
                            <div class="page-header clearfix">
                                <span class="h3">Review and Confirm</span>
                            </div>
                            <p>Review your order and make sure you've selected the right products before confirming it. You can go back and make changes if needed.</p>
                            <div class="cb-product-box">
                                <div class="cb-product-title">
                                    Selected Plan
                                </div>
                                <div class="cb-product-list">
                                    <div class="row cb-product-item">
                                        <div class="col-xs-8" id="selectedPlanReview">Basic (£9.00 x 1)</div>
                                        <div class="col-xs-4 text-right" id="selectedPlanAmount"><strong>£9.00</strong></div>
                                    </div>
                                </div>
                                <div class="cb-product-title" id="ifAddonSelected" style="display: none;">
                                    Selected Addon(s)
                                </div>
                                <div class="cb-product-list" id="selectedAddon">
                                </div>
                                <div class="cb-product-total">
                                    <hr class="clearfix">
                                    <div class="cb-product-list">
                                        <div class="row cb-product-item cb-product-grand-total">
                                            <div class="col-xs-8 col-sm-9">Total
                                            </div>
                                            <div class="col-xs-4 col-sm-3 text-right" data-cb="grand-total" id="grand-total" ><strong>£214.00</strong></div>
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
                                            if ($settingconfigData[subscription][immediately] == 'false') {
                                                $phrase = $infoconfigData['Messages_during_change_subscription']['Change_at_end_of_term'];
                                                $default = array('$subscription.current_term_end', '$estimated_invoice.amount');
                                                $assign = array(date('d-M-y', $currentTermEnd), '');
                                                $subscriptionMessage = str_replace($default, $assign, $phrase);
                                                ?>
                                                <input type="hidden" value="true" name="end_of_term" id="end_of_term" /> 

                                            <?php
                                            } else {
                                                $phrase = $infoconfigData['Messages_during_change_subscription']['Change_immediately'];
                                                $default = array('$subscription.current_term_end', '$estimated_invoice.amount');
                                                $assign = array(date('d-M-y', $currentTermEnd), '');
                                                $subscriptionMessage = str_replace($default, $assign, $phrase);
                                                ?>
                                                <input type="hidden" value="false" name="end_of_term" id="end_of_term" />  
<?php }
?>
                                        </div>
                                        <input type="hidden" value="<?php echo $subscriptionMessage ?>" name="submessge" id="submessge" /> 
                                    </div>
                                </div>
                            </div>
                            <hr class="clearfix">
                            <p class="cb-step-nav clearfix">
                                <a data-cb-prev-link="cb-nav-prev" class="cb-nav-prev" href="#" id="prev1" onclick="backAddon()">Prev</a>
                            </p>
                            <div class="clearfix">
                                <input type="button" data-cb="review" class="btn btn-primary" value="Change Subscription" id="changeSubscription">
                                <a class="btn btn-link" id="back2" href=<?php echo getCancelURL($configData) ?>>Cancel</a>
                            </div>
                        </div>



                        <div class="cb-product-body" data-cb="cb-product-body" data-cb-req-from="addon" style='display:none'></div>
                        <div class="cb-product-body" data-cb="cb-product-body" data-cb-req-from="review"></div>
                    </div>
                    <input type="hidden" id="data-cb-req-from" name="data-cb-req-from" value="" />
                    <input type="hidden" id="sub_id" name="sub_id" value="2102635" />
                    <input type="hidden" id="total_selected_addons" name="total_selected_addons" value="" />
                </div>
            </form>
        </div>
    </div>
</div>
<?php include("footer.php"); ?>
<script>
    var addonIdList = [];
    var addonQuantityList = [];
    var currencyValue = "<?php echo $configData['currency_value'] ?>";
    $(function () {
        var onePlan = $("#onePlan").val();
        if (onePlan == 1) {
            $('#step-plan').toggleClass('cb-product-step current cb-product-step past');
            $('#step-addon').toggleClass('cb-product-step future cb-product-step current');
            $('#changeYourPlan, #next').hide();
            $('#addons, #next1, #prev').show();
            saveAddon(1);
            return;
        }
    });
    function savePlan() {
        var radioValue = $('input:radio[name=plan_id]:checked').val();
        if (radioValue) {
            $("#selectedPlanId").val(radioValue);
            var price = $("#product_price_" + radioValue).text();
            var planPrice = $("#plan_price_" + radioValue).val();
            var quantity = $("#plan_quantity_" + radioValue).val();
            if ($("#plan_quantity_" + radioValue).length > 0) {
                var replaceHtml = toTitleCase(radioValue) + ' / ' + 'Month ' + '(' + currencyValue + ' ' + planPrice + ' x ' + quantity + ')';
            } else{
                var replaceHtml = toTitleCase(radioValue);
            }
            var data = {plan_id: radioValue, plan_quantity: quantity, plan_price: planPrice, perunit_price: price};
            var NoAddon = $("#NoAddon").val();
            if (NoAddon == 1) {
                saveAddon(1);
                return;
            }
            $(".cb-alert-flash, .loader").show();
            $.ajax({
                type: 'POST',
                url: 'editAddon.php',
                data: data,
                contentType: "application/x-www-form-urlencoded;charset=utf-8",
                success: function (result) {
                    $(".cb-alert-flash, .loader").hide();
                    $('#addons').html(result);
                    $('#step-plan').toggleClass('cb-product-step current cb-product-step past');
                    $('#step-addon').toggleClass('cb-product-step future cb-product-step current');
                    $('#changeYourPlan, #next').hide();
                    $('#addons, #next1, #prev').show();
                    $('#cb-wrapper-ssp').css('min-height', '620px');
                    $('#selectedPlan').html(replaceHtml);
                    $('#selectedPrice').html(price);
                }
            });
        }


    }
    function saveAddon(id) {
        if (id == 1) {
            $('#step-plan').toggleClass('cb-product-step current cb-product-step past');
            $('#step-review').toggleClass('cb-product-step future cb-product-step current');
            $('#changeYourPlan, #next').hide();
        } else {
            $('#step-addon').toggleClass('cb-product-step current cb-product-step past');
            $('#step-review').toggleClass('cb-product-step future cb-product-step current');
        }
        $('#addons, #prev, #next1, #next').hide();
        $('#prev1, #review').show();
        $('#cb-wrapper-ssp').css('min-height', '620px');

        var addonRadioValue = $('input[name=addons]:checked').val();
        var planRadioValue = $('input:radio[name=plan_id]:checked').val();
        var planPrice = $("#product_price_" + planRadioValue).text();
        var planActualPrice = $("#plan_price_" + planRadioValue).val();
        var planQuantity = $("#plan_quantity_" + planRadioValue).val();

        var addonActualPrice = $("#addon_price_" + addonRadioValue).val();
        var addonQuantity = $("#addon_quantity_" + addonRadioValue).val();

        var submessge = $("#submessge").val();

        if (planActualPrice == '0.00') {
            var planReplaceHtml = toTitleCase(planRadioValue);
        } else {
            if (typeof planQuantity === 'undefined') {
                var planReplaceHtml = toTitleCase(planRadioValue) + ' / ' + 'Month ' + '(' + planActualPrice + ')';
            } else {
                var planReplaceHtml = toTitleCase(planRadioValue) + ' / ' + 'Month ' + '(' + currencyValue + ' ' + planActualPrice + ' x ' + planQuantity + ')';
            }
        }
        var finalTotal = planPrice;
        if (addonRadioValue) {
            var addonPrice = '';
            var addonName = '';
            var addonReplaceHtml = '';
            var emptytext = '';
            var addonPricetot = 0;
            var container = $("#selectedAddon");
            $('#selectedAddon').html(emptytext);
            $('#selectedAddonAmount').html(emptytext);
            $("input[name=addons]:checked").each(function () {
                var addonRadioValue = $(this).val();
                addonPrice = $("#addon_price_" + addonRadioValue).text();
                var addonProductPrice = $("#addon_product_price_" + addonRadioValue).val();
                var addonQty = $("#addon_quantity_" + addonRadioValue).val();
                addonName = $("#addonName_" + addonRadioValue).val();
                addonIdList.push(addonRadioValue);
                addonQuantityList.push(addonQty);
                if (typeof addonQty === 'undefined') {
                    addonReplaceHtml = addonName + ' - ' + '(' + addonProductPrice + '  )';
                } else {
                    addonReplaceHtml = addonName + ' - ' + '(' + currencyValue + ' ' + addonProductPrice + ' x ' + addonQty + '  )';
                }
                container.append('<div class="row cb-product-item"> <div class="col-xs-8">'+addonReplaceHtml+'</div> <div class="col-xs-4 text-right">'+addonPrice+'</div></div>');
                var planSplit = addonPrice.split(" ");
                addonPricetot = parseFloat(addonPricetot) + parseFloat(planSplit[1]);
            });
            $('#ifAddonSelected').show();
            $('#selectedPlanReview').html(planReplaceHtml);
            $('#selectedPlanAmount').html(planPrice);
            var planSplit = planPrice.split(" ");
            var addonSplit = addonPrice.split(" ");
            var grandTotal = +planSplit[1] + +addonPricetot;
            var grandTotal = grandTotal.toFixed(2);
            var space = planSplit[0].concat(' ');
            finalTotal = space.concat(grandTotal);
            finalsubmessage = submessge + finalTotal + '.';
            $("#grand-total, #grandTotal-body").html(finalTotal);
            $("#subscriptionMessage").html(finalsubmessage);
        }
        if (planRadioValue) {
            $('#selectedPlanReview').html(planReplaceHtml);
            $('#selectedPlanAmount').html(planPrice);
            $("#grand-total, #grandTotal-body").html(finalTotal);
            finalsubmessage = submessge + finalTotal + '.';
            $("#subscriptionMessage").html(finalsubmessage);
        }
    }
    function backPlan(id) {
        if (id == 1) {
            $('#step-addon').toggleClass('cb-product-step past cb-product-step current');
            $('#step-review').toggleClass('cb-product-step current cb-product-step future');
            $('#review, #prev1').hide();
            $('#addons, #next1, #prev').show();
        }
        $('#step-addon').toggleClass('cb-product-step current cb-product-step future');
        $('#step-plan').toggleClass('cb-product-step past cb-product-step current');
        $('#addons, #prev, #next1, #next').hide();
        $('#changeYourPlan, #next').show();
    }
    function backAddon() {
        var NoAddon = $("#NoAddon").val();
        if (NoAddon == 1) {
            backPlan(1);
            return;
        }
        $('#step-addon').toggleClass('cb-product-step past cb-product-step current');
        $('#step-review').toggleClass('cb-product-step current cb-product-step future');
        $('#review, #prev1').hide();
        $('#addons, #next1, #prev').show();
        $('#cb-wrapper-ssp').css('min-height', '620px');
    }
	/*
	 * On selecting the plan enables the plan quantity for the user to change the quantity.
	 */
    $("input[name=plan_id]:radio").change(function () {
        var selectedId = $("input[name=plan_id]:radio:checked").attr('id');
        $('#' + selectedId).attr("checked");
        var res = selectedId.split("plan.id.");
        $('input[name=plan_quantity]').attr('disabled', true);
        $("#plan_quantity_" + res[1]).removeAttr('disabled');
    });


    function quantityChange(planId) {
        var quantity = $("#plan_price_" + planId).val();
        var price = $("#product_price_" + planId).text();
        var dropqty = $("#plan_quantity_" + planId).val();
        var res = price.split(" ");
        var tot = quantity * dropqty;
        var tot = tot.toFixed(2);
        var space = res[0].concat(' ');
        var final = space.concat(tot);
        $("#product_price_" + planId).text(final);
    }

    function quantityChangeaddon(addonId) {
        var productprice = $("#addon_product_price_" + addonId).val();
        var price = $("#addon_price_" + addonId).text();
        var dropqty = $("#addon_quantity_" + addonId).val();
        var res = price.split(" ");
        var tot = productprice * dropqty;
        var tot = tot.toFixed(2);
        var space = res[0].concat(' ');
        var final = space.concat(tot);
        $("#addon_price_" + addonId).text(final);
    }

    function toTitleCase(str) {
        return str.replace(/(?:^|\s)\w/g, function (match) {
            return match.toUpperCase();
        });
    }
    $('#changeSubscription').click(function () {
        var addonRadioValue = $('input[name=addons]:checked').val();
        var planRadioValue = $('input[name=plan_id]:checked').val();
        var addonQuantity = 1;
        if ($("#addon_quantity_" + addonRadioValue).length > 0) {
            addonQuantity = $("#addon_quantity_" + addonRadioValue).val();
        } else
        {
            addonQuantity = 1;
        }

        if ($("#plan_quantity_" + planRadioValue).length > 0) {
            var planQuantity = $("#plan_quantity_" + planRadioValue).val();
        } else
        {
            var planQuantity = 1;
        }
        var planRadioValue = $('input:radio[name=plan_id]:checked').val();
        var subscriptionId = $("#subscriptionId").val();
        var endOfTerm = $("#end_of_term").val();
        var addons = {},
            i;

        for (i = 0; i < addonIdList.length; i++) {
            var addonId = addonIdList[i];
            var addonQuantity = addonQuantityList[i];
            addons[i] = {id:addonId, quantity:addonQuantity};
        }
        var params = {action : "updateSubscription", planId: planRadioValue, planQuantity: planQuantity, addons: addons, endOfTerm: endOfTerm};
        AjaxCallMessage('api.php', 'POST', 'json', $.param(params), 'editsubscription');
    });
</script>
