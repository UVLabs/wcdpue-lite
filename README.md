# WooCommerce Downloadable Product Update-Emails

License: GPLv2 or later<br>
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Current Version: 1.3.3-beta

A simple plugin used to send emails to customers who bought a downloadable product, letting them know that there's been an update to their download.

I Noticed that there's not a feature for this, or maybe I didn't look hard enough. But either way this is a plugin to add some basic functionality to your website.

<img src="http://s31.postimg.org/kejzzm08r/product_emails_v3_1_0.png" />

# Plans

<ul>
<em>Plugin options page for settings such as:</em><br />

<li>CUSTOM SMTP (currently possible by using POSTMAN SMTP plugin or any other custom SMTP plugin, might not bother)</li>
<li>CUSTOM HTML EMAILS</li>
/**************/<br>
</ul>

<ul>
<em>Better handling of emails</em><br />

<li>Currently emails are sent within a loop on post save if "I Updated The Download File" is checked, this behaviour will be changed to add a schedule event for each email.</li>
</ul>
# How to install

Download the folder inside the "plugin" directory of this repository and add it to your site via ftp, then go to plugins and click activate to activate plugin.
