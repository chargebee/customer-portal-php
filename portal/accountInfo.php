<div class="form-horizontal">
        <div class="row">
            <div class="col-sm-6">
                <div class="row">
                    <label class="col-xs-5 control-label">First Name
                    </label>
                    <div class="col-xs-7 form-control-static">
                        <?php echo (isset($customer->firstName) ? esc($customer->firstName) : "") ?>
                    </div>
                </div>
            </div>
             <div class="col-sm-6">
                    <div class="row">
                        <label class="col-xs-5 control-label">Last Name
                        </label>
                        <div class="col-xs-7 form-control-static">
                            <?php echo (isset($customer->lastName) ? esc($customer->lastName) : "") ?>
                        </div>
                    </div>
             </div>
        </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="row">
                <label class="col-xs-5 control-label">Email
                </label>
                <div class="col-xs-7 form-control-static">
                    <?php echo esc($customer->email) ?>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
                <div class="row">
                    <label class="col-xs-5 control-label">Company
                    </label>
                    <div class="col-xs-7 form-control-static">
                        <?php echo (isset($customer->company) ?  esc($customer->company) : "") ?>
                    </div>
                </div>
        </div>
    </div>
    <div class="row">
            <div class="col-sm-6">
                <div class="row">
                    <label class="col-xs-5 control-label">Phone
                    </label>
                    <div class="col-xs-7 form-control-static">
                        <?php echo (isset($customer->phone) ? esc($customer->phone) : "") ?>
                    </div>
                </div>
            </div>
     </div>
</div>