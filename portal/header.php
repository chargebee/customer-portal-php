<?php
include_once('init.php');?>
<!DOCTYPE html>
<html>
    <head>
        <title>Customer Portal</title>
        <link rel="shortcut icon" href="assets/images/favicon.ico">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.1.0/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="assets/css/portal.css">
        <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    </head>
    <body>

    <div class="navbar navbar-static-top">
        <div class="container">
            <div class="navbar-header pull-left">
                <a href="index.php">
                    <img src="assets/images/logo.png" alt="My awesome subscription service" class="navbar-brand img-responsive">
                </a>
                <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
            </div>
            <div class="navbar-collapse collapse" >
                <ul class="nav navbar-nav navbar-right">
                    <li class="visible-xs">
                        <a href=<?php echo getChangePasswordUrl($configData); ?>>
                            Change Password
                        </a>
                    </li>
                    <li class="visible-xs">
                        <a href=<?php echo getLogoutUrl($configData); ?>>
                            Logout
                        </a>
                    </li>
                    <li class="dropdown hidden-xs">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <span class="glyphicon glyphicon-user"></span> Your Account
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="">
                                <a href=<?php echo getChangePasswordUrl($configData); ?>>
                                    Change Password
                                </a>
                            </li>
                            <li class="">
                                <a href=<?php echo getLogoutUrl($configData); ?>>
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
