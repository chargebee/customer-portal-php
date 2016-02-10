<?php
include_once('header.php');
$subscription = $servicePortal->getSubscription();
$termEndDate = date('d-M-y', $subscription->currentTermEnd);
?>

<div class="container" style="height:674px">
    <div id="cb-wrapper-ssp">
		<?php include("processing.php") ?>
        <div id="cb-user-content">
            <form id="cancelForm" method="POST">                
                <input id="cancelLaterText" name="cancelLaterText" type="hidden" class="form-control" value="<?php echo str_replace('$subscription.current_term_end', $termEndDate, $infoconfigData['Warnings_during_Cancellation']['Cancel_on_end_of_term_active']) ?>" > 
                <input id="cancelImmediateText" name="cancelImmediateText" type="hidden" class="form-control" value="<?php echo $infoconfigData['Warnings_during_Cancellation']['Cancel_immediately'] ?>" > 
                <div class="cb-well">
                    <h3 class="text-center">Cancel Subscription</h3>                    
                    <?php 
                    $cancelImmediateMessage = $infoconfigData['Warnings_during_Cancellation']['Cancel_immediately'];
                    if($subscription->status == 'in_trial'){
                        $cancelEndOfTermMessage = str_replace('$subscription.trial_end', $subscription->trialEnd, $infoconfigData['Warnings_during_Cancellation']['Cancel_on_end_of_term_trial']);
                    }
                    $cancelEndOfTermMessage = str_replace('$subscription.current_term_end', $termEndDate, $infoconfigData['Warnings_during_Cancellation']['Cancel_on_end_of_term_active']);
                    if (($settingconfigData["cancelsubscription"]["immediately"] == 'true' && $subscription->status == "non_renewing")
                            || ($settingconfigData["cancelsubscription"]["immediately"] == 'true' && $subscription->status == 'future')) {
                       ?>
                        
                            <input type="hidden" id="endOfTerm" value="true" name="endOfTerm" >
                            <div id="cancel-immediately-info" class="alert alert-warning" >
                                <div class="media text-left">
                                    <span class="glyphicon glyphicon-exclamation-sign pull-left"></span>
                                    <div class="media-body">
                                        <?php echo $cancelImmediateMessage ?>
                                    </div>
                                </div>
                            </div> 
                        <?php 
                    } elseif ($settingconfigData["cancelsubscription"]["immediately"] == 'true' && $settingconfigData["cancelsubscription"]["endcurrentterm"] == 'true') { ?>
                        <p> When do you want to cancel? </p>
                        <div class="radio-group">                            
                            <div class="radio">
                                <label>                                 
                                    <input type="radio" name="endOfTerm" id="cancelNow" value="false" checked=""> Cancel Immediately
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" id="cancelLater" value="true" name="endOfTerm" id="cancelLater"> Cancel on next renewal                                
                                </label>
                            </div>
                            <span id="sub_cancel.err" class="text-danger">&nbsp;</span>
                        </div> 
                        
                            <div id="cancel-immediately-info" class="alert alert-warning">
                                <div class="media text-left">
                                    <span class="glyphicon glyphicon-exclamation-sign pull-left"></span>
                                    <div class="media-body">
                                        <span id="cancelText"><?php echo $cancelImmediateMessage ?></span>
                                    </div>
                                </div>
                            </div>     
                        
                            <div id="cancel-next-renewal-info" class="alert alert-warning" style='display:none;'>
                                <div class="media text-left">
                                    <span class="glyphicon glyphicon-exclamation-sign pull-left"></span>
                                    <div class="media-body">
                                        <?php echo $cancelEndOfTermMessage ?>
                                    </div>
                                </div>
                            </div>
                    <?php 
                    } 
                    elseif ($settingconfigData["cancelsubscription"]["endcurrentterm"] == 'true') { 
                         ?>
                        
                            <input type="hidden" id="endOfTerm" value="true" name="endOfTerm" >
                            <div id="cancel-next-renewal-info" class="alert alert-warning" >
                                <div class="media text-left">
                                    <span class="glyphicon glyphicon-exclamation-sign pull-left"></span>
                                    <div class="media-body">
                                        <?php echo $cancelEndOfTermMessage ?>
                                    </div>
                                </div>
                            </div>
                        <?php  
                    } elseif ($settingconfigData["cancelsubscription"]["immediately"] == 'true') {?>
                        
                            <input type="hidden" id="endOfTerm" value="false" name="endOfTerm" >
                            <div id="cancel-immediately-info" class="alert alert-warning" >
                                <div class="media text-left">
                                    <span class="glyphicon glyphicon-exclamation-sign pull-left"></span>
                                    <div class="media-body">
                                        <?php echo $cancelImmediateMessage ?>
                                    </div>
                                </div>
                            </div> 
                        <?php 
                    }             
                    ?>           
                    <br>                                        
                    <div class="form-inline">
                        <div class="form-group">
                            <input type="button" id="updateSubscription" value="Cancel Subscription" class="btn
                                   btn-danger">
                        </div>
                        <div class="form-group">
                            <a class="btn btn-link" id="back" href=<?php echo getCancelURL($configData) ?>>Go Back</a>                 
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include("footer.php"); ?>
<script>
    $('#cancelNow').click(function () {
        var cancelImmediateText = $("#cancelImmediateText").val();
        $('#cancelText').text(cancelImmediateText);
    });
    $('#cancelLater').click(function () {
        var cancelLaterText = $("#cancelLaterText").val();
        $('#cancelText').text(cancelLaterText);
    });

    $('#updateSubscription').click(function () {
        var subscriptionId = $("#subscriptionId").val();
        var endOfTerm = $('input[name=endOfTerm]:checked', '#cancelForm').val();
        if(endOfTerm == ''){
            var endOfTerm = $("#endOfTerm").val();
   	 	}    
        var params = {action: "subscriptionCancel", endOfTerm: endOfTerm, subscriptionId: subscriptionId};
        AjaxCallMessage('api.php', 'POST', 'json', $.param(params), 'cancelsubscription');
    });
</script>
