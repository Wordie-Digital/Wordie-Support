=== Ajax Load More: Filters ===

Contributors: dcooney
Author: Darren Cooney
Author URI: https://connekthq.com/
Plugin URI: https://connekthq.com/plugins/ajax-load-more/add-ons/filters/
Requires at least: 4.0
Tested up to: 5.7.0
Stable tag: trunk
Homepage: https://connekthq.com/
Version: 1.12.2

== Copyright ==
Copyright 2021 Darren Cooney

This software is NOT to be distributed, but can be INCLUDED in WP themes: Premium or Contracted.
This software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

== Description ==

= The Filters add-on provides front-end and admin functionality for building and managing Ajax filters. =

Create custom Ajax Load More filters in seconds.

http://connekthq.com/plugins/ajax-load-more/add-ons/filters/

== Installation ==

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `ajax-load-more-filters.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `ajax-load-more-filters.zip`.
2. Extract the `ajax-load-more-filters` directory to your computer.
3. Upload the `ajax-load-more-filters` directory to the `/wp-content/plugins/` directory.
4. Ensure Ajax Load More is installed prior to activating the plugin.
5. Activate the plugin in the WP plugin dashboard.

== Changelog ==

= 1.12.2 - July 8, 2021 =
* NEW - Added new hook that provides support adjusting the term parameters of the Filters `get_terms` query. This will allow for setting a `parent` or `child_of` taxonomy option to return the children of a specific term.* FIX - Fixed issue with W3C HTML validator errors. [View Docs](https://connekthq.com/plugins/ajax-load-more/docs/add-ons/filters/#term-query)
* FIX - Fixed issue caused by `alm_filters_public_taxonomies` filter working incorrectly to show non-public taxonomies.
* FIX - Fixed an error with Radio buttons where the selected element was unable to be unchecked in some instances.
* UPDATE - Adding activation warning if core Ajax Load More is not installed when attempting to install add-on.
* UPDATE - Code cleanup and optimization.


= 1.12.1 - May 5, 2021 =
* NEW - Added new callback for the Range Slider field type that allows for modification of the start and end value display label. [View Docs](https://connekthq.com/plugins/ajax-load-more/docs/add-ons/filters/#almFiltersFormatRangeValues)
* NEW - Made it easier for uses to find the ID of each filter group by including the dynamic ID under the 'What's This' helper in each filter. e.g. alm_filters_actors_category
* FIX - Fixed issue with Select 'Default Select Option' not displaying in taxonomy queries.
* FIX - Fixed issues with duplicate IDs on input and select field types when using multiple Custom Field filters.
* UPDATE - Updated [alm_filters_{id}_{key}](https://connekthq.com/plugins/ajax-load-more/docs/add-ons/filters/#alm_filters_id_key) filter to accept the current Custom Values as a $values array. This allows users to use Custom Values and the Filter Hook to build the filter options.
* UPDATE - Improved admin UI/UX of adding/removing filter blocks.


= 1.12.0 - April 20, 2021 =
UPGRADE NOTICE:
The following two updates affect the Select fieldtype only. You may need to adjust your filters after updating.

* NEW - Added new `Default Select Option` field to replace the `Default Label` field for when using the Select field type.
	Note: This field will be displayed as the first option in the select dropdown list.
* NEW - Added HTML `Label` field for Select and Multi-Select field types. This update was added for accessibility and best practices purposes.
	Note: The `Label` field was previously used as the `Default Label` for each filter.

OTHER CHANGES:
* NEW - Added support for multiple Ajax Load More filter shortcodes on the same page or page template.
* NEW - Added aria-label attributes on Selected Filters button for accessibility.
* NEW - Added dynamic classnames to radio and checkbox <li/> attributes for easier target CSS styling.
* UPDATE - Updated JavaScript to allow for moving of filter elements on page load. This is useful when a developer wants to move a specific filter group (`.alm-filter--group`) to another location on the page.
* UPDATE - Removed use `parse_str` for querystring parsing for a custom solution to allow spaces in Custom Field values. Previously, spaces would be converted to `+` for querying.
* FIX - Fixed issue with hash links causing issues when using back/fwd buttons after a filter and URL changes.
* FIX - Fixed issue with multi-select input field and [Selected Filters](https://connekthq.com/plugins/ajax-load-more/add-ons/filters/selected-filters/) functionality not working to display the active filters.
* FIX - Fixed issue with spaces in custom fields and search queries causing problems with selected filters and initial query.


= 1.11.0 - January 3, 2021 =
* NEW - Added `Show Count` setting to display a total count beside each filter item.
* NEW - Added setting to add a `Reset Filters` button. Under the `Options` for each Filter is a `Reset Filters` option to enable the button on the filter frontend.
* UPDATE - Added support for selecting by `meta_value` and `meta_value_num` while using the Sorting filter with Custom Fields.
* FIX - Fixed issue with <select/> field types and the new Hierarchical terms listing not displaying child terms.
* FIX - Fixed issue with exluded Authors appearing is author list.
* FIX - Fixed issue with HTML markup in nested radio/checkbox lists.
* FIX - Fixed potential issue with recurrsion when a taxonomy does not exist and a filter is attemtped to be run.
* FIX - Added fix for restoring the default values of a checkbox and sort field types.

= 1.10.2 - November 25, 2020 =
* NEW - Added filter setting to set toggle blocks collapsed on initial page load.
* NEW - Added new `alm_filters_public_taxonomies` filter to allow for filtering the taxonomy query options in the filter builder. e.g. `add_filter( 'alm_filters_public_taxonomies', '__return_false' );`
* UPDATE - Added support for multi-level taxonomy terms listings in nested `<ul/>`. Previously only two levels was supported aesthetically.
* FIX - Fixed issue with hash links (`href="#target"`) causing a popstate which would trigger a filter change event.
* FIX - Fixed issue in [Selected Filters](https://connekthq.com/plugins/ajax-load-more/add-ons/filters/selected-filters/) where searching with a `+` would cause the string to split into multiple results.
* FIX - Fixed issue in admin where Author Role would not be selected in the Filter builder.

= 1.10.1 - October 1, 2020 =
* NEW - Added new Filter toggle option to allow users to expand/collapse induvidual filter groups. This is turned off by default and must be set to true in each filter group.
* NEW - Added new `.hidden` CSS class to quickly allow users to hide filters via custom `CSS Class` input in filter admin. Under CSS Classes of each filter you can add a class of `hidden` to hide the entire filter.
* FIX - Fixed issue in Safari with Filters and Paging add-ons where a back button press would remove ALM content.
* FIX - Fixed issue with console error related to star field type on `popstate`.
* FIX - Fixed issue where star rating field would not reset when removing a star filter.
* UPDATE - Added Category and Tags to Taxonomy filters. Ccategory and tags can now be filtered via Taxonomy query if required.

= 1.10.0.1 - September, 2020 =
-  HOTFIX - Fixed issue with PHP warning messages being displayed in WP 5.5+ warning about REST API issues when `WP_DEBUG` is `true`.

= 1.10.0 - July 13, 2020 =

-  NEW - Added new _Star Rating_ field for allowing users to query by rating custom fields.
-  NEW - Added new optional description field for each filter block.
-  UPDATE - Added new PHP Output option. Filters can now can be added via PHP Array and not a shortcode. Click the `Generate PHP` button in the Shortcode Output sidebar.
-  UPDATE - Admin UI/UX updates for a better experince building filters.

= 1.9.3 - June 29, 2020 =

-  NEW - Added `Reset` button to Range Slider field type. Users can now reset the Range Slider to the default values after filtering.
-  UPDATE - Added support for Masonry `transition` and updating the paging URLs when using Filters add-on.
-  UPDATE - Improved the stability of the scroll to post functionality when loading a paged URL.
-  FIX - Fixed IE11 issue with keyboard navigation of radio buttons.
-  FIX - Fixed IE11 issue where Range Slider was not triggering a change event.

= 1.9.2 - June 12, 2020 =

-  UPDATE - Added support for spacerbar keydown event to trigger events when using radio or checkbox field types.
-  UPDATE - Added support for arrow keys to traverse the radio groups and act more like native radio buttons.
-  FIX - Fixed issue on setting page not displaying filter preview
-  FIX - Pushed fix for issue where hitting a paged URL would not send the user to the top of the current page.

= 1.9.1 - March 18, 2020 =

## UPGRADE NOTICE

This version of Filters updates the markup of Radio and Checkbox filed types.
For accessibility purposes, the `<a href="#"/>` has been replaced with clickable `<div/>` elements.

Users who are using custom CSS to style these elements may need to update their markup for these elements.

-  UPDATE - Improved Radio and Checkbox accessibility by adding aria role, checked and labelledby attributes.
-  FIX - Fixed issue with filter loading animation not working as intended.

= 1.9.0 - March 2, 2020 =

-  NEW - Added Range Slider field type using [noUiSlider](https://refreshless.com/nouislider/). This field type is considered to be in beta but is fully functional for querying by custom field ranges using the `BETWEEN` compare operator.
-  NEW - Added new `alm_filters_{$id}_{key}_label` filter to allow for filtering of the default label in select and textfield field types.
-  UPDATE - Updated time of filter transitions. In some cases users were reporting issues of double clicks causing no results to be returned.
-  FIX - Fixed issue where browser URL update would happen while filtering causing issues in URL string.
-  FIX - Fixed issue where empty filter group could result in a JS error causing the add-on to stall.

= 1.8.3 - December 17, 2019 =

-  FIX - Fixed issue in `almfilters.start()` function not initiating properly when loaded via Ajax.
-  NEW - Adding separate `filters.min` JS file for easier debugging.

= 1.8.2 - December 6, 2019 =

-  FIX - Fixed issue with `filters_scrolltop` parameter not being maintained on scroll.
-  UPDATE - Filters admin UI updates and tweaks.

= 1.8.1 - November 18, 2019 =

-  NEW - Added new feature to add a `Toggle All` option to the Checkbox field type. Users can select/unselect all options with a single click.
-  NEW - Added support for custom taxonomy and tag queries on front pages, home pages and archive templates. Previously, if a user shared a filters URL they would redircted to the archive URL.
-  FIX - Fixed issue with tag\_\_and and tag filtering causing duplicates in some instances.
-  FIX - Fixed issue with filters not starting when initiated via Ajax.
-  FIX - Remove JS error that could occur when using [Selected Filters](https://connekthq.com/plugins/ajax-load-more/add-ons/filters/selected-filters/) and the value does not exist.

= 1.8.0 - October 22, 2019 =

-  NEW - Added date picker field type using [FlatpickrJS](https://flatpickr.js.org/) This field type is considered to be in beta but is fully functional for querying by custom field dates.
-  NEW - Added new `almfilters.start()` public function to init filters.
-  NEW - Added ability to add custom classnames to each filter block.

= 1.7.5 - October 1, 2019 =

-  NEW - Added new [filter hook](https://connekthq.com/plugins/ajax-load-more/docs/add-ons/filters/#filter-hooks) to allow for custom ordering of Author, Category, Tag and Custom Taxonomy term listings. `alm_filters_taxonomy_test_actor_args`
-  NEW - Added new `almfilters.resetFilter(key)` [public function](https://connekthq.com/plugins/ajax-load-more/docs/add-ons/filters/#public-functions) that allows for resetting a specific filterback to it's default state.
-  UPDATE - Improved the HTML layout of child terms when using category, tags or taxonomy filters. Child terms are now displayed in their own `<ul/>` wrapper nested inside the parent term.
-  FIX - Fixed issue with filtering a `meta_value` with a value of `0`. It was returning false instead of a numeric value.
-  FIX - Fixed JS error that may appear when using back/fwd buttons to modify a filter. This only affected the Multi-Select field type.
-  FIX - Fixed issue with Filters and Paging adding paged URLs(?pg=3) even if set to `false` in the shortcode.

= 1.7.4 - August 15, 2019 =

-  NEW - Added new `almFiltersActive` callback function. This callback function contains an object of the currently active filters.
-  NEW - Added new `filters_url` parameter that can disable URL rewrites preventing the browser querystring from being updated when filters are modified. e.g. `filters_url="false"`
-  NEW - Added `<noscript/>` support for Filters Addon.
-  UPDATE - Updated Preselected filter value functionality to remove URL parameters when all Preselected options are selected.

= 1.7.3 - June 26, 2019 =

-  FIX - Fixed issue where `title` HTML markup was being rendered even if empty.
-  FIX - Fixed an issue with UTM (querystring) parameters being treated as meta query parameters.
-  FIX - Fixed issue with fatal error after filter deletion.
-  UPDATE - Improved debug information.

= 1.7.2 - May 13, 2019 =

-  NEW - Added Sort (`sort`) filter. This new filter, combines order & orderby parameters into a single filter. Perfect for WooCommerce users who want to sort products `Lowest to Highest` or `Highest to Lowest`.
-  NEW - Added Multi select field type (`<select multiple/>`) support.
-  NEW - Added instructional text inside the admin to help users better understand the functionality of each Filter Key.

= 1.7.1 - May 6, 2019 =

-  NEW - Added support for nested (hierarchal) display for taxonomy, custom fields, tags and categories.
-  NEW - Adding `AND` operator for Taxonomy queries.
-  NEW - Added new `alm_filters_{id}_{key}_title` filter which will allow users to customize & localize filter group titles. FTI - Better localization support for string is coming soon.
-  NEW - Adding support for pre-selected values and checkbox field type.
-  FIX - Fixed issue where the textfield field type was not able to set Meta Query or Tax Query data.
-  FIX - Fixed issue with `almFiltersClear` function not clearing <select/> values.
-  FIX - Fixed issue with paging URLs when using Filters + Paging add-ons.

= 1.7.0 - March 8, 2019 =

## UPGRADE NOTICE

When updating to Filters 1.7.0 you must also update core [Ajax Load More](https://wordpress.org/plugins/ajax-load-more/)to version 5.0.

#### What's New

-  NEW - Adding paging URL parameters to allow for deep linking to paded results. `website.com/blog/?pg=3`. Paging URLs can be turned off in the shortcode `filters_paging="false"`.
-  NEW - Added new `filters_scroll` shortcode parameter to allow for scrolling user to top of the listing after a filter action. By default this is set to false.
-  FIX - Fixed bug where [almFiltersChange](https://connekthq.com/plugins/ajax-load-more/docs/add-ons/filters/#almFiltersChange) callback function was not triggering.
-  FIX - Added polyfills for supporting IE10 and 11.
-  FIX - Added fix for popstate error of `Cannot read property 'indexOf' of undefined` function when setting current filters.
-  FIX - Added fix for nested filter elements.
-  UPDATE - Improved scroll to element functionality and URL updates.
-  UPDATE - Update `almFilter` function in favour of new `ajaxloadmore.filter` function.
-  UPDATE - Removed shorthand array `[]` sysntax for users on PHP version < 5.4.

= 1.6.4 - February 4, 2019 =

-  FIX - Fixed issue with `alm_filters_{id}_{key}_default` & `alm_filters_{id}_{key}_selected` filters not triggering correctly with Taxonomy and Meta Query

= 1.6.3 - December 28, 2018 =

-  FIX - Fixed issue with custom taxonomy term values not being selected on page load.
-  FIX - Fixed with saving of filter data in WordPress admin. On some servers the data being passed was being rejected by the REST API as the data was not being sent as JSON.

= 1.6.2 - December 3, 2018 =

-  FIX - I accidentally left `print_r()` function in the deployed 1.6.1 release. Sorry about that :)

= 1.6.1 - December 6, 2018 =

-  FIX - Fixed a bug with parsing the URL of `category` and `category__and` querystring parameters.
-  FIX - Fixed issue where filters would remain disabled after zero posts are returned from Ajax Load More - You must update to core Ajax Load More v4.1.0 for this to be resolved.

= 1.6 - November 3, 2018 =

-  NEW - Added support for category**and and tag**and queries.
-  NEW - Better success and error notifications in WP Admin.
-  UPDATE - Improved drag and drop admin for filter groups.
-  FIX - Fixed PHP warning messaqge for undefined \$alt_key variable.
-  FIX - Fixed issue where `almFiltersClear` public JS function was not working with `<select>` elements - https://connekthq.com/plugins/ajax-load-more/docs/add-ons/filters/#almFiltersClear.
-  FIX - Fixed issue search term filtering on default `search.php` template.
-  FIX - Fixed bug where switching a filter key from Taxonomy or Custom Field wouldn't clear json data causing issues when filtering.

= 1.5 - August 21, 2018 =

-  NEW - Adding Created and Modified dates to filters.
-  NEW - Added import and export functionality.
-  UPDATED - Updated Filters admin interface for UI/UX improvements.
-  UPDATED - Better code commenting and organization.
-  FIX - Fixed issue with querystring parameters that are not part of filters parsing as custom field values.
-  UPDATED - Better code commenting and organization.

= 1.4.1 - July 9, 2018 =

-  NEW - Added new Default Value (fallback) parameter which allows for a fallback/default to be set on each filter group.
-  NEW - Added controls to move/re-arrange Custom Values in admin.
-  NEW - Added controls for collapsing filter groups for better readability.
-  UPDATE - Enhanced filter drag and drop functionality.
-  UPDATE - Security fix to remove special characters from querystring prior to being parsed.
-  UPDATE - Various admin UI/UX improvements

= 1.4 - May 22, 2018 =

-  NEW - Adding interactive selected filters display [View example](https://connekthq.com/plugins/ajax-load-more/add-ons/filters/selected-filters/).
-  BUG - Fixed issue in filters admin where filters would become unresponsive if a new filter was created and then drag and dropped into a new order

= 1.3 - May 8, 2018 =

-  NEW - Adding drag and drop to allow for re-ordering of filters in admin.
-  NEW - Adding support for search filter on default WP search template e.g. ?s={term}.
-  NEW - Adding callback functions dispatched at various intervals throughout the filter process. See the [docs](https://connekthq.com/plugins/ajax-load-more/docs/add-ons/filters/#callback-functions).

= 1.2 - March 20, 2018 =

-  NEW - Added `Selected Value` parameter that allows for setting a default, pre-selected value of a filter.
-  NEW - Added public JS function (`almFiltersClear`) that allows for the complete resetting/clearing of a filter group.
-  FIX - Fixed issue with missing quotes causing issues with filter submit in some browsers.
-  FIX - Removed `ALM_FILTERS_EXCLUDE_ARRAY` variable as it was causing issues in PHP version < 7.
-  FIX - Fixed issue with filters clearing after popstate event when sharing a filtered URL.

= 1.1 - February 22, 2018 =

-  UPGRADE NOTICE - Updated Ajax Load More shortcode to accept the filter ID (as a target) to help with querystring parsing on page load. `[ajax_load_more filters="true" target="{filter_id}"]`.
-  UPDATE - Added new `target` shortcode parameter to link the Ajax Load More instance to the filters.
-  UPDATE - Temporary removal of paged URLs due to integration issues with other add-ons - Paged URLs will return soon. e.g. `?pg=3`
-  UPDATE - Added support for Preloaded + Filters add-on.
-  FIX - Fixed multiple compatibility issues with Filters & Paging add-ons.
-  FIX - Added a fix for incorrect selected Taxonomy Operator in Filters admin.
-  FIX - Fixed string to array error in PHP 7.1.
-  FIX - Updated CSS of form properties to help with cross browser compatibility issues.

= 1.0 - February 13, 2018 =

-  Initial Release.
