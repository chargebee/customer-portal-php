<?php
include("header.php");

$curAddons = $servicePortal->getAddon();
$curPlan = $servicePortal->getSubscription()->planId;
$curPlanQty = $servicePortal->getSubscription()->planQuantity;
$allPlans = $servicePortal->retrieveAllPlans(); 
$allAddons = $servicePortal->retrieveAllAddons(); 
$planQuantity = $servicePortal->getSubscription()->planQuantity;
$planChange = $servicePortal->planAccessible($allPlans, $settingconfigData);
$addonChange = $servicePortal->addonAccessible($allAddons, $settingconfigData);
?>

    <div class="container">
      <div id="cb-wrapper-ssp">
        <?php include("processing.php") ?>
        <div id="cb-user-content">
          <form id="portal_subscription_change_submit" method="post">
            <div class="cb-well">
              <div class="cb-product">
                <div class="cb-product-header">
                  <div class="cb-product-steps" data-cb="cb-product-steps">
					 <?php if($planChange) { ?>
                    	 <div class="cb-product-step current" data-cb-step-for="section-plan" data-cb-current-step='current'>
                      	   	Change your plan
                    	 </div>
					<?php } ?>
					<?php if($addonChange) { ?>
                    <div class="cb-product-step <?php echo !$planChange ? "current" :  "" ?>" 
								data-cb-step-for="section-addons" data-cb-current-step=''>
                      Add/Remove Addons
                    </div>
					<?php } ?>
                    <div class="cb-product-step" data-cb-step-for="section-review" data-cb-current-step=''>
                      Review and Confirm
                    </div>
                  </div>
                </div>
				<?php if($planChange) {?>
                <div id="section-plan" class="cb-product-body" data-cb="cb-product-body" data-cb-req-from="plan">
					<?php include("editPlan.php") ?>
                </div>
				<?php } ?>
				
				<?php if($addonChange) {?>
                <div id="section-addons" class="cb-product-body" data-cb-req-from="addon" 
						<?php echo $planChange ? "style='display:none'" : ""?> >
                	<?php 
					if(!$planChange) {
						include("editAddon.php");
					}
					?>
                </div>
				<?php } ?>
                <div id="section-review" class="cb-product-body" data-cb-req-from="review" style="display:none">
                </div>
              </div>

            </div>
          </form>
        </div>
      </div>
    </div>

<?php include("footer.php"); ?>

<script type="text/javascript">
  $(document).ready(function() {
  	
  	/*
  	 * On selecting the plan enables the plan quantity for the user to change the quantity.
  	 */
    $("input[name=plan_id]:radio").on('change', function () {
          var plan = $("input[name=plan_id]:radio:checked");
          $(plan).attr("checked");
          var planId = $(plan).val();
          $('input[name=plan_quantity]').attr('disabled', true);
          $("input[data-plan-quantity='"+ planId +"']").removeAttr('disabled');
     });

    /**
	 * On quantity change for a plan, plan price is updated.
	 */
	 $('input[data-cb="plan-quantity-select"]').on('click', function(){
	 	var planId = $(this).attr("data-plan-quantity")
		var qty = $(this).val();
		var planPrice = $('input[data-plan-price="' + planId + '"]').val();
		var total = (planPrice * qty).toFixed(2);
		$("span[data-plan-total-price='"+ planId +"']").text(total);
	 })
	 
	 $('#section-addons').on('change', 'input[name="addons"]', function(){
		 var addonId = $(this).val();
		 if($(this).is(":checked")){
		 	$("input[data-addon-quantity='"+ addonId +"']").removeAttr('disabled');
		 } else {
		 	$("input[data-addon-quantity='"+ addonId +"']").attr("disabled", "true");
		 }			 
	 })
	   

    /**
	 * On change quantity in addon, addon price is updated.
	 */
	 $('#section-addons').on('change', 'input[data-cb="addon-quantity-select"]', function() {
		 var addonId = $(this).attr("data-addon-quantity");
		 var addonQty = $(this).val();
	     var addonPrice = $("input[data-addon-price='" + addonId + "']").val();
		 var total = (addonPrice * addonQty).toFixed(2);
		 $("span[data-addon-total-price='" + addonId + "']").text(total);
	 })
	
	/**
	 * Prev action in addon section
	 */
	$('#section-addons').on('click', 'a[data-action="prev"]', function() {
		// back to previous section
		var currentSection = $(this).attr("data-current-section"); 
		var prevSection = $(this).attr("data-prev-section");
		$('div[data-cb-step-for="'+ currentSection +'"]').removeClass("current");
		$('#' + currentSection).hide();
		$('div[data-cb-step-for="'+ prevSection +'"]').addClass("current");
		$('#' + prevSection).show();
	}) 
	
	$('#section-review').on('click', 'a[data-action="prev"]', function() {
		// back to previous section
		var currentSection = $(this).attr("data-current-section"); 
		var prevSection = $(this).attr("data-prev-section");
		$('div[data-cb-step-for="'+ currentSection +'"]').removeClass("current");
		$('#' + currentSection).hide();
		$('div[data-cb-step-for="'+ prevSection +'"]').addClass("current");
		$('#' + prevSection).show();
	})
	/**
	 * Next action in plan section
	 */
	$('.skip-plan').on('click', function() {
		var currentSection = $(this).attr("data-current-section");
		var nextSection = $(this).attr("data-next-section");
		var url = $(this).attr("data-url");
		sendRequest("GET", url, "", currentSection, nextSection);
	})
	
	/**
	 * Next action in addon section
	 */
	$('#section-addons').on('click', '.skip-addons', function() {
		var currentSection = $(this).attr("data-current-section");
		var nextSection = $(this).attr("data-next-section");
		var url = $(this).attr("data-url");
		var data = {"plan_id" : $('input[name="plan-id"]').val(), 
	                "plan_quantity" : $('input[name="plan-qty"]').val() };	 
		sendRequest("GET", url, data, currentSection, nextSection);
	})
	
	/**
	 * Save and continue action in plan section
	 */
	$('input[data-cb="save-plan"]').on('click', function() {
		// save the selected plan and move to addon section.
		var plan = $("input[name=plan_id]:radio:checked");
		var planId = $(plan).val();
		
		var qty = $('input[data-plan-quantity="'+ planId +'"]').val();
		if( typeof(qty) === "undefined") {
			qty=1;
		}
		
		var data = { "plan_id" : planId, "plan_quantity" : qty};
		var url = $(this).attr("data-url");
		var currentSection = $(this).attr("data-current-section");
		var nextSection = $(this).attr("data-next-section");
		sendRequest("GET", url, data, currentSection, nextSection);
	})
	
	/**
	 * Save and continue action in addon section
	 */
	$('#section-addons').on('click', 'input[data-cb="save-addon"]', function() {
		var addons = getSelectedAddons();
		var data = {"plan_id" : $('input[name="plan-id"]').val(), 
	                "plan_quantity" : $('input[name="plan-qty"]').val(),
		            "replace_addon_list" : "true",
	                "addons" : addons };
		var url = $(this).attr("data-url");			
		var currentSection = $(this).attr("data-current-section");
		var nextSection = $(this).attr("data-next-section");			
		sendRequest("GET", url, $.param(data), currentSection, nextSection);			
	})
	
	function getSelectedAddons() {
		var selectedAddons = $("input[name='addons']:checked");
		var addons = {};
		for(var i=0; i< selectedAddons.length; i++) {
			var addonId = $(selectedAddons[i]).val();
			var addonQty = $('input[data-addon-quantity="'+ addonId +'"]').val();
			if( typeof(addonQty) == "undefined") {
				addonQty = 1;
			}
			addons[i] = {"id" : addonId, "quantity" : addonQty};
		} 
		return addons;
	}
	
	/**
	 * Change Subscription form submit
	 */
	$('#section-review').on('click','input[data-cb="review"]', function() {
		var params = { action : "updateSubscription",
		               plan_id : $('input[name="plan-id"]').val(), 
					   plan_quantity : $('input[name="plan-qty"]').val() };
		if($('input[name="replace_addon_list"]').val() == "true") {
			params["replace_addon_list"] = "true";
		   	params["addons"] = getSelectedAddons();
		} else {
			params["replace_addon_list"] = "false";
		}
 		AjaxCallMessage('api.php', 'POST', 'json', $.param(params), 'editsubscription');
	})
	
	function sendRequest(method, url, data, currentSection, nextSection) {
		$.ajax({
			type: method,
			url: url,
			data : data,
	 		beforeSend: function(response){
	 				$(".cb-alert-flash, .loader").show();
	 		},
            success: function (response) {
				$('#'+currentSection).hide();
				$('div[data-cb-step-for="'+ currentSection +'"]').removeClass("current");
				$('div[data-cb-step-for="'+ nextSection +'"]').addClass("current");
                $('#'+nextSection).html(response);
				$('#'+nextSection).show();
			},
			error: function(response) {
				var resp = JSON.parse(response.responseText);
				$("#cb-handle-progress .alert-danger").show();
				$("#cb-handle-progress .alert-danger .message").text(resp.error_msg);
	        	$("#cb-handle-progress .alert-danger").fadeOut(8000);
			},
			complete: function(response) {
				$(".cb-alert-flash, .loader").hide();
			}		 
		})
		
	}
	
	
	
	
  })
</script>