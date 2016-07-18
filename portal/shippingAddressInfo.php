<?php if (!isset($subscription->shippingAddress)) { ?>
   <div class="text-center">
            <div class="alert alert-info">
                <div class="media text-left">
                    <span class="glyphicon glyphicon-info-sign pull-left"></span>
                    <div class="media-body">
                        <?php echo InfoNAlerts::shippingAddressNotPresetInfoMsg($servicePortal) ?>
                    </div>
                </div>
            </div>
	</div>
<?php } else { ?>               
    <address>
        <?php $shippingAddress = $subscription->shippingAddress ?>
        	<?php echo (isset($shippingAddress->firstName) ? esc($shippingAddress->firstName) : "" ) ?>
			<?php echo (isset($shippingAddress->lastName) ? esc($shippingAddress->lastName) . "<br>" : "") ?>
        	<?php echo (isset($shippingAddress->company) ? esc($shippingAddress->company) . "<br>" : "") ?>
        	<?php echo (isset($shippingAddress->line1) ? $shippingAddress->line1 . "<br>" : "") ?>
        	<?php echo (isset($shippingAddress->line2) ? $shippingAddress->line2 . "<br>" : "") ?>
        	<?php echo (isset($shippingAddress->city) ? $shippingAddress->city .  (isset($shippingAddress->zip) ? "-" .$shippingAddress->zip : "") . "<br>" : "") ?>
        	<?php echo (isset($shippingAddress->state) ? $shippingAddress->state . "<br>" : "") ?>
        	<?php $countryCodes = $servicePortal->getCountryCodes($configData); ?>
        	<?php echo (isset($shippingAddress->country) ? $countryCodes[$shippingAddress->country] . "<br>" : "" ) ?>
			<?php echo (isset($shippingAddress->zip) ? $shippingAddress->zip . "<br>" : "") ?>
    </address>
<?php } ?>