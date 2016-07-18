<?php
/**
 * Chargebee APIs are invoked from this class.
 * This class will provide the entire information of a particualar subscription.
 * All form submits and their corresponding Chargebee APIs are called here.
 */
class ServicePortal {

    private $sessionSubscriptionId = null;
    private $subscriptionDetails = null;
	private $planDetails = null;

    public function __construct($subscriptionId) {
        $this->sessionSubscriptionId = $subscriptionId;
        $this->subscriptionDetails = $this->retrieveSubscription($this->sessionSubscriptionId);
		$this->planDetails = $this->retrievePlan($this->subscriptionDetails->subscription()->planId);
    }

    /*
     * Retrieves subscription information from Chargebee.
     */
    private function retrieveSubscription($subscriptionId) {
        $result = ChargeBee_Subscription::retrieve($subscriptionId);
        return $result;
    }
	
	
    /*
     * Retrieves plan information from Chargebee.
     */
    public function retrievePlan($planId) {
        $planResult = ChargeBee_Plan::retrieve($planId);
        return $planResult->plan();
    }
	

    /*
     * Retrieves all available plans from Chargebee.
     */
    public function retrieveAllPlans() {
		$offset = null;
		$plans = array();
		do {
        	$all = ChargeBee_Plan::all(array("limit" => 100, "offset" => $offset));
			foreach ($all as $plan) {
				array_push($plans, $plan);
			}
			$offset = $all->nextOffset();
		} while($offset != null);
        return $plans;
    }

    /*
     * Retrieves all available addons from Chargebee.
     */
    public function retrieveAllAddons() {
		$addons = array();
		$offset = null;
		do{
			 $all = ChargeBee_Addon::all(array("limit" => 100, "offset" => $offset));
			 foreach ($all as $a){
				 array_push($addons, $a);
			 }
			 $offset = $all->nextOffset();
		} while($offset != null);
        return $addons;
    }
	
	/**
	 * Estimation of Subscription change returned based on the inputs passed. 
	 */
	public function changeSubscriptionEstimate($planId, $planQuantity, $addons, $replaceAddonList, $endOfTerm) {
		$subParams = array("id" => $this->getSubscription()->id);
		if(isset($planId) ){
			$subParams["planId"] = $planId;
			$subParams["planQuantity"] = $planQuantity;
		}
		$params = array("subscription" => $subParams);
		if(isset($addons)) {
			$params["addons"] = $addons;
		}
		if(isset($replaceAddonList)){
			$params["replaceAddonList"] = $replaceAddonList;
		}
		if(isset($endOfTerm)) {
			$params["endOfTerm"] = $endOfTerm;
		}
		$subscription = $this->subscriptionDetails->subscription();
		$result = ChargeBee_Estimate::updateSubscription($params);
		return $result->estimate();
	}
	
	/**
	 * Returns only the line items of the passed entity type(plan, addon)
	 */
	public function getLineItems($invoiceEstimate, $entityType) {
	   $items = array();
	   if(!(isset($invoiceEstimate) && isset($invoiceEstimate->lineItems))) {
		   return $items;
	   }
 	   foreach ($invoiceEstimate->lineItems as $li) {
 		  if($li->entityType ==  $entityType) {
			array_push($items, $li);
		  }
	   }
	   return $items;
	}

    /**
	 * Retrieves only 5 invoices from Chargebee. If offset is passed, then the 
	 * invoices from the offset will be returned.
	 */
    function retrieveInvoice($offset=null) {
        $inputParams = array("limit" => 5 );
        if(isset($offset)){
            $inputParams['offset'] = $offset;
        }         
        return ChargeBee_Invoice::invoicesForSubscription($this->getSubscription()->id, $inputParams);
    }

    /*
     * Updates the customer details in Chargebee.
     */
    function updateAccountInfo() {
        try {
            $result = ChargeBee_Customer::update($this->getCustomer()->id, 
					array("firstName" => $_POST['first_name'],
                    	"lastName" => $_POST['last_name'],
                        "company" => $_POST['company'],
                        "phone" => $_POST['phone']
            ));
            $response["status"] = "success";
			$response["forward"] = getReturnURL();
            return json_encode($response);
        } catch (ChargeBee_InvalidRequestException $e) {
            return $this->handleInvalidRequestErrors($e);
        } catch (Exception $e) {
            return $this->handleGeneralErrors($e);
        }
    }

    /*
     * Updates the billing information of customer in Chargebee.
     */
    function updateBillingAddress() {
        try {
            $result = ChargeBee_Customer::updateBillingInfo($this->getCustomer()->id, 
									array("billingAddress" => $_POST["billing_address"]));
						            $response["status"] = "success";
									$response["forward"] = getReturnURL();
            $response["status"] = "success";
			$response["forward"] = getReturnURL();
            return json_encode($response);						
        } catch (ChargeBee_InvalidRequestException $e) {
            return $this->handleInvalidRequestErrors($e);
        } catch (Exception $e) {
            return $this->handleGeneralErrors($e);
        }
    }

    /*
     * Updates the shipping address of the subscription in Chargebee.
     */
    function updateShippingAddress() {
        try {
            $result = ChargeBee_Subscription::update($this->getSubscription()->id, 
								array("shippingAddress" => $_POST["shipping_address"]));
            $response["status"] = "success";
			$response["forward"] = getReturnURL();
            return json_encode($response);
        } catch (ChargeBee_InvalidRequestException $e) {
            return $this->handleInvalidRequestErrors($e);
        } catch (Exception $e) {
            return $this->handleGeneralErrors($e);
        }
    }

    /*
     * Updates the subcription in Chargebee.
     */
    function updateSubscription() {
        try {
            global $settingconfigData;
            $endOfTerm = $settingconfigData["subscription"]["immediately"] == "false";
            $result = ChargeBee_Subscription::update($this->getSubscription()->id, array(
                "planId" => $_POST['plan_id'],
                "planQuantity" => $_POST['plan_quantity'],
                "endOfTerm" => $endOfTerm,
                "replaceAddonList" => $_POST["replace_addon_list"],
                "addons" => isset($_POST['addons']) ? $_POST["addons"] : array()
            ));
            $response["status"] = "success";
			$response["forward"] = getReturnURL();
            return json_encode($response);
        } catch (ChargeBee_PaymentException $e) {
            return $this->handleChargeAttemptFailureErrors($e);
        }catch (ChargeBee_InvalidRequestException $e) {
            return $this->handleInvalidRequestErrors($e);
        } catch (Exception $e) {
            return $this->handleGeneralErrors($e);
        }
    }

    /*
     * Reactivates the subscription from "cancel" to "active" state.
     */
    function subscriptionReactivate() {
        try {            
            $result = ChargeBee_Subscription::reactivate($this->getSubscription()->id);
            $response["status"] = "success";
			$response["forward"] = getReturnURL();
            return json_encode($response);
        } catch (ChargeBee_PaymentException $e) {
            return $this->handleChargeAttemptFailureErrors($e);
        } catch (ChargeBee_InvalidRequestException $e) {
            return $this->handleInvalidRequestErrors($e);
        } catch (Exception $e) {
            return $this->handleGeneralErrors($e);
        }
    }

    /*
     * Cancels the subscription in Chargebee.
     */
    function subscriptionCancel() {
        try {
            $result = ChargeBee_Subscription::cancel($this->getSubscription()->id, 
                array("endOfTerm" => $_POST['endOfTerm']));
            $response["status"] = "success";
			$response["forward"] = getReturnURL();
            return json_encode($response);
        } catch (ChargeBee_InvalidRequestException $e) {
            return $this->handleInvalidRequestErrors($e);
        } catch (Exception $e) {
            return $this->handleGeneralErrors($e);
        }
    }

    /*
     * Gets the subscription's card details.
     */
    function getCard() {
        return $this->subscriptionDetails->card();
    }

    /*
     * Gets the subscription's addon details.
     */
    function getAddon() {
        return isset($this->getSubscription()->addons) ? $this->getSubscription()->addons : array();
    }

    /*
     * Gets the customer details from the current session
     */
    function getCustomer() {
        return $this->subscriptionDetails->customer();
    }
    
    /*
     * Gets the subscription details from the current session
     */
    function getSubscription(){
        return $this->subscriptionDetails->subscription();
    }
	
    /*
     * Returns the subscription's Chargebee Plan Object.
     */
    public function getPlan() {
        return $this->planDetails;
    }
    
    /*
     * Hide/show "Edit" subscription in the portal page.
     */
    function getEditSubscription($settingconfigData){
        $customer = $this->getCustomer();        
        $autoCollection = $customer->autoCollection;
        $subscription = $this->getSubscription();
		
		if($subscription->status == "in_trial" || $subscription->status == "cancelled" || $subscription->status == "future" || 
				($autoCollection == "on" && $subscription->status == "non_renewing" && $customer->cardStatus == "no_card") ) {
			return false;
		}
		
        $allPlans = $this->retrieveAllPlans();
		$planChange = $this->planAccessible($allPlans, $settingconfigData);
		if($planChange) {
			// if plan change is allowed then showing the edit subscription option.
			return true;
		}
        $allAddons = $this->retrieveAllAddons(); 
        $addonChange = $this->addonAccessible($allAddons, $settingconfigData);
	
		return $planChange || $addonChange;
    }
	
	public function planAccessible($allPlans, $settingconfigData) {
        $activePlans = array();
        $archivedPlans = array();
        foreach ($allPlans as $p) {
            if($p->plan()->status == 'active'){
                $activePlans[] = $p->plan()->id;        
            }          

            if($p->plan()->status == 'archived'){
                $archivedPlans[] = $p->plan()->id;
            }

            if($p->plan()->id == $this->getSubscription()->planId){
                $currentPlanDetails = $p->plan();
            }
        }

		$planChange = true;
		if(sizeof($activePlans) == 0) { // if no active plans in the site
			$planChange = false;
			if($settingconfigData["changesubscription"]["planqty"] == 'true' && $currentPlanDetails->chargeModel=='per_unit') {
				$planChange = true;					
			}	
		}
		if(sizeof($activePlans) == 1 && $currentPlanDetails->status != "archived" ) {
			$planChange = true;
			if($currentPlanDetails->chargeModel == "flat_fee" || 
					$settingconfigData["changesubscription"]["planqty"] == 'false') {
				$planChange = false;
			} 
		}
		return $planChange;
	}
	
	public function addonAccessible($allAddons, $settingconfigData) {
		$curAddons = $this->getAddon();
        $activeAddons = array();
        $archivedAddons = array();
        foreach ($allAddons as $a) {
			if($a->addon()->chargeType == "non_recurring" || !$this->addonIsApplicableToPlan($this->getPlan(), $a->addon())) {
				continue;
			}
            if($a->addon()->status == 'active'){
                $activeAddons[] = $a->addon()->id;        
            }          

            if($a->addon()->status == 'archived'){
                $archivedAddons[] = $a->addon()->id;
            }
        }	
		$addonChange = true;
		if($settingconfigData["changesubscription"]["addon"] == 'false'){
			$addonChange = false;
		} else {
			if(sizeof($activeAddons) == 0) { // if no active addons present in the site
				if(sizeof($curAddons) == 0) {
					$addonChange = false;
				} else {
					// current addons is in archived state
					$addonChange = true;
				}
			} 
		}
		return $addonChange;
	}
	
	/**
	 * Check the addon is applicable to the plan during change subscription
	 */
	function addonIsApplicableToPlan($plan, $addon){
		$planPeriod = $plan->period;
		$planPeriodUnit = $plan->periodUnit;
		if($planPeriodUnit == "year") {
			$planPeriodUnit = "month";
			$planPeriod = $planPeriod * 12;
		}
		
		$addonPeriod = $addon->period;
		$addonPeriodUnit = $addon->periodUnit;
		if($addonPeriodUnit == "year") {
			$addonPeriodUnit = "month";
			$addonPeriod = $addonPeriod * 12;
		}
		if($planPeriodUnit == "week") {
			return $addonPeriodUnit == "week" && ($planPeriod % $addonPeriod == 0);
		} else if($planPeriodUnit == "month") {
			return $addonPeriodUnit == "month" && ($planPeriod % $addonPeriod == 0);
		} else {
			throw new RuntimeException("Not handled plan period");
		}
	}
	
	
	/**
	 *Check if the passed addons is the current subscription addon
	 */
	function isCurrentSubscriptionAddon($addon){
		foreach($this->getAddon() as $currentAddon) {
			if($addon->id == $currentAddon->id){
				return true;
			}
		}
		return false;
	}

    /*
     * This method handles ChargeBee_PaymentException when an attempt to charge a subscription fails.
     */
    private function handleChargeAttemptFailureErrors($e) {
        error_log("Error : " . json_encode($e->getJSONObject()));
        $errorResponse = array();
        if($e->getApiErrorCode() == "payment_processing_failed") {
            $errorResponse["error_msg"] = "We are unable to process the payment using the existing card information. 
                                            Please update your account with a valid card and try again.";
        } else if($e->getApiErrorCode() == "payment_method_not_present") {
            $errorResponse["error_msg"] = "We couldn't process the payment because your account doesn't have a card associated with it.                                         Please update your account with a valid card and try again.";
        } else {
            $errorResponse["error_msg"] = "Couldn't charge the subscription";
        }
        header("HTTP/1.0 400 Invalid Request");
        print json_encode($errorResponse, true);
    }

    /*
     * This method handles ChargeBee_InvalidRequestException and 
     * api_error_code of type "invalid_request".
     */
    private function handleInvalidRequestErrors($e, $param = null) {
        $jsonResult = $e->getJSONObject();
        error_log("Error : " . json_encode($jsonResult));
        $errorResponse = array();
		error_log("Error : " . json_encode($e->getJSONObject()));
		$errorResponse = array();
		if( $e->getParam() == null || ($param != null && is_array($param) && in_array($e->getParam(), $param) ) 
									|| $e->getParam() == $param ) {
			// These errors due to incomplete or wrong configuration (such as plan or addon not being present) in your Chargebee site
			// and not due to user's input.
			$errorResponse["error_msg"] = "Service has encountered an error. Please contact us.";
			header('HTTP/1.0 500 Internal Server Error');
		} else {
			// These are parameter errors from Chargebee that should be validated  before calling the API.
			$errorResponse["error_param"] = $e->getParam();
			$errorResponse["error_msg"] = "invalid value";
			header('HTTP/1.0 400 Invalid Request');
		}
		return json_encode($errorResponse,true);	
    }
    
    /*
    * This method handles ChargeBee_InvalidRequestException, 
    * api_error_code of types "invalid_state_request" and "invalid_request".
    */
    function handleInvalidErrors($e) {
        if ($e->getApiErrorCode() == "invalid_state_for_request") {
            // Error due to invalid state to perform the API call.
            error_log("Error : " . json_encode($e->getJSONObject()));
            $errorResponse = array("error_msg" => "Invalid state for this operation");
            print json_encode($errorResponse, true);
            header("HTTP/1.0 400 Invalid Request");
        } else {
            handleInvalidRequestException($e);
        }
    }

    /*
     * Handles general errors ocurred during API call.
     */
    private function handleGeneralErrors($e) {
        error_log("Error : " . $e->getMessage());
        $errorResponse = array();
        if( $e instanceof ChargeBee_OperationFailedException  ) {
            // This error could be due to unhandled exception in Chargebee 
            // or requests blocked due to too many API calls.
            $errorResponse["error_msg"] = "Something went wrong. Please try again later.";      
        } else if( $e instanceof ChargeBee_APIError ) {
            // This error could be due to invalid API key/invalid site name/ some configuration  missing in Chargebee's web interface.
            $errorResponse["error_msg"] = "Sorry, Something doesn't seem right. Please inform us. We will get it fixed.";       
        } else if( $e instanceof ChargeBee_IOException ) {
            // This error could be due to failure in communication with Chargebee.
            // This is generally a temporary error. 
            // Note: Incase it is a read timeout error (and not connection timeout error) 
            // then the API call might have succeeded in Chargebee.
            $errorResponse["error_msg"] = "We are facing some network connectivity issues. Please try again.";      
        } else {
            // Bug in code. Depending on your code, it could have happened even after successful 
            // Chargebee API call.
            $errorResponse["error_msg"] = "Whoops! Something went wrong. Please inform us. We will get it fixed.";
        }
        //header("HTTP/1.0 500 Error");
        return json_encode($errorResponse, true);    
    }

    /*
     * Get the list of Countries and their codes.
     */
    function getCountryCodes($configData) {
        $filePath = $_SERVER['DOCUMENT_ROOT'] . "/" . $configData['APP_PATH'] . "/countries.txt";
        $countryCodes = array();
        $content = file_get_contents($filePath);
        $countryCodeArray = explode(":", $content);
        foreach ($countryCodeArray as $line) {
            $cc = explode(",", $line);
            if (sizeof($cc) == 2) {
                $countryCodes[$cc[0]] = $cc[1];
            }
        }
        return $countryCodes;
    }

}
