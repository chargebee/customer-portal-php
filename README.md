# CUSTOMER PORTAL
Chargebee is a subscription billing management service that eases the complexities involved in managing recurring payments. As part of a comprehensive subscription lifecycle management system, Chargebee provides hosted checkout pages and 'customer portals'.

As your business scales up, you might come across a scenario where you’d want certain specific and essential features to be implemented in your customer portal. Needless to say, this comes with a lot of dependencies such as:

* The waiting time for the feature to be fully developed.
* The iterations needed to get the feature right based on your requirements. 
* Additional cost. 

Although Chargebee's default [customer portal](https://www.chargebee.com/docs/customer_portal.html) covers the basic account management functionalities, some of you might need extensive customization based on your business needs. For this reason, we’ve created an open source implementation of the customer portal using our own API- so that you can continue to develop customized features of varying degrees or individual specificity, independently without the wait time.

## What can I do with the open source portal code?

The code base for this portal is built on PHP and mirrors our current portal in terms of the UI and features. You could fork this to add any additional features that you think would help your business such as:

* Reactivate Subscription (already available in the open source )
* Pause Subscription 
* One time Checkout
* Option to display customer specific plans, etc.

## Why would I want to use this?

***To help you transition to your own implementation easily***

This portal implementation adopts the UI and CSS from Chargebee’s portal.

***For easy  adoption and to make changes to the portal using Bootstrap***

The UI for this portal is built using Bootstrap CSS and a base customized CSS is borrowed from Chargebee’s customer portal theme. 

***To add necessary functionalities on your own***

The code is modular which makes it easy to add the functionalities you need using the configuration files present under portal/config directory.

Here’s a quick look at the open source portal design:

![Portal UI](screenshot-1.png)

Below are the list of functions present in this portal:
* Update Account Information
* Add/Update Billing Address
* Add/Update Payment Method
* Add/Update Shipping Address
* View & Download Past Invoices
* Cancel Subscription
* Reactivate Subscription

## What can be done with this code?

This portal implementation adopts the UI and CSS from Chargebee’s portal to help you transition to your own implementation easily.

The UI for this portal built using Bootstrap CSS and the base customized CSS is borrowed from Chargebee’s customer portal theme. This is because it’s easy to adopt and make changes to the portal using Bootstrap. 

The code is modular which makes it easy to add the functionalities you need using the configuration files present under portal/config directory.

## How is User Management handled?

If you already have a user management system at your end, you could ignore this section and directly use the rest of the code base.

Chargebee’s user management supports the entire account setup starting from portal set up to email address verification, password retrieval, login, and logout. 

To understand the detailed workflow of the signup process, please follow the link below: 
https://www.chargebee.com/docs/customer_portal.html#how-can-your-customers-access-their-portal

The code that integrates with Chargebee’s user management system is located in [Auth.php](portal/Auth.php).

## Theme Customization
If you’re using our user management option, you would have to customize the theme to match your website. 

## Configuration

The configuration details of this implementation are captured in the files present under the [portal/config](portal/config/) directory.

**portal/config/config.ini:** This captures the settings that are required to connect to Chargebee account.

**portal/config/infoandalerts.ini:** This captures the info & alerts texts displayed across the portal. You can customize the content as needed.

**portal/config/portalsettings.ini:** This can be used to control the actions allowed in the portal.  

*Example:*   
1. If you do not want to allow your customers to cancel their subscription via the portal, you could set *cancelsubscription[allow]= ‘false’*.  
2. If you’d like to allow your customers to change the plan but not the quantity, you could set *changesubscription[allow] = 'true'* and *changesubscription[planqty] = 'false'*.

## How to get started?

In order to use this portal, you would have to:

1. Add your server domain in your Chargebee site under *Settings> Hosted Page Settings> Whitelist Return URL> Whitelisted Domain*. 

2. Edit the properties below in the "portal/config/config.ini" file:
   * SITE_URL and CANCEL_URL should be your server domain.
   * APP_PATH: The name of the folder created in the ROOT directory. *Eg:* If the portal files are saved in your ROOT directory as portal, then the app path value should be the same (portal).
    * SITE_NAME: Your Chargebee site name.
    * SITE_API_KEY: Chargebee site API key.
    * COOKIE_DOMAIN: should be your server domain.

    ```ini
    [GLOBAL]
    SITE_URL = http://localhost
    CANCEL_URL = http://localhost

    [PATH] ;Set the APP PATH (Relative to ROOT Folder). Eg: If the portal files are saved in your ROOT directory as portal, then the app path value should be the same (portal).
    APP_PATH = portal

    [CHARGEBEE_ENVIRONMENT] ;Configure your Chargebee site name and API key.
    SITE_NAME = <your-chargebee-site-name>
    SITE_API_KEY = <your-chargebee-site-api-key>

    [CHARGEBEE_COOKIE]
    COOKIE_DOMAIN = localhost
    COOKIE_PATH = /portal
    ```

If you need assistance with the implementation, or have requirements regarding the same, please write to support@chargebee.com. 

## License
See the LICENSE file
