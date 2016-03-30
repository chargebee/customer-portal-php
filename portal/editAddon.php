<?php 
include_once('init.php');
$subAddons = $servicePortal->getAddon();
$allAddons = $servicePortal->retrieveAllAddons(); 
$plan_id = isset($_POST['plan_id']) ? $_POST['plan_id'] : null;
$plan_quantity = isset($_POST['plan_quantity']) ? $_POST['plan_quantity'] : null;
$plan = $servicePortal->retrievePlan($plan_id);
?>
<div class="cb-product-body" data-cb="cb-product-body" data-cb-req-from="addon" >
    <div class="cb-product-box">
        <div class="cb-product-title">
            Your Selected Plan
        </div>
        <hr class="clearfix">
        <div class="cb-product-list">
            <div class="row cb-product-item">
                <div class="col-xs-8"  id="selectedPlan"><?php echo esc(isset($plan->invoiceName) ? $plan->invoiceName : $plan->name) ?> / Month ( <?php echo $configData['currency_value'] .' '. number_format($plan->price / 100, 2, '.', '') ?>  x <?php echo $plan->period ?> )</div>
                <div class="col-xs-4 text-right" id="selectedPrice"> <?php echo $configData['currency_value'] ?><strong><?php echo number_format($plan->price / 100, 2, '.', '') ?> </strong></div>
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
                if ($plan->period == $addons->addon()->period && $plan->periodUnit == $addons->addon()->periodUnit) {
                    $showMessage++;
                    ?>
					
					<?php
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
                                <input type="checkbox" name="addons" 
										id="addons.id.<?php echo esc($addons->addon()->id) ?>" 
										value="<?php echo esc($addons->addon()->id) ?>" validate="true"
										data-addon-id="<?php echo esc($addons->addon()->id) ?>"
										<?php echo ($currentAddon != null ? "checked" : "" ) ?>  onclick="onAddonClick(this)">
                                		<?php echo esc(isset($addons->addon()->invoiceName) ? $addons->addon()->invoiceName : $addons->addon()->name) ?> 
                                <input type="hidden" data-cb="cb-addon-index" name="cb-selected-addon" disabled="" 
								 	   	id="addonName_<?php echo esc($addons->addon()->id) ?>" 
										data-addon-name="<?php echo esc($addons->addon()->id) ?>"
										value="<?php echo esc(isset($addons->addon()->invoiceName) ? $addons->addon()->invoiceName : $addons->addon()->name) ?>"> 
                            </label>

                            <div class="cb-available-pick">
                                <input type="hidden" id="addon_product_price_<?php echo esc($addons->addon()->id) ?>" 										name="addon_product_price" data-addon-price=<?php echo esc($addons->addon()->id) ?>
										value="<?php echo number_format($addons->addon()->price / 100, 2, '.', '') ?>"/>    
                                <?php if ($addons->addon()->type == "quantity") { ?>
                                    <span>Qty</span>
                                    <input type="number" validate="true" class="form-control"  
										id="addon_quantity_<?php echo esc($addons->addon()->id) ?>" 
										data-addon-quantity="<?php echo esc($addons->addon()->id) ?>"
										name="addon_quantity" data-cb="product-quantity-elem" min="1" 
										value="<?php  echo ($currentAddon != null ? $currentAddon->quantity : "1" )?>" 
                                    	onchange="addonQuantityChange('<?php echo esc($addons->addon()->id) ?>')" 
										<?php echo ($currentAddon == null ? "disabled='true'": "") ?> >
                                <?php } ?>

                                <?php
                                if ($addons->addon()->price != 0) {
                                    ?>
                                    <strong id="addon_price_<?php echo esc($addons->addon()->id) ?>" class="cb-available-pick-price" >
										<?php echo $configData['currency_value']  ?>
										<span data-addon-total-price="<?php echo esc($addons->addon()->id) ?>"> 
											<?php echo number_format($addons->addon()->price / 100, 2, '.', '') ?> 
										</span>
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
