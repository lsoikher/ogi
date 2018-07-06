# WC Autoship PayPal Payments

PayPal Payments for WC Autoship

Version 1.4.0<br />
Published: May 13, 2015<br />
© 2015 Patterns In the Cloud,
http://patternsinthecloud.com<br />
 Contact
support@patternsinthecloud.com for
additional support. 

## Hosting Requirements

* Operating System: Linux 64-bit
* Web Server: Apache Web Server 2.4.7
* PHP: PHP 5.5.9
* Database: MySQL Server 5.5.38
* Wordpress Version: Wordpress 3.9
* WooCommerce Version: WooCommerce 2.2.8
* WC Autoship Version: WC Autoship 2.0

## Installation

### Install and Configure Required Plugins

1.  Extract the archive to the plugins folder
    of your Wordpress site (usually /wp-content/plugins/).
1.  Log in to Wordpress as an administrator and navigate to the Plugins
    list. Find and activate the WC Autoship PayPal Payments plugin.
1.  Navigate to WooCommerce \> Settings \> Checkout \> WC Autoship
    PayPal Payments
1.  Complete the fields in the WC Autoship PayPal Payments settings
    form. See reference section A for field descriptions. Make sure that
    this payment gateway is enabled.

## Payment Gateway Settings

### Required PayPal Account Settings

1.  Billing Agreements must be **enabled**.

## Testing the Payment Gateway

### Testing Checkout

1.  Add a product to the shopping cart with a selected Auto-Ship option.
1.  Navigate to the checkout page.
1.  Select the PayPal payment method (titled "PayPal" by default).
1.  Complete the checkout with PayPal.

If the checkout is successful, a new order will be created, and a new Auto-Ship schdule will be available on the My Account page.

### Testing Auto-Ship Schedules

1.  Navigate to the My Account page. You should see your Auto-Ship schedules at the top of the page.
1.  Click the button to Change Billing.
1.  Select the PayPal payment method (titled "PayPal" by default).
1.  Complete the update with PayPal.

If the update is successful, the Payment Info section will be updated with the PayPal account.

## Reference

### A. PayPal Settings

Enter PayPal settings and account info.

| Field Name                           | Description                          |
| ------------------------------------ | ------------------------------------ |
| License Key                          | The software license key issued to you after purchase. |
| User                                 | PayPal API user                      |
| Password                             | PayPal API password                  |
| Signature                            | PayPal API signature                 |
| Sandbox Mode                         | Enable sandbox mode. Select this option to send transactions to the test gateway. |
