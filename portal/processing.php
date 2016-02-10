<?php
$successFlashMsg = ""; 
if ($successMessage == 'true') { 
 	 if ($_GET['page'] == 'editaddress') { 
       $successFlashMsg = $infoconfigData['address']['success'];
     } elseif ($_GET['page'] == 'editaccount') { 
		$successFlashMsg =  $infoconfigData['account']['success'];
     } elseif ($_GET['page'] == 'editshipping') {
         $successFlashMsg = $infoconfigData['shipping']['success'];
     } elseif ($_GET['page'] == 'editsubscription') { 
       $successFlashMsg = $infoconfigData['subscription']['success'];
     } elseif ($_GET['page'] == 'cancelsubscription') { 
		$successFlashMsg = $infoconfigData['cansubscription']['success'];
	 } elseif ($_GET['page'] == 'index') { 
        $successFlashMsg = $infoconfigData['reactivatesubscription']['success'];
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