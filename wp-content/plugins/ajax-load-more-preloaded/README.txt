=== Ajax Load More: Preloaded ===

Contributors: dcooney
Author: Darren Cooney
Author URI: https://connekthq.com/
Plugin URI: https://connekthq.com/plugins/ajax-load-more/add-ons/preloaded/
Version: 1.3.3

== Copyright ==
Copyright 2019 Connekt Media

This software is NOT to be distributed, but can be INCLUDED in WP themes: Premium or Contracted.
This software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.


== Description ==

= Quickly and easily preload an initial set of posts before completing any Ajax requests to the server with the Ajax Load More Preloaded add-on! =

The Preloaded add-on will render content to the screen faster and allow you to cache the initial result set which can greatly reduce load times and stress on your server.

http://connekthq.com/plugins/ajax-load-more/preloaded/

== Installation ==

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `ajax-load-more-preloaded.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `ajax-load-more-preloaded.zip`.
2. Extract the `ajax-load-more-preloaded` directory to your computer.
3. Upload the `ajax-load-more-preloaded` directory to the `/wp-content/plugins/` directory.
4. Ensure Ajax Load More is installed prior to activating the plugin.
5. Activate the plugin in the WP plugin dashboard.



== Changelog ==

= 1.3.3 - May 6, 2019 =
* FIX - Fixed issue if core Ajax Load More is deactivated the add-on will throw a fatal error becasue of undefined methods.


= 1.3.2 - August 26, 2018 =
** FIX - Bug fix for when using sticky posts and total stickies equals the preloaded_amount parameter. Is they were equal, Ajax Load More would shut down.
** UPDATE - Removing deprecated license activation code.


= 1.3.1 - July 17, 2018 =
* UPDATE - Updated ACF Relationship field function to return null if field is not present on the post ID page.
* UPDATE - Removing `EDD_SL_Plugin_Updater` class include as it was causing performance issues when viewing the plugins dashboard.


= 1.3.0 - May 4, 2017 =

* NEW - Added support for Advanced Custom Fields extension (Relationship + Repeater fields)
* NEW - Added support for > 4 meta_query queries.
* UPDATE - Updated plugin updater class


= 1.2.13 - March 24, 2017 =
* Adding support for sticky posts.
* Updated version of plugin update script.


= 1.2.12 - December 5, 2016 =
* UPDATE - Updated post_status parameter to support 'inherit' - this is useful for users loading post attachments.


= 1.2.11 - November 6, 2016 =
* UPDATE - Updated to support unlimited taxonomy queries.
* UPDATE - Updated author function to support multiple authors.


= 1.2.10 - September 5, 2016 =
* MILESTONE - Must update core Ajax Load More to 2.12.0 when updating this add-on.
* UPDATE - Added support for author slug when querying by author.
* UPDATE - Added support for post_status 'inherit'.


= 1.2.9 =
* SECURITY - Security fix for posts_status parameter. posts_status is now only available for logged in (admin) users. Non logged in users will only have access to view content in a 'publish' state.
* UPDATE - Adding support for preloading comments with the Comments add-on.


= 1.2.8 =
* FIX - Updated meta_query and meta_key query functions to match update to core Ajax Load More 2.10.1.


= 1.2.7 =
* FIX - Bug fix for date archives not working with Preloaded.

= 1.2.6 =
* UPDATE - Adding new $alm_current; variable that will return the current item number relative to the active loop - must upgrade core Ajax Load More to 2.8.7.
* UPDATE - Removing deprecated functions


= 1.2.5 =
* UPDATE - Adding support for multiple taxonomy queries - must upgrade core Ajax Load More to 2.8.5.
* UPDATE - Changing 'exclude' shortcode parameter name to be 'post__not_in' to follow WordPress naming conventions. 'exclude' will continue to work along side 'post__not_in'.


= 1.2.4 =
* FIX - Fixed issue with the passing of ALM template variables to Preloaded and Theme Repeaters.


= 1.2.3 =
* UPDATE - Adding 'type' to meta_query parameters. 'meta_type'.


= 1.2.2 =
* UPDATE - Adding required functionality for new Theme Repeaters add-on (https://connekthq.com/plugins/ajax-load-more/add-ons/theme-repeaters/).

= 1.2.1 =
* FIX - Fixed issue for querying by meta_key - users are not required to enter a meta_value to query by meta_key.
* FIX - Fixed issue where meta_key and meta_value were unable to pull shortcode values from core ALM.


= 1.2 =
* UPDATE - Updating plugin update script. Users are now required to input a license key to receive updates directly within the WP Admin. Please contact us for information regarding legacy license keys.
* NEW - Added multiple meta query functionality to the shortcode builder - users can now query by up to 4 custom fields.
* FIX - Fixed bug with the 'custom_args' parameter that was blocking arrays from being passed. Please check the documentation for the updated 'custom_args' syntax for multiple values.


= 1.1.1 =
* Adding 'custom_args' parameter.


= 1.1 =
* Adding 'post__in' parameter.


= 1.0 =
* Initial Release.
