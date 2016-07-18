<?php
$successFlashMsg = ""; 
if ($successMessage == 'true') { 
 	 if ($_GET['page'] == 'editaddress') { 
       $successFlashMsg = "Billing Address Updated Successfully";
     } elseif ($_GET['page'] == 'editaccount') { 
		$successFlashMsg =  "Account Information Updated Successfully";
     } elseif ($_GET['page'] == 'editshipping') {
         $successFlashMsg = "Shipping Address Updated Successfully";
     } elseif ($_GET['page'] == 'editsubscription') { 
       $successFlashMsg = "Subscription Updated Successfully";
     } elseif ($_GET['page'] == 'cancelsubscription') { 
		$successFlashMsg = "Subscription Cancelled Successfully";
	 } elseif ($_GET['page'] == 'index') { 
        $successFlashMsg = "Subscription Reactivated Successfully";
	 }
}	 
?>
<div id="cb-handle-progress" >
    <div class="cb-alert-flash">
		<?php if($successMessage == 'true' && !empty($successFlashMsg)) {?>
        <div class="alert alert-success">
            <span class="glyphicon glyphicon-ok-sign">
            </span>
			<span class="message">
				<?php echo $successFlashMsg ?>
			</span>
		</div>
		<?php } ?>
        <div class="alert alert-danger" style="display: none;">
            <span class="glyphicon glyphicon-remove"></span>
            <span class="message"></span>
        </div>
        <div class="loader" style="display:none;">
            <span class="cb-process"></span> Loading...
        </div>
    </div>
</div>