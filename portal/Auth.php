<?php

/**
 * Authentication module uses Chargebee's portal login and 
 * sets the subscription and portal session IDs in cookies.
 */
class Auth {

    /*
     * Activates the portal session and sets a cookie after the customer logs in to Chargebee's portal.
     */
    private function authenticate($configData, $params) {
        $result = ChargeBee_PortalSession::activate($params['cb_auth_session_id'], array(
                    "token" => $params['cb_auth_session_token']));
        
        $linked_customers = $result->portalSession()->linkedCustomers;
        $cb_customer_email = $linked_customers[0]->email;
        $customerId = $result->portalSession()->customerId;
        $listOfSubscription = ChargeBee_Subscription::subscriptionsForCustomer($customerId);
        foreach ($listOfSubscription as $value) {
            $subscriptionDetails[] = $value;
        }        
        $subscriptionDetails = $subscriptionDetails[0];
        $subscription = $subscriptionDetails->subscription();
        $this->setSubscriptionId($subscription->id);
                
        setcookie('cb_portal_session_id', 
            $params['cb_auth_session_id'], 
            time() + 60 *60, 
            $configData['COOKIE_PATH'], 
            $configData['COOKIE_DOMAIN'], 
            $configData['COOKIE_SECURE'], 
            $configData['COOKIE_HTTPONLY']
        );
    }

	/*
	 * Sets the subscription ID in cookie. 
	 */
    public function setSubscriptionCookie($configData){        
        setcookie('cb_subscription_id', 
            $this->subscriptionId, 
            time() + 60 *60, 
            $configData['COOKIE_PATH'], 
            $configData['COOKIE_DOMAIN'], 
            $configData['COOKIE_SECURE'], 
            $configData['COOKIE_HTTPONLY']
        );
    }

    public function setSubscriptionId($subscriptionId){
        $this->subscriptionId = $subscriptionId;
    }

    public function getSessionSubscriptionId(){
        return $subscriptionId = isset($_COOKIE['cb_subscription_id']) ? $_COOKIE['cb_subscription_id'] : null;
    }
	

	/*
	 * If the params 'cb_auth_session_id' and 'cb_auth_session_token' are set, then it is 
	 * considered as a redirection from Chargebee's portal login page.
	 * Activate portal session API should be invoked after redirection. 'cb_auth_session_id' is Chargebee's portal session ID.
	 */
    public function authenticateSession($params, $configData){    
        if (isset($params['cb_auth_session_id']) && isset($params['cb_auth_session_token']) && !$this->isLoggedIn()) {
            try {
                $this->authenticate($configData, $params);
                $this->setSubscriptionCookie($configData);
                $request_url = explode("://", $configData['SITE_URL'])[0] . "://" .
                        explode("://", $configData['SITE_URL'])[1] . $_SERVER["REQUEST_URI"];

                $redirect_url = removeQueryArg(
                    array(
                        "auth_session_id", 
                        "auth_session_token", 
                        "action", 
                        "do"
                    ), $request_url);
                header('Location: ' . $redirect_url);
                exit;
            } catch (Exception $e) {
                try {
                    ChargeBee_PortalSession::logout($params['cb_auth_session_id']);
                } catch (ChargeBee_APIError $e) {
                    error_log("Error from ChargeBee: " . json_encode($e->getJSONObject()));
                }
                if ($e instanceof ChargeBee_APIError) {
                    error_log("Error : Couldn't authenticate the customer. Error msg from ChargeBee " .
                            json_encode($e->getJSONObject()));
                } else {
                    error_log("Exception : " . $e->getMessage());
                }
            }
        }
    }
	
	
	/*
     * Logs out the customer and calls Chargebee's portal seesion logout API. 
	 * Unsets the subscription and portal session IDs.
     */    
    public function logout($configData) {
        $cb_portal_session_id = isset($_COOKIE['cb_portal_session_id']) ? filter_input(INPUT_COOKIE, 'cb_portal_session_id') : null;
        if (isset($cb_portal_session_id)) {
            try {
                ChargeBee_PortalSession::logout($cb_portal_session_id);
            } catch (ChargeBee_APIError $e) {
                error_log("Error from ChargeBee : " . json_encode($e->getJSONObject()));
            }
        }
        setcookie('cb_portal_session_id', false, time() - 3600, $configData['COOKIE_PATH'], 
					$configData['COOKIE_DOMAIN'], $configData['COOKIE_SECURE'], $configData['COOKIE_HTTPONLY']);
        unset($_COOKIE['cb_portal_session_id']);        
        header('Location: ' . $configData['SITE_URL']."/".$configData['APP_PATH']);
        exit;
    }

	/*
     * Checks if the user is logged in to portal.
     */
    public function isLoggedIn() {
        $cb_portal_session_id = isset($_COOKIE['cb_portal_session_id']) ? $_COOKIE['cb_portal_session_id'] : null;
        if (isset($cb_portal_session_id)) {
            return true;
        }
        return false;
    }

	/*
     * Retrieves Chargebee's portal session ID from cookie.
     */
    public function getPortalSessionId() {
        if (isset($_COOKIE['cb_portal_session_id'])) {
            return filter_input(INPUT_COOKIE, 'cb_portal_session_id');
        }
        return false;
    }

}
