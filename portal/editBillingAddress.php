<?php
include("header.php");
$customer = $servicePortal->getCustomer();
$address = $customer->billingAddress;
?>

<div class="container" style="height:674px">
    <div id="cb-wrapper-ssp">
		<?php include("processing.php") ?>
        <div id="cb-user-content">
            <form id="addressForm" method="POST">
                <div class="cb-well">
                    <h3 class="text-center">Billing Information</h3>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="billing_address[first_name]">First Name <span>*</span>
                                </label>
                                <input id="billing_address[first_name]" name="billing_address[first_name]" type="text" 								class="form-control" value="<?php echo (isset($address->firstName) ? esc($address->firstName) : "" ) ?>" 
								required data-msg-required="cannot be blank" >
                                <span id="billing_address[first_name]" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="billing_address[last_name]">Last Name <span>*</span>
                                </label>
                                <input id="billing_address[last_name]" name="billing_address[last_name]" type="text" 								class="form-control" value="<?php echo (isset($address->lastName) ? esc($address->lastName) : "" ) ?>" 
								required data-msg-required="cannot be blank"  >
                                <span id="billing_address[last_name]" class="text-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="billing_address[company]">Company
                                </label>
                                <input id="billing_address[company]" name="billing_address[company]" type="text" class="form-control"    									value="<?php echo (isset($address->company) ? esc($address->company) : "" ) ?>" >
                                <span id="billing_address[company]" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="billing_address[email]">Address Line 1 <span>*</span>
                                </label>
                                <input id="billing_address[line1]" name="billing_address[line1]" type="text" class="form-control" 								value="<?php echo (isset($address->line1) ? esc($address->line1) : "" ) ?>" 
								required data-msg-required="cannot be blank" >
                                <span id="billing_address[line1]" class="text-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="billing_address[line2]">Address Line 2
                                </label>
                                <input id="billing_address[line2]" name="billing_address[line2]" type="text" class="form-control"  									value="<?php echo (isset($address->line1) ? esc($address->line2) : "" ) ?>" >
                                <span id="billing_address[line2]" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="billing_address[city]">City <span>*</span>
                                </label>
                                <input id="billing_address[city]" name="billing_address[city]" type="text" class="form-control" 										value="<?php echo (isset($address->city) ? esc($address->city) : "" ) ?>" 
									 	required data-msg-required="cannot be blank" >
                                <span id="billing_address[city]" class="text-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="billing_address[zip]">Zip
                                </label>
                                <input id="billing_address[zip]" name="billing_address[zip]" type="text" class="form-control" value="<?php echo (isset($address->zip) ? esc($address->zip) : "" ) ?>" validate="true">
                                <span id="billing_address[zip]" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="billing_address[country]">Country <span>*</span>
                                </label>
                                <select name="billing_address[country]" class="form-control" id="country" name="country"
										required data-msg-required="cannot be blank" >
                                    <?php
                                    $countryCodes = $servicePortal->getCountryCodes($configData);
                                    $billingCountry = null;
                                    if (isset($address->country) && isset($address->country)) {
                                        $billingCountry = $address->country;
                                    }
                                    ?>
                                    <option value="" <?php echo ($billingCountry == null ) ? "selected" : "" ?> >
                                        Select your country
                                    </option>
                                        <?php foreach ($countryCodes as $code => $country) { ?>
                                        <option value="<?php echo $code ?>" <?php echo ($code == $billingCountry) ? "selected" : "" ?> >
                                        <?php echo $country ?>
                                        </option>
										<?php } ?>  
                                </select>
                                <span id="billing_address[country]" class="text-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="billing_address[state]">State
                                </label>
                                <input id="billing_address[state]" name="billing_address[state]" type="text" class="form-control"  value="<?php echo (isset($address->state) ? esc($address->state) : "" ) ?>" validate="true">
                                <span id="billing_address[state]" class="text-danger"></span>
                            </div>
                        </div>
                    </div>             
                    <hr class="clearfix">
                    <div class="form-inline">
                        <div class="form-group">
                            <input type="submit" id="updateAddress" value="Update billing address" class="btn
                                   btn-primary">
						    <input type="hidden" name="action" value="updateBillingAddress">
                        </div>
                        <div class="form-group">
                            <a href=<?php echo getCancelURL($configData) ?> id="back" class="btn btn-link">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include("footer.php"); ?>
<script>  
$(document).ready(function() {
    $('#updateAddress').click(function () {
		if(!$('#addressForm').valid()){
			return false;
		}
		var data = $('#addressForm').serialize();
        AjaxCallMessage('api.php', 'POST', 'json', data, 'editaddress');    
		return false;        
    });
})    
    
</script>

