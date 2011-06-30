=== WP Finance Calculator ===
Contributors: butterflymedia
Donate link: http://www.getbutterfly.com/
Tags: finance, calculator, loans, mortgage, ppp, payment protection, repayments
Requires at least: 2.8
Tested up to: 3.1
Stable tag: 1.3.5

== Description ==

WP Finance Calculator is a drop in form for users to calculate indicative repayments. It can be implemented on a page or a post. It contains a real-time AJAX calculation option, which degrades gracefully on older browsers and uses a button to calculate repayments.

The plugin also contains an application form which sends a message to a specified email address.

The calculator features payment protection, and 12 to 60 months calculation.

The plugin can be used for car purchases, mortgage payments, real estate and big loans calculations.

Check the [official homepage](http://www.blogtycoon.net/wordpress-plugins/finance-calculator-with-application-form/ "Blog Tycoon") for feedback and support.

== Installation ==

1. Upload to your plugins folder, usually `wp-content/plugins/`
2. Activate the plugin on the plugin screen.
3. Configure the plugin.

== Frequently Asked Questions ==

= How do I add the form to a post or page? =

You need to add the `[finance_calculator]` shortcode to the body of the post/page in the editor's HTML mode.

== Screenshots ==

1. Front-end form
2. Administration section

== Changelog ==

= 1.3.5 =
* Changed author URL address

= 1.3.4 =
* Fixed a large string issue in occupations list
* Fixed a URL/directory detection function (replaced with WordPress specific function)
* Fixed several typos
* Removed several unused parameters
* Removed a deprecated capability function

= 1.3.3 =
* Changed name of email sender in application form instead of using the default "wordpress@yourdomain.com" (thanks @RedBlood)
* Changed email headers to be actually sent as text/html

= 1.3.2 =
* Added a new plugin path detection and fixed some more WordPress configurations
* Users can now override the default finance rate in the shortcode

= 1.3.1 =
* Fixed a wrong path issue

= 1.3 =
* Updated the readme.txt file in the /docs/ folder with more information
* Fixed XHTML code which invalidated strictness
* Tested the plugin with WordPress 3.1
* Removed old PHP code from index.php
* Removed an invalid attribute from the occupations list

= 1.2 =
* Added uninstall function
* Added license file
* Fixed screenshot order
* Rewritten the readme.txt file for consistency

= 1.1 =
* Added new features
* Added administration section
* Fixed typos
* Optimized scripts

= 1.0 =
* First release

== Upgrade Notice ==

= 1.1 =
Added possibility of modifiying rates and application email.
