<?php if (!isset($customer->billingAddress)) { ?>
    <div class="form-horizontal">
    	<div class="text-center">
        	<div class="alert alert-info">
            	<div class="media text-left">
                        <span class="glyphicon glyphicon-info-sign pull-left"></span>
                        <div class="media-body">
                            <?php echo  InfoNAlerts::billingAddressInfoMsg($servicePortal) ?>
                        </div>
                 </div>
               </div>
         </div>
     </div>
    <?php } else { ?>
      <address>
			<?php $billingAddress = $customer->billingAddress ?>
		  	<?php echo (isset($billingAddress->firstName) ? esc($billingAddress->firstName) : "" )?>
		  	<?php echo (isset($billingAddress->lastName) ? esc($billingAddress->lastName) : "" )?>
		  	<?php echo (isset($billingAddress->lastName) || isset($billingAddress->firstName) ? "<br>" : "") ?>
		  	<?php echo (isset($billingAddress->line1) ? esc($billingAddress->line1) . "<br>" : "") ?>
		  	<?php echo (isset($billingAddress->line2) ? esc($billingAddress->line2) . "<br>" : "") ?>
		  	<?php echo (isset($billingAddress->city) ? esc($billingAddress->city) . "<br>" : "") ?>
		  	<?php echo (isset($billingAddress->state) ? esc($billingAddress->state) . "<br>" : "") ?>
        	<?php $countryCodes = $servicePortal->getCountryCodes($configData); ?>
        	<?php echo (isset($billingAddress->country) ? esc($countryCodes[$billingAddress->country]) . "<br>" : "" ) ?>
			<?php echo (isset($billingAddress->zip) ? esc($billingAddress->zip) . "<br>" : "") ?>
    	</address>
<?php } ?>
