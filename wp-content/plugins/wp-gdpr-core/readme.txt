=== WP GDPR ===
Contributors: Mieke Nijs, Sebastian Kurzynowski, AppSaloon
Tags: Personal data, GDPR, European, regulation, privacy
Requires at least: 4.6.10
Tested up to: 4.9.5
Stable tag: 1.5.2
Requires PHP: 5.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Make your website GDPR compliant and automate the process of handling personal data while integrating with plugins.

== Description ==
This open source plugin will assist you making your website GDPR compliant by making personal data accessible to the owner of the data. Visitors (owners) don't need user accounts to access their data. Everything works through a unique link and e-mails.


WP-GDPR integrates with some of the most well-known plugins through add-ons. This will make the data stored by the plugins available for the visitor to manage it.
List of all add-ons:  [https://wp-gdpr.eu/add-ons/](https://wp-gdpr.eu/add-ons/).
Integration with:
  - [Gravity Forms](https://wp-gdpr.eu/add-ons/gravity-forms/)
  - [Contact Form DB 7](https://wp-gdpr.eu/add-ons/contact-form-db-7-addon/)
  - [WooCommerce](https://wp-gdpr.eu/add-ons/woocommerce-add-on/)

= How WP-GDPR Core works =

The plugin creates a page where users can request access to their personal data, stored on your website. You can find this page in the list of WordPress pages.
In the backend you'll get an overview of the requests users send and you can see which plugins collect personal data and need a 'ask for approval' checkbox.

Users who ask to view their personal data will get an email with a unique url on which they can view, update and download their comments and ask for a removal per comment.
When they ask for a removal, the admin has the ability to delete the comment through the wp-gdpr backend.
All emails will be sent automatically.

We made our code available on [Github](https://github.com/WP-GDPR/wp-gdpr-core/) and are welcoming Pull Request!

== Installation ==
1. Upload the plugin files to the /wp-content/plugins, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the ‘Plugins’ screen in WordPress
3. ‘WP GDPR’ will be created to view the requests in the backend
4. The page 'GDPR – Request personal data' will be created. This page displays the form where visitors can submit their request.


== Screenshots ==
1. WP-GDPR backend - overview of requests
2. WP-GDPR frontend - form where visitors can enter their email and ask to view there personal data
3. WP-GDPR frontend - form succes message

== Frequently Asked Questions ==

== Changelog ==

Version 1.5.2 (2018-04-12)
    - Hotfix svn conflicts

Version 1.5.1 (2018-04-12)
    - Minor bug fix

Version 1.5.0 (2018-04-12)
    - Improve interface in wp-admin
    - Improve labels and texts
    - Add data to plugin.json
    - Add Call To Action buttons to add-on overview
    - Add Norwegian translation
    - Add Italian translation

Version 1.4.4 (2018-03-30)
    - Fix compatible Jetpack
    - Added translation SV
    - Minor bug fix

Version 1.4.3 (2018-03-23)
    - Fix minor bugs
    
Version 1.4.2 (2018-03-16)
    - Fix deprecated warning
    - Fix when request form is embedded on a non-standard page. Until now, you got a 404-error when redirecting to the      "Thank you"-page
    - Fix confirmation of processing the delete request shows a short reference to what happened to the data
    - Enhancement add table header "request language"

Version 1.4.1 (2018-03-15)
    - Make checkbox compatible with jetpack

Version 1.4.0 (2018-03-09)
    - Add DPO e-mail address
    - Add dpo setting
    - Option to not show the comments section
    - Add settings feature
    - Stop form submition after refreshing
    - Add filter to implement checkbox in other commentforms
    - Update DE language

Version 1.3.3
    - Check version to create column

Version 1.3.2
    - Create colomn languages in table
    - Update autoloader

Version 1.3.1
    - Bugfix check if ICL_LANGUAGE_CODE is defined

Version 1.3.0
    - Add Spanish, Portuguese and Catalan languages translations
    - Make gdpr check fields customizable
    - Make check fields translatable

Version 1.2.4
    - Update readme with github repository
    - Change pot-file and po/mo-files
    - Update styling
    - Add hooks

Version 1.2.3
    - Check if is_plugin_active() exists

Version 1.2.2
    - Update de and po language

Version 1.2.1
    - Update .pot file
    - Quickfix dublicated GDPR checkbox

Version 1.2
    - Fix compatibility with WP Discuz
    - Add functionality to upadate default privacy url
    - Add grumphp configuration
    - Add CHANGELOG.md
    - Add README.md
    - Add git repo on Github: https://github.com/AppSaloon/WP-GDPR
    - Add email notification when sommeone askes for a "delete requests"

Version 1.1.6
    - Add .pot file
    - Add german translation

Version 1.1.5
    - Delete develop code

Version 1.1.4
    - Update typing errors

Version 1.1.3
    - Add admin css
    - Add gdpr-translation.php file

Version 1.1.2
    - Update page template comments overview page
    - Add checkbox when data is requested
    - Update front-end translation
    - Add translation PL

Version 1.1.1
    - Add update_comments.js

Version 1.1.0
    - Add name and email field to comments list
    - Let users update their name and email
    - Add download button to comments list
    - Make it possible for the admin to choose between delete comment or make comment anonymous