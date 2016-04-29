# WooCommerce Downloadable Product Update-Emails

License: GPLv2 or later<br>
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Current Version: 1.0.1

A simple plugin used to send emails to customers who bought a downloadable product, letting them know that there's been an update to their download.

Main UI on product screen (button turned off)

<img src="http://s32.postimg.org/51qbfxkd1/screenshot_1.png" />

Main UI on product screen ( button turned on )

<img src="http://s32.postimg.org/luai7kzf9/screenshot_2.png" />

Settings screen

<img src="http://s32.postimg.org/uf3ssw2c5/screenshot_2.png" />

Customizable email body

<img src="http://s32.postimg.org/y7nb8mhud/screenshot_4.png" />


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
