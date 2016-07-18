<?php 
include("header.php"); 
$cancelImmediately = $settingconfigData["cancelsubscription"]["immediately"] == 'true';
$cancelEndOfTerm = $servicePortal->getSubscription()->status != "non_renewing" && 
			$servicePortal->getSubscription()->status != "future" && 
			!($servicePortal->getSubscription()->status == "in_trial" && isset($servicePortal->getSubscription()->cancelledAt))	&&
			$settingconfigData["cancelsubscription"]["immediately"] == 'true';
?>
<div class="container">
  <div id="cb-wrapper-ssp">
    <?php include("processing.php") ?>
    <div id="cb-user-content">
      <form id="portal_subscription_cancel_submit" method="post">
		  <div class="cb-well">
          <h3 class="text-center">Cancel Subscription</h3>
          <p> When do you want to cancel? </p>
          <div class="radio-group">
			<?php if($cancelImmediately){ ?>  
            <div class="radio">
              <label>
                <input type="radio" name="cancel-end-of-term" value="false" 
						data-info="cancel-immediately-info" checked="checked"> Cancel Immediately
              </label>
            </div>
			<?php } ?>
			<?php if($cancelEndOfTerm) {?>
            <div class="radio">
              <label>
                <input type="radio" name="cancel-end-of-term" value="true"
					 data-info="cancel-next-renewal-info"
					<?php echo $cancelImmediately ? "": "checked='checked'" ?> > Cancel on next renewal
              </label>
            </div>
			<?php } ?>
            <span id="sub_cancel.err" class="text-danger">&nbsp;</span>
          </div>
		  <?php if($cancelImmediately){?>           
          <div id="cancel-immediately-info" class="alert alert-warning">
            <div class="media text-left">
              <span class="glyphicon glyphicon-exclamation-sign pull-left"></span>
              <div class="media-body">
                <?php echo InfoNAlerts::cancelSubscriptionImmediatelyInfoMsg($servicePortal); ?>
              </div>
            </div>
          </div>
		  <?php } ?>   
		  <?php if($cancelEndOfTerm) {?>           
          <div id="cancel-next-renewal-info" class="alert alert-warning" <?php echo $cancelImmediately ? "style='display:none;'" : "" ?>>
            <div class="media text-left">
              <span class="glyphicon glyphicon-exclamation-sign pull-left"></span>
              <div class="media-body">
                <?php echo InfoNAlerts::cancelSubscriptionEndOfTermInfoMsg($servicePortal)?>
              </div>
            </div>
          </div>
		  <?php } ?>              
          <div class="form-inline">
            <div class="form-group">
              <input type="submit" id="updateSubscription" value="Cancel Subscription" class="btn btn-danger">
            </div>
            <div class="form-group">
              <a href="<?php echo getCancelURL($configData) ?>" class="btn btn-link">
                Go back
              </a>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include("footer.php") ?>

<script type="text/javascript">
$(document).ready(function(){
	$('input[name="cancel-end-of-term"]').on('click', function(){
		var id = $(this).attr("data-info");
		$(".alert").hide();
		$('#' +id).show();
	})
    $('#updateSubscription').click(function (e) {
		e.preventDefault();
        var endOfTerm = $('input[name="cancel-end-of-term"]:checked').val();
        var params = {action: "subscriptionCancel", endOfTerm: endOfTerm};
        AjaxCallMessage('api.php', 'POST', 'json', $.param(params), 'cancelsubscription');
    });
})
</script>