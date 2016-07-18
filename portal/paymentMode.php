<?php   
$paymentModeMsg  = InfoNAlerts::paymentModeMsg($servicePortal);
?>
                
<div class="form-horizontal">
	<div class="text-center">
    	<div class="alert alert-info">
        	<div class="media text-left">
            	<span class="glyphicon glyphicon-info-sign pull-left"></span>
                	<div class="media-body">
                    	<?php echo $paymentModeMsg ?>
                    </div>
            </div>
        </div>
    </div>
</div>