<?php   
if($customer->autoCollection == "on" && $customer->cardStatus == "no_card") {
     $paymentMessage = $infoconfigData['Payment_Mode']['Autocolletion_On_Nocard'];
} elseif ($customer->autoCollection == "on") {
	$paymentMessage = $infoconfigData['Payment_Mode']['Autocolletion_On'];
} elseif ($customer->autoCollection == "off") {
	$paymentMessage = $infoconfigData['Payment_Mode']['Offline_payment'];
}
?>
                
<div class="form-horizontal">
	<div class="text-center">
    	<div class="alert alert-info">
        	<div class="media text-left">
            	<span class="glyphicon glyphicon-info-sign pull-left"></span>
                	<div class="media-body">
                    	<?php echo $paymentMessage ?>
                    </div>
            </div>
        </div>
    </div>
</div>