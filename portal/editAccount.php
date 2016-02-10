<?php
include("header.php");
$customer = $servicePortal->getCustomer();
$customerId = $customer->id;
?>
<div class="container" style="height:674px">
    <div id="cb-wrapper-ssp">
		<?php include("processing.php") ?>
        <div id="cb-user-content">
            <form id="accountForm">
                <div class="cb-well">
                    <h3 class="text-center">Account Information</h3>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="first_name">First Name <span>*</span>
                                </label>
                                <input id="first_name" name="first_name" type="text" class="form-control" 
										value="<?php echo esc($customer->firstName) ?>" 
										required data-msg-required="cannot be blank" >
                                <span id="first_name_err" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="last_name">Last Name <span>*</span>
                                </label>
                                <input id="last_name" name="last_name" type="text" class="form-control" 
										value="<?php echo esc($customer->lastName) ?>" 
										required data-msg-required="cannot be blank" >
                                <span id="last_name_err" class="text-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="company">Company
                                </label>
                                <input id="company" name="company" type="text" class="form-control"   value="<?php echo esc($customer->company) ?>" validate="true">
                                <span id="company_err" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="email">Email
                                </label>
                                <input id="email" type="text" class="form-control" value="<?php echo esc($customer->email) ?>" validate="true" disabled>
                                <span id="email_err" class="text-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="phone">Phone
                                </label>
                                <input id="phone" name="phone" type="text" class="form-control"  value="<?php echo esc($customer->phone) ?>" validate="true">
                                <span id="phone_err" class="text-danger"></span>
                            </div>
                        </div>
                    </div>             
                    <hr class="clearfix">
                    <div class="form-inline">
                        <div class="form-group">
							<input type="hidden" name="action" value="updateAccountInfo">
                            <input type="button" value="Update account information" id="updateAccount"  class="btn
                                   btn-primary">
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
    $('#updateAccount').click(function () {
		if(!$('#accountForm').valid()){
			return false;
		}
        var data = $('#accountForm').serialize();
        AjaxCallMessage('api.php', 'POST', 'json', data,'editaccount');
        return false;
    });       
</script>
