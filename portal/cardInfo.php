<?php if(isset($customer->paymentMethod) && $customer->paymentMethod->type == "paypal_express_checkout") {?>
<div class="form-horizontal">
	<div class="row">
		<div class="col-sm-6">
			<div class="row">
    			<label class="col-xs-5 control-label">Payment Method</label>
    			<div class="col-xs-7 form-control-static">
        			Paypal Express Checkout
    			</div>
			</div>

			<div class="row">
    			<label class="col-xs-5 control-label">Billing Agreement ID</label>
    			<div class="col-xs-7 form-control-static">
      			  <?php echo esc($customer->paymentMethod->referenceId) ?>
    		  </div>
		  </div>
		</div>
	</div>
</div>
<?php } else {?>

<?php
$card = $servicePortal->getCard();
$currentYear = date('Y');
$currentMonth = date('n');
if (!isset($customer->paymentMethod)) { 
	$cardInfoMsg = $infoconfigData['Payment_Related']['No_card_details'];
} else if( $currentYear > $card->expiryYear || ($card->expiryYear == $currentYear && $currentMonth  > $card->expiryMonth ) ) {
	$cardInfoMsg = $infoconfigData['Payment_Related']['Card_expired'];
} else if( $card->expiryYear == $currentYear && $card->expiryMonth == $currentMonth ) {
	$cardInfoMsg = $infoconfigData['Payment_Related']['Card_expiring'];
}
?>

<?php if (!is_null($cardInfoMsg)) { ?>
    <div class="form-horizontal">
        <div class="text-center">
            <div class="alert alert-info">
                <div class="media text-left">
                    <span class="glyphicon glyphicon-info-sign pull-left"></span>
                    <div class="media-body">
                        <?php echo $cardInfoMsg; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if(isset($customer->paymentMethod) && $customer->paymentMethod->type == "card") {?>
	<div class="form-horizontal">
		<div class="row">
    		<div class="col-sm-6">
        		<div class="row">
            		<label class="col-xs-5 control-label">Card type</label>
            		<div class="col-xs-7">
                		<div id="cb-cards">
                    		<span class="<?php echo $card->cardType ?>"></span>
                		</div>
            		</div>
        		</div>
        		<div class="row">
            		<label class="col-xs-5 control-label">Card Number</label>
            		<div class="col-xs-7 form-control-static">
                		**** **** **** <?php echo esc($card->last4) ?>
            		</div>
        		</div>
        		<div class="row">
            		<label class="col-xs-5 control-label">Expiry</label>
            		<div class="col-xs-7 form-control-static">
                		<?php echo esc($card->expiryMonth) . "/" . esc($card->expiryYear) ?>
            		</div>
        		</div>
    		</div>
    		<?php if(isset($card->firstName)) { ?></li>
    		<div class="col-sm-6">
        		<div class="row">
            		<label class="col-xs-5 control-label">Name</label>
            		<div class="col-xs-7 form-control-static">
                		<address><?php echo esc($card->firstName) . " " . esc($card->lastName) ?><br></address>
           	 		</div>
        		</div>
    		</div>
    		<?php } ?>
		</div>
	</div>
<?php }
} ?>	
