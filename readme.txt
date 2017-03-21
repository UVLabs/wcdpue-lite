=== Plugin Name ===
Contributors: uriahs-victor
Plugin Name:       WooCommerce Downloadable Product Update Emails
Plugin URI:        https://github.com/UVLabs/WooCommerce-Downloadable-Product-Update-Emails
Tags:              woocommerce, emails, downloadable, products, update, schedule, ecommerce, e-commerce, customers, ebook update, email customers, software update, music, videos, notify customers, edd, digital downloads, product notify
Author URI:        http://uriahsvictor.com
Author:            Uriahs Victor
Requires at least: 4.1
Tested up to:      4.7
Stable tag:        1.1.6
Version:           1.1.6
License:           GPLv2 of later

== Description ==

WooCommerce Downloadable Product Update Emails. This plugin can be used to send emails to customers who bought a WooCommerce downloadable product, letting them know that there's been an update to their download. Never let customers guess if an edition of the e-book they bought or other product they bought has been updated. No more need to manually email customers about downloadable product updates or creating a new product.


= Pro Version =

Take a look at the Pro version for more functionality including HTML Emails, Variable Product Support and more!

[Go to Pro plugin page.](https://codecanyon.net/item/woocommerce-downloadable-product-update-emails/18908283?ref=TheLoneDev)

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings->Product Update Emails screen to configure the plugin


== Upgrade Notice ==

== Screenshots ==

1. Plugin UI on product page (button turned off).
2. Plugin UI on product page (button turned on).
3. Plugin Settings page.
4. Update email sent to buyers of the product.
5. Customable email body, more customization soon.

== Changelog ==

**1.1.6**

* Fixed an issue where the plugin would incorrectly count the amount of buyers with access to the download.


**1.1.5**

* Output admin notice if WooCommerce not active.
* Metabox text changes
* Tested WP 4.7
* Other minor changes

**1.1.4**

* Pro version now available.
* Plugin name change.
* Other minor changes.

**1.1.3**

* Fix changelog

**1.1.2**

* Metabox now only shows if product is a downloadable product.
* Metabox will not show if is a downloadable variable product.
* Minor metabox text changes
* Minor source code changes

**1.1.1**

* Fix a bug which caused the schedule feature to not work.
*Please go back to any previously updated product and click update again if you used the plugin scheduling feature during version 1.1.0*

**1.1.0**

* Tested with WP 4.6.1
* Removed "Current schedule" from settings page, dropdown menu will now stay at the selected schedule.
* Added custom email footer option.
* Metabox now only appears when editing an already published product.
* Minor default changes.

**1.0.1**

* Small CSS fix

**1.0.0**

* Initial release

== Frequently Asked Questions ==

= Email sends as wordpress.example.com =

I used wp_mail in the plugin so you could easily override the default send from email using an SMTP plugin such as: https://wordpress.org/plugins/postman-smtp/

= HTML Email Templates? =

Available in Pro version of plugin.

= What are Email Bursts? =

Used for setting the amount of emails addresses to grab and send emails to every time the scheduled time hits.

= What Happens if I set the email bursts amount to more than 10? =

In theory there should be no problem with this. But if you are on shared hosting it's recommended that you keep the bursts count at a decent amount.

== Donations ==

Please use plugin's donate button on settings screen if you wish to donate, thank you.
