<?php 
include_once('init.php');
$curAddons = $servicePortal->getAddon();
$allAddons = $servicePortal->retrieveAllAddon(); 
$plan_id = isset($_POST['plan_id']) ? $_POST['plan_id'] : null;
$plan_quantity = isset($_POST['plan_quantity']) ? $_POST['plan_quantity'] : null;
$plan_price = isset($_POST['plan_price']) ? $_POST['plan_price'] : null;
$planResult = $servicePortal->retrievePlan($plan_id);
foreach ($curAddons as $curaddon) {
    $currentAddon[] = $curaddon->id;
}
?>
<div class="cb-product-body" data-cb="cb-product-body" data-cb-req-from="addon" >
    <div class="cb-product-box">
        <div class="cb-product-title">
            Your Selected Plan
        </div>
        <hr class="clearfix">
        <div class="cb-product-list">
            <div class="row cb-product-item">
                <div class="col-xs-8"  id="selectedPlan"><?php echo esc($planResult->plan()->invoiceName) ?> / Month ( <?php echo $configData['currency_value'] .' '. number_format($planResult->plan()->price / 100, 2, '.', '') ?>  x <?php echo $planResult->plan()->period ?> )</div>
                <div class="col-xs-4 text-right" id="selectedPrice"> <?php echo $configData['currency_value'] ?><strong><?php echo number_format($planResult->plan()->price / 100, 2, '.', '') ?> </strong></div>
            </div>
        </div>
    </div>
    <div class="page-header clearfix">
        <span class="h3">Add/Remove Addon(s)</span>
    </div>
    <p>You can pick one or more addons from the list below.</p>                   
    <div class="cb-available-list " id="changeAddon">
        <?php                  
        $showMessage = 0; 
        foreach ($allAddons as $addons) {
            if (($addons->addon()->status == "archived" && in_array($addons->addon()->id, $currentAddon)) || ($addons->addon()->status == "active" && $addons->addon()->chargeType == "recurring")) {
                if ($planResult->plan()->period == $addons->addon()->period && $planResult->plan()->periodUnit == $addons->addon()->periodUnit) {
                    $showMessage++;
                    ?>
                    <div class="cb-available-item cb-avail-has-qty" data-cb="cb-available-item">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="addons" 
										id="addons.id.<?php echo esc($addons->addon()->id) ?>" 
										value="<?php echo esc($addons->addon()->id) ?>" validate="true" 
										<?php echo (in_array($addons->addon()->id, $currentAddon)) ? "checked" : "" ?> >
                                		<?php echo esc($addons->addon()->name) ?> 
                                <input type="hidden" data-cb="cb-addon-index" name="cb-selected-addon" disabled="" 
								 	   	id="addonName_<?php echo esc($addons->addon()->id) ?>" 
										value="<?php echo esc($addons->addon()->name) ?>"> 
                            </label>

                            <div class="cb-available-pick">
                                <input type="hidden" id="addon_product_price_<?php echo esc($addons->addon()->id) ?>" 										name="addon_product_price" 
										value="<?php echo number_format($addons->addon()->price / 100, 2, '.', '') ?>"/>    
                                <?php if ($addons->addon()->type == "quantity") { ?>
                                    <span>Qty</span>
                                    <input type="number" validate="true" class="form-control"  
										id="addon_quantity_<?php echo esc($addons->addon()->id) ?>" 
										name="addon_quantity" data-cb="product-quantity-elem" min="1" value="1" 
                                    	onchange="quantityChangeaddon('<?php echo esc($addons->addon()->id) ?>')" >
                                <?php } ?>

                                <?php
                                if ($addons->addon()->price != 0) {
                                    ?>
                                    <strong id="addon_price_<?php echo esc($addons->addon()->id) ?>" class="cb-available-pick-price" >
										<?php echo $configData['currency_value'] .' '. number_format($addons->addon()->price / 100, 2, '.', '') ?>
									</strong>
                                <?php } else{ ?>
                                    <strong id="addon_price_<?php echo esc($addons->addon()->id) ?>" class="cb-available-pick-price">
									</strong>
                                <?php }
?>
                                <br>
                                <span id="addons[quantity][0].err" class="text-danger">&nbsp;</span>
                            </div>
                            <div class="clearfix"></div>
                            <?php if (isset($addons->addon()->description) ) { ?>
                            	<hr class="clearfix">
                            	<p class="help-block" style="display: block;"> <?php echo esc($addons->addon()->description) ?></p>
                            <?php } ?>
                        </div>
                    </div>
                    <?php
                }                    
            }   
        }
        if($showMessage == NULL){ ?>
            <input id="NoAddon" name="NoAddon" type="hidden" class="form-control" value="1" > 
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
        <?php }
        ?>
    </div>
    <input type="hidden" id="replace_addon_list" name="replace_addon_list" value="true">
    <hr class="clearfix">
    <p class="cb-step-nav clearfix">
        <a data-cb-prev-link="cb-nav-prev" class="cb-nav-prev" href="#" id="prev" onclick="backPlan()">Prev</a>
        <span class="hidden-xs">&emsp;|&emsp;</span>
        <a data-cb-next-link="cb-nav-next" class="cb-nav-next" href="#" id="next1" onclick="saveAddon()">Next</a>
    </p>
    <div class="clearfix">
        <input type="button" data-cb="addon" class="btn btn-default" value="Save and Continue" id="continueSubscription1" onclick="saveAddon()">
        <a class="btn btn-link" id="back1" href=<?php echo getCancelURL($configData) ?>>Cancel</a> 
    </div>
</div>
